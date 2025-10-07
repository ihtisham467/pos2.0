<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wayfinder Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Wayfinder package.
    |
    */

    'exclude_controllers' => [
        'App\\Http\\Controllers\\Settings\\StoreController',
    ],

    'exclude_routes' => [
        'store.*',
    ],
];
