<?php
// public/login.php
session_start();
if (isset($_SESSION["user"])) {
    // already logged in, go to correct dashboard
    $role = $_SESSION["user"]["role"];
    if ($role === "DIRECTOR") header("Location: /fasttaxis_app/director/dashboard.php");
    if ($role === "STAFF")    header("Location: /fasttaxis_app/staff/dashboard.php");
    if ($role === "DRIVER")   header("Location: /fasttaxis_app/driver/dashboard.php");
    if ($role === "OWNER")    header("Location: /fasttaxis_app/owner/dashboard.php");
    exit;
}

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>FastTaxis Login</title>
  <style>
    body { font-family: Arial; background:#f6f6f6; }
    .box { width:360px; margin:80px auto; background:#fff; padding:18px; border-radius:10px; border:1px solid #ddd; }
    input { width:100%; padding:10px; margin:8px 0; }
    button { width:100%; padding:10px; cursor:pointer; }
    .err { color:#b00020; margin:10px 0; }
  </style>
</head>
<body>
<div class="box">
  <h2>FastTaxis System Login</h2>

  <?php if ($error): ?>
    <div class="err"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form method="POST" action="/fasttaxis_app/public/login_process.php">
    <label>Username</label>
    <input type="text" name="username" required>

    <label>Password</label>
    <input type="password" name="password" required>

    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>