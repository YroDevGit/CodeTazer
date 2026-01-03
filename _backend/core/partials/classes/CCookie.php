<?php

namespace Classes;

class Ccookie
{

    //create a function here...

    public static function add(string $key, mixed $value, int|float $hour = 24): bool
    {
        $newHour = 3600 * $hour;
        if (is_array($value)) {
            $val = encrypt(json_encode($value));
            setcookie($key, $val, time() + $newHour, "/", "", isset($_SERVER['HTTPS']));
            $_COOKIE[$key] = $val;
            return true;
        } else {
            $val = encrypt($value);
            setcookie($key, $val, time() + $newHour, "/");
            $_COOKIE[$key] = $val;
            return true;
        }
        return false;
    }

    public static function delete(string $key): bool
    {
        setcookie($key, "", time() - 3600, "/", "", isset($_SERVER['HTTPS']));
        unset($_COOKIE[$key]);
        return true;
    }

    public static function exist(string $key): bool
    {
        if (isset($_COOKIE[$key])) {
            return true;
        }
        return false;
    }

    public static function get(string $key)
    {
        if (isset($_COOKIE[$key])) {
            $cookie = decrypt($_COOKIE[$key]);
            $ret = json_decode($cookie, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $ret;
            } else {
                $post = $cookie;
                return $post;
            }
        }
        return null;
    }
}
