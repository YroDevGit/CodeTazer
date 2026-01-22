<?php
use Classes\Migration;

$jsonfile = $filename;

$jsonfile = str_ends_with($jsonfile, ".json") ? $jsonfile : $jsonfile . ".json";
$json = file_get_contents($jsonfile);

$json = json_decode($json, true);

foreach($json as $k=>$v){
    $is_ts = false;
    $is_ac = false;
    $table = $k;
    $columns = $v;
    unset($columns["--attr"]);
    foreach($v as $attr=>$val){
        if($attr == "--attr"){
            if(is_array($val)){
                foreach($val as $kk=>$vv){
                    if($vv == "timestamp" || $vv == "ts"){
                        $is_ts = true;
                    }
                    if($vv == "active" || $vv == "act"){
                        $is_ac = true;
                    }
                }
            }
            continue;
        }

        Migration::table($table, $columns, $is_ts, $is_ac);
    }
}

echo "\n";