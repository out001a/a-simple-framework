<?php
$redis = array();

if (\simple\IN_TEST_MODE) {
    $redis['server'] = array(
        array('host' => 'localhost', 'port' => 6379),
    );
} else {
    $redis['server'] = array(
        array('host' => 'localhost', 'port' => 6380),
        array('host' => 'localhost', 'port' => 6381),
    );
}

$redis['timeout'] = 0.2; // seconds

return $redis;