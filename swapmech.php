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

	$mechPrices = getMechValues($conn);

	if ($unit_type != 'clan') {
		$_SESSION['flashMessage'] = "Only clans can swap mechs.";
		header('Location: index.php');
		die();
	}
	if ($username != $attacker && $username != $mercenary && $username != $defender && $username != $defender_mercenary) {
		$_SESSION['flashMessage'] = "You are not involved in that match.";
		header('Location: index.php');
		die();
	}
	
	$atons = 0;
	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $attacker . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$amechs .= $row[1] . " " . $row[0] . ", ";
		$atons += $mechPrices[$row[0]]['tons'] * $row[1];
	}
	mysqli_free_result($result);
	$amechs = substr($amechs, 0, strlen($amechs) - 2);
	$dtons = 0;

	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$dmechs .= $row[1] . " " . $row[0] . ", ";
		$dtons += $mechPrices[$row[0]]['tons'] * $row[1];
	}
	mysqli_free_result($result);
	$dmechs = substr($dmechs, 0, strlen($dmechs) - 2);

	if ($mercenary != "") {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$ammechs .= $row[1] . " " . $row[0] . ", ";
			$atons += $mechPrices[$row[0]]['tons'] * $row[1];
		}
		mysqli_free_result($result);
		$ammechs = substr($ammechs, 0, strlen($ammechs) - 2);
	}
	if ($defender_mercenary != "") {
		$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender_mercenary . "' AND match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$dmmechs .= $row[1] . " " . $row[0] . ", ";
			$dtons += $mechPrices[$row[0]]['tons'] * $row[1];
		}
		mysqli_free_result($result);
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

	$usermechs = array();
	$sql = "SELECT mech, quantity, dropship_id, planet_name FROM `match_mech` WHERE owner='" . $username . "' AND match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$usermechs[] = array('qty' => $row[1], 'mech' => $row[0], 'dropship' => $row[2], 'planet' => $row[3]);
	}
	mysqli_free_result($result);
	

	$dropships = array();
	$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name, p.location_x, p.location_y" .
		", d.capacity, d.location_x, d.location_y, d.planet_name, p.capacity FROM mech AS m LEFT OUTER JOIN" . 
		" planet AS p ON p.planet_name=m.planet_name LEFT OUTER JOIN (SELECT dr.dropship_id," . 
		" dr.capacity, pl.location_x, pl.location_y, pl.planet_name FROM dropship AS dr INNER JOIN planet AS pl ON " . 
		"dr.planet_name=pl.planet_name) AS d ON d.dropship_id=m.dropship_id WHERE m.username='" . $username . "' AND (m.planet_name='" . $planet_name . "' OR d.planet_name='" . $planet_name . "');";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != "") {
			if (isset($dropships[$row[2]])) {
				$dropships[$row[2]][$row[0]] = $row[1];
			} else {
				$dropships[$row[2]] = array($row[0] => $row[1], 'capacity' => $row[6], 'planet' => $row[9]);
			}
		} elseif ($row[3] != "") {
			if (isset($dropships[$row[3]])) {
				$dropships[$row[3]][$row[0]] = $row[1];
			} else {
				$dropships[$row[3]] = array($row[0] => $row[1], 'capacity' => $row[10]);
			}
		}
	}
	mysqli_free_result($result);

	$use_mechs = $dropships;
	$sql = "SELECT mech, quantity, dropship_id, planet_name FROM match_mech WHERE match_id='" . $match_id . "' AND owner='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != "") {
			if (isset($use_mechs[$row[2]]) && !isset($use_mechs[$row[2]][$row[0]])) {
				$use_mechs[$row[2]][$row[0]] = $row[1];
			} elseif (isset($use_mechs[$row[2]]) && isset($use_mechs[$row[2]][$row[0]])) {
				$use_mechs[$row[2]][$row[0]] += $row[1];
			}
		} elseif ($row[3] != "") {
			if (isset($use_mechs[$row[3]]) && !isset($use_mechs[$row[3]][$row[0]])) {
				$use_mechs[$row[3]][$row[0]] = $row[1];
			} elseif (isset($use_mechs[$row[3]]) && isset($use_mechs[$row[3]][$row[0]])) {
				$use_mechs[$row[3]][$row[0]] += $row[1];
			}
		}
	}
	mysqli_free_result($result);

	$mechs = array();
	$etons = 0;
	$utons = 0;

	if ($username == $defender) {
		$etons = $atons;
		$utons = $dtons;
	} else {
		$etons = $dtons;
		$utons = $atons;
	}
	if ($utons - 20 < $etons) {
		$_SESSION['flashMessage'] = "You must be dishonorable to swap mechs";
		header('Location: match.php?m=' . $match_id);
		die();
	}

	if (isset($_GET['mechs']) && $qty < 12) {
		$temp_mechs = mysqli_real_escape_string($conn, $_GET['mechs']);
		$mechs = explode(", ",$temp_mechs);
		$mech_count = 0;
		$tons = 0;
		foreach ($mechs as $mech) {
			$mech_qty = (int) substr($mech, 0, 1);
			$mech_var = substr(explode(":", $mech)[0], 1);
			$tons += $mechPrices[$mech_var]['tons'] * $mech_qty;
			$drop_id = explode(":", $mech)[1];

			if (!array_key_exists($drop_id, $use_mechs)) {
				$_SESSION['flashMessage'] = "That dropship is not available for attack";
				header('Location: swapmech.php?m=' . $match_id);
				die();
			}
			if (!array_key_exists($mech_var, $use_mechs[$drop_id])) {
				$_SESSION['flashMessage'] = "No " . $mech_var . " available in dropship " . $drop_id . " " . $planet_name;
				header('Location: swapmech.php?m=' . $match_id);
				die();
			}
			if ($use_mechs[$drop_id][$mech_var] < $mech_qty) {
				$_SESSION['flashMessage'] = "You don't have " . $mech_qty . " " . $mech_var . " in dropship " . $drop_id . " " . $planet_name;
				header('Location: swapmech.php?m=' . $match_id);
				die();
			}
			$mech_count = $mech_count + $mech_qty;
		}
		if (!($mech_count == (12 - $qty))) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $qty) . " mechs to use, not " . $mech_count;
			header('Location: swapmech.php?m=' . $match_id);
			die();
		}
	}

	if ($mechs != array()) {

		if ($etons - 50 > $tons || $etons + 20 < $tons) {
			$_SESSION['flashMessage'] = "You can only swap to a neutal tonnage deck";
			header('Location: swapmech.php?m=' . $match_id);
			die();
		}

		return_mechs($match_id, $username, $conn);

		$sql = "SELECT mech, quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . "';";
		$result = mysqli_query($conn, $sql);

		while ($row = $result->fetch_row()) {
			$mech_qty = $row[1];
			$mech_var = $row[0];
			foreach ($mechs as $mech) {
				$mmech_qty = (int) substr($mech, 0, 1);
				$mmech_var = substr(explode(":", $mech)[0], 1);
				$dropship_var = explode(":", $mech)[1];
				$sql_where = "";
				if ($mmech_var != $mech_var) {
					continue;
				}
				if ($row[3] == $dropship_var && $row[3] != "") {
					$sql_where = "planet_name='" . $row[3] . "';";
				} elseif ($row[2] == $dropship_var && $row[2] != "") {
					$sql_where = "dropship_id=" . $row[2] . ";";
				} else {
					continue;
				}
				if ($mmech_qty < $mech_qty) {
					$sql = "UPDATE mech SET quantity=" . ($mech_qty - $mmech_qty) . " WHERE username='" . $username .
						"' AND mech='" . $mech_var . "' AND " . $sql_where;
				} else {
					$sql = "DELETE FROM mech WHERE username='" . $username . "' AND mech='" . $mech_var . "' AND " . $sql_where;
				}
				$result1 = mysqli_query($conn, $sql);
				mysqli_free_result($result1);

				$sql = "INSERT INTO match_mech VALUES ('" . $mech_var . "', " . $match_id . ", '" . $username . "', " . $mmech_qty . ", ";
				if ($row[3] == $dropship_var && $row[3] != "") {
					$sql .= "NULL, '" . $row[3] . "');";
				} elseif ($row[2] == $dropship_var && $row[2] != "") {
					$sql .= $row[2] . ", NULL);";
				} else {
					continue;
				}
				$result1 = mysqli_query($conn, $sql);
				mysqli_free_result($result1);
			}
		}
		mysqli_free_result($result);

		$_SESSION['flashMessage'] = "Mechs have been swapped";
		header('Location: match.php?m=' . $match_id);
		die();

	}
	



	mysqli_close($conn);
  ?>
	<h2>Swap Mechs for Match <?php echo $match_id; ?></h2><br>
	<div class='wdth-33 left'>
	<h4>Dropships Available</h4><br>

	<?php foreach (array_keys($dropships) as $drop_id) {
		if ($drop_id == $planet_name) {
			echo "<span class='green'>" . $planet_name . "</span><span class='grey'> Mech Capacity: " . 
				$dropships[$drop_id]['capacity'] . "</span><br><br>";
		} else {
			echo "<span class='green'>" . $drop_id . " " . $dropships[$drop_id]['planet'] . 
				"</span><span class='grey'> Mech Capacity: " . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		}
		echo "<div id='" . str_replace(" ", "_", $drop_id) . "-dropship' style='margin-left: 10px;'>";
		foreach (array_keys($dropships[$drop_id]) as $mech) {
			if ($mech == 'capacity' || $mech == 'planet') {
				continue;
			}
			echo "<div id='" . str_replace(" ", "_", $drop_id) . "use" . $mech . "'><a onclick='use(\"" . $drop_id . "\", \"" . $mech . "\", " . $dropships[$drop_id][$mech] . 
				")' style='cursor: pointer;' class='bttn'>Use</a> <span class='qty'>" . $dropships[$drop_id][$mech] .
				"</span> " . $mech . "<br></div>";
		}
		echo "</div><br>";
		
	}?>
	</div>


	<div class='wdth-66 left'>
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
	<?php 
	if ($attacker == $username) {
		echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> is bringing: <span class='red'>" . $amechs . " (" . $atons . ")</span><br><br>";
	} else {
		echo "<a href='profile.php?u=" . $attacker . "'>" . $attacker . "</a> is bringing: <span class='red'> (" . $atons . ")</span><br><br>";
	}
	if ($defender == $username) {
	      echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> is bringing: <span class='red'>" . $dmechs . " (" . $dtons . ")</span><br><br>";
	} else {
	      echo "<a href='profile.php?u=" . $defender . "'>" . $defender . "</a> is bringing: <span class='red'> (" . $dtons . ")</span><br><br>";
	} ?>
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
	<br>
	
	<?php
		$mechs_value_field = "";
		$output_mechs_ready = "";
		foreach ($usermechs as $umech) {
			$tempid="";
			$isdropshipid=false;
			if ($umech['dropship'] == "") {
				$tempid = str_replace("_", " ", $umech['planet']);
			} else {
				$tempid = str_replace("_", " ", $umech['dropship']);
				$isdropshipid = true;
			}
			$output_mechs_ready .= "<div id='" . $tempid . "ret" . $umech['mech'] . "'>";
			$output_mechs_ready .= "<a class='bttn' onclick='ret(\"" . $tempid . "\", \"" . $umech['mech'] . "\", " . $umech['qty'] . ")' style='cursor: pointer;'>Return</a> <span class='qty'>";
			$output_mechs_ready .= $umech['qty'] . "</span> " . $umech['mech'] . " <span class='green'>" . $tempid;
			if ($isdropshipid) {
				$output_mechs_ready .= " " . $planet_name;
			}
			$output_mechs_ready .= "</span></div>";
			if ($mechs_value_field == "") {
				$mechs_value_field = $umech['qty'] . $umech['mech'] . ":" . $tempid;
			} else {
				$mechs_value_field .= ", " . $umech['qty'] . $umech['mech'] . ":" . $tempid;
			}
		}
	?>	

	<form class='inline' action='swapmech.php' method='get'>
	<input class='hide' type='hidden' name='m' value='<?php echo $match_id; ?>' />
	<input id='mech_input' type='hidden' class='hide' name='mechs' value='<?php echo $mechs_value_field; ?>' />
	<input class='inline' type='submit' name='attack' value='Swap Mechs' />
			
	</form>

	<br><br>
	<h4>Mechs Ready</h4><br>
	<div id='ready-mechs'>
	<?php echo $output_mechs_ready; ?>
	</div>

	</p>
	</div>
	<div class='clearfix'></div>


	<?php echo $jquery; ?>
	<script type='text/javascript'>
	function use(id, mech, qty) {
		if (qty > 1) {
			$('#' + id.replace(" ", "_") + 'use' + mech + ' .qty').text(qty - 1);
			$('#' + id.replace(" ", "_") + 'use' + mech + ' a').attr("onClick", "use(\"" + id + "\", \"" + mech + "\", " + (qty - 1) + ")");
		} else {
			$('#' + id.replace(" ", "_") + 'use' + mech).remove();
		}
		var nqty = $('#' + id.replace(" ", "_") + 'ret' + mech + ' .qty').html();
		if (nqty == null) {
			if ('<?php echo $planet_name; ?>' == id) {
				$('#ready-mechs').append("<div id='" + id.replace(" ", "_") + "ret" + mech + 
					"'><a class='bttn' onclick='ret(\"" +
					id + "\", \"" + mech + "\", " + 1 + 
					")' style='cursor: pointer;'>Return</a> <span class='qty'>" +
					"1</span> " + mech + " <span class='green'><?php echo $planet_name; ?></span><br></div>");
			} else {
				$('#ready-mechs').append("<div id='" + id.replace(" ", "_") + "ret" + mech + 
					"'><a class='bttn' onclick='ret(\"" +
					id + "\", \"" + mech + "\", " + 1 + 
					")' style='cursor: pointer;'>Return</a> <span class='qty'>" +
					"1</span> " + mech + " <span class='green'>" + id + "<?php echo $planet_name; ?></span><br></div>");
			}
		} else {
			nqty = parseInt(nqty);
			$('#' + id.replace(" ", "_") + 'ret' + mech + ' .qty').text(nqty + 1);
			$('#' + id.replace(" ", "_") + 'ret' + mech + ' a').attr("onClick", "ret(\"" + id + "\", \"" + mech + "\", " + (nqty + 1) + ")");
		}
		var value = $('#mech_input').attr('value');
		var index = value.indexOf(mech + ':' + id);
		if (index == -1) {
			if (value == '') {
				$('#mech_input').attr('value', '1' + mech + ':' + id);
			} else {
				$('#mech_input').attr('value', value + ', 1' + mech + ':' + id);
			}
		} else {
			var aqty = parseInt(value.substring(index - 1, index));
			$('#mech_input').attr('value', value.replace(aqty + mech + ':' + id, (aqty + 1) + mech + ':' + id));
		}
	}
	function ret(id, mech, qty) {
		if (qty > 1) {
			$('#' + id.replace(" ", "_") + 'ret' + mech + ' .qty').text(qty - 1);
			$('#' + id.replace(" ", "_") + 'ret' + mech + ' a').attr("onClick", "ret(\"" + id + "\", \"" + mech + "\", " + (qty - 1) + ")");
		} else {
			$('#' + id.replace(" ", "_") + 'ret' + mech).remove();
		}
		var nqty = $('#' + id.replace(" ", "_") + 'use' + mech + ' .qty').html();
		if (nqty == null) {
			$('#' + id.replace(" ", "_") + '-dropship').append("<div id='" + id.replace(" ", "_") + "use" + mech + 
				"'><a class='bttn' onclick='use(\"" +
				id + "\", \"" + mech + "\", " + 1 + 
				")' style='cursor: pointer;'>Use</a> <span class='qty'>" +
				"1</span> " + mech + "<br></div>");
		} else {
			nqty = parseInt(nqty);
			$('#' + id.replace(" ", "_") + 'use' + mech + ' .qty').text(nqty + 1);
			$('#' + id.replace(" ", "_") + 'use' + mech + ' a').attr("onClick", "use(\"" + id + "\", \"" + mech + "\", " + (nqty + 1) + ")");
		}
		var value = $('#mech_input').attr('value');
		var index = value.indexOf(mech + ':' + id);
		if (index != -1) {
			var aqty = parseInt(value.substring(index -1, index));
			if (aqty < 2) {
				if (value.indexOf(',') != -1) {
					$('#mech_input').attr('value', value.replace(', ' + aqty + mech + ':' + id, ''));
				} else {
					$('#mech_input').attr('value', '');
				}
			} else {
				$('#mech_input').attr('value', value.replace(aqty + mech + ':' + id, (aqty - 1) + mech + ':' + id));
			}
		}
	}
	</script>


	<?php echo $footer; ?>
  </div>
</body>
</html>