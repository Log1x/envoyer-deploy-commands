<?php

namespace Log1x\EnvoyerDeploy\Console\Concerns;

use Illuminate\Support\Str;

trait HasTitleComponent
{
    /**
     * Create a title component.
     */
    protected function title(string $value, int $padding = 12, string $bg = 'blue', string $fg = 'white'): void
    {
        $length = Str::length($value) + $padding;

        $title = Str::padBoth($value, $length);
        $spacing = Str::padLeft('', $length);

        $this->newLine();
        $this->line("  <bg={$bg}>{$spacing}</>");
        $this->line("  <bg={$bg};fg={$fg}>{$title}</>");
        $this->line("  <bg={$bg}>{$spacing}</>");
    }
}
