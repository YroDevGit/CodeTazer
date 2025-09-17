<?php

namespace Classes;

use Classes\Request;
use Exception;

class File
{

    public static function encode_blob(array|null|bool $data, string|array $columns): array
    {
        if (is_bool($data)) {
            return [];
        }
        $data = is_null($data) ? [] : $data;
        $columns = is_array($columns) ? $columns : [$columns];

        $isSingle = array_keys($data) !== range(0, count($data) - 1);
        $rows = $isSingle ? [$data] : $data;

        foreach ($rows as &$row) {
            foreach ($columns as $column) {
                if (!isset($row[$column]) || !$row[$column]) {
                    continue;
                }
                if (is_null($row[$column]) || $row[$column] == "") {
                    continue;
                }

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime  = finfo_buffer($finfo, $row[$column]);
                finfo_close($finfo);

                $row[$column] = "data:$mime;base64," . base64_encode($row[$column]);
            }
        }

        return $isSingle ? $rows[0] : $rows;
    }

    public static function blob_to_text(string|null|bool $blob): string|null
    {
        if (is_bool($blob)) {
            return null;
        }
        if (is_null($blob) || $blob === "") {
            return "";
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_buffer($finfo, $blob);
        finfo_close($finfo);

        return "data:$mime;base64," . base64_encode($blob);
    }

    public static function get($name, $type = null)
    {
        return Request::file($name, $type);
    }

    public static function is_image($file)
    {
        if (! $file) {
            throw new Exception("File not found");
        }
        $tmp = $file['tmp_name'];

        if (is_string($tmp)) {
            if ($tmp && exif_imagetype($tmp)) {
                return true;
            } else {
                return false;
            }
        } else if (is_array($tmp)) {
            foreach ($tmp as $t => $v) {
                $isNot = self::is_image(["tmp_name" => $v]);
                if (! $isNot) {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
}
