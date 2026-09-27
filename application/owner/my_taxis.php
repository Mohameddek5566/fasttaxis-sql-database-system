<?php
require_once __DIR__ . "/../config/auth.php";
require_once __DIR__ . "/../config/db.php";
requireRole("OWNER");

$ownerID = $_SESSION["user"]["ownerID"];

$sql = "
SELECT
t.taxiID,
t.taxiModel,
t.licensePlate,
COUNT(j.jobID) AS totalJobs,
SUM(j.charge) AS earnings
FROM Taxi t
LEFT JOIN Job j ON j.taxiID = t.taxiID
WHERE t.ownerID = ?
GROUP BY t.taxiID
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i",$ownerID);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Taxis</h2>

<table border="1" cellpadding="10">
<tr>
<th>Taxi</th>
<th>Model</th>
<th>License</th>
<th>Total Jobs</th>
<th>Total Earnings</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>

<tr>
<td><?php echo $row["taxiID"]; ?></td>
<td><?php echo $row["taxiModel"]; ?></td>
<td><?php echo $row["licensePlate"]; ?></td>
<td><?php echo $row["totalJobs"]; ?></td>
<td><?php echo $row["earnings"]; ?></td>
</tr>

<?php endwhile; ?>

</table>

<br>
<a href="/fasttaxis_app/owner/dashboard.php">Back</a>