<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/reset.css" />
  <?php session_start(); 
	$flashMessage = "";
	if (isset($_SESSION['flashMessage'])) {
		$flashMessage = "<p id='flash-message'>" . $_SESSION['flashMessage'] . "</p>";
		unset($_SESSION['flashMessage']);
	}
?>
  <?php include 'header.php'; ?>
</head>
<body style='width: 4100px; height: 4800px;'>
<div style='position: relative;'>

<?php
echo $background;
$conn = getConnection();
$username = "";
$unit_type = "";
$planet_id = "";
$dropship_id = 0;
if (isset($_SESSION['username'])) {
	$username= strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
}
if (isset($_GET['p'])) {
	$planet_id = mysqli_real_escape_string($conn, $_GET['p']);
}
if (isset($_GET['d'])) {
	$dropship_id = mysqli_real_escape_string($conn, $_GET['d']);
}
$sql = "SELECT unit_type FROM user WHERE username='" . $username . "';";
$result = mysqli_query($conn, $sql);
if ($row = $result->fetch_row()) {
	$unit_type = $row[0];
}
if (isset($_GET['unclaim'])) {
	$cbill_value = 0;
	$sql = "SELECT p.owner_name, p.cbill_value, m.match_id FROM planet AS p LEFT OUTER JOIN `match` AS m " .
		"ON m.planet_name=p.planet_name AND ISNULL(m.resolved) AND (ISNULL(m.mercenary) OR NOT(ISNULL(m.mercenary_time))) WHERE p.planet_name='" . $planet_id . "';";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		if ($username != $row[0]) {
			$_SESSION['flashMessage'] = "You can't unclaim a planet you own.";
			header('Location: map.php');
			die();
		}
		if ($row[2] != "") {
			$_SESSION['flashMessage'] = "You can't unclaim this planet until you finish match " . $row[2];
			header('Location: map.php');
			die();
		}
		$cbill_value = $row[1];
	}
	mysqli_free_result($result);
	$cbill_value = $cbill_value * 0.5;
	$sql = "UPDATE planet SET owner_name='Unowned', cbill_value=" . $cbill_value . " WHERE planet_name='" . $planet_id . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$sql = "UPDATE user SET cbills=(cbills+" . $cbill_value . ") WHERE username='" . $username . "';";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	$_SESSION['flashMessage'] = $planet_id . " was unclaimed successfully. You gained " . number_format($cbill_value) . "cbills";
	header('Location: map.php');
	die();
}
mysqli_free_result($result);
if (!($planet_id == "" || $dropship_id == "" || $username == "") && isset($_GET['movedrop'])) {
	$planet_name = "";
	$sql = "SELECT DISTINCT d.last_move, p1.location_x, p1.location_y, p2.location_x, p2.location_y, p1.planet_name," . 
		" m.match_id, m1.match_id FROM dropship AS d INNER JOIN planet AS p1 ON d.planet_name=p1.planet_name INNER JOIN" . 
		" planet AS p2 ON p2.planet_name='" . $planet_id . "' LEFT OUTER JOIN (SELECT ma.match_id, ma.planet_name" . 
		" FROM cw.match AS ma INNER JOIN cw.match_mech AS mm ON mm.match_id=ma.match_id AND mm.dropship_id=" . $dropship_id .
		" WHERE ISNULL(ma.resolved) AND (ISNULL(ma.mercenary) OR NOT(ISNULL(ma.mercenary_time)))) AS m ON" . 
		" m.planet_name=p1.planet_name LEFT OUTER JOIN `match` AS m1 ON m1.defender='" . $username . 
		"' AND m1.planet_name=d.planet_name AND ISNULL(m1.responded) AND (ISNULL(m1.mercenary) OR NOT(ISNULL(m1.mercenary_time))) WHERE d.dropship_id=" . $dropship_id . " LIMIT 1;";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		if ($row[5] == $planet_id) {
			header('Location: map.php');
			die();
		}
		if ($row[6] != "") {
			$_SESSION['flashMessage'] = "Dropship " . $dropship_id . " " . $row[5] . " is in a fight (match " . $row[6] . ") and can't move";
			header('Location: map.php');
			die();
		}
		if ($row[7] != "") {
			$_SESSION['flashMessage'] = "Dropship " . $dropship_id . " " . $row[5] . " is being attacked (match " . $row[7] . ") and can't move<br>You can hire mercenaries to defend while you escape";
			header('Location: map.php');
			die();
		}
		if (strtotime($row[0]) > strtotime('-1 day')) {
			$_SESSION['flashMessage'] = "Jumpship needs 1 full day to recharge from last jump";
			header('Location: map.php');
			die();
		}
		if (pow(abs($row[1] - $row[3]), 2) + pow(abs($row[2] - $row[4]), 2) > 250217) {
			$_SESSION['flashMessage'] = "Jumpships cannot jump farther than 500";
			header('Location: map.php');
			die();
		}
		$planet_name = $row[5];
	} else {
		$_SESSION['flashMessage'] = "Jumpship not found";
		header('Location: map.php');
		die();
	}
	mysqli_free_result($result);
	$sql = "SELECT planet_name, unit_type, sender, owner_name FROM planet AS p LEFT OUTER JOIN user AS u ON u.username=p.owner_name LEFT OUTER JOIN alliance AS a ON a.sender=p.owner_name WHERE p.planet_name='" . $planet_name . "' OR p.planet_name='" . $planet_id . "';";
	$result = mysqli_query($conn, $sql);
	$to_planet = 0;
	$from_planet = 0;
	while ($row = $result->fetch_row()) {
		if ($planet_id == $row[0] && ($row[2] != NULL || $row[1] == 'admin' || $row[3] == 'Unowned' || $row[3] == $username)) {
			$to_planet = 1;
		} elseif ($planet_name == $row[0] && ($row[2] != NULL || $row[1] == 'admin' || $row[3] == 'Unowned' || $row[3] == $username)) {
			$from_planet = 1;
		}
	}
	if ($to_planet == 0 && $from_planet == 0 && $unit_type != 'pirate') {
		$_SESSION['flashMessage'] = "You cannot move from 1 non-allied planet to another non-allied planet";
		header('Location: map.php');
		die();
	}


	$file = 'playerlogs/' . $username . '.log';
	$outputmessage = date("Y-m-d H:i:s") . ' Dropship moved from ' . $planet_name . ' to ' . $planet_id . '
