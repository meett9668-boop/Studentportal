<?php
declare(strict_types=1);

$pageTitle = 'Campus Events - Student Hub Portal';
$activePage = 'events';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Filter by category or search
$category = trim($_GET['category'] ?? '');
$search = trim($_GET['search'] ?? '');

$query = "SELECT e.*, 
            (SELECT COUNT(*) FROM registrations r WHERE r.event_id = e.id) AS registered_count
          FROM events e WHERE 1=1";
$params = [];

if ($category !== '') {
    $query .= " AND e.category = ?";
    $params[] = $category;
}

if ($search !== '') {
    $query .= " AND (e.event_name LIKE ? OR e.description LIKE ? OR e.location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY e.event_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll();

// Distinct categories for filter pills
$catStmt = $pdo->query("SELECT DISTINCT category FROM events WHERE category IS NOT NULL");
$categories = $catStmt->fetchAll(PDO::FETCH_COLUMN);

// If student is logged in, find their registered events
$myRegistrations = [];
if (isAuthenticated() && currentUser()['role'] === 'student') {
    $userId = currentUser()['id'];
    $stuStmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
    $stuStmt->execute([$userId]);
    $studentId = $stuStmt->fetchColumn();

    if ($studentId) {
        $regStmt = $pdo->prepare("SELECT event_id FROM registrations WHERE student_id = ?");
        $regStmt->execute([$studentId]);
        $myRegistrations = $regStmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>

<main class="py-5">
    <div class="container">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Campus Community</span>
                <h1 class="fw-bold">Campus Events & Workshops</h1>
                <p class="text-muted mb-0">Discover technical conferences, hackathons, and cultural activities.</p>
            </div>
            <div class="col-md-6 mt-3 mt-md-0">
                <form method="GET" class="d-flex gap-2 justify-content-md-end">
                    <input type="text" name="search" class="form-control" placeholder="Search events..." value="<?= e($search) ?>" style="max-width: 250px;">
                    <button type="submit" class="btn btn-primary-sh px-3"><i class="bi bi-search"></i></button>
                    <?php if ($search !== '' || $category !== ''): ?>
                        <a href="<?= APP_URL ?>/events.php" class="btn btn-outline-secondary">Reset</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Category Filters -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <a href="<?= APP_URL ?>/events.php<?= $search ? '?search=' . urlencode($search) : '' ?>" 
               class="btn btn-sm <?= ($category === '') ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                All Categories
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= APP_URL ?>/events.php?category=<?= urlencode($cat) ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
                   class="btn btn-sm <?= ($category === $cat) ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill px-3">
                    <?= e($cat) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Events Grid -->
        <div class="row g-4">
            <?php if (empty($events)): ?>
                <div class="col-12">
                    <div class="alert alert-secondary text-center py-5">
                        <i class="bi bi-calendar-x display-4 text-muted mb-3 d-block"></i>
                        <h5>No Events Found</h5>
                        <p class="text-muted">There are no events matching your criteria.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <?php
                        $isRegistered = in_array($event['id'], $myRegistrations, false);
                        $seatsLeft = max(0, (int)$event['max_seats'] - (int)$event['registered_count']);
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card card-sh h-100 border-0 overflow-hidden d-flex flex-column">
                            <?php if (!empty($event['poster_image']) && file_exists(__DIR__ . '/uploads/events/' . $event['poster_image'])): ?>
                                <img src="<?= APP_URL ?>/uploads/events/<?= e($event['poster_image']) ?>" class="card-img-top" alt="<?= e($event['event_name']) ?>" style="height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 140px;">
                                    <i class="bi bi-calendar-event display-4 text-primary opacity-50"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body p-4 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-primary-subtle text-primary fw-medium"><?= e($event['category']) ?></span>
                                    <span class="badge <?= ($seatsLeft > 0) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?>">
                                        <?= $seatsLeft ?> seats left
                                    </span>
                                </div>
                                
                                <h5 class="fw-bold mb-2"><?= e($event['event_name']) ?></h5>
                                <p class="text-muted small mb-3 flex-grow-1">
                                    <?= e($event['description'] ?? 'Join us for this exciting campus event.') ?>
                                </p>

                                <div class="small text-secondary mb-3">
                                    <div class="mb-1"><i class="bi bi-calendar3 me-2 text-primary"></i><?= date('F d, Y', strtotime($event['event_date'])) ?></div>
                                    <div class="mb-1"><i class="bi bi-clock me-2 text-primary"></i><?= e($event['event_time']) ?></div>
                                    <div><i class="bi bi-geo-alt me-2 text-primary"></i><?= e($event['location']) ?></div>
                                </div>

                                <div class="mt-auto pt-2 border-top">
                                    <?php if (!isAuthenticated()): ?>
                                        <a href="<?= APP_URL ?>/login.php" class="btn btn-outline-primary btn-sm w-100">
                                            Sign in to Register
                                        </a>
                                    <?php elseif (currentUser()['role'] === 'student'): ?>
                                        <?php if ($isRegistered): ?>
                                            <button class="btn btn-success btn-sm w-100" disabled>
                                                <i class="bi bi-check-circle-fill me-1"></i> Already Enrolled
                                            </button>
                                        <?php elseif ($seatsLeft <= 0): ?>
                                            <button class="btn btn-secondary btn-sm w-100" disabled>
                                                Event Full
                                            </button>
                                        <?php else: ?>
                                            <!-- Practical 13 Async Registration -->
                                            <button type="button" class="btn btn-primary-sh btn-sm w-100 btn-register-event" data-event-id="<?= $event['id'] ?>">
                                                <i class="bi bi-ticket-perforated me-1"></i> Register Now
                                            </button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="<?= APP_URL ?>/admin/events.php" class="btn btn-outline-secondary btn-sm w-100">
                                            Manage in Admin
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
// Practical 13 - Asynchronous Event Registration using Fetch API
document.addEventListener("DOMContentLoaded", function () {
    const regButtons = document.querySelectorAll(".btn-register-event");
    regButtons.forEach(btn => {
        btn.addEventListener("click", async function () {
            const eventId = this.dataset.eventId;
            if (!confirm("Confirm your registration for this event?")) return;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Registering...';

            try {
                const response = await fetch("<?= APP_URL ?>/api/events.php?action=register", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        event_id: eventId,
                        csrf_token: "<?= getCsrfToken() ?>"
                    })
                });

                const data = await response.json();
                if (data.success) {
                    this.className = "btn btn-success btn-sm w-100";
                    this.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> Enrolled Successfully!';
                } else {
                    alert(data.message || "Failed to register.");
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-ticket-perforated me-1"></i> Register Now';
                }
            } catch (err) {
                alert("Network communication error. Please try again.");
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-ticket-perforated me-1"></i> Register Now';
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

