<?php
declare(strict_types=1);

$pageTitle = 'Home - Student Hub Portal';
$activePage = 'home';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch latest 3 notices
$noticesStmt = $pdo->query("SELECT * FROM notices ORDER BY published_date DESC, id DESC LIMIT 3");
$recentNotices = $noticesStmt->fetchAll();

// Fetch upcoming 3 events
$eventsStmt = $pdo->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $eventsStmt->fetchAll();
?>

<main>
    <!-- Hero Section -->
    <section class="hero-gradient">
        <div class="container text-center text-lg-start">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-light text-primary px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                        <i class="bi bi-stars me-1"></i> ITUE203 Practical 15 Final Project
                    </span>
                    <h1 class="display-4 fw-bold mb-3">
                        Welcome to Student Hub
                    </h1>
                    <p class="lead mb-4 text-light opacity-90">
                        The unified student lifecycle and academic management portal. Experience integrated attendance tracking, performance insights, event registration, and role-based administrative control.
                    </p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        <?php if (!isAuthenticated()): ?>
                            <a href="<?= APP_URL ?>/register.php" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow">
                                <i class="bi bi-person-plus-fill me-2"></i>Create Student Account
                            </a>
                            <a href="<?= APP_URL ?>/login.php" class="btn btn-outline-light btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </a>
                        <?php else: ?>
                            <?php if ($user['role'] === 'student'): ?>
                                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow">
                                    <i class="bi bi-speedometer2 me-2"></i>Go to My Dashboard
                                </a>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/admin/dashboard.php" class="btn btn-light btn-lg px-4 fw-bold text-primary shadow">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Go to Admin Dashboard
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                        <i class="bi bi-mortarboard display-1 text-info"></i>
                        <h4 class="mt-3 text-white fw-bold">Academic Excellence</h4>
                        <p class="text-white-50 small mb-0">Single sign-on portal connecting students, faculty, and administration.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Features & Practical Highlights -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h6 class="text-primary text-uppercase fw-bold letter-spacing-1">Features & Capabilities</h6>
                <h2 class="fw-bold">Everything You Need in One Portal</h2>
                <p class="text-muted">Directly combining the foundational work of Practicals 1 through 14.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-sh h-100 p-4 border-0">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h5 class="fw-bold">Academic Tracking</h5>
                        <p class="text-muted small">
                            Monitor real-time class attendance percentages, individual subject progress, and semester grade results derived from Practical 2.
                        </p>
                        <a href="<?= APP_URL ?>/student/attendance.php" class="text-primary fw-semibold small mt-auto text-decoration-none">
                            View Attendance <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-sh h-100 p-4 border-0">
                        <div class="kpi-icon bg-success bg-opacity-10 text-success mb-3">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <h5 class="fw-bold">Campus Events & Workshops</h5>
                        <p class="text-muted small">
                            Explore upcoming technical hackathons, workshops, and symposiums. Register instantly with asynchronous REST API integration.
                        </p>
                        <a href="<?= APP_URL ?>/events.php" class="text-success fw-semibold small mt-auto text-decoration-none">
                            Browse Events <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-sh h-100 p-4 border-0">
                        <div class="kpi-icon bg-info bg-opacity-10 text-info mb-3">
                            <i class="bi bi-search"></i>
                        </div>
                        <h5 class="fw-bold">Fetch API Help Center</h5>
                        <p class="text-muted small">
                            Practical 6 dynamic JSON search and filter engine with pagination, sorting, and responsive category tabs.
                        </p>
                        <a href="<?= APP_URL ?>/support.php" class="text-info fw-semibold small mt-auto text-decoration-none">
                            Open Help Center <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Notices & Upcoming Events Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-5">
                <!-- Latest Notices -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-megaphone-fill text-danger"></i> Important Notices
                        </h4>
                        <a href="<?= APP_URL ?>/notices.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($recentNotices)): ?>
                            <div class="alert alert-secondary">No notices published at this time.</div>
                        <?php else: ?>
                            <?php foreach ($recentNotices as $notice): ?>
                                <div class="card card-sh p-3 border-0 border-start border-4 border-<?= ($notice['priority'] === 'urgent') ? 'danger' : (($notice['priority'] === 'high') ? 'warning' : 'primary') ?>">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="fw-bold mb-0"><?= e($notice['title']) ?></h6>
                                        <span class="badge bg-secondary badge-category"><?= e($notice['category']) ?></span>
                                    </div>
                                    <p class="text-muted small mb-2 text-truncate"><?= e($notice['content']) ?></p>
                                    <small class="text-secondary d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar3"></i> <?= date('M d, Y', strtotime($notice['published_date'])) ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Upcoming Events -->
                <div class="col-lg-6">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-calendar3 text-primary"></i> Upcoming Campus Events
                        </h4>
                        <a href="<?= APP_URL ?>/events.php" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <?php if (empty($upcomingEvents)): ?>
                            <div class="alert alert-secondary">No upcoming events scheduled.</div>
                        <?php else: ?>
                            <?php foreach ($upcomingEvents as $event): ?>
                                <div class="card card-sh p-3 border-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="badge bg-primary-subtle text-primary mb-1"><?= e($event['category']) ?></span>
                                            <h6 class="fw-bold mb-1"><?= e($event['event_name']) ?></h6>
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-geo-alt me-1"></i><?= e($event['location']) ?>
                                            </small>
                                            <small class="text-secondary">
                                                <i class="bi bi-clock me-1"></i><?= date('M d, Y', strtotime($event['event_date'])) ?> at <?= e($event['event_time']) ?>
                                            </small>
                                        </div>
                                        <a href="<?= APP_URL ?>/events.php?id=<?= $event['id'] ?>" class="btn btn-outline-primary btn-sm mt-2">
                                            Details
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