';
	$debugvalue = file_put_contents($file, $outputmessage, FILE_APPEND | LOCK_EX);

	$sql = "UPDATE dropship SET last_move='" . date('Y-m-d H:i:s') . "', planet_name='" . $planet_id .
		"' WHERE dropship_id=" . $dropship_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	header('Location: map.php');
}
$sql = "SELECT d.dropship_id, p.planet_name, d.owner, d.dropship_name FROM dropship AS d INNER JOIN planet AS p ON p.planet_name=d.planet_name WHERE p.owner_name='" . $username . 
	"' AND NOT(d.owner='" . $username . "');";
$edropships = array();
$result=mysqli_query($conn, $sql);
$num_edropships = mysqli_num_rows($result);
while ($row = $result->fetch_row()) {
	if (isset($edropships[$row[1]])) {
		$edropships[$row[1]][$row[0]] = array('owner' => $row[2], 'id' => $row[0], 'planet' => $row[1], 'name' => $row[3]);
	} else {
		$edropships[$row[1]] = array($row[0] => array('owner' => $row[2], 'id' => $row[0], 'planet' => $row[1], 'name' => $row[3]));
	}
}
mysqli_free_result($result);
?>
<script type='text/javascript'>
var dropship = "";
var dropship_id = 0;
var dropship_x = 0;
var dropship_y = 0;
var planet_x = 0;
var planet_y = 0;
var planet = "";
var pop = {};
<?php if ($username != "") {
	$sql = "SELECT p.planet_name, p.owner_name, u.users FROM planet AS p LEFT OUTER JOIN (SELECT GROUP_CONCAT(d.owner separator ', ') " . 
		"AS users, pl.planet_name FROM dropship AS d INNER JOIN planet AS pl ON pl.planet_name=d.planet_name GROUP BY pl.planet_name) AS u ON p.planet_name=u.planet_name;";
	$result=mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$people = array();
		foreach (explode(", ", $row[2]) as $p) {
			if (!in_array($p, $people)) {
				$people[] = $p;
			}
		}
		if (!in_array($username, $people) && $row[1] != $username) {
			$people = array();
		}
		if (!in_array($row[1], $people)) {
			$people[] = $row[1];
		}
		$msg = "";
		foreach ($people as $p) {
			if ($p == "" || $p == "Unowned" || $p == $username) {
				continue;
			}
			if ($msg == "") {
				$msg .= "'" . $p . "'";
			} else {
				$msg .= ", '" . $p . "'";
			}
		}
		echo "pop['" . $row[0] . "'] = [" . $msg . "];
";
	}
	mysqli_free_result($result);
	
}?>
function selectPos(y, x, name) {
	planet = name;
	planet_x = x;
	planet_y = y;
	var e_name = document.getElementById('sel_name');
	var e_pos = document.getElementById('sel_pos');
	var e_move = document.getElementById('move_link');
	var e_plab = document.getElementById('planet_lab');
	e_name.innerHTML=name;
	e_pos.innerHTML= x + ", " + y;
	$('#attack_link').attr('value', name);
	$('#attack_select').text('');
	$('#claim_link').attr('href', 'claim.php?p=' + name);
	$('#unclaim_link').attr('href', 'map.php?p=' + name + '&unclaim=1');
	for (pe in pop[name]) {
		$('#attack_select').append('<option>' + pop[name][pe] + '</option>');
	}
	if (dropship != "") {
		e_move.href='map.php?movedrop=1&p=' + name + '&d=' + dropship_id;
	}
	e_plab.href='mechlab.php?p=' + name;
	updateDistance();
}
function selectDrop(y, x, name, id) {
	dropship = name;
	dropship_id = id;
	dropship_x = x;
	dropship_y = y;
	var e_name = document.getElementById('drop_sel_name');
	var e_pos = document.getElementById('drop_sel_pos');
	var e_move = document.getElementById('move_link');
	var e_dlab = document.getElementById('drop_lab');
	e_name.innerHTML=name + " " + id;
	e_pos.innerHTML= x + ", " + y;
	if (planet != "") {
		e_move.href='map.php?movedrop=1&d=' + id + '&p=' + planet;
	}
	e_dlab.href='mechlab.php?d=' + id;
	updateDistance();
}
function updateDistance() {
	var distance = Math.round(Math.sqrt(Math.pow(Math.abs(dropship_x - planet_x), 2) + Math.pow(Math.abs(dropship_y - planet_y), 2)));
	if (distance > 500) {
		document.getElementById('distance').innerHTML="<span class='red'>" + distance + "</span>";
	} else {
		document.getElementById('distance').innerHTML="<span class='green'>" + distance + "</span>";
	}
}
</script>

