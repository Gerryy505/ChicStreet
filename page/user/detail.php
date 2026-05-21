<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once 'config/Database.php';
$pdo = getDB();

// Tambah ke keranjang
if (isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $_SESSION['cart'][] = [
        'id_produk' => (int)$_POST['id_produk'],
        'nama'      => $_POST['nama'],
        'harga'     => (float)$_POST['harga'],
        'gambar'    => $_POST['gambar'],
        'size'      => $_POST['size'],
        'qty'       => max(1, (int)$_POST['qty']),
    ];
    echo "<script>alert('Produk berhasil ditambahkan ke keranjang!');window.location.href='index.php?page=keranjang';</script>";
    exit;
}

// Ambil produk
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php?page=shop'); exit; }

$stmt = $pdo->prepare("SELECT p.*, k.nama AS nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id WHERE p.id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) { echo '<div class="container py-5 text-center"><h4>Produk tidak ditemukan.</h4><a href="index.php?page=shop" class="btn btn-dark mt-3">Kembali</a></div>'; return; }

// Ukuran tersedia
$ukuran_list = array_map('trim', explode(',', $p['ukuran'] ?? ''));

// Produk rekomendasi (same kategori)
$stmt2 = $pdo->prepare("SELECT * FROM produk WHERE id_kategori = ? AND id != ? LIMIT 4");
$stmt2->execute([$p['id_kategori'], $p['id']]);
$rekomendasi = $stmt2->fetchAll();
?>

<!-- DETAIL PRODUK -->
<section class="product-detail py-5">
  <div class="container">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php?page=home" class="text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=shop" class="text-decoration-none">Shop</a></li>
        <li class="breadcrumb-item active"><?= htmlspecialchars($p['nama']) ?></li>
      </ol>
    </nav>

    <div class="row g-4">

      <!-- GAMBAR -->
      <div class="col-md-6">
        <img src="Image/<?= htmlspecialchars($p['gambar'] ?? 'Rectangle 18.png') ?>"
             alt="<?= htmlspecialchars($p['nama']) ?>"
             class="img-fluid rounded shadow-sm w-100" style="max-height:450px;object-fit:cover;">
      </div>

      <!-- INFO -->
      <div class="col-md-6">
        <span class="badge bg-secondary mb-2"><?= htmlspecialchars($p['nama_kategori'] ?? '') ?></span>
        <h6 class="text-danger fw-bold fs-5"><?= htmlspecialchars($p['merek']) ?></h6>
        <h2 class="fw-bold mb-2"><?= htmlspecialchars($p['nama']) ?></h2>
        <h3 class="text-primary fw-bold mb-3">Rp <?= number_format($p['harga'],0,',','.') ?></h3>
        <p class="text-secondary mb-3"><?= nl2br(htmlspecialchars($p['deskripsi'] ?? '')) ?></p>

        <!-- Stok -->
        <div class="mb-3">
          <?php if ($p['stok'] <= 0): ?>
            <span class="badge bg-danger fs-6">Stok Habis</span>
          <?php elseif ($p['stok'] <= 5): ?>
            <span class="badge bg-warning text-dark fs-6">Stok Terbatas – Sisa <?= $p['stok'] ?></span>
          <?php else: ?>
            <span class="badge bg-success fs-6">Stok Tersedia (<?= $p['stok'] ?>)</span>
          <?php endif; ?>
        </div>

        <?php if ($p['stok'] > 0): ?>
        <!-- FORM BELI -->
        <form method="POST">
          <input type="hidden" name="id_produk" value="<?= $p['id'] ?>">
          <input type="hidden" name="nama"      value="<?= htmlspecialchars($p['nama']) ?>">
          <input type="hidden" name="harga"     value="<?= $p['harga'] ?>">
          <input type="hidden" name="gambar"    value="<?= htmlspecialchars($p['gambar'] ?? '') ?>">

          <!-- SIZE -->
          <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Size</label>
            <select class="form-select" name="size" required>
              <?php foreach ($ukuran_list as $uk): ?>
                <?php if ($uk): ?><option value="<?= $uk ?>"><?= $uk ?></option><?php endif; ?>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- QTY -->
          <div class="mb-4">
            <label class="form-label fw-semibold">Quantity</label>
            <input type="number" class="form-control" name="qty" value="1" min="1" max="<?= $p['stok'] ?>">
          </div>

          <button type="submit" name="tambah_keranjang" class="btn btn-primary w-100 py-3 fw-bold">
            <i class="bi bi-bag-plus me-2"></i>TAMBAH KE KERANJANG
          </button>
        </form>
        <?php else: ?>
          <button class="btn btn-secondary w-100 py-3 fw-bold" disabled>Stok Habis</button>
        <?php endif; ?>

      </div>
    </div>

    <!-- DESKRIPSI LENGKAP -->
    <div class="row mt-5">
      <div class="col-12">
        <h4 class="fw-bold mb-3">Deskripsi Produk</h4>
        <p class="text-secondary"><?= nl2br(htmlspecialchars($p['deskripsi'] ?? 'Tidak ada deskripsi.')) ?></p>
      </div>
    </div>

  </div>
</section>

<!-- REKOMENDASI -->
<?php if (!empty($rekomendasi)): ?>
<section class="recommendation py-5 bg-light">
  <div class="container">
    <h4 class="fw-bold mb-4">Produk Serupa</h4>
    <div class="row g-4">
      <?php foreach ($rekomendasi as $r): ?>
      <div class="col-6 col-md-3">
        <a href="index.php?page=detail&id=<?= $r['id'] ?>" class="text-decoration-none text-dark">
          <div class="card border-0 shadow-sm h-100">
            <img src="Image/<?= htmlspecialchars($r['gambar'] ?? 'Rectangle 18.png') ?>"
                 class="card-img-top" style="height:180px;object-fit:cover;" alt="<?= htmlspecialchars($r['nama']) ?>">
            <div class="card-body">
              <h6 class="text-danger fw-bold"><?= htmlspecialchars($r['merek']) ?></h6>
              <p class="card-text small"><?= htmlspecialchars($r['nama']) ?></p>
              <span class="fw-bold text-primary">Rp <?= number_format($r['harga'],0,',','.') ?></span>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
