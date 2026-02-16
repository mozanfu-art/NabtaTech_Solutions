<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO clients (client_name, industry, contact_person, contact_email, contact_phone, service_tier, contract_start, contract_end, status, notes) VALUES (:client_name, :industry, :contact_person, :contact_email, :contact_phone, :service_tier, :contract_start, :contract_end, :status, :notes)');
    $stmt->execute([
        'client_name' => trim($_POST['client_name'] ?? ''),
        'industry' => trim($_POST['industry'] ?? ''),
        'contact_person' => trim($_POST['contact_person'] ?? ''),
        'contact_email' => trim($_POST['contact_email'] ?? ''),
        'contact_phone' => trim($_POST['contact_phone'] ?? ''),
        'service_tier' => trim($_POST['service_tier'] ?? 'standard'),
        'contract_start' => ($_POST['contract_start'] ?? '') ?: null,
        'contract_end' => ($_POST['contract_end'] ?? '') ?: null,
        'status' => trim($_POST['status'] ?? 'active'),
        'notes' => trim($_POST['notes'] ?? ''),
    ]);
    redirect('clients.php');
}

$clients = db()->query('SELECT client_id, client_name, industry, contact_person, service_tier, status, contract_end FROM clients ORDER BY client_id DESC LIMIT 80')->fetchAll();

render_header('Client Directory', $user);
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card glass-card tech-card">
            <div class="card-header"><h1 class="h5 mb-0">Add Client</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <div class="col-12"><input class="form-control" name="client_name" placeholder="Client name" required></div>
                    <div class="col-6"><input class="form-control" name="industry" placeholder="Industry"></div>
                    <div class="col-6"><input class="form-control" name="contact_person" placeholder="Contact person"></div>
                    <div class="col-6"><input class="form-control" name="contact_email" placeholder="Email"></div>
                    <div class="col-6"><input class="form-control" name="contact_phone" placeholder="Phone"></div>
                    <div class="col-6"><select class="form-select" name="service_tier"><option>basic</option><option selected>standard</option><option>premium</option></select></div>
                    <div class="col-6"><select class="form-select" name="status"><option>active</option><option>inactive</option><option>prospect</option></select></div>
                    <div class="col-6"><input class="form-control" type="date" name="contract_start"></div>
                    <div class="col-6"><input class="form-control" type="date" name="contract_end"></div>
                    <div class="col-12"><textarea class="form-control" name="notes" rows="2" placeholder="Notes"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Client</button></div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card glass-card tech-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="h6 mb-0">Client Portfolio</h2>
                <a class="btn btn-sm btn-outline-secondary" href="operations.php">Go to Service Operations</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Client</th><th>Industry</th><th>Contact</th><th>Tier</th><th>Status</th><th>Contract End</th></tr></thead>
                    <tbody>
                    <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= e((string) $c['client_name']) ?></td>
                            <td><?= e((string) $c['industry']) ?></td>
                            <td><?= e((string) $c['contact_person']) ?></td>
                            <td><?= e((string) $c['service_tier']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $c['status']) ?></span></td>
                            <td><?= e((string) ($c['contract_end'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
