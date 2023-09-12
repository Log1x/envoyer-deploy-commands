<?php

namespace Log1x\EnvoyerDeploy\Console\Concerns;

use Illuminate\Support\Str;

use function Termwind\terminal;

trait HasProcessTask
{
    use ClearsOutput;

    /**
     * Process the task.
     */
    public function processTask(object $process): void
    {
        $description = "<fg=yellow;options=bold>↻</> Running <fg=yellow>{$process->name}</>";

        $descriptionWidth = mb_strlen(preg_replace("/\<[\w=#\/\;,:.&,%?]+\>|\\e\[\d+m/", '$1', $description) ?? '');

        $this->output->write("  $description ", false);

        $width = min(terminal()->width(), 150);
        $dots = max($width - $descriptionWidth - 10, 0);

        $this->output->write(str_repeat('<fg=gray>.</>', $dots), false);
        $this->output->writeln(' <fg=yellow;options=bold>RUNNING</>');

        $process = $this->handleProcess($process);

        $this->clear();

        $description = Str::contains($process->status, 'failed') ?
            "<fg=red;options=bold>✖</> Failed <fg=red>{$process->name}</>" :
            "<fg=blue;options=bold>✔</> Finished <fg=blue>{$process->name}</>";

        $descriptionWidth = mb_strlen(preg_replace("/\<[\w=#\/\;,:.&,%?]+\>|\\e\[\d+m/", '$1', $description) ?? '');

        $this->output->write("  $description ", false);

        $started = now()->parse($process->started_at);
        $finished = now()->parse($process->finished_at);

        $elapsed = number_format($started->diffInMilliseconds($finished));
        $elapsed = $process ? " {$elapsed}ms" : '';

        $elapsedWidth = mb_strlen($elapsed);

        $width = min(terminal()->width(), 150);
        $dots = max($width - $descriptionWidth - $elapsedWidth - 10, 0);

        $this->output->write(str_repeat('<fg=gray>.</>', $dots), false);
        $this->output->write("<fg=gray>{$elapsed}</>", false);

        $this->output->writeln(
            Str::is($process->status, 'finished') ? ' <fg=green;options=bold>DONE</>' : ' <fg=red;options=bold>FAIL</>'
        );
    }
}
