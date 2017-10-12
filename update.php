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
            if (!is_array($items)) {
                $items = @json_decode(file_get_contents(
                    __DIR__ . '/fallback/' . $repository . '-tags.json'
                ));
            }
            $list = array_merge($list, $items);
            if (count($items) < 30) {
                break;
            }
        }
        file_put_contents($versionCache, json_encode($list));
    }
    $touched = false;
    $tags = json_decode(file_get_contents($versionCache));
    usort($tags, function ($a, $b) {
        $a = strtolower($a->name);
        $b = strtolower($b->name);
        if ($a === $b) {
            return 0;
        }
        if (strpos($a, $b) === 0) {
            return 1;
        }
        if (strpos($b, $a) === 0) {
            return -1;
        }
        
        $tab = [$a, $b];
        sort($tab, SORT_STRING);
        
        return $a === $tab[0] ? 1 : -1;
    });
    foreach ($tags as $tag) {
        echo "Load $url {$tag->name}\n";
        $optionsHtml .= '<option value="' . $tag->name . '">' . $tag->name . '</option>';
        $versionDirectory = $directory . DIRECTORY_SEPARATOR . $tag->name;
        if (needDirectory($versionDirectory)) {
            $touched = true;
            chdir($versionDirectory);
            echo shell_exec('git clone ' . $gitHost . $url . ' .');
            echo shell_exec('git checkout tags/' . $tag->name);
            foreach (array('tests', 'examples') as $ignore) {
                if (file_exists($ignore)) {
                    shell_exec('rm -rf ' . $ignore);
                }
            }
            echo shell_exec('composer update --no-dev &');
        }
    }
    if ($touched) {
        file_put_contents($cacheDirectory . DIRECTORY_SEPARATOR . $repository . '-versions-options.html', $optionsHtml);
    }
}
