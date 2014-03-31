<?php
/*include 'header.php';
$conn = getConnection();
$sql = "SELECT production, planet_name, region, cbill_value FROM planet WHERE planet_name='shirotori';";
$result=mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	$sql = "UPDATE planet SET production='" . $row[0] . ", CPLT-A1, RVN-4X, RVN-3L, JR7-F" . "' WHERE planet_name='shirotori';";
	$result1=mysqli_query($conn, $sql);
	mysqli_free_result($result1);
	if (($row[2] == 'marik') && rand(0, 100) > 94 && strpos($row[0], 'dropship') === false) {
		$prod = ', AS7-K';
		if ($row[0] == "") {
			$prod = 'AS7-K';
		}
		$sql = "UPDATE planet SET production='" . $row[0] . $prod . "' WHERE planet_name='" . $row[1] . "';";
		$result1=mysqli_query($conn, $sql);
		mysqli_free_result($result1);
	}
}
mysqli_free_result($result);

mysqli_close($conn);*/
?>