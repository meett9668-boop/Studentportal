<?php
declare(strict_types=1);

$user = currentUser();
$role = $user['role'] ?? 'guest';
?>
<nav class="navbar navbar-expand-lg navbar-dark navbar-studenthub sticky-top" aria-label="Main Navigation">
    <div class="container">
        <a class="navbar-brand navbar-brand-badge" href="<?= APP_URL ?>/index.php">
            <i class="bi bi-mortarboard-fill"></i>
            <span>Student Hub</span>
        </a>
        
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'home') ? 'active' : '' ?>" href="<?= APP_URL ?>/index.php">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'about') ? 'active' : '' ?>" href="<?= APP_URL ?>/about.php">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'academics') ? 'active' : '' ?>" href="<?= APP_URL ?>/academics.php">
                        <i class="bi bi-book me-1"></i>Academics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'events') ? 'active' : '' ?>" href="<?= APP_URL ?>/events.php">
                        <i class="bi bi-calendar-event me-1"></i>Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'notices') ? 'active' : '' ?>" href="<?= APP_URL ?>/notices.php">
                        <i class="bi bi-bell me-1"></i>Notices
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'support') ? 'active' : '' ?>" href="<?= APP_URL ?>/support.php">
                        <i class="bi bi-question-circle me-1"></i>Help & FAQ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($activePage === 'contact') ? 'active' : '' ?>" href="<?= APP_URL ?>/contact.php">
                        <i class="bi bi-envelope me-1"></i>Contact
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-2">
                <!-- Theme toggle button (Practical 4) -->
                <button type="button" class="btn btn-sm btn-outline-light border-0 px-2" id="themeToggleBtn" title="Toggle Theme" aria-label="Toggle Theme">
                    <i class="bi bi-moon-stars-fill"></i>
                </button>

                <?php if ($role === 'guest'): ?>
                    <a href="<?= APP_URL ?>/login.php" class="btn btn-outline-light btn-sm px-3">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Sign In
                    </a>
                    <a href="<?= APP_URL ?>/register.php" class="btn btn-light btn-sm px-3 fw-semibold">
                        <i class="bi bi-person-plus me-1"></i>Register
                    </a>
                <?php else: ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span class="fw-medium"><?= e($user['name']) ?></span>
                            <span class="badge bg-primary text-uppercase" style="font-size:0.65rem;"><?= e($role) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <?php if ($role === 'student'): ?>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/student/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/student/profile.php"><i class="bi bi-person-lines-fill me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/student/attendance.php"><i class="bi bi-calendar-check me-2"></i>My Attendance</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/student/results.php"><i class="bi bi-trophy me-2"></i>My Results</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/student/events.php"><i class="bi bi-ticket-perforated me-2"></i>My Event Registrations</a></li>
                            <?php elseif ($role === 'admin' || $role === 'moderator'): ?>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/students.php"><i class="bi bi-people me-2"></i>Student Management</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/events.php"><i class="bi bi-calendar3 me-2"></i>Event Management</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/notices.php"><i class="bi bi-megaphone me-2"></i>Notice Management</a></li>
                                <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/inquiries.php"><i class="bi bi-inbox me-2"></i>Contact Inquiries</a></li>
                                <?php if ($role === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= APP_URL ?>/admin/audit_logs.php"><i class="bi bi-shield-check me-2"></i>System Audit Logs</a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= APP_URL ?>/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
<div class="container">
    <?php renderFlash(); ?>
</div>

