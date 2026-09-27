<?php
require_once __DIR__ . '/../config/auth.php';
requireRole('DIRECTOR');

require_once __DIR__ . '/../config/db.php';

$msg = "";

// Toggle active
if (isset($_GET['toggle'])) {
  $userID = (int)$_GET['toggle'];

  // flip isActive (1->0, 0->1)
  $stmt = $mysqli->prepare("UPDATE users SET isActive = IF(isActive=1,0,1) WHERE userID=?");
  $stmt->bind_param("i", $userID);
  $stmt->execute();
  $stmt->close();

  header("Location: manage_users.php");
  exit;
}

// Create user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? "");
  $password = $_POST['password'] ?? "";
  $role     = strtoupper(trim($_POST['role'] ?? ""));

  if ($username === "" || $password === "" || $role === "") {
    $msg = "Please fill username, password, and role.";
  } else {
    $hash = password_hash($password, PASSWORD_BCRYPT);

    // staffID / ownerID optional
    $staffID = $_POST['staffID'] !== "" ? (int)$_POST['staffID'] : null;
    $ownerID = $_POST['ownerID'] !== "" ? (int)$_POST['ownerID'] : null;

    $stmt = $mysqli->prepare("INSERT INTO users (username, passwordHash, role, staffID, ownerID, isActive)
                              VALUES (?, ?, ?, ?, ?, 1)");
    // bind nulls safely
    $stmt->bind_param(
      "sssii",
      $username,
      $hash,
      $role,
      $staffID,
      $ownerID
    );

    if ($stmt->execute()) {
      $msg = "User created successfully.";
    } else {
      $msg = "Error: " . $stmt->error;
    }
    $stmt->close();
  }
}

$res = $mysqli->query("SELECT userID, username, role, staffID, ownerID, isActive FROM users ORDER BY userID DESC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Manage Users - Director</title>
  <style>
    body{font-family:Arial;margin:24px;background:#f6f7fb;}
    .top{display:flex;justify-content:space-between;align-items:center;}
    a.btn{display:inline-block;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;}
    .box{background:#fff;border:1px solid #ddd;border-radius:10px;padding:14px;margin-top:14px;}
    input,select{padding:10px;width:100%;margin-top:6px;border:1px solid #ccc;border-radius:8px;}
    button{padding:10px 12px;border-radius:8px;border:none;background:#111;color:#fff;margin-top:10px;cursor:pointer;}
    table{width:100%;border-collapse:collapse;margin-top:10px;}
    th,td{padding:10px;border-bottom:1px solid #eee;text-align:left;font-size:14px;}
    th{background:#111;color:#fff;}
    .msg{color:#b00020;font-weight:700;}
  </style>
</head>
<body>
  <div class="top">
    <h2>Manage Users</h2>
    <a class="btn" href="dashboard.php">Back</a>
  </div>

  <div class="box">
    <h3>Create New User</h3>
    <?php if($msg !== ""): ?><div class="msg"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post">
      <label>Username</label>
      <input name="username" required>

      <label>Password</label>
      <input name="password" type="password" required>

      <label>Role</label>
      <select name="role" required>
        <option value="DIRECTOR">DIRECTOR</option>
        <option value="STAFF">STAFF</option>
        <option value="DRIVER">DRIVER</option>
        <option value="OWNER">OWNER</option>
      </select>

      <label>staffID (optional, for STAFF/DRIVER)</label>
      <input name="staffID" type="number" min="1">

      <label>ownerID (optional, for OWNER)</label>
      <input name="ownerID" type="number" min="1">

      <button type="submit">Create User</button>
    </form>
  </div>

  <div class="box">
    <h3>All Users</h3>
    <table>
      <thead>
        <tr>
          <th>UserID</th>
          <th>Username</th>
          <th>Role</th>
          <th>staffID</th>
          <th>ownerID</th>
          <th>Active</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($res && $res->num_rows > 0): ?>
          <?php while($u = $res->fetch_assoc()): ?>
            <tr>
              <td><?= (int)$u['userID'] ?></td>
              <td><?= htmlspecialchars($u['username']) ?></td>
              <td><?= htmlspecialchars($u['role']) ?></td>
              <td><?= $u['staffID'] === null ? '-' : (int)$u['staffID'] ?></td>
              <td><?= $u['ownerID'] === null ? '-' : (int)$u['ownerID'] ?></td>
              <td><?= (int)$u['isActive'] === 1 ? 'Yes' : 'No' ?></td>
              <td><a href="manage_users.php?toggle=<?= (int)$u['userID'] ?>">Toggle Active</a></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</body>
</html>