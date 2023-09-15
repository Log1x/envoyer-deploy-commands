<?php

namespace Log1x\EnvoyerDeploy;

use Illuminate\Support\Collection;

class EnvoyerDeploy
{
    /**
     * The Envoyer API instance.
     *
     * @var \Log1x\EnvoyerDeploy\EnvoyerApi
     */
    protected $api;

    /**
     * Create a new instance of the EnvoyerDeploy class.
     */
    public static function make(): EnvoyerDeploy
    {
        return new static;
    }

    /**
     * Get hte Envoyer API instance.
     */
    public function api(): EnvoyerApi
    {
        if ($this->api) {
            return $this->api;
        }

        $this->api = EnvoyerApi::make();

        return $this->api;
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

    /**
     * Get the confirm option.
     */
    public function confirm(): bool
    {
        return (bool) config('envoyer.confirm', true);
    }

    /**
     * Get the URL option.
     */
    public function url(): string|bool
    {
        return config('envoyer.url', true);
    }

    /**
     * Get the polling option.
     */
    public function polling(): int
    {
        return (int) config('envoyer.polling', 3);
    }
}
