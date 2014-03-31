<?php
//echo md5('changeme');
include('header.php');
$conn = getConnection();

echo "Initializing market repair tool<br>";

$sql = "SELECT mech FROM market;";
$result = mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	$mech = $row[0];
	$qty = 0;
	$sql = "SELECT SUM(q.quantity) FROM (SELECT m.quantity FROM mech AS m WHERE mech='" . $mech . 
			"' UNION ALL SELECT mm.quantity FROM `match_mech` AS mm WHERE mech='" . $mech . "') AS q;";
	$result1 = mysqli_query($conn, $sql);
	if ($row1 = $result1->fetch_row()) {
		$qty = $row1[0];
	}
	mysqli_free_result($result1);
	echo "Mech: " . $mech . " qty " . $qty . "<br>";

	$sql = "UPDATE market SET buy=" . $qty . " WHERE mech='" . $mech . "';";
	$result1 = mysqli_query($conn, $sql);
	mysqli_free_result($result1);
}
mysqli_free_result($result);
mysqli_close($conn);
echo "Repairs complete";
?>