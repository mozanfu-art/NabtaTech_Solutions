<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function render_header(string $title, ?array $user = null): void
{
    $appName = env('APP_NAME', 'NabtaTech Solutions');
    ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> | <?= e($appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<header class="site-header shadow-sm sticky-top">
    <nav class="navbar navbar-expand-xl navbar-dark container py-2">
        <a class="navbar-brand d-flex align-items-center gap-2" href="dashboard.php">
            <img src="assets/img/NabtaTech_Logo.png" alt="NabtaTech Solutions" class="brand-logo">
            <span class="fw-semibold brand-text">NabtaTech Solutions</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <?php if ($user): ?>
                <ul class="navbar-nav ms-auto align-items-xl-center gap-xl-1">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Internal Ops</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="reception.php">Reception</a></li>
                            <li><a class="dropdown-item" href="secretary.php">Secretary</a></li>
                            <li><a class="dropdown-item" href="hr.php">HR</a></li>
                            <li><a class="dropdown-item" href="finance.php">Finance</a></li>
                            <li><a class="dropdown-item" href="support.php">IT Support Desk</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Client Services</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="clients.php">Client Directory</a></li>
                            <li><a class="dropdown-item" href="operations.php">Service Operations</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Enterprise</a>
                        <ul class="dropdown-menu dropdown-menu-dark">
                            <li><a class="dropdown-item" href="platforms.php">ERP/CRM/SAP Workflows</a></li>
                            <li><a class="dropdown-item" href="documents.php">Documentation</a></li>
                            <li><a class="dropdown-item" href="reports.php">Management Reports</a></li>
                        </ul>
                    </li>
                    <li class="nav-item ms-xl-2"><a class="btn btn-sm btn-outline-light" href="logout.php">Logout</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main class="container py-4">
<?php
}

function render_footer(): void
{
    ?>
</main>
<footer class="site-footer mt-4">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-md-center py-3 gap-2">
        <small>&copy; <?= date('Y') ?> NabtaTech Solutions | IT Solutions & Managed Services Provider</small>
        <small>Reception | Operations | Support | Cloud | ERP/CRM | DevOps</small>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
<?php
}
