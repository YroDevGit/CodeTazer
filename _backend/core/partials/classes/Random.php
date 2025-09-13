<?php

namespace Classes;

class Random
{

    //create a function here...

    static function text($characters = 15, $strick = true)
    {
        $arr = range("A", "Z");

        for ($x = 1; $x <= 9; $x++) {
            $arr[] = (string) $x;
        }
        shuffle($arr);
        $str = "";
        //return $arr;
        if ($strick) {
            $s = date("ymdhis");
            for ($i = 1; $i <= $characters - 12; $i++) {
                $str .= (string)$arr[$i];
            }
            $str .= $s;
        } else {
            for ($i = 1; $i <= $characters; $i++) {
                $str .= (string)$arr[$i];
            }
        }
        return $str;
    }

    static function string($characters = 10, $capitalize = true)
    {
        $arr = range("a", "z");
        if($capitalize){
            $arr = range("A", "Z");
        }
        shuffle($arr);
        $str = "";
        for ($i = 1; $i <= $characters; $i++) {
            $str .= (string)$arr[$i];
        }
        return $str;
    }

    static function integer($characters = 9){
        $arr = range(1, 10);
        shuffle($arr);
        $str = "";
        for ($i = 1; $i <= $characters; $i++) {
            $str .= (string)$arr[$i];
        }
        return (string) $str;
    }
}
