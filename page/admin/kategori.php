<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

// Hapus kategori
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    // Cek apakah kategori masih dipakai produk
    $cek = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id_kategori = ?");
    $cek->execute([$id]);
    if ($cek->fetchColumn() > 0) {
        header('Location: kategori.php?pesan=kategori_dipakai');
        exit;
    }
    $pdo->prepare("DELETE FROM kategori WHERE id = ?")->execute([$id]);
    header('Location: kategori.php?pesan=hapus_ok');
    exit;
}

$pesan = $_GET['pesan'] ?? '';

// Ambil semua kategori beserta jumlah produk
$list = $pdo->query("
    SELECT k.*, COUNT(p.id) AS jml_produk
    FROM kategori k
    LEFT JOIN produk p ON p.id_kategori = k.id
    GROUP BY k.id ORDER BY k.nama
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Daftar Kategori – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Daftar Kategori';</script>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Daftar Kategori</h4>
    <a href="tambah_kategori.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Tambah Kategori</a>
  </div>

  <?php if ($pesan === 'hapus_ok'): ?>
  <div class="alert alert-success alert-dismissible fade show">Kategori berhasil dihapus. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php elseif ($pesan === 'simpan_ok'): ?>
  <div class="alert alert-success alert-dismissible fade show">Kategori berhasil disimpan. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php elseif ($pesan === 'kategori_dipakai'): ?>
  <div class="alert alert-danger alert-dismissible fade show">Kategori tidak bisa dihapus karena masih dipakai oleh produk. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Nama Kategori</th>
            <th>Deskripsi</th>
            <th class="text-center">Jumlah Produk</th>
            <th class="text-center">Dibuat</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $i => $k): ?>
        <tr>
          <td class="text-muted small"><?= $i+1 ?></td>
          <td><strong><?= htmlspecialchars($k['nama']) ?></strong></td>
          <td class="small text-muted"><?= htmlspecialchars($k['deskripsi'] ?? '-') ?></td>
          <td class="text-center">
            <a href="produk.php?kat=<?= $k['id'] ?>" class="badge bg-primary text-decoration-none">
              <?= $k['jml_produk'] ?> produk
            </a>
          </td>
          <td class="text-center small text-muted"><?= date('d/m/Y', strtotime($k['dibuat_at'])) ?></td>
          <td class="text-center">
            <a href="edit_kategori.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-warning me-1" title="Edit">
              <i class="bi bi-pencil"></i>
            </a>
            <a href="kategori.php?hapus=<?= $k['id'] ?>" class="btn btn-sm btn-danger" title="Hapus"
               onclick="return confirm('Hapus kategori ini? Pastikan tidak ada produk yang menggunakannya.')">
              <i class="bi bi-trash"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
