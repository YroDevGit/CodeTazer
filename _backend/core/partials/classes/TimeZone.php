<?php
namespace Classes;

class TimeZone{

    //create a function here...

    static function set_default_timezone(string|null $timezone = "Asia/Manila"){
        return date_default_timezone_set($timezone);
    }

    static function get_default_timezone(){
        return date_default_timezone_get();
    }

}
?>