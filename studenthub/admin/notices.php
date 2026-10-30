<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['admin', 'moderator']);

$pageTitle = 'Campus Notice Management - Admin Control';
$activePage = 'admin_notices';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$user = currentUser();

// Create Notice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_notice') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $title    = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'General');
        $content  = trim($_POST['content'] ?? '');
        $priority = trim($_POST['priority'] ?? 'normal');
        $date     = trim($_POST['published_date'] ?? date('Y-m-d'));

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO notices (title, category, content, priority, published_date, created_by) 
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([$title, $category, $content, $priority, $date, $user['id']]);
            logAudit('NOTICE_PUBLISH', "Published notice: $title", $user['id']);
            setFlash('success', 'Notice published successfully.');
        } catch (Throwable $e) {
            setFlash('danger', 'Error publishing notice: ' . $e->getMessage());
        }
        header('Location: ' . APP_URL . '/admin/notices.php');
        exit;
    }
}

// Delete Notice
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_notice') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $delId = (int) $_POST['notice_id'];
        $nStmt = $pdo->prepare('SELECT title FROM notices WHERE id = ?');
        $nStmt->execute([$delId]);
        $noticeTitle = $nStmt->fetchColumn();

        if ($noticeTitle) {
            $pdo->prepare('DELETE FROM notices WHERE id = ?')->execute([$delId]);
            logAudit('NOTICE_DELETE', "Deleted notice: $noticeTitle", $user['id']);
            setFlash('success', 'Notice deleted.');
        }
        header('Location: ' . APP_URL . '/admin/notices.php');
        exit;
    }
}

// Fetch all notices
$notices = $pdo->query('SELECT n.*, u.name as publisher_name FROM notices n LEFT JOIN users u ON n.created_by = u.id ORDER BY n.published_date DESC, n.id DESC')->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Administrative Bulletin</span>
                <h2 class="fw-bold mb-0">Campus Notice Board Management</h2>
                <p class="text-muted small mb-0">Publish official alerts, examination schedules, and timetable advisories.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <button type="button" class="btn btn-primary-sh btn-sm" data-bs-toggle="modal" data-bs-target="#addNoticeModal">
                    <i class="bi bi-megaphone-fill me-1"></i> Post New Notice
                </button>
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
                            <th>Notice Title</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Published Date</th>
                            <th>Author</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($notices)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No notices found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($notices as $n): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($n['title']) ?></div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 320px;"><?= e($n['content']) ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= e($n['category']) ?></span></td>
                                    <td>
                                        <span class="badge bg-<?= ($n['priority'] === 'urgent') ? 'danger' : (($n['priority'] === 'high') ? 'warning' : 'primary') ?>-subtle text-<?= ($n['priority'] === 'urgent') ? 'danger' : (($n['priority'] === 'high') ? 'warning' : 'primary') ?>">
                                            <?= e(ucfirst($n['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($n['published_date'])) ?></td>
                                    <td><?= e($n['publisher_name'] ?? 'System Admin') ?></td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this notice?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="delete_notice">
                                            <input type="hidden" name="notice_id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Notice">
                                                <i class="bi bi-trash"></i>
                                            </button>
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

<!-- Add Notice Modal -->
<div class="modal fade" id="addNoticeModal" tabindex="-1" aria-labelledby="addNoticeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-sh border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="addNoticeModalLabel"><i class="bi bi-megaphone-fill me-2 text-primary"></i>Post Campus Notice</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/notices.php">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="create_notice">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Notice Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Schedule for Lab Submissions">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category" class="form-select">
                                <option value="Academic">Academic</option>
                                <option value="Exam">Exam</option>
                                <option value="Event">Event</option>
                                <option value="General" selected>General</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Priority Level</label>
                            <select name="priority" class="form-select">
                                <option value="normal" selected>Normal</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Date of Release</label>
                            <input type="date" name="published_date" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Notice Detailed Body <span class="text-danger">*</span></label>
                            <textarea name="content" class="form-control" rows="4" required placeholder="Type notice description here..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-sh">Publish Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
