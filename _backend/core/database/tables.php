<?php
use Classes\Migration;


Migration::table_ts("authorization",[
    "id" => "@primary",
    "key" => "text",
    "user" => ["int"=>11],
    "status" => ["int"=>1, "default"=>1],
]);

Migration::table_ts("logs",[
    "id" => "@primary",
    "message" => "text",
    "type" => ["varchar"=>"20"],
]);

Migration::table_ts("user", [
    "id" => "@primary",
    "username" => "varchar",
    "password" => "varchar",
    "fullname" => "varchar",
    "status" => ["int"=>11, "default"=>1],
]);


