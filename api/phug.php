<?php

use Phug\Phug;

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

$options = array(
    'lexer_options' => array(
        'allow_mixed_indent' => !empty($_POST['allowMixedIndent']),
    ),
    'class_attribute'  => empty($_POST['classAttribute']) ? null : $_POST['classAttribute'],
    'pretty'           => empty($_POST['prettyprint']) ? false : str_repeat(str_replace('\\t', "\t", $_POST['indentChar']), intval($_POST['indentSize'])),
);

$vars = eval('return ' . $_POST['vars'] . ';');

try {
    if (empty($_POST['compileOnly'])) {
        Phug::displayString($_POST['pug'], $vars ?: array(), $options);
    } else {
        echo Phug::getRenderer($options)->getCompiler()->compile($_POST['pug']);
    }
} catch (\Exception $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
