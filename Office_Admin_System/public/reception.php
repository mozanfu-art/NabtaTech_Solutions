<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'checkout' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE visitors SET status = 'checked_out', check_out_time = NOW() WHERE visitor_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('reception.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare(
        'INSERT INTO visitors (full_name, company_name, contact_phone, contact_email, purpose_of_visit, person_to_meet, department, badge_number, notes, created_by)
         VALUES (:full_name, :company_name, :contact_phone, :contact_email, :purpose_of_visit, :person_to_meet, :department, :badge_number, :notes, :created_by)'
    );
    $stmt->execute([
        'full_name' => trim($_POST['full_name'] ?? ''),
        'company_name' => trim($_POST['company_name'] ?? ''),
        'contact_phone' => trim($_POST['contact_phone'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'purpose_of_visit' => trim($_POST['purpose_of_visit'] ?? ''),
        'person_to_meet' => trim($_POST['person_to_meet'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'badge_number' => trim($_POST['badge_number'] ?? ''),
        'notes' => trim($_POST['notes'] ?? ''),
        'created_by' => (string) $user['username'],
    ]);
    redirect('reception.php');
}

$visitors = db()->query('SELECT visitor_id, full_name, company_name, person_to_meet, department, badge_number, status, check_in_time, check_out_time FROM visitors ORDER BY check_in_time DESC LIMIT 80')->fetchAll();

render_header('Reception', $user);
?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card glass-card tech-card">
            <div class="card-header"><h1 class="h5 mb-0">Visitor Registration</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <div class="col-12"><input class="form-control" name="full_name" placeholder="Full name" required></div>
                    <div class="col-6"><input class="form-control" name="company_name" placeholder="Company"></div>
                    <div class="col-6"><input class="form-control" name="badge_number" placeholder="Badge number" required></div>
                    <div class="col-6"><input class="form-control" name="contact_phone" placeholder="Phone"></div>
                    <div class="col-6"><input class="form-control" name="contact_email" placeholder="Email"></div>
                    <div class="col-6"><input class="form-control" name="person_to_meet" placeholder="Person to meet"></div>
                    <div class="col-6"><input class="form-control" name="department" placeholder="Department"></div>
                    <div class="col-12"><textarea class="form-control" name="purpose_of_visit" placeholder="Purpose" rows="2"></textarea></div>
                    <div class="col-12"><textarea class="form-control" name="notes" placeholder="Notes" rows="2"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Check-In Visitor</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card glass-card tech-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Live Visitor Log</h2>
                <a href="secretary.php" class="btn btn-sm btn-outline-secondary">Open Secretary Queue</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>Name</th><th>Company</th><th>Host</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($visitors as $v): ?>
                        <tr>
                            <td><?= e((string) $v['full_name']) ?></td>
                            <td><?= e((string) ($v['company_name'] ?? '')) ?></td>
                            <td><?= e((string) ($v['person_to_meet'] ?? '')) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $v['status']) ?></span></td>
                            <td>
                                <?php if ((string) $v['status'] === 'checked_in'): ?>
                                    <a class="btn btn-sm btn-outline-success" href="reception.php?action=checkout&id=<?= (int) $v['visitor_id'] ?>">Check-Out</a>
                                <?php else: ?>
                                    <span class="text-muted small">Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
