<?php

set_time_limit(0);

function needDirectory($directory) {
    if (!file_exists($directory)) {
        return mkdir($directory, 0777, true);
    }
    
    return false;
}

$enginesDirectory = __DIR__ . '/var/engines';
needDirectory($enginesDirectory);
$cacheDirectory = __DIR__ . '/var/cache';
needDirectory($cacheDirectory);

$gitHost = 'https://github.com/';
$apiHost = 'https://api.github.com/';
$apiContext = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP',
        ],
    ],
]);

$enginesRepositories = array(
    'tale-pug' => 'Talesoft/tale-pug',
    'phug'     => 'phug-php/phug',
    'pug-php'  => 'pug-php/pug',
);

foreach ($enginesRepositories as $repository => $url) {
    $optionsHtml = '';
    $directory = $enginesDirectory . DIRECTORY_SEPARATOR . $repository;
    needDirectory($directory);
    $versionCache = $cacheDirectory . DIRECTORY_SEPARATOR . $repository . '-tags.json';
    $versionFile = $versionCache;
    if (!file_exists($versionCache) || time() - filemtime($versionCache) > 3600) {
        $list = array();
        for ($i = 1; true; $i++) {
            $items = @json_decode(file_get_contents(
                $apiHost . 'repos/' . $url . '/tags?page=' . $i,
                false,
                $apiContext
            ));
            if (!is_object($items)) {
                $items = @json_decode(file_get_contents(
                    __DIR__ . '/fallback/' . $url . '-tags.json'
                ));
            }
            $list = array_merge($list, $items);
            if (count($items) < 30) {
                break;
            }
        }
        file_put_contents($versionCache, json_encode($list));
    }
    $tags = json_decode(file_get_contents($versionCache));
    foreach ($tags as $tag) {
        $optionsHtml .= '<option value="' . $tag->name . '">' . $tag->name . '</option>';
        $versionDirectory = $directory . DIRECTORY_SEPARATOR . $tag->name;
        if (needDirectory($versionDirectory)) {
            chdir($versionDirectory);
            shell_exec('git clone ' . $gitHost . $url . ' .');
            shell_exec('git checkout tags/' . $tag->name);
            foreach (array('tests', 'examples') as $ignore) {
                if (file_exists($ignore)) {
                    shell_exec('rm -rf ' . $ignore);
                }
            }
            shell_exec('composer update --no-dev &');
        }
    }
    file_put_contents($cacheDirectory . DIRECTORY_SEPARATOR . $repository . '-versions-options.html', $optionsHtml);
}
