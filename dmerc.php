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
		$_SESSION['flashMessage'] = "No contract found.";
		header('Location: index.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to accept a contract";
		header('Location: index.php');
		die();
	}
    	$conn = getConnection();
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$unit_name = "";
	$unit_type = "";
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$uunit_name = "";
	$uunit_type = "";
	$cbills = 0;
    $mercenary = array();
	$mercenary['name'] = "";
    $planet = array();
	$planet['conditions'] = "";
	$planet['name'] = "";
	$planet['value'] = 0;
	$mechs = "";
    $attacker = array();
	$attacker['name'] = "";
    $defender = array();
	$defender['name'] = "";
	$declared = "";
	$accepted = 0;
	$retract = 0;
	$declined = 0;
    $dmercenary = array();
	$dmercenary['name'] = "";
    $dmercenary['qty'] = 0;
	$umechs = mysqli_real_escape_string($conn, $_GET['mechs']);
	$mechs = $umechs;
	$planet['x'] = 0;
	$planet['y'] = 0;
	if (isset($_GET['r'])) {
		if ($_GET['r'] == 'accept') {
			$accepted = 1;
		} elseif ($_GET['r'] == 'retract') {
			$retract = 1;
		} else {
			$declined = 1;
			$retract = 1;
		}
	}

	$sql = "SELECT match.attacker, match.defender, match.planet_name, match.responded," .
		"match.defender_mercenary, match.defender_mercenary_qty, match.defender_mercenary_rply, user.unit_type, user.cbills, " .
		"user.is_dead, user.approved, match.mercenary, p.match_conditions, p.cbill_value, p.location_x, p.location_y, match.resolved FROM `match` INNER JOIN user ON" .
		" match.defender_mercenary=user.username INNER JOIN planet AS p ON p.planet_name=match.planet_name WHERE match.match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$defender['name'] = $row[1];
		$attacker['name'] = $row[0];
		$planet['name'] = $row[2];
		$declared = $row[3];
		$dmercenary['name'] = $row[4];
		if ($username != $dmercenary['name'] && $retract == 0) {
			$_SESSION['flashMessage'] = "You can't accept/decline " . $defender['name'] . "'s contract with '" . $row[4] . "'";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$dmercenary['qty'] = $row[5];
		if ($row[6] == 1) {
			$_SESSION['flashMessage'] = "This contract has already been accepted";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$uunit_type = $row[7];
		$cbills = $row[8];
		if ($row[9] == 1) {
			$_SESSION['flashMessage'] = "A dead unit can't accept/decline contracts";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if ($row[10] == 0) {
			$_SESSION['flashMessage'] = "Your account must first be approved before you can accept/decline contracts";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$mercenary['name'] = $row[11];
		$planet['conditions'] = $row[12];
		$planet['value'] = $row[13];
		$planet['x']=$row[14];
		$planet['y']=$row[15];
		if ($row[16] != "") {
			$_SESSION['flashMessage'] = "This match was already resolved";
			header('Location: profile.php?u=' . $username);
			die();
		}
	}
	mysqli_free_result($result);
	if ($retract == 1) {
		if ($defender['name'] != $username && $declined == 0) {
			$_SESSION['flashMessage'] = "You are not the contract offerer";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if ($declined == 1 && $dmercenary['name'] != $username) {
			$_SESSION['flashMessage'] = "You are not the contract recipient";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$attack_timestamp = "";
		$sql = "SELECT defender_mercenary_rply, responded, declared FROM `match` WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		$last_timestamp = "";
		if ($row = $result->fetch_row()) {
			if ($row[0] > 0) {
				$_SESSION['flashMessage'] = "You can't retract an offer that has already been accepted";
				header('Location: profile.php?u=' . $username);
				die();
			}
			$last_timestamp = $row[1];
			$attack_timestamp = $row[2];
		}
		mysqli_free_result($result);
		if ($declined == 1) {
			$sql = "UPDATE notifications SET value=-1 WHERE created='" . $declared . "' AND (notification_type='defend contract' OR notification_type='defend hire');";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		} else {
			$sql = "DELETE FROM notifications WHERE (notification_type='defend hire' OR notification_type='defend contract') AND value=" . $match_id . ";";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		$sql = "UPDATE notifications SET value=" . $match_id . " WHERE created='" . $attack_timestamp . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		$sql = "SELECT mm.mech, mm.quantity, mm.dropship_id, mm.planet_name," .
			" m.quantity FROM `match_mech` AS mm LEFT OUTER JOIN mech AS m ON mm.mech=m.mech " .
			"AND ((mm.dropship_id=m.dropship_id AND NOT(ISNULL(mm.dropship_id))) OR " . 
			"(mm.planet_name=m.planet_name AND NOT(ISNULL(mm.planet_name)))) LEFT OUTER JOIN " . 
			"planet AS p ON p.planet_name=mm.planet_name LEFT OUTER JOIN dropship AS d ON " . 
			"d.dropship_id=mm.dropship_id WHERE mm.owner='" . $defender['name'] . "';";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$mech = $row[0];
			$mqty = $row[1];
			$did = $row[2];
			$pid = $row[3];
			$oqty = $row[4];
			if ($pid != "") {
				
				if ($oqty == 0) {
					$sql = "INSERT INTO mech VALUES ('" . $mech . "', '" . $defender['name'] . "', " . $mqty . ", NULL, '" . $pid . "')";
					$result1 = mysqli_query($conn, $sql);
					mysqli_free_result($result1);
				} else {
					$sql = "UPDATE mech SET quantity=" . ($mqty + $oqty) . " WHERE mech='" . $mech . "' AND planet_name='" . $pid . "';";
					$result1 = mysqli_query($conn, $sql);
					mysqli_free_result($result1);
				}
				$sql = "DELETE FROM `match_mech` WHERE match_id=" . $match_id . " AND owner='" . $defender['name'] . "' AND mech='" .
						$mech . "' AND planet_name='" . $pid . "';";
				$result1 = mysqli_query($conn, $sql);
				mysqli_free_result($result1);

			} elseif ($did != "") {

				if ($oqty == 0) {
					$sql = "INSERT INTO mech VALUES ('" . $mech . "', '" . $defender['name'] . "', " . $mqty . ", " . $did . ", NULL);";
					$result1 = mysqli_query($conn, $sql);
					mysqli_free_result($result1);
				} else {
					$sql = "UPDATE mech SET quantity=" . ($mqty + $oqty) . " WHERE mech='" . $mech . "' AND dropship_id='" . $did . "';";
					$result1 = mysqli_query($conn, $sql);
					mysqli_free_result($result1);
				}
				$sql = "DELETE FROM `match_mech` WHERE match_id=" . $match_id . " AND owner='" . $defender['name'] .
						"' AND mech='" . $mech . "' AND dropship_id=" . $did . ";";
				$result1 = mysqli_query($conn, $sql);
				mysqli_free_result($result1);
			}
		}
		mysqli_free_result($result);

		$sql = "UPDATE `match` SET defender_mercenary=NULL, defender_mercenary_qty=0, responded=NULL WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($declined == 1) {
			$_SESSION['flashMessage'] = "Contract offer declined";
			header('Location: profile.php?u=' . $username);
			die();
		} else {
			$_SESSION['flashMessage'] = "Contract offer retracted";
			header('Location: profile.php?u=' . $username);
			die();
		}
	}

	if ($uunit_type != 'merc') {
		$_SESSION['flashMessage'] = "Only mercenaries can receive contracts";
		header('Location: profile.php?u=' . $username);
		die();
	}

	$dropships = array();
	$nodrop = array();
	$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name, p.location_x, p.location_y" .
		", d.capacity, d.location_x, d.location_y, d.planet_name, p.capacity, p.invuln FROM mech AS m LEFT OUTER JOIN" .
		" planet AS p ON p.planet_name=m.planet_name LEFT OUTER JOIN (SELECT dr.dropship_id," .
		" dr.capacity, pl.location_x, pl.location_y, pl.planet_name FROM dropship AS dr INNER JOIN planet AS pl ON " . 
		"dr.planet_name=pl.planet_name AND pl.invuln=0) AS d ON d.dropship_id=m.dropship_id WHERE m.username='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != NULL) {
			if (in_array($row[2], $nodrop) || $row[11] == "1") {
				continue;
			}
			$x1 = $row[7];
			$y1 = $row[8];
			if (pow(abs($x1 -$planet['x']), 2) + pow(abs($y1 - $planet['y']), 2) > 1000000) {
				$nodrop[] = $row[2];
				continue;
			}
			if (isset($dropships[$row[2]])) {
				$dropships[$row[2]][$row[0]] = $row[1];
				$dropships[$row[2]]['mechs'] += $row[1];
			} else {
				$dropships[$row[2]] = array($row[0] => $row[1],
											'capacity' => $row[6],
											'planet' => $row[9],
											'mechs' => $row[1]
				);
			}
		} elseif ($row[3] != NULL) {
			if ($row[3] != $planet['name']) {
				$nodrop[] = $row[3];
			}
			if (in_array($row[3], $nodrop)) {
				continue;
			}
			$x1 = $row[4];
			$y1 = $row[5];
			if (isset($dropships[$row[3]])) {
				$dropships[$row[3]][$row[0]] = $row[1];
				$dropships[$row[3]]['mechs'] += $row[1];
			} else {
				$dropships[$row[3]] = array($row[0] => $row[1], 'capacity' => $row[10], 'mechs' => $row[1]);
			}
		}
		
	}

	mysqli_free_result($result);
	if ($umechs != "") {
		$mechs = explode(", ",$umechs);
		$mech_count = 0;
		foreach ($mechs as $mech) {
			$mech_qty = (int) explode(":", $mech)[0];
			$mech_var = explode(":", $mech)[1];
			$drop_id = explode(":", $mech)[2];
			if (!array_key_exists($drop_id, $dropships)) {
				$_SESSION['flashMessage'] = "That dropship is not available for attack";
				header('Location: dmerc.php?m=' . $match_id);
				die();
			}
			if (!array_key_exists($mech_var, $dropships[$drop_id])) {
				$_SESSION['flashMessage'] = "No " . $mech_var . " available in dropship " . $drop_id . " " . $planet['name'];
				header('Location: dmerc.php?m=' . $match_id);
				die();
			}
			if ($dropships[$drop_id][$mech_var] < $mech_qty) {
				$_SESSION['flashMessage'] = "You don't have " . $mech_qty . " " . $mech_var . " in dropship " . $drop_id . " " . $planet['name'];
				header('Location: dmerc.php?m=' . $match_id);
				die();
			}
			$mech_count += $mech_qty;
		}
		if ($mech_count != ($dmercenary['qty'])) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $dmercenary['qty']) . " mechs to use, not " . $mech_count;
			header('Location: dmerc.php?m=' . $match_id);
			die();
		}
	}

	$sql = "SELECT mech, quantity FROM `match_mech` WHERE owner='" . $defender['name'] . "' AND match_id='" . $match_id . "';";
	$result = mysqli_query($conn, $sql);
	$mechs_brough = "";
	while ($row = $result->fetch_row()) {
		$mechs_brough .= $row[1] . $row[0] . ", ";
	}
	$mechs_brough = substr($mechs_brough, 0, strlen($mechs_brough) - 2);
	mysqli_free_result($result);

	if ($accepted == 1) {
		if ($umechs == "") {
			$_SESSION['flashMessage'] = "You must choose " . $dmercenary['qty'] . " mechs to use before accepting";
			header('Location: dmerc.php?m=' . $match_id);
			die();
		}

		$file = 'playerlogs/' . $username . '.log';
		$outputmessage = date("Y-m-d H:i:s") . ' ' . $username . ' accepted defense contract with ' . $defender['name'] . ' on ' . $planet_name . ' using ' . $umechs . '
';
		$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);

		$sql = "SELECT mech, quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$mech_qty = $row[1];
			$mech_var = $row[0];
			$umechs = explode(", ", $umechs);
			foreach ($umechs as $mech) {
				$mmech_qty = (int) explode(":", $mech)[0];
				$mmech_var = explode(":", $mech)[1];
				$dropship_var = explode(":", $mech)[2];
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

				$sql = "INSERT INTO `match_mech` VALUES ('" . $mech_var . "', " . $match_id . ", '" . $username . "', " . $mmech_qty . ", ";
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

		$sql = "UPDATE notifications SET value=0 WHERE created='" . $declared . "' AND (notification_type='defend contract' OR notification_type='defend hire');";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$contract_time = date("Y-m-d H:i:s");
		
		$sql = "UPDATE `match` SET defender_mercenary_rply=1, defender_mercenary_time='" . $contract_time . "' WHERE match_id='" . $match_id . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		$sql = "SELECT attacker FROM cw.match WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		$attacker['name'] = "";
		if ($row = $result->fetch_row()) {
			$attacker['name'] = $row[0];
		}
		mysqli_free_result($result);

		$sql = "INSERT INTO notifications VALUES ('match hire', '" . $username . "', '" . $contract_time . "', '" . $attacker['name'] . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if (!($mercenary['name'] == "")) {
			$sql = "INSERT INTO notifications VALUES ('match hire', '" . $mercenary['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		$sql = "INSERT INTO notifications VALUES ('defend', '" . $defender['name'] . "', '" . $contract_time . "', '" . $attacker['name'] . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('defend', '" . $attacker['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		
		$_SESSION['flashMessage'] = "Attack declared successfully";
		header('Location: match.php?m=' . $match_id);
		die();
	}
  ?>
  <h2>Contract</h2><br>
	<div class='wdth-33 left'>
	<h4>Dropships Available</h4><br>

	<?php
		echo $jquery;
		foreach (array_keys($dropships) as $drop_id) {
		if ($drop_id == $planet['name']) {
			echo "<span class='green'>" . $planet['name'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechs'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		} else {
			echo "<span class='green'>" . $drop_id . " " . $dropships[$drop_id]['planet'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechs'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		}
		echo "<div id='" . str_replace(" ", "_", $drop_id) . "-dropship' style='margin-left: 10px;'>";
		foreach (array_keys($dropships[$drop_id]) as $mech) {
			if ($mech == 'capacity' || $mech == 'mechs' || $mech == 'planet') {
				continue;
			}
			echo "<div id='" . str_replace(" ", "_", $drop_id) . "use" . $mech . "'><a onclick='use(\"" . $drop_id . "\", \"" . $mech . "\", " . $dropships[$drop_id][$mech] . 
				")' style='cursor: pointer;' class='bttn'>Use</a> <span class='qty'>" . $dropships[$drop_id][$mech] .
				"</span> " . $mech . "<br></div>";
		}
		echo "</div><br>";
		
	}?>
	</div>

	<div class='left wdth-66'>
	<?php 
	
	echo "<a href='profile.php?u=" . $defender['name'] . "'>" . $defender['name'] . "</a> is offering a contract to you to bring <span class='red'>" . $dmercenary['qty'] . " mechs</span> to defend against <a href='profile.php?u=" .
			$attacker['name'] . "'>" . $attacker['name'] . "</a><br><br>";
	echo "You are about to defend <a href='profile.php?u=" . $defender['name'] . "'>" .  $defender['name'] . "</a> on";
	echo " planet <span class='green'>" . $planet['name'] . "</span><br>";
	echo "<br>Match conditions are: <span class='grey'>" . $planet['conditions'] . "</span><br><br>"; 
	
	if ($dmercenary['qty'] < 12) {
		echo "<a href='profile.php?u=" . $defender['name'] . "'>" . $defender['name'] . "</a> is bringing: ";
		$mechs_brought = "";
		foreach (explode(", ", $mechs_brough) as $mech) {
			$mech_qty = substr($mech, 0, 1);
			$mech_var = substr($mech, 1);
			$mechs_brought .= $mech_qty . " " . $mech_var . ", ";
		}
		$mechs_brought = substr($mechs_brought, 0, strlen($mechs_brought) - 2);
		echo "<span class='grey'>" . $mechs_brought . "</span><br>";
	}

?>
	<br><br>
	<h4>Mechs Ready</h4><br>
	<div id='ready-mechs'>
	</div>
	<br><br>
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
			if ('<?php echo $planet['name']; ?>' == id) {
				$('#ready-mechs').append("<div id='" + id.replace(" ", "_") + "ret" + mech + 
					"'><a class='bttn' onclick='ret(\"" +
					id + "\", \"" + mech + "\", " + 1 + 
					")' style='cursor: pointer;'>Return</a> <span class='qty'>" +
					"1</span> " + mech + " <span class='green'><?php echo $planet['name']; ?></span><br></div>");
			} else {
				$('#ready-mechs').append("<div id='" + id.replace(" ", "_") + "ret" + mech + 
					"'><a class='bttn' onclick='ret(\"" +
					id + "\", \"" + mech + "\", " + 1 + 
					")' style='cursor: pointer;'>Return</a> <span class='qty'>" +
					"1</span> " + mech + " <span class='green'>" + id + "<?php echo $planet['name']; ?></span><br></div>");
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
				$('#mech_input').attr('value', '01' + ':' + mech + ':' + id);
			} else {
				$('#mech_input').attr('value', value + ', 01' + ':' + mech + ':' + id);
			}
		} else {
			var aqty = parseInt(value.substring(index - 3, index - 1));
			var nqty = aqty;
			if (aqty < 9) {
				nqty = "0" + (aqty + 1);
			} else {
				nqty = aqty + 1;
			}
			console.log(value.substring(index - 3, index - 1) + ":" + aqty + ":" + nqty);
			$('#mech_input').attr('value', value.replace(value.substring(index - 3, index - 1) + ":" + mech + ':' + id, nqty + ":" + mech + ':' + id));
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
			var aqty = parseInt(value.substring(index - 3, index - 1));
			if (aqty < 2) {
				if (value.indexOf(',') != -1) {
					$('#mech_input').attr('value', value.replace(', ' + value.substring(index - 3, index - 1) + ':' + mech + ':' + id, ''));
				} else {
					$('#mech_input').attr('value', '');
				}
			} else {
				var nqty = "01";
				if (aqty > 10) {
					nqty = aqty - 1;
				} else {
					nqty = "0" + (aqty - 1);
				}
				$('#mech_input').attr('value', value.replace(value.substring(index - 3, index - 1) + ":" + mech + ':' + id, nqty + ':' + mech + ':' + id));
			}
		}
	}
	</script>


	<form class='inline' action='dmerc.php' method='get'>


	<input class='hide' type='hidden' name='m' value='<?php echo $match_id; ?>' />
	<input id='mech_input' type='hidden' class='hide' name='mechs' />
	Is this okay? <button class='inline' name='r' value='accept'>Accept</button>
			
			</form> | <a class='bttn' href='dmerc.php?m=<?php echo $match_id; ?>&r=decline'>Decline</a><br><br>

	</div><div class='clearfix'></div>
  	
	<?php echo $footer; ?>
  </div>
</body>
</html>