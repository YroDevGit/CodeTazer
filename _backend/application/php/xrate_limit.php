<?php
use Classes\Request;

//Set the limit of request sent at the backend.
Request::x_rate_limit(200);
