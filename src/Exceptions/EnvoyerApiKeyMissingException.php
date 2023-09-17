<?php

namespace Log1x\EnvoyerDeploy\Exceptions;

use Exception;

class EnvoyerApiKeyMissingException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('The Envoyer API key is missing.');
    }
}
