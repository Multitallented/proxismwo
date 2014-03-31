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

if ($match_id != 0 && $action == "cancel") {
	$sql = "SELECT attacker, defender, mercenary, defender_mercenary, mercenary_qty, defender_mercenary_qty FROM `match` WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		if ($row[0] != "" && $row[4] < 8) {
			return_mechs($match_id, $row[0], $conn);
		}
		if ($row[1] != "" && $row[5] < 8) {
			return_mechs($match_id, $row[1], $conn);
		}
		if ($row[2] != "") {
			return_mechs($match_id, $row[2], $conn);
		}
		if ($row[3] != "") {
			return_mechs($match_id, $row[3], $conn);
		}
	}
	mysqli_free_result($result);
	$sql = "DELETE FROM `match` WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$sql = "DELETE FROM notifications WHERE value=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = "Match " . $match_id . " was cancelled successfully";
	header('Location: admin-cmatches.php');
	die();
}

if ($match_id != 0 && $action == "resolve") {
	resolveMatch($conn, $match_id, false);
}
if ($match_id != 0 && $action == "timeout") {
	resolveMatch($conn, $match_id, true);
}

$sql = "SELECT match_id, planet_name, attacker, defender, mercenary, defender_mercenary, declared, mercenary_time, " .
	" responded, defender_mercenary_time, reported, report_response FROM `match` WHERE ISNULL(resolved);";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>ID</th><th>planet</th><th>attacker</th><th>atkr merc</th><th>defender</th>" . 
		"<th>def merc</th><th>progress</th><th style='width: 150px;'>Actions</th>";
while ($row = $result->fetch_row()) {
  $cmatch_stage = "declared";
  $due = "";
  $can_resolve = 0;
  $can_timeout = 0;
  if ($row[11] == "" && $row[10] != "") {
    $cmatch_stage = "<span class='red'>report disagree<br>" . $row[10] . "</span>";
  } else if ($row[10] == "" && ($row[9] != "" || ($row[8] != "" && $row[5] == ""))) {
    if ($row[9] == "") {
	$due = $row[8];
    } else {
	$due = $row[9];
    }
    $can_resolve = 1;
    $can_timeout = 1;
    $cmatch_stage = "<span class='gold'>fighting<br>" . add_date($due, 7, 0, 0) . "</span>";
  } else if (!($row[9] != "" || ($row[8] != "" && $row[5] == "")) && ($row[7] != "" || ($row[6] != "" && $row[4] == ""))) {
    if ($row[7] == "") {
	$due = $row[6];
    } else {
	$due = $row[7];
    }
    $can_timeout = 1;
    $cmatch_stage = "<span class='green'>defend<br>" . add_date($due, 4, 0, 0) . "</span>";
  } else if ($row[4] != "") {
    $cmatch_stage = "<span class='grey'>hire merc<br>" . $row[6] . "</span>";
  }
  $cmatches .= "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td class='red'>" . $row[2] . "</td><td class='red'>" .
		$row[4] . "</td><td class='green'>" . $row[3] . "</td><td class='green'>" . $row[5] . "</td><td>" . $cmatch_stage . 
		"</td><td><a class='bttn' href='admin-cmatches.php?m=" . $row[0] . "&a=cancel'>Cancel</a><a class='bttn' href='admin-cmatches-edit.php?m=" . $row[0] . "'>Edit</a>";
  if ($can_resolve == 1) {
	$cmatches .= "<a class='bttn' href='admin-cmatches.php?m=" . $row[0] . "&a=resolve'>Resolve</a>";
  }
  if ($can_timeout == 1) {
	$cmatches .= "<a class='bttn' href='admin-cmatches.php?m=" . $row[0] . "&a=timeout'>Timeout</a>";
  }
  $cmatches .= "</td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

  <a href="admin.php">Admin Home</a><br><br>

	<h3>Current Matches (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>