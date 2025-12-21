<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Authentication
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'web',       // default guard = admin
        'passwords' => 'users', // reset password admin
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [              // ADMIN
            'driver' => 'session',
            'provider' => 'users',
        ],

        'student' => [          // SISWA
            'driver' => 'session',
            'provider' => 'members',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        // ADMIN provider
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // SISWA provider
        'members' => [
            'driver' => 'eloquent',
            'model' => App\Models\Member::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Reset
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'members' => [
            'provider' => 'members',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
