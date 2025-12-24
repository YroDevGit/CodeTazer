<?php
use Classes\Routing;

//To enable api protection, go to loader.php and enable //"api",

$routes = [
    //Add routes here...
];

Routing::group_route($routes, function () {
    // Add auto load function here...

});
