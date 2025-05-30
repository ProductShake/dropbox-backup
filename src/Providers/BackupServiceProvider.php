<?php

namespace ProductShake\Backup\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Spatie\Dropbox\Client as DropboxClient;

class BackupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/backup.php', 'backup'
        );

        // Merge our filesystems config with the application's
        $this->mergeFilesystemsConfig();

        // Register the Dropbox driver early in the boot process
        Storage::extend('dropbox', function ($app, array $config) {
            $resource = (new Client())->post($config['token_url'], [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $config['refresh_token'],
                ],
            ]);

            $accessToken = json_decode($resource->getBody(), true)['access_token'];
            $adapter = new DropboxAdapter(new DropboxClient($accessToken));

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
                $config
            );
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/backup.php' => config_path('backup.php'),
                __DIR__.'/../config/filesystems.php' => config_path('filesystems.php'),
            ], 'backup-config');
        }
    }

    protected function mergeFilesystemsConfig(): void
    {
        $packageConfig = require __DIR__.'/../config/filesystems.php';
        
        // Get the current filesystems config
        $filesystemsConfig = $this->app['config']->get('filesystems', []);
        
        // Merge the disks
        $filesystemsConfig['disks'] = array_merge(
            $filesystemsConfig['disks'] ?? [],
            $packageConfig['disks'] ?? []
        );
        
        // Set the merged config
        $this->app['config']->set('filesystems', $filesystemsConfig);
    }
}
