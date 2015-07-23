<?php
namespace simple\cli;

use \simple\sys\Util as Util;

class User {

    public function say($word) {
        Util::output(\simple\app\User::say($word));
    }

}
