<?php

defined("ctrc") || define("ctrc", $_GET['ctrc'] ?? $_GET['page'] ?? null);

if (! function_exists("ctr_content")) {
    function ctr_content(string $content, array|string $query = [])
    {
        $params = "";
        if (is_array($query)) {
            $arr = [];
            foreach ($query as $k => $v) {
                $arr[] = $k . "=" . $v;
            }
            $params = implode("&", $arr);
            $params = "&" . $params;
        } else {
            $params = "&" . $params;
        }
        if (! $query) {
            $params = "";
        }
        $get = $_GET;
        $arr = [];
        $existing = "";
        foreach ($get as $k => $v) {
            if ($k == "ctrc" || $k == "page") {
                continue;
            }
            $arr[] = $k . "=" . $v;
            $existing = implode("&", $arr);
            $existing = "&" . $existing;
        }

        $pg = isset($_GET['page']) ? "page=" . $_GET['page'] . "&" : "";
        return "?" . $pg . "ctrc=" . $content . $params . $existing;
    }
}

// add more functions here...