<?php
	if (!($flashMessage == "")) {
		echo "<div style='position: fixed; margin-left: 190px;'>" . $flashMessage . "</div>";
	}
?>

<div class='center' style='z-index: 2; width: 150px; position: fixed; background: #222; opacity: 0.8; border-radius: 5px; border: 1px solid grey; padding: 10px 15px 0; overflow: scroll; height: 100%;'>
	<a href='index.php'><img src='images/proxis-icon.png' alt='Home' /></a><br><br>
	<span class='grey'>Selected Planet:</span><br><span id='sel_name'>no selection</span> (<span id='sel_pos'>0, 0</span>)<br><br>
	<span class='grey'>Selected Dropship:</span><br><span id='drop_sel_name'>no selection</span> (<span id='drop_sel_pos'>0, 0</span>)<br><br>
	<span class='grey'>Distance:</span><br><span id='distance'>0</span><br><br>
	<span class='grey'>Current Time:</span><br><?php echo date('Y-m-d H:i:s'); ?><br><br>
	<div class='hr'></div><br>
	<a id='move_link' class='bttn'>Move Dropship</a><br><br>
	<div class='hr'></div><br>
	<a id='drop_lab' class='bttn'>Dropship Mechlab</a><br><br>
	<a id='planet_lab' class='bttn'>Planet Mechlab</a><br><br>
	<div class='hr'></div><br>
	<form action='attack.php' method='get'>
	<select id='attack_select' name='u'>
		
	</select><br><br>
	<button id='attack_link' name='i'>Attack</button>
	<br><br>
	</form>
	<a id='claim_link' class='bttn'>Claim Planet</a><br><br>
	<a id='unclaim_link' class='bttn'>Unclaim Planet</a><br><br>
	</div>
	<br><br>

