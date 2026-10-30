<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['student', 'admin']);

$pageTitle = 'My Attendance - Student Hub';
$activePage = 'attendance';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$userId = currentUser()['id'];

// Get student record id
$stuStmt = $pdo->prepare('SELECT id, student_name, student_id FROM students WHERE user_id = ?');
$stuStmt->execute([$userId]);
$student = $stuStmt->fetch();
$studentId = $student ? (int) $student['id'] : 1; // Fallback to first student if admin view

// Retrieve attendance records (Practical 2 integration)
$attStmt = $pdo->prepare('SELECT * FROM attendance WHERE student_id = ?');
$attStmt->execute([$studentId]);
$records = $attStmt->fetchAll();

// Calculate total stats
$totalClasses = 0;
$attendedClasses = 0;
foreach ($records as $r) {
    $totalClasses += (int) $r['total_classes'];
    $attendedClasses += (int) $r['classes_attended'];
}
$overallPercent = ($totalClasses > 0) ? round(($attendedClasses / $totalClasses) * 100, 1) : 0;
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 2 HTML5 Tables</span>
                <h2 class="fw-bold mb-0">Course Attendance Records</h2>
                <p class="text-muted small mb-0">Live attendance status for <strong><?= e($student['student_name'] ?? 'Meet Tanti') ?></strong> (<?= e($student['student_id'] ?? '25CS104') ?>)</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Attendance Summary Card -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Total Lectures Delivered</small>
                    <h3 class="fw-bold text-dark my-1"><?= $totalClasses ?></h3>
                    <small class="text-secondary">Across all enrolled subjects</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Total Lectures Attended</small>
                    <h3 class="fw-bold text-success my-1"><?= $attendedClasses ?></h3>
                    <small class="text-secondary">Verified physical attendance</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Aggregate Percentage</small>
                    <h3 class="fw-bold <?= ($overallPercent >= 80) ? 'text-primary' : 'text-danger' ?> my-1"><?= $overallPercent ?>%</h3>
                    <small class="text-secondary"><?= ($overallPercent >= 80) ? 'Eligible for end-semester exams' : 'Attendance shortfall warning' ?></small>
                </div>
            </div>
        </div>

        <!-- Practical 2 Attendance HTML Table -->
        <div class="card card-sh border-0 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0">Subject-wise Attendance Breakdown</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject / Course Title</th>
                            <th>Semester</th>
                            <th class="text-center">Total Classes</th>
                            <th class="text-center">Classes Attended</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Eligibility Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($records)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No attendance data registered for this account.</td></tr>
                        <?php else: ?>
                            <?php foreach ($records as $r): ?>
                                <?php 
                                    $pct = round(($r['classes_attended'] / max(1, $r['total_classes'])) * 100, 1);
                                    $isEligible = $pct >= 75.0;
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($r['subject']) ?></div>
                                    </td>
                                    <td><span class="badge bg-light text-secondary"><?= e($r['semester']) ?></span></td>
                                    <td class="text-center fw-semibold"><?= (int) $r['total_classes'] ?></td>
                                    <td class="text-center text-success fw-bold"><?= (int) $r['classes_attended'] ?></td>
                                    <td class="text-center">
                                        <div class="fw-bold <?= $isEligible ? 'text-primary' : 'text-danger' ?>"><?= $pct ?>%</div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar <?= $isEligible ? 'bg-primary' : 'bg-danger' ?>" role="progressbar" style="width: <?= $pct ?>%"></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($isEligible): ?>
                                            <span class="badge bg-success-subtle text-success px-3 py-1">Eligible</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger-subtle text-danger px-3 py-1">Shortage</span>
                                        <?php endif; ?>
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
