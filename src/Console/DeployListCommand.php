<?php

namespace Log1x\EnvoyerDeploy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log1x\EnvoyerDeploy\Console\Concerns\HighlightsText;
use Log1x\EnvoyerDeploy\EnvoyerDeploy;

class DeployListCommand extends Command
{
    use HighlightsText;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:list
                            {search? : The project to search for}
                            {--limit= : The number of projects to display}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the application using Envoyer';

    /**
     * The EnvoyerDeploy instance.
     *
     * @var \Log1x\EnvoyerDeploy\EnvoyerDeploy
     */
    protected $envoyer;

    /**
     * The search term.
     */
    protected ?string $search = null;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->envoyer = EnvoyerDeploy::make();

        $projects = $this->envoyer->api()->getProjects();

        if ($this->search = $this->argument('search')) {
            $projects = $projects->filter(function ($project) {
                return Str::contains(strtolower($project->name), strtolower($this->search));
            });
        }

        if ($limit = $this->option('limit')) {
            $projects = $projects->take($limit);
        }

        $headers = collect([
            'ID',
            'Name',
            'Repository',
            'Branch',
            'Status',
            'Last Deployment',
        ])->map(fn ($header) => "<fg=blue;options=bold>{$header}</>");

        $projects = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $this->search ? $this->highlight($this->search, $project->name) : $project->name,
                'repository' => strtolower($project->repository),
                'branch' => $project->branch,
                'status' => $project->status == null ? 'Ready' : Str::title($project->status),
                'timestamp' => $project->last_deployment_timestamp,
            ];
        })->map(function ($project) {
            if ($this->envoyer->project($project['id'])) {
                foreach ($project as $key => $value) {
                    $project[$key] = Str::contains($project['status'], 'running') ?
                        "<fg=green>{$value}</>" :
                        "<fg=blue>{$value}</>";
                }
            }

            return $project;
        });

        $this->newLine();
        $this->table($headers->toArray(), $projects->toArray());
    }
}
