<?php

use NodejsPhpFallback\NodejsPhpFallback;
use Pug\Pug;

if (!file_exists(__DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php')) {
    echo 'Update in progress, please retry in few minutes.';
    exit;
}

require_once __DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php';

$expressionLanguages = array(
  'php',
  'js'
);

if (class_exists('\\NodejsPhpFallback\\NodejsPhpFallback')) {
    NodejsPhpFallback::setModulePath('pug-cli', __DIR__ . '/../node_modules/pug-cli');
}

$pug = new Pug(array(
    'allowMixedIndent'   => !empty($_POST['allowMixedIndent']),
    'allowMixinOverride' => !empty($_POST['allowMixinOverride']),
    'classAttribute'     => empty($_POST['classAttribute']) ? null : $_POST['classAttribute'],
    'expressionLanguage' => in_array($_POST['expressionLanguage'], $expressionLanguages) ? $_POST['expressionLanguage'] : 'auto',
    'indentChar'         => str_replace('\\t', "\t", $_POST['indentChar']),
    'indentSize'         => intval($_POST['indentSize']),
    'keepBaseName'       => !empty($_POST['keepBaseName']),
    'keepNullAttributes' => !empty($_POST['keepNullAttributes']),
    'phpSingleLine'      => !empty($_POST['phpSingleLine']),
    'prettyprint'        => !empty($_POST['prettyprint']),
    'pugjs'              => !empty($_POST['pugjs']),
    'restrictedScope'    => !empty($_POST['restrictedScope']),
    'singleQuote'        => !empty($_POST['singleQuote']),
));

$vars = eval('return ' . $_POST['vars'] . ';');

try {
    if (empty($_POST['compileOnly'])) {
        echo $pug->render($_POST['pug'], $vars);
    } else {
        echo $pug->compile($_POST['pug']);
    }
} catch (\Exception $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
