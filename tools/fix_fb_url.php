<?php
require __DIR__ . '/../config/db.php';
$pdo->exec("UPDATE site_settings SET setting_value='https://www.facebook.com/profile.php?id=100067832423652' WHERE setting_key='fb_page_url'");
echo $pdo->query("SELECT setting_value FROM site_settings WHERE setting_key='fb_page_url'")->fetchColumn();
echo "\n";
