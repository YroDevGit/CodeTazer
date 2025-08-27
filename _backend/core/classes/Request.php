<?php

namespace Classes;

class Request
{

    static function post(string $key)
    {
        return post($key);
    }

    static function get(string $key)
    {
        return get($key);
    }

    static function all()
    {
        return postdata();
    }

    static function input($key)
    {
        return self::post($key);
    }

    static function headers(string|null $key = null, $ucwords = true)
    {
        if ($key == null) {
            return server_headers($key);
        } else {
            if ($ucwords) {
                return server_headers(ucwords(strtolower($key)));
            }
            return server_headers($key);
        }
    }

    static function origin()
    {
        return $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    }

    static function file($name, $type = null)
    {
        if (! $_FILES[$name]) {
            return null;
        }

        $file = $_FILES[$name];

        switch ($type) {
            case 'name':
                return $file['name'];
                break;

            case 'size':
                return $file['size'];
                break;

            case 'tmp_name':
                return $file['tmp_name'];
                break;

            case 'type':
                return $file['type'];
                break;

            case 'blob':
                return file_get_contents($file['tmp_name']);
                break;

            default:
                return $file;
                break;
        }
    }
}
