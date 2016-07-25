<?php
namespace simple\cgi;

use Exception;
use simple\sys\Response;
use simple\app\User as UserApp;

class User {

    public function say($word) {
        // 正常输出
        Response::send((new UserApp())->say($word));
        // 异常处理
        throw new Exception('exception occured!', Response::STATUS_EXPIRED);
    }

}
