<?php
// Colors
$green = "\033[1;32m";
$yellow = "\033[1;33m";
$blue = "\033[1;34m";
$red = "\033[1;31m";
$reset = "\033[0m";

$dir = basename(getcwd());

echo "\n";
echo $green . <<<'BANNER'
#   #  ####   ### 
 # #  #   #  #   #
  #   #   #  #   #
  #   ####   #   #
  #   #  #   #   #
  #   #   #   ### 
BANNER
. $reset;

echo $yellow . "⚡ CODETAZER by CodeYRO ⚡" . $reset . "\n\n";

echo $green . "✅ Codetazer installed successfully! 🎉" . $reset . "\n\n";

echo $blue . "👉 Next steps:" . $reset . "\n";
echo "   1. {$yellow}cd {$dir}{$reset}\n";
echo "   2. {$yellow}php ctr run{$reset}\n";
echo "   3. Open {$yellow}http://localhost:9999{$reset} in your browser\n\n";
