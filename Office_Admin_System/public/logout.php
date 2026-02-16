<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

logout();
flash('error', 'You have logged out successfully.');
redirect('login.php');
