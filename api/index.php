<?php

use Pug\Pug;

require_once __DIR__ . '/../vendor/autoload.php';

$pug = new Pug(array(
    'singleQuote' => false,
    'prettyprint' => true,
));

$vars = eval('return ' . $_POST['vars'] . ';');

try {
    echo $pug->render($_POST['pug'], $vars);
} catch (\Exception $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
