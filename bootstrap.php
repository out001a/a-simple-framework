<?php
namespace simple;

/// consts
const PROJECT      = 'Sample';
const ROOT_DIR     = __DIR__;
const IN_TEST_MODE = true;

/// autoload
spl_autoload_register(function($class) {
    $prefix = __NAMESPACE__ . '\\';
    if (strstr($class, $prefix)) {
        $file = ROOT_DIR . '/' . str_replace(array($prefix, '\\'), array('', '/'), $class) . '.php';
        if (file_exists($file)) {
            require $file;
        } else {
            throw new \Exception("class not found, {$class}", -1);
        }
    }
});

return;