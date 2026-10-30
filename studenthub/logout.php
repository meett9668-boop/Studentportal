<?php
declare(strict_types=1);

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/audit.php';
require_once __DIR__ . '/includes/auth.php';

if (isAuthenticated()) {
    logAudit('USER_LOGOUT', 'User signed out', (int) $_SESSION['user_id']);
}

// Clear all session variables
$_SESSION = [];

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session
session_destroy();

// Redirect to home with flash message
session_start();
setFlash('info', 'You have been successfully signed out.');
header('Location: ' . APP_URL . '/index.php');
exit;

