<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../../config/Database.php';
    $pdo = getDB();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $admin['username'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login – Chic Street</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height:100vh; display:flex; align-items:center; justify-content:center; }
    .login-card { background:#fff; border-radius:16px; padding:40px; width:100%; max-width:400px; box-shadow:0 20px 60px rgba(0,0,0,.4); }
    .login-card .brand { font-size:28px; font-weight:700; color:#1a1a2e; letter-spacing:1px; }
    .btn-login { background:#1a1a2e; color:#fff; border:none; padding:12px; font-weight:600; letter-spacing:.5px; }
    .btn-login:hover { background:#0f3460; color:#fff; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="text-center mb-4">
      <div class="brand">👟 CHIC STREET</div>
      <p class="text-muted small mt-1">Admin Dashboard</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label class="form-label fw-semibold">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" name="username" placeholder="admin" required autofocus>
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label fw-semibold">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" name="password" placeholder="••••••" required>
        </div>
      </div>
      <button type="submit" class="btn btn-login w-100 rounded-3">
        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
      </button>
    </form>

    <p class="text-center text-muted small mt-4">
      <a href="../../index.php" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i>Kembali ke Toko
      </a>
    </p>
    <p class="text-center text-muted" style="font-size:11px;">Default: admin / password</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
