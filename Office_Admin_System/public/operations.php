<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'complete_job' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE service_jobs SET status = 'completed', completion_percent = 100 WHERE job_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('operations.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'job') {
        $stmt = db()->prepare('INSERT INTO service_jobs (job_code, client_id, service_type, scope_summary, engineer_name, start_date, due_date, status, completion_percent) VALUES (:job_code, :client_id, :service_type, :scope_summary, :engineer_name, :start_date, :due_date, :status, :completion_percent)');
        $stmt->execute([
            'job_code' => trim($_POST['job_code'] ?? ''),
            'client_id' => ($_POST['client_id'] ?? '') !== '' ? (int) $_POST['client_id'] : null,
            'service_type' => trim($_POST['service_type'] ?? 'network_setup'),
            'scope_summary' => trim($_POST['scope_summary'] ?? ''),
            'engineer_name' => trim($_POST['engineer_name'] ?? ''),
            'start_date' => ($_POST['start_date'] ?? '') ?: null,
            'due_date' => ($_POST['due_date'] ?? '') ?: null,
            'status' => trim($_POST['status'] ?? 'planned'),
            'completion_percent' => (int) ($_POST['completion_percent'] ?? 0),
        ]);
    }

    if ($formType === 'asset') {
        $stmt = db()->prepare('INSERT INTO device_inventory (asset_tag, client_id, asset_type, vendor, model, serial_number, deployment_status, location, assigned_engineer, last_service_date, next_service_date) VALUES (:asset_tag, :client_id, :asset_type, :vendor, :model, :serial_number, :deployment_status, :location, :assigned_engineer, :last_service_date, :next_service_date)');
        $stmt->execute([
            'asset_tag' => trim($_POST['asset_tag'] ?? ''),
            'client_id' => ($_POST['asset_client_id'] ?? '') !== '' ? (int) $_POST['asset_client_id'] : null,
            'asset_type' => trim($_POST['asset_type'] ?? 'router'),
            'vendor' => trim($_POST['vendor'] ?? ''),
            'model' => trim($_POST['model'] ?? ''),
            'serial_number' => trim($_POST['serial_number'] ?? ''),
            'deployment_status' => trim($_POST['deployment_status'] ?? 'stock'),
            'location' => trim($_POST['location'] ?? ''),
            'assigned_engineer' => trim($_POST['assigned_engineer'] ?? ''),
            'last_service_date' => ($_POST['last_service_date'] ?? '') ?: null,
            'next_service_date' => ($_POST['next_service_date'] ?? '') ?: null,
        ]);
    }

    redirect('operations.php');
}

$clients = db()->query('SELECT client_id, client_name FROM clients ORDER BY client_name')->fetchAll();
$jobs = db()->query('SELECT j.job_id, j.job_code, c.client_name, j.service_type, j.engineer_name, j.status, j.completion_percent, j.due_date FROM service_jobs j LEFT JOIN clients c ON c.client_id = j.client_id ORDER BY j.job_id DESC LIMIT 60')->fetchAll();
$assets = db()->query('SELECT d.asset_tag, c.client_name, d.asset_type, d.vendor, d.model, d.deployment_status, d.next_service_date FROM device_inventory d LEFT JOIN clients c ON c.client_id = d.client_id ORDER BY d.asset_id DESC LIMIT 60')->fetchAll();

render_header('Service Operations', $user);
?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h1 class="h5 mb-0">Create Service Job</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="job">
                    <div class="col-6"><input class="form-control" name="job_code" placeholder="Job code" required></div>
                    <div class="col-6">
                        <select class="form-select" name="client_id">
                            <option value="">No client</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= (int) $c['client_id'] ?>"><?= e((string) $c['client_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12"><select class="form-select" name="service_type"><option>network_setup</option><option>device_installation</option><option>troubleshooting</option><option>cloud_services</option><option>business_systems</option><option>cybersecurity</option><option>documentation</option><option>backup_recovery</option></select></div>
                    <div class="col-12"><input class="form-control" name="engineer_name" placeholder="Engineer"></div>
                    <div class="col-6"><input class="form-control" type="date" name="start_date"></div>
                    <div class="col-6"><input class="form-control" type="date" name="due_date"></div>
                    <div class="col-6"><select class="form-select" name="status"><option>planned</option><option>active</option><option>blocked</option><option>completed</option></select></div>
                    <div class="col-6"><input class="form-control" type="number" min="0" max="100" name="completion_percent" value="0"></div>
                    <div class="col-12"><textarea class="form-control" name="scope_summary" rows="2" placeholder="Scope summary"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Job</button></div>
                </form>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Register Device Asset</h2></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="asset">
                    <div class="col-6"><input class="form-control" name="asset_tag" placeholder="Asset tag" required></div>
                    <div class="col-6">
                        <select class="form-select" name="asset_client_id">
                            <option value="">Internal</option>
                            <?php foreach ($clients as $c): ?>
                                <option value="<?= (int) $c['client_id'] ?>"><?= e((string) $c['client_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6"><select class="form-select" name="asset_type"><option>router</option><option>switch</option><option>firewall</option><option>server</option><option>workstation</option><option>laptop</option><option>printer</option><option>other</option></select></div>
                    <div class="col-6"><select class="form-select" name="deployment_status"><option>stock</option><option>deployed</option><option>maintenance</option><option>retired</option></select></div>
                    <div class="col-4"><input class="form-control" name="vendor" placeholder="Vendor"></div>
                    <div class="col-4"><input class="form-control" name="model" placeholder="Model"></div>
                    <div class="col-4"><input class="form-control" name="serial_number" placeholder="Serial"></div>
                    <div class="col-6"><input class="form-control" name="location" placeholder="Location"></div>
                    <div class="col-6"><input class="form-control" name="assigned_engineer" placeholder="Assigned engineer"></div>
                    <div class="col-6"><input class="form-control" type="date" name="last_service_date"></div>
                    <div class="col-6"><input class="form-control" type="date" name="next_service_date"></div>
                    <div class="col-12 d-grid"><button class="btn btn-outline-primary">Save Asset</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Service Delivery Board</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Job</th><th>Client</th><th>Type</th><th>Engineer</th><th>%</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($jobs as $j): ?>
                        <tr>
                            <td><?= e((string) $j['job_code']) ?></td>
                            <td><?= e((string) ($j['client_name'] ?? 'Internal')) ?></td>
                            <td><?= e((string) $j['service_type']) ?></td>
                            <td><?= e((string) ($j['engineer_name'] ?? '')) ?></td>
                            <td><?= (int) $j['completion_percent'] ?>%</td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $j['status']) ?></span></td>
                            <td>
                                <?php if ((string) $j['status'] !== 'completed'): ?>
                                    <a class="btn btn-sm btn-outline-success" href="operations.php?action=complete_job&id=<?= (int) $j['job_id'] ?>">Complete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Device Deployment & Lifecycle</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Asset</th><th>Client</th><th>Type</th><th>Model</th><th>Status</th><th>Next Service</th></tr></thead>
                    <tbody>
                    <?php foreach ($assets as $a): ?>
                        <tr>
                            <td><?= e((string) $a['asset_tag']) ?></td>
                            <td><?= e((string) ($a['client_name'] ?? 'Internal')) ?></td>
                            <td><?= e((string) $a['asset_type']) ?></td>
                            <td><?= e(trim(((string) $a['vendor']) . ' ' . ((string) $a['model']))) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $a['deployment_status']) ?></span></td>
                            <td><?= e((string) ($a['next_service_date'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
