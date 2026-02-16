<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

if (auth_user()) {
    redirect('dashboard.php');
}

redirect('login.php');
