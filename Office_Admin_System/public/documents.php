<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO documents (document_number, document_title, document_type, category, department, version, status, author, reviewer, description, tags) VALUES (:document_number, :document_title, :document_type, :category, :department, :version, :status, :author, :reviewer, :description, :tags)');
    $stmt->execute([
        'document_number' => trim($_POST['document_number'] ?? ''),
        'document_title' => trim($_POST['document_title'] ?? ''),
        'document_type' => trim($_POST['document_type'] ?? ''),
        'category' => trim($_POST['category'] ?? ''),
        'department' => trim($_POST['department'] ?? ''),
        'version' => trim($_POST['version'] ?? '1.0'),
        'status' => trim($_POST['status'] ?? 'draft'),
        'author' => trim($_POST['author'] ?? ''),
        'reviewer' => trim($_POST['reviewer'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'tags' => trim($_POST['tags'] ?? ''),
    ]);
    redirect('documents.php');
}

$documents = db()->query('SELECT document_number, document_title, document_type, category, department, version, status, author, updated_at FROM documents ORDER BY updated_at DESC LIMIT 70')->fetchAll();

render_header('Documentation Center', $user);
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card glass-card tech-card">
            <div class="card-header"><h1 class="h5 mb-0">Add/Update Document Record</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <div class="col-12"><input class="form-control" name="document_number" placeholder="Document number" required></div>
                    <div class="col-12"><input class="form-control" name="document_title" placeholder="Document title" required></div>
                    <div class="col-6"><input class="form-control" name="document_type" placeholder="Type"></div>
                    <div class="col-6"><input class="form-control" name="category" placeholder="Category"></div>
                    <div class="col-6"><input class="form-control" name="department" placeholder="Department"></div>
                    <div class="col-6"><input class="form-control" name="version" value="1.0"></div>
                    <div class="col-6"><input class="form-control" name="author" placeholder="Author"></div>
                    <div class="col-6"><input class="form-control" name="reviewer" placeholder="Reviewer"></div>
                    <div class="col-12"><select class="form-select" name="status"><option>draft</option><option>pending_review</option><option>approved</option><option>archived</option></select></div>
                    <div class="col-12"><input class="form-control" name="tags" placeholder="Tags"></div>
                    <div class="col-12"><textarea class="form-control" name="description" rows="2" placeholder="Description"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Document</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Knowledge Base Index</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Number</th><th>Title</th><th>Type</th><th>Department</th><th>Version</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($documents as $d): ?>
                        <tr>
                            <td><?= e((string) $d['document_number']) ?></td>
                            <td><?= e((string) $d['document_title']) ?></td>
                            <td><?= e((string) ($d['document_type'] ?? '')) ?></td>
                            <td><?= e((string) ($d['department'] ?? '')) ?></td>
                            <td><?= e((string) ($d['version'] ?? '')) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $d['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
