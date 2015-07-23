<?php
$db = array();

// read
$db['r'] = array();
$db['r']['name']    = 'db_name';
$db['r']['user']    = 'user';
$db['r']['pass']    = 'pass';
$db['r']['timeout'] = 1;
if (\simple\IN_TEST_MODE) {
    $db['r']['host'] = '127.0.0.1';
} else {
    $db['r']['host'] = '127.0.0.1:3307';
}

return $db;