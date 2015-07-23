<?php
namespace simple\sys;

class Db {
    private static $_handlers;
    private static $_is_select = true;

    public static function execSql($sql) {
        self::$_is_select = (strtolower(substr(trim($sql), 0, 6)) == 'select'? true : false);
        if (self::$_is_select) {
            $data = array();
            $stmt = self::_handler()->query($sql);
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
        } else {
            $data = self::_handler()->exec($sql);
        }
        return $data;
    }

    private static function _handler() {
        $type = (self::$_is_select? 'r' : 'w');
        if (empty(self::$_handlers[$type]) || !(self::$_handlers[$type] instanceof \PDO)) {
            $db = Config::load('db');
            $db = $db[$type];
            $dsn = "mysql:host={$db['host']};dbname={$db['name']}";
            self::$_handlers[$type] = @new \PDO($dsn, $db['user'], $db['pass'], array(
                \PDO::ATTR_PERSISTENT => true, \PDO::ATTR_TIMEOUT => $db['timeout'],
            ));
            self::$_handlers[$type]->exec('SET NAMES utf8');
        }
        return self::$_handlers[$type];
    }

}
