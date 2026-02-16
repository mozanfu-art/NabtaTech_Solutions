<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'approve_leave' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE hr_leave_requests SET status = 'approved', approved_by = :approved_by WHERE leave_id = :id");
    $stmt->execute(['approved_by' => (string) $user['full_name'], 'id' => (int) $_GET['id']]);
    redirect('hr.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'employee') {
        $stmt = db()->prepare('INSERT INTO hr_employees (employee_code, full_name, email, phone, department, job_title, hire_date, employment_status, manager_name) VALUES (:employee_code, :full_name, :email, :phone, :department, :job_title, :hire_date, :employment_status, :manager_name)');
        $stmt->execute([
            'employee_code' => trim($_POST['employee_code'] ?? ''),
            'full_name' => trim($_POST['full_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'department' => trim($_POST['department'] ?? ''),
            'job_title' => trim($_POST['job_title'] ?? ''),
            'hire_date' => trim($_POST['hire_date'] ?? ''),
            'employment_status' => trim($_POST['employment_status'] ?? 'active'),
            'manager_name' => trim($_POST['manager_name'] ?? ''),
        ]);
    }

    if ($formType === 'leave') {
        $stmt = db()->prepare('INSERT INTO hr_leave_requests (employee_code, leave_type, start_date, end_date, reason) VALUES (:employee_code, :leave_type, :start_date, :end_date, :reason)');
        $stmt->execute([
            'employee_code' => trim($_POST['employee_code_leave'] ?? ''),
            'leave_type' => trim($_POST['leave_type'] ?? 'annual'),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'reason' => trim($_POST['reason'] ?? ''),
        ]);
    }

    redirect('hr.php');
}

$employees = db()->query('SELECT employee_id, employee_code, full_name, department, job_title, employment_status FROM hr_employees ORDER BY employee_id DESC LIMIT 40')->fetchAll();
$leaves = db()->query('SELECT leave_id, employee_code, leave_type, start_date, end_date, status, approved_by FROM hr_leave_requests ORDER BY leave_id DESC LIMIT 40')->fetchAll();

render_header('Human Resources', $user);
?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h1 class="h5 mb-0">Add Employee</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="employee">
                    <div class="col-6"><input class="form-control" name="employee_code" placeholder="Employee code" required></div>
                    <div class="col-6"><input class="form-control" name="full_name" placeholder="Full name" required></div>
                    <div class="col-6"><input class="form-control" name="email" placeholder="Email"></div>
                    <div class="col-6"><input class="form-control" name="phone" placeholder="Phone"></div>
                    <div class="col-6"><input class="form-control" name="department" placeholder="Department"></div>
                    <div class="col-6"><input class="form-control" name="job_title" placeholder="Job title"></div>
                    <div class="col-6"><input class="form-control" type="date" name="hire_date"></div>
                    <div class="col-6"><select class="form-select" name="employment_status"><option>active</option><option>on_leave</option><option>terminated</option></select></div>
                    <div class="col-12"><input class="form-control" name="manager_name" placeholder="Manager"></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Employee</button></div>
                </form>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Submit Leave Request</h2></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="leave">
                    <div class="col-6"><input class="form-control" name="employee_code_leave" placeholder="Employee code" required></div>
                    <div class="col-6"><select class="form-select" name="leave_type"><option>annual</option><option>sick</option><option>unpaid</option><option>emergency</option></select></div>
                    <div class="col-6"><input class="form-control" type="date" name="start_date" required></div>
                    <div class="col-6"><input class="form-control" type="date" name="end_date" required></div>
                    <div class="col-12"><textarea class="form-control" name="reason" rows="2" placeholder="Reason"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-outline-primary">Submit Request</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Employee Roster</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Code</th><th>Name</th><th>Department</th><th>Role</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($employees as $e): ?>
                        <tr>
                            <td><?= e((string) $e['employee_code']) ?></td>
                            <td><?= e((string) $e['full_name']) ?></td>
                            <td><?= e((string) $e['department']) ?></td>
                            <td><?= e((string) $e['job_title']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $e['employment_status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Leave Queue</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Employee</th><th>Type</th><th>Range</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($leaves as $l): ?>
                        <tr>
                            <td><?= e((string) $l['employee_code']) ?></td>
                            <td><?= e((string) $l['leave_type']) ?></td>
                            <td><?= e((string) $l['start_date']) ?> to <?= e((string) $l['end_date']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $l['status']) ?></span></td>
                            <td>
                                <?php if ((string) $l['status'] === 'pending'): ?>
                                    <a class="btn btn-sm btn-outline-success" href="hr.php?action=approve_leave&id=<?= (int) $l['leave_id'] ?>">Approve</a>
                                <?php else: ?>
                                    <small class="text-muted"><?= e((string) ($l['approved_by'] ?? '')) ?></small>
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
