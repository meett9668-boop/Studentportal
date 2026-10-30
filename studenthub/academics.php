<?php
declare(strict_types=1);

$pageTitle = 'Academics - Student Hub Portal';
$activePage = 'academics';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Department programs
$programs = [
    [
        'title' => 'B.Tech in Computer Science & Engineering',
        'code' => 'CSE-BTECH',
        'duration' => '4 Years (8 Semesters)',
        'description' => 'Comprehensive degree covering Algorithms, Full-Stack Web Technologies, Distributed Systems, Cloud Computing, and Artificial Intelligence.',
        'courses' => ['ITUE203 - Web Development Frameworks', 'ITUC201 - Computer Networks', 'ITUC202 - Database Systems', 'ITUC204 - Operating Systems']
    ],
    [
        'title' => 'B.Tech in Information Technology',
        'code' => 'IT-BTECH',
        'duration' => '4 Years (8 Semesters)',
        'description' => 'Specialized engineering curriculum emphasizing enterprise software, cloud microservices, network security, and agile methodologies.',
        'courses' => ['Enterprise Java', 'Network Administration', 'Modern DevOps Pipelines', 'Information Security']
    ],
    [
        'title' => 'B.Tech in Artificial Intelligence & Data Science',
        'code' => 'AIDS-BTECH',
        'duration' => '4 Years (8 Semesters)',
        'description' => 'Advanced engineering program focused on Deep Learning, Machine Learning, Big Data Infrastructure, and Statistical Modeling.',
        'courses' => ['Machine Learning Algorithms', 'Big Data Engineering', 'Computer Vision', 'Deep Neural Networks']
    ]
];
?>

<main class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Curriculum & Degrees</span>
            <h1 class="fw-bold">Academic Programs</h1>
            <p class="text-muted">Rigorous undergraduate engineering programs empowering tomorrow's technologists.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($programs as $prog): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card card-sh h-100 p-4 border-0 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-primary-subtle text-primary fw-bold"><?= e($prog['code']) ?></span>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i><?= e($prog['duration']) ?></small>
                        </div>
                        <h4 class="fw-bold mb-2"><?= e($prog['title']) ?></h4>
                        <p class="text-muted small mb-4"><?= e($prog['description']) ?></p>
                        
                        <div class="mt-auto">
                            <h6 class="fw-bold small text-uppercase text-secondary mb-2">Core Practical Subjects:</h6>
                            <ul class="list-unstyled small mb-3">
                                <?php foreach ($prog['courses'] as $course): ?>
                                    <li class="mb-1 text-secondary"><i class="bi bi-check2 text-primary me-2"></i><?= e($course) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (isAuthenticated() && currentUser()['role'] === 'student'): ?>
                                <a href="<?= APP_URL ?>/student/results.php" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-card-checklist me-1"></i>View Enrolled Results
                                </a>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/register.php" class="btn btn-primary-sh btn-sm w-100">
                                    <i class="bi bi-person-plus me-1"></i>Enroll as Student
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

