<?php

return [
    /*
     * Application admin front end url.
     */
    'admin_url' => env('ADMIN_URL', 'http://localhost:3000'),

    /*
     * Application front end url.
     */
    'url' => env('FRONTEND_URL', 'http://localhost:4200'),

    'app_url' => env('MOBILE_APP_URL', 'https://togumogu.app'),

    'mail_to_address' => env('MAIL_TO_ADDRESS', 'mbillah.batterylowinteractive@gmail.com'),
    'mail_from_address' => env('MAIL_FROM_ADDRESS', 'hello@togumogu.com'),
    'mail_from_name' => env('MAIL_FROM_NAME', 'ToguMogu Parenting Support'),

    'mail_to_subscription' => env('MAIL_TO_SUBSCRIPTION', 'subscription.togumogu@gmail.com'),
    'mail_to_order' => env('MAIL_TO_ORDER', 'orders.togumogu@gmail.com'),

    /*
     * User email verification url.
     */
    'email_verify_url' => env('ADMIN_EMAIL_VERIFY_URL', '/auth/email-verify?queryURL='),

    /*
     * User email verification url.
     */
    'reset_password_url' => env('ADMIN_RESET_PASSWORD_URL', '/auth/reset-password/'),

    /**
     * Firebase api key
     */
    'firebase_api_key' => env('FIREBASE_API_KEY'),
    'firebase_server_key' => env('FIREBASE_SERVER_KEY'),

    /*
     * BoomCast SMS API query parameters.
     */
    'boom_cast_sms_endpoint' => env('BOOM_CAST_SMS_ENDPOINT'),
    'boom_cast_sms_username' => env('BOOM_CAST_SMS_USERNAME'),
    'boom_cast_sms_password' => env('BOOM_CAST_SMS_PASSWORD'),
    'boom_cast_sms_type' => env('BOOM_CAST_SMS_TYPE', 'TEXT'),
    'boom_cast_unicode_sms_type' => env('BOOM_CAST_UNICODE_SMS_TYPE', 'TEXT'),
    'boom_cast_sms_is_localhost' => env('BOOM_CAST_SMS_IS_LOCALHOST', false),

    /*
     * SSL SMS API query parameters.
     */
    'ssl_sms_endpoint' => env('SSL_SMS_ENDPOINT'),
    'ssl_sms_username' => env('SSL_SMS_USERNAME'),
    'ssl_sms_password' => env('SSL_SMS_PASSWORD'),
    'ssl_sms_sid' => env('SSL_SMS_SID'),
    'ssl_sms_is_localhost' => env('SSL_SMS_IS_LOCALHOST', false),

    /**
     * Bkash credentials.
     */

    'bkash_app_key' => env('BKASH_APP_KEY'),
    'bkash_app_secret' => env('BKASH_APP_SECRET'),
    'bkash_username' => env('BKASH_USERNAME'),
    'bkash_password' => env('BKASH_PASSWORD'),
    'bkash_currency' => env('BKASH_CURRENCY', 'BDT'),
    'bkash_base_url' => env('BKASH_BASE_URL'),
    'bkash_api_version' => env('BKASH_API_VERSION', 'v1.2.0-beta'),
    'bkash_is_localhost' => env('BKASH_IS_LOCALHOST', false),

    /***
     * new bkash credentials.
     */
    'bkash_checkout_username' => env('BKASH_CHECKOUT_USERNAME'),
    'bkash_checkout_password' => env('BKASH_CHECKOUT_PASSWORD'),
    'bkash_checkout_app_key' => env('BKASH_CHECKOUT_APP_KEY'),
    'bkash_checkout_app_secret' => env('BKASH_CHECKOUT_APP_SECRET'),
    'bkash_checkout_base_url' => env('BKASH_CHECKOUT_BASE_URL'),

    /*
    * get supported artisan commands.
    */
    'artisan_commands' => [
        'route:cache' => [
            'text' => 'Create a route cache file for faster route registration.',
            'class' => 'primary'
        ],
        'config:cache' => [
            'text' => 'Create a cache file for faster configuration loading.',
            'class' => 'primary'
        ],
        'optimize' => [
            'text' => 'Cache the framework bootstrap files.',
            'class' => 'primary'
        ],
        'view:cache' => [
            'text' => 'Compile all of the application\'s Blade templates.',
            'class' => 'primary'
        ],
        'storage:link' => [
            'text' => 'Create the symbolic links configured for the application.',
            'class' => 'primary'
        ],
        'route:clear' => [
            'text' => 'Remove the route cache file.',
            'class' => 'warning'
        ],
        'config:clear' => [
            'text' => 'Remove the configuration cache file.',
            'class' => 'warning'
        ],
        'cache:clear' => [
            'text' => 'Flush the application cache.',
            'class' => 'warning'
        ],
        'view:clear' => [
            'text' => 'Clear all compiled view files.',
            'class' => 'warning'
        ],
        'permission:cache-reset' => [
            'text' => 'Reset the permission cache.',
            'class' => 'warning'
        ],
        'auth:clear-resets' => [
            'text' => 'Flush expired password reset tokens.',
            'class' => 'warning'
        ],
        'medialibrary:clean' => [
            'text' => 'Clean deprecated conversions and files without related model.',
            'class' => 'warning'
        ],
        'optimize:clear' => [
            'text' => 'Remove the cached bootstrap files.',
            'class' => 'warning'
        ],
        'clear-compiled' => [
            'text' => 'Remove the compiled class file.',
            'class' => 'warning'
        ]
    ]
];