</div>
<?php
$sql = "SELECT sender FROM alliance WHERE ally='" . $username . "';";
$allies = array();
$result=mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	$allies[] = $row[0];
}
mysqli_free_result($result);

$sql = "SELECT d.dropship_id, d.planet_name, d.capacity, d.last_move, p.location_x, p.location_y, d.dropship_name" . 
	" FROM dropship AS d INNER JOIN planet AS p ON d.planet_name=p.planet_name WHERE owner='" . $username . "';";
$dropships = array();
$result=mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	if (isset($dropships[$row[1]])) {
		$dropships[$row[1]][$row[0]] = array('capacity' => $row[2], 'id' => $row[0], 'last_move' => $row[3], 'x' => $row[4], 'y' => $row[5], 'mechCount' => 0, 'name' => $row[6]);
	} else {
		$dropships[$row[1]] = array($row[0] => array('capacity' => $row[2], 'id' => $row[0], 'last_move' => $row[3], 'x' => $row[4], 'y' => $row[5], 'mechCount' => 0, 'name' => $row[6]));
	}
}
mysqli_free_result($result);

$sql = "SELECT m.mech, m.quantity, m.dropship_id, m.planet_name, d.planet_name FROM mech AS m LEFT OUTER JOIN dropship AS d ON d.dropship_id=m.dropship_id WHERE username='" . $username . "';";
$mechs = array();
$result=mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	if ($row[2] == NULL || $row[2] == "") {
		$mechs[$row[3]][] = array('mech' => $row[0], 'qty' => $row[1]);
	} else {
		$mechs[$row[2]][] = array('mech' => $row[0], 'qty' => $row[1]);
		if (isset($dropships[$row[4]][$row[2]]['mechCount'])) {
			$dropships[$row[4]][$row[2]]['mechCount'] += $row[1];
		} else {
			$dropships[$row[4]][$row[2]]['mechCount'] = $row[1];
		}
	}
}
mysqli_free_result($result);
$sql = "SELECT p.planet_name, p.owner_name, p.match_conditions, p.location_x, p.location_y, p.image, p.cbill_value," . 
	" p.production, u.unit_type, u.unit_name, p.invuln, p.capacity, q.mechcount FROM planet AS p" . 
	" LEFT JOIN (SELECT m.planet_name, SUM(m.quantity) AS mechcount FROM mech AS m GROUP BY m.planet_name)" .
	" AS q ON p.planet_name=q.planet_name LEFT OUTER JOIN user AS u ON p.owner_name=u.username;";
