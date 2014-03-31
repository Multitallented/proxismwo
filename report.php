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

	if (!isset($_POST['m']) && !isset($_GET['m'])) {
		$_SESSION['flashMessage'] = "No Match by that name.";
		header('Location: index.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to report a match";
		header('Location: index.php');
		die();
	}
    $conn = getConnection();
	$match_id = -1;
	if (isset($_POST['m'])) {
		$match_id = mysqli_real_escape_string($conn, $_POST['m']);
	} else {
		$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	}
	$attacker = "";
	$defender = "";
	$responded = "";
	$report_due = "";
	$reported = "";
	$mercenary = "";
	$mercenary_time = "";
	$mercenary_qty = 0;
	$defender_mercenary = "";
	$defender_mercenary_time = "";
	$defender_mercenary_qty = 0;
	$amechs = "";
	$dmechs = ""; 
	$ammechs = "";
	$dmmechs = "";
	$planet_name = "";
	$planet_value = 0;
	$match_conditions = "Normal";
	$reporter = "";
	$attacker_lost_mechs = "";
	$defender_lost_mechs = "";
	$winner = "";
	$error_message = "";
	$timestamp = date('Y-m-d H:i:s');
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));

	/*if ($username != 'tester01' && $username != 'tester02') {
		$_SESSION['flashMessage'] = "Match reporting is undergoing maintenance. Try again later";
		header('Location: index.php');
		die();
	}*/

	$sql = "SELECT attacker, defender, planet_name, responded, reported, mercenary, mercenary_time, defender_mercenary, " .
		"defender_mercenary_time, attacker_url, defender_url, attacker_lost_mechs, defender_lost_mechs, winner" . 
		", amerc_lost_mechs, dmerc_lost_mechs, report_response, defender_mercenary_qty, mercenary_qty, resolved FROM `match` WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker = $row[0];
		$defender = $row[1];
		if (!($username == $attacker || $username == $defender)) {
			$_SESSION['flashMessage'] = "You can't report a match you were not in--mercenaries can't report matches";
			header('Location: /mwo/');
			die();
		}
		$planet_name = $row[2];
		if ($planet_name == NULL) {
			$planet_name = "";
		}
		$responded = $row[3];
		if ($responded == NULL || $responded == "" || (!($mercenary == NULL || $mercenary == "") && 
				($mercenary_time == NULL || $mercenary_time == ""))) {
			$_SESSION['flashMessage'] = "You can't report a match that has not be created yet";
			header('Location: index.php');
			die();
		}
		$report_due = date('Y-m-d H:i:s', strtotime($responded) + 604800);
		$reported = $row[4];
		$mercenary = $row[5];
		if ($username != $attacker && $username != $mercenary && $reported == "") {
			$_SESSION['flashMessage'] = "Only attackers can report matches";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$mercenary_time = $row[6];
		$defender_mercenary = $row[7];
		$defender_mercenary_time = $row[8];
		if ($mercenary == NULL) {
			$mercenary = "";
		}
		if ($defender_mercenary == NULL) {
			$defender_mercenary = "";
		}
		$attacker_url = $row[9];
		$defender_url = $row[10];
		if (!($attacker_url == "")) {
			$reporter = $attacker;
		} elseif (!($defender_url == "")) {
			$reporter = $defender;
		}
		/*if ($reporter == $username) {
			$_SESSION['flashMessage'] = "You have already reported this match";
			header('Location: index.php');
			die();
		}*/
		$attacker_lost_mechs = $row[11];
		$defender_lost_mechs = $row[12];
		$winner = $row[13];
		$amerc_lost_mechs = $row[14];
		$dmerc_lost_mechs = $row[15];
		if ($row[16] != "" || $row[19] != "") {
			$_SESSION['flashMessage'] = "This match has already been resolved.";
			header('Location: index.php');
			die();
		}
		$defender_mercenary_qty = $row[17];
		$mercenary_qty = $row[18];
	}
	mysqli_free_result($result);
	$tempArray = array();
	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $attacker . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if (isset($tempArray[$row[0]])) {
			$tempArray[$row[0]] = $tempArray[$row[0]] + $row[1];
		} else {
			$tempArray[$row[0]] = $row[1];
		}
	}
	mysqli_free_result($result);
	foreach(array_keys($tempArray) as $key) {
		$amechs .= $tempArray[$key] . " " . $key . ", ";
	}
	$amechs = substr($amechs, 0, strlen($amechs) - 2);
	$tempArray = array();
	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if (isset($tempArray[$row[0]])) {
			$tempArray[$row[0]] = $tempArray[$row[0]] + $row[1];
		} else {
			$tempArray[$row[0]] = $row[1];
		}
	}
	mysqli_free_result($result);
	foreach(array_keys($tempArray) as $key) {
		$dmechs .= $tempArray[$key] . " " . $key . ", ";
	}
	$dmechs = substr($dmechs, 0, strlen($dmechs) - 2);
	$tempArray = array();
	if (!($mercenary == "")) {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			if (isset($tempArray[$row[0]])) {
				$tempArray[$row[0]] = $tempArray[$row[0]] + $row[1];
			} else {
				$tempArray[$row[0]] = $row[1];
			}
		}
		mysqli_free_result($result);
		foreach(array_keys($tempArray) as $key) {
			$ammechs .= $tempArray[$key] . " " . $key . ", ";
		}
		$ammechs = substr($ammechs, 0, strlen($ammechs) - 2);
	}
	$tempArray = array();
	if ($defender_mercenary != "") {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender_mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			if (isset($tempArray[$row[0]])) {
				$tempArray[$row[0]] = $tempArray[$row[0]] + $row[1];
			} else {
				$tempArray[$row[0]] = $row[1];
			}
		}
		mysqli_free_result($result);
		foreach(array_keys($tempArray) as $key) {
			$dmmechs .= $tempArray[$key] . " " . $key . ", ";
		}
		$dmmechs = substr($dmmechs, 0, strlen($dmmechs) - 2);
	}
	
	if ($planet_name != "") {
		$sql = "SELECT cbill_value, match_conditions FROM planet WHERE planet_name='" . $planet_name . "';";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$planet_value = $row[0];
			$match_conditions = $row[1];
		}
		mysqli_free_result($result);
	}
	if (isset($_POST['r'])) {
	if ($_POST['r'] == "agree") {
		resolveMatch($conn, $match_id, false);
		$_SESSION['flashMessage'] = "Report has been confirmed";
		header('Location: score.php?m=' . $match_id);
		die();
	} elseif ($_POST['r'] == "disagree") {
		if (!($report_response == "")) {
			$_SESSION['flashMessage'] = "You have already responded to this report.";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if (!isset($_POST['url']) || $_POST['url'] == "") {
			$_SESSION['flashMessage'] = "Please provide a screenshot url";
			header('Location: report.php?m=' . $match_id);
			die();
		}
		$url = mysqli_real_escape_string($conn, $_POST['url']);
		$cur_timestamp = date('Y-m-d H:i:s');
		if ($attacker_url == "" || $attacker_url == NULL) {
			$sql = "UPDATE `match` SET attacker_url='" . $url .
				"', report_response='" . $cur_timestamp . "' WHERE match_id=" . $match_id . ";";
		} else {
			$sql = "UPDATE `match` SET defender_url='" . $url .
				"', report_response='" . $cur_timestamp . "' WHERE match_id=" . $match_id . ";";
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "UPDATE notifications SET value=-1 WHERE created='" . $reported . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Match disagreement sent. An admin will resolve this shortly";
		header('Location: profile.php?u=' . $username);
		die();
	} else {
		if ($reported != "") {
			$_SESSION['flashMessage'] = "Report has already been sent. Awaiting confirmation from opposing team.";
			header('Location: report.php?m=' . $match_id);
			die();
		}
		$error_message = "";
		if (!(isset($_POST['amechs']) && isset($_POST['dmechs']) && isset($_POST['url']) && isset($_POST['winner']))) {
			$error_message = "Please enter all fields<br>";
	   	} else {
	  	$pamechs = mysqli_real_escape_string($conn, $_POST['amechs']);
          	$pdmechs = mysqli_real_escape_string($conn, $_POST['dmechs']);
		$pammechs = "";
		if (isset($_POST['ammechs'])) {
          		$pammechs = mysqli_real_escape_string($conn, $_POST['ammechs']);
		}
		$pdmmechs = "";
		if (isset($_POST['dmmechs'])) {
          		$pdmmechs = mysqli_real_escape_string($conn, $_POST['dmmechs']);
		}
	   	$url = mysqli_real_escape_string($conn, $_POST['url']);
	   	$winner = mysqli_real_escape_string($conn, $_POST['winner']);
		
		if ($pamechs != "") {
		foreach (explode(", ", $pamechs) as $mech) {
			$contained = 0;
			$mech_qty = explode(" ", $mech)[0];
			$mech_var = explode(" ", $mech)[1];
			foreach (explode(", ", $amechs) as $nmech) {
				if (explode(" ",$nmech)[1] == $mech_var && !(explode(" ",$nmech)[0] < $mech_qty)) {
					$contained = 1;
				}
			}
			if ($contained == 0) {
				$error_message .= $attacker . " does not own " . $mech_qty . $mech_var . "<br>";
			}
		}
		}
		if ($pdmechs != "") {
		foreach (explode(", ", $pdmechs) as $mech) {
			$contained = 0;
			$mech_qty = explode(" ", $mech)[0];
			$mech_var = explode(" ", $mech)[1];
			foreach (explode(", ", $dmechs) as $nmech) {
				if (explode(" ",$nmech)[1] == $mech_var && !(explode(" ",$nmech)[0] < $mech_qty)) {
					$contained = 1;
				}
			}
			if ($contained == 0) {
				$error_message .= $defender . " does not own " . $mech_qty . $mech_var . "<br>";
			}
		}
		}
		if ($pammechs != "") {
		if ($mercenary != "") {
			foreach (explode(", ", $pammechs) as $mech) {
				$contained = 0;
				$mech_qty = explode(" ", $mech)[0];
				$mech_var = explode(" ", $mech)[1];
				foreach (explode(", ", $ammechs) as $nmech) {
					if (explode(" ",$nmech)[1] == $mech_var && !(explode(" ",$nmech)[0] < $mech_qty)) {
						$contained = 1;
					}
				}
				if ($contained == 0) {
					$error_message .= $mercenary . " does not own " . $mech_qty . $mech_var . "<br>";
				}
			}
		}
		}
		if ($pdmmechs != "") {
		if ($defender_mercenary != "") {
			foreach (explode(", ", $pdmmechs) as $mech) {
				$contained = 0;
				$mech_qty = explode(" ", $mech)[0];
				$mech_var = explode(" ", $mech)[1];
				foreach (explode(", ", $dmmechs) as $nmech) {
					if (explode(" ",$nmech)[1] == $mech_var && !(explode(" ",$nmech)[0] < $mech_qty)) {
						$contained = 1;
					}
				}
				if ($contained == 0) {
					$error_message .= $defender_mercenary . " does not own " . $mech_qty . $mech_var . "<br>";
				}
			}
		}
		}
		if (!($winner == "Tie" || $winner == $attacker || $winner == $defender)) {
			$error_message .= "The winner must be the defender, attacker or Tie";
		}
		if ($url == "") {
			$error_message .= "Please enter a screenshot url";
		}
		if ($error_message == "") {
			$cur_timestamp = date('Y-m-d H:i:s');
			$sql = "UPDATE cw.match SET reported='" . $cur_timestamp . "', last_action='" . $cur_timestamp . "', winner='" . $winner . "', ";
			if ($username == $attacker) {
				$sql .= "attacker_url='" . $url . "', ";
			} else {
				$sql .= "defender_url='" . $url . "', ";
			}
			$sql .= "attacker_lost_mechs='" . $pamechs . "', defender_lost_mechs='" . $pdmechs . "'";
			if (!($pammechs == "")) {
				$sql .= ", amerc_lost_mechs='" . $pammechs . "'";
			}
			if (!($pdmmechs == "")) {
				$sql .= ", dmerc_lost_mechs='" . $pdmmechs . "'";
			}
			$sql .= " WHERE match_id=" . $match_id . ";";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			
			$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . " AND (notification_type='defend'" .
				" OR notification_type='defend declared')";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$sql = "INSERT INTO notifications VALUES ('report', '";
			if (!($username == $attacker)) {
				$sql .= $defender . "', '" . $cur_timestamp . "', '" . $attacker . "', " . $match_id . ");";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$sql = "INSERT INTO notifications VALUES ('report declared', '" . $attacker . "', '" .
					$cur_timestamp . "', '" . $defender . "', " . $match_id . ");";
			} else {
				$sql .= $attacker . "', '" . $cur_timestamp . "', '" . $defender . "', " . $match_id . ");";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$sql = "INSERT INTO notifications VALUES ('report declared', '" . $defender . "', '" .
					$cur_timestamp . "', '" . $attacker . "', " . $match_id . ");";
			}
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$_SESSION['flashMessage'] = "Match report sent. Please wait for opponent to confirm the report.";
			header('Location: profile.php?u=' . $username);
			die();
		}

		} //all fields entered
	}
	} //isset($_POST
	
	mysqli_close($conn);
  ?>
	<h2>Report Match Results for Match <?php echo $match_id; ?></h2><br>
	<form action='report.php' method='post'>
	<input class='hide' type='hidden' name='m' value='<?php echo $match_id; ?>' />
	<?php if ($error_message != "") {
		echo "<p class='red'>" . $error_message . "</p><br>";
	} ?>
	<?php if ($reported != "") {
		echo "<span class='red'>" .$reporter . " has already reported this match. Awaiting confirmation</span></p><br>";
		
		echo "<h4>" . $reporter . "'s Report</h4>";
		
		if ($winner == "Tie") {
			echo "<p class='grey'>The match was a tie<br>";
		} else {
			echo "<p class='grey'>" . $winner . " won<br>";
		}
		echo $attacker . " lost mechs: " . $attacker_lost_mechs .
			"<br>" . $defender . " lost  mechs: " . $defender_lost_mechs;
		if ($mercenary != "") {
			if ($amerc_lost_mechs == "") {
				echo "<br>" . $mercenary . " lost no mechs";
			} else {
				echo "<br>" . $mercenary . " lost mechs: " . $amerc_lost_mechs;
			}
		}
		if ($defender_mercenary != "") {
			if ($dmerc_lost_mechs == "") {
				echo "<br>" . $defender_mercenary . " lost no mechs";
			} else {
				echo "<br>" . $defender_mercenary . " lost mechs: " . $dmerc_lost_mechs;
			}
		}
		if ($attacker_url == "") {
			echo "<br>Screenshot: <a target='_blank' href='" . $defender_url . "'>" . $defender_url . "</a></p><br>";
		} else {
			echo "<br>Screenshot: <a target='_blank' href='" . $attacker_url . "'>" . $attacker_url . "</a></p><br>";
		}
	} ?>
	<?php if ($reporter != "") { 
		echo "<!-- ";
	} ?>
	<div class='wdth-50 left'>
	
	<h4><strong><?php echo $attacker; ?></strong></h4><br>
	<?php if ($reported != NULL) {
		echo "Mechs taken: <br>" . $amechs . "<br><br>";
		echo "<span class='red'>Please use exactly this format: 1 JR7-F, 4 AS7-D-DC, 3 CTF-3D</span><br>";
	}?>
	<span class='red'>Mechs Lost: <br><input type='textfield' style='width: 300px;' name='amechs' value='<?php echo $pamechs; ?>' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' />
	<br><br></span>
	<?php if ($mercenary != "") {
		echo "<h4><strong>" . $mercenary . "</strong></h4><br>";
		if ($reported != NULL) {
			echo "Mechs taken: <br>" . $ammechs . "<br><br>";
			echo "<span class='red'>Please use exactly this format: 1 JR7-F, 4 AS7-D-DC, 3 CTF-3D</span><br>";
		}
		echo "<span class='red'>Mechs Lost:<br> <input type='textfield' style='width: 300px;' name='ammechs' value='" . $pammechs . "'  placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' />
			<br><br></span>";
	}?>

	</div>
	<div class='wdth-50 left'>

	<h4><strong><?php echo $defender; ?></strong></h4><br>
	<?php if ($reported != NULL) {
		echo "Mechs taken: <br>" . $dmechs . "<br><br>";
		echo "<span class='red'>Please use exactly this format: 1 JR7-F, 4 AS7-D-DC, 3 CTF-3D</span><br>";
	} ?>
	<span class='red'>Mechs Lost: <br><input type='textfield' style='width: 300px;' name='dmechs' value='<?php echo $pdmechs; ?>' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' />
	<br><br></span>

	<?php if ($defender_mercenary != "") {
		echo "<h4><strong>" . $defender_mercenary . "</strong></h4><br>";
		if ($reported != NULL) {
			echo "Mechs taken: <br>" . $dmmechs . "<br><br>";
			echo "<span class='red'>Please use exactly this format: 1 JR7-F, 4 AS7-D-DC, 3 CTF-3D</span><br>";
		}
		echo "<span class='red'>Mechs Lost:<br> <input type='textfield' style='width: 300px;' name='dmmechs' value='" .
			$pdmmechs . "' placeholder='1 JR7-F, 10 AS7-D-DC, 1 CTF-3D' /><br><br></span>";
	}?>
	

	</div>
	<div class='clearfix'></div>
	<?php if ($reporter != "") { 
		echo "--> ";
	} ?>
	<?php
	if ($reported != "") {
		echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> brought: <span class='red'>" . $amechs . "</span><br><br>";
	       echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> brought: <span class='red'>" . $dmechs . "</span><br><br>";
	 	if ($mercenary != "") {
			echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> hired <a href='profile.php?u=" . $mercenary . "'>" . $mercenary . 
			"</a> who brought: <span class='red'>" . $ammechs . "</span><br><br>";
	}
		if ($defender_mercenary != "") {
			echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> hired <a href='profile.php?u=" . $defender_mercenary . "'>" . $defender_mercenary . 
				"</a> who brought: <span class='red'>" . $dmmechs . "</span><br><br>";
		}
	} ?>
	Screenshot URL: <input type='textfield' style='width: 500px;' name='url' value='<?php echo $url; ?>' /><br><br>
	<?php if ($reporter != "") { 
		echo "<!-- ";
	} ?>
	<h2 class="inline">Winner:</h2> <select name='winner'>
	<option>(Choose One)</option>
	<?php echo "<option>" . $attacker . "</option><option>" . $defender . "</option>"; ?>
	<option>Tie</option></select><br><br>
	<?php if ($reporter != "") { 
		echo "--> ";
	} ?>
	<p class='center'>
	<?php if ($reporter  == "") {
		echo "<input type='submit' name='r' value='Submit' />";
	} elseif ($reporter != $username) {
		echo "<button name='r' value='agree'>Agree</button> | ";
		echo "<button name='r' value='disagree'>Disagree</button>";
		echo "<br>*You only need a screenshot if you disagree with the report.";
	} else {
		echo "<button name='r' value='disagree'>Dispute</button>";
		echo "<br>*You need a screenshot if you want to dispute the report.";
	} ?>
	</p>

	</form>


	<?php echo $footer; ?>
  </div>
</body>
</html>