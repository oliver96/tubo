<?php

class String {

    public static function objToArray($obj) {
        $arr = array();
        if (is_object($obj)) {
            $vars = get_object_vars($obj);
        } else {
            $vars = $obj;
        }
        if (!empty($vars)) {
            foreach ($vars as $key => $val) {
                if (is_object($val) || is_array($val)) {
                    $arr[$key] = String::objToArray($val);
                } else {
                    $arr[$key] = $val;
                }
            }
        }
        return $arr;
    }

    public static function jsonDecode($json) {
        if (empty($json))
            return array();
        $jsonObj = json_decode(stripslashes($json));
        return String::objToArray($jsonObj);
    }

    public static function jsonEncode($data) {
        return json_encode($data);
    }

    public static function decode($str) {
        return trim(preg_replace("/[\\\\]{0,1}u([a-f0-9]{4})/e", "iconv('UCS-4LE', 'UTF-8', pack('V', hexdec('U$1')))", json_encode($str)), " \t\n\r\0\x0B\"'");
    }
}

?>
