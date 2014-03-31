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
	$username = "";
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
    $mercenary = array();
	$mercenary['name'] = "";
	$mercenary['qty'] = 0;
    $mercenary['utype'] = "";
    $mercenary['cbills'] = 0;
    $planet = array();
	$planet['conditions'] = "";
    $attacker = array();
	$attacker['name'] = "";
    $defender = array();
	$defender['name'] = "";
	$declared = "";
	$accepted = 0;
	$retract = 0;
	$declined = 0;
	$umechs = mysqli_real_escape_string($conn, $_GET['mechs']);
	$mechs = $umechs;
	$planet['x'] = 0;
	$planet['y'] = 0;
	$defender['email'] = "";
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

	$sql = "SELECT match.attacker, match.defender, match.planet_name, match.declared," .
		"match.mercenary, match.mercenary_qty, match.mercenary_rply, u.unit_type, u.cbills, " .
		"u.is_dead, u.approved, p.match_conditions, p.location_x, p.location_y, ud.email, p.owner_name FROM `match` INNER JOIN `user` AS u ON" .
		" match.mercenary=u.username INNER JOIN user AS ud ON match.defender=ud.username " .
		"INNER JOIN planet AS p ON p.planet_name=match.planet_name WHERE match.match_id=" .
		$match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker['name'] = $row[0];
		$defender['name'] = $row[1];
		$planet['name'] = $row[2];
		if ($row[15] == "Unowned") {
			return_mechs($match_id, $row[0], $conn);
			$sql = "DELETE FROM notifications WHERE value=" . $match_id . ";";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
			$sql = "DELETE FROM `match` WHERE match_id=" . $match_id . ";";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
			$_SESSION['flashMessage'] = "Contract revoked. Planet was unclaimed";
			header('Location: map.php');
			die();
		} elseif ($row[15] != $row[1]) {
			$sql = "UPDATE `match` SET defender='" . $row[1] . "' WHERE match_id=" . $match_id . ";";
			$result1 = mysqli_query($conn, $sql);
			mysqli_free_result($result1);
		}
		$declared = $row[3];
		$mercenary['name']=$row[4];
		if (!($username == $row[4]) && $retract == 0) {
			$_SESSION['flashMessage'] = "You can't accept/decline " . $attacker['name'] . "'s contract with " . $row[4];
			header('Location: profile.php?u=' . $username);
			die();
		}
		$mercenary['qty'] = $row[5];
		if ($row[6] == 1 && $retract == 0) {
			$_SESSION['flashMessage'] = "This contract has already been accepted";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$mercenary['utype'] = $row[7];
		$mercenary['cbills'] = $row[8];
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
		$planet['conditions'] = $row[11];
		$planet['x'] = $row[12];
		$planet['y'] = $row[13];
		$defender['email'] = $row[14];
	}
	mysqli_free_result($result);

	if ($retract == 1) {
		if ($username != $attacker['name'] && $declined == 0) {
			$_SESSION['flashMessage'] = "You are not the contract offerer";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if ($username != $mercenary['name'] && $declined == 1) {
			$_SESSION['flashMessage'] = "You are not the contract recipient";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$sql = "SELECT defender_mercenary_rply, responded, declared FROM cw.match WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		$attack_timestamp = "";
		$last_timestamp = "";
		if ($row = $result->fetch_row()) {
			if ($row[0] > 0 ) {
				$_SESSION['flashMessage'] = "You can't retract an offer that has already been accepted";
				header('Location: profile.php?u=' . $username);
				die();
			}
			$last_timestamp = $row[1];
			$attack_timestamp = $row[2];
		}
		mysqli_free_result($result);

		$sql = "SELECT attacker, defender, mercenary, defender_mercenary, mercenary_qty, defender_mercenary_qty FROM `match` WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			if ($row[0] != "" && $row[4] < 12) {
				return_mechs($match_id, $row[0], $conn);
			}
			if ($row[1] != "" && $row[5] < 12) {
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

		if ($declined == 1) {
			$sql = "UPDATE notifications SET value=-1 WHERE created='" . $attack_timestamp . "' AND (notification_type='contract' OR notification_type='hire');";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		} else {
			$sql = "DELETE FROM notifications WHERE (notification_type='hire' OR notification_type='contract') AND value=" . $match_id . ";";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		$_SESSION['flashMessage'] = "Contract offer retracted";
		header('Location: profile.php?u=' . $username);
		die();
	}

	$dropships = array();
	$nodrop = array();
	$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name, p.location_x, p.location_y," .
		" d.capacity, d.location_x, d.location_y, d.planet_name, p.capacity FROM mech AS m LEFT OUTER JOIN" .
		" planet AS p ON p.planet_name=m.planet_name LEFT OUTER JOIN (SELECT dr.dropship_id," .
		" dr.capacity, pl.location_x, pl.location_y, pl.planet_name FROM dropship AS dr INNER JOIN planet AS pl ON " . 
		"dr.planet_name=pl.planet_name AND pl.invuln=0) AS d ON d.dropship_id=m.dropship_id WHERE m.username='" . $mercenary['name'] . "';";

	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != NULL) {
			if (in_array($row[2], $nodrop)) {
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
											'mechs' => $row[1],
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
				header('Location: merc.php?m=' . $match_id);
				die();
			}
			if (!array_key_exists($mech_var, $dropships[$drop_id])) {
				$_SESSION['flashMessage'] = "No " . $mech_var . " available in dropship " . $drop_id . " " . $planet['name'];
				header('Location: merc.php?m=' . $match_id);
				die();
			}
			if ($dropships[$drop_id][$mech_var] < $mech_qty) {
				$_SESSION['flashMessage'] = "You don't have " . $mech_qty . " " . $mech_var . " in dropship " . $drop_id . " " . $planet['name'];
				header('Location: merc.php?m=' . $match_id);
				die();
			}
			$mech_count += $mech_qty;
		}
		if ($mech_count != ($mercenary['qty'])) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $mercenary['qty']) . " mechs to use, not " . $mech_count;
			header('Location: merc.php?m=' . $match_id);
			die();
		}
	}

	if ($accepted == 1) {
		if ($umechs == "") {
			$_SESSION['flashMessage'] = "You must choose " . $mercenary['qty'] . " mechs to use before accepting";
			header('Location: merc.php?m=' . $match_id);
			die();
		}
		$mechs = explode(", ",$umechs);
		$sql = "SELECT mech, quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . "';";
		$result = mysqli_query($conn, $sql);

		$file = 'playerlogs/' . $username . '.log';
		$outputmessage = date("Y-m-d H:i:s") . ' ' . $username . ' accepted attack contract with ' . $attacker['name'] . ' on ' . $planet['name'] . ' using ' . $umechs . '
';
		$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);

		while ($row = $result->fetch_row()) {
			$mech_qty = $row[1];
			$mech_var = $row[0];
			foreach ($mechs as $mech) {
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
		
		$sql = "UPDATE notifications SET value=0 WHERE created='" . $declared . "' AND notification_type='contract';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "UPDATE notifications SET value=0 WHERE created='" . $declared . "' AND notification_type='hire';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$contract_time = date("Y-m-d H:i:s");
		$sql = "UPDATE `match` SET mercenary_rply=1, mercenary_time='" . $contract_time . "' WHERE match_id='" . $match_id . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('attack', '" . $defender['name'] . "', '" . $contract_time . "', '" . $attacker['name'] . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('attack declared', '" . $attacker['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', 0);";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('attack declared', '" . $username . "', '" . $contract_time . "', '" . $defender['name'] . "', 0);";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($defender['email'] != "") {
			$sub = $attacker['name'] . " is attacking " . $planet['name'];
			$bod = $attacker['name'] . " is attacking " . $planet['name'] . ". 
You have until " . strtotime('+4 days') . " CST to respond to this attack. 
If you do not respond in 4 days, you will forfiet the match and lose 5-7 mechs.
You can respond to this attack via your profile page.";
			sendMail($defender['email'], $sub, $bod);
		}
		$_SESSION['flashMessage'] = "Attack declared successfully";
		header('Location: profile.php?u=' . $username);
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
	
	echo "<a href='profile.php?u=" . $attacker['name'] . "'>" . $attacker['name'] . "</a> is offering a contract to you to bring <span class='red'>" . $mercenary['qty'] . " mechs</span> to attack <a href='profile.php?u=" .
			$defender['name'] . "'>" . $defender['name'] . "</a><br><br>";
	echo "You are about to attack <a href='profile.php?u=" . $defender['name'] . "'>" .  $defender['name'] . "</a> on";
	echo " planet <span class='green'>" . $planet['name'] . "</span><br>";
	echo "<br>Match conditions are: <span class='grey'>" . $planet['conditions'] . "</span><br><br>"; 
	
	if ($mercenary['qty'] < 12) {
		echo "<a href='profile.php?u=" . $attacker['name'] . "'>" . $attacker['name'] . "</a> is bringing: ";
		$sql = "SELECT mech, quantity FROM cw.match_mech WHERE owner='" . $attacker['name'] . "' AND match_id=" . $match_id . ";";
		$result=mysqli_query($conn, $sql);
		$allied_mechs = "";
		while ($row = $result->fetch_row()) {
			if ($allied_mechs == "") {
				$allied_mechs .= $row[1] . $row[0];
			} else {
				$allied_mechs .= ", " . $row[1] . $row[0];
			}
		}
		echo "<span class='grey'>" . $allied_mechs . "</span><br>";
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


	<form class='inline' action='merc.php' method='get'>


	<input class='hide' type='hidden' name='m' value='<?php echo $match_id; ?>' />
	<input id='mech_input' type='hidden' class='hide' name='mechs' />
	Is this okay? <button class='inline' name='r' value='accept'>Accept</button>
			
			</form> | <a class='bttn' href='merc.php?m=<?php echo $match_id; ?>&r=decline'>Decline</a><br><br>

	</div><div class='clearfix'></div>
	
	<?php echo $footer; ?>
  </div>
</body>
</html>