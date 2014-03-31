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
		header('Location: /mwo/');
		die();
	}
	$conn = getConnection();
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$euser = "";
	if (isset($_GET['u'])) {
		$euser = strtolower(mysqli_real_escape_string($conn, $_GET['u']));
	} else {
		header('Location: admin-user.php');
		die();
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
	
	if (isset($_GET['cbills'])) {
		$new_cbills = mysqli_real_escape_string($conn, $_GET['cbills']);
		$sql = "UPDATE user SET cbills=" . $new_cbills . " WHERE username='" . $euser . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = $euser . "'s cbills set to " . $new_cbills;
		header('Location: admin-edit-user.php?u=' . $euser);
		die();
	}
	if (isset($_GET['acbills'])) {
		$anew_cbills = mysqli_real_escape_string($conn, $_GET['acbills']);
		$sql = "UPDATE user SET cbills=(cbills+" . $anew_cbills . ") WHERE username='" . $euser . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Added " . $anew_cbills . " to " . $euser . "'s account";
		header('Location: admin-edit-user.php?u=' . $euser);
		die();
	}
	if (isset($_GET['wins'])) {
		$new_wins = mysqli_real_escape_string($conn, $_GET['wins']);
		$sql = "UPDATE user SET wins=" . $new_wins . " WHERE username='" . $euser . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = $euser . "'s wins set to " . $new_wins;
		header('Location: admin-edit-user.php?u=' . $euser);
		die();
	}
	if (isset($_GET['loses'])) {
		$new_loses = mysqli_real_escape_string($conn, $_GET['loses']);
		$sql = "UPDATE user SET loses=" . $new_loses . " WHERE username='" . $euser . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = $euser . "'s loses set to " . $new_loses;
		header('Location: admin-edit-user.php?u=' . $euser);
		die();
	}
	$cbills = 0;
	$wins = 0;
	$loses = 0;
	$kills = 0;
	$unit_name = "";
	$unit_type = "";
	$account_url = "";
	$sql = "SELECT cbills, wins, loses, kills, unit_name, unit_type, url FROM user LEFT OUTER JOIN roster ON unit_leader=username WHERE username='" . $euser . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$cbills = $row[0];
		$wins = $row[1];
		$loses = $row[2];
		$kills = $row[3];
		$unit_name = $row[4];
		$unit_type = $row[5];
		$account_url = $row[6];
	} else {
		$_SESSION['flashMessage'] = "No user found by name " . $euser;
		header('Location: admin.php');
		die();
	}
	mysqli_free_result($result);

	if (isset($_GET['url'])) {
		$new_url = mysqli_real_escape_string($conn, $_GET['url']);
		$sql = "SELECT * FROM roster WHERE url='" . $new_url . "';";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$_SESSION['flashMessage'] = "That url is already in use";
			header('Location: admin-edit-user.php?u=' . $euser);
			die();
		}
		
		$sql = "UPDATE roster SET url=" . $new_url . " WHERE url='" . $account_url . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = $euser . "'s url set to " . $new_url;
		header('Location: admin-edit-user.php?u=' . $euser);
		die();
	}
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a> | <a href="admin-user.php">User Manager</a><br><br>

	<h3>Editing <?php echo $euser; ?></h3><br>

	<span class='gold'>Update CBILLs: </span>
	<form action='admin-edit-user.php' method='get'><input type='textfield' name='cbills' value='<?php echo $cbills;?>' />
		 <input type='submit' value='Save' />
		<input class='hide' type='hidden' name='u' value='<?php echo $euser; ?>' /></form><br><br>
	
	<span class='gold'>Add CBILLs: </span>
	<form action='admin-edit-user.php' method='get'><input type='textfield' name='acbills' value='0' />
		 <input type='submit' value='Add' />
		<input class='hide' type='hidden' name='u' value='<?php echo $euser; ?>' /></form><br><br>

	<span class='gold'>Update Wins: </span>
	<form action='admin-edit-user.php' method='get'><input type='textfield' name='wins' value='<?php echo $wins;?>' />
		 <input type='submit' value='Save' />
		<input class='hide' type='hidden' name='u' value='<?php echo $euser; ?>' /></form><br><br>

	<span class='gold'>Update Loses: </span>
	<form action='admin-edit-user.php' method='get'><input type='textfield' name='loses' value='<?php echo $loses;?>' />
		 <input type='submit' value='Save' />
		<input class='hide' type='hidden' name='u' value='<?php echo $euser; ?>' /></form><br><br>

	<span class='gold'>Update URL: </span>
	<form action='admin-edit-user.php' method='get'><input class='wdth-66' type='textfield' name='url' value='<?php echo $account_url;?>' />
		 <input type='submit' value='Save' />
		<input class='hide' type='hidden' name='u' value='<?php echo $euser; ?>' /></form><br><br>
	
<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>