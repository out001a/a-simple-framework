<?php
namespace simple\sys;

class DataFetcher {
    public function __construct() {
    }

    public static function fetchSqlOne($sql, $ttl=0) {
        $data = self::fetchSql($sql, $ttl);
        if (count($data) > 0) {
            $data = $data[0];
        }
        return $data;
    }

    public static function fetchSql($sql, $ttl=0) {
        $cache = new Cache();
        $c_key = self::cacheKey($sql);

        $data = $cache->get($c_key);
        if ($data) {
            $data = msgpack_unpack($data);
        } else {
            $data = Db::execSql($sql);
            $cache->setex($c_key, $ttl, msgpack_pack($data));
        }

        return $data;
    }

    public static function cacheKey($str) {
        return \simple\PROJECT . '::' . preg_replace(array('/\s\s+/', '/\r?\n/'), ' ', $str);
    }
}
