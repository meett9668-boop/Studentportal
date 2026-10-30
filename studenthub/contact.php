<?php
declare(strict_types=1);

$pageTitle = 'Contact Us - Student Hub Portal';
$activePage = 'contact';

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$errors = [];
$successMessage = '';

// Practical 7 Server-side Input Validation & Sanitization
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!validateCsrfToken($token)) {
        $errors[] = 'Security verification failed (Invalid CSRF token). Please try again.';
    }

    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $mobile  = trim($_POST['mobile'] ?? '');
    $pincode = trim($_POST['pincode'] ?? '');
    $gender  = trim($_POST['gender'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // Form Validations
    if ($name === '') {
        $errors[] = 'Full Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($mobile === '' || !preg_match('/^[6-9]\d{9}$/', $mobile)) {
        $errors[] = 'A valid 10-digit mobile number starting with 6-9 is required.';
    }
    if ($pincode === '' || !preg_match('/^\d{6}$/', $pincode)) {
        $errors[] = 'A valid 6-digit postal pincode is required.';
    }
    if ($gender === '') {
        $errors[] = 'Please select your gender.';
    }
    if ($address === '') {
        $errors[] = 'Address details are required.';
    }
    if ($message === '') {
        $errors[] = 'Message / Inquiry content cannot be empty.';
    }

    if (empty($errors)) {
        try {
            // Save to MySQL inquiries table
            $stmt = $pdo->prepare(
                'INSERT INTO inquiries (name, email, mobile, pincode, gender, address, message, status) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, "new")'
            );
            $stmt->execute([$name, $email, $mobile, $pincode, $gender, $address, $message]);

            // Practical 7: Fallback file append (CSV format)
            $csvFile = __DIR__ . '/data/inquiries.csv';
            $isNewFile = !file_exists($csvFile);
            $fh = fopen($csvFile, 'a');
            if ($fh) {
                if ($isNewFile) {
                    fputcsv($fh, ['timestamp', 'name', 'email', 'mobile', 'pincode', 'gender', 'address', 'message']);
                }
                fputcsv($fh, [date('Y-m-d H:i:s'), $name, $email, $mobile, $pincode, $gender, $address, $message]);
                fclose($fh);
            }

            setFlash('success', 'Thank you! Your inquiry has been safely recorded. An advisor will contact you soon.');
            header('Location: ' . APP_URL . '/contact.php');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Failed to submit inquiry: ' . $e->getMessage();
        }
    }
}
?>

<main class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5">
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold text-uppercase mb-2">Practical 7 Integration</span>
                <h1 class="fw-bold mb-3">Get in Touch</h1>
                <p class="text-muted mb-4">
                    Have questions regarding student registration, attendance discrepancies, or campus event submissions? Submit an inquiry and our department will assist promptly.
                </p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-primary bg-opacity-10 text-primary rounded-circle">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Campus Location</h6>
                            <p class="text-muted small mb-0">CSPIT Building, CHARUSAT Campus, Changa - 388421</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-success bg-opacity-10 text-success rounded-circle">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Official Email</h6>
                            <p class="text-muted small mb-0">studenthub@cspit.charusat.ac.in</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon bg-info bg-opacity-10 text-info rounded-circle">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">Helpline</h6>
                            <p class="text-muted small mb-0">+91 (02697) 265011 / 265021</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card card-sh p-4 p-md-5 border-0">
                    <h4 class="fw-bold mb-3">Submit Inquiry Form</h4>
                    <p class="text-muted small mb-4">Inputs are validated on both client (HTML5/Regex) and server side (PHP filter sanitization).</p>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= e($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= APP_URL ?>/contact.php">
                        <?= csrfField() ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Meet Tanti" required value="<?= e($_POST['name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="student@example.com" required value="<?= e($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="mobile" class="form-control" placeholder="10-digit number" pattern="[6-9][0-9]{9}" required value="<?= e($_POST['mobile'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Postal Pincode <span class="text-danger">*</span></label>
                                <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" pattern="\d{6}" required value="<?= e($_POST['pincode'] ?? '') ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" <?= (($_POST['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($_POST['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= (($_POST['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">City / Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control" placeholder="e.g. Anand, Gujarat" required value="<?= e($_POST['address'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Inquiry Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Please describe your question or issue in detail..." required><?= e($_POST['message'] ?? '') ?></textarea>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn btn-primary-sh w-100 py-2">
                                    <i class="bi bi-send-fill me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

