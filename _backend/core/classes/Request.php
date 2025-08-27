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
        if (!isset($_FILES[$name]) || ! $_FILES[$name]) {
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

            case 'size_mb':
                $fileSizeBytes = $file['size'];
                $fileSizeMB = $fileSizeBytes / 1024 / 1024;
                return round($fileSizeMB, 2);
                break;

            case 'size_kb':
                $fileSizeBytes = $file['size'];
                $fileSizeMB = $fileSizeBytes / 1024;
                return round($fileSizeMB, 2);
                break;

            case 'size_gb':
                $fileSizeBytes = $file['size'];
                $fileSizeMB = $fileSizeBytes / 1024 / 1024 / 1024;
                return round($fileSizeMB, 2);
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

            case 'filetype':
            case 'extension':
                $filename = $file['name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);
                return $extension;
                break;

            default:
                return $file;
                break;
        }
    }
}
