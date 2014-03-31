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
	if (!isset($_GET['p'])) {
		$_SESSION['flashMessage'] = "No Planet by that name.";
		header('Location: index.php');
		die();
	}
    $conn = getConnection();
	$planet_name = mysqli_real_escape_string($conn, $_GET['p']);
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$planet_value = 0;
	$cbills = 0;
	$unit_type = "";
	if ($username=="") {
		$_SESSION['flashMessage'] = "You must be logged in to claim a planet";
		header('Location: map.php');
		die();
	}

	$sql = "SELECT p.cbill_value, p.owner_name, d.dropship_id, u.cbills, u.unit_type FROM planet AS p INNER JOIN user AS u ON u.username='" . $username . 
		"' LEFT OUTER JOIN dropship AS d ON d.planet_name=p.planet_name AND d.owner='" . $username . "' WHERE p.planet_name='" . $planet_name . "';";

	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$planet_value = $row[0];
		if ($row[1] != "Unowned") {
			$_SESSION['flashMessage'] = "You can't claim owned planets";
			header('Location: map.php');
			die();
		}
		if ($row[2] == NULL) {
			$_SESSION['flashMessage'] = "You need a dropship in orbit in order to claim the planet";
			header('Location: map.php');
			die();
		}
		if ($row[3] < $row[0]) {
			$_SESSION['flashMessage'] = "You don't have enough cbills to claim this planet";
			header('Location: map.php');
			die();
		}
		$cbills = $row[3];
		$unit_type = $row[4];
	}
	mysqli_free_result($result);

	
	$sql = "UPDATE planet SET owner_name='" . $username . "'";
	$sql .= " WHERE planet_name='" . $planet_name . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);

	$sql = "UPDATE user SET cbills=" . ($cbills - $planet_value) . " WHERE username='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	
	$_SESSION['flashMessage'] = "You have claimed " . $planet_name . " for " . number_format($planet_value) . "cbills";
	header('Location: map.php');

	mysqli_close($conn);
  ?>
	You should be redirected momentarily

	<?php echo $footer; ?>
  </div>
</body>
</html>