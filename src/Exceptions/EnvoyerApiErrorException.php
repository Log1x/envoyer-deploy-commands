<?php

namespace Log1x\EnvoyerDeploy\Exceptions;

use Exception;

class EnvoyerApiErrorException extends Exception
{
    /**
     * The Envoyer error codes.
     */
    protected array $errorCodes = [
        '400' => 'Valid data was given but the request has failed.',
        '401' => 'No valid API Key was given.',
        '404' => 'The request resource could not be found.',
        '422' => 'The payload has missing required parameters or invalid data was given.',
        '429' => 'Too many attempts.',
        '500' => 'Request failed due to an internal error in Envoyer.',
        '503' => 'Envoyer is offline for maintenance.',
    ];

    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct($response)
    {
        parent::__construct($this->errorCodes[$response->status()] ?? $response->body());
    }
}
