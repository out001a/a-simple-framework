<?php
namespace simple\sys;

class Config {
    private static $_configs = array();

    static function load($name) {
        $name = trim($name);
        if (empty(self::$_configs[$name])) {
            self::$_configs[$name] = require(__DIR__ . "/config/{$name}.php");
        }
        return self::$_configs[$name];
    }
}
