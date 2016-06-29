<?php

use Pug\Pug;

require_once __DIR__ . '/../vendor/autoload.php';

$pug = new Pug(array(
    'singleQuote' => false,
    'prettyprint' => true,
));

$vars = eval('return ' . $_POST['vars'] . ';');

echo $pug->render($_POST['pug'], $vars);
