<?php
// File ini HANYA untuk generate password hash
// Jalankan sekali, lalu hapus file ini!
// Akses: http://localhost/ChicStreet/generate_password.php

if (isset($_POST['password'])) {
    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    echo "<p>Hash: <code>$hash</code></p>";
    echo "<p>UPDATE admin SET password='$hash' WHERE username='admin';</p>";
}
?>
<!DOCTYPE html>
<html>
<head><title>Generate Password Hash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-5" style="max-width:400px">
<h5>Generate Bcrypt Hash</h5>
<p class="text-muted small">Masukkan password baru untuk admin</p>
<form method="POST">
  <input type="text" class="form-control mb-3" name="password" placeholder="Password baru" required>
  <button type="submit" class="btn btn-dark w-100">Generate Hash</button>
</form>
<hr>
<p class="small text-muted">⚠️ Hapus file ini setelah digunakan!</p>
</body>
</html>
