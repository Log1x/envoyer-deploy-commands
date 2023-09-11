<?php

namespace Log1x\EnvoyerDeploy;

use Illuminate\Support\Collection;
use JustSteveKing\Laravel\Envoyer\SDK\Envoyer;
use Log1x\EnvoyerDeploy\Exceptions\ApiKeyMissingException;

class EnvoyerDeploy
{
    /**
     * The Envoyer SDK instance.
     *
     * @var \JustSteveKing\Laravel\Envoyer\SDK\Envoyer
     */
    protected $envoyer;

    /**
     * Create a new instance of the EnvoyerDeploy class.
     */
    public static function make(): EnvoyerDeploy
    {
        return new static;
    }

    /**
     * Get the Envoyer SDK instance.
     */
    public function api(): Envoyer
    {
        if ($this->envoyer) {
            return $this->envoyer;
        }

        $this->envoyer = Envoyer::illuminate(
            $this->apiKey()
        );

        return $this->envoyer;
    }

    /**
     * Get the Envoyer API key.
     *
     * @throws \Log1x\EnvoyerDeploy\Exceptions\ApiKeyMissingException
     */
    public function apiKey(): string
    {
        $key = config('envoyer.api_key', null);

        if (empty($key)) {
            throw new ApiKeyMissingException;
        }

        return $key;
    }

    /**
     * Get the Envoyer projects.
     */
    public function projects(): Collection
    {
        return collect(
            config('envoyer.projects', [])
        );
    }

    /**
     * Get the specified project.
     */
    public function project(string|int $project = null): ?int
    {
        if (! $project) {
            return null;
        }

        if (is_numeric($project) && $this->projects()->flip()->has($project)) {
            return (int) $project;
        }

        $project = $this->projects()->get($project);

        return $project ? (int) $project : null;
    }
}
