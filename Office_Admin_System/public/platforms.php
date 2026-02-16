<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'workflow') {
        $stmt = db()->prepare('INSERT INTO enterprise_workflows (workflow_code, platform, workflow_name, owner_name, stage, status, target_go_live, notes) VALUES (:workflow_code, :platform, :workflow_name, :owner_name, :stage, :status, :target_go_live, :notes)');
        $stmt->execute([
            'workflow_code' => trim($_POST['workflow_code'] ?? ''),
            'platform' => trim($_POST['platform'] ?? 'erp'),
            'workflow_name' => trim($_POST['workflow_name'] ?? ''),
            'owner_name' => trim($_POST['owner_name'] ?? ''),
            'stage' => trim($_POST['stage'] ?? 'design'),
            'status' => trim($_POST['status'] ?? 'on_track'),
            'target_go_live' => ($_POST['target_go_live'] ?? '') ?: null,
            'notes' => trim($_POST['notes'] ?? ''),
        ]);
    }

    if ($formType === 'pipeline') {
        $stmt = db()->prepare('INSERT INTO devops_pipelines (pipeline_name, repository, environment, last_run_at, last_result, deploy_frequency, owner_name) VALUES (:pipeline_name, :repository, :environment, :last_run_at, :last_result, :deploy_frequency, :owner_name)');
        $stmt->execute([
            'pipeline_name' => trim($_POST['pipeline_name'] ?? ''),
            'repository' => trim($_POST['repository'] ?? ''),
            'environment' => trim($_POST['environment'] ?? 'dev'),
            'last_run_at' => ($_POST['last_run_at'] ?? '') ?: null,
            'last_result' => trim($_POST['last_result'] ?? 'queued'),
            'deploy_frequency' => trim($_POST['deploy_frequency'] ?? 'on_demand'),
            'owner_name' => trim($_POST['pipeline_owner_name'] ?? ''),
        ]);
    }

    redirect('platforms.php');
}

$workflows = db()->query('SELECT workflow_code, platform, workflow_name, owner_name, stage, status, target_go_live FROM enterprise_workflows ORDER BY workflow_id DESC LIMIT 60')->fetchAll();
$pipelines = db()->query('SELECT pipeline_name, repository, environment, last_result, deploy_frequency, owner_name FROM devops_pipelines ORDER BY pipeline_id DESC LIMIT 60')->fetchAll();

render_header('Enterprise Platforms', $user);
?>
<div class="row g-3">
    <div class="col-lg-5">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h1 class="h5 mb-0">ERP/CRM/SAP Workflow</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="workflow">
                    <div class="col-6"><input class="form-control" name="workflow_code" placeholder="Workflow code" required></div>
                    <div class="col-6"><select class="form-select" name="platform"><option>erp</option><option>crm</option><option>sap_style</option><option>cloud</option></select></div>
                    <div class="col-12"><input class="form-control" name="workflow_name" placeholder="Workflow name" required></div>
                    <div class="col-6"><input class="form-control" name="owner_name" placeholder="Owner"></div>
                    <div class="col-6"><input class="form-control" type="date" name="target_go_live"></div>
                    <div class="col-6"><select class="form-select" name="stage"><option>design</option><option>build</option><option>test</option><option>deploy</option><option>operate</option></select></div>
                    <div class="col-6"><select class="form-select" name="status"><option>on_track</option><option>at_risk</option><option>blocked</option><option>done</option></select></div>
                    <div class="col-12"><textarea class="form-control" name="notes" rows="2" placeholder="Workflow notes"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Workflow</button></div>
                </form>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">DevOps Pipeline</h2></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="pipeline">
                    <div class="col-12"><input class="form-control" name="pipeline_name" placeholder="Pipeline name" required></div>
                    <div class="col-12"><input class="form-control" name="repository" placeholder="Repository"></div>
                    <div class="col-4"><select class="form-select" name="environment"><option>dev</option><option>staging</option><option>production</option></select></div>
                    <div class="col-4"><select class="form-select" name="last_result"><option>success</option><option>failed</option><option>running</option><option>queued</option></select></div>
                    <div class="col-4"><select class="form-select" name="deploy_frequency"><option>daily</option><option>weekly</option><option>on_demand</option></select></div>
                    <div class="col-6"><input class="form-control" type="datetime-local" name="last_run_at"></div>
                    <div class="col-6"><input class="form-control" name="pipeline_owner_name" placeholder="Owner"></div>
                    <div class="col-12 d-grid"><button class="btn btn-outline-primary">Save Pipeline</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Enterprise Workflow Tracker</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Code</th><th>Platform</th><th>Name</th><th>Stage</th><th>Status</th><th>Go Live</th></tr></thead>
                    <tbody>
                    <?php foreach ($workflows as $w): ?>
                        <tr>
                            <td><?= e((string) $w['workflow_code']) ?></td>
                            <td><?= e((string) $w['platform']) ?></td>
                            <td><?= e((string) $w['workflow_name']) ?></td>
                            <td><?= e((string) $w['stage']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $w['status']) ?></span></td>
                            <td><?= e((string) ($w['target_go_live'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">DevOps Pipeline Monitor</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Pipeline</th><th>Repo</th><th>Env</th><th>Result</th><th>Frequency</th><th>Owner</th></tr></thead>
                    <tbody>
                    <?php foreach ($pipelines as $p): ?>
                        <tr>
                            <td><?= e((string) $p['pipeline_name']) ?></td>
                            <td><?= e((string) ($p['repository'] ?? '')) ?></td>
                            <td><?= e((string) $p['environment']) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $p['last_result']) ?></span></td>
                            <td><?= e((string) $p['deploy_frequency']) ?></td>
                            <td><?= e((string) ($p['owner_name'] ?? '')) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
