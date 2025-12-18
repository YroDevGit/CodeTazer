<?php

namespace Classes;

class CCookie
{

    //create a function here...

    public static function add(string $key, mixed $value, int|float $hour = 24): bool
    {
        $newHour = 3600 * $hour;
        if (is_array($value)) {
            setcookie($key, json_encode($value), time() + $newHour, "/", true, true);
            return true;
        } else {
            setcookie($key, $value, time() + $newHour, "/");
            return true;
        }
        return false;
    }

    public static function delete(string $key): bool
    {
        setcookie($key, "", time() - 3600, "/", true, true);
        unset($_COOKIE[$key]);
        return true;
    }

    public static function exist(): bool
    {
        if (isset($_COOKIE['user'])) {
            return true;
        }
        return false;
    }

    public static function get(string $key)
    {
        if (isset($_COOKIE[$key])) {
            $cookie = $_COOKIE[$key];
            $ret = json_decode($session, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $ret;
            } else {
                $post = $cookie;
            }
        }
        return null;
    }
}
