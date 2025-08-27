<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/functions.php';

if (!isset($_SESSION['user_id'])) {
    redirect(SITE_URL . '/public/index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Dashboard'; ?> - <?php echo SITE_NAME; ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-palette"></i> Themes
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item theme-switcher" href="#" data-theme="theme-corporate-blue">Corporate Blue</a>
                    <a class="dropdown-item theme-switcher" href="#" data-theme="theme-academic-green">Academic Green</a>
                    <a class="dropdown-item theme-switcher" href="#" data-theme="theme-modern-dark">Modern Dark</a>
                    <a class="dropdown-item theme-switcher" href="#" data-theme="theme-vibrant-orange">Vibrant Orange</a>
                    <a class="dropdown-item theme-switcher" href="#" data-theme="theme-clean-purple">Clean Purple</a>
                </div>
            </li>
        </ul>
    </div>
</nav>