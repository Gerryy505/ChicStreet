<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: kategori.php'); exit; }

$k = $pdo->prepare("SELECT * FROM kategori WHERE id = ?");
$k->execute([$id]);
$kat = $k->fetch();
if (!$kat) { header('Location: kategori.php'); exit; }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if (!$nama) $errors[] = 'Nama kategori wajib diisi.';

    $cek = $pdo->prepare("SELECT COUNT(*) FROM kategori WHERE nama = ? AND id != ?");
    $cek->execute([$nama, $id]);
    if ($cek->fetchColumn() > 0) $errors[] = 'Nama kategori sudah digunakan.';

    if (empty($errors)) {
        $pdo->prepare("UPDATE kategori SET nama = ?, deskripsi = ? WHERE id = ?")->execute([$nama, $deskripsi, $id]);
        header('Location: kategori.php?pesan=simpan_ok');
        exit;
    }
    $kat = array_merge($kat, compact('nama', 'deskripsi'));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Kategori – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Edit Kategori';</script>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Kategori</h4>
    <a href="kategori.php" class="btn btn-outline-dark btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm" style="max-width:550px;">
    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($kat['nama']) ?>" required>
        </div>
        <div class="mb-4">
          <label class="form-label fw-semibold">Deskripsi</label>
          <textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($kat['deskripsi'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Update</button>
          <a href="kategori.php" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
