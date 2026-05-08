<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUserRole() {
    return $_SESSION['role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}

function requireRole($role) {
    requireLogin();

    if (currentUserRole() !== $role) {
        header('Location: /index.php');
        exit;
    }
}

function redirectByRole($role) {
    if ($role === 'admin') {
        header('Location: /admin/dashboard.php');
    } elseif ($role === 'vendor') {
        header('Location: /vendor/dashboard.php');
    } elseif ($role === 'customer') {
        header('Location: /customer/dashboard.php');
    } elseif ($role === 'delivery') {
        header('Location: /delivery/dashboard.php');
    } else {
        header('Location: /index.php');
    }

    exit;
}
?>
