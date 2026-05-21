<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

// Statistik
$total_produk   = $pdo->query("SELECT COUNT(*) FROM produk")->fetchColumn();
$total_kategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$stok_habis     = $pdo->query("SELECT COUNT(*) FROM produk WHERE stok = 0")->fetchColumn();
$stok_tipis     = $pdo->query("SELECT COUNT(*) FROM produk WHERE stok > 0 AND stok <= 5")->fetchColumn();
$total_stok     = $pdo->query("SELECT SUM(stok) FROM produk")->fetchColumn() ?? 0;

// 5 produk stok terbatas
$stok_warning = $pdo->query("SELECT * FROM produk WHERE stok <= 5 ORDER BY stok ASC LIMIT 5")->fetchAll();

// Produk terbaru
$produk_terbaru = $pdo->query("SELECT p.*, k.nama AS kat FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id ORDER BY p.dibuat_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard – Chic Street Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>

<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Dashboard';</script>

  <h4 class="fw-bold mb-4">Dashboard</h4>

  <!-- STATS CARDS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:50px;height:50px;background:#e8f4fd;">
            <i class="bi bi-shoe fs-4 text-primary"></i>
          </div>
          <div>
            <div class="text-muted small">Total Produk</div>
            <div class="fw-bold fs-4"><?= $total_produk ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:50px;height:50px;background:#e8fdf0;">
            <i class="bi bi-tags fs-4 text-success"></i>
          </div>
          <div>
            <div class="text-muted small">Kategori</div>
            <div class="fw-bold fs-4"><?= $total_kategori ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:50px;height:50px;background:#fff3e0;">
            <i class="bi bi-boxes fs-4 text-warning"></i>
          </div>
          <div>
            <div class="text-muted small">Total Stok</div>
            <div class="fw-bold fs-4"><?= number_format($total_stok) ?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center gap-3">
          <div class="rounded-circle d-flex align-items-center justify-content-center"
               style="width:50px;height:50px;background:#fdeaea;">
            <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
          </div>
          <div>
            <div class="text-muted small">Stok Habis</div>
            <div class="fw-bold fs-4"><?= $stok_habis ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">

    <!-- PRODUK STOK TERBATAS -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-circle me-2"></i>Stok Perlu Diisi</h6>
          <a href="stok.php" class="btn btn-sm btn-outline-danger">Kelola Stok</a>
        </div>
        <div class="card-body p-0">
          <?php if (empty($stok_warning)): ?>
            <div class="text-center text-muted py-4 small">Semua stok aman 👍</div>
          <?php else: ?>
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Produk</th><th>Merek</th><th class="text-center">Stok</th></tr></thead>
            <tbody>
            <?php foreach ($stok_warning as $s): ?>
            <tr>
              <td class="small"><?= htmlspecialchars($s['nama']) ?></td>
              <td class="small"><?= htmlspecialchars($s['merek']) ?></td>
              <td class="text-center">
                <span class="badge <?= $s['stok'] == 0 ? 'bg-danger' : 'bg-warning text-dark' ?>">
                  <?= $s['stok'] == 0 ? 'Habis' : $s['stok'] ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- PRODUK TERBARU -->
    <div class="col-md-6">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Produk Terbaru</h6>
          <a href="produk.php" class="btn btn-sm btn-outline-dark">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
          <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Nama</th><th>Kategori</th><th class="text-end">Harga</th></tr></thead>
            <tbody>
            <?php foreach ($produk_terbaru as $t): ?>
            <tr>
              <td class="small"><?= htmlspecialchars($t['nama']) ?></td>
              <td class="small"><?= htmlspecialchars($t['kat'] ?? '-') ?></td>
              <td class="small text-end">Rp <?= number_format($t['harga'],0,',','.') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <h6 class="fw-bold mb-3">Aksi Cepat</h6>
          <div class="d-flex flex-wrap gap-2">
            <a href="tambah_produk.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Produk</a>
            <a href="tambah_kategori.php" class="btn btn-success"><i class="bi bi-tag me-2"></i>Tambah Kategori</a>
            <a href="stok.php" class="btn btn-warning text-dark"><i class="bi bi-boxes me-2"></i>Update Stok</a>
            <a href="produk.php" class="btn btn-outline-dark"><i class="bi bi-list-ul me-2"></i>Semua Produk</a>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
