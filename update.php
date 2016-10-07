<?php

function dumpVersion() {
    if (!file_exists(__DIR__ . '/var/cache')) {
        mkdir(__DIR__ . '/var/cache', 0777, true);
    }

    $info = shell_exec('composer show pug-php/pug');
    $info = explode('versions : * ', $info, 2);
    $info = explode("\n", $info[1], 2);
    $version = $info[0];

    file_put_contents(__DIR__ . '/var/cache/pug-version.txt', $version);
    chmod(__DIR__ . '/var/cache/pug-version.txt', 0666);
}

$lastUpdate = filemtime(__DIR__ . '/vendor/autoload.php');

if (in_array('--dump-version', $argv)) {
    dumpVersion();
} elseif (time() - $lastUpdate >= 6 * 3600) {
    shell_exec('composer update');
    dumpVersion();
}
