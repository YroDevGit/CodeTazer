<?php

namespace Classes;

class Ctr
{

    //create a function here...
    public static function generate_token(string|int $text, string|null $key = null, int $length = 22): string
    {
        if (! $key) {
            return substr(md5(date("ymdHisA") . $text . getenv("hash_secret")), 0, $length);
        }
        return substr(md5(date("ymdHisA") . $text . $key), 0, $length);
    }
}
