<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
require_once __DIR__ . "/../config/db.php";

function h($s){ return htmlspecialchars($s ?? ""); }

$msg = $_GET["msg"] ?? "";

// taxis list
$taxis = [];
$res = $mysqli->query("SELECT taxiID, taxiModel, licensePlate FROM Taxi ORDER BY taxiID DESC");
if($res){ while($r=$res->fetch_assoc()) $taxis[]=$r; }

if($_SERVER["REQUEST_METHOD"] === "POST"){
  $taxiID = (int)($_POST["taxiID"] ?? 0);
  $serviceDate = trim($_POST["serviceDate"] ?? "");
  $nextServiceDate = trim($_POST["nextServiceDate"] ?? "");
  $description = trim($_POST["description"] ?? "");

  if($taxiID<=0 || $serviceDate==="" || $nextServiceDate===""){
    header("Location: add_maintenance.php?msg=".urlencode("Please fill taxi, service date, next service date."));
    exit;
  }

  $stmt = $mysqli->prepare("INSERT INTO Maintenance (taxiID, serviceDate, nextServiceDate, description) VALUES (?,?,?,?)");
  $stmt->bind_param("isss", $taxiID, $serviceDate, $nextServiceDate, $description);

  if(!$stmt->execute()){
    header("Location: add_maintenance.php?msg=".urlencode("Insert failed: ".$stmt->error));
    exit;
  }

  header("Location: add_maintenance.php?msg=".urlencode("Maintenance added successfully."));
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Maintenance - Staff</title>
  <style>
    body{font-family:Arial;background:#f5f7fb;margin:0;padding:30px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;max-width:900px}
    label{display:block;margin-top:10px;font-weight:700}
    input,select,textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px}
    textarea{height:80px}
    button{margin-top:14px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;border:0;cursor:pointer}
    .msg{padding:10px;border-radius:10px;background:#dcfce7;border:1px solid #bbf7d0;margin-bottom:10px}
    a{color:#111827;text-decoration:none}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Back</a></p>

  <div class="card">
    <h2>Add Maintenance Record</h2>
    <?php if($msg!==""): ?><div class="msg"><?php echo h($msg); ?></div><?php endif; ?>

    <form method="post">
      <label>Taxi</label>
      <select name="taxiID" required>
        <option value="">-- select taxi --</option>
        <?php foreach($taxis as $t): ?>
          <option value="<?php echo (int)$t["taxiID"]; ?>">
            <?php echo "Taxi ".$t["taxiID"]." - ".$t["taxiModel"]." (".$t["licensePlate"].")"; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Service Date</label>
      <input type="date" name="serviceDate" required>

      <label>Next Service Date</label>
      <input type="date" name="nextServiceDate" required>

      <label>Description (optional)</label>
      <textarea name="description" placeholder="e.g., Oil change, Brake check"></textarea>

      <button type="submit">Save Maintenance</button>
    </form>
  </div>
</body>
</html>