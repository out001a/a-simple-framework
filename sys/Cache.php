<?php
namespace simple\sys;

class Cache {

    private static $_clients = array(
        array('cli' => null,),
    );

    // 过期时间（秒）
    // 每次设置过期时间时最好向前后随机偏移几分钟，见setExpire方法
    private $_expire     = 1200;    // 20 * 60
    private $_serverInfo = null;

    public function __construct() {
        $this->_serverInfo = Config::load('redis');
    }

    //////////////////////////////////////////////////////////////

    public function setExpire($seconds = null) {
        $seconds = intval($seconds);
        if ($seconds > 0) {
            $this->_expire = $seconds;
        } else {
            $h = date('H');
            if ($h < 2 || $h > 19) {
                $this->_expire = 30 * 60 + rand(0, 10 * 60); // 晚间高峰期增加缓存时间
            } else {
                $this->_expire = 20 * 60 + rand(0, 5 * 60);
            }
        }
    }

    //////////////////////////////////////////////////////////////

    /// 通用方法

    public function __call($name, $args) {
        $rtn = false;
        try {
            $redis = $this->_conn($k);
            if (method_exists($redis, $name)) {
                $rtn = call_user_func_array(array($redis, $name), $args);
            }
        } catch (\Exception $e) {
            // pass
        }
        return $rtn;
    }

    /// 特殊方法

    public function setex($k, $ttl, $v) {
        $rtn = false;
        try {
            $redis = $this->_conn($k);
            if ($ttl <= 0) {
                $ttl = $this->_expire;
            }
            $rtn = $redis->setex($k, $ttl, $v);
        } catch (\Exception $e) {
            // pass
        }
        return $rtn;
    }

    //////////////////////////////////////////////////////////////

    // 按key哈希到不同的服务器
    private function _conn($key) {
        $id = $this->_getServerId($key, count($this->_serverInfo['server']));

        if (!(self::$_clients[$id]['cli'] instanceof \Redis)) {
            self::$_clients[$id]['cli'] = new \Redis();
        }

        if (!@is_resource(self::$_clients[$id]['cli']->socket)) {
            $server = $this->_serverInfo['server'][$id];
            self::$_clients[$id]['cli']->pconnect($server['host'], $server['port'], $this->_serverInfo['timeout']);
        }

        $this->setExpire();

        return self::$_clients[$id]['cli'];
    }

    private function _getServerId($key, $server_count) {
        return hexdec(substr(md5(strrev($key)), 0, 3)) % $server_count;
    }

}
