<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

$kpis = [
    'clients' => (int) db()->query("SELECT COUNT(*) FROM clients WHERE status = 'active'")->fetchColumn(),
    'tickets_open' => (int) db()->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('new','in_progress','waiting_client')")->fetchColumn(),
    'tickets_resolved' => (int) db()->query("SELECT COUNT(*) FROM support_tickets WHERE status IN ('resolved','closed')")->fetchColumn(),
    'jobs_active' => (int) db()->query("SELECT COUNT(*) FROM service_jobs WHERE status IN ('planned','active','blocked')")->fetchColumn(),
    'jobs_completed' => (int) db()->query("SELECT COUNT(*) FROM service_jobs WHERE status = 'completed'")->fetchColumn(),
    'workflows_risk' => (int) db()->query("SELECT COUNT(*) FROM enterprise_workflows WHERE status = 'at_risk'")->fetchColumn(),
    'finance_pending' => (int) db()->query("SELECT COUNT(*) FROM finance_transactions WHERE payment_status IN ('pending','overdue')")->fetchColumn(),
];

$sla = $kpis['tickets_open'] + $kpis['tickets_resolved'] > 0
    ? round(($kpis['tickets_resolved'] / ($kpis['tickets_open'] + $kpis['tickets_resolved'])) * 100, 1)
    : 0;

$netFinancial = db()->query("SELECT (SUM(CASE WHEN direction='inflow' THEN amount ELSE 0 END) - SUM(CASE WHEN direction='outflow' THEN amount ELSE 0 END)) AS net FROM finance_transactions")->fetchColumn();

$serviceMix = db()->query('SELECT service_type, COUNT(*) AS total FROM service_jobs GROUP BY service_type ORDER BY total DESC LIMIT 8')->fetchAll();
$workflowBoard = db()->query('SELECT workflow_code, workflow_name, platform, stage, status, target_go_live FROM enterprise_workflows ORDER BY target_go_live ASC LIMIT 10')->fetchAll();

render_header('Management Reporting', $user);
?>
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Active Clients</div><div class="h3 mb-0"><?= $kpis['clients'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Ticket Resolution Ratio</div><div class="h3 mb-0"><?= $sla ?>%</div></div></div></div>
    <div class="col-md-3"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Jobs Active / Completed</div><div class="h3 mb-0"><?= $kpis['jobs_active'] ?>/<?= $kpis['jobs_completed'] ?></div></div></div></div>
    <div class="col-md-3"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Net Financial Position</div><div class="h3 mb-0"><?= number_format((float) $netFinancial, 2) ?></div></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card glass-card tech-card h-100">
            <div class="card-header"><h1 class="h6 mb-0">Service Mix (Technical Operations)</h1></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Service Type</th><th>Total Jobs</th></tr></thead>
                    <tbody>
                    <?php foreach ($serviceMix as $item): ?>
                        <tr><td><?= e((string) $item['service_type']) ?></td><td><?= (int) $item['total'] ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card glass-card tech-card h-100">
            <div class="card-header"><h2 class="h6 mb-0">Enterprise Workflow Risk Board</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Code</th><th>Workflow</th><th>Platform</th><th>Stage</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($workflowBoard as $row): ?>
                        <tr>
                            <td><?= e((string) $row['workflow_code']) ?></td>
                            <td><?= e((string) $row['workflow_name']) ?></td>
                            <td><?= e((string) $row['platform']) ?></td>
                            <td><?= e((string) $row['stage']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $row['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card glass-card tech-card mt-3">
    <div class="card-body">
        <h2 class="h6">Executive Notes</h2>
        <ul class="mb-0">
            <li>Open tickets: <?= $kpis['tickets_open'] ?> | Pending finance transactions: <?= $kpis['finance_pending'] ?>.</li>
            <li>At-risk enterprise workflows: <?= $kpis['workflows_risk'] ?>.</li>
            <li>Use this page as weekly management report before stakeholder meetings.</li>
        </ul>
    </div>
</div>
<?php render_footer(); ?>
