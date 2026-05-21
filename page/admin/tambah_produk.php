<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama'] ?? '');
    $merek       = trim($_POST['merek'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $harga       = str_replace(['.', ','], ['', '.'], trim($_POST['harga'] ?? ''));
    $stok        = (int)($_POST['stok'] ?? 0);
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $ukuran      = trim($_POST['ukuran'] ?? '');

    // Validasi
    if (!$nama)    $errors[] = 'Nama produk wajib diisi.';
    if (!$merek)   $errors[] = 'Merek wajib diisi.';
    if (!$harga || !is_numeric($harga)) $errors[] = 'Harga tidak valid.';
    if ($stok < 0) $errors[] = 'Stok tidak boleh negatif.';

    // Upload gambar
    $gambar = 'Rectangle 18.png'; // default
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format gambar tidak valid (jpg, png, webp, gif).';
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar maksimal 5MB.';
        } else {
            $filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
            $target = '../../uploads/produk/' . $filename;
            if (!is_dir('../../uploads/produk')) mkdir('../../uploads/produk', 0755, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                $gambar = 'uploads/produk/' . $filename;
            } else {
                $errors[] = 'Gagal mengupload gambar.';
            }
        }
    } elseif (!empty($_POST['gambar_existing'])) {
        $gambar = $_POST['gambar_existing'];
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO produk (nama, merek, deskripsi, harga, stok, gambar, id_kategori, ukuran) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$nama, $merek, $deskripsi, $harga, $stok, $gambar, $id_kategori ?: null, $ukuran]);
        header('Location: produk.php?pesan=simpan_ok');
        exit;
    }
}

$kats = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
// Daftar gambar existing (Image/)
$images_existing = glob('../../Image/Rectangle 18*.png');
$images_existing = array_map('basename', $images_existing ?: []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Tambah Produk – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Tambah Produk';</script>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Tambah Produk Baru</h4>
    <a href="produk.php" class="btn btn-outline-dark btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger">
    <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
  </div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form method="POST" enctype="multipart/form-data">

        <div class="row g-3">

          <!-- Nama -->
          <div class="col-md-8">
            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required placeholder="Contoh: Speedcat OG Unisex">
          </div>

          <!-- Merek -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Merek <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="merek"
                   value="<?= htmlspecialchars($_POST['merek'] ?? '') ?>" required placeholder="PUMA, NIKE, ADIDAS...">
          </div>

          <!-- Harga -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="harga"
                   value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>" required placeholder="1999000">
          </div>

          <!-- Stok -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Stok Awal</label>
            <input type="number" class="form-control" name="stok" min="0"
                   value="<?= (int)($_POST['stok'] ?? 0) ?>">
          </div>

          <!-- Kategori -->
          <div class="col-md-4">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="id_kategori" class="form-select">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kats as $k): ?>
              <option value="<?= $k['id'] ?>" <?= (($_POST['id_kategori'] ?? '') == $k['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($k['nama']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Ukuran -->
          <div class="col-md-6">
            <label class="form-label fw-semibold">Ukuran Tersedia</label>
            <input type="text" class="form-control" name="ukuran"
                   value="<?= htmlspecialchars($_POST['ukuran'] ?? '') ?>"
                   placeholder="38,39,40,41,42 (pisahkan dengan koma)">
            <div class="form-text">Pisahkan dengan koma. Contoh: 38,39,40,41,42</div>
          </div>

          <!-- Deskripsi -->
          <div class="col-12">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="3"
                      placeholder="Deskripsi produk..."><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
          </div>

          <!-- Gambar -->
          <div class="col-12">
            <label class="form-label fw-semibold">Foto Produk</label>
            <div class="row g-3 align-items-start">
              <div class="col-md-5">
                <input type="file" class="form-control" name="gambar" accept="image/*"
                       onchange="previewGambar(this)">
                <div class="form-text">Max 5MB. Format: JPG, PNG, WEBP</div>
                <img id="preview" src="" class="mt-2 img-fluid rounded" style="max-height:140px;display:none;">
              </div>
              <div class="col-md-7">
                <label class="form-label small text-muted">Atau pilih gambar yang sudah ada:</label>
                <select name="gambar_existing" class="form-select form-select-sm">
                  <option value="">-- Upload gambar baru --</option>
                  <?php foreach ($images_existing as $img): ?>
                  <option value="<?= $img ?>"><?= $img ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary px-5">
            <i class="bi bi-save me-2"></i>Simpan Produk
          </button>
          <a href="produk.php" class="btn btn-outline-secondary">Batal</a>
        </div>

      </form>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function previewGambar(input) {
  const preview = document.getElementById('preview');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
    reader.readAsDataURL(input.files[0]);
  }
}
</script>
</body>
</html>
