<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
requireRole("DRIVER");

$driverID = $_SESSION["user"]["staffID"] ?? null;
if (!$driverID) {
    die("Driver account is not linked to staffID in users table.");
}

$jobID = (int)($_POST["jobID"] ?? 0);
$status = $_POST["status"] ?? "";
$mileageUsed = $_POST["mileageUsed"] ?? null;
$failureReason = trim($_POST["failureReason"] ?? "");

if ($jobID <= 0) {
    header("Location: /fasttaxis_app/driver/my_jobs.php");
    exit;
}

$allowed = ["Completed", "Failed"];
if (!in_array($status, $allowed, true)) {
    die("Invalid status.");
}

// Make mileage NULL if empty
if ($mileageUsed === "" || $mileageUsed === null) {
    $mileageUsed = null;
} else {
    $mileageUsed = (float)$mileageUsed;
}

// If completed, failureReason must be NULL
if ($status === "Completed") {
    $failureReason = null;
}
// If failed and no reason, put default
if ($status === "Failed" && ($failureReason === "")) {
    $failureReason = "Not specified";
}

$sql = "
UPDATE Job
SET status = ?,
    mileageUsed = ?,
    failureReason = ?
WHERE jobID = ? AND driverID = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sdsii", $status, $mileageUsed, $failureReason, $jobID, $driverID);
$stmt->execute();

header("Location: /fasttaxis_app/driver/my_jobs.php");
exit;