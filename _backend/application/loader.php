<?php
//This is Backend Autoload

// Auto load file inside php folder
autoload_php([
    "autoload",
    //xrate_limit,
]);


// Autoload file inside routing folder
autoload_routing([
    "web",
    "path",
    "api",  
]);
