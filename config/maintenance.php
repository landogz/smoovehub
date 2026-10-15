<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Site maintenance mode
    |--------------------------------------------------------------------------
    |
    | Flip SITE_MAINTENANCE=true in .env to show the public under-maintenance
    | page and return HTTP 503 for the marketplace, portals, and APIs.
    | Installer, health check, client payment, and project progress stay open.
    |
    */

    'enabled' => filter_var(env('SITE_MAINTENANCE', false), FILTER_VALIDATE_BOOLEAN),

    'message' => env(
        'SITE_MAINTENANCE_MESSAGE',
        'SmooveHub is undergoing scheduled maintenance. Please check back shortly.',
    ),

    'retry_after' => (int) env('SITE_MAINTENANCE_RETRY_AFTER', 3600),

    /*
    | Optional bypass token. Visit /?bypass=TOKEN to set a cookie and browse
    | the site while maintenance is on. Leave empty to disable bypass.
    */
    'bypass' => (string) env('SITE_MAINTENANCE_BYPASS', ''),

];
