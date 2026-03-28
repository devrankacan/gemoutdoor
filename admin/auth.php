<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireAuth(): void {
    if (empty($_SESSION['hm_admin'])) {
        header('Location: login.php');
        exit;
    }
}
