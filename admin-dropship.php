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

$sql = "SELECT d.dropship_id, d.planet_name, d.owner, COALESCE(a.qty, 0), d.capacity FROM dropship AS d LEFT OUTER JOIN (SELECT m.dropship_id," .
	" SUM(m.quantity) AS qty FROM mech AS m WHERE NOT(ISNULL(dropship_id)) GROUP BY m.dropship_id) AS a ON a.dropship_id=d.dropship_id" . 
	" WHERE NOT(owner='multitallented') ORDER BY d.owner;";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>Name</th><th>Owner</th><th>Mechs</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><td>" . $row[0] . " " . $row[1] . "</td><td class='grey'>" . $row[2] . "</td><td>" .
	  $row[3] . "/" . $row[4] . "</td><td><a class='bttn' href='admin-edit-dropship.php?d=" . $row[0] . "'>Edit</a></td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a><br><br>

	<h3>Current Dropships (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>