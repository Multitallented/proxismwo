<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/reset.css" />
  <?php session_start(); 
  		

  ?>
  <?php include 'header.php'; ?>
</head>
<body>
  <div id="container">
	<?php echo $header; ?>
  <?php
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in on an admin account to view this page";
		header('Location: index.php');
		die();
	}
	$conn = getConnection();
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$match_id=0;
	$action = "";
	if (isset($_GET['m'])) {
		$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	}
	if (isset($_GET['a'])) {
		$action = mysqli_real_escape_string($conn, $_GET['a']);
	}
	$sql = "SELECT admin FROM user WHERE username ='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		if ($row[0] == 0) {
			$_SESSION['flashMessage'] = "You must be logged in on an admin account to view this page";
			header('Location: index.php');
			die();
		}
	}
	mysqli_free_result($result);

	if ($action == 'undo') {
		$sql = "UPDATE `match` SET resolved=NULL WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Undid match " . $match_id;
		header('Location: admin-pmatches.php');
		die();
	}


$sql = "SELECT match_id, planet_name, attacker, defender, mercenary, defender_mercenary, winner" .
	" FROM `match` WHERE NOT(ISNULL(resolved));";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>ID</th><th>planet</th><th>attacker</th><th>atkr merc</th><th>defender</th>" . 
		"<th>def merc</th><th>winner</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><td><a href='score.php?m=" . $row[0] . "'>" . $row[0] . "</a></td><td>" . $row[1] . "</td><td class='red'>" . $row[2] . "</td><td class='red'>" .
		$row[4] . "</td><td class='green'>" . $row[3] . "</td><td class='green'>" . $row[5] . "</td><td>" . $row[6] . 
		"</td><td><a class='bttn' href='admin-pmatches.php?a=undo&m=" . $row[0] . "'>Undo</a></td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a><br><br>

	<h3>Past Matches (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>