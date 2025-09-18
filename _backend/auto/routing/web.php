<?php
//this is the web routing

use Classes\Routing;

$admin = [
    
];

Routing::group_route($admin, function () {
    use_middleware("auth");
});
