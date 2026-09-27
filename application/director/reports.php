<?php
require_once __DIR__ . '/../config/auth.php';
requireRole('DIRECTOR');

$reports = [
  'A' => 'Managers at each office',
  'B' => 'Female drivers in Cyberjaya',
  'C' => 'Total staff at each office',
  'D' => 'Taxis in Cyberjaya office',
  'E' => 'Total registered taxis',
  'F' => 'Number of drivers allocated to each taxi',
  'G' => 'Owners with more than one taxi',
  'H' => 'Business clients in Cyberjaya',
  'I' => 'Current contracts for business clients in Kuala Lumpur',
  'K' => 'Jobs by a driver on a given day',
  'L' => 'Drivers over 25 years old',
  'M' => 'Private clients hired in Nov 2025',
  'N' => 'Private clients hired > 3 times',
  'O' => 'Average fee for private jobs',
  'P' => 'Total jobs allocated to each taxi',
  'Q' => 'Total jobs allocated to each driver',
  'R' => 'Total amount charged for each car in Nov 2025',
  'S' => 'Total jobs and km for a given contract',
  'T' => 'Taxis due for maintenance on Jan 1, 2026',
];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Reports - Director</title>
  <style>
    body{font-family:Arial;margin:24px;background:#f6f7fb;}
    .top{display:flex;justify-content:space-between;align-items:center;}
    a.btn{display:inline-block;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;}
    .list{background:#fff;border:1px solid #ddd;border-radius:10px;padding:14px;margin-top:14px;}
    .item{padding:10px;border-bottom:1px solid #eee;}
    .item:last-child{border-bottom:none;}
    a.link{text-decoration:none;color:#111;}
    small{color:#666;}
  </style>
</head>
<body>
  <div class="top">
    <h2>Director Reports</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <div class="list">
    <?php foreach($reports as $code => $title): ?>
      <div class="item">
        <a class="link" href="reports.php?run=<?= urlencode($code) ?>"><b>Report <?= htmlspecialchars($code) ?></b> — <?= htmlspecialchars($title) ?></a>
      </div>
    <?php endforeach; ?>
  </div>

  <?php
  if (!isset($_GET['run'])) exit;

  require_once __DIR__ . '/../config/db.php';

  $run = strtoupper(trim($_GET['run']));
  $sql = "";
  $params = [];
  $types = "";

  if ($run === 'A') {
    $sql = "SELECT o.officeName, s.firstName, s.lastName, s.phoneNumber
            FROM Manager m
            JOIN Staff s ON s.staffID = m.staffID
            JOIN Office o ON o.officeID = s.officeID
            ORDER BY o.officeName, s.lastName";
  } elseif ($run === 'B') {
    $sql = "SELECT s.firstName, s.lastName
            FROM Driver d
            JOIN Staff s ON s.staffID = d.staffID
            JOIN Office o ON o.officeID = s.officeID
            WHERE s.gender='Female' AND o.location='Cyberjaya'";
  } elseif ($run === 'C') {
    $sql = "SELECT o.officeName, COUNT(*) AS totalStaff
            FROM Staff s
            JOIN Office o ON o.officeID = s.officeID
            GROUP BY o.officeID, o.officeName";
  } elseif ($run === 'D') {
    $sql = "SELECT t.taxiID, t.taxiModel, t.licensePlate, t.nextServiceDate
            FROM Taxi t
            JOIN Office o ON o.officeID = t.officeID
            WHERE o.location='Cyberjaya'";
  } elseif ($run === 'E') {
    $sql = "SELECT COUNT(*) AS totalTaxis FROM Taxi";
  } elseif ($run === 'F') {
    $sql = "SELECT t.taxiID, t.licensePlate, COUNT(DISTINCT a.driverID) AS totalDrivers
            FROM Taxi t
            LEFT JOIN DriverTaxiAllocation a ON a.taxiID = t.taxiID
            GROUP BY t.taxiID, t.licensePlate";
  } elseif ($run === 'G') {
    $sql = "SELECT o.ownerID, o.firstName, o.lastName, COUNT(t.taxiID) AS totalTaxis
            FROM Owner o
            JOIN Taxi t ON t.ownerID = o.ownerID
            GROUP BY o.ownerID, o.firstName, o.lastName
            HAVING COUNT(t.taxiID) > 1";
  } elseif ($run === 'H') {
    $sql = "SELECT bc.companyName, c.address, bc.city
            FROM BusinessClient bc
            JOIN Client c ON c.clientID = bc.clientID
            WHERE bc.city='Cyberjaya'";
  } elseif ($run === 'I') {
    $sql = "SELECT ct.contractNumber, bc.companyName, ct.fixedFee, ct.jobLimit, ct.startDate, ct.endDate
            FROM Contract ct
            JOIN BusinessClient bc ON bc.clientID = ct.clientID
            WHERE bc.city='Kuala Lumpur'
              AND CURDATE() BETWEEN ct.startDate AND ct.endDate";
  } elseif ($run === 'K') {
    $driverID = isset($_GET['driverID']) ? (int)$_GET['driverID'] : 7;
    $day = isset($_GET['day']) ? $_GET['day'] : '2025-11-03';
    $sql = "SELECT jobID, pickupDateTime, dropoffDateTime, pickupAddress, dropoffAddress, mileageUsed, charge, status
            FROM Job
            WHERE driverID=? AND DATE(pickupDateTime)=?";
    $types = "is";
    $params = [$driverID, $day];
  } elseif ($run === 'L') {
    $sql = "SELECT s.firstName, s.lastName, TIMESTAMPDIFF(YEAR, s.DOB, CURDATE()) AS age
            FROM Driver d
            JOIN Staff s ON s.staffID = d.staffID
            WHERE TIMESTAMPDIFF(YEAR, s.DOB, CURDATE()) > 25";
  } elseif ($run === 'M') {
    $sql = "SELECT DISTINCT pc.firstName, pc.lastName, c.phoneNumber
            FROM Job j
            JOIN PrivateClient pc ON pc.clientID = j.clientID
            JOIN Client c ON c.clientID = pc.clientID
            WHERE j.pickupDateTime BETWEEN '2025-11-01' AND '2025-11-30'";
  } elseif ($run === 'N') {
    $sql = "SELECT pc.firstName, pc.lastName, c.address, COUNT(j.jobID) AS totalHires
            FROM PrivateClient pc
            JOIN Client c ON c.clientID = pc.clientID
            JOIN Job j ON j.clientID = pc.clientID
            GROUP BY pc.clientID, pc.firstName, pc.lastName, c.address
            HAVING COUNT(j.jobID) > 3";
  } elseif ($run === 'O') {
    $sql = "SELECT AVG(j.charge) AS averagePrivateCharge
            FROM Job j
            JOIN PrivateClient pc ON pc.clientID = j.clientID
            WHERE j.charge IS NOT NULL AND j.status='Completed'";
  } elseif ($run === 'P') {
    $sql = "SELECT t.taxiID, t.licensePlate, COUNT(j.jobID) AS totalJobs
            FROM Taxi t
            LEFT JOIN Job j ON j.taxiID = t.taxiID
            GROUP BY t.taxiID, t.licensePlate";
  } elseif ($run === 'Q') {
    $sql = "SELECT d.staffID AS driverID, s.firstName, s.lastName, COUNT(j.jobID) AS totalJobs
            FROM Driver d
            JOIN Staff s ON s.staffID = d.staffID
            LEFT JOIN Job j ON j.driverID = d.staffID
            GROUP BY d.staffID, s.firstName, s.lastName";
  } elseif ($run === 'R') {
    $sql = "SELECT t.taxiID, t.licensePlate, SUM(COALESCE(j.charge,0)) AS totalAmount
            FROM Taxi t
            JOIN Job j ON j.taxiID = t.taxiID
            WHERE j.pickupDateTime BETWEEN '2025-11-01' AND '2025-11-30'
            GROUP BY t.taxiID, t.licensePlate";
  } elseif ($run === 'S') {
    $contract = isset($_GET['contractNumber']) ? (int)$_GET['contractNumber'] : 1;
    $sql = "SELECT j.contractNumber, COUNT(j.jobID) AS totalJobs, SUM(COALESCE(j.mileageUsed,0)) AS totalKM
            FROM Job j
            WHERE j.contractNumber = ?
            GROUP BY j.contractNumber";
    $types = "i";
    $params = [$contract];
  } elseif ($run === 'T') {
    $sql = "SELECT taxiID, licensePlate, taxiModel, nextServiceDate
            FROM Taxi
            WHERE nextServiceDate = '2026-01-01'";
  } else {
    echo "<p><b>Unknown report.</b></p>";
    exit;
  }

  echo "<h3 style='margin-top:18px;'>Running report $run</h3>";

  if ($types !== "") {
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
  } else {
    $result = $mysqli->query($sql);
  }

  if (!$result) { echo "<p><b>SQL error:</b> ".htmlspecialchars($mysqli->error)."</p>"; exit; }

  echo "<div style='background:#fff;border:1px solid #ddd;border-radius:10px;padding:14px;margin-top:10px;overflow:auto;'>";
  echo "<table style='border-collapse:collapse;width:100%;'>";
  echo "<tr>";
  foreach ($result->fetch_fields() as $f) {
    echo "<th style='text-align:left;border-bottom:1px solid #eee;padding:8px;background:#111;color:#fff;'>".htmlspecialchars($f->name)."</th>";
  }
  echo "</tr>";

  while($r = $result->fetch_assoc()){
    echo "<tr>";
    foreach($r as $v){
      echo "<td style='border-bottom:1px solid #eee;padding:8px;'>".htmlspecialchars((string)$v)."</td>";
    }
    echo "</tr>";
  }
  echo "</table></div>";
  ?>
</body>
</html>