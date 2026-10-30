<footer class="footer-studenthub mt-auto">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white fw-bold d-flex align-items-center gap-2 mb-3">
                    <i class="bi bi-mortarboard-fill text-info"></i> Student Hub
                </h5>
                <p class="small text-secondary mb-3">
                    A comprehensive academic and student lifecycle portal engineered for ITUE203 Web Development Frameworks Practical 15, integrating Practicals 1 through 14.
                </p>
                <div class="d-flex gap-3 text-secondary">
                    <span class="badge bg-dark border border-secondary text-light">PHP 8.2</span>
                    <span class="badge bg-dark border border-secondary text-light">MariaDB / MySQL</span>
                    <span class="badge bg-dark border border-secondary text-light">Bootstrap 5.3</span>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white text-uppercase small fw-bold mb-3">Public Portal</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="<?= APP_URL ?>/index.php">Home Page</a></li>
                    <li><a href="<?= APP_URL ?>/about.php">About & Sitemap</a></li>
                    <li><a href="<?= APP_URL ?>/academics.php">Academics</a></li>
                    <li><a href="<?= APP_URL ?>/events.php">Campus Events</a></li>
                    <li><a href="<?= APP_URL ?>/notices.php">Announcements</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white text-uppercase small fw-bold mb-3">Student & Support</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="<?= APP_URL ?>/support.php">Help & FAQs (Fetch API)</a></li>
                    <li><a href="<?= APP_URL ?>/contact.php">Contact Us</a></li>
                    <li><a href="<?= APP_URL ?>/login.php">Student / Admin Sign In</a></li>
                    <li><a href="<?= APP_URL ?>/register.php">New Student Registration</a></li>
                </ul>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white text-uppercase small fw-bold mb-3">Student Hub Identity</h6>
                <p class="small text-secondary mb-1">Developer: <strong>Meet Tanti</strong></p>
                <p class="small text-secondary mb-1">Student ID: <strong>25CS104</strong></p>
                <p class="small text-secondary mb-3">Course: <strong>ITUE203 - WDF</strong></p>
                <p class="small text-muted">&copy; <?= date('Y') ?> Student Hub Portal. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Student Hub App JS -->
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>

