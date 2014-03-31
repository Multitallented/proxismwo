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
	if (!isset($_GET['i'])) {
		$_SESSION['flashMessage'] = "No planet by that name.";
		header('Location: map.php');
		die();
	}
	if (!isset($_GET['u'])) {
		$_SESSION['flashMessage'] = "No user by that name.";
		header('Location: map.php');
		die();
	}
	if (!isset($_SESSION['username'])) {
		$_SESSION['flashMessage'] = "You must be logged in to attack";
		header('Location: /mwo/');
		die();
	}
    $conn = getConnection();

    $attacker = array('name' => strtolower(mysqli_real_escape_string($conn, $_SESSION['username'])),
	                  'cbills' => 0,
                      'mechCount' => 0,
                      'utype' => "",
	                  'uname' => "",
    );
    $defender = array('name' => mysqli_real_escape_string($conn, $_GET['u']),
	                  'utype' => "",
	                  'uname' => "",
	                  'email' => "",
    );
    $mercenary = array('name' => "",
	                   'email' => "",
	                   'qty' => 0,
	                   'utype' => ""
    );
    $planet = array('name' => mysqli_real_escape_string($conn, $_GET['i']),
                    'x' => 0,
	                'y' => 0,
                    'owner' => 0,
                    'value' => 0,
	                'conditions' => "",
    );

    //Check if attacking yourself
	if ($attacker['name'] == $defender['name']) {
		$_SESSION['flashMessage'] = "You can't attack yourself";
		header('Location: map.php');
		die();
	}

    //Get Mercenary and qty
	if (isset($_GET['qty']) && isset($_GET['hire']) && $_GET['hire'] != "") {
		$mercenary['qty'] = mysqli_real_escape_string($conn, $_GET['qty']);
		if ($mercenary['qty'] < 0 || $mercenary['qty'] > 12) {
			$_SESSION['flashMessage'] = "You can't hire that many mercenaries for a single match";
			header('Location: attack.php?u=' . $defender['name'] . '&i=' . $planet['name']);
			die();
		}
		$mercenary['name'] = strtolower(mysqli_real_escape_string($conn, $_GET['hire']));
	}

    //Check if hiring yourself
	if ($mercenary['name'] == $attacker['name']) {
		$_SESSION['flashMessage'] = "You can't hire yourself";
		header('Location: attack.php?u=' . $defender['name'] . '&i=' . $planet['name']);
		die();
	}
    
	$sql = "SELECT a.unit_name, a.unit_type, a.cbills, d.unit_name, d.unit_type," .
		" d.username, p.cbill_value, p.match_conditions, p.invuln," .
		" a.approved, a.is_dead, p.location_x, p.location_y, us.dropship_id, d.email FROM planet AS p" .
		" INNER JOIN user AS d ON d.username=p.owner_name INNER JOIN user AS a ON a.username='" . $attacker['name'] .
		"' LEFT OUTER JOIN (SELECT d1.dropship_id, d1.planet_name FROM dropship AS d1 WHERE d1.owner='" .
		$defender['name'] . "' AND d1.planet_name='" . $planet['name'] . "' LIMIT 1) AS us ON us.planet_name=p.planet_name WHERE p.planet_name='" . $planet['name'] . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		
		$attacker['uname'] = $row[0];
		$attacker['utype'] = $row[1];
		$attacker['cbills'] = $row[2];
		$defender['uname'] = $row[3];
		$defender['utype'] = $row[4];
		
		if ($row[5] != $defender['name'] && $row[13] == NULL) {
			$_SESSION['flashMessage'] = $defender['name'] . " is not on planet " . $planet['name'];
			header('Location: map.php');
			die();
		}
		if ($row[5] == $defender['name']) {
			$planet['owner'] = 1;
		}
		$planet['value'] = $row[6];
		$planet['conditions'] = $row[7];
		if ($row[8] > 0) {
			$_SESSION['flashMessage'] = "This planet is a safe zone. Players can't be attacked here";
			header('Location: map.php');
			die();
		}
		/*if ($row[8] > 0 && $row[14] == $defender['name']) {
			$_SESSION['flashMessage'] = "This person can't be attacked on this planet";
			header('Location: map.php');
			die();
		}*/
		if ($row[9] == 0 || $row[10] == 1) {
			$_SESSION['flashMessage'] = "Your account is waiting for approval/has been closed";
			header('Location: map.php');
			die();
		}
		$planet['x'] = $row[11];
		$planet['y'] = $row[12];
		$defender['email'] = $row[14];
	} else {
		$_SESSION['flashMessage'] = "No planet found";
		header('Location: map.php');
		die();
	}
	mysqli_free_result($result);	

	if ($attacker['utype'] == 'clan' && $mercenary['qty'] > 0) {
		$_SESSION['flashMessage'] = "Clans can't hire mercenaries";
		header('Location: attack.php?i=' . $planet['name'] . '&u=' . $defender['name']);
		die();
	}

      $requiredMechCount = 12;
      $inPrevMatch = 0;
	  $sql = "SELECT match_id, responded, defender_mercenary_qty, defender_mercenary_time FROM `match` WHERE (attacker='" .
		  $defender['name'] . "' OR defender='" . $defender['name'] . "' OR mercenary='" . $defender['name'] .
		  "' OR defender_mercenary='" . $defender['name'] . "') AND planet_name='" .
		  $planet['name'] . "' AND ISNULL(resolved);";
	  $result1 = mysqli_query($conn, $sql);
	  while ($row1 = $result1->fetch_row()) {
		  if (!($row1[1] && (!$row[2] || $row[3]))) {
			  $requiredMechCount += 12;
		  }
		  $inPrevMatch = $row[0];
	  }
	  mysqli_free_result($result1);

    if ($inPrevMatch) {
		if ($planet['owner'] == 1) {
			$sql = "SELECT m.planet_name, SUM(m.quantity) FROM mech AS m WHERE m.username='" .
				$defender['name'] . "' AND m.planet_name='" . $planet['name'] . "' GROUP BY m.planet_name";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				$requiredMechCount -= $row[1];
			}
			mysqli_free_result($result);
		}

	    if ($requiredMechCount > 0) {

		    $sql = "SELECT m.dropship_id, SUM(m.quantity) FROM mech AS m INNER JOIN dropship AS d ON " .
			    "d.dropship_id=m.dropship_id WHERE d.owner='" . $defender['name'] . "' AND d.planet_name='" .
			    $planet['name'] . "' GROUP BY m.dropship_id";
		    $result = mysqli_query($conn, $sql);
		    while($row = $result->fetch_row()) {
			    $requiredMechCount -= $row[1];
		    }
	    }

	    if ($requiredMechCount > 0) {
		    $_SESSION['flashMessage'] = "Unable to attack. Finish your match " . $inPrevMatch;
		    header('Location: map.php');
		    die();
	    }
    }

	$dropships = array();
	$nodrop = array();
	$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name," .
		" d.capacity, d.location_x, d.location_y, d.planet_name, p.capacity, d.dropship_name FROM mech AS m LEFT OUTER JOIN" .
		" planet AS p ON p.planet_name=m.planet_name LEFT OUTER JOIN (SELECT dr.dropship_id, " .
		" dr.capacity, pl.location_x, pl.location_y, pl.planet_name, dr.dropship_name FROM dropship AS dr INNER JOIN planet AS pl ON " . 
		"dr.planet_name=pl.planet_name) AS d ON d.dropship_id=m.dropship_id WHERE m.username='" . $attacker['name'] . "'";
	if ($attacker['utype'] == 'pirate') {
		$sql .= ";";
	} else {
		$sql .= " AND (m.planet_name='" . $planet['name'] . "' OR d.planet_name='" . $planet['name'] . "');";
	}
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$mech = array('name' => $row[0],
					  'qty' => $row[1],
					  'dropship' => array('id' => $row[2],
										 'capacity' => $row[4],
										 'x' => $row[5],
										 'y' => $row[6],
						                 'planet' => $row[7],
						                 'name' => $row[9],
					                     ),
					  'planet' => array('name' => $row[3],
						                'capacity' => $row[8]
					                    )
					  );

		if ($mech['dropship']['id'] != "") {
			if ($attacker['utype'] == 'pirate') {
				if (in_array($mech['dropship']['id'], $nodrop)) {
					continue;
				}
				$x1 = $mech['dropship']['x'];
				$y1 = $mech['dropship']['y'];
				if (pow(abs($x1 - $planet['x']), 2) + pow(abs($y1 - $planet['y']), 2) > 640000) {
					$nodrop[] = $mech['dropship']['id'];
					continue;
				}
			}
			if (isset($dropships[$mech['dropship']['id']])) {
				$dropships[$mech['dropship']['id']][$mech['name']] = $mech['qty'];
				$dropships[$mech['dropship']['id']]['mechs'] += $mech['qty'];
			} else {
				$dropships[$mech['dropship']['id']] = array($mech['name'] => $mech['qty'],
															'capacity' => $mech['dropship']['capacity'],
															'planet' => $mech['dropship']['planet'],
															'mechs' => $mech['qty'],
															'name' => $mech['dropship']['name'],
				);
			}
		} elseif ($row[3] != "") {
			if ($attacker['utype'] == 'pirate' && $mech['planet']['name'] != $planet['name']) {
				$nodrop[] = $mech['planet']['name'];
				continue;
			}
			if (isset($dropships[$mech['planet']['name']])) {
				$dropships[$mech['planet']['name']][$mech['name']] = $mech['qty'];
				$dropships[$mech['planet']['name']]['mechs'] += $mech['qty'];
			} else {
				$dropships[$mech['planet']['name']] = array($mech['name'] => $mech['qty'],
															'capacity' => $mech['planet']['capacity']);
				$dropships[$mech['planet']['name']]['mechs'] = $mech['qty'];
			}
		}
		
	}
	mysqli_free_result($result);

	if (isset($_GET['mechs']) && $mercenary['qty'] < 12) {
		$temp_mechs = mysqli_real_escape_string($conn, $_GET['mechs']);
		
		$mechs = explode(", ",$temp_mechs);
		$mech_count = 0;
		foreach ($mechs as $mech) {
			$mech_qty = (int) explode(":", $mech)[0];
			$mech_count += $mech_qty;
			$mech_var = explode(":", $mech)[1];
			$drop_id = explode(":", $mech)[2];

			if (!array_key_exists($drop_id, $dropships)) {
				$_SESSION['flashMessage'] = "That dropship is not available for attack";
				header('Location: attack.php?i=' . $planet['name'] . '&u=' . $defender['name']);
				die();
			}
			if (!array_key_exists($mech_var, $dropships[$drop_id])) {
				$_SESSION['flashMessage'] = "No " . $mech_var . " available in dropship " . $drop_id . " " . $planet['name'];
				header('Location: attack.php?i=' . $planet['name'] . '&u=' . $defender['name']);
				die();
			}
			if ($dropships[$drop_id][$mech_var] < $mech_qty) {
				$_SESSION['flashMessage'] = "You don't have " . $mech_qty . " " . $mech_var . " in dropship " . $drop_id . " " . $planet['name'];
				header('Location: attack.php?i=' . $planet['name'] . '&u=' . $defender['name']);
				die();
			}
		}
		if (!($mech_count == (12 - $mercenary['qty']))) {
			$_SESSION['flashMessage'] = "You must choose " . (12 - $mercenary['qty']) . " mechs to use, not " . $mech_count;
			header('Location: attack.php?i=' . $planet['name'] . '&u=' . $defender['name']);
			die();
		}
	}

	if (!($mercenary['name'] == "" || $mercenary['qty'] == 0)) {
		$sql = "SELECT username, email, unit_type FROM user WHERE username LIKE '%" . $mercenary['name'] . "%' AND approved='1' AND is_dead='0';";
		$result = mysqli_query($conn, $sql);
		$result_length = mysqli_num_rows($result);
		if ($result_length < 1) {
			mysqli_free_result($result);
			$sql = "SELECT username, email, unit_type FROM user WHERE unit_name LIKE '%" . $mercenary['name'] . "%' AND is_dead='0' AND approved='1';";
			$result = mysqli_query($conn, $sql);
		}
		if ($row = $result->fetch_row()) {
			$mercenary['name'] = $row[0];
			$memail = $row[1];
			$mercenary['utype'] = $row[2];
		} else {
			$_SESSION['flashMessage'] = "There is no mercenary unit by the name of " . $mercenary['name'];
			header('Location: profile.php?u=' . $attacker['name']);
			die();
		}
		mysqli_free_result($result);
	}

    //Check if the mercenary is the right unit type to be hired
  if ($mercenary['name'] != "" && $mercenary['qty'] > 0) {
	    if ($attacker['utype'] == "clan") {
		    if ($mercenary['utype'] != "clan") {
			    $_SESSION['flashMessage'] = "You can't hire " . $mercenary['name'] . " because they are not a clan";
			    header('Location: profile.php?u=' . $mercenary['name']);
			    die();
		    }
	    } else {
		    if ($mercenary['utype'] != "merc") {
			    $_SESSION['flashMessage'] = "You can't hire " . $mercenary['name'] . " because they are not a mercenary";
			    header('Location: profile.php?u=' . $mercenary['name']);
			    die();
		    }
	    }
  }

	if (isset($_GET['attack'])) {
		$timestamp = date("Y-m-d H:i:s");
		$sql = "INSERT INTO `match` (attacker, defender, planet_name, declared, last_action, mercenary, mercenary_qty) VALUES ('" .
			$attacker['name'] . "', '" . $defender['name'] . "', '" . $planet['name'] . "', '" . $timestamp . "', '" . $timestamp . "', ";
		if ($mercenary['qty'] < 1) {
			$sql .= "NULL, 0);";
		} else {
			$sql .= "'" . $mercenary['name'] . "', " . $mercenary['qty'] . ");";
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "SELECT match_id FROM `match` WHERE attacker='" . $attacker['name'] . "' AND declared='" . $timestamp . "';";
		$result = mysqli_query($conn, $sql);
		$match_id = 0;
		if ($row = $result->fetch_row()) {
			$match_id = $row[0];
		}
		mysqli_free_result($result);

		$sql = "SELECT mech, quantity, dropship_id, planet_name FROM mech WHERE username='" . $attacker['name'] . "';";
		$result = mysqli_query($conn, $sql);
		
		$file = 'playerlogs/' . $username . '.log';
		$outputmessage = date("Y-m-d H:i:s") . ' ' . $attacker['name'] . ' attacked ' . $defender['name'] . ' on ' . $planet_name . ' using ' . $mechs . '
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
					$sql = "UPDATE mech SET quantity=" . ($mech_qty - $mmech_qty) . " WHERE username='" . $attacker['name'] .
						"' AND mech='" . $mech_var . "' AND " . $sql_where;
				} else {
					$sql = "DELETE FROM mech WHERE username='" . $attacker['name'] . "' AND mech='" . $mech_var . "' AND " . $sql_where;
				}
				$result1 = mysqli_query($conn, $sql);
				mysqli_free_result($result1);

				$sql = "INSERT INTO `match_mech` VALUES ('" . $mech_var . "', " . $match_id . ", '" . $attacker['name'] . "', " . $mmech_qty . ", ";
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
		
		if ($mercenary['qty'] > 0) {
			$sql = "INSERT INTO notifications VALUES ('contract', '" . $mercenary['name'] . "', '" . $timestamp . "', '" . $attacker['name'] . "', '" . $match_id . "');";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$sql = "INSERT INTO notifications VALUES ('hire', '" . $attacker['name'] . "', '" . $timestamp . "', '" . $mercenary['name'] . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			if ($memail != "") {
				$sub = $attacker['name'] . " is hiring you to attack " . $defender['name'] . " on " . $planet['name'];
				$bod = $attacker['name'] . " is hiring you to attack " . $defender['name'] . " on " . $planet['name'] . ". 
You can respond to this contract via your profile page.";
				sendMail($memail, $sub, $bod);
			}
			$_SESSION['flashMessage'] = "Contract offer given to " . $mercenary['name'] . ".";
			header('Location: profile.php?u=' . $attacker['name']);
			die();
		}
		$sql = "INSERT INTO notifications VALUES ('attack', '" . $defender['name'] . "', '" . $timestamp . "', '" . $attacker['name'] . "', '" . $match_id . "');";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('attack declared', '" . $attacker['name'] . "', '" . $timestamp . "', '" . $defender['name'] . "', 0);";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($demail != "") {
			$sub = $attacker['name'] . " is attacking " . $planet['name'];
			$bod = $attacker['name'] . " is attacking " . $planet['name'] . ". 
You have until " . strtotime('+4 days') . " to respond to this attack. 
If you do not respond in 4 days, you will forfiet the match and lose 3-5 mechs.
You can respond to this attack via your profile page.";
			sendMail($demail, $sub, $bod);
		}
		$_SESSION['flashMessage'] = "Attack declared successfully. " . $defender['name'] . " has 4 days to respond.";
		header('Location: profile.php?u=' . $attacker['name']);
		die();
	}

  echo $jquery;
  ?>
  <h2>Attack!</h2><br>
	<div class='wdth-33 left'>
	<h4>Dropships Available</h4><br>

	<?php foreach (array_keys($dropships) as $drop_id) {
		if ($drop_id == $planet['name']) {
			echo "<span class='green'>" . $planet['name'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechs'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		} elseif ($dropships[$drop_id]['name'] == "") {
			echo "<span class='green'>" . $drop_id . " " . $dropships[$drop_id]['planet'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechs'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		} else {
			echo "<span class='green'>" . $dropships[$drop_id]['name'] . " " . $dropships[$drop_id]['planet'] . "</span><span class='grey'> Mechs: " .
				$dropships[$drop_id]['mechs'] . "/" . $dropships[$drop_id]['capacity'] . "</span><br><br>";
		}
		echo "<div id='" . str_replace(" ", "_", $drop_id) . "-dropship' style='margin-left: 10px;'>";
		foreach (array_keys($dropships[$drop_id]) as $mech) {
			if ($mech == 'capacity' || $mech == 'mechs' || $mech == 'planet' || $mech == 'name') {
				continue;
			}
			echo "<div id='" . str_replace(" ", "_", $drop_id) . "use" . $mech . "'><a onclick='use(\"" . $drop_id . "\", \"" . $mech . "\", " . $dropships[$drop_id][$mech] . 
				")' style='cursor: pointer;' class='bttn'>Use</a> <span class='qty'>" . $dropships[$drop_id][$mech] .
				"</span> " . $mech . "<br></div>";
		}
		echo "</div><br>";
		
	}?>
	</div>

	<div class='left wdth-66'>You are about to attack <a href='profile.php?u=<?php echo $defender['name'] . "'>" .  $defender['name'] . "</a>'s"; ?>
	<?php 
	echo " planet <span class='green'>" . $planet['name'] . "</span> worth <span class='green'>" .
		number_format($planet['value']) . "</span> cbills.<br>";
	echo "<br>Match conditions are: " . $planet['conditions'] . "<br>"; 
	if ($mercenary['qty'] > 0) {
		echo "You are going to hire " . $mercenary['qty'] . " pilots from <a href='profile.php?u=" . $mercenary['name'] . "'>" . $mercenary['name'] . "</a><br>";
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

	<form class='inline' action='attack.php' method='get'>
	Hire <input style='width: 20px;' type='textfield' name='qty' value='<?php echo $mercenary['qty']; ?>' />
	pilots from <input type='textfield' name='hire' value='<?php echo $mercenary['name']; ?>' />

	<br><br>
	<input class='hide' type='hidden' name='i' value='<?php echo $planet['name']; ?>' />
	<input class='hide' type='hidden' name='u' value='<?php echo $defender['name']; ?>' />
	<input id='mech_input' type='hidden' class='hide' name='mechs' />
	Is this okay? <input class='inline' type='submit' name='attack' value='Attack!' />
			
			</form> | <a class='bttn' href='profile.php?u=<?php echo $attacker['name']; ?>'>Back Down</a><br><br>

	<?php mysqli_close($conn); ?>

	</div>
	<div class='clearfix'></div>
	<?php echo $footer; ?>
  </div>
</body>
</html>