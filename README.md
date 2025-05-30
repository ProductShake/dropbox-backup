# Laravel Backup Package

A pre-configured Laravel backup solution with Dropbox integration, built on top of [spatie/laravel-backup](https://github.com/spatie/laravel-backup).

## Features

- Automatic Dropbox integration with token refresh
- Pre-configured backup settings
- Slack notifications
- Zero configuration needed in your Laravel project
- Automatic service provider registration
- Automatic task scheduling

## Requirements

- PHP 8.2 or higher
- Laravel 11.x
- Dropbox account with API access

## Installation

1. Install the package via Composer:
```bash
composer require productshake/backup
```

2. Add the following environment variables to your `.env` file:
```bash
# Required
BACKUP_PREFIX_NAME="your_site_name"          # Prefix for backup files (e.g., "mysite")
BACKUP_DIRECTORY_NAME="backups"              # Directory name in Dropbox
DROPBOX_AUTH_TOKEN="your_token"             # Dropbox OAuth token
DROPBOX_SECRET="your_secret"                # Dropbox app secret
DROPBOX_KEY="your_key"                      # Dropbox app key
DROPBOX_TOKEN_URL="your_token_url"          # Dropbox token refresh URL
DROPBOX_REFRESH_TOKEN="your_refresh_token"  # Dropbox refresh token

# Optional - for Slack notifications
BACKUP_SLACK_WEBHOOK_URL="your_webhook_url"
```

That's it! The package will automatically:
- Configure the Dropbox filesystem driver
- Set up backup settings
- Handle token refresh
- Configure notifications
- Schedule backup tasks:
  - Daily backup at 01:00
  - Clean old backups at 02:00
  - Monitor backup health at 03:00

## Usage

### Creating a Backup

```bash
php artisan backup:run
```

### Listing Backups

```bash
php artisan backup:list
```

### Cleaning Old Backups

```bash
php artisan backup:clean
```

### Monitoring Backup Health

```bash
php artisan backup:monitor
```

### Logs

Backup logs are automatically stored in:
- `storage/logs/backup.log`
- `storage/logs/backup-clean.log`
- `storage/logs/backup-monitor.log`

## Dropbox Setup Guide

1. Go to [Dropbox Developer Console](https://www.dropbox.com/developers)
2. Create a new app
3. Generate an OAuth 2 refresh token:
   ```
   https://www.dropbox.com/oauth2/authorize?client_id=YOUR_APP_KEY&response_type=code&token_access_type=offline
   ```
4. Exchange the code for refresh token:
   ```bash
   curl https://api.dropbox.com/oauth2/token \
     -d code=YOUR_AUTH_CODE \
     -d grant_type=authorization_code \
     -u YOUR_APP_KEY:YOUR_APP_SECRET
   ```

## Configuration

The package comes pre-configured, but you can publish the config file if you need to customize it:

```bash
php artisan vendor:publish --tag="backup-config"
```

## Credits

- [Spatie](https://github.com/spatie) for their excellent Laravel Backup package
- [ProductShake](https://productshake.com)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
