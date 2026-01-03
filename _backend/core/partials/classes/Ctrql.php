<?php

namespace Classes;

use Classes\Ccookie;
use Classes\Random;

class Ctrql
{

    private static $ctrqlString = "ctrql_cookie_cuser";

    //create a function here...

    public static function activate(string|array $access = "CRUDMQ", $minutes = 60)
    {
        $ctrqlString = self::$ctrqlString;
        $ccookie = Ccookie::exist($ctrqlString);

        if (is_array($access)) {
            $imp = implode("", $access);
            $access = $imp;
        }
        $access = strtoupper($access);
        if (! $ccookie) {
            Ccookie::add($ctrqlString, $access, $minutes);
        }
        Ccookie::delete($ctrqlString);
        Ccookie::add($ctrqlString, $access, $minutes);
        return true;
    }

    public static function remove()
    {
        $ctrqlString = self::$ctrqlString;
        $ccookie = Ccookie::exist($ctrqlString);
        if (! $ccookie) {
            return false;
        }
        Ccookie::delete($ctrqlString);
        return true;
    }

    public static function getAccess(): array
    {
        $ctrqlString = self::$ctrqlString;
        $ccookie = Ccookie::exist($ctrqlString);
        if (! $ccookie) {
            return [];
        }
        $cok = Ccookie::get($ctrqlString);

        $arr = str_split($cok);
        if (! $arr) {
            return [];
        }
        return $arr;
    }

    public static function checkAccess(string $access)
    {
        $access = strtoupper($access);
        $arr = self::getAccess();
        if (in_array($access, $arr)) {
            return true;
        }
        return false;
    }

    public static function accept_table(array $tables, $minutes = 60)
    {
        $ctrqlString = self::$ctrqlString . "_at";
        $ccookie = Ccookie::exist($ctrqlString);
        if (! is_array($tables)) {
            return "tables should be an array.!";
        }
        if (array_is_list($tables)) {
            return "tables should has key and value.!";
        }
        $tbl = json_encode($tables);
        if (! $ccookie) {
            Ccookie::add($tbl, $tbl, $minutes);
        }
        Ccookie::delete($ctrqlString);
        Ccookie::add($ctrqlString, $tbl, $minutes);
        return true;
    }

    public static function remove_accept_table_filter()
    {
        $ctrqlString = self::$ctrqlString . "_at";
        $ccookie = Ccookie::exist($ctrqlString);
        if ($ccookie) {
            Ccookie::delete($ctrqlString);
            return true;
        }
    }

    public static function remove_ignore_table_filter()
    {
        $ctrqlString = self::$ctrqlString . "_it";
        $ccookie = Ccookie::exist($ctrqlString);
        if ($ccookie) {
            Ccookie::delete($ctrqlString);
            return true;
        }
    }

    public static function refresh_table_filter()
    {
        self::remove_accept_table_filter();
        self::remove_ignore_table_filter();
    }

    public static function ignore_table(array $tables, $minutes = 60)
    {
        $ctrqlString = self::$ctrqlString . "_it";
        $ccookie = Ccookie::exist($ctrqlString);
        if (! is_array($tables)) {
            return "tables should be an array.!";
        }
        if (array_is_list($tables)) {
            return "tables should has key and value.!";
        }
        $tbl = json_encode($tables);
        if (! $ccookie) {
            Ccookie::add($tbl, $tbl, $minutes);
        }
        Ccookie::delete($ctrqlString);
        Ccookie::add($ctrqlString, $tbl, $minutes);
        return true;
    }


    public static function filterAction($action)
    {
        $arr = [
            "INSERT" => "C",
            "CREATE" => "C",
            "READ" => "R",
            "SELECT" => "R",
            "GET" => "R",
            "FIND" => "R",
            "FINDONE" => "R",
            "DELETE" => "D",
            "UPDATE" => "U",
        ];

        if (! array_key_exists($action, $arr)) {
            return null;
        }
        return $arr[$action];
    }

    public static function check_table(string|null $table, string|null $action)
    {
        if (! $table || ! $action) {
            return false;
        }
        $accept = Ccookie::get(self::$ctrqlString . "_at");
        $ignore = Ccookie::get(self::$ctrqlString . "_it");

        if ($ignore && array_key_exists($table, $ignore)) {
            $role = strtoupper($ignore[$table]);
            if ($role == "*") {
                $role = "CRUD";
            }
            $action = strtoupper($action);
            $access = strtoupper(self::filterAction($action));
            if (str_contains($role, $access)) {
                Response::code(unauthorized_code)->data(["role" => $role, "acc" => $access])->message("ctrql: User is not able to $action data @ '$table' table")->send(unauthorized_code);
            }
        }

        if ($accept && array_key_exists($table, $accept)) {
            $role = strtoupper($accept[$table]);
            if ($role == "*") {
                $role = "CRUD";
            }
            $action = strtoupper($action);
            $access = strtoupper(self::filterAction($action));
            if (! str_contains($role, $access)) {
                Response::code(unauthorized_code)->message("ctrql: User is not able to $action data @ '$table' table")->send(unauthorized_code);
            }
        }
    }
}
