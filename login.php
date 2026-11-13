<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare('SELECT id, name, password_hash FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Prevent session fixation
            session_regenerate_id(true);
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            header('Location: ../admin/dashboard.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login — LedgerFlow</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-body">
  <div class="auth-card">
    <div class="auth-brand">Ledger<span>Flow</span></div>
    <p class="auth-sub">Sign in to your billing dashboard</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="auth-form">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required autofocus>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" required>

      <button type="submit" class="btn-primary">Sign in</button>
    </form>
  </div>
</body>
</html>
