<?php
namespace simple\cli;

use \Exception as Exception;
use \simple\sys\Response as Response;

class User {

    public function say($word) {
        // 正常输出
        Response::send((new \simple\app\User())->say($word));
        // 异常处理
        throw new Exception('exception occured!', Response::STATUS_EXPIRED);
    }

}
