<?php

declare(strict_types=1);

namespace NITSAN\NsT3afExtended\Provider;

/**
 * Minimal platform handle for {@see T3afExtendedAdapter} — satisfies AiService duck-typed invoke().
 */
final class T3afExtendedPlatform
{
    /**
     * @param mixed ...$args AiService passes model id, prompt, and options payloads.
     */
    public function invoke(mixed ...$args): string
    {
        $prompt = '';
        foreach ($args as $arg) {
            if (is_string($arg) && trim($arg) !== '' && !str_contains($arg, '.')) {
                $prompt = $arg;
                break;
            }
        }
        if ($prompt === '' && isset($args[1]) && is_string($args[1])) {
            $prompt = $args[1];
        }

        return '[T3AF Extended adapter] ' . ($prompt !== '' ? $prompt : 'No prompt received.');
    }
}
