<?php
// Sertakan file ini di setiap halaman admin
if (session_status() == PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}
