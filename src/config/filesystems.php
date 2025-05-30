<?php

return [
    'disks' => [
        'dropbox' => [
            'driver' => 'dropbox',
            'authorization_token' => env('DROPBOX_AUTH_TOKEN'),
            'app_secret' => env('DROPBOX_SECRET'),
            'app_key' => env('DROPBOX_KEY'),
            'token_url' => env('DROPBOX_TOKEN_URL'),
            'refresh_token' => env('DROPBOX_REFRESH_TOKEN'),
        ],
    ],
];
