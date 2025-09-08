<?php

//Use this as:  use_middleware('auth');

$apikey = server_headers("apikey");
if($apikey !== getenv("default_apikey")){
    json_unauthorized(["error"=>"Invalid apikey"]);
}

?>