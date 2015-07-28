<?php
namespace simple\sys;

class Util {
    static function ip2Numeric($ip) {
        $numeric = sprintf('%u', ip2long($ip));
        if (empty($numeric)) {
            throw new \Exception('given ip string is invalid!', -1);
        }
        return $numeric;
    }

    static function obtainHttpUserIp() {
        if (!empty($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($ips as $ip) {
                $ip = trim($ip);
                if (!empty($ip)) {
                    return $ip;
                }
            }
        }

        return false;
    }

    static function getHttpRealHost() {
        return isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (
            isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
    }

    static function getRequestArg($name, $default=null) {
        $val = isset($_REQUEST[$name])? trim($_REQUEST[$name]) : $default;
        if ($val) {
            $val = str_replace(array('\\', '\'', '"', '(', ')', ';'), '', $val);
            $val = trim(addslashes($val));
        }
        return $val;
    }

    static function versionToInt($version) {
        static $set = array();
        if (isset($set[$version])) {
            $vernum = $set[$version];
        } else {
            // 不能正确转换所有小于1的版本号如0.4或0.4.3等等。TODO ....
            $vernum = intval(substr(intval(str_replace('.', '', trim($version))).str_repeat('0', 10), 0, 10));
            $set[$version] = $vernum;
        }
        return $vernum;
    }

    static function checkVersionAllowed($version, $allowed_version_list='-') {
        if ($allowed_version_list == '-') {
            return true;
        }

        if (!is_int($version)) {
            $version = self::versionToInt($version);
        }
        $allowed_versions = explode(',', $allowed_version_list);

        foreach ($allowed_versions as $allowed) {
            if (strstr($allowed, '-')) {
                $two = explode('-', $allowed);

                $min_version = self::versionToInt($two[0]);
                $max_version = self::versionToInt($two[1]);

                // -
                if ($min_version == 0 && $max_version == 0) {
                    return true;
                }
                /// 以下三个if条件可以合并成一个，不过下面这样更清楚一些
                // 1.2.3-4.5.6
                if ($version >= $min_version && $version <= $max_version) {
                    return true;
                }
                // -3.4.5
                if ($min_version == 0 && $version <= $max_version) {
                    return true;
                }
                // 2.3.4-
                if ($version >= $min_version && $max_version == 0) {
                    return true;
                }
            } else {
                if ($version == self::versionToInt($allowed)) {
                    return true;
                }
            }
        }

        return false;
    }

    static function serverResponse($status = 1, $msg = '', $result = array(), array $extra = array()) {
        $response = array('status' => intval($status), 'msg' => strval($msg));
        if ($status > 0 || $result) {
            $response = array_merge($response, $extra);
            $response['result'] = $result;
        }
        return json_encode($response);
    }

    static function httpHeader200($msg = '', $status = 0) {
        header('HTTP/1.1 200 OK');
        echo self::serverResponse($status, $msg);
        exit;
    }

    static function httpHeader404($msg = '', $status = 0) {
        header('HTTP/1.1 404 Not Found');
        echo self::serverResponse($status, $msg);
        exit;
    }

    static function httpHeader500($msg = '', $status = -1) {
        header('HTTP/1.1 500 Internal Server Error');
        echo self::serverResponse($status, $msg);
        exit;
    }

    static function httpHeader503($msg = '', $status = -1) {
        header('HTTP/1.1 503 Service Unavailable');
        echo self::serverResponse($status, $msg);
        exit;
    }

    static function httpHeaderNoCache() {
        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
    }

    static function getUrlContents($url, $timeout=1) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $contents = trim(curl_exec($curl));
        $response = curl_getinfo($curl);
        curl_close($curl);
        if ($response['http_code'] != 200) {
            return false;
        }
        return $contents;
    }

}