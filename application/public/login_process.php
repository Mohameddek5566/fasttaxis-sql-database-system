<?php
session_start();
require_once __DIR__ . "/../config/db.php";

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    header("Location: /fasttaxis_app/public/login.php?error=1");
    exit;
}

$sql = "SELECT userID, username, passwordHash, role, isActive, staffID, ownerID
        FROM users
        WHERE username = ?
        LIMIT 1";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || (int)$user["isActive"] !== 1 || !password_verify($password, $user["passwordHash"])) {
    header("Location: /fasttaxis_app/public/login.php?error=1");
    exit;
}

// Normalize role (avoid issues like "director" or "DIRECTOR ")
$role = strtoupper(trim($user["role"]));

// Save session
$_SESSION["user"] = [
    "userID"   => $user["userID"],
    "username" => $user["username"],
    "role"     => $role,
    "staffID"  => $user["staffID"] ?? null,
    "ownerID"  => $user["ownerID"] ?? null
];

// Redirect by role (use absolute paths)
switch ($role) {
    case "DIRECTOR":
        header("Location: /fasttaxis_app/director/dashboard.php");
        break;
    case "STAFF":
        header("Location: /fasttaxis_app/staff/dashboard.php");
        break;
    case "DRIVER":
        header("Location: /fasttaxis_app/driver/dashboard.php");
        break;
    case "OWNER":
        header("Location: /fasttaxis_app/owner/dashboard.php");
        break;
    default:
        session_destroy();
        header("Location: /fasttaxis_app/public/login.php?error=Invalid+role");
        break;
}
exit;