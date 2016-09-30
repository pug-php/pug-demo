<?php

use Pug\Pug;

require_once __DIR__ . '/../vendor/autoload.php';

$pug = new Pug(array(
    'allowMixedIndent'   => !empty($_POST['allowMixedIndent']),
    'allowMixinOverride' => !empty($_POST['allowMixinOverride']),
    'classAttribute'     => empty($_POST['classAttribute']) ? null : $_POST['classAttribute'],
    'expressionLanguage' => 'auto',
    'indentChar'         => str_replace('\\t', "\t", $_POST['indentChar']),
    'indentSize'         => $_POST['indentSize'],
    'keepBaseName'       => !empty($_POST['keepBaseName']),
    'keepNullAttributes' => !empty($_POST['keepNullAttributes']),
    'phpSingleLine'      => !empty($_POST['phpSingleLine']),
    'prettyprint'        => !empty($_POST['prettyprint']),
    'restrictedScope'    => !empty($_POST['restrictedScope']),
    'singleQuote'        => !empty($_POST['singleQuote']),
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
