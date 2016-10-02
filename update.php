<?php

$lastUpdate = filemtime(__DIR__ . '/vendor/autoload.php');

if (time() - $lastUpdate >= 6 * 3600) {
    shell_exec('composer update');

    $info = shell_exec('composer show pug-php/pug');
    $info = explode('versions : * ', $info, 2);
    $info = explode("\n", $info[1], 2);
    $version = $info[0];

    chmod(__DIR__ . '/var', 0777);
    chmod(__DIR__ . '/var/cache', 0777);
    chmod(__DIR__ . '/var/cache/pug-version.txt', 0666);
    file_put_contents(__DIR__ . '/var/cache/pug-version.txt', $version);
}
