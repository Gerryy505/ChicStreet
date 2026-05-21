<?php
require_once 'config/Database.php';
$pdo = getDB();

// Ambil filter dari GET
$id_kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$q           = trim($_GET['q'] ?? '');
$merek       = trim($_GET['merek'] ?? '');

// Build query dinamis
$where = ["1=1"];
$params = [];

if ($id_kategori > 0) {
    $where[]  = "p.id_kategori = ?";
    $params[] = $id_kategori;
}
if ($q !== '') {
    $where[]  = "(p.nama LIKE ? OR p.merek LIKE ? OR p.deskripsi LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($merek !== '') {
    $where[]  = "p.merek = ?";
    $params[] = $merek;
}

$sql = "SELECT p.*, k.nama AS nama_kategori
        FROM produk p
        LEFT JOIN kategori k ON p.id_kategori = k.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY p.dibuat_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produk_list = $stmt->fetchAll();

// Ambil semua kategori untuk filter
$kats  = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$mereks = $pdo->query("SELECT DISTINCT merek FROM produk ORDER BY merek")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="container py-4">

  <h3 class="fw-bold mb-4">
    <?php if ($q): ?>
      Hasil Pencarian: "<?= htmlspecialchars($q) ?>"
    <?php elseif ($id_kategori > 0): ?>
      <?php $kat_name = array_filter($kats, fn($k) => $k['id'] == $id_kategori); ?>
      Kategori: <?= htmlspecialchars(array_values($kat_name)[0]['nama'] ?? '') ?>
    <?php else: ?>
      Semua Produk
    <?php endif; ?>
  </h3>

  <div class="row g-4">

    <!-- SIDEBAR FILTER -->
    <div class="col-md-3">
      <div class="card border-0 shadow-sm p-3">
        <form method="GET" action="index.php">
          <input type="hidden" name="page" value="shop">

          <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-1"></i>Filter</h6>

          <!-- Cari -->
          <div class="mb-3">
            <label class="form-label small fw-semibold">Cari Produk</label>
            <input type="text" class="form-control form-control-sm" name="q"
                   value="<?= htmlspecialchars($q) ?>" placeholder="Nama produk...">
          </div>

          <!-- Kategori -->
          <div class="mb-3">
            <label class="form-label small fw-semibold">Kategori</label>
            <select name="kategori" class="form-select form-select-sm">
              <option value="">Semua Kategori</option>
              <?php foreach ($kats as $k): ?>
              <option value="<?= $k['id'] ?>" <?= $id_kategori == $k['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($k['nama']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Merek -->
          <div class="mb-3">
            <label class="form-label small fw-semibold">Merek</label>
            <select name="merek" class="form-select form-select-sm">
              <option value="">Semua Merek</option>
              <?php foreach ($mereks as $m): ?>
              <option value="<?= $m ?>" <?= $merek == $m ? 'selected' : '' ?>><?= $m ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <button type="submit" class="btn btn-dark btn-sm w-100">Terapkan</button>
          <a href="index.php?page=shop" class="btn btn-outline-secondary btn-sm w-100 mt-2">Reset</a>
        </form>
      </div>
    </div>

    <!-- PRODUK GRID -->
    <div class="col-md-9">
      <?php if (empty($produk_list)): ?>
        <div class="alert alert-info text-center py-5">
          <i class="bi bi-search fs-1"></i>
          <p class="mt-2 mb-0">Produk tidak ditemukan.</p>
        </div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($produk_list as $p): ?>
        <div class="col-6 col-md-4">
          <a href="index.php?page=detail&id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
            <div class="card product-card h-100 border-0 shadow-sm">
              <img src="Image/<?= htmlspecialchars($p['gambar'] ?? 'Rectangle 18.png') ?>"
                   alt="<?= htmlspecialchars($p['nama']) ?>"
                   class="card-img-top" style="height:200px;object-fit:cover;">
              <div class="card-body">
                <span class="badge bg-secondary mb-1"><?= htmlspecialchars($p['nama_kategori'] ?? '') ?></span>
                <h6 class="text-danger fw-bold mb-0"><?= htmlspecialchars($p['merek']) ?></h6>
                <p class="small mb-1"><?= htmlspecialchars($p['nama']) ?></p>
                <span class="fw-bold text-primary">Rp <?= number_format($p['harga'],0,',','.') ?></span>
                <div class="mt-1">
                  <?php if ($p['stok'] <= 0): ?>
                    <span class="badge bg-danger">Stok Habis</span>
                  <?php elseif ($p['stok'] <= 5): ?>
                    <span class="badge bg-warning text-dark">Sisa <?= $p['stok'] ?></span>
                  <?php else: ?>
                    <span class="badge bg-success">Tersedia</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <p class="text-muted small mt-3"><?= count($produk_list) ?> produk ditemukan</p>
      <?php endif; ?>
    </div>

  </div>
</div>
