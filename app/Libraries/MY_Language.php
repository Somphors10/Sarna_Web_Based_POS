<?php

namespace App\Libraries;

use CodeIgniter\Language\Language;
use Throwable;

class MY_Language extends Language
{
    public function getLine(string $line, array $args = [])
    {
        try {
            if (! str_contains($line, '.')) {
                return $this->formatMessage($line, $args);
            }

            [$file, $parsedLine] = $this->parseLine($line, $this->locale);
            $output = $this->getTranslationOutput($this->locale, $file, $parsedLine);

            if ($output === null && strpos($this->locale, '-')) {
                [$locale] = explode('-', $this->locale, 2);
                [$file, $parsedLine] = $this->parseLine($line, $locale);
                $output = $this->getTranslationOutput($locale, $file, $parsedLine);
            }

            if ($output === null || $output === '') {
                [$file, $parsedLine] = $this->parseLine($line, 'en');
                $output = $this->getTranslationOutput('en', $file, $parsedLine);
            }

            $output ??= $line;

            return $this->formatMessage($output, $args);
        } catch (Throwable $e) {
            log_message('error', 'Language line failed [' . $line . ']: ' . $e->getMessage());

            return $this->formatMessage($line, $args);
        }
    }

    /**
     * CI4 requires language files to return an array. A missing `return` makes
     * require() yield integer 1 and crashes the page with Whoops.
     */
    protected function requireFile(string $path): array
    {
        $files = service('locator')->search($path, 'php', false);
        $strings = [];

        foreach ($files as $file) {
            if (! is_file($file)) {
                continue;
            }

            try {
                $loaded = require $file;
            } catch (Throwable $e) {
                log_message('error', 'Language file failed [' . $file . ']: ' . $e->getMessage());
                continue;
            }

            if (is_array($loaded)) {
                $strings[] = $loaded;
            }
        }

        if (isset($strings[1])) {
            $first = array_shift($strings);

            return array_replace_recursive($first, ...$strings);
        }

        return $strings[0] ?? [];
    }
}
