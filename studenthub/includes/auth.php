<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

/**
 * Check if a user is currently authenticated.
 */
function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current authenticated user details.
 */
function currentUser(): ?array
{
    if (!isAuthenticated()) {
        return null;
    }
    return [
        'id'        => (int) $_SESSION['user_id'],
        'name'      => $_SESSION['name'] ?? 'User',
        'email'     => $_SESSION['email'] ?? '',
        'role'      => $_SESSION['role'] ?? 'guest',
        'student_id'=> $_SESSION['student_id'] ?? null
    ];
}

/**
 * Require user to be authenticated, else redirect to login.
 */
function requireAuth(string $redirect = 'login.php'): void
{
    if (!isAuthenticated()) {
        setFlash('warning', 'Please sign in to access this page.');
        header('Location: ' . APP_URL . '/' . ltrim($redirect, '/'));
        exit;
    }
}

/**
 * Require user to possess a specific role, else throw 403 Forbidden.
 */
function requireRole(array|string $roles): void
{
    requireAuth();

    $allowedRoles = is_array($roles) ? $roles : [$roles];
    $userRole = $_SESSION['role'] ?? '';

    if (!in_array($userRole, $allowedRoles, true)) {
        http_response_code(403);
        echo '<!DOCTYPE html><html lang="en"><head><title>403 Forbidden</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></head><body class="bg-light d-flex align-items-center vh-100"><div class="container text-center"><div class="card p-5 shadow-sm mx-auto" style="max-width:500px;"><h1 class="display-3 text-danger fw-bold">403</h1><h4 class="mb-3">Access Forbidden</h4><p class="text-muted">You do not have administrative permissions to view this resource.</p><a href="' . APP_URL . '" class="btn btn-primary mt-3">Return to Safety</a></div></div></body></html>';
        exit;
    }
}

