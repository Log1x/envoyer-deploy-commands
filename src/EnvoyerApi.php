<?php

namespace Log1x\EnvoyerDeploy;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Log1x\EnvoyerDeploy\Exceptions\ApiKeyMissingException;

class EnvoyerApi
{
    /**
     * The Envoyer API endpoint.
     */
    protected string $endpoint = 'https://envoyer.io/api/projects';

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
     * @throws \Log1x\EnvoyerDeploy\Exceptions\ApiKeyMissingException
     */
    public function getApiKey(): string
    {
        if ($this->apiKey) {
            return $this->apiKey;
        }

        $this->apiKey = config('envoyer.api_key', null);

        if (empty($this->apiKey)) {
            throw new ApiKeyMissingException;
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
        $projects = json_decode(
            $this->api()->get('/')->body()
        );

        return collect($projects->projects ?? []);
    }

    /**
     * Get the specified project.
     */
    public function getProject(): ?object
    {
        $project = json_decode(
            $this->api()->get("/{$this->project}")->body()
        );

        return $project->project ?? null;
    }

    /**
     * Get the specified deployment.
     */
    public function getDeployment(int $deployment): ?object
    {
        $deployment = json_decode(
            $this->api()->get("/{$this->project}/deployments/{$deployment}")->body()
        );

        return $deployment->deployment ?? null;
    }

    /**
     * Get the specified project's deployments.
     */
    public function getDeployments(): Collection
    {
        $deployments = json_decode(
            $this->api()->get("/{$this->project}/deployments")->body()
        );

        return collect($deployments->deployments ?? []);
    }

    /**
     * Create a new deployment.
     */
    public function deploy(): void
    {
        $this->api()->post("/{$this->project}/deployments");
    }
}
