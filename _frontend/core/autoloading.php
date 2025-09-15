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


if (! function_exists("include_error_page")) {
    function include_error_page(string|null $error_page, $variables = [], $exit = true)
    {
        $error_page = substr($error_page, -4) == ".php" ? $error_page : $error_page . ".php";
        $path = "_frontend/extra/errors/$error_page";
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

if (! function_exists("include_template_page")) {
    function include_template_page(string|null $template_page, $variables = [], $exit = true)
    {
        $error_page = $template_page;
        $error_page = substr($error_page, -4) == ".php" ? $error_page : $error_page . ".php";
        $path = "_frontend/extra/template/$error_page";
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
        <script src="<?= assets('code/swal.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_ctr")) {
    function import_ctr()
    {
    ?>
        <script src="<?= assets('code/ctr.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_currency")) {
    function import_currency()
    {
    ?>
        <script src="<?= assets('code/currency.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_twal")) {
    function import_twal()
    {
    ?>
        <script src="<?= assets('code/twal.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_date")) {
    function import_date()
    {
    ?>
        <script src="<?= assets('code/date.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_jquery")) {
    function import_jquery()
    {
    ?>
        <script src="<?= assets('code/jquery.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_paths")) {
    function import_paths()
    {
    ?>
        <script src="<?= assets('code/paths.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_loading")) {
    function import_loading()
    {
    ?>
        <script src="<?= assets('code/loading.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_secure")) {
    function import_secure()
    {
    ?>
        <script src="<?= assets('code/secure.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_tyrux")) {
    function import_tyrux()
    {
        $tyrux = assets("tyrux/index.js");
        return '<script type="module">import "./' . $tyrux . '";</script>';
    }
}

if (! function_exists("import_bundle")) {
    function import_bundle()
    {
    ?>
        <link rel="stylesheet" href="<?= assets('code/vendor.bundle.base.css') ?>" />
        <script src="<?= assets('code/vendor.bundle.base.js') ?>"></script>
    <?php
    }
}

if (! function_exists("import_datatable")) {
    function import_datatable()
    {
    ?>
        <link rel="stylesheet" href="<?= assets('code/datatable.css') ?>" />
        <script src="<?= assets('code/datatable.js') ?>"></script>
    <?php
    }
}
if (! function_exists("import_jspost")) {
    function import_jspost()
    {
    ?>
        <script src="<?= assets('code/jspost.js') ?>"></script>
<?php
    }
}



?>