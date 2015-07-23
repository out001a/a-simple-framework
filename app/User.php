<?php
namespace simple\app;

use \simple\sys\Util as Util;
use \simple\sys\Config as Config;

class User {

    public function __construct() {
    }

    //////////////////////////////////////////////////////////

    public function say($word) {
        return array(
            'say' => (new \simple\model\User())->say($word),
            'get' => $_GET,
            'redis_config' => Config::load('redis'),
            // 'server' => $_SERVER,
        );
    }

}