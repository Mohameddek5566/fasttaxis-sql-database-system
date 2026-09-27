<?php
require_once __DIR__ . '/../config/auth.php';
requireRole('DIRECTOR');

require_once __DIR__ . '/../config/db.php';

// last 50 jobs with client + driver name + taxi plate
$sql = "
SELECT 
  j.jobID,
  j.pickupDateTime,
  j.status,
  c.clientID,
  c.phoneNumber,
  s.firstName AS driverFirst,
  s.lastName  AS driverLast,
  t.licensePlate,
  j.pickupAddress,
  j.dropoffAddress,
  j.charge
FROM Job j
JOIN Client c ON c.clientID = j.clientID
JOIN Driver d ON d.staffID = j.driverID
JOIN Staff s ON s.staffID = d.staffID
JOIN Taxi t ON t.taxiID = j.taxiID
ORDER BY j.pickupDateTime DESC
LIMIT 50
";
$res = $mysqli->query($sql);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>All Jobs - Director</title>
  <style>
    body{font-family:Arial;margin:24px;background:#f6f7fb;}
    .top{display:flex;justify-content:space-between;align-items:center;}
    table{width:100%;border-collapse:collapse;background:#fff;border:1px solid #ddd;}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;vertical-align:top;}
    th{background:#111;color:#fff;}
    a.btn{display:inline-block;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;}
  </style>
</head>
<body>
  <div class="top">
    <h2>All Jobs (Latest 50)</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>JobID</th>
        <th>Pickup Time</th>
        <th>Status</th>
        <th>Client</th>
        <th>Driver</th>
        <th>Taxi</th>
        <th>Pickup</th>
        <th>Dropoff</th>
        <th>Charge</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($res && $res->num_rows > 0): ?>
        <?php while($row = $res->fetch_assoc()): ?>
          <tr>
            <td><?= (int)$row['jobID'] ?></td>
            <td><?= htmlspecialchars($row['pickupDateTime']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>ID <?= (int)$row['clientID'] ?><br><?= htmlspecialchars($row['phoneNumber']) ?></td>
            <td><?= htmlspecialchars($row['driverFirst'].' '.$row['driverLast']) ?></td>
            <td><?= htmlspecialchars($row['licensePlate']) ?></td>
            <td><?= htmlspecialchars($row['pickupAddress']) ?></td>
            <td><?= htmlspecialchars($row['dropoffAddress']) ?></td>
            <td><?= $row['charge'] === null ? '-' : htmlspecialchars($row['charge']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="9">No job records found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>