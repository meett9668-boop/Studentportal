<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['admin', 'moderator']);

$pageTitle = 'Contact Inquiries - Practical 7 Records';
$activePage = 'admin_inquiries';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

// Handle Status Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_id'], $_POST['status'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $inqId = (int) $_POST['inquiry_id'];
        $newStatus = in_array($_POST['status'], ['new', 'in_progress', 'resolved'], true) ? $_POST['status'] : 'new';
        $upd = $pdo->prepare('UPDATE inquiries SET status = ? WHERE id = ?');
        $upd->execute([$newStatus, $inqId]);
        setFlash('success', 'Inquiry status updated to ' . e($newStatus));
        header('Location: ' . APP_URL . '/admin/inquiries.php');
        exit;
    }
}

$inquiries = $pdo->query('SELECT * FROM inquiries ORDER BY created_at DESC')->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 7 Data Submissions</span>
                <h2 class="fw-bold mb-0">Contact Inquiries Inbox</h2>
                <p class="text-muted small mb-0">Review submitted inquiries received through the sanitized contact form.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="<?= APP_URL ?>/data/inquiries.csv" class="btn btn-outline-success btn-sm" download>
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
                </a>
                <a href="<?= APP_URL ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Sender Details</th>
                            <th>Contact Info</th>
                            <th>Pincode & City</th>
                            <th>Inquiry Message</th>
                            <th>Received At</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inquiries)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No inquiries received yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($inquiries as $inq): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($inq['name']) ?></div>
                                        <small class="badge bg-light text-secondary"><?= e($inq['gender']) ?></small>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-envelope me-1 text-muted"></i><?= e($inq['email']) ?></div>
                                        <div><i class="bi bi-telephone me-1 text-muted"></i><?= e($inq['mobile']) ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-semibold"><?= e($inq['pincode']) ?></div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;"><?= e($inq['address']) ?></small>
                                    </td>
                                    <td>
                                        <p class="small mb-0 text-dark"><?= e($inq['message']) ?></p>
                                    </td>
                                    <td class="small text-muted"><?= date('M d, Y h:i A', strtotime($inq['created_at'])) ?></td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="new" <?= ($inq['status'] === 'new') ? 'selected' : '' ?>>New</option>
                                                <option value="in_progress" <?= ($inq['status'] === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                                                <option value="resolved" <?= ($inq['status'] === 'resolved') ? 'selected' : '' ?>>Resolved</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
