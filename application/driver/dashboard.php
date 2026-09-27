<?php
require_once __DIR__ . "/../config/auth.php";
requireRole("DRIVER");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Driver Dashboard - FastTaxis</title>
  <style>
    body{font-family:Arial, sans-serif;background:#f5f7fb;margin:0;padding:30px}
    .top{display:flex;justify-content:space-between;align-items:center}
    .card{background:#fff;border-radius:14px;padding:20px;max-width:900px;box-shadow:0 2px 10px rgba(0,0,0,.06);margin-top:20px}
    .btn{display:inline-block;padding:12px 16px;border-radius:10px;background:#111;color:#fff;text-decoration:none;margin-right:10px}
    .btn2{display:inline-block;padding:12px 16px;border-radius:10px;background:#444;color:#fff;text-decoration:none}
  </style>
</head>
<body>
  <div class="top">
    <h1>FastTaxis Driver Dashboard</h1>
    <a class="btn2" href="/fasttaxis_app/public/logout.php">Logout</a>
  </div>

  <div class="card">
    <p><b>Welcome,</b> <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?> (DRIVER)</p>
    <p>Choose an action:</p>

    <a class="btn" href="/fasttaxis_app/driver/my_jobs.php">My Assigned Jobs</a>
  </div>
</body>
</html>