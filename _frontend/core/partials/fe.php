<?php

include "_frontend/core/partials/loadenv.php";

if (getenv("rootpath") == "" || getenv("rootpath") == null) {
    $rootpath = get_basixs_root_path();
    putenv("rootpath=$rootpath");
    $_ENV['rootpath'] = $rootpath;
}
define('rootpath', getenv('rootpath'));
define('pages', '_frontend/pages');
define('_backend', '_backend');
define('assets', '_frontend/assets');
define('codepath', '_frontend/code');

define('SUCCESS', getenv('success_code'));

define("success_code", getenv("success_code"));
define("error_code", getenv("error_code"));
define("db_error_code", getenv("db_error_code"));
define("notfound_code", getenv("notfound_code"));
define("forbidden_code", getenv("forbidden_code"));
define("unauthorized_code", getenv("unauthorized_code"));
define("badrequest_code", getenv("badrequest_code"));
define("warning_code", getenv("warning_code"));
define("no_internet_code", getenv("no_internet_code"));
define("backend_error_code", getenv("backend_error_code"));
define("failed_code", getenv("failed_code"));

define("now", date("Y-m-d H:i:s"));

if (! function_exists("now")) {
    function now($dateformat = "Y-m-d H:i:s")
    {
        return date($dateformat);
    }
}

if (! function_exists("env")) {
    function env(string $key)
    {
        return getenv($key);
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $v) {
            echo '<pre>';
            var_dump($v);
            echo '</pre>';
        }
        exit(1);
    }
}

if (! function_exists("val")) {
    function val(&$val, $datatype = "string")
    {
        $datatype = strtolower($datatype);
        if (! isset($val) || ! $val || $val == null || $val == "") {
            switch ($datatype) {
                case "string":
                case "text":
                    return "";
                    break;
                case "number":
                case "int":
                case "integer":
                case "float":
                case "double":
                case "num":
                    return "0";
                    break;
                case "array":
                case "object":
                    return [];
                    break;
                case "boolean":
                case "bool":
                    return false;
                    break;
                default: return ""; break;
            }
        }
    }
}

if (! function_exists("change_date")) {
    function change_date(string $date, string|null $interval)
    {
        $given = $date;
        $date = new DateTime($given);
        $date->modify($interval);
        ///or: $new   = date('Y-m-d H:i:s', strtotime($given . ' +20 minutes'));
        return $date->format('Y-m-d H:i:s');
    }
}

if (! function_exists("get_date")) {
    function get_date(string $date, string $format = "Y-m-d H:i:s")
    {
        $given = $date;
        $date  = new DateTime($given);
        return $date->format($format);
    }
}

if (! function_exists('page')) {
    function page(string $path = "=", mixed $param = [], $ext = false)
    {
        if ($path === "=") {
            return rootpath . "/?page=";
        }
        if ($path === "?") {
            return rootpath . "/?page";
        }
        $bb = explode("?", $path);
        $path = $bb[0];
        $params = isset($bb[1]) ? "?" . $bb[1] : "";
        if ($param) {
            if (is_array($param)) {
                $getter = "";
                foreach ($param as $k => $v) {
                    $getter .= $k . "=" . $v . "&";
                }
                $params = "?" . rtrim($getter, "&");
            } else {
                $params = "?param=" . $param;
            }
        }
        if ($path == "" || $path == null) {
            return rootpath . $params;
        } else {
            //$path = substr($path, -4) == ".php" ? $path : $path . ".php";
            return rootpath . "/?page=" . $path . $params;
        }
    }
}

if (! function_exists('function_page')) {
    function function_page(string $path = "?", mixed $param = [])
    {
        if ($path === "?") {
            return rootpath . "/?funcpage=";
        }
        $bb = explode("?", $path);
        $path = $bb[0];
        $params = isset($bb[1]) ? "?" . $bb[1] : "";
        if ($param) {
            if (is_array($param)) {
                $getter = "";
                foreach ($param as $k => $v) {
                    $getter .= $k . "=" . $v . "&";
                }
                $params = "?" . rtrim($getter, "&");
            } else {
                $params = "?param=" . $param;
            }
        }
        if ($path == "" || $path == null) {
            return rootpath . $params;
        } else {
            $path = substr($path, -4) == ".php" ? $path : $path . ".php";
            return rootpath . "/?funcpage=" . $path . $params;
        }
    }
}



if (! function_exists('back_end')) {
    function back_end(string $path = "=")
    {
        if ($path === "=") {
            return rootpath . "/?be=";
        }
        if ($path === "?") {
            return rootpath . "/?be";
        }
        $bb = explode("?", $path);
        $path = $bb[0];
        $param = isset($bb[1]) ? "?" . $bb[1] : "";
        if ($path == "" || $path == null) {
            return rootpath . $param;
        } else {
            $path = substr($path, -4) == ".php" ? $path : $path . ".php";
            return rootpath . "/?be=" . $path . $param;
        }
    }
}

