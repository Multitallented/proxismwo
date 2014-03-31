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
	$dropship_id=0;
	if (isset($_GET['d'])) {
		$dropship_id = mysqli_real_escape_string($conn, $_GET['d']);
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

$sql = "SELECT d.planet_name, d.owner, d.capacity FROM dropship AS d WHERE d.dropship_id=" . $dropship_id . ";";
$result = mysqli_query($conn, $sql);
$planet_name = "";
$owner = "";
$capacity = 0;
if ($row = $result->fetch_row()) {
	$planet_name = $row[0];
	$owner = $row[1];
	$capacity = $row[2];
}
mysqli_free_result($result);

if (isset($_GET['oedit']) && isset($_GET['qty']) && isset($_GET['mech'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$oqty = 0;
	$sql = "SELECT quantity FROM `mech` WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id	. ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$oqty = $row[0];
	}
	mysqli_free_result($result);

	$sql = "UPDATE `market` SET buy=(buy + " . ($qty - $oqty) . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($qty < 1) {
		$sql = "DELETE FROM mech WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Updated dropship " . $dropship_id . " to have " . $qty . " " . $mech;
		header('Location: admin-edit-dropship.php?d=' . $dropship_id);
		die();
	}
	$sql = "UPDATE mech SET quantity=" . $qty . " WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated dropship " . $dropship_id . " to have " . $qty . " " . $mech;
	header('Location: admin-edit-dropship.php?d=' . $dropship_id);
	die();
}

if (isset($_GET['oadd']) && isset($_GET['qty']) && isset($_GET['mech'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$sql = "INSERT INTO mech VALUES ('" . $mech . "', '" . $owner . "', " . $qty . ", " . $dropship_id . ", NULL);";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$sql = "UPDATE `market` SET buy=(buy + " . $qty . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Added " . $qty . " " . $mech . " to dropship " . $dropship_id;
	header('Location: admin-edit-dropship.php?d=' . $dropship_id);
	die();
}

if (isset($_GET['mmedit']) && isset($_GET['qty']) && isset($_GET['mech']) && isset($_GET['m'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$oqty = 0;
	$sql = "SELECT quantity FROM `match_mech` WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id	. " AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$oqty = $row[0];
	}
	mysqli_free_result($result);

	$sql = "UPDATE `market` SET buy=(buy + " . ($qty - $oqty) . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($qty < 1) {
		$sql = "DELETE FROM match_mech WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id . " AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Updated dropship " . $dropship_id . " to have " . $qty . " " . $mech;
		header('Location: admin-edit-dropship.php?d=' . $dropship_id);
		die();
	}
	$sql = "UPDATE match_mech SET quantity=" . $qty . " WHERE mech='" . $mech . "' AND dropship_id=" . $dropship_id . " AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated dropship " . $dropship_id . " to have " . $qty . " " . $mech;
	header('Location: admin-edit-dropship.php?d=' . $dropship_id);
	die();
}

if (isset($_GET['mmadd']) && isset($_GET['qty']) && isset($_GET['mech']) && isset($_GET['m'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$sql = "INSERT INTO match_mech VALUES ('" . $mech . "', " . $match_id . ", '" . $owner . "', " . $qty . ", " . $dropship_id . ", NULL);";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$sql = "UPDATE `market` SET buy=(buy + " . $qty . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Added " . $qty . " " . $mech . " to dropship " . $dropship_id;
	header('Location: admin-edit-dropship.php?d=' . $dropship_id);
	die();
}

$sql = "SELECT mech, quantity FROM mech WHERE dropship_id=" . $dropship_id . ";";
$result = mysqli_query($conn, $sql);

$num_matches = 0;
$cmatches = "<table><th>Mech</th><th>Qty</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><form method='get' action='admin-edit-dropship.php'><td><input type='hidden' class='hide' name='mech' value='" . 
		$row[0] . "' />" . $row[0] . "</td><td><input type='textfield' name='qty' value='" . $row[1] . "' /></td><td>" . 
		"<input type='hidden' class='hide' name='d' value='" . $dropship_id . "' /><input name='oedit' type='submit' value='Save' /></form></td></tr>";
  $num_matches += $row[1];
}
mysqli_free_result($result);
$cmatches .= "</table>";


$sql = "SELECT mech, quantity, match_id FROM match_mech WHERE dropship_id=" . $dropship_id . ";";
$result = mysqli_query($conn, $sql);

$num_mmechs = 0;
$mmechs = "<table><th>Mech</th><th>Qty</th><th>Match</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $mmechs .= "<tr><form method='get' action='admin-edit-dropship.php'><td><input type='hidden' class='hide' name='mech' value='" . 
		$row[0] . "' />" . $row[0] . "</td><td><input type='textfield' name='qty' value='" . $row[1] . "' /></td><td>" . 
		"<input type='hidden' class='hide' name='d' value='" . $dropship_id . 
		"' />" . $row[2] . "</td><td><input class='hide' type='hidden' name='m' value='" . $row[2] . "' /><input name='mmedit' type='submit' value='Save' /></form></td></tr>";
  $num_mmechs += $row[1];
}
mysqli_free_result($result);
$mmechs .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a> | <a href="admin-dropship.php">Dropship Manager</a><br><br>

  <h2>Edit Dropship <?php echo $dropship_id; ?></h2><br>

	<div class='left wdth-50'>
	<h3>Onboard Mechs (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?><br><br>
	<form action='admin-edit-dropship.php' method='get'>
	Add <input type='textfield' style='width: 20px;' name='qty' value='0' />
	<input type='textfield' name='mech' />
	<input type='hidden' class='hide' name='d' value='<?php echo $dropship_id; ?>' />
	<input type='submit' name='oadd' value='Add Mech' />
	</form>
	</div>

	<div class='left wdth-50'>
	<h3>In-Combat Mechs (<?php echo $num_mmechs; ?>)</h3><br>

	<?php echo $mmechs; ?><br><br>
	<form action='admin-edit-dropship.php' method='get'>
	Add <input type='textfield' style='width: 20px;' name='qty' value='0' />
	<input type='textfield' name='mech' />
	in match <input type='textfield' style='width: 20px;' name='m' />
	<input type='hidden' class='hide' name='d' value='<?php echo $dropship_id; ?>' />
	<input type='submit' name='mmadd' value='Add Mech' />
	</form>
	</div>

	<div class='clearfix'></div>
<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>