<?php
function clearBrowserStorage($type = "*")
{
    include "_frontend/core/partials/loadenv.php";
    $host = getenv("rootpath");
    $root = realpath(__DIR__ . '/../../');
    $pagename = "basixstoragemanager.php";
    $tempFile = $root . "/_frontend/pages/$pagename";

    $actions = [
        "localstorage"   => "localStorage.clear();",
        "localStorage"   => "localStorage.clear();",
        "session" => "sessionStorage.clear();",
        "cookie"  => "document.cookie.split(';').forEach(c => {
                        document.cookie = c.trim().split('=')[0] + 
                        '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/';
                     });",
        "cache"   => "if ('caches' in window) {
                        caches.keys().then(keys => keys.forEach(k => caches.delete(k)));
                      }"
    ];

    $jsCode = "";

    if ($type === "*") {
        foreach ($actions as $code) {
            $jsCode .= $code . "\n";
        }
    } elseif (isset($actions[$type])) {
        $jsCode = $actions[$type];
    } else {
        echo "❌ Unknown storage type: $type\n";
        return;
    }

    $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
    <title>Clear Storage</title>
    <script>
        window.onload = function() {
            $jsCode
            alert("✅ $type storage cleared! You can close this tab now.");
            window.close();
        };
    </script>
    </head>
    <body>
    <h2>Clearing: $type ...</h2>
    </body>
    </html>
    HTML;

    file_put_contents($tempFile, $html);

    $url = "$host?page=$pagename";

    if (PHP_OS_FAMILY === 'Windows') {
        exec("start \"\" \"$url\"");
    } elseif (PHP_OS_FAMILY === 'Darwin') {
        exec("open \"$url\"");
    } else {
        exec("xdg-open \"$url\"");
    }

    sleep(5);
    unlink($tempFile);

    echo "✅ $type storage clear page launched and file deleted.\n";
}
