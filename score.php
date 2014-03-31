<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/reset.css" />
  <?php session_start();
		include 'header.php'; 
  		
  ?>
</head>
<body>
<div id="container">
<?php echo $header; ?>
<h2>Match Score</h2><br>
<?php
if (!isset($_SESSION['username'])) {
	$_SESSION['flashMessage'] = "You must be logged in to view the score";
	header('Location: /mwo/');
	die();
}
if (!isset($_GET['m'])) {
	$_SESSION['flashMessage'] = "No match found";
	header('Location: /mwo/');
	die();
}
$conn = getConnection();
$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
$match_id = mysqli_real_escape_string($conn, $_GET['m']);
$sql = "SELECT winner, attacker_url, defender_url, aftermath_report, attacker, defender, mercenary, defender_mercenary, resolved, unit_type FROM `match` INNER JOIN user ON username='" . $username . "' WHERE match_id=" . $match_id . ";";
$result = mysqli_query($conn, $sql);
if ($row = $result->fetch_row()) {
if ($row[8] == NULL || $row[8] == "") {
	$_SESSION['flashMessage'] = "That match hasn't been finished yet";
	header('Location: /mwo/');
	die();
}
if ($username == $row[4] || $username == $row[5] || $username == $row[6] || $username == $row[7] || $row[9] == 'admin') {
	echo "<p class='grey'>" . $row[0] . " won the match!<br>";
	if (!($row[1] == "" || $row[1] == NULL)) {
		echo $row[4] . "'s screenshot: <a target='_blank' href='" . $row[1] . "'>" . $row[1] . "</a><br>";
	}
	if (!($row[2] == "" || $row[2] == NULL)) {
		echo $row[5] . "'s screenshot: <a target='_blank' href='" . $row[2] . "'>" . $row[2] . "</a><br>";
	}
	echo $row[3];
	echo "</p>";
} else {
	echo "<p class='grey'>You were not involved in that match</p><br>";
} //check if user in the match
} else {
	echo "<p class='grey'>No match found</p><br>";
}
mysqli_free_result($result);
mysqli_close($conn);
?>

<?php echo $footer; ?>
</div>

</body>
</html>