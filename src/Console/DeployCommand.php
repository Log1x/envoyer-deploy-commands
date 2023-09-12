<?php

namespace Log1x\EnvoyerDeploy\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log1x\EnvoyerDeploy\Console\Concerns\HasProcessTask;
use Log1x\EnvoyerDeploy\Console\Concerns\HasTitleComponent;
use Log1x\EnvoyerDeploy\EnvoyerDeploy;

class DeployCommand extends Command
{
    use HasProcessTask, HasTitleComponent;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy
                            {project? : The project to deploy to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deploy the application using Envoyer';

    /**
     * The Envoyer Deploy instance.
     *
     * @var \Log1x\EnvoyerDeploy\EnvoyerDeploy
     */
    protected $envoyer;

    /**
     * The Envoyer API instance.
     *
     * @var \Log1x\EnvoyerDeploy\EnvoyerApi
     */
    protected $api;

    /**
     * The project object.
     *
     * @var object
     */
    protected $project;

    /**
     * The deployment object.
     *
     * @var object
     */
    protected $deployment;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->envoyer = EnvoyerDeploy::make();

        $this->title('Envoyer Deploy');

        $project = $this->envoyer->project(
            $this->argument('project')
        );

        if (! $project) {
            $projects = $this->envoyer->projects();

            if ($projects->isEmpty()) {
                $this->components->error('There are no <fg=red>projects</> configured');

                return;
            }

            $project = $projects->count() === 1 ? $projects->first() : $projects->get(
                $this->components->choice(
                    'Which <fg=blue>project</> would you like to <fg=blue>deploy</> to?',
                    $projects->keys()->flip()->toArray(),
                    0
                )
            );
        }

        $this->api = $this->envoyer->api()->project($project);

        $this->project = $this->api
            ->getProjects()
            ->filter(fn ($item) => $item->id === $project)
            ->first();

        $this->newLine();

        if (! $this->project) {
            $this->components->error("The project with ID <fg=red>{$project}</> could not be found.");

            return;
        }

        if (! $this->components->confirm("Deploy to <fg=blue>{$this->project->name}</>?", true)) {
            return;
        }

        $this->components->task("<fg=blue>✔</> Starting deployment for <fg=blue>{$this->project->name}</>", function () {
            $this->api->deploy();

            sleep(3);
        });

        $this->components->task('<fg=blue>✔</> Fetching the <fg=blue>deployment</> ID', function () {
            $this->deployment = $this->api->getDeployments()->first();
        });

        $this->newLine();

        $this->line("  <fg=blue>↳</> <options=bold>Deployment ID:</> <fg=blue>{$this->deployment->id}</>");
        $this->line("  <fg=blue>↳</> <options=bold>Repository:</> <fg=blue>{$this->project->plain_repository}</>:<fg=blue>{$this->deployment->commit_branch}</>");
        $this->line("  <fg=blue>↳</> <options=bold>Commit Hash:</> <fg=blue>{$this->deployment->commit_hash}</>");
        $this->line("  <fg=blue>↳</> <options=bold>Commit Author:</> <fg=blue>{$this->deployment->commit_author}</>");

        $this->newLine();

        $this->handleProcesses();

        $this->newLine();

        $this->finishDeploy(true);

        $this->newLine();

        $processes = collect($this->deployment->processes);

        $started = now()->parse($processes->first()->started_at);
        $finished = now()->parse($processes->last()->finished_at);
        $elapsed = $started->diffInSeconds($finished);

        if (Str::is($this->deployment->status, 'finished')) {
            $this->line("  🎉 Deployment <options=bold>completed</> in <fg=blue>{$elapsed}</> seconds.");

            return;
        }

        $this->line("  ❌ Deployment <options=bold>failed</> after <fg=red>{$elapsed}</> seconds with status <fg=red>{$this->deployment->status}</>");
    }

    /**
     * Handle the deployment processes.
     */
    protected function handleProcesses(): void
    {
        $this->deployment = $this->getDeployment();

        foreach ($this->deployment->processes as $process) {
            $this->processTask($process);
        }
    }

    /**
     * Handle the process step.
     */
    protected function handleProcess($process, int $delay = 2): ?object
    {
        $this->deployment = $this->getDeployment();

        $process = collect($this->deployment->processes)->filter(fn ($item) => $item->name === $process->name)->first();

        if (! Str::contains($process->status, 'finished')) {
            sleep($delay);

            return $this->handleProcess($process);
        }

        return $process;
    }

    /**
     * Finish the deployment.
     */
    protected function finishDeploy(bool $task = false, int $delay = 1): mixed
    {
        $this->deployment = $this->getDeployment();

        if (Str::contains($this->deployment->status, 'finished')) {
            return null;
        }

        sleep($delay);

        return $task ?
            $this->components->task('<fg=blue>✔</> Finalizing <fg=blue>Deployment</>', fn () => $this->finishDeploy()) :
            $this->finishDeploy();
    }

    /**
     * Get the current deployment object.
     */
    protected function getDeployment(): ?object
    {
        return $this->api->getDeployment($this->deployment->id) ?? null;
    }
}
