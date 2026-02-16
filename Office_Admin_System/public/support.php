<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'resolve' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE support_tickets SET status = 'resolved' WHERE ticket_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('support.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO support_tickets (ticket_number, client_id, title, issue_type, severity, status, assigned_to, opened_by, resolution_notes) VALUES (:ticket_number, :client_id, :title, :issue_type, :severity, :status, :assigned_to, :opened_by, :resolution_notes)');
    $stmt->execute([
        'ticket_number' => trim($_POST['ticket_number'] ?? ''),
        'client_id' => ($_POST['client_id'] ?? '') !== '' ? (int) $_POST['client_id'] : null,
        'title' => trim($_POST['title'] ?? ''),
        'issue_type' => trim($_POST['issue_type'] ?? 'network'),
        'severity' => trim($_POST['severity'] ?? 'medium'),
        'status' => trim($_POST['status'] ?? 'new'),
        'assigned_to' => trim($_POST['assigned_to'] ?? ''),
        'opened_by' => trim($_POST['opened_by'] ?? ''),
        'resolution_notes' => trim($_POST['resolution_notes'] ?? ''),
    ]);
    redirect('support.php');
}

$clients = db()->query('SELECT client_id, client_name FROM clients ORDER BY client_name')->fetchAll();
$tickets = db()->query('SELECT t.ticket_id, t.ticket_number, c.client_name, t.title, t.issue_type, t.severity, t.status, t.assigned_to FROM support_tickets t LEFT JOIN clients c ON c.client_id = t.client_id ORDER BY t.updated_at DESC LIMIT 70')->fetchAll();

render_header('IT Support Desk', $user);
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card glass-card tech-card">
            <div class="card-header"><h1 class="h5 mb-0">New Support Ticket</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <div class="col-12"><input class="form-control" name="ticket_number" placeholder="Ticket number" required></div>
                    <div class="col-12"><input class="form-control" name="title" placeholder="Issue title" required></div>
                    <div class="col-12">
                        <select class="form-select" name="client_id">
                            <option value="">Internal ticket (no client)</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= (int) $c['client_id'] ?>"><?= e((string) $c['client_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6"><select class="form-select" name="issue_type"><option>network</option><option>device</option><option>cloud</option><option>erp_crm</option><option>security</option><option>backup</option><option>user_admin</option><option>other</option></select></div>
                    <div class="col-6"><select class="form-select" name="severity"><option>low</option><option selected>medium</option><option>high</option><option>critical</option></select></div>
                    <div class="col-6"><select class="form-select" name="status"><option>new</option><option>in_progress</option><option>waiting_client</option><option>resolved</option><option>closed</option></select></div>
                    <div class="col-6"><input class="form-control" name="assigned_to" placeholder="Assigned engineer"></div>
                    <div class="col-12"><input class="form-control" name="opened_by" placeholder="Opened by"></div>
                    <div class="col-12"><textarea class="form-control" name="resolution_notes" rows="2" placeholder="Initial notes"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Create Ticket</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Ticket Board</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Ticket</th><th>Client</th><th>Issue</th><th>Type</th><th>Severity</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><?= e((string) $t['ticket_number']) ?></td>
                            <td><?= e((string) ($t['client_name'] ?? 'Internal')) ?></td>
                            <td><?= e((string) $t['title']) ?></td>
                            <td><?= e((string) $t['issue_type']) ?></td>
                            <td><?= e((string) $t['severity']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $t['status']) ?></span></td>
                            <td>
                                <?php if (!in_array((string) $t['status'], ['resolved', 'closed'], true)): ?>
                                    <a class="btn btn-sm btn-outline-success" href="support.php?action=resolve&id=<?= (int) $t['ticket_id'] ?>">Resolve</a>
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
