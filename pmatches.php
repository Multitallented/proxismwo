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
	$username = "";
	if (isset($_SESSION['username'])) {
		$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	}
	$conn = getConnection();
	$match_id=0;
	$action = "";
	if (isset($_GET['m'])) {
		$match_id = mysqli_real_escape_string($conn, $_GET['m']);
	}
	if (isset($_GET['a'])) {
		$action = mysqli_real_escape_string($conn, $_GET['a']);
	}
	mysqli_free_result($result);


$sql = "SELECT m.match_id, m.planet_name, m.attacker, m.defender, m.mercenary, m.defender_mercenary, m.winner, au.unit_name, du.unit_name, mu.unit_name, dmu.unit_name, wu.unit_name" .
	" FROM `match` AS m INNER JOIN user AS au ON au.username=m.attacker INNER JOIN user AS du " . 
	"ON du.username=m.defender LEFT OUTER JOIN user AS mu ON mu.username=m.mercenary LEFT OUTER " . 
	"JOIN user AS dmu ON dmu.username=m.defender_mercenary LEFT OUTER JOIN user AS wu ON wu.username=m.winner WHERE NOT(ISNULL(resolved)) ORDER BY match_id DESC;";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><th>ID</th><th>planet</th><th>attacker</th><th>atkr merc</th><th>defender</th>" . 
		"<th>def merc</th><th>winner</th>";
while ($row = $result->fetch_row()) {
  $cmatches .= "<tr><td><a href='score.php?m=" . $row[0] . "'>" . $row[0] . "</a><br><br></td><td>" . $row[1] . "</td><td><a href='profile.php?u=" . $row[2] . "' class='red'>" . $row[7] . 
  		"</a></td><td><a href='profile.php?u=" . $row[4] . "' class='red'>" .
		$row[9] . "</a></td><td><a href='profile.php?u=" . $row[3] . "' class='green'>" . $row[8] . "</a></td><td><a href='profile.php?u=" . $row[5] . "' class='green'>" . $row[10] . 
		"</a></td><td>";

	if ($row[6] == "Tie") {
		$cmatches .= $row[6] . "</td></tr>";
	} else {
		$cmatches .= "<a href='profile.php?u=" . $row[6] . "'>" . $row[11] . "</a></td></tr>";
	}
}
mysqli_free_result($result);
$cmatches .= "</table>";
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

	<h3>Past Matches (<?php echo $num_matches; ?>)</h3><br>

	<?php echo $cmatches; ?>

<br>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>