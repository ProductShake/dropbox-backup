<?php

return [
    'backup' => [
        'name' => env('BACKUP_DIRECTORY_NAME', 'backups'),

        'source' => [
            'files' => [
                'include' => [
                    base_path(),
                    base_path('storage/app/public'),
                ],

                'exclude' => [
                    base_path('storage/app/backup-temp'),
                    base_path('storage/app/backups'),
                    base_path('storage/app/livewire-tmp'),
                    base_path('storage/debugbar'),
                    base_path('storage/logs'),
                    base_path('storage/media-library'),
                    base_path('storage/framework'),
                    base_path('vendor'),
                    base_path('node_modules'),
                    base_path('releases'),
                ],

                'follow_links' => false,
                'ignore_unreadable_directories' => false,
                'relative_path' => null,
            ],

            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        'database_dump_compressor' => null,
        'database_dump_file_timestamp_format' => null,
        'database_dump_filename_base' => 'database',
        'database_dump_file_extension' => '',

        'destination' => [
            'compression_method' => ZipArchive::CM_DEFAULT,
            'compression_level' => 9,
            'filename_prefix' => env('BACKUP_PREFIX_NAME', 'backups').'_',
            'disks' => [
                'dropbox',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),
        'password' => env('BACKUP_ARCHIVE_PASSWORD'),
        'encryption' => 'default',
        'tries' => 1,
        'retry_delay' => 0,
    ],

    'notifications' => [
        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => ['slack'],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_NOTIFICATION_EMAIL'),
            'from' => [
                // 'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
                'name' => env('MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'slack' => [
            'webhook_url' => env('HEALTH_SLACK_WEBHOOK_URL'),
            'channel' => '#backups',
            'username' => 'Backup',
            'icon' => ':robot_face:',
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('BACKUP_NAME', 'backups'),
            'disks' => ['dropbox'],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];
