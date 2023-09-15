<?php

namespace Log1x\EnvoyerDeploy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log1x\EnvoyerDeploy\EnvoyerDeploy;

use function Termwind\terminal;

class DeployInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:info
                            {project? : The project to get information about}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get information about the specified project';

    /**
     * The EnvoyerDeploy instance.
     *
     * @var \Log1x\EnvoyerDeploy\EnvoyerDeploy
     */
    protected $envoyer;

    /**
     * The projects.
     */
    protected array $projects = [];

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->envoyer = EnvoyerDeploy::make();

        $projects = $this->envoyer->projects();

        if ($project = $this->argument('project')) {
            $projects = $projects->only($project);
        }

        if ($projects->isEmpty()) {
            $this->components->error(
                $project ? "The project <fg=red>{$project}</> could not be found." : 'No projects found.'
            );

            return;
        }

        $projects->each(fn ($project) => $this->projects[] = $this->envoyer->api()->project($project)->getProject());

        foreach ($this->projects as $i => $project) {
            $i++;

            $this->newLine();

            if ($i > 1) {
                $seperator = Str::repeat('─', min(terminal()->width(), 146));

                $this->line("  <fg=gray>{$seperator}</>");
                $this->newLine();
            }

            $this->components->twoColumnDetail("<fg=blue;options=bold>{$project->name}</>");

            $lastDeployed = $project->last_deployment_timestamp ?? 'N/A';

            $columns = [
                'Alias' => "<fg=blue;options=bold>{$projects->flip()->get($project->id)}</>",
                'Repository' => "{$project->plain_repository}:{$project->branch}",
                'Status' => $project->status == null ? '<fg=green;options=bold>Ready</>' : '<fg=yellow;options=bold>'.Str::title($project->status).'</>',
                'Heartbeat' => $project->has_missing_heartbeats ? '<fg=red;options=bold>Missing</>' : '<fg=green;options=bold>Healthy</>',
                'Last Deployed' => $lastDeployed,
                'Last Deploy Took' => "{$project->last_deployment_took}s",
                'Daily Deployments' => $project->daily_deploys,
                'Weekly Deployments' => $project->weekly_deploys,
            ];

            foreach ($columns as $label => $value) {
                $this->components->twoColumnDetail($label, $value);
            }

            $lastDeploy = $project->last_deployment_id ? $this->envoyer->api()->getDeployment($project->last_deployment_id) : null;

            if ($lastDeploy && $lastDeploy->processes) {
                $this->newLine();
                $this->components->twoColumnDetail('<fg=blue;options=bold>Hooks</>');

                foreach ($lastDeploy->processes as $process) {
                    $this->components->twoColumnDetail("<fg=blue>{$process->sequence}.</> {$process->name}");
                }
            }

            if ($project->servers) {
                foreach ($project->servers as $i => $server) {
                    $this->newLine();

                    $this->components->twoColumnDetail(
                        count($project->servers) > 1 ?
                        "<fg=blue;options=bold>Server #{$i}</>" :
                        '<fg=blue;options=bold>Server</>'
                    );

                    $status = Str::title($server->connection_status);

                    $columns = [
                        'Name' => "<fg=blue;options=bold>{$server->name}</>",
                        'Address' => "{$server->ip_address}:{$server->port}",
                        'User' => $server->connect_as,
                        'Path' => $server->deployment_path,
                        'Status' => Str::contains($server->connection_status, 'success') ? "<fg=green;options=bold>{$status}</>" : "<fg=red;options=bold>{$status}</>",
                    ];

                    foreach ($columns as $label => $value) {
                        $this->components->twoColumnDetail($label, $value);
                    }
                }
            }

            if ($project->monitor) {
                $this->newLine();

                $health = collect([
                    'New York' => $project->new_york_status,
                    'London' => $project->london_status,
                    'Singapore' => $project->singapore_status,
                ]);

                $health = $health->map(function ($status) {
                    $label = Str::title($status);

                    return $status === 'healthy' ? "<fg=green;options=bold>{$label}</>" : "<fg=red;options=bold>{$label}</>";
                });

                $this->components->twoColumnDetail('<fg=blue;options=bold>Monitor</>');

                $health->each(function ($status, $location) {
                    $this->components->twoColumnDetail($location, $status);
                });
            }
        }
    }
}
