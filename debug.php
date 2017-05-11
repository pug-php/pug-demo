<?php

function dumpDirectory($dir, $indent = 0) {
    echo str_repeat(' ', $indent * 3) . basename($dir) . "\n";
    if (is_dir($dir) && $indent < 5) {
        foreach (scandir($dir) as $element) {
            if ($element !== '.' && $element !== '..') {
                dumpDirectory($dir . '/' . $element, $indent + 1);
            }
        }
    }
}

echo shell_exec('node --version');

echo '<pre>';
dumpDirectory(__DIR__ . '/vendor/nodejs-php-fallback');
dumpDirectory(__DIR__ . '/vendor/pug-php');
dumpDirectory(__DIR__ . '/node_modules');
echo '<pre>';