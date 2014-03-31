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
	if (!isset($_GET['search'])) {
		$_SESSION['flashMessage'] = "No User by that name.";
		header('Location: /mwo/');
		die();
	}
    	$conn = getConnection();
	$search = strtolower(mysqli_real_escape_string($conn, $_GET['search']));
	
	if ($search == 'merc' || $search == 'mercenary' || $search == 'mercenaries') {
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_type='merc' AND approved='1' AND is_dead='0' ORDER BY last_login DESC;";
	} elseif ($search == 'house' || $search == 'faction') {
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_type='faction' AND approved='1' AND is_dead='0' ORDER BY last_login DESC;";
	} elseif ($search == 'pirate' || $search == 'pirates') {
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_type='pirate' AND approved='1' AND is_dead='0' ORDER BY last_login DESC;";
	} elseif ($search == 'clan' || $search == 'clanner') {
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_type='clan' AND approved='1' AND is_dead='0' ORDER BY last_login DESC;";
	} elseif ($search == 'moderator' || $search == 'admin') {
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_type='admin';";
	} else {
    		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE username LIKE '%" . $search . "%' AND approved='1' AND is_dead='0' ORDER BY last_login DESC;";
	}
    	$result = mysqli_query($conn, $sql);
	$result_html = "";
	$result_length = mysqli_num_rows($result);
	if ($result_length < 1) {
		mysqli_free_result($result);
		$sql = "SELECT username, unit_name, unit_type, wins, loses, last_login FROM user WHERE unit_name LIKE '%" . $search . "%' ORDER BY last_login DESC;";
		$result = mysqli_query($conn, $sql);
		$result_length = mysqli_num_rows($result);
	}
	while ($row = mysqli_fetch_row($result)) {
		$result_html .= "<a href='profile.php?u=" . $row[0] . "'>" . $row[0] . "</a>
		 " . $row[1] . "(" . $row[2] . ") <span class='green'>" . $row[3] . ":wins</span> <span class='red'>" . $row[4] . ":loses</span> <span class='grey'>" . explode(" ", $row[5])[0] . ":last login</span><br><br>";
	}
	mysqli_free_result($result);
	if ($result_html == "") {
		$_SESSION['flashMessage'] = "No users found";
		header('Location: /mwo/');
		die();
	}
	echo "Search Results (" . $result_length . "):<br>";
	echo "<br><br>" . $result_html;
	mysqli_close($conn);
  ?>


	<?php echo $footer; ?>
  </div>
</body>
</html>