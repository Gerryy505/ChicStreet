<?php
// Dipanggil setelah sidebar.php
?>
<!-- TOPBAR -->
<div class="admin-topbar">
  <div class="d-flex align-items-center gap-2">
    <button class="btn btn-sm d-md-none" onclick="document.getElementById('adminSidebar').classList.toggle('show')">
      <i class="bi bi-list fs-4"></i>
    </button>
    <span class="fw-semibold text-muted" id="pageTitle">Admin Panel</span>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">
      <i class="bi bi-person-circle me-1"></i>
      <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
    </span>
    <a href="logout.php" class="btn btn-sm btn-outline-danger">
      <i class="bi bi-box-arrow-right"></i>
    </a>
  </div>
</div>
