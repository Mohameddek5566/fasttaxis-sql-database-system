<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
require_once __DIR__ . "/../config/db.php";

function h($s){ return htmlspecialchars($s ?? ""); }

$msg = $_GET["msg"] ?? "";

$businessClients = [];
$sql = "SELECT bc.clientID, bc.companyName, bc.city, c.phoneNumber
        FROM BusinessClient bc
        JOIN Client c ON c.clientID = bc.clientID
        ORDER BY bc.companyName";
$res = $mysqli->query($sql);
if($res){ while($r=$res->fetch_assoc()) $businessClients[]=$r; }

if($_SERVER["REQUEST_METHOD"] === "POST"){
  $clientID = (int)($_POST["clientID"] ?? 0);
  $fixedFee = (float)($_POST["fixedFee"] ?? 0);
  $jobLimit = (int)($_POST["jobLimit"] ?? 0);
  $startDate = trim($_POST["startDate"] ?? "");
  $endDate = trim($_POST["endDate"] ?? "");

  if($clientID<=0 || $fixedFee<=0 || $jobLimit<=0 || $startDate==="" || $endDate===""){
    header("Location: create_contract.php?msg=".urlencode("Please fill all fields correctly."));
    exit;
  }

  $stmt = $mysqli->prepare("INSERT INTO Contract (clientID, fixedFee, jobLimit, startDate, endDate) VALUES (?,?,?,?,?)");
  $stmt->bind_param("idiss", $clientID, $fixedFee, $jobLimit, $startDate, $endDate);

  if(!$stmt->execute()){
    header("Location: create_contract.php?msg=".urlencode("Insert failed: ".$stmt->error));
    exit;
  }

  header("Location: create_contract.php?msg=".urlencode("Contract created successfully."));
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Create Contract - Staff</title>
  <style>
    body{font-family:Arial;background:#f5f7fb;margin:0;padding:30px}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;max-width:900px}
    label{display:block;margin-top:10px;font-weight:700}
    input,select{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px}
    button{margin-top:14px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;border:0;cursor:pointer}
    .msg{padding:10px;border-radius:10px;background:#dcfce7;border:1px solid #bbf7d0;margin-bottom:10px}
    a{color:#111827;text-decoration:none}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Back</a></p>
  <div class="card">
    <h2>Create Contract (Business Clients)</h2>
    <?php if($msg!==""): ?><div class="msg"><?php echo h($msg); ?></div><?php endif; ?>

    <form method="post">
      <label>Business Client</label>
      <select name="clientID" required>
        <option value="">-- select business client --</option>
        <?php foreach($businessClients as $b): ?>
          <option value="<?php echo (int)$b["clientID"]; ?>">
            <?php echo "ID ".$b["clientID"]." - ".$b["companyName"]." (".$b["city"].") - ".$b["phoneNumber"]; ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label>Fixed Fee (RM)</label>
      <input type="number" step="0.01" name="fixedFee" required>

      <label>Job Limit</label>
      <input type="number" name="jobLimit" required>

      <label>Start Date</label>
      <input type="date" name="startDate" required>

      <label>End Date</label>
      <input type="date" name="endDate" required>

      <button type="submit">Create Contract</button>
    </form>
  </div>
</body>
</html>