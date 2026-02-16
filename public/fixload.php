<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';

use Composer\Autoload\ClassLoader;

/** rebuild composer autoload **/
if (file_exists(__DIR__.'/../vendor/composer/autoload_classmap.php')) {
    echo "Rebuilding autoload...\n";
    system('composer dump-autoload -o');
    echo "✅ Done";
} else {
    echo "⚠️ composer not found — check vendor folder";
}
