<?php
declare(strict_types=1);

/**
 * Common Helper Functions for Student Hub
 */

// Start session securely if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

/**
 * Escape HTML output to prevent Cross-Site Scripting (XSS).
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token.
 */
function getCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Render CSRF hidden input tag.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(getCsrfToken()) . '">';
}

/**
 * Validate CSRF token from POST request.
 */
function validateCsrfToken(?string $token): bool
{
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set flash message for alert banners.
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Get and clear flash message.
 */
function getFlash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Render flash alert banner if set.
 */
function renderFlash(): void
{
    $flash = getFlash();
    if ($flash) {
        $alertType = $flash['type'] === 'error' ? 'danger' : $flash['type'];
        echo '<div class="alert alert-' . e($alertType) . ' alert-dismissible fade show my-3 shadow-sm" role="alert">';
        echo e($flash['message']);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
}

