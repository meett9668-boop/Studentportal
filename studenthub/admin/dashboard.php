<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['admin', 'moderator']);

$pageTitle = 'Admin Dashboard - Student Hub';
$activePage = 'admin_dashboard';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$user = currentUser();

// Dynamic KPI Statistics from MySQL (Practical 14)
$totalUsers = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$totalStudents = (int) $pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$totalEvents = (int) $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$totalRegistrations = (int) $pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$totalInquiries = (int) $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn();

// Fetch latest 5 registrations with JOIN (Practical 14)
$stmt = $pdo->query(
    "SELECT r.id, r.registration_date, r.status, s.student_name, s.student_id, e.event_name
     FROM registrations r
     JOIN students s ON r.student_id = s.id
     JOIN events e ON r.event_id = e.id
     ORDER BY r.registration_date DESC
     LIMIT 5"
);
$latestRegistrations = $stmt->fetchAll();

// Fetch recent 5 audit logs (Practical 14)
$auditStmt = $pdo->query(
    "SELECT a.*, u.name as user_name, u.email 
     FROM audit_logs a 
     LEFT JOIN users u ON a.user_id = u.id 
     ORDER BY a.created_at DESC 
     LIMIT 5"
);
$recentAudits = $auditStmt->fetchAll();

// Log dashboard viewing
logAudit('ADMIN_DASHBOARD_VIEW', 'Admin accessed overview statistics', $user['id']);
?>

<main class="py-5">
    <div class="container">
        <!-- Top banner with role indicator -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-danger bg-opacity-10 text-danger fw-bold text-uppercase mb-1">Practical 14 Admin Control Centre</span>
                <h2 class="fw-bold mb-0">System Overview & Management</h2>
                <p class="text-muted small mb-0">Logged in as <strong><?= e($user['name']) ?></strong> (Role: <span class="badge bg-dark text-uppercase"><?= e($user['role']) ?></span>)</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <a href="<?= APP_URL ?>/admin/students.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-people me-1"></i> Students
                </a>
                <a href="<?= APP_URL ?>/admin/events.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-calendar-event me-1"></i> Events
                </a>
                <a href="<?= APP_URL ?>/admin/notices.php" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-megaphone me-1"></i> Notices
                </a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= APP_URL ?>/admin/audit_logs.php" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-shield-check me-1"></i> Audit Trail
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Metric KPI Cards (Practical 14 Dynamic Totals) -->
        <div class="row g-4 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card-sh card-kpi bg-white border-0">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Users</span>
                        <h2 class="fw-bold my-1 text-primary"><?= $totalUsers ?></h2>
                        <small class="text-secondary">System accounts</small>
                    </div>
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-sh card-kpi bg-white border-0">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Total Students</span>
                        <h2 class="fw-bold my-1 text-success"><?= $totalStudents ?></h2>
                        <a href="<?= APP_URL ?>/admin/students.php" class="small text-success text-decoration-none">Manage students <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-sh card-kpi bg-white border-0">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Campus Events</span>
                        <h2 class="fw-bold my-1 text-info"><?= $totalEvents ?></h2>
                        <a href="<?= APP_URL ?>/admin/events.php" class="small text-info text-decoration-none">Manage events <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="kpi-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-calendar2-event-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="card-sh card-kpi bg-white border-0">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Registrations</span>
                        <h2 class="fw-bold my-1 text-warning"><?= $totalRegistrations ?></h2>
                        <small class="text-secondary">Event enrollments</small>
                    </div>
                    <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-ticket-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Latest Event Registrations Table (Practical 14) -->
        <div class="card card-sh border-0 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Latest Event Registrations</h5>
                <span class="badge bg-secondary-subtle text-secondary">Recent 5</span>
            </div>
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Event Name</th>
                            <th>Registered At</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($latestRegistrations)): ?>
                            <tr><td colspan="5" class="text-center py-4 text-muted">No student registrations recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($latestRegistrations as $reg): ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= e($reg['student_name']) ?></td>
                                    <td><span class="badge bg-light text-secondary"><?= e($reg['student_id']) ?></span></td>
                                    <td><?= e($reg['event_name']) ?></td>
                                    <td><?= date('M d, Y h:i A', strtotime($reg['registration_date'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-<?= ($reg['status'] === 'approved') ? 'success' : 'warning' ?>-subtle text-<?= ($reg['status'] === 'approved') ? 'success' : 'warning' ?> px-3 py-1">
                                            <?= e(ucfirst($reg['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Audit Activity Log Snippet (Practical 14) -->
        <?php if ($user['role'] === 'admin'): ?>
            <div class="card card-sh border-0 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Recent Administrative Audit Logs</h5>
                    <a href="<?= APP_URL ?>/admin/audit_logs.php" class="btn btn-outline-danger btn-sm">
                        View Full Audit Trail <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-sh align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Action</th>
                                <th>User</th>
                                <th>Details</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAudits as $log): ?>
                                <tr>
                                    <td class="small text-muted"><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                                    <td><span class="badge bg-dark"><?= e($log['action']) ?></span></td>
                                    <td class="small fw-semibold"><?= e($log['user_name'] ?? 'System') ?></td>
                                    <td class="small text-muted"><?= e($log['details']) ?></td>
                                    <td class="small font-monospace"><?= e($log['ip_address']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
