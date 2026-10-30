<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['admin', 'moderator']);

$pageTitle = 'Student Directory Management - Practical 11';
$activePage = 'admin_students';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
$user = currentUser();
$isAdmin = ($user['role'] === 'admin');

// Handle Student Creation (CRUD - Create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_student') {
    if (!$isAdmin) {
        setFlash('danger', 'Moderators do not have permission to create student accounts.');
    } else {
        $token = $_POST['csrf_token'] ?? '';
        if (validateCsrfToken($token)) {
            $name      = trim($_POST['student_name'] ?? '');
            $studentId = strtoupper(trim($_POST['student_id'] ?? ''));
            $email     = trim($_POST['email'] ?? '');
            $phone     = trim($_POST['phone'] ?? '');
            $course    = trim($_POST['course'] ?? '');
            $branch    = trim($_POST['branch'] ?? '');
            $year      = trim($_POST['academic_year'] ?? '');
            $gender    = trim($_POST['gender'] ?? 'Male');

            try {
                $pdo->beginTransaction();
                // Create user login
                $hash = password_hash('student123', PASSWORD_BCRYPT);
                $uStmt = $pdo->prepare('INSERT INTO users (name, email, password, role_id) VALUES (?, ?, ?, 2)');
                $uStmt->execute([$name, $email, $hash]);
                $uId = (int) $pdo->lastInsertId();

                // Create student profile
                $sStmt = $pdo->prepare(
                    'INSERT INTO students (user_id, student_id, student_name, phone, course, branch, academic_year, gender) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $sStmt->execute([$uId, $studentId, $name, $phone, $course, $branch, $year, $gender]);
                $sId = (int) $pdo->lastInsertId();

                // Default attendance
                $pdo->prepare('INSERT INTO attendance (student_id, subject, total_classes, classes_attended) VALUES (?, "Java Programming", 40, 36)')->execute([$sId]);
                $pdo->prepare('INSERT INTO attendance (student_id, subject, total_classes, classes_attended) VALUES (?, "HTML & Web Frameworks", 38, 34)')->execute([$sId]);

                $pdo->commit();
                logAudit('STUDENT_CREATE', "Admin created student: $name ($studentId)", $user['id']);
                setFlash('success', "Student $name ($studentId) created successfully with default password 'student123'.");
            } catch (Throwable $e) {
                $pdo->rollBack();
                setFlash('danger', 'Error adding student: ' . $e->getMessage());
            }
            header('Location: ' . APP_URL . '/admin/students.php');
            exit;
        }
    }
}

// Handle Student Deletion (CRUD - Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_student') {
    if (!$isAdmin) {
        setFlash('danger', 'Moderators do not have permission to delete student accounts.');
    } else {
        $token = $_POST['csrf_token'] ?? '';
        if (validateCsrfToken($token)) {
            $delId = (int) $_POST['student_id'];
            $stuInfo = $pdo->prepare('SELECT student_name, user_id FROM students WHERE id = ?');
            $stuInfo->execute([$delId]);
            $sData = $stuInfo->fetch();

            if ($sData) {
                // Delete user which cascades or deletes student
                $delStmt = $pdo->prepare('DELETE FROM students WHERE id = ?');
                $delStmt->execute([$delId]);
                if ($sData['user_id']) {
                    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$sData['user_id']]);
                }
                logAudit('STUDENT_DELETE', "Admin deleted student record: " . $sData['student_name'], $user['id']);
                setFlash('success', "Student record removed.");
            }
            header('Location: ' . APP_URL . '/admin/students.php');
            exit;
        }
    }
}

// Search and Filtering (Practical 11)
$search = trim($_GET['search'] ?? '');
$branchFilter = trim($_GET['branch'] ?? '');

$query = "SELECT s.*, u.email, u.status as account_status 
          FROM students s 
          LEFT JOIN users u ON s.user_id = u.id 
          WHERE 1=1";
$params = [];

if ($search !== '') {
    $query .= " AND (s.student_name LIKE ? OR s.student_id LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($branchFilter !== '') {
    $query .= " AND s.branch = ?";
    $params[] = $branchFilter;
}

