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

  <!-- <h2>Sponsored Mercenaries</h2>
  <p>If you are signed up in this league as a mercenary, you can pay cbills to show up here!</p><br><br> -->
<table><tr>
	<?php
	$conn = getConnection();
	$sql = "SELECT COUNT(username) FROM user WHERE NOT(unit_type='admin') AND NOT(is_dead=1) AND NOT(username='tester01') AND NOT(username='tester02') AND approved=1;";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " teams</span></td> ";
	}
	mysqli_free_result($result);


	$sql = "SELECT COUNT(match_id) FROM `match` WHERE ISNULL(resolved);";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " pending matches</span></td> ";
	}
	mysqli_free_result($result);

	$sql = "SELECT COUNT(planet_name) FROM planet WHERE NOT(owner_name='Unowned') AND NOT(owner_name='multitallented') AND NOT(owner_name='Neutral');";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " owned planets</span></td> ";
	}
	mysqli_free_result($result);

	$sql = "SELECT COUNT(dropship_id) FROM dropship WHERE NOT(owner='multitallented');";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " dropships</span></td> ";
	}
	mysqli_free_result($result);

	$sql = "SELECT COALESCE(SUM(quantity), 0) FROM `match_mech`;";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " mechs in combat</span></td> ";
	}
	mysqli_free_result($result);

	$sql = "SELECT COALESCE(SUM(quantity), 0) FROM mech;";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		echo "<td><span class='gold'>" . $row[0] . " idle mechs</span></td> ";
	}
	mysqli_free_result($result);

	$mechs_array = getMechValues($conn);
	$mkey = array_rand($mechs_array);
	if ($mkey == 'variety' || $mkey == 'count') {
		$mkey = 'LCT-3M';
	}
	if ($mkey == 'COM-TDK') {
		$mkey = 'COM-DK';
	}
	echo "<td><span class='gold'>" . $mechs_array[$mkey]['buy'] . " " . $mkey . " in service</span></td> ";
	?>
