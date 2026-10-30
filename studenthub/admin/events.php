<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['admin', 'moderator']);

$pageTitle = 'Event Management & Uploads - Practical 12';
$activePage = 'admin_events';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$user = currentUser();

// Practical 12 File Upload & Event Creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_event') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $name        = trim($_POST['event_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category    = trim($_POST['category'] ?? 'Academic');
        $date        = trim($_POST['event_date'] ?? '');
        $time        = trim($_POST['event_time'] ?? '10:00 AM');
        $location    = trim($_POST['location'] ?? '');
        $seats       = (int) ($_POST['max_seats'] ?? 100);

        $posterName = null;

        // Practical 12: Secure Multipart Poster Upload Handling
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['poster']['tmp_name'];
            $fileName    = $_FILES['poster']['name'];
            $fileSize    = $_FILES['poster']['size'];
            $fileType    = mime_content_type($fileTmpPath);

            $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
            $maxFileSize  = 3 * 1024 * 1024; // 3MB

            if (in_array($fileType, $allowedMimes, true) && $fileSize <= $maxFileSize) {
                $ext = match ($fileType) {
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    default      => 'jpg'
                };
                $posterName = uniqid('event_poster_', true) . '.' . $ext;
                $targetDir  = __DIR__ . '/../uploads/events/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0755, true);
                }
                move_uploaded_file($fileTmpPath, $targetDir . $posterName);
            } else {
                setFlash('warning', 'Poster upload ignored: File must be JPEG/PNG/WEBP and under 3MB.');
            }
        }

        try {
            $stmt = $pdo->prepare(
                'INSERT INTO events (event_name, description, category, event_date, event_time, location, max_seats, poster_image, status, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, "upcoming", ?)'
            );
            $stmt->execute([$name, $description, $category, $date, $time, $location, $seats, $posterName, $user['id']]);
            logAudit('EVENT_CREATE', "Created campus event: $name", $user['id']);
            setFlash('success', "Event '$name' created and published.");
        } catch (Throwable $e) {
            setFlash('danger', 'Error adding event: ' . $e->getMessage());
        }
        header('Location: ' . APP_URL . '/admin/events.php');
        exit;
    }
}

// Handle Event Deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_event') {
    $token = $_POST['csrf_token'] ?? '';
    if (validateCsrfToken($token)) {
        $delId = (int) $_POST['event_id'];
        $evStmt = $pdo->prepare('SELECT event_name, poster_image FROM events WHERE id = ?');
        $evStmt->execute([$delId]);
        $ev = $evStmt->fetch();

        if ($ev) {
            if (!empty($ev['poster_image'])) {
                $pPath = __DIR__ . '/../uploads/events/' . $ev['poster_image'];
                if (file_exists($pPath)) {
                    unlink($pPath);
                }
            }
            $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$delId]);
            logAudit('EVENT_DELETE', "Deleted event: " . $ev['event_name'], $user['id']);
            setFlash('success', 'Event removed.');
        }
        header('Location: ' . APP_URL . '/admin/events.php');
        exit;
    }
}

// Fetch events list with registration counts
$eventsStmt = $pdo->query(
    "SELECT e.*, 
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) as reg_count 
     FROM events e 
     ORDER BY e.event_date DESC"
);
$events = $eventsStmt->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 12 Module</span>
                <h2 class="fw-bold mb-0">Campus Event Management (CRUD)</h2>
                <p class="text-muted small mb-0">Create events, upload promotional poster banners, and monitor student enrollments.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <button type="button" class="btn btn-primary-sh btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal">
                    <i class="bi bi-calendar-plus me-1"></i> Add New Event
                </button>
                <a href="<?= APP_URL ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Events Table (Practical 12) -->
        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Poster</th>
                            <th>Event Title</th>
                            <th>Category</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($events)): ?>
                            <tr><td colspan="8" class="text-center py-4 text-muted">No events currently listed.</td></tr>
                        <?php else: ?>
                            <?php foreach ($events as $ev): ?>
                                <tr>
                                    <td style="width: 70px;">
                                        <?php if (!empty($ev['poster_image']) && file_exists(__DIR__ . '/../uploads/events/' . $ev['poster_image'])): ?>
                                            <img src="<?= APP_URL ?>/uploads/events/<?= e($ev['poster_image']) ?>" class="rounded" style="width: 50px; height: 50px; object-fit: cover;" alt="Poster">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($ev['event_name']) ?></div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;"><?= e($ev['description']) ?></small>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary"><?= e($ev['category']) ?></span></td>
                                    <td>
                                        <div class="fw-semibold"><?= date('M d, Y', strtotime($ev['event_date'])) ?></div>
                                        <small class="text-muted"><?= e($ev['event_time']) ?></small>
                                    </td>
                                    <td><?= e($ev['location']) ?></td>
                                    <td>
                                        <strong><?= (int) $ev['reg_count'] ?></strong> / <?= (int) $ev['max_seats'] ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= ($ev['status'] === 'upcoming') ? 'success' : 'secondary' ?>-subtle text-<?= ($ev['status'] === 'upcoming') ? 'success' : 'secondary' ?>">
                                            <?= e(ucfirst($ev['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete event <?= e($ev['event_name']) ?>?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="action" value="delete_event">
                                            <input type="hidden" name="event_id" value="<?= $ev['id'] ?>">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Event">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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

<!-- Practical 12 Add Event Modal with Multipart File Upload -->
<div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-sh border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="addEventModalLabel"><i class="bi bi-calendar-plus me-2 text-primary"></i>Create Campus Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/events.php" enctype="multipart/form-data">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="create_event">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">Event Name <span class="text-danger">*</span></label>
                            <input type="text" name="event_name" class="form-control" required placeholder="e.g. AI & Robotics Symposium">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category</label>
                            <select name="category" class="form-select">
                                <option value="Technical">Technical</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Exhibition">Exhibition</option>
                                <option value="Cultural">Cultural</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Event Date <span class="text-danger">*</span></label>
                            <input type="date" name="event_date" class="form-control" required value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Event Time</label>
                            <input type="text" name="event_time" class="form-control" value="10:00 AM">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Max Participant Seats</label>
                            <input type="number" name="max_seats" class="form-control" value="100" min="10" max="1000">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Campus Location / Venue <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" required placeholder="e.g. CSPIT Seminar Hall 1">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Event Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Brief outline and schedule of the event..."></textarea>
                        </div>
                        <!-- Practical 12 Poster Upload Input -->
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Event Poster Image (JPG/PNG/WEBP, Max 3MB)</label>
                            <input type="file" name="poster" class="form-control" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Upload an event banner that will be safely validated and stored in /uploads/events/</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-sh">Publish Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
