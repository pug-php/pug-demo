<?php

if (function_exists('xdebug_disable')) {
    xdebug_disable();
}

function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // Ce code d'erreur n'est pas inclu dans error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}

if (is_writeable(__FILE__)) {
    shell_exec('chmod -R 0555 ..');
}

set_error_handler('exception_error_handler');
error_reporting(0);

ini_set('html_errors', false);
ini_set('display_errors', '0');

include_once __DIR__ . '/../allow-csrf.php';
