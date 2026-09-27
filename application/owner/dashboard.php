<?php
require_once __DIR__ . "/../config/auth.php";
requireRole("OWNER");
?>

<!DOCTYPE html>
<html>
<head>
<title>Taxi Owner Dashboard</title>
<style>
body{font-family:Arial;background:#f5f7fb;padding:30px}
.card{background:white;padding:20px;border-radius:10px}
.btn{display:inline-block;padding:12px 16px;background:#111;color:white;border-radius:8px;text-decoration:none;margin-right:10px}
</style>
</head>

<body>

<h1>FastTaxis Owner Dashboard</h1>

<div class="card">
<p>Welcome <?php echo htmlspecialchars($_SESSION["user"]["username"]); ?> (OWNER)</p>

<a class="btn" href="/fasttaxis_app/owner/my_taxis.php">My Taxis</a>
<a class="btn" href="/fasttaxis_app/owner/maintenance_history.php">Maintenance History</a>

<a class="btn" href="/fasttaxis_app/public/logout.php">Logout</a>
</div>

</body>
</html>