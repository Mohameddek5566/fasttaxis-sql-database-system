<?php
require_once __DIR__ . '/../config/auth.php';
requireRole('DIRECTOR');

require_once __DIR__ . '/../config/db.php';

function getSingleValue(mysqli $mysqli, string $sql): string {
  $res = $mysqli->query($sql);
  if (!$res) return "0";
  $row = $res->fetch_row();
  return $row ? (string)$row[0] : "0";
}

$totalOffices  = getSingleValue($mysqli, "SELECT COUNT(*) FROM Office");
$totalStaff    = getSingleValue($mysqli, "SELECT COUNT(*) FROM Staff");
$totalDrivers  = getSingleValue($mysqli, "SELECT COUNT(*) FROM Driver");
$totalTaxis    = getSingleValue($mysqli, "SELECT COUNT(*) FROM Taxi");
$totalClients  = getSingleValue($mysqli, "SELECT COUNT(*) FROM Client");
$totalJobs     = getSingleValue($mysqli, "SELECT COUNT(*) FROM Job");

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Director Dashboard - FastTaxis</title>
  <style>
    body{font-family:Arial;margin:24px;background:#f6f7fb;}
    .top{display:flex;justify-content:space-between;align-items:center;}
    .cardwrap{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-top:18px;}
    .card{background:#fff;border:1px solid #ddd;border-radius:10px;padding:14px;}
    .num{font-size:28px;font-weight:700;margin-top:8px;}
    a.btn{display:inline-block;margin-right:10px;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;}
    a.link{color:#111;}
  </style>
</head>
<body>
  <div class="top">
    <h2>FastTaxis Director Dashboard</h2>
    <div>
      <a class="btn" href="reports.php">Reports</a>
      <a class="btn" href="manage_users.php">Manage Users</a>
      <a class="btn" href="view_all_jobs.php">View Jobs</a>
      <a class="btn" href="../public/logout.php">Logout</a>
    </div>
  </div>

  <div class="cardwrap">
    <div class="card"><div>Offices</div><div class="num"><?= htmlspecialchars($totalOffices) ?></div></div>
    <div class="card"><div>Staff</div><div class="num"><?= htmlspecialchars($totalStaff) ?></div></div>
    <div class="card"><div>Drivers</div><div class="num"><?= htmlspecialchars($totalDrivers) ?></div></div>
    <div class="card"><div>Taxis</div><div class="num"><?= htmlspecialchars($totalTaxis) ?></div></div>
    <div class="card"><div>Clients</div><div class="num"><?= htmlspecialchars($totalClients) ?></div></div>
    <div class="card"><div>Jobs</div><div class="num"><?= htmlspecialchars($totalJobs) ?></div></div>
  </div>

  <p style="margin-top:18px;">
    Use the buttons above to manage the system (users, reports, and job records).
  </p>
</body>
</html>