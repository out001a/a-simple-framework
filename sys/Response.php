<?php
namespace simple\sys;

class Response {
    const STATUS_OK      = 0;
    const STATUS_UNKNOWN = -1;
    const STATUS_EXPIRED = -101;

    private static $_msgs = array(
        self::STATUS_OK      => 'OK',
        self::STATUS_UNKNOWN => '....',
    );

    public static function getMsg($status) {
        if (isset(self::$_msgs[$status])) {
            return self::$_msgs[$status];
        } else {
            return '';
        }
    }

    public static function send($result) {
        $resp = array(
            'status' => self::STATUS_OK,
            'msg'    => self::getMsg(self::STATUS_OK),
            'result' => $result,
        );
        echo json_encode($resp);
    }

    public static function dealException($e) {
        $status = intval($e->getCode());
        $statuses = (new \ReflectionClass(__CLASS__))->getconstants();
        if (!in_array($status, $statuses)) {
            $status = self::STATUS_UNKNOWN;
        }
        $resp = array(
            'status' => $status,
            'msg'    => self::getMsg($status),
        );
        if ($status == self::STATUS_EXPIRED) {
            $resp['t'] = time();
        }
        echo json_encode($resp);
    }

}