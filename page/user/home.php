<?php
require_once 'config/Database.php';
$pdo = getDB();

// Ambil produk terbaru (8 produk)
$produk_baru = $pdo->query("SELECT p.*, k.nama AS nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id ORDER BY p.dibuat_at DESC LIMIT 8")->fetchAll();

// Ambil semua kategori
$kategori_list = $pdo->query("SELECT * FROM kategori ORDER BY nama")->fetchAll();
?>

<!-- CAROUSEL BANNER -->
<?php include 'komponen/carousel.php'; ?>

<!-- BRAND SECTION -->
<section class="brands-section">
  <div class="container-fluid text-center p-0">
    <div class="brands-header py-2">
      <h5 class="fw-bold mb-0">Jenis Sepatu / Merek</h5>
    </div>
    <div class="row justify-content-center align-items-center g-4 py-3 bg-white m-0 brands-logos">
      <div class="col-6 col-md-2"><img src="Image/Group 131.png" alt="Nike" class="brand-logo"></div>
      <div class="col-6 col-md-2"><img src="Image/Group 132.png" alt="Puma" class="brand-logo"></div>
      <div class="col-6 col-md-2"><img src="Image/Group 133.png" alt="Adidas" class="brand-logo"></div>
      <div class="col-6 col-md-2"><img src="Image/anta.png" alt="Anta" class="brand-logo"></div>
      <div class="col-6 col-md-2"><img src="Image/Group 134.png" alt="Converse" class="brand-logo"></div>
      <div class="col-6 col-md-2"><img src="Image/vans.png" alt="Vans" class="brand-logo"></div>
    </div>
  </div>
</section>

<!-- WHAT'S NEW - Dari Database -->
<section class="whats-new py-4">
  <div class="container-fluid text-center">
    <h3 class="fw-bold mb-4">WHAT'S NEW</h3>
    <div class="row g-3 justify-content-center">
      <?php foreach ($produk_baru as $p): ?>
      <div class="col-6 col-md-3">
        <a href="index.php?page=detail&id=<?= $p['id'] ?>" class="text-decoration-none text-dark">
          <div class="card product-card h-100 border-0 shadow-sm">
            <img src="Image/<?= htmlspecialchars($p['gambar'] ?? 'Rectangle 18.png') ?>"
                 alt="<?= htmlspecialchars($p['nama']) ?>" class="card-img-top"
                 style="height:200px;object-fit:cover;">
            <div class="card-body product-info">
              <hr>
              <h6 class="text-danger fw-bold mb-1"><?= htmlspecialchars($p['merek']) ?></h6>
              <p class="small mb-1"><?= htmlspecialchars($p['nama']) ?></p>
              <span class="price fw-bold text-primary">Rp <?= number_format($p['harga'],0,',','.') ?></span>
              <?php if ($p['stok'] <= 0): ?>
                <span class="badge bg-danger d-block mt-1">Stok Habis</span>
              <?php elseif ($p['stok'] <= 5): ?>
                <span class="badge bg-warning d-block mt-1">Stok Terbatas</span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
    <a href="index.php?page=shop" class="btn btn-dark mt-4 px-5">Lihat Semua Produk</a>
  </div>
</section>

<!-- SHOP BY SPORT -->
<section class="shop-sport py-5">
  <div class="container-fluid text-center">
    <h3 class="fw-bold mb-4 text-primary">SHOP BY SPORT</h3>
    <div class="row g-0">
      <?php
      $sport_img = [
        'Basketball' => 'Rectangle 47.png',
        'Tenis'      => 'Rectangle 48.png',
        'Running'    => 'Rectangle 49.png',
        'Voley'      => 'Rectangle 50.png',
        'Style'      => 'Rectangle 51.png',
      ];
      foreach ($kategori_list as $kat):
        $img = $sport_img[$kat['nama']] ?? 'Rectangle 47.png';
      ?>
      <div class="col-6 col-md">
        <a href="index.php?page=shop&kategori=<?= $kat['id'] ?>" class="text-decoration-none">
          <div class="sport-card">
            <img src="Image/<?= $img ?>" alt="<?= $kat['nama'] ?>">
            <div class="sport-overlay"><?= strtoupper($kat['nama']) ?></div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- POSTER -->
<div class="poster-besar">
  <img src="Image/on_desktop_6.webp" alt="">
</div>
