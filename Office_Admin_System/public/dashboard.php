<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

$stats = [
    'active_clients' => (int) db()->query("SELECT COUNT(*) FROM clients WHERE status = 'active'")->fetchColumn(),
    'open_tickets' => (int) db()->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('new','in_progress','waiting_client')")->fetchColumn(),
    'active_jobs' => (int) db()->query("SELECT COUNT(*) FROM service_jobs WHERE status IN ('planned','active','blocked')")->fetchColumn(),
    'workflow_risk' => (int) db()->query("SELECT COUNT(*) FROM enterprise_workflows WHERE status = 'at_risk'")->fetchColumn(),
    'pending_leaves' => (int) db()->query("SELECT COUNT(*) FROM hr_leave_requests WHERE status = 'pending'")->fetchColumn(),
    'finance_pending' => (int) db()->query("SELECT COUNT(*) FROM finance_transactions WHERE payment_status = 'pending'")->fetchColumn(),
];

$todayAgenda = db()->query("SELECT title, participant_name, appointment_time, status FROM appointments WHERE appointment_date = CURDATE() ORDER BY appointment_time ASC LIMIT 8")->fetchAll();
$criticalTickets = db()->query("SELECT ticket_number, title, severity, status FROM support_tickets WHERE severity IN ('high','critical') AND status <> 'closed' ORDER BY updated_at DESC LIMIT 8")->fetchAll();

render_header('MSP Command Center', $user);
?>
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-2">
    <div>
        <span class="badge text-bg-info module-pill mb-2"><?= e((string) $user['role']) ?></span>
        <h1 class="h3 mb-1">NabtaTech MSP Command Center</h1>
        <p class="text-muted mb-0">Internal operations + client technical services + enterprise platforms in one system.</p>
    </div>
    <div class="text-lg-end">
        <small class="text-muted">Live timestamp: <span data-now></span></small>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Active Clients</div><div class="kpi"><?= $stats['active_clients'] ?></div></div></div></div>
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Open Tickets</div><div class="kpi"><?= $stats['open_tickets'] ?></div></div></div></div>
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Active Jobs</div><div class="kpi"><?= $stats['active_jobs'] ?></div></div></div></div>
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">At-Risk Workflows</div><div class="kpi"><?= $stats['workflow_risk'] ?></div></div></div></div>
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Pending Leaves</div><div class="kpi"><?= $stats['pending_leaves'] ?></div></div></div></div>
    <div class="col-md-4 col-lg-2"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Pending Payments</div><div class="kpi"><?= $stats['finance_pending'] ?></div></div></div></div>
</div>

<div class="card glass-card tech-card mb-4">
    <div class="card-body">
        <h2 class="h5">Module Quick Access</h2>
        <div class="quick-grid mt-3">
            <a class="btn btn-outline-primary" href="reception.php">Reception</a>
            <a class="btn btn-outline-primary" href="secretary.php">Secretary</a>
            <a class="btn btn-outline-primary" href="hr.php">HR</a>
            <a class="btn btn-outline-primary" href="finance.php">Finance</a>
            <a class="btn btn-outline-primary" href="support.php">IT Support</a>
            <a class="btn btn-outline-primary" href="clients.php">Clients</a>
            <a class="btn btn-outline-primary" href="operations.php">Service Ops</a>
            <a class="btn btn-outline-primary" href="platforms.php">Enterprise</a>
            <a class="btn btn-outline-primary" href="documents.php">Documents</a>
            <a class="btn btn-outline-primary" href="reports.php">Reports</a>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card glass-card tech-card h-100">
            <div class="card-header"><h2 class="h6 mb-0">Today Agenda (Secretary)</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Time</th><th>Title</th><th>Participant</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($todayAgenda as $item): ?>
                        <tr>
                            <td><?= e((string) $item['appointment_time']) ?></td>
                            <td><?= e((string) $item['title']) ?></td>
                            <td><?= e((string) $item['participant_name']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $item['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$todayAgenda): ?>
                        <tr><td colspan="4" class="text-muted">No agenda items today.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card glass-card tech-card h-100">
            <div class="card-header"><h2 class="h6 mb-0">Priority Ticket Watch</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Ticket</th><th>Issue</th><th>Severity</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($criticalTickets as $ticket): ?>
                        <tr>
                            <td><?= e((string) $ticket['ticket_number']) ?></td>
                            <td><?= e((string) $ticket['title']) ?></td>
                            <td><?= e((string) $ticket['severity']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $ticket['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$criticalTickets): ?>
                        <tr><td colspan="4" class="text-muted">No high priority tickets right now.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
