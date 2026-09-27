<?php
// config/auth.php

// Start session only if it is not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION["user"]);
}

/**
 * Require user to be logged in
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        header("Location: /fasttaxis_app/public/login.php");
        exit;
    }
}

/**
 * Require specific role (DIRECTOR, STAFF, DRIVER, OWNER)
 */
function requireRole(string $role): void {

    // Make sure user is logged in first
    requireLogin();

    // Check role
    if (!isset($_SESSION["user"]["role"]) || $_SESSION["user"]["role"] !== $role) {
        http_response_code(403);
        echo "<h2>Access Denied</h2>";
        echo "<p>You do not have permission to access this page.</p>";
        exit;
    }
}

/**
 * Logout function
 */
function logout(): void {
    session_unset();
    session_destroy();
    header("Location: /fasttaxis_app/public/login.php");
    exit;
}
?>