<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['student', 'admin']);

$pageTitle = 'Academic Results - Student Hub';
$activePage = 'results';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$userId = currentUser()['id'];

// Get student record
$stuStmt = $pdo->prepare('SELECT id, student_name, student_id FROM students WHERE user_id = ?');
$stuStmt->execute([$userId]);
$student = $stuStmt->fetch();
$studentId = $student ? (int) $student['id'] : 1;

// Retrieve results records (Practical 2 integration)
$resStmt = $pdo->prepare('SELECT * FROM academic_results WHERE student_id = ?');
$resStmt->execute([$studentId]);
$results = $resStmt->fetchAll();

$totalMarks = 0;
$totalMax = 0;
foreach ($results as $res) {
    $totalMarks += (int) $res['marks_obtained'];
    $totalMax += (int) $res['max_marks'];
}
$overallPercent = ($totalMax > 0) ? round(($totalMarks / $totalMax) * 100, 1) : 0;
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 2 Grade Book</span>
                <h2 class="fw-bold mb-0">Semester Academic Results</h2>
                <p class="text-muted small mb-0">Official grade sheet for <strong><?= e($student['student_name'] ?? 'Meet Tanti') ?></strong> (<?= e($student['student_id'] ?? '25CS104') ?>)</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Total Marks Obtained</small>
                    <h3 class="fw-bold text-dark my-1"><?= $totalMarks ?> / <?= $totalMax ?></h3>
                    <small class="text-secondary">Cumulative theory and lab marks</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Overall Percentage</small>
                    <h3 class="fw-bold text-success my-1"><?= $overallPercent ?>%</h3>
                    <small class="text-secondary">First Class with Distinction</small>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card-sh p-3 border-0 bg-white">
                    <small class="text-muted text-uppercase fw-semibold">Calculated SGPA</small>
                    <h3 class="fw-bold text-primary my-1"><?= round($overallPercent / 9.5, 2) ?></h3>
                    <small class="text-secondary">On a 10.0 scale</small>
                </div>
            </div>
        </div>

        <!-- Practical 2 Results HTML Table -->
        <div class="card card-sh border-0 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0">Grade Statement</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Subject Title</th>
                            <th>Semester</th>
                            <th class="text-center">Maximum Marks</th>
                            <th class="text-center">Marks Obtained</th>
                            <th class="text-center">Percentage</th>
                            <th class="text-center">Letter Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($results)): ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No academic results released yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($results as $res): ?>
                                <?php 
                                    $pct = round(($res['marks_obtained'] / max(1, $res['max_marks'])) * 100, 1);
                                ?>
                                <tr>
                                    <td class="fw-bold text-dark"><?= e($res['subject']) ?></td>
                                    <td><span class="badge bg-light text-secondary"><?= e($res['semester']) ?></span></td>
                                    <td class="text-center"><?= (int) $res['max_marks'] ?></td>
                                    <td class="text-center fw-bold text-primary"><?= (int) $res['marks_obtained'] ?></td>
                                    <td class="text-center fw-semibold"><?= $pct ?>%</td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success px-3 py-1 fw-bold fs-6">
                                            <?= e($res['grade']) ?>
                                        </span>
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
