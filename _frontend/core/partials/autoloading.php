<?php

/**
 * Autoloading functions for PHP files and libraries
 * This file is part of the core functionality of the frontend.
 * It includes functions to autoload PHP files, use libraries, and import scripts.
 * Please do not modify this file directly.
 * Instead, create a custom autoload file in your project root if needed.
 * By CodeYro - Tyrone Lee Emz
 */

if (! function_exists("autoload_php")) {
    function autoload_php(string|array $filename = null)
    {
        if (!$filename) {
            return false;
        }
        if (is_array($filename)) {
            foreach ($filename as $f) {
                $loadpage = substr($f, -4) == ".php" ? $f : $f . ".php";
                include "_frontend/auto/php/" . $loadpage;
            }
        } else {
            $loadpage = substr($filename, -4) == ".php" ? $filename : $filename . ".php";
            include "_frontend/auto/php/" . $loadpage;
        }
    }
}
/**
 * THis is optional function for CodeTazer
 * include files from error page
 * @param string error_page
 * @param array variables
 * @param mixed exit
 */
if (! function_exists("include_error_page")) {
    function include_error_page(string|null $error_page, $variables = [], $exit = true)
    {
        $error_page = substr($error_page, -4) == ".php" ? $error_page : $error_page . ".php";
        $path = "_frontend/core/errors/$error_page";
        if (! file_exists($path)) {
            die("Error page '$error_page' not found.!");
        }
        if (!empty($variables)) {
            extract($variables);
        }
        include($path);
        if ($exit) {
            exit;
        }
    }
}
/**
 * This is optional function for CodeTazer
 * @param mixed filepath
 */
if (! function_exists("ctr_storage")) {
    function ctr_storage($filepath = "")
    {
        if ($filepath) {
            return "_frontend/core/partials/system/storage/" . str_replace("\\", "/", $filepath);
        }
        return "_frontend/core/partials/system/storage";
    }
}

if (! function_exists("file_multiple")) {
    function file_multiple(array|null $attribute = null)
    {
        $att = "";
        foreach ($attribute as $attr => $v) {
            $att .= " $attr='$v'";
        }
        return "<input type='file' multiple $att >";
    }
}

if (! function_exists("include_template_page")) {
    function include_template_page(string|null $template_page, $variables = [], $exit = true)
    {
        $error_page = $template_page;
        $error_page = substr($error_page, -4) == ".php" ? $error_page : $error_page . ".php";
        $path = "_frontend/core/template/$error_page";
        if (! file_exists($path)) {
            die("Template page '$error_page' not found.!");
        }
        if (!empty($variables)) {
            extract($variables);
        }
        include($path);
        if ($exit) {
            exit;
        }
    }
}

if (! function_exists("use_library")) {
    function use_library(string $library)
    {
        $model = substr($library, -4) == ".php" ? $library : $library . ".php";
        include "_frontend/core/library/" . $model;
    }
}

if (! function_exists("import_swal")) {
    function import_swal()
    {
?>
        <script src="<?= codepath('src/ctr/swal.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_ctr")) {
    function import_ctr()
    {
    ?>
        <script src="<?= codepath('src/ctr/ctr.js') ?>"></script>
    <?php
    }
}
/**
 * This is optional PHP func page where other PHP process works here
 * @param string filename: name of the function file
 * @author CodeYro
 */
if (! function_exists("import_func")) {
    function import_func(string $filename)
    {
        $fl = substr($filename, -3) == ".js" ? $filename : $filename . ".js";
    ?>
        <script src="<?= codepath('func/' . $fl) ?>"></script>
    <?php
    }
}
/**
 * Generic import js script file
 * this import file from :_frontend/code/script
 * @param string filenames
 * @author Tyrone Limen Malocon
 */
