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

$sql = "SELECT username, unit_name, unit_type, wins, loses, cbills, kills, is_dead, approved FROM user WHERE admin=0;";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>Name</th><th>Unit</th><th>W/L</th><th>Cbills</th><th>kills</th><th>Active</th><th>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><td>" . $row[0] . "</td><td class='grey'>" . $row[1] . "(" . $row[2] . ")</td><td>" . $row[3] . "/" . $row[4] .
		" (" . number_format($row[3] / $row[4], 2) . ")</td><td class='gold'>" . number_format($row[5]) .
		"</td><td>" . $row[6] . "</td><td>";
  if ($row[7] == 0 && $row[8] == 1) {
	$cmatches .= "<span class='green'>Active</span></td>";
  } else {
	$cmatches .= "<span class='grey'>Inactive</span></td>";
  }
  $cmatches .= "<td><a class='bttn' href='admin-edit-user.php?u=" . $row[0] . "'>Edit</a> <a class='bttn' href='playerlogs/" . $row[0] . ".log'>Log</a></td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a><br><br>

	<h3>Current Users (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>