<?php

use Phug\Phug;

if (!file_exists(__DIR__ . '/../var/engines/' . $_POST['engine'] . '/' . $_POST['version'] . '/vendor/autoload.php')) {
    echo 'Update in progress, please retry in few minutes.';
    exit;
}

include_once __DIR__ . '/base.php';
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
$scopeEachVariables = $_POST['scopeEachVariables'] ?? true;

if ($scopeEachVariables === 'true') {
    $scopeEachVariables = true;
} elseif ($scopeEachVariables === 'false') {
    $scopeEachVariables = false;
}

$options = array(
    'debug'                => $renderingMode,
    'lexer_options'        => array(
        'allow_mixed_indent' => !empty($_POST['allowMixedIndent']),
    ),
    'locator_class_name'   => SessionLocator::class,
    'scope_each_variables' => $scopeEachVariables,
    'class_attribute'      => empty($_POST['classAttribute']) ? null : $_POST['classAttribute'],
    'pretty'               => !empty($_POST['prettyprint']),
    'get_file_contents'    => function ($path) {
        if (mb_substr($path, 0, 5) === 'save:') {
            $key = 'save_' . mb_substr($path, 5);
            if (isset($_SESSION[$key])) {
                return $_SESSION[$key];
            }
            
            return $key;
        }
        
        return file_get_contents($path);
    },
    'filters'              => array(
        'markdown' => new \Pug\Filter\Markdown(),
    ),
);

try {
    $vars = eval('return ' . $_POST['vars'] . ';');

    if (!empty($_POST['save_as'])) {
        $_SESSION['save_' . $_POST['save_as']] = $_POST['pug'];
    }

    $renderer = Phug::getRenderer($options);
    if ($renderingMode) {
        $method = method_exists($renderer, 'displayString') ? 'displayString' : 'display';
        $renderer->$method($_POST['pug'], $vars ?: array(), __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'compile') {
        echo $renderer->getCompiler()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'parse') {
        echo $renderer->getCompiler()->getParser()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'lex') {
        echo $renderer->getCompiler()->getParser()->getLexer()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } else {
        echo $renderer->getCompiler()->compile($_POST['pug'], __DIR__ . '/../index.pug');
    }
} catch (\Throwable $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
