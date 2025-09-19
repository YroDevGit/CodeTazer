<?php

namespace Classes;

use Classes\Response;
use Exception;
use Throwable;

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

    private static function route_filtering(array $routes, callable $func, $included = true)
    {
        if (! $routes) {
            return false;
        }
        foreach ($routes as $r) {
            $path = substr($r, -4) == ".php" ? $r : $r . ".php";
            if (! file_exists("_backend/_routes/$path")) {
                Response::code(notfound_code)->message("In group route, backend route $r not found.!")->send(notfound_code);
            }
        }
        $current = current_be();
        if ($included) {
            if (in_array($current, $routes)) {
                try {
                    $func();
                } catch (Throwable $e) {
                    throw new Exception($e->getMessage());
                }
            }
        } else {
            if (! in_array($current, $routes)) {
                try {
                    $func();
                } catch (Throwable $e) {
                    throw new Exception($e->getMessage());
                }
            }
        }
        return true;
    }

    public static function group_route(array $routes, callable $func)
    {
        return self::route_filtering($routes, $func);
    }
    public static function except(array $routes, callable $func)
    {
        return self::route_filtering($routes, $func, false);
    }

    public static function set(string|array|null $routes, callable ...$args)
    {
        if (! $routes) {
            return false;
        }
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
                    try {
                        $func();
                    } catch (Throwable $e) {
                        throw new Exception($e->getMessage());
                    }
                }
            }
        } elseif (is_array($routes)) {
            foreach ($routes as $r) {
                self::set($r, ...$args);
            }
        } else {
            throw new Exception("Routing::set must be string or array only");
        }
        return true;
    }
}
