<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['student', 'admin']);

$pageTitle = 'Student Dashboard - Student Hub';
$activePage = 'student_dashboard';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$userId = currentUser()['id'];

// Retrieve linked student profile
$stuStmt = $pdo->prepare('SELECT * FROM students WHERE user_id = ? LIMIT 1');
$stuStmt->execute([$userId]);
$student = $stuStmt->fetch();

if (!$student) {
    // If admin is browsing or student profile not yet linked
    $student = [
        'id' => 0,
        'student_id' => 'ADMIN',
        'student_name' => currentUser()['name'],
        'course' => 'Engineering',
        'branch' => 'Computer Science',
        'academic_year' => 'Faculty / Staff',
        'gender' => 'Other'
    ];
}

$studentPk = (int) $student['id'];

// Calculate overall attendance
$attStmt = $pdo->prepare('SELECT SUM(total_classes) as total, SUM(classes_attended) as attended FROM attendance WHERE student_id = ?');
$attStmt->execute([$studentPk]);
$attTotals = $attStmt->fetch();
$attendancePercentage = ($attTotals && $attTotals['total'] > 0) 
    ? round(($attTotals['attended'] / $attTotals['total']) * 100, 1) 
    : 87.5;

// Count enrolled events
$regStmt = $pdo->prepare('SELECT COUNT(*) FROM registrations WHERE student_id = ?');
$regStmt->execute([$studentPk]);
$totalEnrolledEvents = (int) $regStmt->fetchColumn();

// Fetch latest notices
$notices = $pdo->query('SELECT * FROM notices ORDER BY published_date DESC LIMIT 3')->fetchAll();

// Fetch upcoming events
$upcomingEvents = $pdo->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 3")->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <!-- Welcome banner -->
        <div class="card card-sh p-4 mb-4 border-0 bg-primary text-white" style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-white text-primary fw-bold text-uppercase mb-2">Student Portal</span>
                    <h2 class="fw-bold mb-1">Welcome back, <?= e($student['student_name']) ?>!</h2>
                    <p class="mb-0 opacity-90">
                        Student ID: <strong><?= e($student['student_id']) ?></strong> &bull; 
                        <?= e($student['course']) ?> (<?= e($student['branch']) ?>) &bull; 
                        <?= e($student['academic_year']) ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="<?= APP_URL ?>/student/profile.php" class="btn btn-light btn-sm fw-semibold shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Metric KPI Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card-sh card-kpi bg-white">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Attendance Progress</span>
                        <h2 class="fw-bold my-1 text-primary"><?= $attendancePercentage ?>%</h2>
                        <span class="badge <?= ($attendancePercentage >= 80) ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' ?>">
                            <?= ($attendancePercentage >= 80) ? 'Good Standing' : 'Low Attendance Alert' ?>
                        </span>
                    </div>
                    <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-sh card-kpi bg-white">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Enrolled Events</span>
                        <h2 class="fw-bold my-1 text-success"><?= $totalEnrolledEvents ?></h2>
                        <a href="<?= APP_URL ?>/student/events.php" class="text-success small fw-medium text-decoration-none">
                            View registered events <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="kpi-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-ticket-detailed-fill"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-sh card-kpi bg-white">
                    <div>
                        <span class="text-muted small text-uppercase fw-semibold">Current Semester</span>
                        <h2 class="fw-bold my-1 text-info">Sem 3</h2>
                        <a href="<?= APP_URL ?>/student/results.php" class="text-info small fw-medium text-decoration-none">
                            Check latest results <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="kpi-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Module Cards (Practical 2 Layout) -->
        <h4 class="fw-bold mb-3">Academic Modules</h4>
        <div class="row g-3 mb-5">
            <div class="col-md-3 col-6">
                <a href="<?= APP_URL ?>/student/attendance.php" class="card card-sh text-center p-3 text-decoration-none border-0 h-100">
                    <i class="bi bi-table text-primary display-6 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-0">Attendance Table</h6>
                    <small class="text-muted">Subject-wise records</small>
                </a>
            </div>

            <div class="col-md-3 col-6">
                <a href="<?= APP_URL ?>/student/results.php" class="card card-sh text-center p-3 text-decoration-none border-0 h-100">
                    <i class="bi bi-mortarboard text-success display-6 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-0">Academic Results</h6>
                    <small class="text-muted">Marks and grades</small>
                </a>
            </div>

            <div class="col-md-3 col-6">
                <a href="<?= APP_URL ?>/student/schedule.php" class="card card-sh text-center p-3 text-decoration-none border-0 h-100">
                    <i class="bi bi-clock-history text-info display-6 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-0">Class Schedule</h6>
                    <small class="text-muted">Weekly timetable</small>
                </a>
            </div>

            <div class="col-md-3 col-6">
                <a href="<?= APP_URL ?>/student/events.php" class="card card-sh text-center p-3 text-decoration-none border-0 h-100">
                    <i class="bi bi-calendar-check text-warning display-6 mb-2"></i>
                    <h6 class="fw-bold text-dark mb-0">My Registrations</h6>
                    <small class="text-muted">Workshops & talks</small>
                </a>
            </div>
        </div>

        <!-- Recent Notices & Events Feed -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-sh p-4 border-0 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-bell-fill text-danger"></i> Recent Department Notices
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($notices as $n): ?>
                            <div class="p-2 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold mb-0 small"><?= e($n['title']) ?></h6>
                                    <span class="badge bg-secondary-subtle text-secondary small"><?= e($n['category']) ?></span>
                                </div>
                                <p class="text-muted small mb-1 text-truncate"><?= e($n['content']) ?></p>
                                <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('M d, Y', strtotime($n['published_date'])) ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-sh p-4 border-0 h-100">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-event text-primary"></i> Available Workshops & Events
                    </h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($upcomingEvents as $ev): ?>
                            <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-1 small"><?= e($ev['event_name']) ?></h6>
                                    <small class="text-muted d-block"><i class="bi bi-geo-alt me-1"></i><?= e($ev['location']) ?></small>
                                    <small class="text-secondary"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($ev['event_date'])) ?></small>
                                </div>
                                <a href="<?= APP_URL ?>/events.php" class="btn btn-outline-primary btn-sm">
                                    Register
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
