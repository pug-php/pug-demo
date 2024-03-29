<?php

use Pug\Pug;

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

$expressionLanguages = [
  'php',
  'js'
];

if (class_exists('\\NodejsPhpFallback\\NodejsPhpFallback')) {
    \NodejsPhpFallback\NodejsPhpFallback::setModulePath('pug-cli', __DIR__ . '/../node_modules/pug-cli');
}

$renderingMode = empty($_POST['mode']);
$pugjs = !empty($_POST['pugjs']);
$scopeEachVariables = $_POST['scopeEachVariables'] ?? true;

if ($scopeEachVariables === 'true') {
    $scopeEachVariables = true;
} elseif ($scopeEachVariables === 'false') {
    $scopeEachVariables = false;
}

$pug = new Pug([
    'debug'                => $renderingMode,
    'allowMixedIndent'     => !empty($_POST['allowMixedIndent']),
    'allowMixinOverride'   => !empty($_POST['allowMixinOverride']),
    'scope_each_variables' => $scopeEachVariables,
    'classAttribute'       => empty($_POST['classAttribute']) ? null : $_POST['classAttribute'],
    'expressionLanguage'   => in_array($_POST['expressionLanguage'], $expressionLanguages) ? $_POST['expressionLanguage'] : 'auto',
    'indentChar'           => str_replace('\\t', "\t", $_POST['indentChar']),
    'indentSize'           => intval($_POST['indentSize']),
    'keepBaseName'         => !empty($_POST['keepBaseName']),
    'keepNullAttributes'   => !empty($_POST['keepNullAttributes']),
    'phpSingleLine'        => !empty($_POST['phpSingleLine']),
    'prettyprint'          => !empty($_POST['prettyprint']),
    'pugjs'                => $pugjs,
    'restrictedScope'      => !empty($_POST['restrictedScope']),
    'singleQuote'          => !empty($_POST['singleQuote']),
    'locator_class_name'   => SessionLocator::class,
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
    'filters' => [
        'markdown' => new \Pug\Filter\Markdown(),
    ],
]);

try {
    $vars = eval('return ' . $_POST['vars'] . ';');

    if (!empty($_POST['save_as'])) {
        $_SESSION['save_' . $_POST['save_as']] = $_POST['pug'];
    }

    if ($pugjs) {
        $html = $pug->render($_POST['pug'], $vars ?: []);

        echo substr($html, 0, 1) === "\n" ? substr($html, 1) : $html;
    } elseif ($renderingMode) {
        echo $pug instanceof \Jade\Jade
            ? $pug->render($_POST['pug'], __DIR__ . '/../index.pug', $vars ? $vars : array())
            : $pug->render($_POST['pug'], $vars ?: [], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'compile') {
        echo $pug->getCompiler()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'parse') {
        echo $pug->getCompiler()->getParser()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } elseif ($_POST['mode'] === 'lex') {
        echo $pug->getCompiler()->getParser()->getLexer()->dump($_POST['pug'], __DIR__ . '/../index.pug');
    } else {
        echo $pug->compile($_POST['pug'], __DIR__ . '/../index.pug');
    }
} catch (\Throwable $e) {
    $message = trim($e->getMessage());
    echo 'Error' . (substr($message, 0, 1) === '('
        ? preg_replace('/^\((\d+)\)(\s*:)?/', ' line $1:', $message)
        : ': ' . $message
    );
}
