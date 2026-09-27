<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Staff Dashboard - FastTaxis</title>
  <style>
    body{font-family:Arial;background:#f5f7fb;margin:0;padding:30px}
    .top{display:flex;justify-content:space-between;align-items:center}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin-top:18px}
    a.btn{display:inline-block;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;text-decoration:none;margin-right:10px}
    a.btn:hover{opacity:.9}
  </style>
</head>
<body>
  <div class="top">
    <h1>FastTaxis Staff Dashboard</h1>
    <div>
      <a class="btn" href="../public/logout.php">Logout</a>
    </div>
  </div>

  <div class="card">
    <p>Welcome, <b><?php echo htmlspecialchars($_SESSION["username"] ?? ""); ?></b> (STAFF)</p>
    <p>Choose an action:</p>

    <a class="btn" href="create_job.php">Create Job</a>
    <a class="btn" href="manage_clients.php">Manage Clients</a>
    <a class="btn" href="create_contract.php">Create Contract</a>
    <a class="btn" href="add_maintenance.php">Add Maintenance</a>
  </div>
</body>
</html>