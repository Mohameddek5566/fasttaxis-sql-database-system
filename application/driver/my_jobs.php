<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
requireRole("DRIVER");

$driverID = $_SESSION["user"]["staffID"] ?? null;
if (!$driverID) {
    die("Driver account is not linked to staffID in users table.");
}

// Assigned jobs (include Client phone/email)
$sql = "
SELECT
  j.jobID,
  j.pickupAddress, j.dropoffAddress,
  j.pickupDateTime, j.dropoffDateTime,
  j.mileageUsed, j.charge, j.status, j.failureReason,
  c.phoneNumber, c.email
FROM Job j
JOIN Client c ON c.clientID = j.clientID
WHERE j.driverID = ?
ORDER BY j.pickupDateTime DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $driverID);
$stmt->execute();
$res = $stmt->get_result();
$jobs = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Jobs - FastTaxis</title>
  <style>
    body{font-family:Arial, sans-serif;background:#f5f7fb;margin:0;padding:30px}
    .top{display:flex;justify-content:space-between;align-items:center}
    table{width:100%;border-collapse:collapse;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.06)}
    th,td{padding:12px;border-bottom:1px solid #eee;text-align:left;vertical-align:top}
    th{background:#111;color:#fff}
    .btn{padding:10px 14px;border-radius:10px;background:#111;color:#fff;border:none;cursor:pointer}
    .small{width:110px}
    input,select{padding:8px;border-radius:8px;border:1px solid #ccc}
    .back{display:inline-block;margin-bottom:15px;text-decoration:none}
    .tag{display:inline-block;padding:4px 8px;border-radius:999px;background:#eee}
  </style>
</head>
<body>
  <div class="top">
    <h1>My Assigned Jobs</h1>
    <a class="back" href="/fasttaxis_app/driver/dashboard.php">← Back</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>Job</th>
        <th>Pickup → Dropoff</th>
        <th>Pickup Time</th>
        <th>Status</th>
        <th>Update</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($jobs)): ?>
      <tr><td colspan="5">No jobs found for this driver.</td></tr>
    <?php else: foreach ($jobs as $j): ?>
      <tr>
        <td>
          <b>#<?php echo (int)$j["jobID"]; ?></b><br>
          <span class="tag"><?php echo htmlspecialchars($j["phoneNumber"]); ?></span><br>
          <?php if (!empty($j["email"])) echo htmlspecialchars($j["email"]); ?>
        </td>
        <td>
          <b>From:</b> <?php echo htmlspecialchars($j["pickupAddress"]); ?><br>
          <b>To:</b> <?php echo htmlspecialchars($j["dropoffAddress"]); ?>
        </td>
        <td><?php echo htmlspecialchars($j["pickupDateTime"]); ?></td>
        <td>
          <b><?php echo htmlspecialchars($j["status"]); ?></b><br>
          <?php
            if (!empty($j["mileageUsed"])) echo "KM: " . htmlspecialchars($j["mileageUsed"]) . "<br>";
            if (!empty($j["failureReason"])) echo "Reason: " . htmlspecialchars($j["failureReason"]);
          ?>
        </td>

        <td>
          <form method="post" action="/fasttaxis_app/driver/update_job_status.php">
            <input type="hidden" name="jobID" value="<?php echo (int)$j["jobID"]; ?>">

            <div style="margin-bottom:8px">
              <select name="status" required>
                <option value="Completed">Completed</option>
                <option value="Failed">Failed</option>
              </select>
            </div>

            <div style="margin-bottom:8px">
              <input class="small" type="number" step="0.01" name="mileageUsed" placeholder="KM used">
            </div>

            <div style="margin-bottom:8px">
              <input type="text" name="failureReason" placeholder="Failure reason (if failed)">
            </div>

            <button class="btn" type="submit">Save</button>
          </form>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</body>
</html>