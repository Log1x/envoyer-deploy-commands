<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Envoyer API Key
    |--------------------------------------------------------------------------
    |
    | Here lives your Envoyer API key. It only needs the `deployments:create`
    | scope. You can find your API key in your Envoyer account settings.
    |
    */

    'api_key' => env('ENVOYER_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Envoyer Projects
    |--------------------------------------------------------------------------
    |
    | Here you will configure the projects you wish to deploy to. You must
    | specify a name and ID for each project. You can find the project ID
    | using `artisan deploy:list` or by inspecting the URL in Envoyer.
    |
    */

    'projects' => [
        // 'production' => 000000,
        // 'staging' => 111111,
    ],

    /*
    |--------------------------------------------------------------------------
    | Confirmation Prompt
    |--------------------------------------------------------------------------
    |
    | Here you can toggle the default confirmation prompt when deploying.
    | You can also toggle this on a per-project basis by passing the
    | `--confirm` flag when deploying.
    |
    */

    'confirm' => true,

    /*
    |--------------------------------------------------------------------------
    | Show Monitor URL
    |--------------------------------------------------------------------------
    |
    | Here you can toggle displaying the monitor URL after deploying. You can
    | optionally set this to a string to override the URL with a custom value.
    |
    */

    'url' => true,

    /*
    |--------------------------------------------------------------------------
    | Polling Interval
    |--------------------------------------------------------------------------
    |
    | Here you can set the polling interval in seconds for updating the
    | deployment status. The default is 3 seconds.
    |
    */

    'polling' => 3,

];
