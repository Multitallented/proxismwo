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
	if (!isset($_GET['m'])) {
		$_SESSION['flashMessage'] = "No contract by that name.";
		header('Location: index.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to view this page.";
		header('Location: index.php');
		die();
	}
    	$conn = getConnection();
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));

	$sql = "SELECT planet_name, defender_mercenary, defender_mercenary_time, attacker, responded FROM `match` WHERE match_id=" . $match_id . " AND NOT(ISNULL(defender_mercenary));";

    	$result = mysqli_query($conn, $sql);
	$result_html = "";
	$merc = "";
	$mercenary_time = "";
	if ($row = mysqli_fetch_row($result)) {
		if ($row[2] == "") {
			$result_html = "You currently have a contract offer to " . $row[1] . " to defend against " . $row[3] . " on planet " . $row[0] .
				"<br>Offer was sent on " . $row[4] . ". " . $row[1] . " has until " . add_date($row[4], 4, 0, 0) . " to respond";
		} else {
			$result_html = "Contract was accepted by " . $row[1] . " to defend against " . $row[3] . " on planet " . $row[0] .
				"<br>Offer was sent on " . $row[4] . " and accepted on " . $row[2];
		}
	} else {
		$_SESSION['flashMessage'] = "No contract found";
		header('Location: index.php');
		die();
	}
	mysqli_free_result($result);
	echo "<h2>Contract with " . $merc . "</h2><br>";
	echo "<br>" . $result_html;
	mysqli_close($conn);
  ?>


	<?php echo $footer; ?>
  </div>
</body>
</html>