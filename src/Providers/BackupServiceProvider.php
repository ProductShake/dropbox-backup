<?php

namespace ProductShake\Backup\Providers;

use GuzzleHttp\Client;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class BackupServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/backup.php', 'backup'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/services.php', 'services'
        );

        // Merge our filesystems config with the application's
        $this->mergeFilesystemsConfig();

        // Register the Dropbox driver early in the boot process
        Storage::extend('dropbox', static function (Application $app, array $config) {
            $resource = (new Client)->post($config['token_url'], [
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
                __DIR__.'/../config/services.php' => config_path('services.php'),
            ], 'config');
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
