<?php //route: test/sample

use Classes\Response;

$message = "This is CodeTazer Framework";

Response::code(200)->message($message)->send();

