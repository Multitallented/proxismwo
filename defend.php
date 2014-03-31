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
		$_SESSION['flashMessage'] = "No match found.";
		header('Location: index.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to defend";
		header('Location: index.php');
		die();
	}
  	$conn = getConnection();
	$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	$defender = array('name' => strtolower(mysqli_real_escape_string($conn, $_SESSION['username'])));
	$defender['uname'] = "";
	$defender['utype'] = "";
	$mercenary = array();
	$mercenary['name'] = "";
	$attacker['name'] = "";
	$defender['cbills'] = 0;
	$mercenary = array();
	$mercenary['name'] = "";
	$mercenary['qty'] = 0;
	$planet = array();
	$planet['name'] = "";
	$planet['conditions'] = "";
	$planet['value']=0;
	$mechs = "";
	$declared = "";
	$accepted = 0;
	$dmercenary = array();
	$dmercenary['qty'] = 0;
	$dmercenary['name'] = "";
	$dmercenary['utype'] = "";
	$mercenary['time'] = "";
	$umechs = "";
	$attacker['email'] = "";
	$dmercenary['email'] = "";
	if (isset($_GET['hire']) && isset($_GET['qty']) && $_GET['hire'] != "") {
		$dmercenary['qty'] = mysqli_real_escape_string($conn, $_GET['qty']);
		$dmercenary['name'] = strtolower(mysqli_real_escape_string($conn, $_GET['hire']));
	}
	if (isset($_GET['mechs'])) {
		$umechs = mysqli_real_escape_string($conn, $_GET['mechs']);
		$mechs = $umechs;
	}
	if (isset($_GET['r']) && $_GET['r'] == 'accept') {
		$accepted = 1;
	}

	$sql = "SELECT match.attacker, match.defender, match.planet_name, match.declared," .
		"match.mercenary, match.mercenary_qty, match.mercenary_rply, u.unit_type, u.cbills, " .
		"u.is_dead, u.approved, match.mercenary_time, p.match_conditions, p.cbill_value, " . 
		"ua.email, match.responded, p.location_x, p.location_y FROM `match` INNER JOIN user AS u ON" .
		" match.defender=u.username INNER JOIN user AS ua ON ua.username=match.attacker INNER" .
		" JOIN planet AS p ON p.planet_name=match.planet_name WHERE match.match_id='" .
		$match_id . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker['name'] = $row[0];
		if ($dmercenary['name'] != "" && ($dmercenary['name'] == $attacker['name'] || $dmercenary['name'] == $mercenary['name'] || $dmercenary['name'] == $defender['name'])) {
			$_SESSION['flashMessage'] = "You can't hire " . $dmercenary['name'] . " to that";
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
		$mercenary['name'] = $row[4];
		$planet['name'] = $row[2];
		$declared = $row[3];
		if ($row[1] != $defender['name']) {
			$_SESSION['flashMessage'] = "You can't fight/forfiet " . $row[1] . "'s match with " . $row[0];
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
		$mercenary['qty'] = $row[5];
		if ($row[6] == 0 && $mercenary['name'] != "") {
			$_SESSION['flashMessage'] = "You can't defend this attack until " . $row[4] . " accepts the contract";
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
		$uunit_type = $row[7];
		$defender['cbills'] = $row[8];
		if ($row[9] == 1) {
			$_SESSION['flashMessage'] = "A dead unit can't fight matches";
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
		if ($row[10] == 0) {
			$_SESSION['flashMessage'] = "Your account must first be approved before you can fight matches";
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
		$mercenary['time'] = $row[11];
		$planet['conditions'] = $row[12];
		$planet['value'] = $row[13];
		$planet['x'] = $row[16];
		$planet['y'] = $row[17];
		$attacker['email'] = $row[14];
		if ($row[15] != "") {
			$_SESSION['flashMessage'] = "This attack has already been responded to";
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
	}
	mysqli_free_result($result);

	if ($accepted == 0 && isset($_GET['r'])) {
		$responded_time = date("Y-m-d H:i:s");
		$sql = "UPDATE `match` SET responded='" . $responded_time . "', reported='" . $responded_time . "', report_response='" .
			$responded_time . "', resolved='" . $responded_time . "', winner='" .
			$attacker['name'] . "' WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		resolveMatch($conn, $match_id, true);

		if ($mercenary['time'] == "") {
			$sql = "UPDATE notifications SET value=0 WHERE notification_type='attack' AND created='" . $declared . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$sql = "UPDATE notifications SET value=1 WHERE notification_type='attack declared' AND created='" . $declared . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		} else {
			$sql = "UPDATE notifications SET value=0 WHERE notification_type='attack' AND created='" . $mercenary['time'] . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$sql = "UPDATE notifications SET value=1 WHERE notification_type='attack declared' AND created='" . $mercenary['time'] . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		
		$_SESSION['flashMessage'] = "You have forfieted your match against " . $attacker['name'];
		header('Location: score.php?m=' . $match_id);
		die();
	}

	if ($dmercenary['name'] != "") {
		$sql = "SELECT unit_type, email FROM user WHERE username='" . $dmercenary['name'] . "';";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$dmercenary['utype'] = $row[0];
			$dmercenary['email'] = $row[1];
		}
		//Check if the mercenary is the right unit type to be hired
		if ($defender['utype'] == "clan") {
			if ($dmercenary['utype'] != "clan") {
				$_SESSION['flashMessage'] = "You can't hire " . $dmercenary['name'] . " because they are not a clan";
				header('Location: profile.php?u=' . $dmercenary['name']);
				die();
			}
		} else {
			if ($dmercenary['utype'] != "merc") {
				$_SESSION['flashMessage'] = "You can't hire " . $dmercenary['name'] . " because they are not a mercenary";
				header('Location: profile.php?u=' . $dmercenary['name']);
				die();
			}
		}
		mysqli_free_result($result);
	}

	$dropships = array();
	$nodrop = array();
	$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name, p.location_x, p.location_y" .
		", d.capacity, d.location_x, d.location_y, d.planet_name, p.capacity FROM mech AS m LEFT OUTER JOIN" . 
		" planet AS p ON p.planet_name=m.planet_name LEFT OUTER JOIN (SELECT dr.dropship_id," . 
		" dr.capacity, pl.location_x, pl.location_y, pl.planet_name FROM dropship AS dr INNER JOIN planet AS pl ON " . 
		"dr.planet_name=pl.planet_name) AS d ON d.dropship_id=m.dropship_id WHERE m.username='" . $defender['name'] . "'";
	if ($aunit_type == 'pirate') {
		$sql .= ";";
	} else {
		$sql .= " AND (m.planet_name='" . $planet['name'] . "' OR d.planet_name='" . $planet['name'] . "');";
	}
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != "") {
			if ($aunit_type == 'pirate') {
				if (in_array($row[2], $nodrop)) {
					continue;
				}
				$x1 = $row[7];
				$y1 = $row[8];
				if (pow(abs($x1 - $planet['x']), 2) + pow(abs($y1 - $planet['y']), 2) > 640000) {
					$nodrop[] = $row[2];
					continue;
				}
			}
			if (isset($dropships[$row[2]])) {
				$dropships[$row[2]][$row[0]] = $row[1];
				$dropships[$row[2]]['mechCount'] += $row[1];
			} else {
				$dropships[$row[2]] = array($row[0] => $row[1], 'capacity' => $row[6], 'planet' => $row[9], 'mechCount' => $row[1]);
			}
		} elseif ($row[3] != "") {
			if ($aunit_type == 'pirate' && $row[3] != $planet['name']) {
				$nodrop[] = $row[3];
				continue;
			}
			if (isset($dropships[$row[3]])) {
				$dropships[$row[3]][$row[0]] = $row[1];
				$dropships[$row[3]]['mechCount'] += $row[1];
			} else {
				$dropships[$row[3]] = array($row[0] => $row[1], 'capacity' => $row[10], 'mechCount' => $row[1]);
			}
		}
	}
	mysqli_free_result($result);

	if ($umechs != "" && $dmercenary['qty'] < 12) {
		$mechs = explode(", ",$umechs);
		$mech_count = 0;
		foreach ($mechs as $mech) {
			$mech_qty = (int) explode(":", $mech)[0];
			$mech_var = explode(":", $mech)[1];
			$drop_id = explode(":", $mech)[2];

			if (!array_key_exists($drop_id, $dropships)) {
				$_SESSION['flashMessage'] = "That dropship is not available for defense";
				header('Location: defend.php?m=' . $match_id);
				die();
			}
			if (!array_key_exists($mech_var, $dropships[$drop_id])) {
				$_SESSION['flashMessage'] = "No " . $mech_var . " available in dropship " . $drop_id . " " . $planet['name'];
				header('Location: defend.php?m=' . $match_id);
				die();
			}
			if ($dropships[$drop_id][$mech_var] < $mech_qty) {
				$_SESSION['flashMessage'] = "You don't have " . $mech_qty . " " . $mech_var . " in dropship " . $drop_id . " " . $planet['name'];
				header('Location: defend.php?m=' . $match_id);
				die();
			}
			$mech_count = $mech_count + $mech_qty;
		}
		if ($mech_count != (12 - $dmercenary['qty'])) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $dmercenary['qty']) . " mechs to use, not " . $mech_count;
			header('Location: defend.php?m=' . $match_id);
			die();
		}
	}

	if ($accepted == 1) {
		if ($umechs == "" && $dmercenary['qty'] < 12) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $dmercenary['qty']) . " mechs to use before accepting";
			header('Location: defend.php?m=' . $match_id);
			die();
		}
		if ($umechs != "") {
			$sql = "SELECT mech, quantity, dropship_id, planet_name FROM mech WHERE username='" . $defender['name'] . "';";
			$result = mysqli_query($conn, $sql);

			$file = 'playerlogs/' . $username . '.log';
			$outputmessage = date("Y-m-d H:i:s") . ' ' . $defender['name'] . ' defended against ' . $attacker['name'] . ' on ' . $planet['name'] . ' using ' . $mechs . '
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
						$sql = "UPDATE mech SET quantity=" . ($mech_qty - $mmech_qty) . " WHERE username='" . $defender['name'] .
							"' AND mech='" . $mech_var . "' AND " . $sql_where;
					} else {
						$sql = "DELETE FROM mech WHERE username='" . $defender['name'] . "' AND mech='" . $mech_var . "' AND " . $sql_where;
					}
					$result1 = mysqli_query($conn, $sql);
					mysqli_free_result($result1);

					$sql = "INSERT INTO `match_mech` VALUES ('" . $mech_var . "', " . $match_id . ", '" . $defender['name'] . "', " . $mmech_qty . ", ";
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
		}

		$note_time = $mercenary['time'];
		if ($mercenary['name'] == "") {
			$note_time = $declared;
		}
		$sql = "UPDATE notifications SET value=0 WHERE created='" . $note_time . "' AND notification_type='attack';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "UPDATE notifications SET value=1 WHERE created='" . $note_time . "' AND notification_type='attack declared';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$contract_time = date("Y-m-d H:i:s");
		
		if ($dmercenary['name'] == "") {
			$sql = "UPDATE `match` SET responded='" . $contract_time . "' WHERE match_id='" . $match_id . "';";
		} else {
			$sql = "UPDATE `match` SET responded='" . $contract_time . "', defender_mercenary='" . $dmercenary['name'] .
				"', defender_mercenary_qty=" . $dmercenary['qty'] . " WHERE match_id='" . $match_id . "';";
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		
		if ($dmercenary['name'] == "") {
			$sql = "INSERT INTO notifications VALUES ('defend', '" . $attacker['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			
			$sql = "INSERT INTO notifications VALUES ('defend', '" . $defender['name'] . "', '" . $contract_time . "', '" . $attacker['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			if ($mercenary['name'] != "") {
				$sql = "INSERT INTO notifications VALUES ('match hire', '" . $mercenary['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', " . $match_id . ");";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
			}
			if ($attacker['email'] != "") {
				$sub = $defender['name'] . " is defending " . $planet['name'];
				$bod = $defender['name'] . " is defending " . $planet['name'] . ". 
You have until " . strtotime('+7 days') . " CST to submit a screenshot of the match. 
If you do not report the match winner in 7 days, you will tie the match and lose 
2 pilots and mechs (for each side).  
You can report the winner of this match via your profile page.";
				sendMail($attacker['email'], $sub, $bod);
			}
			$_SESSION['flashMessage'] = "Attack response sent";
			header('Location: match.php?m=' . $match_id);
			die();
		} else {
			$sql = "INSERT INTO notifications VALUES ('defend contract', '" . $dmercenary['name'] . "', '" . $contract_time . "', '" . $defender['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			
			$sql = "INSERT INTO notifications VALUES ('defend hire', '" . $defender['name'] . "', '" . $contract_time . "', '" . $dmercenary['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$_SESSION['flashMessage'] = "Contract sent to " . $dmercenary['name'];
			header('Location: profile.php?u=' . $defender['name']);
			die();
		}
	}
	echo $jquery;
  ?>
<h2>Defend!</h2><br>
	<div class='wdth-33 left'>
	<h4>Dropships Available</h4><br>

	<?php foreach (array_keys($dropships) as $drop_id) {
		if ($drop_id == $planet['name']) {
			echo "<span class='green'>" . $planet['name'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechCount'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		} else {
			echo "<span class='green'>" . $drop_id . " " . $dropships[$drop_id]['planet'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechCount'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		}
		echo "<div id='" . str_replace(" ", "_", $drop_id) . "-dropship' style='margin-left: 10px;'>";
		foreach (array_keys($dropships[$drop_id]) as $mech) {
			if ($mech == 'capacity' || $mech == 'mechCount' || $mech == 'planet') {
				continue;
			}
			echo "<div id='" . str_replace(" ", "_", $drop_id) . "use" . $mech . "'><a onclick='use(\"" . $drop_id . "\", \"" . $mech . "\", " . $dropships[$drop_id][$mech] . 
				")' style='cursor: pointer;' class='bttn'>Use</a> <span class='qty'>" . $dropships[$drop_id][$mech] .
				"</span> " . $mech . "<br></div>";
		}
		echo "</div><br>";
		
	}?>
	</div>

	<div class='left wdth-66'>You are being attacked by <a href='profile.php?u=<?php echo $attacker['name'] . "'>" .
		 $attacker['name'] . "</a>"; ?> on
	<?php 
	echo " planet <span class='green'>" . $planet['name'] . "</span> worth <span class='green'>" .
		number_format($planet['value']) . "</span> cbills.<br>";
	echo "<br>Match conditions are:<span class='red'> " . $planet['conditions'] . "</span><br>"; 
	if ($mercenary['qty'] > 0) {
		echo $attacker['name'] . " has hired " . $mercenary['qty'] . " pilots from <a href='profile.php?u=" . $mercenary['name'] . "'>" . $mercenary['name'] . "</a><br>";
	} ?>
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

	<form class='inline' action='defend.php' method='get'>
	Hire <input style='width: 20px;' type='textfield' name='qty' value='<?php echo $aqty; ?>' />
	pilots from <input type='textfield' name='hire' value='<?php echo $hire; ?>' />

	<br><br>
	<input class='hide' type='hidden' name='m' value='<?php echo $match_id; ?>' />
	<input id='mech_input' type='hidden' class='hide' name='mechs' />
	<input type='hidden' class='hide' name='r' value='accept' />
	Is this okay? <input class='inline' type='submit' name='attack' value='Defend!' />
			
			</form> | <a class='bttn' href='defend.php?m=<?php echo $match_id; ?>&r=decline'>Forfiet</a><br><br>
	</div>
	
	<?php echo $footer; ?>
  </div>
</body>
</html>