if (!function_exists("import_script")) {
    function import_script(string ...$filenames)
    {
        foreach ($filenames as $flx) {
            $fl = str_ends_with($flx, '.js') ? $flx : $flx . '.js';
            echo '<script type="module" src="' . htmlspecialchars(codepath('script/' . $fl), ENT_QUOTES) . '"></script>' . PHP_EOL;
        }
    }
}
/**
 * Generic import package for ctr js files,
 * You can use js file name with or without .js extension
 * @author CodeYro
 */
if (! function_exists("import_packages")) {
    function import_packages(string ...$packages)
    {
        /**
         * Code path @_frontend/code
         * @param null no param
         * @author Tyrone Limen Malocon
         * @author CodeYro
         */
        $cpath = codepath() . "/src/ctr/";
        foreach ($packages as $p) {
            $fl = substr($p, -3) == ".js" ? $p : $p . ".js";
            /**
             * this is default tyrax FE-BE communication tool
             * @author CodeTazer
             * @author Tyrone Limen Malocon
             * @author CodeYro
             * @param null no param
             */
            if ($fl == "tyrux.js" || $fl == "tyrax.js") {
                echo import_tyrux();
                continue;
            }
            $pt = $cpath . $fl;
            /**
             * Bundle js
             * @use for javascript/jquery bundle libraries
             * @author CodeYro
             */
            if ($fl == "bundle.js") {
                $css = $cpath . "bundle.css";
                echo "<link rel='stylesheet' href='$css'>";
                echo "<script src='$pt'></script>";
                continue;
            }
            /**
             * Optional DataTable Library
             * @use let dtable = new DataTable(document.querySelector("#tableid"));
             * @author CodeYro
             */
            if ($fl == "datatable.js") {
                $css = $cpath . "datatable.css";
                echo "<link rel='stylesheet' href='$css'>";
                echo "<script src='$pt'></script>";
                continue;
            }
            /**
             * Import path or paths is one
             * use: PATH.page("user/manage")
             * @author Tyrone Limen Malocon
             */
            if ($fl == "paths.js" || $fl == "path.js") {
                import_paths();
                continue;
            }
            echo "<script src='$pt'></script>";
        }
    }
}

if (! function_exists("import_currency")) {
    function import_currency()
    {
    ?>
        <script src="<?= codepath('src/ctr/currency.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_twal")) {
    function import_twal()
    {
    ?>
        <script src="<?= codepath('src/ctr/twal.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_date")) {
    function import_date()
    {
    ?>
        <script src="<?= codepath('src/ctr/date.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_jquery")) {
    function import_jquery()
    {
    ?>
        <script src="<?= codepath('src/ctr/jquery.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_paths")) {
    function import_paths()
    {
    ?>
        <script src="<?= codepath('src/ctr/paths.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_loading")) {
    function import_loading()
    {
    ?>
        <script src="<?= codepath('src/ctr/loading.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_secure")) {
    function import_secure()
    {
    ?>
        <script src="<?= codepath('src/ctr/secure.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_tyrux")) {
    function import_tyrux()
    {
        $tyrux = codepath . "/src/tyrux/index.js";
        return '<script type="module">import "./' . $tyrux . '";</script>';
    }
}

if(! function_exists("import_tyrax")){
    function import_tyrax(){
        return import_tyrux();
    }
}

if (! function_exists("import_bundle")) {
    function import_bundle()
    {
    ?>
        <link rel="stylesheet" href="<?= codepath('src/ctr/bundle.css') ?>" />
        <script src="<?= codepath('src/ctr/bundle.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_datatable")) {
    function import_datatable()
    {
    ?>
        <link rel="stylesheet" href="<?= codepath('src/ctr/datatable.css') ?>" />
        <script src="<?= codepath('src/ctr/datatable.js') ?>"></script>
    <?php
    }
}
if (! function_exists("import_jspost")) {
    function import_jspost()
    {
    ?>
        <script src="<?= codepath('src/ctr/jspost.js') ?>"></script>
<?php
    }
}



?>