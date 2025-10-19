<?php

namespace Classes;

use Exception;
use TypeError;
use ValueError;
use Classes\Response;

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

    static function array(string $key, string|null|int $subkey = null)
    {
        $post = post($key);
        if (! is_array($post)) {
            $type = gettype($post);
            throw new Exception("Request::array should be an array, given value is $type");
        }
        if ($subkey) {
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
            $key = strtolower($key);
            if ($ucwords) {
                return server_headers($key);
            }
            return server_headers($key);
        }
    }

    static function validate_csrf()
    {
        $post = self::headers("X_CSRF_CTR_Token") ?? null;
        if (! $post) {
            Response::code(unauthorized_code)->message("csrf not found")->data(self::headers())->send(unauthorized_code);
        }
        if ($post !== csrf_token()) {
            Response::code(unauthorized_code)->message("Unauthorize request (csrf)")->send(unauthorized_code);
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

    public static function x_rate_limit($limit = 80, $seconds = 60)
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $window = $seconds;

        $file = sys_get_temp_dir() . '/ratelimit_' . md5($ip);

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['start'] > $window) {
                $data = ['count' => 0, 'start' => time()];
            }
        } else {
            $data = ['count' => 0, 'start' => time()];
        }

        $data['count']++;
        $remaining = max(0, $limit - $data['count']);
        $reset = $data['start'] + $window;

        header("X-RateLimit-Limit: $limit");
        header("X-RateLimit-Remaining: $remaining");
        header("X-RateLimit-Reset: $reset");

        if ($data['count'] > $limit) {
            http_response_code(429);
            header('Retry-After: ' . ($window - (time() - $data['start'])));
            echo json_encode([
                'code' => 429,
                'message' => 'Request limit exceed',
                'error' => 'Request limit exceeded',
                'limit' => $limit,
                'window' => $window,
                'retry_after' => $window - (time() - $data['start'])
            ]);
            exit;
        }
        file_put_contents($file, json_encode($data));
    }
}
