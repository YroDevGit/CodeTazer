<?php

namespace Classes;

use Exception;
use Classes\Request;
use Classes\Random;

class CtrStorage
{
    protected static $autochangename = true;

    public static function auto_changename(bool $changename)
    {
        self::$autochangename = $changename;
    }
    protected static function dirfile()
    {
        return realpath(__DIR__ . "/../../../../");
    }
    protected static function storagepath()
    {
        return self::dirfile() . "\\" . self::relativepath();
    }

    protected static function relativepath()
    {
        return "_frontend\\core\\partials\\system\\storage\\";
    }

    //Pag gamit $upload =  Storage::upload_file($file)
    // $path = $upload['path'];
    static function upload_file($file, string|null $path = null)
    {
        if (! $file) {
            throw new Exception("File not found.!");
        }
        $pathname = self::storagepath();
        if (! is_dir($pathname)) {
            throw new Exception("Storage is not yet enabled");
        }
        if ($path) {
            $path = str_replace("/", "\\", $path);
            $pathname = $pathname . $path . "\\";
        }
        if (!is_dir($pathname)) {
            @mkdir($pathname, 0777, true);
        }

        if (is_string($file)) {
            $file = Request::file($file);
        }
        return self::upd($file, $pathname, $path);
    }


    protected static function upd($file, $dir, $path)
    {
        $path = is_null($path) ? "" : $path . "\\";
        $files = $file;
        $uploadDir = $dir;
        $single = false;
        if (!is_array($files['name'])) {
            $single = true;
            foreach ($files as $k => $v) {
                $files[$k] = [$v];
            }
        }

        $pp = [];
        $ff = [];
        $fp = [];
        if (self::$autochangename) {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                $fileName = basename($files['name'][$key]);
                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newfilename = Random::text(17);
                $targetFile = $uploadDir . $newfilename . "." . $extension;
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $fp[] = $targetFile;
                    $ff[] = $newfilename . "." . $extension;
                    $pp[] = self::relativepath() . $path . $newfilename . "." . $extension;
                } else {
                    throw new Exception("File not uploaded. (" . $fileName . ")");
                }
            }

            if ($single) {
                return [
                    "fullpath" => $fp[0] ?? $fp,
                    "file" => $ff[0] ?? $ff,
                    "filename" => $ff[0] ?? $ff,
                    "path" => $pp[0] ?? $pp
                ];
            }
            return [
                "fullpath" => $fp,
                "file" => $ff,
                "filename" => $ff,
                "path" => $pp
            ];
        } else {
            foreach ($files['tmp_name'] as $key => $tmpName) {
                $fileName = basename($files['name'][$key]);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetFile)) {
                    $fp[] = $targetFile;
                    $ff[] = $fileName;
                    $pp[] = self::relativepath() . $path . $fileName;
                } else {
                    throw new Exception("File not uploaded. (" . $fileName . ")");
                }
            }
            if ($single) {
                return [
                    "fullpath" => $fp[0] ?? $fp,
                    "file" => $ff[0] ?? $ff,
                    "filename" => $ff[0] ?? $ff,
                    "path" => $pp[0] ?? $pp
                ];
            }
            return [
                "fullpath" => $fp,
                "file" => $ff,
                "filename" => $ff,
                "path" => $pp
            ];
        }
    }
}
