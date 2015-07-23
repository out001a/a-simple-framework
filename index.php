<?php
namespace simple;

use \simple\sys\Util as Util;

require __DIR__ . '/bootstrap.php';

/// ini
date_default_timezone_set('Asia/Shanghai');
if (\simple\IN_TEST_MODE) {
    error_reporting(E_ALL & ~E_STRICT);
    ini_set('display_errors', 'on');
} else {
    error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE);
    ini_set('display_errors', 'off');
}

/// fatal error handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error['type'] == E_ERROR) {
        if (!headers_sent()) {
            // header('HTTP/1.1 502 Bad Gateway');
            header('HTTP/1.1 503 Service Unavailable');
            // header(str_replace("\n", ' ', "X-ERRMSG: {$error['message']} - {$error['line']} {$error['file']}"));
            header(str_replace("\n", ' ', "X-ERRMSG: {$error['message']} - {$error['line']}"));
            echo json_encode(array('status' => -1, 'msg' => 'error occured!',));
        }
        exit();
    }
});

/// run
$sapi = php_sapi_name();

// 以cli模式（命令行）执行
if ($sapi == 'cli') {
    $code = 0;
    $msg  = 'SUCC';
    try {
        call_callable(get_callable('cli'));
    } catch (\Exception $e) {
        $msg  = $e->getMessage();
        $code = 1;
    }
    echo date('Y-m-d H:i:s'), ", {$msg}\n";
    exit($code);
}

// 以cgi模式（web程序）执行
if (stripos($sapi, 'cgi') !== false || stripos($sapi, 'apache') !== false) {
    try {
        call_callable(get_callable('cgi'));
    } catch (\Exception $e) {
        Util::dealException($e);
    }
    exit();
}

exit();

function call_callable($callable) {
    if (is_callable($callable[0])) {
        return call_user_func_array($callable[0], $callable[1]);
    } else {
        throw new \Exception("method is not callable, {$callable[0][1]}", -1);
    }
}

function get_callable($sapi_type) {
    $class  = '';
    $method = '';
    $args   = array();

    switch ($sapi_type) {
        case 'cli':
            global $argv, $argc;
            @list(, $class, $method) = $argv;
            if ($argc > 3) {
                $args = array_slice($argv, 3);
            }
            break;
        case 'cgi':
            $query = explode('&', $_SERVER['QUERY_STRING']);
            $argv  = explode('/', '/' . ltrim($query[0], '/'));
            $argc  = count($argv);
            @list(, $class, $method) = $argv;
            if ($argc > 3) {
                $args = array_slice($argv, 3);
            }
            break;
        default:
            break;
    }
    if (empty($class)) {
        $class = 'index';
    }
    $class = __NAMESPACE__ . "\\{$sapi_type}\\" . $class;
    if (empty($method)) {
        $method = 'index';
    }

    return array(array(new $class(), $method), $args);
}
