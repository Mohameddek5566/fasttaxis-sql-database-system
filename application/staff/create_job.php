<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
require_once __DIR__ . "/../config/db.php";

// Clients
$clients = [];
$res = $mysqli->query("SELECT clientID, phoneNumber, email FROM Client ORDER BY clientID DESC");
if ($res) { while($r=$res->fetch_assoc()) $clients[]=$r; }

// Drivers (staff + driver)
$drivers = [];
$sql = "SELECT d.staffID AS driverID, s.firstName, s.lastName
        FROM Driver d
        JOIN Staff s ON s.staffID = d.staffID
        ORDER BY s.firstName, s.lastName";
$res = $mysqli->query($sql);
if ($res) { while($r=$res->fetch_assoc()) $drivers[]=$r; }

// Taxis
$taxis = [];
$res = $mysqli->query("SELECT taxiID, taxiModel, licensePlate FROM Taxi ORDER BY taxiID DESC");
if ($res) { while($r=$res->fetch_assoc()) $taxis[]=$r; }

// Contracts (optional)
$contracts = [];
$res = $mysqli->query("SELECT contractNumber, clientID FROM Contract ORDER BY contractNumber DESC");
if ($res) { while($r=$res->fetch_assoc()) $contracts[]=$r; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Job - Staff</title>
  <style>
    body{font-family:Arial;background:#f5f7fb;margin:0;padding:30px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;max-width:980px}
    label{display:block;margin-top:10px;font-weight:700}
    input,select{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px}
    .row{display:flex;gap:14px}
    .row > div{flex:1}
    button{margin-top:16px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;border:0;cursor:pointer}
    a{color:#111827;text-decoration:none}
    .msg{padding:10px;border-radius:10px;margin-bottom:12px}
    .err{background:#fee2e2;border:1px solid #fecaca}
    .ok{background:#dcfce7;border:1px solid #bbf7d0}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Back</a></p>

  <div class="card">
    <h2>Create Job</h2>

    <?php if(isset($_GET["ok"])): ?>
      <div class="msg ok">Job saved successfully.</div>
    <?php endif; ?>
    <?php if(isset($_GET["err"])): ?>
      <div class="msg err"><?php echo htmlspecialchars($_GET["err"]); ?></div>
    <?php endif; ?>

    <form method="post" action="save_job.php">
      <label>Client</label>
      <select name="clientID" required>
        <option value="">-- select client --</option>
        <?php foreach($clients as $c): ?>
          <option value="<?php echo (int)$c["clientID"]; ?>">
            <?php echo "ID ".$c["clientID"]." - ".$c["phoneNumber"]." - ".$c["email"]; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <div class="row">
        <div>
          <label>Driver</label>
          <select name="driverID" required>
            <option value="">-- select driver --</option>
            <?php foreach($drivers as $d): ?>
              <option value="<?php echo (int)$d["driverID"]; ?>">
                <?php echo "Driver ".$d["driverID"]." - ".$d["firstName"]." ".$d["lastName"]; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label>Taxi</label>
          <select name="taxiID" required>
            <option value="">-- select taxi --</option>
            <?php foreach($taxis as $t): ?>
              <option value="<?php echo (int)$t["taxiID"]; ?>">
                <?php echo "Taxi ".$t["taxiID"]." - ".$t["taxiModel"]." (".$t["licensePlate"].")"; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <label>Contract (optional)</label>
      <select name="contractNumber">
        <option value="">-- none (private job) --</option>
        <?php foreach($contracts as $ct): ?>
          <option value="<?php echo (int)$ct["contractNumber"]; ?>">
            <?php echo "Contract ".$ct["contractNumber"]." (clientID ".$ct["clientID"].")"; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Pickup Address</label>
      <input name="pickupAddress" required>

      <label>Dropoff Address</label>
      <input name="dropoffAddress" required>

      <div class="row">
        <div>
          <label>Pickup DateTime</label>
          <input type="datetime-local" name="pickupDateTime" required>
        </div>
        <div>
          <label>Status</label>
          <select name="status" required>
            <option value="Completed">Completed</option>
            <option value="Failed">Failed</option>
            <option value="Ongoing">Ongoing</option>
          </select>
        </div>
      </div>

      <div class="row">
        <div>
          <label>Dropoff DateTime (optional)</label>
          <input type="datetime-local" name="dropoffDateTime">
        </div>
        <div>
          <label>Mileage Used (optional)</label>
          <input type="number" step="0.01" name="mileageUsed">
        </div>
      </div>

      <div class="row">
        <div>
          <label>Charge (optional)</label>
          <input type="number" step="0.01" name="charge">
        </div>
        <div>
          <label>Failure Reason (only if Failed)</label>
          <input name="failureReason" placeholder="e.g., Client not available">
        </div>
      </div>

      <button type="submit">Save Job</button>
    </form>
  </div>
</body>
</html>