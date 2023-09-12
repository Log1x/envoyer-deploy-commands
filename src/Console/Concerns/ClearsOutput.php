<?php

namespace Log1x\EnvoyerDeploy\Console\Concerns;

trait ClearsOutput
{
    /**
     * Clear the specified lines from the terminal output.
     */
    protected function clear(int $lines = 1): void
    {
        $this->output->write("\x1b[{$lines}A\x1b[0J");
    }
}
