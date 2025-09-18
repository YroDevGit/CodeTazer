<?php

namespace Classes;

use Classes\Response;
use Exception;

class Routing
{
    public static function in_route(string $route, callable $func)
    {
        $current = current_be();
        $current = trim($current);
        $route = trim($route);
        if (strtolower($current) == strtolower($route)) {
            $func();
        }
    }

    public static function group_route(array $routes, callable $func)
    {
        foreach ($routes as $r) {
            $path = substr($r, -4) == ".php" ? $r : $r . ".php";
            if (! file_exists("_backend/_routes/$path")) {
                Response::code(notfound_code)->message("In group route, backend route $r not found.!")->send(notfound_code);
            }
        }
        $current = current_be();
        if (in_array($current, $routes)) {
            $func();
        }
    }

    public static function set(string|array $routes, callable ...$args)
    {
        if (is_string($routes)) {
            $current = current_be();
            $path = substr($routes, -4) == ".php" ? $routes : $routes . ".php";
            if (! file_exists("_backend/_routes/$path")) {
                Response::code(notfound_code)
                    ->message("In set route, backend route $routes not found.!")
                    ->send(notfound_code);
            }

            if ($routes === $current) {
                foreach ($args as $func) {
                    $func();
                }
            }
        } elseif (is_array($routes)) {
            foreach ($routes as $r) {
                self::set($r, ...$args);
            }
        } else {
            throw new Exception("Routing::set must be string or array only");
        }
    }
}
