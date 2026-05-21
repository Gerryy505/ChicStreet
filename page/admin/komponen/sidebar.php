<?php
// Tentukan halaman aktif
$current = basename($_SERVER['PHP_SELF'], '.php');
function isActive($page) {
    global $current;
    return $current === $page ? 'active' : '';
}
?>
<style>
  :root {
    --sidebar-w: 250px;
    --sidebar-bg: #1a1a2e;
    --sidebar-hover: #0f3460;
    --accent: #e94560;
  }
  body { font-family: 'Roboto', sans-serif; background: #f4f6fb; }
  .admin-sidebar {
    width: var(--sidebar-w); min-height: 100vh; background: var(--sidebar-bg);
    position: fixed; top: 0; left: 0; z-index: 1000; transition: transform .3s;
    display: flex; flex-direction: column;
  }
  .sidebar-brand {
    padding: 20px; border-bottom: 1px solid rgba(255,255,255,.1);
    font-size: 20px; font-weight: 700; color: #fff; letter-spacing: 1px;
  }
  .sidebar-brand span { color: var(--accent); }
  .sidebar-nav { padding: 16px 0; flex: 1; overflow-y: auto; }
  .nav-section-label {
    font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase;
    color: rgba(255,255,255,.4); padding: 12px 20px 4px;
  }
  .sidebar-link {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 20px; color: rgba(255,255,255,.75);
    text-decoration: none; font-size: 14px; transition: all .2s;
    border-left: 3px solid transparent;
  }
  .sidebar-link:hover, .sidebar-link.active {
    background: var(--sidebar-hover); color: #fff;
    border-left-color: var(--accent);
  }
  .sidebar-link i { font-size: 17px; width: 22px; }
  .admin-topbar {
    position: sticky; top: 0; z-index: 999; height: 60px;
    background: #fff; border-bottom: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 24px; box-shadow: 0 1px 4px rgba(0,0,0,.06);
    margin-left: var(--sidebar-w);
  }
  .admin-content { margin-left: var(--sidebar-w); padding: 24px; min-height: calc(100vh - 60px); }
  @media (max-width: 768px) {
    .admin-sidebar { transform: translateX(-100%); }
    .admin-sidebar.show { transform: translateX(0); }
    .admin-topbar, .admin-content { margin-left: 0; }
  }
</style>

<!-- SIDEBAR -->
<div class="admin-sidebar" id="adminSidebar">
  <div class="sidebar-brand">
    👟 CHIC <span>STREET</span>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-section-label">Menu</div>
    <a href="dashboard.php" class="sidebar-link <?= isActive('dashboard') ?>">
      <i class="bi bi-speedometer2"></i> Dashboard
    </a>

    <div class="nav-section-label">Produk</div>
    <a href="produk.php" class="sidebar-link <?= isActive('produk') ?>">
      <i class="bi bi-shoe"></i> Daftar Produk
    </a>
    <a href="tambah_produk.php" class="sidebar-link <?= isActive('tambah_produk') ?>">
      <i class="bi bi-plus-circle"></i> Tambah Produk
    </a>
    <a href="stok.php" class="sidebar-link <?= isActive('stok') ?>">
      <i class="bi bi-boxes"></i> Kelola Stok
    </a>

    <div class="nav-section-label">Kategori</div>
    <a href="kategori.php" class="sidebar-link <?= isActive('kategori') ?>">
      <i class="bi bi-tags"></i> Daftar Kategori
    </a>
    <a href="tambah_kategori.php" class="sidebar-link <?= isActive('tambah_kategori') ?>">
      <i class="bi bi-tag"></i> Tambah Kategori
    </a>

    <div class="nav-section-label">Akun</div>
    <a href="../../index.php" class="sidebar-link" target="_blank">
      <i class="bi bi-shop"></i> Lihat Toko
    </a>
    <a href="logout.php" class="sidebar-link text-danger">
      <i class="bi bi-box-arrow-left"></i> Keluar
    </a>
  </nav>
</div>
