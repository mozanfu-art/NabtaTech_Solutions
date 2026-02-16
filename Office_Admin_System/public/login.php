<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

if (auth_user()) {
    redirect('dashboard.php');
}

$error = flash('error');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (login($username, $password)) {
        redirect('dashboard.php');
    }

    $error = 'Invalid credentials or inactive account.';
}

render_header('Login');
?>
<div class="login-panel card glass-card tech-card">
    <div class="card-body p-4">
        <div class="text-center mb-3">
            <img src="assets/img/logo.svg" alt="NabtaTech" class="brand-logo mb-2">
            <h1 class="h4 mb-1">NabtaTech Solutions</h1>
            <p class="text-muted mb-0">IT Solutions & Managed Services Platform</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post" class="d-grid gap-3">
            <div>
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div>
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="bi bi-box-arrow-in-right"></i> Login</button>
        </form>
        <hr>
        <small class="text-muted">Sample users: <code>admin</code>, <code>reception</code>, <code>support1</code></small>
    </div>
</div>
<?php render_footer(); ?>
