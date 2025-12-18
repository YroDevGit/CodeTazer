<?php

defined("ctrc") || define("ctrc", $_GET['ctrc'] ?? null);

if(! function_exists("ctr_content")){
    function ctr_content(string $content, array|string $query = []){
        $params = "";
        if(is_array($query)){
            $arr = [];
            foreach($query as $k=>$v){
                $arr[] = $k."=".$v;
            }
            $params = implode("&", $arr);
            $params = "&" . $params;
        }else{
            $params = "&". $params;
        }
        return "?ctrc=". $content . $params;
    }
}

// add more functions here...

