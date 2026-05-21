<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Hapus item
if (isset($_GET['hapus'])) {
    $idx = (int)$_GET['hapus'];
    if (isset($_SESSION['cart'][$idx])) {
        unset($_SESSION['cart'][$idx]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
        echo "<script>alert('Produk berhasil dihapus.');window.location.href='index.php?page=keranjang';</script>";
        exit;
    }
}
?>

<div class="container py-5">
  <h2 class="fw-bold mb-4"><i class="bi bi-bag-heart me-2"></i>Keranjang Belanja</h2>

  <?php if (!empty($_SESSION['cart'])): ?>
    <?php $total = 0; ?>

    <?php foreach ($_SESSION['cart'] as $idx => $item): ?>
      <?php $subtotal = $item['harga'] * $item['qty']; $total += $subtotal; ?>
      <div class="card mb-3 border-0 shadow-sm">
        <div class="card-body">
          <div class="row align-items-center">
            <!-- Gambar -->
            <div class="col-3 col-md-2 text-center">
              <img src="Image/<?= htmlspecialchars($item['gambar'] ?? '') ?>"
                   class="img-fluid rounded" style="max-width:90px;max-height:90px;object-fit:cover;"
                   onerror="this.src='Image/Rectangle 18.png'">
            </div>
            <!-- Info -->
            <div class="col-6 col-md-7">
              <h5 class="fw-bold mb-1"><?= htmlspecialchars($item['nama']) ?></h5>
              <p class="mb-0 text-muted small">Size: <?= htmlspecialchars($item['size']) ?> &nbsp;|&nbsp; Qty: <?= $item['qty'] ?></p>
              <h6 class="text-primary fw-bold mt-1">Rp <?= number_format($subtotal,0,',','.') ?></h6>
            </div>
            <!-- Hapus -->
            <div class="col-3 text-end">
              <a href="index.php?page=keranjang&hapus=<?= $idx ?>"
                 class="btn btn-outline-danger btn-sm"
                 onclick="return confirm('Hapus produk ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

    <!-- TOTAL -->
    <div class="card border-0 shadow-sm mt-4">
      <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <span class="text-muted">Total Belanja</span>
          <h3 class="fw-bold text-primary mb-0">Rp <?= number_format($total,0,',','.') ?></h3>
        </div>
        <div class="d-flex gap-2">
          <a href="index.php?page=shop" class="btn btn-outline-dark px-4">Lanjut Belanja</a>
          <button class="btn btn-success px-5 py-2 fw-bold">
            <i class="bi bi-credit-card me-2"></i>Checkout
          </button>
        </div>
      </div>
    </div>

  <?php else: ?>
    <div class="text-center py-5">
      <i class="bi bi-bag-x fs-1 text-muted"></i>
      <h5 class="mt-3 text-muted">Keranjang kamu masih kosong.</h5>
      <a href="index.php?page=shop" class="btn btn-dark mt-3 px-5">Mulai Belanja</a>
    </div>
  <?php endif; ?>
</div>
