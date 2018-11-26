<?php

use Tale\Pug\Renderer;

if (!file_exists(__DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php')) {
    echo 'Update in progress, please retry in few minutes.';
    exit;
}

include_once __DIR__ . '/base.php';
require_once __DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php';

$renderer = new Renderer(array(
    'pretty' => !empty($_POST['prettyprint']),
));

try {
    $vars = eval('return ' . $_POST['vars'] . ';');
    $vars = $vars ? $vars : array();

    if (empty($_POST['mode'])) {
        extract($vars);
        eval('?>'.$renderer->getCompiler()->compile($_POST['pug']));
    } else {
        echo $renderer->getCompiler()->compile($_POST['pug']);
    }
} catch (\Throwable $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
