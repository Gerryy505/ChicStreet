<?php
if (session_status() == PHP_SESSION_NONE) session_start();

$jumlah_cart = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $jumlah_cart += $item['qty'];
    }
}
?>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
  <div class="container-fluid px-4">

    <!-- LOGO -->
    <a class="navbar-brand d-flex align-items-center" href="index.php?page=home">
      <img src="Image/accusoft.png" alt="Logo" style="width:40px;height:40px;object-fit:contain;">
      <span class="ms-2 fw-bold text-white fs-4">Chic Street</span>
    </a>

    <!-- TOGGLE MOBILE -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
      data-bs-target="#navbarMain" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- MENU -->
    <div class="collapse navbar-collapse justify-content-end" id="navbarMain">
      <ul class="navbar-nav align-items-lg-center gap-lg-1">
        <li class="nav-item">
          <a class="nav-link fw-semibold" href="index.php?page=home">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold" href="index.php?page=shop">EKSKLUSIF</a>
        </li>
        <!-- DROPDOWN KATEGORI -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button"
            data-bs-toggle="dropdown">KATEGORI</a>
          <ul class="dropdown-menu border-0 shadow">
            <?php
            require_once 'config/Database.php';
            try {
                $pdo = getDB();
                $kats = $pdo->query("SELECT id, nama FROM kategori ORDER BY nama")->fetchAll();
                foreach ($kats as $k) {
                    echo "<li><a class='dropdown-item' href='index.php?page=shop&kategori={$k['id']}'>{$k['nama']}</a></li>";
                }
            } catch (Exception $e) {
                echo "<li><a class='dropdown-item' href='#'>Semua</a></li>";
            }
            ?>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-semibold" href="#">BANTUAN</a>
        </li>
      </ul>

      <!-- SEARCH -->
      <form class="d-flex ms-lg-3 me-3 mt-3 mt-lg-0" action="index.php" method="GET">
        <input type="hidden" name="page" value="shop">
        <input class="form-control form-control-sm rounded-pill px-3 me-2" type="search"
          name="q" placeholder="Cari produk..." aria-label="Search">
        <button class="btn btn-outline-light btn-sm rounded-pill px-3" type="submit">Cari</button>
      </form>

      <!-- CART -->
      <a href="index.php?page=keranjang" class="text-decoration-none mt-3 mt-lg-0">
        <div class="position-relative d-flex align-items-center">
          <i class="bi bi-bag-heart text-white fs-3"></i>
          <?php if ($jumlah_cart > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $jumlah_cart ?>
          </span>
          <?php endif; ?>
        </div>
      </a>
    </div>

  </div>
</nav>
