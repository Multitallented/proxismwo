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
		$_SESSION['flashMessage'] = "You must be logged in to go to the mech lab.";
		header('Location: index.php');
		die();
	}
	$approved = "0";
	$is_dead = "1";
	$conn = getConnection();
	$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	$cbills = 0;
	$unit_type = "";
	$dropship_id = 0;
	$pilots = 0;
	$capacity = 0;
	$canSwap = 0;
	$region = "";
	$production = array();
	$match_played = 0;
	$dropships = array();
    $mechCount = 0;
    $planet_owner = '';
    $dropship_name = '';
    $allied = false;
	if (isset($_GET['d'])) {
		$dropship_id = mysqli_real_escape_string($conn, $_GET['d']);
	}
	$planet_name = "";
	if (isset($_GET['p'])) {
		$planet_name = mysqli_real_escape_string($conn, $_GET['p']);
	}
	$mechPrices = getMechValues($conn);
	$mechlab_url = "";
	if ($dropship_id != 0) {
		$mechlab_url = "mechlab.php?d=" . $dropship_id;
	} else {
		$mechlab_url = "mechlab.php?p=" . $planet_name;
	}
	$sql = "SELECT cbills, approved, is_dead, unit_type, wins, loses FROM user WHERE username='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$cbills = $row[0];
		$approved = $row[1];
		$is_dead = $row[2];
		$unit_type = $row[3];
		if ($row[4] > 0 || $row[5] > 0) {
			$match_played = 1;
		}
	}
	mysqli_free_result($result);
	if ($is_dead || !$approved) {
		$_SESSION['flashMessage'] = "You must be approved/not dead to buy/sell";
		header('Location : profile.php?u=' . $username);
		die();
	}

	if ($dropship_id != 0) {
		$sql = "SELECT q.name, SUM(q.quantity) FROM (" .
			"SELECT d.dropship_id AS name, d.quantity FROM mech AS d WHERE d.dropship_id=" . $dropship_id .
			" UNION ALL SELECT dd.dropship_id AS name, dd.quantity FROM `match_mech` AS dd WHERE " .
			"dd.dropship_id=" . $dropship_id . ") AS q GROUP BY q.name;";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$mechCount = $row[1];
		}
		mysqli_free_result($result);
		$sql = "SELECT p.planet_name, p.owner_name, p.production, d.owner, d.capacity, a.sender, u.unit_type" . 
			", p.region, d.dropship_name FROM planet AS p INNER JOIN dropship AS d ON d.planet_name=p.planet_name " . 
			"LEFT OUTER JOIN alliance AS a ON a.sender=p.owner_name AND d.owner=a.ally " .
			"INNER JOIN user AS u ON u.username=p.owner_name " . 
			"WHERE d.dropship_id=" . $dropship_id . ";";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$planet_name = $row[0];
			if ($row[1] == $username || $row[6] == 'admin') {
				$canSwap = 1;
				$production = explode(", ", $row[2]);
			} elseif ($row[5] != NULL) {
				$production = explode(", ", $row[2]);
				$allied = true;
			}
			if ($row[3] != $username) {
				$_SESSION['flashMessage'] = "You are not the owner of that dropship";
				header('Location: profile.php?u=' . $username);
				die();
			}
			$capacity = $row[4];
			if ($capacity == NULL) {
				$capacity = 0;
			}
			$region = $row[7];
			$planet_owner = $row[1];

			if ($planet_owner == $username) {
				$dropships[] = array('id' => "", 'name' => "");
			}
			$dropship_name = $row[8];
			$sql = "SELECT d.dropship_id, d.dropship_name FROM dropship AS d WHERE d.planet_name='" . $planet_name . "' AND d.owner='" . $username . "';";
			$result = mysqli_query($conn, $sql);
			while ($row = $result->fetch_row()) {
				if ($row[0] != $dropship_id) {
					$dropships[] = array('id' => $row[0], 'name' => $row[1]);
				}
			}
			mysqli_free_result($result);
		}
		mysqli_free_result($result);
	} else {
		$sql = "SELECT q.name, SUM(q.quantity) FROM (SELECT m.planet_name AS name, m.quantity" .
			" FROM mech AS m WHERE m.planet_name='" . $planet_name . "' UNION ALL SELECT mm.planet_name" .
			" AS name, mm.quantity FROM `match_mech` AS mm WHERE mm.planet_name='" . $planet_name . "' " .
			") AS q GROUP BY q.name;";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$mechCount = $row[1];
		}
		mysqli_free_result($result);

		$sql = "SELECT p.owner_name, p.production, u.unit_type, p.region, p.capacity FROM planet AS p" . 
			" INNER JOIN user AS u ON u.username=p.owner_name WHERE p.planet_name='" . $planet_name . "';";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			if ($row[0] != $username && $row[2] != 'admin') {
				$_SESSION['flashMessage'] = "You are not the owner of that planet";
				header('Location: profile.php?u=' . $username);
				die();
			}
			$production = explode(", ", $row[1]);
			$capacity = $row[4];
			$region = $row[3];
		}
		mysqli_free_result($result);
		$sql = "SELECT d.dropship_id, d.dropship_name FROM dropship AS d WHERE d.planet_name='" . $planet_name . "' AND d.owner='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		while ($row = $result->fetch_row()) {
			$dropships[] = array('id' => $row[0], 'name' => $row[1]);
		}
		mysqli_free_result($result);
	}

	if (isset($_GET['sell']) && ($canSwap == 1 || $dropship_id == 0 || $allied)) {
		$sell_mech = strtoupper(mysqli_real_escape_string($conn, $_GET['sell']));
		$sell_value = $mechPrices[$sell_mech]['sell_price'];
		$sqlwhere = "";
		if ($dropship_id != 0) {
			$sql = "SELECT quantity FROM mech WHERE username='" . $username . 
				"' AND mech='" . $sell_mech . "' AND dropship_id=" . $dropship_id . ";";
			$sqlwhere = "dropship_id=" . $dropship_id;
		} else {
			$sql = "SELECT quantity FROM mech WHERE username='" . $username . 
				"' AND mech='" . $sell_mech . "' AND planet_name='" . $planet_name . "';";
			$sqlwhere = "planet_name='" . $planet_name . "'";
		}
		$result = mysqli_query($conn, $sql);
		$qty = 0;
		if ($row = $result->fetch_row()) {
			$qty = $row[0];
		}
		mysqli_free_result($result);
		if ($qty==0) {
			$_SESSION['flashMessage'] = "You do not have a " . $sell_mech . " to sell";
			header('Location: ' . $mechlab_url);
			die();
		} else {
			$sql = "UPDATE user SET cbills = '" . ($sell_value + $cbills) . "' WHERE username = '" . $username . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			
			if ($qty < 2) {
				$sql = "DELETE FROM mech WHERE mech = '" . $sell_mech . "' AND username = '" . $username . "' AND " . $sqlwhere . ";";
			} else {
				$sql = "UPDATE mech SET quantity = '" . ($qty - 1) . "' WHERE mech = '" . $sell_mech . "' AND username = '" . $username . "' AND " . $sqlwhere . ";";
			}
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);

			$file = 'playerlogs/' . $username . '.log';
			$outputmessage = date("Y-m-d H:i:s") . ' Sold ' . $sell_mech . '
';
			$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);

			$sql = "UPDATE market SET buy=(buy-1) WHERE mech='" . $sell_mech . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);

			$_SESSION['flashMessage'] = $sell_mech . " sold for " . number_format($sell_value) . " cbills";
			header('Location: ' . $mechlab_url);
			die();
		}
	}
	$moveto = "";
	if (isset($_GET['moveto'])) {
		$moveto = mysqli_real_escape_string($conn, $_GET['moveto']);
		if ($moveto != '') {
			$sql = "SELECT planet_name FROM dropship WHERE dropship_id=" . $moveto . ";";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				if (!($row[0] == $planet_name)) {
					$moveto = "";
				}
			}
			mysqli_free_result($result);
		}
	}
	if (isset($_GET['move'])) {
		$sell_mech = strtoupper(mysqli_real_escape_string($conn, $_GET['move']));
		$sql_where = "";
		$asql_where= "";
		if ($dropship_id != 0) {
			if ($moveto == "") {
				if ($planet_owner != $username) {
					$_SESSION['flashMessage'] = "You do not own this planet and can't move mechs to it";
					header('Location: ' . $mechlab_url);
					die();
				}
				$sql = "SELECT SUM(q.quantity), p.capacity FROM planet AS p LEFT OUTER JOIN (SELECT quantity FROM mech WHERE planet_name='" . $planet_name . "' AND username='" . $username .
					   "' UNION ALL SELECT quantity FROM `match_mech` WHERE planet_name='" . $planet_name . "' AND owner='" . $username . "') AS q ON 1=1 WHERE p.planet_name='" . $planet_name . "';";
				$result = mysqli_query($conn, $sql);
				if ($row = $result->fetch_row()) {
					if ($row[0] >= $row[1]) {
						$_SESSION['flashMessage'] = "Planet is at max capacity " . $row[0] . "/" . $row[1] . " Move cancelled";
						header('Location: ' . $mechlab_url);
						die();
					}
				}
				mysqli_free_result($result);
				$sql = "SELECT quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . 
					"' AND mech='" . $sell_mech . "' AND (dropship_id=" . $dropship_id . " OR planet_name='" . $planet_name . "');";
				$sql_where = "dropship_id=" . $dropship_id;
				$asql_where = "planet_name='" . $planet_name . "'";
			} else {
				$sql = "SELECT SUM(q.quantity), d.capacity FROM dropship AS d LEFT OUTER JOIN (SELECT quantity FROM mech WHERE dropship_id=" . $moveto . " AND username='" . $username .
				   "' UNION ALL SELECT quantity FROM `match_mech` WHERE dropship_id=" . $moveto . " AND owner='" . $username . "') AS q ON 1=1 WHERE d.dropship_id='" . $moveto . "';";
				$result = mysqli_query($conn, $sql);
				if ($row = $result->fetch_row()) {
					if ($row[0] >= $row[1]) {
						$_SESSION['flashMessage'] = "Dropship is at max capacity. Move cancelled";
						header('Location: ' . $mechlab_url);
						die();
					}
				}
				mysqli_free_result($result);
				$sql = "SELECT quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . 
					"' AND mech='" . $sell_mech . "' AND (dropship_id=" . $moveto . " OR dropship_id=" . $dropship_id . ");";
				$sql_where = "dropship_id=" . $dropship_id;
				$asql_where= "dropship_id=" . $moveto;
			}
		} else {
			if ($moveto == "") {
				$_SESSION['flashMessage'] = "No dropship found to move mechs to";
				header('Location: ' . $mechlab_url);
				die();
			}
			$sql = "SELECT SUM(q.quantity), d.capacity FROM dropship AS d LEFT OUTER JOIN (SELECT quantity FROM mech WHERE dropship_id=" . $moveto . " AND username='" . $username .
				   "' UNION ALL SELECT quantity FROM `match_mech` WHERE dropship_id=" . $moveto . " AND owner='" . $username . "') AS q ON 1=1 WHERE d.dropship_id='" . $moveto . "';";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				if ($row[0] >= $row[1]) {
					$_SESSION['flashMessage'] = "Dropship is at max capacity. Move cancelled";
					header('Location: ' . $mechlab_url);
					die();
				}
			}
			mysqli_free_result($result);
			$sql = "SELECT quantity, dropship_id, planet_name FROM mech WHERE username='" . $username . 
				"' AND mech='" . $sell_mech . "' AND (dropship_id=" . $moveto . " OR planet_name='" . $planet_name . "');";
			$sql_where = "planet_name='" . $planet_name . "'";
			$asql_where= "dropship_id=" . $moveto;
		}
		$result = mysqli_query($conn, $sql);
		$qty = 0;
		$cur_qty = 0;
		while ($row = $result->fetch_row()) {
			if ($row[1] != NULL) { //from dropship
				if ($dropship_id == 0 || $row[1] == $moveto) {
					$cur_qty = $row[0];
				} else {
					$qty = $row[0];
				}
			} else {
				if ($dropship_id == 0) {
					$qty = $row[0];
				} else {
					$cur_qty = $row[0];
				}
			}
		}
		mysqli_free_result($result);
		if ($qty==0) {
			$_SESSION['flashMessage'] = "You do not have a " . $sell_mech . " to move";
			header('Location: ' . $mechlab_url);
			die();
		} else {
			if ($qty < 2) {
				$sql = "DELETE FROM mech WHERE mech='" . $sell_mech . "' AND username = '" . $username . "' AND " . $sql_where . ";";
			} else {
				$sql = "UPDATE mech SET quantity=" . ($qty - 1) . " WHERE mech = '" . $sell_mech . "' AND username = '" . $username . "' AND " . $sql_where . ";";
			}
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			if ($cur_qty == 0) {
				if ($moveto != "") {
					$sql = "INSERT INTO mech VALUES ('" . $sell_mech . "', '" . $username . "', 1, " . $moveto . ", NULL);";


					$file = 'playerlogs/' . $username . '.log';
					$outputmessage = date("Y-m-d H:i:s") . ' Moved ' . $sell_mech . ' to dropship ' . $moveto . '
';
					$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);
				} else {
					$sql = "INSERT INTO mech VALUES ('" . $sell_mech . "', '" . $username . "', 1, NULL, '" . $planet_name . "');";


					$file = 'playerlogs/' . $username . '.log';
					$outputmessage = date("Y-m-d H:i:s") . ' Moved ' . $sell_mech . ' to planet ' . $planet_name . '
';
					$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);
				}
			} else {
				$sql = "UPDATE mech SET quantity=" . ($cur_qty + 1) . " WHERE mech = '" . $sell_mech . "' AND username = '" . $username . "' AND " . $asql_where . ";";
			}

			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$_SESSION['flashMessage'] = $sell_mech . " moved to dropship " . $moveto . " " . $planet_name;
			header('Location: ' . $mechlab_url);
			die();
		}
	}

	if (isset($_GET['buy'])) {
		if ($mechCount + 1 > $capacity) {
			$_SESSION['flashMessage'] = "Capacity limit reached. Failed to buy mech.";
			header('Location: ' . $mechlab_url);
			die();
		}
		if ($dropship_id != 0 && $planet_owner != $username && !$allied) {
			$_SESSION['flashMessage'] = "You do not own this planet and can't buy mechs on it";
			header('Location: ' . $mechlab_url);
			die();
		}

		$buy_mech = strtoupper(mysqli_real_escape_string($conn, $_GET['buy']));
		$buy_value = $mechPrices[$buy_mech]['buy_price'];
		if (($unit_type == 'pirate' && $mechPrices[$buy_mech]['buy_price'] > $mechPrices[$buy_mech]['base_price']) ||
		 		($unit_type == 'clan' && $region != 'clan')) {
			$buy_value = $buy_value * 2;
		}
		if (!in_array($buy_mech, $production) && !in_array('All', $production)) {
			$_SESSION['flashMessage'] = "That mech is not for sale at this planet";
			header('Location: ' . $mechlab_url);
			die();
		}

		if ($buy_value > $cbills) {
			$_SESSION['flashMessage'] = "You need " . number_format($buy_value - $cbills) . " more cbills to buy a " . $buy_mech;
			header('Location: ' . $mechlab_url);
			die();
		}		
		if ($dropship_id != 0) {
			$sql = "SELECT quantity FROM mech WHERE username='" . $username . 
				"' AND mech='" . $buy_mech . "' AND dropship_id=" . $dropship_id . ";";
		} else {
			$sql = "SELECT quantity FROM mech WHERE username='" . $username . 
				"' AND mech='" . $buy_mech . "' AND planet_name='" . $planet_name . "';";
		}
		$result = mysqli_query($conn, $sql);
		$qty = 0;
		if ($row = $result->fetch_row()) {
			$qty = $row[0];
		}
		mysqli_free_result($result);
		$sql = "UPDATE user SET cbills = '" . ($cbills - $buy_value) . "' WHERE  username =  '" . $username . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($qty==0) {
			if ($dropship_id != 0) {
				$sql = "INSERT INTO mech (mech, username, quantity, dropship_id) VALUES ('" . $buy_mech . "',  '" . $username . "',  '1', " . $dropship_id . ");";
			} else {
				$sql = "INSERT INTO mech (mech, username, quantity, planet_name) VALUES ('" . $buy_mech . "',  '" . $username . "',  '1', '" . $planet_name . "');";

			}
		} else {
			if ($dropship_id != 0) {
				$sql = "UPDATE mech SET quantity='" . ($qty + 1) . "' WHERE  mech='" . $buy_mech . "' AND username='" . $username . "' AND dropship_id=" . $dropship_id . ";";
			} else {
				$sql = "UPDATE mech SET quantity='" . ($qty + 1) . "' WHERE  mech='" . $buy_mech . "' AND username='" . $username . "' AND planet_name='" . $planet_name . "';";
			}
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		$file = 'playerlogs/' . $username . '.log';
		$outputmessage = date("Y-m-d H:i:s") . ' Bought ' . $buy_mech . '
';
		$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);

		$sql = "UPDATE market SET buy=(buy+1) WHERE mech='" . $buy_mech . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		$_SESSION['flashMessage'] = $buy_mech . " bought for " . number_format($buy_value) . " cbills";
		header('Location: ' . $mechlab_url);
		die();
	}

	if (isset($_GET['buydropship']) && ($dropship_id == 0 || $canSwap)) {
		$buycap = (int) mysqli_real_escape_string($conn, $_GET['buydropship']);
		if (!in_array("dropship" . $buycap, $production)) {
			$_SESSION['flashMessage'] = "That type of dropship isn't sold here";
			header('Location: ' . $mechlab_url);
			die();
		}
		$buy_value = 8000000 * $buycap;
		if ($unit_type=='faction') {
			$buy_value = $buy_value * 2;
		}
		if ($cbills < $buy_value) {
			$_SESSION['flashMessage'] = "You need " . number_format($buy_value - $cbills) . " more cbills to buy a dropship" . $buycap;
			header('Location: ' . $mechlab_url);
			die();
		}
		$sql = "UPDATE user SET cbills = '" . ($cbills - $buy_value) . "' WHERE  username =  '" . $username . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);

		$file = 'playerlogs/' . $username . '.log';
		$outputmessage = date("Y-m-d H:i:s") . ' Bought dropship for ' . $buy_value . '
