<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

// Handle DELETE
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Ambil gambar dulu untuk dihapus
    $g = $pdo->prepare("SELECT gambar FROM produk WHERE id = ?");
    $g->execute([$id]);
    $row = $g->fetch();
    // Hapus file gambar jika ada (bukan default)
    if ($row && $row['gambar'] && !str_starts_with($row['gambar'], 'Rectangle')) {
        $path = '../../uploads/produk/' . $row['gambar'];
        if (file_exists($path)) @unlink($path);
    }
    $pdo->prepare("DELETE FROM produk WHERE id = ?")->execute([$id]);
    header('Location: produk.php?pesan=hapus_ok');
    exit;
}

$pesan = $_GET['pesan'] ?? '';

// Filter
$q    = trim($_GET['q'] ?? '');
$kat  = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
$where = ['1=1']; $params = [];
if ($q) { $where[] = "(p.nama LIKE ? OR p.merek LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($kat > 0) { $where[] = "p.id_kategori = ?"; $params[] = $kat; }

$stmt = $pdo->prepare("SELECT p.*, k.nama AS kat FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id WHERE " . implode(' AND ', $where) . " ORDER BY p.dibuat_at DESC");
$stmt->execute($params);
$produk_list = $stmt->fetchAll();

$kats = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Produk – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Daftar Produk';</script>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="fw-bold mb-0">Daftar Produk</h4>
    <a href="tambah_produk.php" class="btn btn-primary">
      <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </a>
  </div>

  <?php if ($pesan === 'hapus_ok'): ?>
  <div class="alert alert-success alert-dismissible fade show">Produk berhasil dihapus. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php elseif ($pesan === 'simpan_ok'): ?>
  <div class="alert alert-success alert-dismissible fade show">Produk berhasil disimpan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <!-- Filter -->
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
      <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-5">
          <input type="text" class="form-control form-control-sm" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama / merek...">
        </div>
        <div class="col-md-3">
          <select name="kat" class="form-select form-select-sm">
            <option value="">Semua Kategori</option>
            <?php foreach ($kats as $k): ?>
            <option value="<?= $k['id'] ?>" <?= $kat == $k['id'] ? 'selected' : '' ?>><?= $k['nama'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-auto">
          <button type="submit" class="btn btn-sm btn-dark">Filter</button>
          <a href="produk.php" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <!-- TABLE -->
  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th style="width:50px">#</th>
              <th style="width:70px">Foto</th>
              <th>Nama Produk</th>
              <th>Merek</th>
              <th>Kategori</th>
              <th class="text-end">Harga</th>
              <th class="text-center">Stok</th>
              <th class="text-center" style="width:130px">Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($produk_list)): ?>
          <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada produk ditemukan.</td></tr>
          <?php else: ?>
          <?php foreach ($produk_list as $i => $p): ?>
          <tr>
            <td class="text-muted small"><?= $i+1 ?></td>
            <td>
              <img src="../../Image/<?= htmlspecialchars($p['gambar'] ?? 'Rectangle 18.png') ?>"
                   style="width:50px;height:50px;object-fit:cover;border-radius:6px;"
                   onerror="this.src='../../Image/Rectangle 18.png'" alt="">
            </td>
            <td><strong class="small"><?= htmlspecialchars($p['nama']) ?></strong></td>
            <td class="small"><?= htmlspecialchars($p['merek']) ?></td>
            <td><span class="badge bg-secondary"><?= htmlspecialchars($p['kat'] ?? '-') ?></span></td>
            <td class="text-end small">Rp <?= number_format($p['harga'],0,',','.') ?></td>
            <td class="text-center">
              <span class="badge <?= $p['stok'] == 0 ? 'bg-danger' : ($p['stok'] <= 5 ? 'bg-warning text-dark' : 'bg-success') ?>">
                <?= $p['stok'] ?>
              </span>
            </td>
            <td class="text-center">
              <a href="edit_produk.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <a href="produk.php?hapus=<?= $p['id'] ?>" class="btn btn-sm btn-danger" title="Hapus"
                 onclick="return confirm('Yakin hapus produk ini?')">
                <i class="bi bi-trash"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white text-muted small"><?= count($produk_list) ?> produk ditemukan</div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
