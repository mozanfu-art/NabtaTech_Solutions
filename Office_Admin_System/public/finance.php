<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'mark_paid' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE finance_transactions SET payment_status = 'paid' WHERE transaction_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('finance.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = db()->prepare('INSERT INTO finance_transactions (transaction_code, transaction_date, category, description, amount, direction, payment_status, client_name) VALUES (:transaction_code, :transaction_date, :category, :description, :amount, :direction, :payment_status, :client_name)');
    $stmt->execute([
        'transaction_code' => trim($_POST['transaction_code'] ?? ''),
        'transaction_date' => trim($_POST['transaction_date'] ?? ''),
        'category' => trim($_POST['category'] ?? 'invoice'),
        'description' => trim($_POST['description'] ?? ''),
        'amount' => (float) ($_POST['amount'] ?? 0),
        'direction' => trim($_POST['direction'] ?? 'inflow'),
        'payment_status' => trim($_POST['payment_status'] ?? 'pending'),
        'client_name' => trim($_POST['client_name'] ?? ''),
    ]);
    redirect('finance.php');
}

$transactions = db()->query('SELECT transaction_id, transaction_code, transaction_date, category, amount, direction, payment_status, client_name FROM finance_transactions ORDER BY transaction_date DESC LIMIT 60')->fetchAll();
$totals = db()->query("SELECT SUM(CASE WHEN direction='inflow' THEN amount ELSE 0 END) AS inflow, SUM(CASE WHEN direction='outflow' THEN amount ELSE 0 END) AS outflow FROM finance_transactions")->fetch();

render_header('Finance', $user);
?>
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card glass-card tech-card">
            <div class="card-header"><h1 class="h5 mb-0">Record Transaction</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <div class="col-12"><input class="form-control" name="transaction_code" placeholder="Code" required></div>
                    <div class="col-6"><input class="form-control" type="date" name="transaction_date" required></div>
                    <div class="col-6"><input class="form-control" type="number" step="0.01" name="amount" placeholder="Amount" required></div>
                    <div class="col-6"><select class="form-select" name="category"><option>invoice</option><option>expense</option><option>payroll</option><option>subscription</option></select></div>
                    <div class="col-6"><select class="form-select" name="direction"><option>inflow</option><option>outflow</option></select></div>
                    <div class="col-12"><select class="form-select" name="payment_status"><option>pending</option><option>paid</option><option>overdue</option></select></div>
                    <div class="col-12"><input class="form-control" name="client_name" placeholder="Client (optional)"></div>
                    <div class="col-12"><textarea class="form-control" name="description" rows="2" placeholder="Description"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Transaction</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="row g-3 mb-3">
            <div class="col-md-6"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Total Inflow</div><div class="h3 mb-0"><?= number_format((float) ($totals['inflow'] ?? 0), 2) ?></div></div></div></div>
            <div class="col-md-6"><div class="card tech-card"><div class="card-body"><div class="small text-muted">Total Outflow</div><div class="h3 mb-0"><?= number_format((float) ($totals['outflow'] ?? 0), 2) ?></div></div></div></div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Finance Ledger</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Code</th><th>Category</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?= e((string) $t['transaction_date']) ?></td>
                            <td><?= e((string) $t['transaction_code']) ?></td>
                            <td><?= e((string) $t['category']) ?></td>
                            <td><?= number_format((float) $t['amount'], 2) ?></td>
                            <td><span class="badge text-bg-secondary"><?= e((string) $t['payment_status']) ?></span></td>
                            <td>
                                <?php if ((string) $t['payment_status'] !== 'paid'): ?>
                                    <a class="btn btn-sm btn-outline-success" href="finance.php?action=mark_paid&id=<?= (int) $t['transaction_id'] ?>">Mark Paid</a>
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