';
		$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);


		$sql = "INSERT INTO dropship (owner, planet_name, capacity) VALUES ('" . $username . 
			"', '" . $planet_name . "', " . $buycap . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "You have purchased a new dropship";
		header('Location: ' . $mechlab_url);
		die();
	}

	if (isset($_GET['rename']) && $dropship_id != 0) {
		$rename = mysqli_real_escape_string($conn, $_GET['rename']);
		$sql = "UPDATE dropship SET dropship_name='" . $rename . "' WHERE dropship_id=" . $dropship_id . " AND owner='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Dropship renamed to " . $rename;
		header('Location: ' . $mechlab_url);
		die();
	}

	if ($match_played == 0) {
		$sql = "SELECT mech FROM `match_mech` WHERE owner='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			if ($row[0] > 0 || $row[1] > 0) {
				$match_played = 1;
			}
		}
		mysqli_free_result($result);
	}

	$result_html = "";
	if ($dropship_id != 0) {
		$sql = "SELECT mech, quantity FROM mech WHERE username='" . $username . "' AND dropship_id=" . $dropship_id . " ORDER BY mech;";
	} else {
		$sql = "SELECT mech, quantity FROM mech WHERE username='" . $username . "' AND planet_name='" . $planet_name . "' ORDER BY mech;";
	}
    	$result = mysqli_query($conn, $sql);
	while ($row = mysqli_fetch_row($result)) {
		if ($canSwap || $dropship_id == 0 || $allied) {
			$result_html .= "<button class='inline' value='" . $row[0] . "' name='sell'>Sell</button> | ";
				
		}
		$result_html .= "<button class='inline' value='" . $row[0] . "' name='move'>Move</button> " . 
						$row[1] . " " . $row[0] . " <span class='gold' style='line-height: 22px;'>" . 
						number_format($mechPrices[$row[0]]['sell_price']) . " CBILLS</span><br>";
	}
	mysqli_free_result($result);
	if ($result_html == "") {
		$result_html = "<span class='red'>You have no mechs to sell/cannot sell on a foreign planet.</span>";
	}
    	
	echo "<h3>Mechlab for ";
	if ($dropship_id != 0 && $dropship_name == "") {
		echo "Dropship " . $dropship_id . " on Planet " . strtoupper($planet_name);
	} elseif ($dropship_id != 0) {
		echo "Dropship " . $dropship_name . " on Planet " . strtoupper($planet_name);
	} else {
		echo "Planet " . strtoupper($planet_name);
	}
    echo " | Current Capacity " . $mechCount . "/" . $capacity . " Mechs </h3><br>";
  ?>
  <h4 class='gold inline left' style='padding-top: 5px;'>CBILLS: <?php echo number_format($cbills); ?></h4>

  <?php if ($dropship_id != 0) { ?>
    <br><br>
	<form method='get' action='mechlab.php'>
		<input type='hidden' class='hide' name='d' value='<?php echo $dropship_id; ?>' />
		Rename Dropship: <input type='textfield' name='rename' value='<?php echo $dropship_name; ?>' placeholder='<?php echo $dropship_id . " " . $planet_name;?>' />
		<input type='submit' name='arename' value='Rename Dropship' />
	</form>
  <?php } ?>

  <?php	mysqli_close($conn); ?>
