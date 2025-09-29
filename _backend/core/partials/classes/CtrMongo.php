<?php

namespace Classes;

use Exception;
use MongoDB\Client;
use MongoDB\Driver\Session;

class CtrMongo
{
    private static $client;
    private static $db;

    private static $setclient = null;
    private static $setdb = null;
    private static ?Session $session = null;

    private function __construct() {} 

    public static function setURI(string $uri){
        self::$setclient = $uri;
    }

    public static function setDB(string $db){
        self::$setdb = $db;
    }

    private static function init()
    {
            $uri = self::$setclient ?? getenv("MONGO_URI");
            $dbname = self::$setdb ?? getenv("MONGO_DB");

            if(! $uri){
                throw new Exception("Mongo URI not set");
            }
            if (!$dbname) {
                throw new Exception("Mongo DB name not set");
            }

            self::$client = new Client($uri);
            self::$db = self::$client->$dbname;
    }

    public static function collection($name)
    {
        self::init();
        return self::$db->$name;
    }

    public static function begin()
    {
        self::init();
        if (!self::$session) {
            self::$session = self::$client->startSession();
            self::$session->startTransaction();
        }
        return self::$session;
    }

    public static function commit()
    {
        if (self::$session) {
            self::$session->commitTransaction();
            self::$session->endSession();
            self::$session = null;
        }
    }

    public static function rollback()
    {
        if (self::$session) {
            self::$session->abortTransaction();
            self::$session->endSession();
            self::$session = null;
        }
    }

    public static function getSession()
    {
        return self::$session;
    }
}
