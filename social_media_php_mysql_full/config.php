<?php
// config.php - Database configuration and helper functions

// Show all errors during development (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'social_media';

// Create a new MySQLi connection
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($mysqli->connect_errno) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Escape output safely (prevent XSS)
// Handle null or non-string values safely
function e($s): string {
    return htmlspecialchars((string)($s ?? ''), ENT_QUOTES, 'UTF-8');
}

// Get current logged-in user
function current_user(): ?array {
    global $mysqli;
    if (!empty($_SESSION['user_id'])) {
        $id = (int)$_SESSION['user_id'];
        $res = $mysqli->prepare('SELECT id, username, display_name, avatar FROM users WHERE id = ? LIMIT 1');
        if ($res) {
            $res->bind_param('i', $id);
            $res->execute();
            $result = $res->get_result();
            $user = $result->fetch_assoc();
            $res->close();
            return $user ?: null;
        }
    }
    return null;
}

// CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function csrf_token(): string {
    return $_SESSION['csrf_token'];
}

function check_csrf(string $t): bool {
    return hash_equals($_SESSION['csrf_token'], $t);
}
