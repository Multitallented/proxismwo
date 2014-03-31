<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/reset.css" />
  <?php session_start();
		include 'header.php'; 
  		
  ?>
</head>
<body>
<div id="container">
	<?php echo $header; ?>
You should be redirected momentarily<br>

<?php
if (!isset($_POST['username']) || !isset($_POST['password'])) {
	$_SESSION['flashMessage'] = "Please enter a username or password";
	header('Location: index.php');
	die();
}
$conn = getConnection();
$username = strtolower(mysqli_real_escape_string($conn, $_POST['username']));

$password = md5(mysqli_real_escape_string($conn, $_POST['password']));
$sql = "SELECT username, cbills, last_cbill_gift, approved, is_dead, unit_type FROM user WHERE username='" . $username . "' AND password='" . $password . "';";
$result = mysqli_query($conn, $sql);
$cbills = 0;
$new_cbills = 0;
$last_cbill = "";
$unit_type = "";
$mechPrices = getMechValues($conn);
if ($row = $result->fetch_row()) {
	$_SESSION['username'] = $_POST['username'];
	$cbills = $row[1];
	$new_cbills = $row[1];
	$last_cbill = $row[2];
	if ($row[3] == 0 || $row[4] == 1) {
		$last_cbill = "";
	}
	$unit_type = $row[5];
} else {
	$_SESSION['flashMessage'] = "Username and/or Password incorrect";
}
$old_cbills = $cbills;
mysqli_free_result($result);
if (!($last_cbill == "") && (strtotime('-1 day') > strtotime($last_cbill))) {
	$sql = "SELECT mech, quantity FROM mech WHERE username='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$new_cbills += $mechPrices[$row[0]]['sell_price'] * $row[1];
	}
	mysqli_free_result($result);
	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$new_cbills += $mechPrices[$row[0]]['sell_price'] * $row[1];
	}
	mysqli_free_result($result);
	
	if ($unit_type != "admin") {
		$sql = "UPDATE planet SET cbill_value=(cbill_value + 200000) WHERE owner_name='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		$num_rows = mysqli_affected_rows($conn);
		$cbills += 250000 * $num_rows;
		mysqli_free_result($result);
	}
	
	if ($new_cbills < 130000000) {
		$cbills += 15000000;
	}
	if ($old_cbills < $cbills) {
		$_SESSION['flashMessage'] = "You have been awarded "  . number_format($cbills - $old_cbills) . "cbills for logging in today";
	}
	$sql = "UPDATE user SET last_login='" . date("Y-m-d H:i:s") . "', cbills=" . $cbills . ", last_cbill_gift='" .
		date("Y-m-d H:i:s") . "' WHERE username='" . $username . "';";
} else {
	$sql = "UPDATE user SET last_login='" . date("Y-m-d H:i:s") . "' WHERE username='" . $username . "';";
}

$result = mysqli_query($conn, $sql);
mysqli_free_result($result);
$sql = "SELECT declared, responded, reported, report_response, match_id, attacker, defender, mercenary, defender_mercenary" . 
			", mercenary_time, defender_mercenary_time FROM `match` WHERE (attacker='" .
			$username . "' OR defender='" . $username . "' OR mercenary='" . $username . "' OR defender_mercenary='" . $username . 
			"') AND ISNULL(resolved);";
$result = mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	$declared = $row[0];
	$responded = $row[1];
	$reported = $row[2];
	$report_response = $row[3];
	$match_id = $row[4];
	$mercenary = $row[7];
	$defender_mercenary = $row[8];
	$mercenary_time = $row[9];
	$defender_mercenary_time = $row[10];
	$cur_timestamp = date('Y-m-d H:i:s');
	if (($responded == "" || ($defender_mercenary != "" && $defender_mercenary_time == "")) && 
			(($declared != "" && strtotime('-4 day') > strtotime($declared) && $mercenary == "") ||
			($mercenary_time != "" && strtotime('-4 day') > strtotime($mercenary_time) && $mercenary != ""))) {
		$sql = "UPDATE `match` SET winner='" . $row[5] . "', responded='" . $cur_timestamp . 
			"', reported='" . $cur_timestamp . "' WHERE match_id=" . $match_id . ";";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
		if ($mercenary != "") {
			$sql = "UPDATE notifications SET value=1 WHERE value=" . $match_id . " AND notification_type='attack declared';";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
			$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . " AND notification_type='attack';";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
		} else {
			$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . " AND notification_type='attack';";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
			$sql = "UPDATE notifications SET value=1 WHERE value=" . $match_id . " AND notification_type='attack declared';";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
		}
		resolveMatch($conn, $match_id, true);
	} elseif ($reported == "" && 
			(($defender_mercenary == "" && $responded != "" && strtotime('-7 day') > strtotime($responded)) ||
			($defender_mercenary != "" && $defender_mercenary_time != "" && strtotime('-7 day') > strtotime($defender_mercenary_time)))) {
		$sql = "UPDATE `match` SET winner='Tie', reported='" . $cur_timestamp . "', report_response='" .
			$cur_timestamp . "' WHERE match_id=" . $match_id . ";";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
		$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . ";";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
		resolveMatch($conn, $match_id, true);
	} elseif ($report_response == "" && $reported != "" && strtotime('-1 day') > strtotime($reported)) {
		$sql = "UPDATE `match` SET report_response='" . $cur_timestamp . "' WHERE match_id=" . $match_id . ";";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
		$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . ";";
		$result1 = mysqli_query($conn, $sql);
		mysqli_free_result($result1);
		resolveMatch($conn, $match_id, false);
	}
}
mysqli_free_result($result);

mysqli_close($conn);

header('Location: index.php');
?>

	<?php echo $footer; ?>
</div>

</body>
</html>