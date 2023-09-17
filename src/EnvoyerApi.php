<?php

namespace Log1x\EnvoyerDeploy;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiErrorException;
use Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiKeyMissingException;

class EnvoyerApi
{
    /**
     * The Envoyer API endpoint.
     */
    protected string $endpoint = 'https://envoyer.io/api';

    /**
     * The API key.
     */
    protected string $apiKey = '';

    /**
     * The project ID.
     */
    protected int $project;

    /**
     * Create a new instance of the EnvoyerDeploy class.
     */
    public static function make(): EnvoyerApi
    {
        return new static;
    }

    /**
     * Get the Envoyer HTTP client.
     *
     * @throws \Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiKeyMissingException
     */
    public function api(): PendingRequest
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => "Bearer {$this->getApiKey()}",
        ])->baseUrl($this->endpoint);
    }

    /**
     * Get from the Envoyer API.
     *
     * @throws \Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiErrorException
     */
    public function get(string $uri): object
    {
        $response = $this->api()->get($uri);

        if ($response->failed()) {
            throw new EnvoyerApiErrorException($response);
        }

        return json_decode(
            $response->body()
        );
    }

    /**
     * Post to the Envoyer API.
     *
     * @throws \Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiErrorException
     */
    public function post(string $uri): ?object
    {
        $response = $this->api()->post($uri);

        if ($response->failed()) {
            throw new EnvoyerApiErrorException($response);
        }

        return json_decode(
            $response->body()
        );
    }

    /**
     * Get the Envoyer API endpoint.
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Set the Envoyer API endpoint.
     */
    public function endpoint(string $endpoint): EnvoyerApi
    {
        $this->endpoint = $endpoint;

        return $this;
    }

    /**
     * Set the Envoyer API key.
     */
    public function apiKey(string $apiKey): EnvoyerApi
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get the Envoyer API key.
     *
     * @throws \Log1x\EnvoyerDeploy\Exceptions\EnvoyerApiKeyMissingException
     */
    public function getApiKey(): string
    {
        if ($this->apiKey) {
            return $this->apiKey;
        }

        $this->apiKey = config('envoyer.api_key', null);

        if (empty($this->apiKey)) {
            throw new EnvoyerApiKeyMissingException;
        }

        return $this->apiKey;
    }

    /**
     * Set the project ID.
     */
    public function project(int $project): EnvoyerApi
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get the Envoyer projects.
     */
    public function getProjects(): Collection
    {
        $projects = $this->get('/projects');

        return collect($projects->projects ?? []);
    }

    /**
     * Get the specified project.
     */
    public function getProject(): ?object
    {
        $project = $this->get("/projects/{$this->project}");

        return $project->project ?? null;
    }

    /**
     * Get the specified deployment.
     */
    public function getDeployment(int $deployment): ?object
    {
        $deployment = $this->get("/projects/{$this->project}/deployments/{$deployment}");

        return $deployment->deployment ?? null;
    }

    /**
     * Get the specified project's deployments.
     */
    public function getDeployments(): Collection
    {
        $deployments = $this->get("/projects/{$this->project}/deployments");

        return collect($deployments->deployments ?? []);
    }

    /**
     * Get the project actions.
     */
    public function getActions(): Collection
    {
        $actions = $this->get('/actions');

        return collect($actions->actions ?? []);
    }

    /**
     * Get the specified project's hooks.
     */
    public function getHooks(): Collection
    {
        $hooks = $this->get("/projects/{$this->project}/hooks");

        return collect($hooks->hooks ?? []);
    }

    /**
     * Get the specified project's linked folders.
     */
    public function getFolders(): Collection
    {
        $folders = $this->get("/projects/{$this->project}/folders");

        return collect($folders->folders ?? []);
    }

    /**
     * Create a new deployment.
     */
    public function deploy(): void
    {
        $this->post("/projects/{$this->project}/deployments");
    }
}
