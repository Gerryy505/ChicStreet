<?php
if (session_status() == PHP_SESSION_NONE) session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {

    // -------------------------------------------------------
    case 'home':
        include "page/user/home.php";
        break;

    // -------------------------------------------------------
    case 'shop':
        include "page/user/shopall.php";
        break;

    // -------------------------------------------------------
    case 'detail':
        include "page/user/detail.php";
        break;

    // -------------------------------------------------------
    case 'keranjang':
        include "page/user/keranjang.php";
        break;

    // -------------------------------------------------------
    default:
        include "page/user/home.php";
        break;
}
?>
