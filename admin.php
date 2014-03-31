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

	if (isset($_POST['accept'])) {
		$user = strtolower(mysqli_real_escape_string($conn, $_POST['accept']));
		$sql = "UPDATE user SET approved=1 WHERE username='" . $user . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		echo "<span class='gold'>" . $user . " has been approved successfully</span>";
	} elseif (isset($_POST['deny'])) {
		$user = strtolower(mysqli_real_escape_string($conn, $_POST['deny']));
		$sql = "DELETE FROM user WHERE username='" . $user . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "DELETE FROM roster WHERE unit_leader='" . $user . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		echo "<span class='gold'>" . $user . " has been denied successfully</span>";
	}

	$search = "";
	if (!isset($_GET['search'])) {
		$search = $_GET['search'];
	}
    	
	$search = strtolower(mysqli_real_escape_string($conn, $search));
	
    	$sql = "SELECT username, unit_name, unit_type FROM user WHERE username LIKE '%" . $search . "%' AND approved='0' AND is_dead='0' ORDER BY last_cbill_gift;";
    	$result = mysqli_query($conn, $sql);
	$result_html = "";
	$result_length = mysqli_num_rows($result);
	while ($row = mysqli_fetch_row($result)) {
		$result_html .= "<a href='admin-view.php?u=" . $row[0] . "'>" . $row[0] . "</a>
		 " . $row[1] . "(" . $row[2] . ") <form action='admin.php' method='post' class='inline'>" .
		"<button class='inline' type='submit' name='accept' value='" . $row[0] . "'>Accept</button></form> " .
		"<form action='admin.php' method='post' class='inline'>" .
		"<button class='inline' type='submit' name='deny' value='" . $row[0] . "'>Deny</button></form><br><br>";
	}
	mysqli_free_result($result);
	if ($result_html == "") {
		$result_html = "No users awaiting approval";
	} 

$d = "";
if (isset($_GET['d'])) {
	$d = mysqli_real_escape_string($conn, $_GET['d']);
}
$m = "";
if (isset($_GET['m'])) {
	$m = mysqli_real_escape_string($conn, $_GET['m']);
}
$amechs = "";
if (isset($_GET['am'])) {
	$amechs = mysqli_real_escape_string($conn, $_GET['am']);
}
$dmechs = "";
if (isset($_GET['dm'])) {
	$dmechs = mysqli_real_escape_string($conn, $_GET['dm']);
}
$ammechs = "";
if (isset($_GET['amm'])) {
	$ammechs = mysqli_real_escape_string($conn, $_GET['amm']);
}
$dmmechs = "";
if (isset($_GET['dmm'])) {
	$dmmechs = mysqli_real_escape_string($conn, $_GET['dmm']);
}
if ($d != "" && $m != "") {
	$sql = "UPDATE `match` SET ";
	if ($amechs != "") {
		$sql .= "attacker_lost_mechs='" . $amechs . "', ";
	}
	if ($dmechs != "") {
		$sql .= "defender_lost_mechs='" . $dmechs . "', ";
	}
	if ($ammechs != "") {
		$sql .= "amerc_lost_mechs='" . $ammechs . "', ";
	}
	if ($dmmechs != "") {
		$sql .= "dmerc_lost_mechs='" . $dmmechs . "', ";
	}
	if ($d == 'Tie') {
		$sql .= "winner='Tie' WHERE match_id=" . $m . ";";
	}
	$sql1 = "SELECT attacker, defender FROM `match` WHERE match_id=" . $m . ";";
	$result = mysqli_query($conn, $sql1);
	$att = "";
	$def = "";
	if ($row = $result->fetch_row()) {
		$att=$row[0];
		$def=$row[1];
	}
	mysqli_free_result($result);
	if ($d == 'win') {
		$sql .= "winner='" . $att . "' WHERE match_id=" . $m . ";";
	}
	if ($d == 'loss') {
		$sql .= "winner='" . $def . "' WHERE match_id=" . $m . ";";
	}
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	resolveMatch($conn, $m, false);
	$_SESSION['flashMessage'] = "Match " . $m . " resolved";
	header('Location: admin.php');
	die();
}

