<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

requireRole(['student', 'admin']);

$pageTitle = 'Class Schedule - Student Hub';
$activePage = 'schedule';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$timetable = [
    ['time' => '09:00 AM - 10:00 AM', 'mon' => 'ITUE203 (Theory)', 'tue' => 'ITUC201 (Networks)', 'wed' => 'ITUC202 (DBMS)', 'thu' => 'ITUE203 (Theory)', 'fri' => 'ITUC204 (OS)'],
    ['time' => '10:00 AM - 11:00 AM', 'mon' => 'ITUC202 (DBMS)', 'tue' => 'ITUE203 (Lab A)', 'wed' => 'ITUE203 (Lab B)', 'thu' => 'ITUC201 (Networks)', 'fri' => 'ITUC202 (DBMS)'],
    ['time' => '11:15 AM - 01:15 PM', 'mon' => 'Web Dev Practical Lab', 'tue' => 'DBMS Practical Lab', 'wed' => 'Networks Practical Lab', 'thu' => 'OS Practical Lab', 'fri' => 'Project / Mentoring'],
    ['time' => '02:00 PM - 03:00 PM', 'mon' => 'Soft Skills', 'tue' => 'Discrete Math', 'wed' => 'Library / Research', 'thu' => 'Tutorials', 'fri' => 'Club Activity']
];
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 2 Timetable Grid</span>
                <h2 class="fw-bold mb-0">Weekly Class Schedule</h2>
                <p class="text-muted small mb-0">B.Tech Computer Science & Engineering - Semester 3</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 18%;">Time Slot</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($timetable as $slot): ?>
                            <tr>
                                <td class="fw-bold bg-light text-secondary"><?= e($slot['time']) ?></td>
                                <td><?= e($slot['mon']) ?></td>
                                <td><?= e($slot['tue']) ?></td>
                                <td><?= e($slot['wed']) ?></td>
                                <td><?= e($slot['thu']) ?></td>
                                <td><?= e($slot['fri']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
