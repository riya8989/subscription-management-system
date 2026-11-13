<?php
/**
 * Include this at the top of every protected admin page.
 * Redirects to login if no active session exists.
 */
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: /auth/login.php');
    exit;
}
