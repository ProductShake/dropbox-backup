<?php

namespace ProductShake\Backup;

use Composer\Script\Event;

class Installer
{
    private const ENV_CONFIGS = [
        'BACKUP_NAME' => 'backups',
        'BACKUP_ARCHIVE_PASSWORD' => null,
        'BACKUP_NOTIFICATION_EMAIL' => null,
        'BACKUP_SLACK_WEBHOOK_URL' => null,
        'DROPBOX_AUTH_TOKEN' => null,
        'DROPBOX_APP_KEY' => null,
        'DROPBOX_APP_SECRET' => null,
        'DROPBOX_REFRESH_TOKEN' => null,
        'DROPBOX_TOKEN_URL' => 'https://api.dropboxapi.com/oauth2/token',
    ];

    public static function postPackageInstall(Event $event): void
    {
        $io = $event->getIO();
        $envFile = getcwd() . '/.env';

        if (!file_exists($envFile)) {
            $io->write('<error>.env file not found. Skipping environment configuration.</error>');
            return;
        }

        $envContent = file_get_contents($envFile);
        $addedConfigs = [];

        foreach (self::ENV_CONFIGS as $key => $defaultValue) {
            // Skip if config already exists
            if (str_contains($envContent, $key . '=')) {
                continue;
            }

            // If default value is null, ask for input
            $value = $defaultValue ?? $io->askAndHideSensitive("Enter value for {$key}: ");

            $addedConfigs[] = "{$key}={$value}";
        }

        if (!empty($addedConfigs)) {
            // Add a section header
            $envContent .= "\n\n# ProductShake Backup Configuration\n";
            $envContent .= implode("\n", $addedConfigs);

            file_put_contents($envFile, $envContent);
            $io->write('<info>Added backup configuration to .env file.</info>');
        } else {
            $io->write('<info>All backup configurations already exist in .env file.</info>');
        }
    }
}
