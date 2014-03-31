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
	$planet_name="";
	if (isset($_GET['p'])) {
		$planet_name = mysqli_real_escape_string($conn, $_GET['p']);
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

$sql = "SELECT owner_name, cbill_value, production, match_conditions, region, location_x, location_y" .
		", invuln FROM planet WHERE planet_name='" . $planet_name . "';";
$result = mysqli_query($conn, $sql);
$owner = "";
$cbill_value = 0;
$production = "";
$match_conditions = "";
$x = 0;
$y = 0;
$region = "";
$invuln = "false";
if ($row = $result->fetch_row()) {
	$owner = $row[0];
	$cbill_value = $row[1];
	$production = $row[2];
	$match_conditions = $row[3];
	$region = $row[4];
	$x = $row[5];
	$y = $row[6];
	if ($row[7] == 1) {
		$invuln = "true";
	}
}
mysqli_free_result($result);
if (isset($_GET['oedit']) && isset($_GET['qty']) && isset($_GET['mech'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$oqty = 0;
	$sql = "SELECT quantity FROM `mech` WHERE mech='" . $mech . "' AND planet_name='" . $planet_name	. "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$oqty = $row[0];
	}
	mysqli_free_result($result);

	$sql = "UPDATE `market` SET buy=(buy + " . ($qty - $oqty) . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($qty < 1) {
		$sql = "DELETE FROM mech WHERE mech='" . $mech . "' AND planet_name='" . $planet_name . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have " . $qty . " " . $mech;
		header('Location: admin-edit-planet.php?p=' . $planet_name);
		die();
	}
	$sql = "UPDATE mech SET quantity=" . $qty . " WHERE mech='" . $mech . "' AND planet_name='" . $planet_name . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have " . $qty . " " . $mech;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['oadd']) && isset($_GET['qty']) && isset($_GET['mech'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$sql = "INSERT INTO mech VALUES ('" . $mech . "', '" . $owner . "', " . $qty . ", NULL , '" . $planet_name . "');";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$sql = "UPDATE `market` SET buy=(buy + " . $qty . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Added " . $qty . " " . $mech . " to planet " . $planet_name;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['mmedit']) && isset($_GET['qty']) && isset($_GET['mech']) && isset($_GET['m'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$oqty = 0;
	$sql = "SELECT quantity FROM `match_mech` WHERE mech='" . $mech . "' AND planet_name='" . $planet_name	. "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$oqty = $row[0];
	}
	mysqli_free_result($result);

	$sql = "UPDATE `market` SET buy=(buy + " . ($qty - $oqty) . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($qty < 1) {
		$sql = "DELETE FROM match_mech WHERE mech='" . $mech . "' AND planet_name='" . $planet_name . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have " . $qty . " " . $mech;
		header('Location: admin-edit-planet.php?p=' . $planet_name);
		die();
	}
	$sql = "UPDATE match_mech SET quantity=" . $qty . " WHERE mech='" . $mech . "' AND planet_name='" . $planet_name . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have " . $qty . " " . $mech;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['mmadd']) && isset($_GET['qty']) && isset($_GET['mech']) && isset($_GET['m'])) {
	$mech = mysqli_real_escape_string($conn, $_GET['mech']);
	$qty = mysqli_real_escape_string($conn, $_GET['qty']);
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$sql = "INSERT INTO match_mech VALUES ('" . $mech . "', " . $match_id . ", '" . $owner . "', " . $qty . ", NULL, '" . $planet_name . "');";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	
	$sql = "UPDATE `market` SET buy=(buy + " . $qty . ") WHERE mech='" . $mech . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Added " . $qty . " " . $mech . " to planet " . $planet_name;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['mcon']) && isset($_GET['match_conditions'])) {
	$mcon = mysqli_real_escape_string($conn, $_GET['match_conditions']);
	$sql = "UPDATE planet SET match_conditions='" . $mcon . "' WHERE planet_name='" . $planet_name . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have " . $mcon;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['prod']) && isset($_GET['produ'])) {
	$produ = mysqli_real_escape_string($conn, $_GET['produ']);
	$sql = "UPDATE planet SET production='" . $produ . "' WHERE planet_name='" . $planet_name . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Updated planet " . $planet_name . " to have production " . $produ;
	header('Location: admin-edit-planet.php?p=' . $planet_name);
	die();
}

if (isset($_GET['einvuln'])) {
	$new_invuln = 0;
	if (isset($_GET['invuln'])) {
		$new_invuln = 1;
	}
	if (($new_invuln == 0 && $invuln == "true") || ($invuln == "false" && $new_invuln == 1)) {
		$sql = "UPDATE planet SET invuln=" . $new_invuln . " WHERE planet_name='" . $planet_name . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Planet " . $planet_name . " invuln set";
		header('Location: admin-edit-planet.php?p=' . $planet_name);
		die();
	}
}

$sql = "SELECT mech, quantity FROM mech WHERE planet_name='" . $planet_name . "';";
$result = mysqli_query($conn, $sql);
$num_matches = 0;
$cmatches = "<table><th>Mech</th><th>Qty</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><form method='get' action='admin-edit-planet.php'><td><input type='hidden' class='hide' name='mech' value='" . 
		$row[0] . "' />" . $row[0] . "</td><td><input type='textfield' name='qty' value='" . $row[1] . "' /></td><td>" . 
		"<input type='hidden' class='hide' name='p' value='" . $planet_name . "' /><input name='oedit' type='submit' value='Save' /></form></td></tr>";
  $num_matches += $row[1];
}
mysqli_free_result($result);
$cmatches .= "</table>";


$sql = "SELECT mech, quantity, match_id FROM match_mech WHERE planet_name='" . $planet_name . "';";
$result = mysqli_query($conn, $sql);

$num_mmechs = 0;
$mmechs = "<table><th>Mech</th><th>Qty</th><th>Match</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $mmechs .= "<tr><form method='get' action='admin-edit-planet.php'><td><input type='hidden' class='hide' name='mech' value='" . 
		$row[0] . "' />" . $row[0] . "</td><td><input type='textfield' name='qty' value='" . $row[1] . "' /></td><td>" . 
		"<input type='hidden' class='hide' name='p' value='" . $planet_name . 
		"' /><input type='hidden' class='hide' name='m' value='" . $row[2] . 
		"' />" . $row[2] . "</td><td><input name='mmedit' type='submit' value='Save' /></form></td></tr>";
  $num_mmechs += $row[1];
}
mysqli_free_result($result);
$mmechs .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a> | <a href="admin-planet.php">Planet Manager</a><br><br>

  <h2>Edit Planet <?php echo $planet_name; ?></h2><br>

  <form action='admin-edit-planet.php' method='get'>
	Match Conditions: <input type='textfield' style='width: 700px;' name='match_conditions' value='<?php echo $match_conditions; ?>' />
	<input type='hidden' class='hide' name='p' value='<?php echo $planet_name; ?>' />
	<input type='submit' name='mcon' value='Save' />
  </form><br>

  <form action='admin-edit-planet.php' method='get'>
    <h3>Production</h3>
	<textarea style='width: 800px; height: 100px;' name='produ'><?php echo $production; ?></textarea>
	<input type='hidden' class='hide' name='p' value='<?php echo $planet_name; ?>' />
	<input type='submit' name='prod' value='Save' />
  </form><br>

	<h3>Planet Invuln <?php echo $invuln; ?></h3>
	<form action='admin-edit-planet.php' method='get'>
		<?php if ($invuln == "true") { ?>
			<input type='checkbox' name='invuln' checked />
		<?php } else { ?>
			<input type='checkbox' name='invuln' />
		<?php } ?>
		<input type='hidden' class='hide' name='p' value='<?php echo $planet_name; ?>' />
		<input type='submit' name='einvuln' value='Save' />
	</form><br><br>

	<div class='left wdth-50'>
	<h3>Onboard Mechs (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?><br><br>
	<form action='admin-edit-planet.php' method='get'>
	Add <input type='textfield' style='width: 20px;' name='qty' value='0' />
	<input type='textfield' name='mech' />
	<input type='hidden' class='hide' name='p' value='<?php echo $planet_name; ?>' />
	<input type='submit' name='oadd' value='Add Mech' />
	</form>
	</div>

	<div class='left wdth-50'>
	<h3>In-Combat Mechs (<?php echo $num_mmechs; ?>)</h3><br>

	<?php echo $mmechs; ?><br><br>
	<form action='admin-edit-planet.php' method='get'>
	Add <input type='textfield' style='width: 20px;' name='qty' value='0' />
	<input type='textfield' name='mech' />
	in match <input type='textfield' style='width: 20px;' name='m' />
	<input type='hidden' class='hide' name='p' value='<?php echo $planet_name; ?>' />
	<input type='submit' name='mmadd' value='Add Mech' />
	</form>
	</div>

	<div class='clearfix'></div>
<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>