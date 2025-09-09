<?php
// Colors
$green = "\033[1;32m";
$yellow = "\033[1;33m";
$blue = "\033[1;34m";
$red = "\033[1;31m";
$reset = "\033[0m";

$dir = basename(getcwd());

echo "\n";
echo $green . "   ____   ____   ____  _____ _______        _    _  ______  ______  _____   _____  \n";
echo "  / ___| |  _ \\ / ___|| ____|__  /_ \\      / \\  | |/ / ___||  _  \\| ____| |__  /  \n";
echo " | |     | | | | |    |  _|   / / | | |    / _ \\ | ' /|  _| | | | |  _|     / /   \n";
echo " | |___  | |_| | |___ | |___ / /_ | |_| | / ___ \\| . \\| |___| |_| | |___   / /_   \n";
echo "  \\____| |____/ \\____||_____/____| \\___/ /_/   \\_\\_|\\_\\\\____||____/|_____| /____|  \n";
echo "                                                                                   \n" . $reset;

echo $yellow . "⚡ CODETAZER by CodeYRO ⚡" . $reset . "\n\n";

echo $green . "✅ Codetazer installed successfully! 🎉" . $reset . "\n\n";

echo $blue . "👉 Next steps:" . $reset . "\n";
echo "   1. {$yellow}cd {$dir}{$reset}\n";
echo "   2. {$yellow}php ctr run{$reset}\n";
echo "   3. Open {$yellow}http://localhost:9999{$reset} in your browser\n\n";