$result=mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	if ($row[8] == 'pirate' && $row[1] != $username && $unit_type != 'admin') {
		$x=$row[3];
		$y=$row[4];
		$hidden=1;
		foreach($dropships as $edropshipplanet) {
			$x1 = 0;
			$y1 = 0;
			foreach ($edropshipplanet as $dur) {
				$x1 = $dur['x'];
				$y1 = $dur['y'];
				break;
			}
			if (pow(abs($x1 -$x), 2) + pow(abs($y1 - $y), 2) < 160001) {
				$hidden=0;
				break;
			}
		}
		if ($hidden == 1) {
			continue;
		}
	}
	echo "<div style='position: absolute; top: " . $row[4] . "px; left: " . $row[3] . "px;'><div class='planet'>";
	echo "<a class='left' onclick='selectPos(" . $row[4] . ", " . $row[3] . ", \"" . $row[0] . "\")'>" .
		"<img src='images/planets/" . $row[5] . "' class='left' />";
	if ($username == $row[1]) {
		echo "<div class='hover grey'>" . $row[3] . ", " . $row[4] . " <span class='green'> " . number_format($row[6]) . "cbills</span><br><span class='grey'>Capacity: " .
			"</span><span class='green'>" . $row[12] . "/" . $row[11] . "</span><br><span class='grey'>Mechs: </span><span class='green'>";
		foreach($mechs[$row[0]] as $mech) {
			echo $mech['qty'] . $mech['mech'] . " ";
		}
		echo "</span><br>Planet Conditions: " . $row[2] . "<br><span class='white'>Production: " . $row[7] . 
			"</span>";
		if (isset($edropships[$row[0]])) {
			echo "<br>Other Orbiting Ships:<br>";
			foreach ($edropships[$row[0]] as $edropship) {
				if ($edropship['name'] == "") {
					echo "<span class='red'>" . $edropship['id'] . " " . $edropship['owner'] . "</span><br>";
				} else {
					echo "<span class='red'>" . $edropship['name'] . " " . $edropship['owner'] . "</span><br>";
				}
			}
		}
	} else {
		echo "<div class='hover grey'>" . $row[0] . "<br>Owner: ";
		if ($username == $row[1] || in_array($row[1], $allies)) {
			echo "<span class='green'>" . $row[1] . "</span>(" . $row[8] . ")";
		} elseif ($row[1] == 'Unowned') {
			echo "<span class='grey'>" . $row[1] . "</span>";
        } elseif ($row[1] == 'Neutral') {
			echo "<span class='white'>" . $row[1] . "</span>";
		} else {
			echo "<span class='red'>" . $row[1] . "</span>(" . $row[8] . ")";
		}
		echo "<br>" . $row[3] . ", " . $row[4] . "<span class='green'> " . number_format($row[6]) . "cbills</span><br>Planet Conditions: " . $row[2] . 
			"<br><span class='white'>Production: " . $row[7] . "</span>";
	}
	echo "</div></a>";
	echo "<div class='left' style='width: 100px;'>";
	if ($row[10] == 1) {
		echo "<a onclick='selectPos(" . $row[4] . ", " . $row[3] . ", \"" . $row[0] . "\")'>" . ucfirst($row[0]) . "<span class='white'>(Safe)</span></a><br>";
	} else {
		echo "<a onclick='selectPos(" . $row[4] . ", " . $row[3] . ", \"" . $row[0] . "\")'>" . ucfirst($row[0]) . "<span class='green'>(" . ((int) ($row[6] / 1000000)) . "M)</span></a><br>";
	}
	if ($username == $row[1] || in_array($row[1], $allies) || $row[1] == 'multitallented') {
		echo "<a style='text-decoration: none;' target='_profile' class='green' href='profile.php?u=" . $row[1] . "'>" . $row[9] . "</a></div>";
	} elseif ($row[1] == 'Unowned') {
		echo "<span class='grey'>" . $row[1] . "</span></div>";
	} elseif ($row[1] == 'Neutral') {
		echo "<span class='white'>" . $row[1] . "</span></div>";
	} else {
		echo "<a style='text-decoration: none;' target='_profile' class='red' href='profile.php?u=" . $row[1] . "'>" . $row[9] . "</a></div>";
	}

	echo "<div class='clearfix'></div></div>";
	if (array_key_exists($row[0], $dropships)) {
		foreach($dropships[$row[0]] as $dropship) {
			echo "<a class='left dropship' onclick='selectDrop(" . $row[4] . ", " . $row[3] . ", \"" . $row[0] . "\", " . $dropship['id'] .
				")'><img src='images/planets/dropship.png' />";
			echo "<div class='hover grey'>";
			if ($dropship['name'] == "") {
				echo "Name: " . $dropship['id'] . " " . $row[0] . "<br>";
			} else {
				echo "Name: " . $dropship['name'] . "<br>";
			}
			echo "Capacity: " . $dropship['mechCount'] . "/" . $dropship['capacity'] . "<br>";
			if (strtotime($dropship['last_move']) > strtotime('-1 day')) {
				echo "Last Jump:<span class='red'>" . $dropship['last_move'] . "</span><br>";
			} else {
				echo "Last Jump:<span class='green'>" . $dropship['last_move'] . "</span><br>";
			}
			echo "<span class='grey'>Mechs: </span>";
			echo "<span class='green'>";
			foreach ($mechs[$dropship['id']] as $mech) {
				echo $mech['qty'] . $mech['mech'] . " ";
			}
			echo "</span></div></a>";
		}
		echo "<div class='clearfix'></div>";
	}
	echo "</div>";
	
}
mysqli_free_result($result);
mysqli_close($conn);

?>


</div>
</body>
</html>