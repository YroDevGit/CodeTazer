<?php

namespace Classes;

use Exception;
use TypeError;
use ValueError;

class Request
{

    static function post(string $key, bool $trim = true)
    {
        $post = post($key);
        if (is_null($post)) {
            return null;
        }
        if (is_array($post)) {
            return $post;
        }
        if (is_string($post)) {
            return $trim ? trim($post) : $post;
        }
        return $post;
    }

    static function array(string $key, string|null|int $subkey = null){
        $post = post($key);
        if(! is_array($post)){
            $type = gettype($post);
            throw new Exception("Request::array should be an array, given value is $type");
        }
        if($subkey){
            return $post[$subkey] ?? null;
        }
        return $post;
    }

    static function get(string $key, bool $trim = true)
    {
        $get = get($key);
        if (is_null($get)) {
            return null;
        }
        if ($trim) {
            return trim($get ?? "");
        }
        return $get;
    }

    static function all()
    {
        return postdata();
    }

    static function input(string $key, bool $trim = true)
    {
        return self::post($key, $trim);
    }

    static function headers(string|null $key = null, $ucwords = false)
    {
        if (is_null($key)) {
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
        try {
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
                    $data = file_get_contents($file['tmp_name']);
                    return $data;
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
        } catch (TypeError $e) {
            return null;
        } catch (ValueError $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }
}