if (! function_exists("current_page")) {
    function current_page(bool $php_exention = false): string
    {
        $filename =  $_SESSION['basixs_current_page'] ?? getenv('app_name') ?? "Page not set";
        if (! $php_exention) {
            $filename = substr($filename, -4) === '.php' ? substr($filename, 0, -4) : $filename;
            return $filename;
        }

        return $filename;
    }
}

if (! function_exists("page_title")) {
    function page_title()
    {
        return $_SESSION['basixs_current_page_title'];
    }
}

if (! function_exists("set_page_title")) {
    function set_page_title(string $pagetitle)
    {
        $_SESSION['basixs_current_page_title'] = $pagetitle;
    }
}

if (! function_exists('_backend')) {
    function _backend(string $path = "")
    {
        if ($path == "" || $path == null) {
            return _backend;
        } else {
            return _backend . "/" . $path;
        }
    }
}
if (! function_exists('assets')) {
    function assets(string $path = "")
    {
        if ($path == "" || $path == null) {
            return assets;
        } else {
            return assets . "/" . $path;
        }
    }
}

if (! function_exists('codepath')) {
    function codepath(string $path = "")
    {
        if ($path == "" || $path == null) {
            return codepath;
        } else {
            return codepath . "/" . $path;
        }
    }
}

if (! function_exists("has_internet_connection")) {
    function has_internet_connection($url = "http://www.google.com")
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        if ($data) {
            curl_close($ch);
            return true;
        } else {
            curl_close($ch);
            return false;
        }
    }
}

if (! function_exists("get")) {
    function get(string $key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
}


if (! function_exists('href')) {
    function href(string $path = "")
    {
        if ($path == "" || $path == null) {
            header('location: ' . rootpath);
        } else {
            header('location: ' . rootpath . "/?page=$path");
        }
    }
}

if (! function_exists('redirect')) {
    function redirect(string $path = "", string $type = "page", int $time = 0)
    {
        if ($type == "page") {
            header("refresh: $time; url=" . rootpath . "/?page=$path");
        }
        if ($type == "func") {
            header("refresh: $time; url=" . rootpath . "/?funcpage=$path");
        }
    }
}

if (! function_exists("write_sql_log")) {
    function write_sql_log($message)
    {
        $setting = getenv('sql_logs');
        if ($setting) {
            $filename = "sql_" . date("Y-M-d") . "_yros.log";
            $logfile =  "_backend/core/partials/app/dblogs/" . $filename;
            $formatted_message = "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL;
            file_put_contents($logfile, $formatted_message, FILE_APPEND);
        }
    }
}

if (! function_exists("write_sql_error")) {
    function write_sql_error($message, string $query = "")
    {;
        $setting = getenv('sql_errors');
        if ($setting == true) {
            $logfile = "_backend/core/partials/app/db_errors/sqlerrors.txt";

            $message = preg_replace('/\s+/', ' ', trim($message));
            $query = preg_replace('/\s+/', ' ', trim($query));

            $formatted_message = "[" . date('Y-m-d H:i:s') . "] " . $message . " ==>> QUERY: " . $query . PHP_EOL . PHP_EOL;
            file_put_contents($logfile, $formatted_message, FILE_APPEND);
        }
    }
}

if (! function_exists("view_page")) {
    function view_page(string $page, array $variables = [])
    {
        $page = substr($page, -4) == ".php" ? $page : $page . ".php";
        if (file_exists("_frontend/pages/$page")) {
            if (!empty($variables)) {
                extract($variables);
            }
            include "_frontend/pages/$page";
        } else {
            echo "<b style='color:red;background:black;padding:5px;font-weight:bold;'>Page $page doesn't exist.! Please check _frontend/pages/$page</b>";
        }
    }
}

if (! function_exists("include_page")) {
    function include_page(string $page, array $variables = [])
    {
        $page = substr($page, -4) == ".php" ? $page : $page . ".php";
        if (file_exists("_frontend/includes/$page")) {
            if (!empty($variables)) {
                extract($variables);
            }
            include "_frontend/includes/$page";
        } else {
            echo "<b style='color:red;background:black;padding:5px;font-weight:bold;'>Include page $page doesn't exist.! Please check _frontend/includes/$page</b>";
        }
    }
}

if (! function_exists("display_error111")) {
    function display_error111(string $message)
    {
        $str = new Exception($message);
        $arr = explode("#", $str);
        $err = [];
        foreach ($arr as $r) {
            if (strpos($r, '\app\system\helpers') !== false) {
            } elseif (strpos($r, '\app\system') !== false) {
            } elseif (strpos($r, '\index.php(11): require_once(') !== false) {
            } else {
                $err[] = $r;
            }
        }
        $ff = implode("\n", $err);
        $final = $message . " " . $ff;
        return $final;
    }
}

if (! function_exists("array_is_multidimensional")) {
    function array_is_multidimensional(array $arr)
    {
        foreach ($arr as $element) {
            if (is_array($element)) {
                return true;
            }
        }
        return false;
    }
}


if (! function_exists("php_file")) {
    function php_file($pagename)
    {
        $mainpage = substr($pagename, -4) == ".php" ? $pagename : $pagename . ".php";
        return $mainpage;
    }
}

define('page', page(""));
