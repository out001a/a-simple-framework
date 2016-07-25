<?php
namespace simple\app;

use simple\sys\Config;
use simple\model\User as UserModel;

class User {

    public function __construct() {
    }

    //////////////////////////////////////////////////////////

    public function say($word) {
        return array(
            'say' => (new UserModel())->say($word),
            'get' => $_GET,
            'redis_config' => Config::load('redis'),
            // 'server' => $_SERVER,
        );
    }

}