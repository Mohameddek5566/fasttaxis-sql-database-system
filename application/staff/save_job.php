<?php
session_start();
require_once __DIR__ . "/../config/auth.php";
requireRole("STAFF");
require_once __DIR__ . "/../config/db.php";

function backErr($msg){
  header("Location: create_job.php?err=" . urlencode($msg));
  exit;
}

$clientID = (int)($_POST["clientID"] ?? 0);
$driverID = (int)($_POST["driverID"] ?? 0);
$taxiID   = (int)($_POST["taxiID"] ?? 0);

$contractNumber = trim($_POST["contractNumber"] ?? "");
$contractNumber = ($contractNumber === "") ? null : (int)$contractNumber;

$pickupAddress  = trim($_POST["pickupAddress"] ?? "");
$dropoffAddress = trim($_POST["dropoffAddress"] ?? "");

$pickupDT = trim($_POST["pickupDateTime"] ?? "");
$dropoffDT = trim($_POST["dropoffDateTime"] ?? "");

$status = trim($_POST["status"] ?? "");
$failureReason = trim($_POST["failureReason"] ?? "");

$mileageUsed = trim($_POST["mileageUsed"] ?? "");
$charge = trim($_POST["charge"] ?? "");

if($clientID<=0 || $driverID<=0 || $taxiID<=0) backErr("Please select client, driver, taxi.");
if($pickupAddress==="" || $dropoffAddress==="") backErr("Pickup/Dropoff required.");
if($pickupDT==="") backErr("Pickup datetime required.");
if(!in_array($status, ["Completed","Failed","Ongoing"], true)) backErr("Invalid status.");

$pickupDT = str_replace("T"," ",$pickupDT).":00";
$dropoffDT = ($dropoffDT==="") ? null : (str_replace("T"," ",$dropoffDT).":00");

$mileageUsed = ($mileageUsed==="") ? null : (float)$mileageUsed;
$charge = ($charge==="") ? null : (float)$charge;

if($status !== "Failed") $failureReason = null;
if($status === "Failed"){
  if($failureReason===null || trim($failureReason)==="") $failureReason = "Failed";
  $dropoffDT = null;
  $mileageUsed = null;
  $charge = null;
}

$sql = "INSERT INTO Job
(clientID, driverID, taxiID, contractNumber, pickupAddress, dropoffAddress,
 pickupDateTime, dropoffDateTime, mileageUsed, charge, status, failureReason)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $mysqli->prepare($sql);
if(!$stmt) backErr("Prepare failed: ".$mysqli->error);

$stmt->bind_param(
  "iiisssssddss",
  $clientID,
  $driverID,
  $taxiID,
  $contractNumber,
  $pickupAddress,
  $dropoffAddress,
  $pickupDT,
  $dropoffDT,
  $mileageUsed,
  $charge,
  $status,
  $failureReason
);

// Fix binding for NULLs (mysqli needs special handling)
$params = [
  $clientID, $driverID, $taxiID,
  $contractNumber, $pickupAddress, $dropoffAddress,
  $pickupDT, $dropoffDT, $mileageUsed, $charge, $status, $failureReason
];

// Rebuild statement with correct null handling:
$stmt->close();

$sql = "INSERT INTO Job
(clientID, driverID, taxiID, contractNumber, pickupAddress, dropoffAddress,
 pickupDateTime, dropoffDateTime, mileageUsed, charge, status, failureReason)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";

$stmt = $mysqli->prepare($sql);
if(!$stmt) backErr("Prepare failed: ".$mysqli->error);

$stmt->bind_param(
  "iiisssssddss",
  $params[0],
  $params[1],
  $params[2],
  $params[3],
  $params[4],
  $params[5],
  $params[6],
  $params[7],
  $params[8],
  $params[9],
  $params[10],
  $params[11]
);

// Convert PHP nulls to proper MySQL nulls
if ($contractNumber === null) $stmt->bind_param("iiisssssddss", $clientID,$driverID,$taxiID,$contractNumber,$pickupAddress,$dropoffAddress,$pickupDT,$dropoffDT,$mileageUsed,$charge,$status,$failureReason);

if(!$stmt->execute()){
  backErr("Insert failed: ".$stmt->error);
}

header("Location: create_job.php?ok=1");
exit;