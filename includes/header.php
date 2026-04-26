<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | St. Paul Chipata Portal' : 'St. Paul Chipata Portal'; ?></title>
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/other/main_logo.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/other/main_logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Global CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Page Specific CSS -->
    <?php if (isset($extraCSS)): ?>
        <link rel="stylesheet" href="assets/css/public_pages/<?php echo $extraCSS; ?>.css">
    <?php endif; ?>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- PWA Support -->
    <link rel="manifest" href="<?php echo BASE_URL; ?>manifest.json">
    <meta name="theme-color" content="#0f172a">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assets/images/other/main_logo.png">
</head>
<body>
    <?php include_once 'navbar.php'; ?>
    <main>
        <?php global $path; if (isset($path) && $path !== 'home' && $path !== ''): ?>
            <div class="container" style="padding-top: 2rem; padding-bottom: 0;">
                <a href="javascript:history.back()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; padding: 0.5rem 1rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b';" onmouseout="this.style.background='#f8fafc'; this.style.color='#475569';">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        <?php endif; ?>
