<?php

return [

    /*
    |--------------------------------------------------------------------------
    | What to scan
    |--------------------------------------------------------------------------
    */
    'scan' => [
        'models'       => true,
        'controllers'  => true,
        'routes'       => true,
        'migrations'   => true,
        'dependencies' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom scan paths
    |--------------------------------------------------------------------------
    | Set to null to auto-detect. Override if your project uses a non-standard
    | structure (e.g. DDD, modular, or older Laravel where models live in app/).
    |
    | Examples:
    |   'models'      => app_path('Domain/Models'),
    |   'controllers' => app_path('Http/Controllers'),
    |   'routes'      => base_path('routes'),
    */
    'paths' => [
        'models'      => null,
        'controllers' => null,
        'routes'      => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | App namespace
    |--------------------------------------------------------------------------
    | Set to null to auto-detect from composer.json.
    | Override only if auto-detection fails.
    |
    | Example: 'app_namespace' => 'MyCompany\\MyApp'
    */
    'app_namespace' => null,

    /*
    |--------------------------------------------------------------------------
    | Include vendor / framework routes
    |--------------------------------------------------------------------------
    | When false, routes from Filament, Livewire, Sanctum, Ignition, etc.
    | are excluded. Only your application routes are kept.
    */
    'include_vendor_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Default output formats
    |--------------------------------------------------------------------------
    | Formats generated when running architecture:discover without --format.
    | Supported: json, html, markdown
    |
    | Example: ['json', 'html', 'markdown']
    */
    'output' => ['json', 'html', 'markdown'],

];
