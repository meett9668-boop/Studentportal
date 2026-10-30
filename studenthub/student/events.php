<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['student', 'admin']);

$pageTitle = 'My Event Registrations - Student Hub';
$activePage = 'student_events';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$userId = currentUser()['id'];

// Get student ID
$stuStmt = $pdo->prepare('SELECT id FROM students WHERE user_id = ?');
$stuStmt->execute([$userId]);
$studentId = $stuStmt->fetchColumn();

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reg_id'])) {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $regId = (int) $_POST['cancel_reg_id'];
        $delStmt = $pdo->prepare('DELETE FROM registrations WHERE id = ? AND student_id = ?');
        $delStmt->execute([$regId, $studentId]);
        logAudit('EVENT_CANCEL_REG', "Student canceled registration ID: $regId", $userId);
        setFlash('info', 'Your event registration has been canceled.');
        header('Location: ' . APP_URL . '/student/events.php');
        exit;
    }
}

// Fetch all registrations for this student
$query = "SELECT r.id as reg_id, r.registration_date, r.status as reg_status, 
                 e.id as event_id, e.event_name, e.event_date, e.event_time, e.location, e.category
          FROM registrations r
          JOIN events e ON r.event_id = e.id
          WHERE r.student_id = ?
          ORDER BY r.registration_date DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$studentId ?: 0]);
$registrations = $stmt->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 8 & 13 Integration</span>
                <h2 class="fw-bold mb-0">My Enrolled Events</h2>
                <p class="text-muted small mb-0">Track confirmation status and manage your workshop reservations.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="<?= APP_URL ?>/events.php" class="btn btn-primary-sh btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Browse More Events
                </a>
                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Category</th>
                            <th>Event Date & Time</th>
                            <th>Location</th>
                            <th>Registration Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-ticket-detailed display-4 text-secondary d-block mb-2"></i>
                                    You have not registered for any events yet.<br>
                                    <a href="<?= APP_URL ?>/events.php" class="btn btn-outline-primary btn-sm mt-3">Explore Events Directory</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($registrations as $r): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($r['event_name']) ?></div>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= e($r['category']) ?></span></td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= date('M d, Y', strtotime($r['event_date'])) ?></div>
                                        <small class="text-muted"><?= e($r['event_time']) ?></small>
                                    </td>
                                    <td><?= e($r['location']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($r['registration_date'])) ?></td>
                                    <td class="text-center">
                                        <?php if ($r['reg_status'] === 'approved'): ?>
                                            <span class="badge bg-success-subtle text-success px-3 py-1">Confirmed</span>
                                        <?php elseif ($r['reg_status'] === 'rejected'): ?>
                                            <span class="badge bg-danger-subtle text-danger px-3 py-1">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning px-3 py-1">Registered</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" onsubmit="return confirm('Are you sure you want to cancel this event registration?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="cancel_reg_id" value="<?= $r['reg_id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Cancel Registration">
                                                <i class="bi bi-x-circle me-1"></i> Cancel
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

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