<div class='clearfix'></div>

  <br>
<?php
  if ($match_played > 0) {
	foreach ($production as $prod) {
		if (strpos($prod, "dropship") > -1) {
			$drop_mult = 1;
			if ($unit_type == 'faction') {
				$drop_mult = 2;
			}
			echo "<a class='bttn' href='" . $mechlab_url . "&buydropship=" . str_replace('dropship', '', $prod) . 
				"'>Buy</a> Dropship (" . str_replace('dropship', '', $prod) . ") <span class='red'>" . (str_replace('dropship', '', $prod) * 8 * $drop_mult) .
				"M cbills</span><br> ";
		}
	}
  } else {
	echo "You can't buy dropships until after you've played 1 match.<br>";
  }
?>
  <br><br>
<div class='clearfix'></div><br>


  <div class='wdth-50 left'>
    <h3>Mechs on Sale</h3><br>
    <div style='padding-left: 15px;'>
	<?php foreach ($mechPrices as $mech => $mechData) {
		if ($mech == 'count' || $mech == 'variety') {
			continue;
		}
		$cur_value = $mechPrices[$mech]['buy_price'];
		if (($unit_type == 'pirate' && $mechPrices[$mech]['buy_price'] > $mechPrices[$mech]['base_price']) || ($unit_type == 'clan' && $region != 'clan')) {
			$cur_value = $cur_value * 2;
		}
		if (!in_array($mech, $production) && !in_array('All', $production)) {
			continue;
		}
		echo "<form style='display: inline;' action='mechlab.php' method='get'>";
		if ($dropship_id != 0) {
			echo "<input type='hidden' class='hide' name='d' value='" . $dropship_id . "' />";
		} else {
			echo "<input type='hidden' class='hide' name='p' value='" . $planet_name . "' />";
		}
	  	echo "<button style='display: inline;' value='" . $mech . "' name='buy' type='submit'>Buy</button></form> " .
			$mech . " <span class='gold'>" . number_format($cur_value) . 
			" CBILLS </span><br>";
	} ?>
	<br>
    </div>
  </div>
  <div class='wdth-50 left'>
    <h3>Owned Mechs</h3><br>
    <div style='padding-left: 15px;'>
	<form action='mechlab.php' method='get'>
	<?php 
		echo "Move mech to: <select name='moveto'>";
		foreach ($dropships as $dropship) {
			echo "<option value='" . $dropship['id'] . "'";
			if ($dropship['id'] == $moveto) {
				echo "selected";
			}
			if ($dropship['name'] == "") {
				echo ">" . $dropship['id'] . " " . $planet_name . "</option>";
			} else {
				echo ">" . $dropship['name'] . "</option>";
			}
		}
		echo "</select><br><br>";
	?>
	<?php if ($dropship_id != 0) {
		$result_html .= "<input type='hidden' class='hide' name='d' value='" . $dropship_id . "' />";
	} else {
		$result_html .= "<input type='hidden' class='hide' name='p' value='" . $planet_name . "' />";
	} ?>
	<?php echo $result_html; ?>
	</form>
    </div>
  </div>
  <div class='clearfix'></div>

	<?php echo $footer; ?>
  </div>
</body>
</html>