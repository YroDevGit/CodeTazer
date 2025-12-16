<?php

use Classes\Response;
use Classes\Routing;
/**
 * Get all routes except api routes
 */
$routes = ctr_all_routes();
/**
 * This is web routes protection.
 * All routes is protected.
 * if you want to test routes, put yes on allow_testing @ .env
 */
Routing::group_route($routes, function () {
    /**Routes is protected by codetazer token */
    $domain_token = csrf_token();
    // if domain token is not present and allow_testing is no, then throw response
    if (! $domain_token && env('allow_testing') == "no") {
        Response::code(401)->message("Data is protected by " . env('rootpath'))->send(401);
    }
});

/**
 * This routes protection is created at December 16 2025
 * By: Tyrone Limen Malocon - CodeTazer Author.
 */
