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
			header('Location: /mwo/');
			die();
		}
	}
	mysqli_free_result($result);
	
	$pname = "";
	$powner = "";
	$pcbill = 0;
	$pcond = "";
	$pprod = "";
	$px = 0;
	$py = 0;
	$pinvuln = 0;
	$pregion = "";
	$pcapacity = 0;
	$pimage = "";
	
	//Add planet
	if (isset($_GET['addplanet'])) {
		$pname = mysqli_real_escape_string($conn, $_GET['name']);
		$powner = mysqli_real_escape_string($conn, $_GET['owner']);
		$pcbill = mysqli_real_escape_string($conn, $_GET['cbillvalue']);
		$pcond = mysqli_real_escape_string($conn, $_GET['cond']);
		$pprod = mysqli_real_escape_string($conn, $_GET['prod']);
		$px = mysqli_real_escape_string($conn, $_GET['x']);
		$py = mysqli_real_escape_string($conn, $_GET['y']);
		$pinvuln = isset($_GET['invuln']) ? 1 : 0;
		$pregion = mysqli_real_escape_string($conn, $_GET['region']);
		$pcapacity = mysqli_real_escape_string($conn, $_GET['capacity']);
		$pimage = mysqli_real_escape_string($conn, $_GET['image']);
		
		$sql = "INSERT INTO planet VALUES ('" . $pname . "', '" . $powner . "', '" . $pcond . 
				"', " . $py . ", " . $pcbill . ", '" . $pprod . "', " . $pinvuln . ", " . $px . 
				", '" . $pimage . "', '" . $pregion . "', " . $pcapacity . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
	}
	

$sql = "SELECT planet_name, owner_name, cbill_value, region, location_x, location_y" .
		" FROM planet;";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>Name</th><th>Owner</th><th>Value</th><th>Region</th><th>Co-ords</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td class='gold'>" . number_format($row[2]) . "</td><td>" .
	  $row[3] . "</td><td class='green'>" . $row[4] . ", " . $row[5] . "</td><td><a class='bttn' href='admin-edit-planet.php?p=" . $row[0] . "'>Edit</a></td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a><br><br>

	<h3>Current Planets (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

	<form action='admin-planet.php' method='get'>
		<br><h3>Create Planet</h3><br>
		Name: <input type='textfield' name='name' value='<?php echo $pname; ?>' /><br>
		Owner: <input type='textfield' name='owner' value='<?php echo $powner; ?>' /><br>
		CBill Value: <input type='textfield' name='cbillvalue' value='<?php echo $pcbill; ?>' /><br>
		Conditions: <input style='width: 800px;' type='textfield' name='cond' value='<?php echo $pcond; ?>' /><br>
		Production: <input style='width: 800px;' type='textfield' name='prod' value='<?php echo $pprod; ?>' /><br>
		Location X: <input type='textfield' name='x' value='<?php echo $px; ?>' /><br>
		Location Y: <input type='textfield' name='y' value='<?php echo $py; ?>' /><br>
		Invuln: <input type='checkbox' name='invuln' /><br>
		Region: <input type='textfield' name='region' value='<?php echo $pregion; ?>' /><br>
		Capacity: <input type='textfield' name='capacity' value='<?php echo $pcapacity; ?>' /><br>
		Image: <select name='image'>
				<option>earth.png</option>
				<option>desert.png</option>
				<option>red.png</option>
				<option>lava.png</option>
				<option>dark.png</option>
				<option>blue.png</option>
				<option>forest.png</option>
				<option>gas.png</option>
			</select><br>
		<input type='submit' name='addplanet' name='Save' />
	</form>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>