$query .= " ORDER BY s.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll();
?>

<main class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Practical 11 Module</span>
                <h2 class="fw-bold mb-0">Student Record Management (CRUD)</h2>
                <p class="text-muted small mb-0">List, search, filter, enroll, and manage enrolled university students.</p>
            </div>
            <div class="mt-3 mt-md-0 d-flex gap-2">
                <?php if ($isAdmin): ?>
                    <button type="button" class="btn btn-primary-sh btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Add New Student
                    </button>
                <?php endif; ?>
                <a href="<?= APP_URL ?>/admin/dashboard.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Practical 11 Search & Filters -->
        <div class="card card-sh p-3 mb-4 border-0">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, ID or email..." value="<?= e($search) ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <select name="branch" class="form-select">
                        <option value="">All Branches</option>
                        <option value="Computer Science & Engineering" <?= ($branchFilter === 'Computer Science & Engineering') ? 'selected' : '' ?>>Computer Science & Engineering</option>
                        <option value="Information Technology" <?= ($branchFilter === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                        <option value="Computer Engineering" <?= ($branchFilter === 'Computer Engineering') ? 'selected' : '' ?>>Computer Engineering</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-sh btn-sm w-100">Filter</button>
                    <?php if ($search !== '' || $branchFilter !== ''): ?>
                        <a href="<?= APP_URL ?>/admin/students.php" class="btn btn-outline-secondary btn-sm">Reset</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Student Records Table (Practical 11) -->
        <div class="card card-sh border-0 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-sh align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Course & Branch</th>
                            <th>Academic Year</th>
                            <th>Contact</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">No student records match the search filter.</td></tr>
                        <?php else: ?>
                            <?php foreach ($students as $stu): ?>
                                <tr>
                                    <td><span class="badge bg-light text-primary font-monospace fw-bold"><?= e($stu['student_id']) ?></span></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= e($stu['student_name']) ?></div>
                                        <small class="text-muted"><?= e($stu['gender']) ?></small>
                                    </td>
                                    <td><?= e($stu['email'] ?? 'Not Linked') ?></td>
                                    <td>
                                        <div class="small fw-semibold"><?= e($stu['course']) ?></div>
                                        <small class="text-muted"><?= e($stu['branch']) ?></small>
                                    </td>
                                    <td><span class="badge bg-secondary-subtle text-secondary"><?= e($stu['academic_year']) ?></span></td>
                                    <td><?= e($stu['phone'] ?? 'N/A') ?></td>
                                    <td class="text-center">
                                        <?php if ($isAdmin): ?>
                                            <form method="POST" class="d-inline" onsubmit="return confirm('Delete student <?= e($stu['student_name']) ?>? This action is permanent.');">
                                                <?= csrfField() ?>
                                                <input type="hidden" name="action" value="delete_student">
                                                <input type="hidden" name="student_id" value="<?= $stu['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Student Record">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Read Only</span>
                                        <?php endif; ?>
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

<!-- Add Student Modal -->
<?php if ($isAdmin): ?>
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content card-sh border-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="addStudentModalLabel"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Enroll New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="<?= APP_URL ?>/admin/students.php">
                <div class="modal-body">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="create_student">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="student_name" class="form-control" required placeholder="e.g. John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Student ID <span class="text-danger">*</span></label>
                            <input type="text" name="student_id" class="form-control" required placeholder="e.g. 25CS107">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="student@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Mobile Phone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="10-digit number">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Degree Course</label>
                            <select name="course" class="form-select">
                                <option value="B.Tech">B.Tech</option>
                                <option value="M.Tech">M.Tech</option>
                                <option value="BCA">BCA</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Branch</label>
                            <select name="branch" class="form-select">
                                <option value="Computer Science & Engineering">Computer Science & Engineering</option>
                                <option value="Information Technology">Information Technology</option>
                                <option value="Computer Engineering">Computer Engineering</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Academic Year</label>
                            <select name="academic_year" class="form-select">
                                <option value="First Year">First Year</option>
                                <option value="Second Year" selected>Second Year</option>
                                <option value="Third Year">Third Year</option>
                                <option value="Final Year">Final Year</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-sh">Save Student Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
