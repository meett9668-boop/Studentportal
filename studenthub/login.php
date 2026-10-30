<?php
declare(strict_types=1);

$pageTitle = 'Sign In - Student Hub Portal';
$activePage = 'login';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/includes/auth.php';

// If already signed in, redirect according to role
if (isAuthenticated()) {
    $role = $_SESSION['role'] ?? 'student';
    if ($role === 'admin' || $role === 'moderator') {
        header('Location: ' . APP_URL . '/admin/dashboard.php');
    } else {
        header('Location: ' . APP_URL . '/student/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $error = 'Session security token invalid. Please refresh and try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Please enter both your email address and password.';
        } else {
            try {
                $stmt = $pdo->prepare(
                    "SELECT u.id, u.name, u.email, u.password, u.status, r.role_name, s.student_id, s.id as student_record_id
                     FROM users u
                     JOIN roles r ON u.role_id = r.id
                     LEFT JOIN students s ON u.id = s.user_id
                     WHERE u.email = ?
                     LIMIT 1"
                );
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] !== 'active') {
                        $error = 'Your account has been deactivated. Please contact an administrator.';
                    } else {
                        // Prevent Session Fixation
                        session_regenerate_id(true);

                        $_SESSION['user_id'] = (int) $user['id'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role_name'];
                        $_SESSION['student_id'] = $user['student_id'] ?? null;
                        $_SESSION['student_record_id'] = $user['student_record_id'] ?? null;

                        // Audit Trail (Practical 14)
                        logAudit('USER_LOGIN', 'User successfully logged in via email: ' . $email, (int) $user['id']);

                        // Role-Based Redirection
                        if ($user['role_name'] === 'admin' || $user['role_name'] === 'moderator') {
                            header('Location: ' . APP_URL . '/admin/dashboard.php');
                        } else {
                            header('Location: ' . APP_URL . '/student/dashboard.php');
                        }
                        exit;
                    }
                } else {
                    $error = 'Invalid email address or password.';
                    logAudit('AUTH_FAILED', 'Failed login attempt for email: ' . $email);
                }
            } catch (Throwable $e) {
                $error = 'A database error occurred. Please try again.';
                error_log('Login Error: ' . $e->getMessage());
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="py-5 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-sh p-4 p-md-5 border-0">
                    <div class="text-center mb-4">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <h3 class="fw-bold">Sign In to Student Hub</h3>
                        <p class="text-muted small">Access your academic records, attendance, and administrative controls.</p>
                    </div>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="bi bi-exclamation-circle-fill me-2"></i><?= e($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= APP_URL ?>/login.php">
                        <?= csrfField() ?>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" placeholder="e.g. meet@studenthub.local" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" class="form-control border-start-0" placeholder="Enter your password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary-sh w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                        </button>
                    </form>

                    <!-- Pre-configured test accounts helper for evaluators -->
                    <div class="mt-4 pt-3 border-top">
                        <div class="alert alert-light border small text-muted mb-0">
                            <strong><i class="bi bi-info-circle me-1"></i> Quick Test Accounts:</strong><br>
                            &bull; <strong>Admin:</strong> <code>admin@studenthub.local</code> / <code>admin123</code><br>
                            &bull; <strong>Student:</strong> <code>meet@studenthub.local</code> / <code>student123</code><br>
                            &bull; <strong>Moderator:</strong> <code>moderator@studenthub.local</code> / <code>mod123</code>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <small class="text-muted">Don't have an account? <a href="<?= APP_URL ?>/register.php" class="text-primary fw-semibold">Register here</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

