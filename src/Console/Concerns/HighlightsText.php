<?php

namespace Log1x\EnvoyerDeploy\Console\Concerns;

trait HighlightsText
{
    /**
     * Bold the specified value in the haystack.
     */
    protected function highlight(string $value, string $haystack): string
    {
        return preg_replace('/'.preg_quote($value).'/i', '<fg=blue;options=bold>$0</>', $haystack);
    }
}
