<?php
if (extension_loaded('gd')) {
    echo "GD extension is LOADED.";
} else {
    echo "GD extension is NOT loaded.";
    
    // Try to find php.ini path
    echo "\nphp.ini path: " . php_ini_loaded_file();
}
?>
