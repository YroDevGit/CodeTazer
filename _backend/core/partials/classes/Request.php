<?php

namespace Classes;

use Exception;
use TypeError;
use ValueError;
use Classes\Response;

class Request
{
    private static string|null $xrateMessage = null;

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

    private static function ctrratelimit($limit = 100, $seconds = 60, $route = "")
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $window = $seconds;
        $org = $route;
        $route = ! $route ? current_be() : "ctzr_" . $route;
        $file = sys_get_temp_dir() . '/ratelimit_' . md5($route . '_' . $ip);
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (time() - $data['start'] > $window) {
                $data = ['count' => 0, 'start' => time()];
            }
        } else {
            $data = ['count' => 0, 'start' => time()];
        }

        $data['route'] = $org;
        $data['ctr'] = $route;
        $data['count']++;
        $data['left'] = $limit - intval($data['count']);
        $data['limit'] = $limit;
        $data['seconds'] = $seconds;
        $remaining = max(0, $limit - $data['count']);
        $reset = $data['start'] + $window;

        header("X-RateLimit-Limit: $limit");
        header("X-RateLimit-Remaining: $remaining");
        header("X-RateLimit-Reset: $reset");

        if ($data['count'] > $limit) {
            header('Content-Type: application/json');
            http_response_code(429);
            header('Retry-After: ' . ($window - (time() - $data['start'])));
            $msg = ! self::$xrateMessage ? 'Request limit exceed, Please try again later.' : self::$xrateMessage;
            echo json_encode([
                'code' => 429,
                'message' => $msg,
                'error' => 'Request limit exceeded',
                'limit' => $limit,
                'window' => $window,
                'retry_after' => $window - (time() - $data['start'])
            ]);
            exit;
        }
        return file_put_contents($file, json_encode($data));
    }

    private static function ctrratedetails($route = "")
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $window = 60;
        $limit = 100;
        $route = ! $route ? current_be() : "ctzr_" . $route;
        $file = sys_get_temp_dir() . '/ratelimit_' . md5($route . '_' . $ip);
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $window = $data['seconds'] ?? $window;
            $limit = $data['limit'] ?? $limit;
            if (time() - $data['start'] > $window) {
                $data = ['count' => 0, 'start' => time()];
            }
        } else {
            $data = ['count' => 0, 'start' => time()];
        }
        $remaining = max(0, $limit - $data['count']);
        $reset = $data['start'] + $window;
        $data['reset'] = $reset;
        return $data;
    }

    private static function fileGetter($name, $type = null, $st = 0)
    {
        try {
            $file = null;
            if (is_string($name)) {
                if (!isset($_FILES[$name]) || ! $_FILES[$name]) {
                    return null;
                }
                $file = $_FILES[$name];
            } else {
                $file = $name;
            }

            $nm = $file['name'] ?? null;

            if (!$nm) {
                return null;
            }

            if (is_array(($nm))) {
                $newarr = [];
                $ret = [];
                $count = 0;
                foreach ($nm as $n) {
                    $newarr['name'] = $file['name'][$count];
                    $newarr['error'] = $file['error'][$count];
                    $newarr['full_path'] = $file['full_path'][$count];
                    $newarr['size'] = $file['size'][$count];
                    $newarr['tmp_name'] = $file['tmp_name'][$count];
                    $newarr['type'] = $file['type'][$count];
                    $fer = self::file($newarr, $type, 1);
                    $ret[$count] = $fer;
                    $count++;
                }
                return $ret;
            }

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

    public static function file($name, $type = null)
    {
        if (!isset($_FILES[$name]) || ! $_FILES[$name]) {
            return null;
        }
        $fl = $_FILES[$name]['name'];
        if (is_array($fl)) {
            throw new Exception("File $name should be a single file, given (Collections of file)");
        }
        return self::fileGetter($name, $type);
    }

    public static function files($name, $type = null)
    {
        if (!isset($_FILES[$name]) || ! $_FILES[$name]) {
            return null;
        }
        $fl = $_FILES[$name]['name'];
        if (! is_array($fl)) {
            throw new Exception("File $name should be a multiple file, given (single file)");
        }
        return self::fileGetter($name, $type);
    }

    /**
     * Limit the request to backend
     * @param int $limit : max request
     * @param int $seconds: max request per seconds
     * @param string $route: unique route/name for this limit
     */
    public static function x_rate_limit(int $limit = 100, int $seconds = 60, string|null $route = "")
    {
        return self::ctrratelimit($limit, $seconds, $route);
    }

    public static function x_rate_limit_message(string|null $message)
    {
        if ($message) {
            self::$xrateMessage = $message;
        }
    }

    public static function x_rate_limit_global(int $limit = 100, int $seconds = 60)
    {
        return self::x_rate_limit($limit, $seconds, "all");
    }

    public static function x_rate_limit_route(int $limit = 100, int $seconds = 60)
    {
        return self::x_rate_limit($limit, $seconds);
    }

    public static function x_rate_details(string|null $route = "")
    {
        return self::ctrratedetails($route);
    }

    public static function x_rate_details_global()
    {
        return self::x_rate_details("all");
    }
}