</tr></table>
<br><br>
	
  <div class="left wdth-25">
	
	<h3>Top Factions</h3>
	<br><div style='margin-left: 15px;'>
	<?php 
		$sql = "SELECT user.username, user.unit_name, user.wins, COALESCE(pr.count, 0) planets FROM user LEFT OUTER JOIN " . 
			"(SELECT COUNT(planet.owner_name) AS count, planet.owner_name FROM planet GROUP BY planet.owner_name)" . 
			" AS pr ON pr.owner_name=user.username WHERE unit_type='faction' AND approved='1' AND is_dead='0' AND NOT(username='tester01') AND NOT(username='tester02') ORDER BY planets DESC, wins DESC LIMIT 20;";
		$result = mysqli_query($conn, $sql);
		$fmsg = "";
		while ($row = $result->fetch_row()) {
			$fmsg .= "<a href='profile.php?u=" . $row[0] . "'>" . $row[1] . "</a><br> Planets: " .
				$row[3] . " Wins: " . $row[2] . "<br><br>";
		}
		echo $fmsg;
		mysqli_free_result($result);
	?>
	</div>

  </div>

  <div class="left wdth-25">

	<h3>Top Mercenaries</h3>
	<br><div style='margin-left: 15px;'>
	<?php 
		$sql = "SELECT username, unit_name, wins, loses, COALESCE(wins / loses, 'Undef') AS ratio FROM user WHERE unit_type='merc' AND" . 
			" approved='1' AND is_dead='0' ORDER BY COALESCE(wins / loses, wins * 2) DESC, wins DESC LIMIT 20;";
		$result = mysqli_query($conn, $sql);
		$factions = array();
		while ($row = $result->fetch_row()) {
			$rat = $row[4];
			if ($rat != "Undef") {
				$rat = number_format($rat, 1);
			}
			array_push($factions, array('username' => $row[0], 'unit_name' => $row[1], 'wins' => $row[2], 'ratio' => $rat));
		}

		foreach ($factions as $faction) {
			$msg = "<a href='profile.php?u=" . $faction['username'] . "'>" . 
			$faction['unit_name'] . "</a><br> Ratio: " . $faction['ratio'] . " Wins: " . $faction['wins'] . "<br><br>";
			echo $msg;
		}
	?>
	</div>

  </div>

  <div class="left wdth-25">

	<h3>Top Clans</h3>
	<br><div style='margin-left: 15px;'>
	<?php 
		$sql = "SELECT user.username, user.unit_name, user.kills, COALESCE(pr.count, 0) AS planets FROM user LEFT OUTER JOIN" . 
			" (SELECT planet.owner_name, COUNT(planet.owner_name) AS count FROM planet GROUP BY planet.owner_name)" . 
			" AS pr ON pr.owner_name=user.username WHERE unit_type='clan' AND approved='1' AND" . 
			" is_dead='0' ORDER BY kills DESC, planets DESC LIMIT 20;";
		$result = mysqli_query($conn, $sql);
		$factions = "";
		while ($row = $result->fetch_row()) {
			$factions .= "<a href='profile.php?u=" . $row[0] . "'>" . $row[1] . "</a><br> Kills: " . $row[2] . " Planets: " .
				$row[3] . "<br><br>";
		}
		echo $factions;
		mysqli_free_result($result);
	?>
	</div>
  </div>

  <div class="left wdth-25">

	<h3>Top Pirates</h3>
	<br><div style='margin-left: 15px;'>
	<?php 
		$mechPrices = getMechValues($conn);
		$sql = "SELECT username, unit_name, cbills FROM user WHERE unit_type='pirate' AND approved='1' AND is_dead='0' ORDER BY cbills DESC LIMIT 20;";
		$result = mysqli_query($conn, $sql);
		$factions = array();
		while ($row = $result->fetch_row()) {
			$assets = $row[2];
			$sql = "SELECT q.mech, SUM(q.quantity) FROM (SELECT m.mech, m.quantity FROM mech AS m WHERE username='" . $row[0] . 
					"' UNION ALL SELECT mm.mech, mm.quantity FROM `match_mech` AS mm WHERE owner='" . $row[0] . "') as q GROUP BY q.mech;";
			$result1 = mysqli_query($conn, $sql);
			while ($row1 = $result1->fetch_row()) {
				$assets += $mechPrices[$row1[0]]['sell_price'] * $row1[1];
			}
			mysqli_free_result($result1);
			$factions[] = array('username' => $row[0], 'unit_name' => $row[1], 'cbills' => $assets);
		}

		function cmp_pirates($a, $b) {
			if ($a['cbills'] == $b['cbills']) {
				return 0;
			}
			return $a['cbills'] > $b['cbills'] ? -1 : 1;
		}
		uasort($factions, 'cmp_pirates');
		foreach ($factions as $faction) {
			$msg = "<a href='profile.php?u=" . $faction['username'] . "'>" . 
			$faction['unit_name'] . "</a><br> Assets: " . (number_format((int) ( $faction['cbills'] / 1000000)) . 'M') . " cbills<br><br>";
			echo $msg;
		}
		mysqli_free_result($result);
		mysqli_close($conn);
	?>
	</div>

  </div>

  <div class="clearfix"></div>
	  <h3>What's the difference between Factions, Mercs, etc?</h3>
	  <p class='grey'><strong>Factions</strong> are rich with lots of planets. Problem is they don't have many dropships making them
      largely dependant on mercenaries to cover for their immobility. Factions typically garrison planets and move out slowly.
	  If you like conquest and diplomacy then factions are the way to go.</p><br>
	  <p><strong>Mercenaries</strong> are average in terms of resources and unique in their ability to be hired to fight
	  others' battles without having to move too far. If you like negotiating prices and alliances then mercs are for you.</p><br>
	  <p class='grey'><strong>Clans</strong> are very powerful. They have many mechs (and all production on their homeworld so they can build
	  any mech). The problem is once they step off that homeworld with their one huge dropship, production costs double. Clans also
	  fight honorably with a unique ability to adjust their drop deck before the battle (when they see the tonnage of their enemy).
	  If you like invasion campaigns then clans are for you.</p><br>
	  <p><strong>Pirates</strong> are tricky in that they can move anywhere and their planets are hidden to anyone without a dropship
	  nearby. They have many dropships and double production costs. To counter-act this, they get increased salvage making them reliant
	  on whatever they can scrounge from battles. If you like roughing it on the dark side, then I recommend pirates.</p>
	<?php echo $footer; ?>
  </div>
</body>
</html>