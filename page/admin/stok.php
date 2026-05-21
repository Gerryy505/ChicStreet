<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

$pesan = '';
$tipe  = 'success';

// Handle update stok
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produk'])) {
    $id_produk = (int)$_POST['id_produk'];
    $tipe_ubah = $_POST['tipe_ubah'] ?? 'set'; // set / tambah / kurang
    $jumlah    = (int)$_POST['jumlah'];

    if ($jumlah < 0) {
        $pesan = 'Jumlah tidak boleh negatif.';
        $tipe = 'danger';
    } else {
        if ($tipe_ubah === 'tambah') {
            $pdo->prepare("UPDATE produk SET stok = stok + ? WHERE id = ?")->execute([$jumlah, $id_produk]);
            $pesan = 'Stok berhasil ditambah.';
        } elseif ($tipe_ubah === 'kurang') {
            // Pastikan tidak negatif
            $curr = $pdo->prepare("SELECT stok FROM produk WHERE id = ?");
            $curr->execute([$id_produk]);
            $curr_stok = (int)$curr->fetchColumn();
            $new_stok  = max(0, $curr_stok - $jumlah);
            $pdo->prepare("UPDATE produk SET stok = ? WHERE id = ?")->execute([$new_stok, $id_produk]);
            $pesan = "Stok berhasil dikurangi. Stok baru: $new_stok";
        } else {
            // set langsung
            $pdo->prepare("UPDATE produk SET stok = ? WHERE id = ?")->execute([$jumlah, $id_produk]);
            $pesan = 'Stok berhasil diperbarui.';
        }
    }
}

// Ambil semua produk
$q_filter = trim($_GET['q'] ?? '');
$params = [];
$where  = '1=1';
if ($q_filter) {
    $where    = "(p.nama LIKE ? OR p.merek LIKE ?)";
    $params[] = "%$q_filter%";
    $params[] = "%$q_filter%";
}
$stmt = $pdo->prepare("SELECT p.*, k.nama AS kat FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id WHERE $where ORDER BY p.stok ASC, p.nama ASC");
$stmt->execute($params);
$list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Kelola Stok – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Kelola Stok';</script>

  <h4 class="fw-bold mb-4">Kelola Stok Produk</h4>

  <?php if ($pesan): ?>
  <div class="alert alert-<?= $tipe ?> alert-dismissible fade show">
    <?= htmlspecialchars($pesan) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Filter -->
  <form method="GET" class="d-flex gap-2 mb-4">
    <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($q_filter) ?>" placeholder="Cari produk...">
    <button class="btn btn-dark px-4">Cari</button>
    <a href="stok.php" class="btn btn-outline-secondary">Reset</a>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th style="width:60px">Foto</th>
              <th>Produk</th>
              <th>Merek</th>
              <th class="text-center" style="width:90px">Stok Saat Ini</th>
              <th style="width:350px" class="text-center">Update Stok</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($list as $prod): ?>
          <tr>
            <td>
              <img src="<?= str_starts_with($prod['gambar'] ?? '', 'uploads') ? '../../' . $prod['gambar'] : '../../Image/' . $prod['gambar'] ?>"
                   style="width:45px;height:45px;object-fit:cover;border-radius:6px;"
                   onerror="this.src='../../Image/Rectangle 18.png'" alt="">
            </td>
            <td>
              <strong class="small d-block"><?= htmlspecialchars($prod['nama']) ?></strong>
              <span class="badge bg-secondary"><?= htmlspecialchars($prod['kat'] ?? '-') ?></span>
            </td>
            <td class="small"><?= htmlspecialchars($prod['merek']) ?></td>
            <td class="text-center">
              <span class="badge fs-6 <?= $prod['stok'] == 0 ? 'bg-danger' : ($prod['stok'] <= 5 ? 'bg-warning text-dark' : 'bg-success') ?>">
                <?= $prod['stok'] ?>
              </span>
            </td>
            <td>
              <form method="POST" class="d-flex gap-1 align-items-center justify-content-center flex-wrap">
                <input type="hidden" name="id_produk" value="<?= $prod['id'] ?>">
                <select name="tipe_ubah" class="form-select form-select-sm" style="width:110px;">
                  <option value="tambah">+ Tambah</option>
                  <option value="kurang">– Kurangi</option>
                  <option value="set">= Set Langsung</option>
                </select>
                <input type="number" name="jumlah" min="0" value="0"
                       class="form-control form-control-sm text-center" style="width:75px;">
                <button type="submit" class="btn btn-sm btn-primary">
                  <i class="bi bi-arrow-clockwise"></i> Update
                </button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
