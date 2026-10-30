<?php
declare(strict_types=1);

$pageTitle = 'Student Registration - Student Hub Portal';
$activePage = 'register';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/includes/auth.php';

if (isAuthenticated()) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}

$errors = [];

// Practical 9 & 5 Secure Registration Handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $errors[] = 'Security verification failed (Invalid CSRF token). Please try again.';
    }

    $name            = trim($_POST['full_name'] ?? '');
    $studentId       = strtoupper(trim($_POST['student_id'] ?? ''));
    $email           = trim($_POST['email'] ?? '');
    $phone           = trim($_POST['phone'] ?? '');
    $course          = trim($_POST['course'] ?? '');
    $branch          = trim($_POST['branch'] ?? '');
    $year            = trim($_POST['academic_year'] ?? '');
    $gender          = trim($_POST['gender'] ?? '');
    $address         = trim($_POST['address'] ?? '');
    $pincode         = trim($_POST['pincode'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validation checks (Practical 5 & 9)
    if ($name === '' || !preg_match('/^[A-Za-z ]{3,100}$/', $name)) {
        $errors[] = 'Full name must contain at least 3 alphabetic characters.';
    }
    if ($studentId === '' || !preg_match('/^[0-9]{2}[A-Za-z]{2}[0-9]{3,4}$/', $studentId)) {
        $errors[] = 'Student ID format invalid (e.g., 25CS104).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($phone === '' || !preg_match('/^[6-9]\d{9}$/', $phone)) {
        $errors[] = 'Enter a valid 10-digit Indian phone number starting with 6-9.';
    }
    if ($course === '' || $branch === '' || $year === '' || $gender === '') {
        $errors[] = 'Please fill out all academic and demographic fields.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters in length.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Password and Confirmation Password do not match.';
    }

    // Duplicate detection in MySQL (Practical 9)
    if (empty($errors)) {
        try {
            $checkEmail = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $checkEmail->execute([$email]);
            if ($checkEmail->fetch()) {
                $errors[] = 'This email address is already registered. Please sign in or use another email.';
            }

            $checkStudentId = $pdo->prepare('SELECT id FROM students WHERE student_id = ? LIMIT 1');
            $checkStudentId->execute([$studentId]);
            if ($checkStudentId->fetch()) {
                $errors[] = 'Student ID ' . e($studentId) . ' is already registered in the system.';
            }
        } catch (Throwable $e) {
            $errors[] = 'Database verification error: ' . $e->getMessage();
        }
    }

    // Atomic Insertion with Transaction (Practical 9)
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // 1. Insert into users table (Role ID 2 = Student)
            $userStmt = $pdo->prepare(
                'INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, 2, "active")'
            );
            $userStmt->execute([$name, $email, $passwordHash]);
            $newUserId = (int) $pdo->lastInsertId();

            // 2. Insert into students table
            $studentStmt = $pdo->prepare(
                'INSERT INTO students (user_id, student_id, student_name, phone, course, branch, academic_year, gender, address, pincode)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $studentStmt->execute([
                $newUserId, $studentId, $name, $phone, $course, $branch, $year, $gender, $address, $pincode
            ]);
            $newStudentId = (int) $pdo->lastInsertId();

            // 3. Initialize default attendance records (Practical 2 integration)
            $attStmt = $pdo->prepare('INSERT INTO attendance (student_id, subject, total_classes, classes_attended) VALUES (?, ?, 40, 35)');
            $attStmt->execute([$newStudentId, 'Java Programming']);
            $attStmt->execute([$newStudentId, 'HTML & Web Frameworks']);
            $attStmt->execute([$newStudentId, 'Database Management Systems']);

            // 4. Initialize sample academic results (Practical 2 integration)
            $resStmt = $pdo->prepare('INSERT INTO academic_results (student_id, subject, marks_obtained, max_marks, grade) VALUES (?, ?, 85, 100, "AA")');
            $resStmt->execute([$newStudentId, 'Java Programming']);
            $resStmt->execute([$newStudentId, 'HTML & Web Frameworks']);
            $resStmt->execute([$newStudentId, 'Database Management Systems']);

            // Commit transaction
            $pdo->commit();

            logAudit('STUDENT_REGISTRATION', "New student registered: $name ($studentId)", $newUserId);
            setFlash('success', 'Registration successful! You may now sign in with your email and password.');
            header('Location: ' . APP_URL . '/login.php');
            exit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Registration failed due to a database exception: ' . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-sh p-4 p-md-5 border-0">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Practical 5 & 9 Integration</span>
                        <h2 class="fw-bold">Student Registration</h2>
                        <p class="text-muted small">Create your student hub account to track attendance, access results, and enroll in campus events.</p>
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

                    <form method="POST" id="studentRegisterForm" action="<?= APP_URL ?>/register.php">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="e.g. Meet Tanti" required value="<?= e($_POST['full_name'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Student ID <span class="text-danger">*</span></label>
                                <input type="text" name="student_id" class="form-control" placeholder="e.g. 25CS104" required pattern="[0-9]{2}[A-Za-z]{2}[0-9]{3,4}" value="<?= e($_POST['student_id'] ?? '') ?>">
                                <small class="text-muted">Format: 25CS104</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="student@example.com" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" pattern="[6-9][0-9]{9}" required value="<?= e($_POST['phone'] ?? '') ?>">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Course / Degree <span class="text-danger">*</span></label>
                                <select name="course" class="form-select" required>
                                    <option value="B.Tech" <?= (($_POST['course'] ?? '') === 'B.Tech') ? 'selected' : '' ?>>B.Tech</option>
                                    <option value="M.Tech" <?= (($_POST['course'] ?? '') === 'M.Tech') ? 'selected' : '' ?>>M.Tech</option>
                                    <option value="BCA" <?= (($_POST['course'] ?? '') === 'BCA') ? 'selected' : '' ?>>BCA</option>
                                    <option value="MCA" <?= (($_POST['course'] ?? '') === 'MCA') ? 'selected' : '' ?>>MCA</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Branch / Department <span class="text-danger">*</span></label>
                                <select name="branch" class="form-select" required>
                                    <option value="Computer Science & Engineering" <?= (($_POST['branch'] ?? '') === 'Computer Science & Engineering') ? 'selected' : '' ?>>Computer Science & Engineering</option>
                                    <option value="Information Technology" <?= (($_POST['branch'] ?? '') === 'Information Technology') ? 'selected' : '' ?>>Information Technology</option>
                                    <option value="Artificial Intelligence" <?= (($_POST['branch'] ?? '') === 'Artificial Intelligence') ? 'selected' : '' ?>>Artificial Intelligence</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label small fw-semibold">Academic Year <span class="text-danger">*</span></label>
                                <select name="academic_year" class="form-select" required>
                                    <option value="First Year" <?= (($_POST['academic_year'] ?? '') === 'First Year') ? 'selected' : '' ?>>First Year</option>
                                    <option value="Second Year" <?= (($_POST['academic_year'] ?? '') === 'Second Year') ? 'selected' : '' ?>>Second Year</option>
                                    <option value="Third Year" <?= (($_POST['academic_year'] ?? '') === 'Third Year') ? 'selected' : '' ?>>Third Year</option>
                                    <option value="Final Year" <?= (($_POST['academic_year'] ?? '') === 'Final Year') ? 'selected' : '' ?>>Final Year</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= (($_POST['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Postal Pincode</label>
                                <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" pattern="\d{6}" value="<?= e($_POST['pincode'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Residential Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Campus / City address" value="<?= e($_POST['address'] ?? '') ?>">
                            </div>

                            <!-- Practical 5 Live Password Strength & Validation -->
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Password <span class="text-danger">*</span></label>
                                <input type="password" id="reg_password" name="password" class="form-control" placeholder="Minimum 8 characters" required minlength="8">
                                <div class="strength-bar-container">
                                    <div id="regStrengthBar" class="strength-bar"></div>
                                </div>
                                <small id="regStrengthText" class="text-muted d-block mt-1"></small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                                <input type="password" id="reg_confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required minlength="8">
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary-sh w-100 py-2 fw-semibold">
                                    <i class="bi bi-person-check-fill me-1"></i> Complete Registration
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <small class="text-muted">Already registered? <a href="<?= APP_URL ?>/login.php" class="text-primary fw-semibold">Sign in here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

