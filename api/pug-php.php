<?php

use Pug\Pug;

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

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php';

session_start();

class SessionLocator extends \Phug\Compiler\Locator\FileLocator {
    public function locate($path, array $locations, array $extensions) {
        if (mb_substr($path, 0, 5) === 'save:') {
            return $path;
        }
        if (isset($_SESSION['save_' . $path])) {
            return 'save:' . $path;
        }

        return parent::locate($path, $locations, $extensions);
    }
}

$expressionLanguages = array(
  'php',
  'js'
);

if (class_exists('\\NodejsPhpFallback\\NodejsPhpFallback')) {
    \NodejsPhpFallback\NodejsPhpFallback::setModulePath('pug-cli', __DIR__ . '/../node_modules/pug-cli');
}

$renderingMode = empty($_POST['mode']);

$pug = new Pug(array(
    'debug'              => $renderingMode,
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
    'locator_class_name' => SessionLocator::class,
    'get_file_contents'  => function ($path) {
        if (mb_substr($path, 0, 5) === 'save:') {
            $key = 'save_' . mb_substr($path, 5);
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
            
            return $key;
        }
        
        return file_get_contents($path);
    },
    'filters' => array(
        'markdown' => new \Pug\Filter\Markdown(),
    ),
));

$vars = eval('return ' . $_POST['vars'] . ';');

if (!empty($_POST['save_as'])) {
    $_SESSION['save_' . $_POST['save_as']] = $_POST['pug'];
}

try {
    if ($renderingMode) {
        echo $pug instanceof \Jade\Jade
            ? $pug->render($_POST['pug'], __DIR__ . '/../index.pug', $vars ? $vars : array())
            : $pug->render($_POST['pug'], $vars ? $vars : array(), __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'parse') {
        echo $pug->getCompiler()->getParser()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'lex') {
        echo $pug->getCompiler()->getParser()->getLexer()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } else {
        echo $pug->compile($_POST['pug'], __DIR__ . '/../index.pug');
    }
} catch (\Exception $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
