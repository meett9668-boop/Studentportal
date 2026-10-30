<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/audit.php';

requireRole(['student', 'admin', 'moderator']);

$pageTitle = 'My Profile - Student Hub';
$activePage = 'student_profile';

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';

$userId = currentUser()['id'];
$errors = [];

// Fetch student profile record
$stmt = $pdo->prepare('SELECT s.*, u.email FROM students s JOIN users u ON s.user_id = u.id WHERE s.user_id = ?');
$stmt->execute([$userId]);
$student = $stmt->fetch();

// If user does not have a linked student row yet (e.g. admin profile)
if (!$student) {
    $userStmt = $pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $userStmt->execute([$userId]);
    $uRow = $userStmt->fetch();
    $student = [
        'student_id' => 'ADMIN-' . $userId,
        'student_name' => $uRow['name'],
        'email' => $uRow['email'],
        'phone' => 'N/A',
        'course' => 'Administration',
        'branch' => 'IT / Systems',
        'academic_year' => 'Staff',
        'gender' => 'Other',
        'address' => 'University Headquarters',
        'pincode' => '388421',
        'created_at' => date('Y-m-d H:i:s')
    ];
}

// Handle Profile Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $errors[] = 'CSRF validation failed. Please try again.';
    } else {
        $name    = trim($_POST['student_name'] ?? '');
        $phone   = trim($_POST['phone'] ?? '');
        $gender  = trim($_POST['gender'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');

        if ($name === '') {
            $errors[] = 'Full name cannot be blank.';
        }
        if ($phone !== '' && !preg_match('/^[6-9]\d{9}$/', $phone)) {
            $errors[] = 'Phone number must be a 10-digit number starting with 6-9.';
        }
        if ($pincode !== '' && !preg_match('/^\d{6}$/', $pincode)) {
            $errors[] = 'Postal Pincode must be 6 digits.';
        }

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();

                // Update users table name
                $updUser = $pdo->prepare('UPDATE users SET name = ? WHERE id = ?');
                $updUser->execute([$name, $userId]);
                $_SESSION['name'] = $name;

                // Update students table details if student record exists
                $updStu = $pdo->prepare(
                    'UPDATE students 
                     SET student_name = ?, phone = ?, gender = ?, address = ?, pincode = ? 
                     WHERE user_id = ?'
                );
                $updStu->execute([$name, $phone, $gender, $address, $pincode, $userId]);

                $pdo->commit();

                logAudit('PROFILE_UPDATE', "User updated profile information", $userId);
                setFlash('success', 'Profile updated successfully!');
                header('Location: ' . APP_URL . '/student/profile.php');
                exit;
            } catch (Throwable $e) {
                $pdo->rollBack();
                $errors[] = 'Update failed: ' . $e->getMessage();
            }
        }
    }
}
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-sh p-4 p-md-5 border-0">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <div>
                            <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-1">Account & Profile</span>
                            <h3 class="fw-bold mb-0">Student Profile</h3>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary text-uppercase"><?= e(currentUser()['role']) ?></span>
                        </div>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= e($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= APP_URL ?>/student/profile.php">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <!-- Non-editable academic identity -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Student ID</label>
                                <input type="text" class="form-control bg-light" value="<?= e($student['student_id']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Email Address (Login Identity)</label>
                                <input type="email" class="form-control bg-light" value="<?= e($student['email']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Degree / Course</label>
                                <input type="text" class="form-control bg-light" value="<?= e($student['course']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Branch / Discipline</label>
                                <input type="text" class="form-control bg-light" value="<?= e($student['branch']) ?>" readonly>
                            </div>

                            <!-- Editable personal details -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="student_name" class="form-control" value="<?= e($student['student_name']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Contact Phone Number</label>
                                <input type="tel" name="phone" class="form-control" value="<?= e($student['phone']) ?>" pattern="[6-9][0-9]{9}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="Male" <?= ($student['gender'] === 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= ($student['gender'] === 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= ($student['gender'] === 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Postal Pincode</label>
                                <input type="text" name="pincode" class="form-control" value="<?= e($student['pincode']) ?>" pattern="\d{6}">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Residential Address</label>
                                <textarea name="address" class="form-control" rows="3"><?= e($student['address']) ?></textarea>
                            </div>

                            <div class="col-12 mt-4 d-flex justify-content-between align-items-center">
                                <a href="<?= APP_URL ?>/student/dashboard.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                                </a>
                                <button type="submit" class="btn btn-primary-sh px-4">
                                    <i class="bi bi-save me-1"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
