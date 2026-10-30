<?php
declare(strict_types=1);

$pageTitle = 'Campus Notices & Circulars - Student Hub Portal';
$activePage = 'notices';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$category = trim($_GET['category'] ?? '');

$query = "SELECT n.*, u.name as publisher_name 
          FROM notices n 
          LEFT JOIN users u ON n.created_by = u.id 
          WHERE 1=1";
$params = [];

if ($category !== '') {
    $query .= " AND n.category = ?";
    $params[] = $category;
}

$query .= " ORDER BY n.published_date DESC, n.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$notices = $stmt->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Campus Announcements</span>
                <h1 class="fw-bold">Notices & Circulars</h1>
                <p class="text-muted mb-0">Official academic schedules, examination notifications, and administrative advisories.</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <?php if (isAuthenticated() && in_array(currentUser()['role'], ['admin', 'moderator'], true)): ?>
                    <a href="<?= APP_URL ?>/admin/notices.php" class="btn btn-primary-sh btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Publish Notice
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filter pills -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="<?= APP_URL ?>/notices.php" class="btn btn-sm <?= ($category === '') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                All Notices
            </a>
            <a href="<?= APP_URL ?>/notices.php?category=Academic" class="btn btn-sm <?= ($category === 'Academic') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                Academic
            </a>
            <a href="<?= APP_URL ?>/notices.php?category=Exam" class="btn btn-sm <?= ($category === 'Exam') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                Exam
            </a>
            <a href="<?= APP_URL ?>/notices.php?category=Event" class="btn btn-sm <?= ($category === 'Event') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                Events
            </a>
            <a href="<?= APP_URL ?>/notices.php?category=General" class="btn btn-sm <?= ($category === 'General') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                General
            </a>
        </div>

        <!-- Notices List -->
        <div class="row g-3">
            <?php if (empty($notices)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center py-5">
                        <i class="bi bi-bell-slash display-4 text-muted mb-3 d-block"></i>
                        <h5>No Notices Found</h5>
                        <p class="text-muted">There are no active notices in this category.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($notices as $notice): ?>
                    <?php
                        $priorityClass = match($notice['priority']) {
                            'urgent' => 'danger',
                            'high' => 'warning',
                            default => 'primary'
                        };
                    ?>
                    <div class="col-12">
                        <div class="card card-sh p-4 border-0 border-start border-5 border-<?= $priorityClass ?>">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <h5 class="fw-bold mb-0"><?= e($notice['title']) ?></h5>
                                    <?php if ($notice['priority'] === 'urgent'): ?>
                                        <span class="badge bg-danger text-uppercase" style="font-size:0.65rem;">Urgent</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="badge bg-secondary-subtle text-secondary me-2"><?= e($notice['category']) ?></span>
                                    <small class="text-muted"><i class="bi bi-calendar3 me-1"></i><?= date('M d, Y', strtotime($notice['published_date'])) ?></small>
                                </div>
                            </div>
                            <p class="text-muted mb-2" style="white-space: pre-line;"><?= e($notice['content']) ?></p>
                            <div class="small text-secondary d-flex align-items-center gap-1">
                                <i class="bi bi-person-circle"></i> Issued by: <?= e($notice['publisher_name'] ?? 'Administration') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

