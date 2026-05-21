<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <?php include "komponen/header.php"; ?>
</head>
<body>

  <!-- NAVBAR -->
  <?php include "komponen/navbar.php"; ?>

  <!-- KONTEN HALAMAN -->
  <main>
    <?php include "route/web.php"; ?>
  </main>

  <!-- FOOTER -->
  <?php include "komponen/footer.php"; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Bootstrap Icons CDN sudah di header -->
</body>
</html>
