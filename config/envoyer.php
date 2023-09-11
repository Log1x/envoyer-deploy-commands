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
    | The deployment projects.
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

];
