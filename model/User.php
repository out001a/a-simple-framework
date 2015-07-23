<?php
namespace simple\model;

class User implements \simple\spec\model\User {

    public function __construct() {
    }

    public function say($word) {
        return $word;
    }

}