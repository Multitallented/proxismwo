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
	$sql = "SELECT admin FROM user WHERE username ='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row() && ($row[0] == "1" || $row[0] == 1)) {
		$_SESSION['flashMessage'] = "You must be logged in on an admin account to view this page";
		header('Location: index.php');
		die();
	}
	mysqli_free_result($result);

	if (!isset($_GET['u'])) {
		$_SESSION['flashMessage'] = "No user found";
		header('Location: admin.php');
		die();
	}
    	
	$search = strtolower(mysqli_real_escape_string($conn, $_GET['u']));
	
    	$sql = "SELECT unit_leader, url FROM roster WHERE unit_leader='" . $search . "';";
    	$result = mysqli_query($conn, $sql);
	$result_html = "";
	$unit_leader = "";
	$result_length = mysqli_num_rows($result);
	while ($row = $result->fetch_row()) {
		$unit_leader = $row[0];
		$result_html .= "<a href='" . $row[1] . "' target='_blank'>" . $row[1] . "</a><br><br>";
	}
	mysqli_free_result($result);
	if ($result_html == "") {
		$result_html = "No users for " . $search . " on the roster";
	} ?>

	<form action='admin-view.php' method='get'>
		<input class='inline left' type='textfield' name='search' /><input type='submit' value='Search' class='left inline' />
	</form>
	<div class='clearfix'></div><br>
  	<?php echo "Search Results (" . $result_length . "):<br>";
		echo "<br>" . $result_html;
		mysqli_close($conn);
  	?>

  <div class='clearfix'></div>


	<?php echo $footer; ?>
  </div>
</body>
</html>