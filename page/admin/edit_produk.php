<?php
require_once 'auth_guard.php';
require_once '../../config/Database.php';
$pdo = getDB();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: produk.php'); exit; }

$p = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$p->execute([$id]);
$produk = $p->fetch();
if (!$produk) { header('Location: produk.php'); exit; }

$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama        = trim($_POST['nama'] ?? '');
    $merek       = trim($_POST['merek'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $harga       = str_replace(['.', ','], ['', '.'], trim($_POST['harga'] ?? ''));
    $stok        = (int)($_POST['stok'] ?? 0);
    $id_kategori = (int)($_POST['id_kategori'] ?? 0);
    $ukuran      = trim($_POST['ukuran'] ?? '');

    if (!$nama)  $errors[] = 'Nama produk wajib diisi.';
    if (!$merek) $errors[] = 'Merek wajib diisi.';
    if (!$harga || !is_numeric($harga)) $errors[] = 'Harga tidak valid.';

    // Handle gambar
    $gambar = $produk['gambar']; // tetap gambar lama
    if (!empty($_FILES['gambar']['name'])) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format gambar tidak valid.';
        } elseif ($_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar max 5MB.';
        } else {
            $filename = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
            $target = '../../uploads/produk/' . $filename;
            if (!is_dir('../../uploads/produk')) mkdir('../../uploads/produk', 0755, true);
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
                // Hapus gambar lama jika bukan default
                if ($produk['gambar'] && !str_starts_with($produk['gambar'], 'Rectangle')) {
                    @unlink('../../uploads/produk/' . basename($produk['gambar']));
                }
                $gambar = 'uploads/produk/' . $filename;
            }
        }
    } elseif (!empty($_POST['gambar_existing'])) {
        $gambar = $_POST['gambar_existing'];
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE produk SET nama=?, merek=?, deskripsi=?, harga=?, stok=?, gambar=?, id_kategori=?, ukuran=? WHERE id=?");
        $stmt->execute([$nama, $merek, $deskripsi, $harga, $stok, $gambar, $id_kategori ?: null, $ukuran, $id]);
        header('Location: produk.php?pesan=simpan_ok');
        exit;
    }

    // Isi ulang dari POST jika ada error
    $produk = array_merge($produk, compact('nama','merek','deskripsi','harga','stok','id_kategori','ukuran','gambar'));
}

$kats = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
$images_existing = array_map('basename', glob('../../Image/Rectangle 18*.png') ?: []);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Edit Produk – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'komponen/sidebar.php'; ?>
<?php include 'komponen/topbar.php'; ?>

<div class="admin-content">
  <script>document.getElementById('pageTitle').textContent = 'Edit Produk';</script>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Edit Produk</h4>
    <a href="produk.php" class="btn btn-outline-dark btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
  <?php endif; ?>

  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <form method="POST" enctype="multipart/form-data">

        <div class="row g-3">

          <div class="col-md-8">
            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($produk['nama']) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Merek <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="merek" value="<?= htmlspecialchars($produk['merek']) ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="harga" value="<?= $produk['harga'] ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Stok</label>
            <input type="number" class="form-control" name="stok" min="0" value="<?= $produk['stok'] ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="id_kategori" class="form-select">
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($kats as $k): ?>
              <option value="<?= $k['id'] ?>" <?= $produk['id_kategori'] == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label fw-semibold">Ukuran (pisahkan koma)</label>
            <input type="text" class="form-control" name="ukuran" value="<?= htmlspecialchars($produk['ukuran'] ?? '') ?>" placeholder="38,39,40,41,42">
          </div>

          <div class="col-12">
            <label class="form-label fw-semibold">Deskripsi</label>
            <textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($produk['deskripsi'] ?? '') ?></textarea>
          </div>

          <!-- Gambar -->
          <div class="col-12">
            <label class="form-label fw-semibold">Foto Produk</label>
            <div class="row g-3 align-items-start">
              <div class="col-md-3">
                <p class="small text-muted mb-1">Gambar saat ini:</p>
                <img src="<?= str_starts_with($produk['gambar'] ?? '', 'uploads') ? '../../' . $produk['gambar'] : '../../Image/' . $produk['gambar'] ?>"
                     style="width:100px;height:100px;object-fit:cover;border-radius:8px;" id="gambarSekarang"
                     onerror="this.src='../../Image/Rectangle 18.png'">
              </div>
              <div class="col-md-4">
                <label class="form-label small">Upload gambar baru:</label>
                <input type="file" class="form-control" name="gambar" accept="image/*" onchange="previewGambar(this)">
                <img id="preview" src="" class="mt-2 img-fluid rounded" style="max-height:100px;display:none;">
              </div>
              <div class="col-md-5">
                <label class="form-label small">Atau pilih gambar existing:</label>
                <select name="gambar_existing" class="form-select form-select-sm">
                  <option value="">-- Tetap pakai gambar saat ini --</option>
                  <?php foreach ($images_existing as $img): ?>
                  <option value="<?= $img ?>" <?= $produk['gambar'] == $img ? 'selected' : '' ?>><?= $img ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Update Produk</button>
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
