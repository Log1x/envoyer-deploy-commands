<?php

namespace Log1x\EnvoyerDeploy\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string apiKey()
 * @method static array projects()
 * @method static int|null project(string $name)
 *
 * @see \Log1x\EnvoyerDeploy\EnvoyerDeploy
 */
class EnvoyerDeploy extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'EnvoyerDeploy';
    }
}
