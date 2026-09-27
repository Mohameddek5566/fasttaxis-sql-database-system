<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
requireRole("OWNER");

$ownerID = $_SESSION["user"]["ownerID"];

$sql = "
SELECT
t.taxiModel,
t.licensePlate,
m.serviceDate,
m.nextServiceDate,
m.description
FROM Maintenance m
JOIN Taxi t ON t.taxiID = m.taxiID
WHERE t.ownerID = ?
ORDER BY m.serviceDate DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i",$ownerID);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Maintenance History</h2>

<table border="1" cellpadding="10">

<tr>
<th>Taxi</th>
<th>Service Date</th>
<th>Next Service</th>
<th>Description</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
<td><?php echo $row["taxiModel"]." (".$row["licensePlate"].")"; ?></td>
<td><?php echo $row["serviceDate"]; ?></td>
<td><?php echo $row["nextServiceDate"]; ?></td>
<td><?php echo $row["description"]; ?></td>
</tr>

<?php endwhile; ?>

</table>

<br>
<a href="/fasttaxis_app/owner/dashboard.php">Back</a>