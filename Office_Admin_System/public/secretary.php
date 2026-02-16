<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/bootstrap.php';

$user = require_auth();

if (($_GET['action'] ?? '') === 'close_correspondence' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE correspondence SET status = 'closed' WHERE correspondence_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('secretary.php');
}

if (($_GET['action'] ?? '') === 'complete_appointment' && isset($_GET['id'])) {
    $stmt = db()->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    redirect('secretary.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    if ($formType === 'appointment') {
        $stmt = db()->prepare('INSERT INTO appointments (title, participant_name, participant_type, owner_name, department, appointment_date, appointment_time, duration_minutes, channel, notes, created_by) VALUES (:title, :participant_name, :participant_type, :owner_name, :department, :appointment_date, :appointment_time, :duration_minutes, :channel, :notes, :created_by)');
        $stmt->execute([
            'title' => trim($_POST['title'] ?? ''),
            'participant_name' => trim($_POST['participant_name'] ?? ''),
            'participant_type' => trim($_POST['participant_type'] ?? 'internal'),
            'owner_name' => trim($_POST['owner_name'] ?? ''),
            'department' => trim($_POST['department'] ?? ''),
            'appointment_date' => trim($_POST['appointment_date'] ?? ''),
            'appointment_time' => trim($_POST['appointment_time'] ?? ''),
            'duration_minutes' => (int) ($_POST['duration_minutes'] ?? 30),
            'channel' => trim($_POST['channel'] ?? 'onsite'),
            'notes' => trim($_POST['notes'] ?? ''),
            'created_by' => (string) $user['username'],
        ]);
    }

    if ($formType === 'correspondence') {
        $stmt = db()->prepare('INSERT INTO correspondence (reference_number, correspondence_type, direction, from_sender, to_recipient, subject, priority, department, assigned_to, summary, action_required) VALUES (:reference_number, :correspondence_type, :direction, :from_sender, :to_recipient, :subject, :priority, :department, :assigned_to, :summary, :action_required)');
        $stmt->execute([
            'reference_number' => trim($_POST['reference_number'] ?? ''),
            'correspondence_type' => trim($_POST['correspondence_type'] ?? 'email'),
            'direction' => trim($_POST['direction'] ?? 'incoming'),
            'from_sender' => trim($_POST['from_sender'] ?? ''),
            'to_recipient' => trim($_POST['to_recipient'] ?? ''),
            'subject' => trim($_POST['subject'] ?? ''),
            'priority' => trim($_POST['priority'] ?? 'medium'),
            'department' => trim($_POST['department'] ?? ''),
            'assigned_to' => trim($_POST['assigned_to'] ?? ''),
            'summary' => trim($_POST['summary'] ?? ''),
            'action_required' => isset($_POST['action_required']) ? 1 : 0,
        ]);
    }

    redirect('secretary.php');
}

$appointments = db()->query("SELECT appointment_id, title, participant_name, appointment_date, appointment_time, channel, status FROM appointments ORDER BY appointment_date DESC, appointment_time DESC LIMIT 40")->fetchAll();
$letters = db()->query("SELECT correspondence_id, reference_number, subject, correspondence_type, priority, status, created_at FROM correspondence ORDER BY created_at DESC LIMIT 40")->fetchAll();

render_header('Secretary Operations', $user);
?>
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h1 class="h5 mb-0">Schedule Item</h1></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="appointment">
                    <div class="col-12"><input class="form-control" name="title" placeholder="Meeting title" required></div>
                    <div class="col-6"><input class="form-control" name="participant_name" placeholder="Participant" required></div>
                    <div class="col-6">
                        <select class="form-select" name="participant_type"><option>internal</option><option>client</option><option>vendor</option></select>
                    </div>
                    <div class="col-6"><input class="form-control" name="owner_name" placeholder="Owner" required></div>
                    <div class="col-6"><input class="form-control" name="department" placeholder="Department"></div>
                    <div class="col-6"><input class="form-control" type="date" name="appointment_date" required></div>
                    <div class="col-6"><input class="form-control" type="time" name="appointment_time" required></div>
                    <div class="col-6"><input class="form-control" type="number" name="duration_minutes" value="30" min="15"></div>
                    <div class="col-6"><select class="form-select" name="channel"><option>onsite</option><option>online</option><option>phone</option></select></div>
                    <div class="col-12"><textarea class="form-control" name="notes" rows="2" placeholder="Notes"></textarea></div>
                    <div class="col-12 d-grid"><button class="btn btn-primary">Save Schedule</button></div>
                </form>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Register Correspondence</h2></div>
            <div class="card-body">
                <form method="post" class="row g-2">
                    <input type="hidden" name="form_type" value="correspondence">
                    <div class="col-6"><input class="form-control" name="reference_number" placeholder="Reference" required></div>
                    <div class="col-6"><select class="form-select" name="correspondence_type"><option>email</option><option>letter</option><option>memo</option><option>ticket_update</option></select></div>
                    <div class="col-6"><select class="form-select" name="direction"><option>incoming</option><option>outgoing</option></select></div>
                    <div class="col-6"><select class="form-select" name="priority"><option>low</option><option selected>medium</option><option>high</option><option>urgent</option></select></div>
                    <div class="col-6"><input class="form-control" name="from_sender" placeholder="From"></div>
                    <div class="col-6"><input class="form-control" name="to_recipient" placeholder="To"></div>
                    <div class="col-12"><input class="form-control" name="subject" placeholder="Subject"></div>
                    <div class="col-6"><input class="form-control" name="department" placeholder="Department"></div>
                    <div class="col-6"><input class="form-control" name="assigned_to" placeholder="Assigned to"></div>
                    <div class="col-12"><textarea class="form-control" name="summary" rows="2" placeholder="Summary"></textarea></div>
                    <div class="col-12 form-check ms-1"><input class="form-check-input" id="ar" type="checkbox" name="action_required"><label for="ar" class="form-check-label">Action required</label></div>
                    <div class="col-12 d-grid"><button class="btn btn-outline-primary">Save Correspondence</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card glass-card tech-card mb-3">
            <div class="card-header"><h2 class="h6 mb-0">Schedule Board</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Title</th><th>Participant</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($appointments as $a): ?>
                    <tr>
                        <td><?= e((string) $a['appointment_date']) ?> <?= e((string) $a['appointment_time']) ?></td>
                        <td><?= e((string) $a['title']) ?></td>
                        <td><?= e((string) $a['participant_name']) ?></td>
                        <td><span class="badge text-bg-secondary"><?= e((string) $a['status']) ?></span></td>
                        <td>
                            <?php if ((string) $a['status'] !== 'completed'): ?>
                                <a class="btn btn-sm btn-outline-success" href="secretary.php?action=complete_appointment&id=<?= (int) $a['appointment_id'] ?>">Complete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card glass-card tech-card">
            <div class="card-header"><h2 class="h6 mb-0">Correspondence Queue</h2></div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Ref</th><th>Subject</th><th>Priority</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($letters as $c): ?>
                    <tr>
                        <td><?= e((string) $c['reference_number']) ?></td>
                        <td><?= e((string) ($c['subject'] ?? '')) ?></td>
                        <td><?= e((string) $c['priority']) ?></td>
                        <td><span class="badge text-bg-secondary"><?= e((string) $c['status']) ?></span></td>
                        <td>
                            <?php if ((string) $c['status'] !== 'closed'): ?>
                                <a class="btn btn-sm btn-outline-success" href="secretary.php?action=close_correspondence&id=<?= (int) $c['correspondence_id'] ?>">Close</a>
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
