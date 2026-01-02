<?php //route: ctr/sample

use Classes\Response;

$message = "This is CodeTazer Framework";

Response::code(200)->message($message)->send();

