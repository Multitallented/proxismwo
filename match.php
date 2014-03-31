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
	if (!isset($_GET['m'])) {
		$_SESSION['flashMessage'] = "No Match by that name.";
		header('Location: index.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to view this page.";
		header('Location: index.php');
		die();
	}
    $conn = getConnection();
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$unit_type = "";
	$attacker = "";
	$defender = "";
	$responded = "";
	$report_due = "";
	$reported = "";
	$mercenary = "";
	$mercenary_time = "";
	$defender_mercenary = "";
	$defender_mercenary_time = "";
	$amechs = "";
	$dmechs = "";
	$ammechs = "";
	$dmmechs = "";
	$planet_value = 0;
	$match_conditions = "Normal";
	$extension = 0;
	$mechPrices = getMechValues($conn);

	$sql = "SELECT attacker, defender, planet_name, responded, reported, mercenary, mercenary_time, defender_mercenary, " .
		"defender_mercenary_time, extension, u.unit_type FROM `match` INNER JOIN user AS u ON u.username='" . $username . "' WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker = $row[0];
		$defender = $row[1];
		$planet_name = $row[2];
		if ($planet_name == NULL) {
			$planet_name = "";
		}
		$responded = $row[3];
		if ($responded == NULL || $responded == "" || (!($mercenary == NULL || $mercenary == "") && 
				($mercenary_time == NULL || $mercenary_time == ""))) {
			$_SESSION['flashMessage'] = "Match screen will be available when both units have responded";
			header('Location: index.php');
			die();
		}
		$report_due = date('Y-m-d H:i:s', strtotime($responded) + 604800);
		$reported = $row[4];
		if (!($reported == NULL || $reported == "")) {
			$_SESSION['flashMessage'] = "This match has already been played. <a href='report.php?m=" . $match_id . "'>Report Match</a>";
			header('Location: index.php');
			die();
		}
		$mercenary = $row[5];
		$mercenary_time = $row[6];
		$defender_mercenary = $row[7];
		$defender_mercenary_time = $row[8];
		if ($mercenary == NULL) {
			$mercenary = "";
		}
		if ($defender_mercenary == NULL) {
			$defender_mercenary = "";
		}
		$extension = $row[9];
		$unit_type=$row[10];
	}
	mysqli_free_result($result);

	if ($username != $attacker && $username != $mercenary && $username != $defender && $username != $defender_mercenary && $unit_type != 'admin') {
		$_SESSION['flashMessage'] = "You are not involved in that match.";
		header('Location: index.php');
		die();
	}

	if (isset($_GET['a'])) {
		if ($extension == 0 && ($username == $attacker || $username == $mercenary)) {
			$sql = "UPDATE `match` SET extension=1 WHERE match_id=" . $match_id . ";";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$_SESSION['flashMessage'] = "Match deadline extension request sent.";
			header('Location: match.php?m=' . $match_id);
			die();
		} elseif ($extension == 1 && ($username == $defender || $username == $defender_mercenary)) {
			if ($defender_mercenary == "") {
				$sql = "UPDATE `match` SET responded='" . add_date($responded, 4) . 
					"', extension=0 WHERE match_id=" . $match_id . ";";
			} else {
				$sql = "UPDATE `match` SET defender_mercenary_time='" . add_date($defender_mercenary_time, 4) . 
					"', extension=0 WHERE match_id=" . $match_id . ";";
			}
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$_SESSION['flashMessage'] = "Match deadline extended.";
			header('Location: match.php?m=' . $match_id);
			die();
		}
	}


	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $attacker . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($username==$attacker || $username==$mercenary) {
		while ($row = $result->fetch_row()) {
			$amechs .= $row[1] . " " . $row[0] . ", ";
		}
		$amechs = substr($amechs, 0, strlen($amechs) - 2);
	} else {
		$amechs = 0;
		while ($row = $result->fetch_row()) {
			$amechs += ($row[1] * $mechPrices[$row[0]]['tons']);
		}
		$amechs .= " tons";
	}
	mysqli_free_result($result);

	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($username==$defender || $username==$defender_mercenary) {
		while ($row = $result->fetch_row()) {
			$dmechs .= $row[1] . " " . $row[0] . ", ";
		}
		$dmechs = substr($dmechs, 0, strlen($dmechs) - 2);
	} else {
		$dmechs = 0;
		while ($row = $result->fetch_row()) {
			$dmechs += ($row[1] * $mechPrices[$row[0]]['tons']);
		}
		$dmechs .= " tons";
	}
	mysqli_free_result($result);

	if ($mercenary != "") {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		if ($username==$mercenary) {
			while ($row = $result->fetch_row()) {
				$ammechs .= $row[1] . " " . $row[0] . ", ";
			}
			$ammechs = substr($ammechs, 0, strlen($ammechs) - 2);
		} else {
			$ammechs = 0;
			while ($row = $result->fetch_row()) {
				$ammechs += ($row[1] * $mechPrices[$row[0]]['tons']);
			}
			$ammechs .= " tons";
		}
		mysqli_free_result($result);
	}

	if ($defender_mercenary != "") {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender_mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		if ($username==$defender_mercenary) {
			while ($row = $result->fetch_row()) {
				$dmmechs .= $row[1] . " " . $row[0] . ", ";
			}
			$dmmechs = substr($dmmechs, 0, strlen($dmmechs) - 2);
		} else {
			$dmmechs = 0;
			while ($row = $result->fetch_row()) {
				$dmmechs += ($row[1] * $mechPrices[$row[0]]['tons']);
			}
			$dmmechs .= " tons";
		}
		mysqli_free_result($result);
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
	$attacker_type = "";
	$sql = "SELECT unit_type FROM user WHERE username='" . $attacker . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker_type = $row[0];
	}
	mysqli_free_result($result);
	mysqli_close($conn);
  ?>
	<h2>Match <?php echo $match_id;?></h2><br>
	<p class='grey'>Current time: <?php echo date('Y-m-d H:i:s'); ?></p>
	<p class='grey'>This match is to be played between
	<?php 
	if ($defender_mercenary == "") {
	   echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker .
		 "</a> and <a href='profile.php?u=" . $defender . "'>" . $defender . "</a> within a week of <span class='gold'>" . $responded . 
		"</span> (<span class='red'>" . $report_due . "</span>)<br><br>"; 
	} else {
	   echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker .
		 "</a> and <a href='profile.php?u=" . $defender . "'>" . $defender . "</a> within a week of <span class='gold'>" . $defender_mercenary_time . 
		"</span> (<span class='red'>" . add_date($defender_mercenary_time, 7) . "</span>)<br><br>"; 
	}
	?>
	<?php echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> is bringing: <span class='red'>" . $amechs . "</span><br><br>";
	      echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> is bringing: <span class='red'>" . $dmechs . "</span><br><br>"; ?>
	<?php if ($mercenary != "") {
		echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> has hired <a href='profile.php?u=" . $mercenary . "'>" . $mercenary . 
			"</a> bringing: <span class='red'>" . $ammechs . "</span><br><br>";
	}
	if ($defender_mercenary != "") {
		echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> has hired <a href='profile.php?u=" . $defender_mercenary . "'>" . $defender_mercenary . 
			"</a> bringing: <span class='red'>" . $dmmechs . "</span><br><br>";
	} ?>
	<?php if ($planet_name != "") {
		echo $attacker . " is fighting on " . $planet_name . " worth <span class='green'>" . number_format($planet_value) . "</span><br>";
		echo "Match conditions: <span class='red'>" . $match_conditions . "</span><br><br>";
	} ?>
	<a class='bttn' href='report.php?m=<?php echo $match_id; ?>'>Report Match</a>

	<?php
	if ($unit_type=='clan') {
		echo "<a class='bttn' href='swapmech.php?m=" . $match_id . "'>Swap Mechs</a>";
	}
	?>
	<br><br>
	<?php 
	if ($extension == 0 && ($username == $attacker || $username == $mercenary)) {
		echo "<span class='grey'>Do you need more time to play this match?</span><br>" . 
			"<a class='bttn' href='match.php?a=extend&m=" . $match_id . "'>Extend Deadline</a>";
	} elseif ($extension == 1 && ($username == $attacker || $username == $mercenary)) {
		echo "<span class='green'>You have applied to extend the match deadline. Waiting for a response from your opponent.</span>";
	} elseif ($extension == 1 && ($username == $defender || $username == $defender_mercenary)) {
		echo "<span class='green'>Your opponent wants to extend the deadline for this match by 4 days.</span><br>" .
			"<a class='bttn' href='match.php?a=extend&m=" . $match_id . "'>Grant Extension</a>";
	}
	?>
	

	</p>


	<?php echo $footer; ?>
  </div>
</body>
</html>