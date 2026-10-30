<?php
declare(strict_types=1);

$pageTitle = 'About & Sitemap - Student Hub Portal';
$activePage = 'about';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-8">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Practical 15 Capstone</span>
                <h1 class="fw-bold display-5">About Student Hub</h1>
                <p class="lead text-muted">
                    Student Hub is an integrated, end-to-end web portal engineered for the course <strong>ITUE203: Web Development Frameworks</strong> at CSPIT, Charusat.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <div class="card card-sh p-3 text-start">
                    <small class="text-muted text-uppercase fw-bold">Candidate Details</small>
                    <h5 class="fw-bold mb-1 text-primary">Meet Tanti</h5>
                    <p class="small text-muted mb-0">Student ID: <strong>25CS104</strong></p>
                    <p class="small text-muted mb-0">Semester: 3rd (CSE)</p>
                </div>
            </div>
        </div>

        <!-- Practical 1: System Sitemap Hierarchy -->
        <div class="card card-sh p-4 mb-5 border-0">
            <h3 class="fw-bold mb-4 d-flex align-items-center gap-2">
                <i class="bi bi-diagram-3-fill text-primary"></i> Application Sitemap (Practical 1 & 2)
            </h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-globe me-2"></i>1. Public Website</h6>
                        <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                            <li><a href="<?= APP_URL ?>/index.php" class="text-decoration-none">/index.php (Portal Home)</a></li>
                            <li><a href="<?= APP_URL ?>/about.php" class="text-decoration-none fw-semibold">/about.php (About & Sitemap)</a></li>
                            <li><a href="<?= APP_URL ?>/academics.php" class="text-decoration-none">/academics.php (Courses & Details)</a></li>
                            <li><a href="<?= APP_URL ?>/events.php" class="text-decoration-none">/events.php (Campus Events)</a></li>
                            <li><a href="<?= APP_URL ?>/notices.php" class="text-decoration-none">/notices.php (Announcements)</a></li>
                            <li><a href="<?= APP_URL ?>/support.php" class="text-decoration-none">/support.php (Fetch API FAQs)</a></li>
                            <li><a href="<?= APP_URL ?>/contact.php" class="text-decoration-none">/contact.php (Contact Inquiries)</a></li>
                            <li><a href="<?= APP_URL ?>/login.php" class="text-decoration-none">/login.php (Authentication)</a></li>
                            <li><a href="<?= APP_URL ?>/register.php" class="text-decoration-none">/register.php (Student Signup)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-person-workspace me-2"></i>2. Student Portal</h6>
                        <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                            <li><a href="<?= APP_URL ?>/student/dashboard.php" class="text-decoration-none">/student/dashboard.php (Overview)</a></li>
                            <li><a href="<?= APP_URL ?>/student/profile.php" class="text-decoration-none">/student/profile.php (Profile Details)</a></li>
                            <li><a href="<?= APP_URL ?>/student/attendance.php" class="text-decoration-none">/student/attendance.php (Attendance)</a></li>
                            <li><a href="<?= APP_URL ?>/student/results.php" class="text-decoration-none">/student/results.php (Academic Results)</a></li>
                            <li><a href="<?= APP_URL ?>/student/schedule.php" class="text-decoration-none">/student/schedule.php (Timetable)</a></li>
                            <li><a href="<?= APP_URL ?>/student/events.php" class="text-decoration-none">/student/events.php (Event History)</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <h6 class="fw-bold text-danger mb-3"><i class="bi bi-shield-lock me-2"></i>3. Admin Control Centre</h6>
                        <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                            <li><a href="<?= APP_URL ?>/admin/dashboard.php" class="text-decoration-none">/admin/dashboard.php (KPI & Stats)</a></li>
                            <li><a href="<?= APP_URL ?>/admin/students.php" class="text-decoration-none">/admin/students.php (Student CRUD)</a></li>
                            <li><a href="<?= APP_URL ?>/admin/events.php" class="text-decoration-none">/admin/events.php (Event CRUD)</a></li>
                            <li><a href="<?= APP_URL ?>/admin/notices.php" class="text-decoration-none">/admin/notices.php (Notice CRUD)</a></li>
                            <li><a href="<?= APP_URL ?>/admin/inquiries.php" class="text-decoration-none">/admin/inquiries.php (Messages)</a></li>
                            <li><a href="<?= APP_URL ?>/admin/audit_logs.php" class="text-decoration-none">/admin/audit_logs.php (Audit Trail)</a></li>
                            <li><a href="<?= APP_URL ?>/api/events.php" class="text-decoration-none">/api/events.php (REST API)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Integrated Practicals Matrix -->
        <div class="card card-sh p-4 border-0">
            <h3 class="fw-bold mb-3"><i class="bi bi-check2-circle text-success me-2"></i>Integrated Lab Practicals (1 to 14)</h3>
            <p class="text-muted mb-4">Each practical has been systematically unified into this single architecture.</p>
            
            <div class="table-responsive">
                <table class="table table-sh align-middle">
                    <thead>
                        <tr>
                            <th style="width: 10%;">Prac</th>
                            <th style="width: 25%;">Domain / Subject</th>
                            <th style="width: 35%;">Key Technical Concepts</th>
                            <th style="width: 30%;">Implemented Module</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td><strong>P1</strong></td><td>Structure & Sitemap</td><td>Directory structure, Git repo, Sitemap</td><td><span class="badge bg-primary">About Page & Hierarchy</span></td></tr>
                        <tr><td><strong>P2</strong></td><td>Semantic HTML5</td><td>Tables, headers, nav, semantic layout</td><td><span class="badge bg-primary">Attendance & Results</span></td></tr>
                        <tr><td><strong>P3</strong></td><td>CSS Grid & Bootstrap</td><td>Grid system, Flexbox, responsive design</td><td><span class="badge bg-primary">Unified Design System</span></td></tr>
                        <tr><td><strong>P4</strong></td><td>JavaScript DOM</td><td>Theme switcher, drawer menu, modal</td><td><span class="badge bg-primary">assets/js/app.js</span></td></tr>
                        <tr><td><strong>P5</strong></td><td>Form Validation</td><td>RegEx rules, live strength meter</td><td><span class="badge bg-primary">register.php validation</span></td></tr>
                        <tr><td><strong>P6</strong></td><td>Fetch API & JSON</td><td>External JSON, async fetch, search, filter</td><td><span class="badge bg-success">support.php (FAQ Engine)</span></td></tr>
                        <tr><td><strong>P7</strong></td><td>Server Form Processing</td><td>Input sanitization, CSV & DB storage</td><td><span class="badge bg-success">contact.php & Inquiries</span></td></tr>
                        <tr><td><strong>P8</strong></td><td>Database Design</td><td>MySQL 3NF schema, PDO, prepared statements</td><td><span class="badge bg-success">studenthub_db & db.php</span></td></tr>
                        <tr><td><strong>P9</strong></td><td>User Registration</td><td>password_hash(), duplicate checks, atomic DB</td><td><span class="badge bg-success">register.php logic</span></td></tr>
                        <tr><td><strong>P10</strong></td><td>Authentication & Sessions</td><td>password_verify(), session_regenerate_id(), RBAC</td><td><span class="badge bg-info">login.php & auth.php</span></td></tr>
                        <tr><td><strong>P11</strong></td><td>Student Management</td><td>Full CRUD, search, filter, pagination</td><td><span class="badge bg-warning text-dark">admin/students.php</span></td></tr>
                        <tr><td><strong>P12</strong></td><td>Event Management</td><td>CRUD, multipart file upload, poster images</td><td><span class="badge bg-warning text-dark">admin/events.php</span></td></tr>
                        <tr><td><strong>P13</strong></td><td>RESTful JSON API</td><td>PHP JSON endpoints, async Fetch CRUD</td><td><span class="badge bg-warning text-dark">api/events.php</span></td></tr>
                        <tr><td><strong>P14</strong></td><td>Admin Dashboard & Audit</td><td>Aggregates, latest registrations, audit logging</td><td><span class="badge bg-danger">admin/dashboard.php</span></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

