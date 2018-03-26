<?php

use Tale\Pug\Renderer;

function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // Ce code d'erreur n'est pas inclu dans error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");

if (!file_exists(__DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php')) {
    echo 'Update in progress, please retry in few minutes.';
    exit;
}

include_once __DIR__ . '/../allow-csrf.php';
require_once __DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php';

$renderer = new Renderer(array(
    'pretty' => !empty($_POST['prettyprint']),
));

$vars = eval('return ' . $_POST['vars'] . ';');
$vars = $vars ? $vars : array();

try {
    if (empty($_POST['mode'])) {
        extract($vars);
        eval('?>'.$renderer->getCompiler()->compile($_POST['pug']));
    } else {
        echo $renderer->getCompiler()->compile($_POST['pug']);
    }
} catch (\Exception $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