$sql = "SELECT attacker, defender, mercenary, defender_mercenary, attacker_url, defender_url, winner, attacker_lost_mechs, " .
	"defender_lost_mechs, amerc_lost_mechs, dmerc_lost_mechs, match_id, report_response FROM `match` WHERE " .
	"NOT(report_response IS NULL) AND resolved IS NULL;";
$result = mysqli_query($conn, $sql);
$match_results = "";
while ($row = $result->fetch_row()) {
	$match_results .= "<form action='admin.php' method='get'><p>Match between <a href='profile.php?u=" . $row[0] . "'>" . $row[0] . "</a>, <a href='profile.php?u=" . $row[1] . "'>" . $row[1] . "</a>";
	if (!($row[2] == NULL || $row[2] == "")) {
		$match_results .= ", <a href='profile.php?u=" . $row[2] . "'>" . $row[2] . "</a>";
	}
	if (!($row[3] == NULL || $row[3] == "")) {
		$match_results .= ", <a href='profile.php?u=" . $row[3] . "'>" . $row[3] . "</a>";
	}
	$match_results .= "<br>";
	$match_results .= "<input class='hide' type='hidden' name='m' value='" . $row[11] . "' />";

	if (!($row[4] == NULL || $row[4] == "")) {
		$match_results .= $row[0] . "'s screenshot: <a target='_blank' href='" . $row[4] . "'>" . $row[4] . "</a><br>";
	}
	if (!($row[5] == NULL || $row[5] == "")) {
		$match_results .= $row[1] . "'s screenshot: <a target='_blank' href='" . $row[5] . "'>" . $row[5] . "</a><br>";
	}
	$match_results .= $row[6] . " was the winner<br>";
	if (!($row[7] == NULL || $row[7] == "")) {
		$match_results .= $row[0] . "'s lost mechs: " . $row[7] . "<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='am' value='" . $row[7] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	} else {
		$match_results .= $row[0] . "'s lost mechs: none<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='am' value='" . $row[7] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	}
	if (!($row[8] == NULL || $row[8] == "")) {
		$match_results .= $row[1] . "'s lost mechs: " . $row[8] . "<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='dm' value='" . $row[8] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	} else {
		$match_results .= $row[1] . "'s lost mechs: none<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='dm' value='" . $row[8] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	}
	if (!($row[9] == NULL || $row[9] == "")) {
		$match_results .= $row[2] . "'s lost mechs: " . $row[9] . "<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='amm' value='" . $row[9] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	} elseif ($row[2] != "") {
		$match_results .= $row[2] . "'s lost mechs: none<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='amm' value='" . $row[9] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	}
	if (!($row[10] == NULL || $row[10] == "")) {
		$match_results .= $row[3] . "'s lost mechs: " . $row[10] . "<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='dmm' value='" . $row[10] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	} elseif ($row[3] != "") {
		$match_results .= $row[3] . "'s lost mechs: none<br>";
		$match_results .= "<input type='textfield' class='wdth-75' name='dmm' value='" . $row[10] . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br>";
	}
	$match_id = $row[11];
	$match_results .= "Match Report Denied on: <span class='red'>" . $row[12] . "</span><br>";
	$match_results .= "<button name='d' value='win'>" . $row[0] . " won</button> | <button name='d' value='loss'>" . $row[1] . 
			" won</button> | <button name='d' value='Tie'>tie game</button>";

	$match_results .= "</p></form><br>";
}
if ($match_results == "") {
	$match_results = "No matches awaiting arbitration";
}
mysqli_free_result($result);
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin-cmatches.php">Current Matches</a> | <a href="admin-pmatches.php">Past Matches</a> |
  <a href="admin-planet.php">Planet Manager</a> | <a href="admin-dropship.php">Dropship Manager</a> |
  <a href="admin-user.php">User Manager</a> | <a href="admin-market.php">Market Manager</a><br><br>

  <div class='left wdth-50'>
	<h3>Accounts Awaiting Approval</h3><br>
	<form action='admin.php' method='get'>
		<input class='inline left' type='textfield' name='search' /><input type='submit' value='Search' class='left inline' />
	</form>
	<div class='clearfix'></div><br>
  	<?php echo "Search Results (" . $result_length . "):<br>";
		echo "<br>" . $result_html;
  	?>
  </div>
  <div class='left wdth-50'>
	<h3>Matches Awaiting Arbitration</h3><br>
	<?php echo $match_results; ?>
  </div>
  <div class='clearfix'></div>


	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>