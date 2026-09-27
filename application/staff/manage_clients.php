<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
require_once __DIR__ . "/../config/db.php";

function h($s){ return htmlspecialchars($s ?? ""); }

$action = $_GET["action"] ?? "";
$type = $_GET["type"] ?? ""; // private|business
$id = (int)($_GET["id"] ?? 0);

// Handle create/update
if($_SERVER["REQUEST_METHOD"] === "POST"){
  $mode = $_POST["mode"] ?? "create"; // create|update
  $clientType = $_POST["clientType"] ?? "";

  $phone = trim($_POST["phoneNumber"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $address = trim($_POST["address"] ?? "");

  if($phone==="" || $address===""){
    header("Location: manage_clients.php?msg=".urlencode("Phone and address required."));
    exit;
  }

  if($mode === "create"){
    // 1) insert into Client
    $stmt = $mysqli->prepare("INSERT INTO Client (phoneNumber, email, address) VALUES (?,?,?)");
    $stmt->bind_param("sss", $phone, $email, $address);
    if(!$stmt->execute()){
      header("Location: manage_clients.php?msg=".urlencode("Client insert failed: ".$stmt->error));
      exit;
    }
    $newClientID = $mysqli->insert_id;

    if($clientType === "private"){
      $first = trim($_POST["firstName"] ?? "");
      $last  = trim($_POST["lastName"] ?? "");
      $dateFirst = trim($_POST["dateFirstBooking"] ?? "");
      $notes = trim($_POST["notes"] ?? "");

      $stmt = $mysqli->prepare("INSERT INTO PrivateClient (clientID, firstName, lastName, dateFirstBooking, notes) VALUES (?,?,?,?,?)");
      $stmt->bind_param("issss", $newClientID, $first, $last, $dateFirst, $notes);
      if(!$stmt->execute()){
        header("Location: manage_clients.php?msg=".urlencode("PrivateClient insert failed: ".$stmt->error));
        exit;
      }
    } else if($clientType === "business"){
      $company = trim($_POST["companyName"] ?? "");
      $reg     = trim($_POST["businessRegNumber"] ?? "");
      $contact = trim($_POST["contactPerson"] ?? "");
      $fax     = trim($_POST["faxNumber"] ?? "");
      $city    = trim($_POST["city"] ?? "");

      $stmt = $mysqli->prepare("INSERT INTO BusinessClient (clientID, companyName, businessRegNumber, contactPerson, faxNumber, city) VALUES (?,?,?,?,?,?)");
      $stmt->bind_param("isssss", $newClientID, $company, $reg, $contact, $fax, $city);
      if(!$stmt->execute()){
        header("Location: manage_clients.php?msg=".urlencode("BusinessClient insert failed: ".$stmt->error));
        exit;
      }
    }

    header("Location: manage_clients.php?msg=".urlencode("Client created successfully."));
    exit;
  }

  if($mode === "update"){
    $clientID = (int)($_POST["clientID"] ?? 0);

    $stmt = $mysqli->prepare("UPDATE Client SET phoneNumber=?, email=?, address=? WHERE clientID=?");
    $stmt->bind_param("sssi", $phone, $email, $address, $clientID);
    if(!$stmt->execute()){
      header("Location: manage_clients.php?msg=".urlencode("Client update failed: ".$stmt->error));
      exit;
    }

    if($clientType === "private"){
      $first = trim($_POST["firstName"] ?? "");
      $last  = trim($_POST["lastName"] ?? "");
      $dateFirst = trim($_POST["dateFirstBooking"] ?? "");
      $notes = trim($_POST["notes"] ?? "");

      $stmt = $mysqli->prepare("UPDATE PrivateClient SET firstName=?, lastName=?, dateFirstBooking=?, notes=? WHERE clientID=?");
      $stmt->bind_param("ssssi", $first, $last, $dateFirst, $notes, $clientID);
      if(!$stmt->execute()){
        header("Location: manage_clients.php?msg=".urlencode("PrivateClient update failed: ".$stmt->error));
        exit;
      }
    } else if($clientType === "business"){
      $company = trim($_POST["companyName"] ?? "");
      $reg     = trim($_POST["businessRegNumber"] ?? "");
      $contact = trim($_POST["contactPerson"] ?? "");
      $fax     = trim($_POST["faxNumber"] ?? "");
      $city    = trim($_POST["city"] ?? "");

      $stmt = $mysqli->prepare("UPDATE BusinessClient SET companyName=?, businessRegNumber=?, contactPerson=?, faxNumber=?, city=? WHERE clientID=?");
      $stmt->bind_param("sssssi", $company, $reg, $contact, $fax, $city, $clientID);
      if(!$stmt->execute()){
        header("Location: manage_clients.php?msg=".urlencode("BusinessClient update failed: ".$stmt->error));
        exit;
      }
    }

    header("Location: manage_clients.php?msg=".urlencode("Client updated successfully."));
    exit;
  }
}

// Load edit data if needed
$edit = null;
if($action === "edit" && $id>0 && in_array($type, ["private","business"], true)){
  $stmt = $mysqli->prepare("SELECT clientID, phoneNumber, email, address FROM Client WHERE clientID=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $editClient = $stmt->get_result()->fetch_assoc();

  if($type === "private"){
    $stmt = $mysqli->prepare("SELECT firstName,lastName,dateFirstBooking,notes FROM PrivateClient WHERE clientID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $sub = $stmt->get_result()->fetch_assoc();
  } else {
    $stmt = $mysqli->prepare("SELECT companyName,businessRegNumber,contactPerson,faxNumber,city FROM BusinessClient WHERE clientID=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $sub = $stmt->get_result()->fetch_assoc();
  }

  if($editClient){
    $edit = ["type"=>$type, "client"=>$editClient, "sub"=>$sub];
  }
}

// Lists
$private = [];
$sql = "SELECT c.clientID, c.phoneNumber, c.email, c.address, pc.firstName, pc.lastName
        FROM PrivateClient pc
        JOIN Client c ON c.clientID = pc.clientID
        ORDER BY c.clientID DESC";
$res = $mysqli->query($sql);
if($res){ while($r=$res->fetch_assoc()) $private[]=$r; }

$business = [];
$sql = "SELECT c.clientID, c.phoneNumber, c.email, c.address, bc.companyName, bc.city
        FROM BusinessClient bc
        JOIN Client c ON c.clientID = bc.clientID
        ORDER BY c.clientID DESC";
$res = $mysqli->query($sql);
if($res){ while($r=$res->fetch_assoc()) $business[]=$r; }

$msg = $_GET["msg"] ?? "";
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Clients - Staff</title>
  <style>
    body{font-family:Arial;background:#f5f7fb;margin:0;padding:30px}
    .top a{color:#111827;text-decoration:none}
    .card{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:18px;margin-top:16px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    label{display:block;margin-top:10px;font-weight:700}
    input,select,textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:10px}
    textarea{height:80px}
    button{margin-top:14px;padding:10px 14px;border-radius:10px;background:#111827;color:#fff;border:0;cursor:pointer}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border-bottom:1px solid #e5e7eb;padding:8px;text-align:left}
    .pill{display:inline-block;padding:4px 10px;border-radius:999px;background:#eef2ff}
    .msg{padding:10px;border-radius:10px;background:#dcfce7;border:1px solid #bbf7d0;margin-top:10px}
    .linkbtn{display:inline-block;padding:6px 10px;border-radius:10px;background:#111827;color:#fff;text-decoration:none}
  </style>
</head>
<body>
  <div class="top">
    <p><a href="dashboard.php">← Back</a></p>
  </div>

  <h2>Manage Clients</h2>
  <?php if($msg!==""): ?><div class="msg"><?php echo h($msg); ?></div><?php endif; ?>

  <div class="card">
    <h3><?php echo $edit ? "Edit ".strtoupper($edit["type"])." Client (ID ".$edit["client"]["clientID"].")" : "Add New Client"; ?></h3>

    <form method="post">
      <input type="hidden" name="mode" value="<?php echo $edit ? "update" : "create"; ?>">
      <?php if($edit): ?>
        <input type="hidden" name="clientID" value="<?php echo (int)$edit["client"]["clientID"]; ?>">
      <?php endif; ?>

      <label>Client Type</label>
      <select name="clientType" required <?php echo $edit ? "readonly disabled" : ""; ?>>
        <option value="">-- select --</option>
        <option value="private" <?php echo ($edit && $edit["type"]==="private") ? "selected" : ""; ?>>Private</option>
        <option value="business" <?php echo ($edit && $edit["type"]==="business") ? "selected" : ""; ?>>Business</option>
      </select>
      <?php if($edit): ?>
        <input type="hidden" name="clientType" value="<?php echo h($edit["type"]); ?>">
      <?php endif; ?>

      <div class="grid">
        <div>
          <label>Phone Number</label>
          <input name="phoneNumber" required value="<?php echo h($edit["client"]["phoneNumber"] ?? ""); ?>">
        </div>
        <div>
          <label>Email (optional)</label>
          <input name="email" value="<?php echo h($edit["client"]["email"] ?? ""); ?>">
        </div>
      </div>

      <label>Address</label>
      <input name="address" required value="<?php echo h($edit["client"]["address"] ?? ""); ?>">

      <?php if(!$edit): ?>
        <p class="pill" style="margin-top:12px">After selecting type, fill the related fields below:</p>
      <?php endif; ?>

      <div class="grid">
        <div class="card">
          <h4>Private fields</h4>
          <label>First Name</label>
          <input name="firstName" value="<?php echo h($edit["sub"]["firstName"] ?? ""); ?>">
          <label>Last Name</label>
          <input name="lastName" value="<?php echo h($edit["sub"]["lastName"] ?? ""); ?>">
          <label>Date First Booking</label>
          <input type="date" name="dateFirstBooking" value="<?php echo h($edit["sub"]["dateFirstBooking"] ?? ""); ?>">
          <label>Notes</label>
          <textarea name="notes"><?php echo h($edit["sub"]["notes"] ?? ""); ?></textarea>
        </div>

        <div class="card">
          <h4>Business fields</h4>
          <label>Company Name</label>
          <input name="companyName" value="<?php echo h($edit["sub"]["companyName"] ?? ""); ?>">
          <label>Business Reg Number</label>
          <input name="businessRegNumber" value="<?php echo h($edit["sub"]["businessRegNumber"] ?? ""); ?>">
          <label>Contact Person</label>
          <input name="contactPerson" value="<?php echo h($edit["sub"]["contactPerson"] ?? ""); ?>">
          <label>Fax Number (optional)</label>
          <input name="faxNumber" value="<?php echo h($edit["sub"]["faxNumber"] ?? ""); ?>">
          <label>City</label>
          <input name="city" value="<?php echo h($edit["sub"]["city"] ?? ""); ?>">
        </div>
      </div>

      <button type="submit"><?php echo $edit ? "Update Client" : "Create Client"; ?></button>
      <?php if($edit): ?>
        <a class="linkbtn" style="margin-left:10px" href="manage_clients.php">Cancel Edit</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="card">
    <h3>Private Clients</h3>
    <table>
      <tr>
        <th>ID</th><th>Name</th><th>Phone</th><th>Email</th><th>Action</th>
      </tr>
      <?php foreach($private as $p): ?>
        <tr>
          <td><?php echo (int)$p["clientID"]; ?></td>
          <td><?php echo h($p["firstName"]." ".$p["lastName"]); ?></td>
          <td><?php echo h($p["phoneNumber"]); ?></td>
          <td><?php echo h($p["email"]); ?></td>
          <td>
            <a class="linkbtn" href="manage_clients.php?action=edit&type=private&id=<?php echo (int)$p["clientID"]; ?>">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="card">
    <h3>Business Clients</h3>
    <table>
      <tr>
        <th>ID</th><th>Company</th><th>City</th><th>Phone</th><th>Action</th>
      </tr>
      <?php foreach($business as $b): ?>
        <tr>
          <td><?php echo (int)$b["clientID"]; ?></td>
          <td><?php echo h($b["companyName"]); ?></td>
          <td><?php echo h($b["city"]); ?></td>
          <td><?php echo h($b["phoneNumber"]); ?></td>
          <td>
            <a class="linkbtn" href="manage_clients.php?action=edit&type=business&id=<?php echo (int)$b["clientID"]; ?>">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>