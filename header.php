<?php

$motd = "I'm working on season 4";

$header = "<div id='header'>" .
"<a class='left' style='position: absolute; top: 5px;' href='index.php'><img src='images/proxislogo.png' alt='Proxis' /></a>" .
"<div style='padding-top: 10px; float: right; position: relative;'>";

if (!isset($_SESSION['username'])) {
	$header .= "<div class='right'><form class='right' action='login.php' method='post'>" .
	"<input id='login-username' name='username' placeholder='Username' class='left' type='textfield' />" .
	"<input id='login-password' name='password' type='password' placeholder='password' class='left' />" .
	"<input class='bttn' type='submit' value='Login' class='left' />" .
	"</form></div>" .
	"<script type='text/javascript' src='js/jquery.placeholder.js'></script>" .
	"<div style='clear: left;'></div><br>" .
	"<p class='right'>Don't have an account? <a class='bttn' href='register.php'>Register</a></p>";
} else {
	$header .= "<p class='right'><a href='profile.php?u=" . strtolower($_SESSION['username']) . "'>" . $_SESSION['username'] . "'s Profile</a>&nbsp;&nbsp;|&nbsp;&nbsp;" .
			"<a class='bttn' href='logout.php'>Logout</a></p>";
}

$header .= "<div style='clear: right;'></div><br>" .
	"<div class='right'><form action='search.php' method='get'>" .
	"<input class='left' name='search' placeholder='Search' type='textfield' />" .
	"<input class='left' value='Search' type='submit' />" .
	"</form></div><br><br>";
if (!isset($_SESSION['username'])) {
	$header .= "<div style='height: 27px;'></div>";
} else {
	$header .= "<div style='height: 51px;'></div>";
}
$header .= "<h1 class='left' style='margin: 0px; z-index: 2; width: 490px;'><span>" .
"<a href='map.php'>Map</a> | <a href='tutorial.php'>Tutorial</a> | <a href='faq.php'>FAQ</a> |" .
" <a href='rules.php'>Rules</a> | <a href='http://proxis.killsecured.com'>Forums</a> |" .
" <a href='pmatches.php'>Combat</a> | <a href='market.php'>Market</a> | <a href='forum.php'>New Forum</a></span></h1></div>";


$header .= "<div class='clearfix'></div><br>" .
"<h2 class='red center'>" . $motd . "</h2></div><br><div id='content'>";

if (isset($_SESSION['flashMessage'])) {
	$header .= "<p id='flash-message'>" . $_SESSION['flashMessage'] . "</p>";
	unset($_SESSION['flashMessage']);
}



/////////////////////////////////// FOOTER /////////////////
$footer = "</div><div id='footer'><p class='center'>Built by Multitallented<br><br>" .
"This is not an official PGI site. This is a user-created site.<br><br>" .
"MechWarrior Online, MWO copyright and property of Piranha Games Interactive</p></div>";

$background = '<script src="js/jquery-1.3.2.min.js"></script>' .
'<script type="text/javascript">$(document).ready(function() {$("body").animate({ backgroundPosition:"-10000px 0px" }, 1280000, "linear");});</script>';

$jquery =  "<script src='js/jquery-1.3.2.min.js'></script>";
//////////////////////////////////////////////////////////////

///////////////////////// MAIL ///////////////////////////////
require_once('../sites/all/phpmailer/class.phpmailer.php');
function sendMail($to, $subject, $body) {
	$mailer = new PHPMailer();
	$mailer->Hostname='hostgoeshere';
	$mailer->Host='hostgoeshere';
	$mailer->Port=465;
	$mailer->SMTPAuth=true;
	$mailer->From='emailaddressgoeshere';
	$mailer->FromName='NoReplyProxisLeague';
	$mailer->Username='emailaddressgoeshere';
	$mailer->Password='passwordgoeshere';
	$mailer->Mailer='smtp';
	$mailer->SMTPSecure='ssl';
	$mailer->Subject=$subject;
	$mailer->Body=$body;
	$mailer->AddAddress($to, 'Recipent');
	return $mailer->Send();
}
////////////////////////////////////////////////////////////////

function add_date($givendate,$day=0,$mth=0,$yr=0) {
	  $cd = strtotime($givendate);
	  $newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
	  date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
	  date('d',$cd)+$day, date('Y',$cd)+$yr));
	  return $newdate;
}

function return_mechs($mid, $playername, $connection) {
	$mechs = array();
	
	$sql = "SELECT mech, quantity, dropship_id, planet_name FROM `mech` WHERE username='" . $playername . "';";
	$result = mysqli_query($connection, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] == "") {
			$mechs[$row[3]][$row[0]] = $row[1];
		} else {
			$mechs[$row[2]][$row[0]] = $row[1];
		}
	}
	mysqli_free_result($result);

	$sql = "SELECT mech, quantity, dropship_id, planet_name FROM `match_mech` WHERE match_id=" . $mid . " AND owner='" . $playername . "';";
	$result = mysqli_query($connection, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] == "") {
			if (array_key_exists($row[3], $mechs) && array_key_exists($row[0], $mechs[$row[3]])) {
				$sql = "UPDATE `mech` SET quantity=(quantity+" . $row[1] . 
					   ") WHERE mech='" . $row[0] . "' AND planet_name='" . $row[3] . "';";
			} else {
				$sql = "INSERT INTO `mech` VALUES ('" . $row[0] . "', '" . $playername . "', " . $row[1] . ", NULL, '" . $row[3] . "');";
		   }
		} else {
			if (array_key_exists($row[2], $mechs) && array_key_exists($row[0], $mechs[$row[2]])) {
				$sql = "UPDATE mech SET quantity=(quantity+" . $row[1] . ") WHERE mech='" . $row[0] . "' AND dropship_id=" . $row[2] . ";";
			} else {
				$sql = "INSERT INTO mech VALUES ('" . $row[0] . "', '" . $playername . "', " . $row[1] . ", " . $row[2] . ", NULL);";
			}
		}
		$result1 = mysqli_query($connection, $sql);
		mysqli_free_result($result1);
	}
	mysqli_free_result($result);

	$sql = "DELETE FROM `match_mech` WHERE match_id=" . $mid . " AND owner='" . $playername . "';";
	$result = mysqli_query($connection, $sql);
	mysqli_free_result($result);
}
//////////////////////////////////////////////////////////
function getMechValues($conn) {
	$mechs = array();
	$sql = "SELECT (m.qty + mm.qty), COUNT(ma.mech) AS mname FROM " .
		"(SELECT COALESCE(SUM(quantity),0) AS qty FROM mech) AS m " .
		"INNER JOIN (SELECT COALESCE(SUM(quantity),0) AS qty " .
		"FROM `match_mech`) AS mm ON 1=1 " .
		"INNER JOIN market AS ma ON 1=1;";

	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$mechs['count'] = $row[0];
		$mechs['variety'] = $row[1];
	}
	mysqli_free_result($result);
	$sql = "SELECT mech, base_price, volatility, buy, tons FROM market ORDER BY tons;";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$bp = $row[1];
		$vol = $row[2];
		$buy = $row[3];
		$tons = $row[4];
		//percent surplus or shortage
		$market_share = (($buy + 20) / ($mechs['count'] + 1860));
		$perfect_market_share = (1 / $mechs['variety']);
		$rate =  $market_share / $perfect_market_share;

		$price = ($rate - 1) * $vol * $bp + $bp;
		$price = $price > $bp * 4 ? $bp * 4 : $price;
		$price = $price < $bp * 0.25 ? $bp * 0.25 : $price;
		$price = round($price);

		$sell_price = $price / 2 * $rate;
		$sell_price = $sell_price > $price * 0.9 ? $price * 0.9 : $sell_price;
		$sell_price = round($sell_price);

		$mechs[$row[0]] = array(
			'base_price' => $bp,
			'volatility' => $vol,
			'buy' => $buy,
			'tons' => $tons,
			'buy_price' => $price,
			'sell_price' => $sell_price,
			'mech' => $row[0],
		);
	}
	mysqli_free_result($result);
	return $mechs;
}

/////////////////////////////////////////////////////////////

function getConnection() {
	return mysqli_connect("localhost", "usernamegoeshere", "passwordgoeshere", "cw", 3306);
}

function getMechWeightClass($weight) {
	if ($weight < 40) {
		return "light";
	} elseif ($weight < 60) {
		return "medium";
	} elseif ($weight < 80) {
		return "heavy";
	} else {
		return "assault";
	}
}

function validEmail($aemail, $skipDNS = false)
{
   $isValid = true;
   $atIndex = strrpos($aemail, "@");
   if (is_bool($atIndex) && !$atIndex)
   {
	  $isValid = false;
   }
   else
   {
	  $domain = substr($aemail, $atIndex+1);
	  $local = substr($aemail, 0, $atIndex);
	  $localLen = strlen($local);
	  $domainLen = strlen($domain);
	  if ($localLen < 1 || $localLen > 64)
	  {
		 // local part length exceeded
		 $isValid = false;
	  }
	  else if ($domainLen < 1 || $domainLen > 255)
	  {
		 // domain part length exceeded
		 $isValid = false;
	  }
	  else if ($local[0] == '.' || $local[$localLen-1] == '.')
	  {
		 // local part starts or ends with '.'
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $local))
	  {
		 // local part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
	  {
		 // character not valid in domain part
		 $isValid = false;
	  }
	  else if (preg_match('/\\.\\./', $domain))
	  {
		 // domain part has two consecutive dots
		 $isValid = false;
	  }
	  else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
	  {
		 // character not valid in local part unless 
		 // local part is quoted
		 if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
		 {
			$isValid = false;
		 }
	  }

	  if(!$skipDNS)
	  {
		  if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
		  {
			 // domain not found in DNS
			 $isValid = false;
		  }
	  }
   }
   return $isValid;
}

function randomDeath($conn, $dest_array, $iterations) {
	$aftermath="";
	for ($i=0; $i<$iterations; $i++) {
		foreach($dest_array as $person => $mechs) {
			if (count($mechs) < 1) {
				continue;
			}
			$index = rand(0, count($mechs) - 1);
			if ($mechs[$index]['qty'] > 1 && $mechs[$index]['match_id'] != 0) {
					$sql = "UPDATE `match_mech` SET quantity=(quantity-1)" .
						   " WHERE mech='" . $mechs[$index]['mech'] . "' AND owner='" .
						   $person . "' AND match_id=" . $mechs[$index]['match_id'];
				if ($mechs[$index]['dropship'] != "") {
					$sql .= " AND dropship_id=" . $mechs[$index]['dropship'];
				} elseif ($mechs[$index]['planet'] != "") {
					$sql .= " AND planet_name='" . $mechs[$index]['planet'] . "'";
				}
				$sql .= ";";
			} elseif ($mechs[$index]['match_id'] != 0) {
				$sql = "DELETE FROM `match_mech` WHERE mech='" . $mechs[$index]['mech'] .
					   "' AND owner='" . $person . "' AND match_id=" . $mechs[$index]['match_id'];
				if ($mechs[$index]['dropship'] != "") {
					$sql .= " AND dropship_id=" . $mechs[$index]['dropship'];
				} elseif ($mechs[$index]['planet'] != "") {
					$sql .= " AND planet_name='" . $mechs[$index]['planet'] . "'";
				}
				$sql .= ";";
			} elseif ($mechs[$index]['match_id'] == 0 && $mechs[$index]['qty'] > 1) {
				$sql = "UPDATE `mech` SET quantity=(quantity-1)" .
					   " WHERE mech='" . $mechs[$index]['mech'] . "' AND owner='" . $person . "'";
				if ($mechs[$index]['dropship'] != "") {
					$sql .= " AND dropship_id=" . $mechs[$index]['dropship'];
				} elseif ($mechs[$index]['planet'] != "") {
					$sql .= " AND planet_name='" . $mechs[$index]['planet'] . "'";
				}
				$sql .= ";";
			} else {
				$sql = "DELETE FROM `mech` WHERE mech='" . $mechs[$index]['mech'] . "' AND owner='" . $person . "'";
				if ($mechs[$index]['dropship'] != "") {
					$sql .= " AND dropship_id=" . $mechs[$index]['dropship'];
				} elseif ($mechs[$index]['planet'] != "") {
					$sql .= " AND planet_name='" . $mechs[$index]['planet'] . "'";
				}
				$sql .= ";";
			}
			$aftermath .= "<span class=\"red\">" . $person . " lost a " . $mechs[$index]['mech'] . "</span><br>";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$sql = "UPDATE market SET buy=(buy-1) WHERE mech='" . $mechs[$index]['mech'] . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
	}
	return $aftermath;
}

function getMechCount($conn, $name, $isPlanet) {
	$sqle = "";
	$returnValue = 0;
	if ($isPlanet) {
		$sqle = "SELECT q.name, SUM(q.quantity) FROM " . 
			"(SELECT m.planet_name AS name, m.quantity FROM mech AS m WHERE m.planet_name='" . $name . "' " . 
				"UNION ALL SELECT mm.planet_name AS name, mm.quantity FROM `match_mech` AS mm WHERE mm.planet_name='" . $name . "') AS q GROUP BY q.name;";
	} else {
		$sqle = "SELECT q.name, SUM(q.quantity) FROM " . 
			"(SELECT m.dropship_id AS name, m.quantity FROM mech AS m WHERE m.dropship_id=" . $name . " " . 
				"UNION ALL SELECT mm.dropship_id AS name, mm.quantity FROM `match_mech` AS mm WHERE mm.dropship_id=" . $name . ") AS q GROUP BY q.name;";
	}
	$resul = mysqli_query($conn, $sqle);
	if ($rowe = $resul->fetch_row()) {
		$returnValue = $rowe[1];
	}
	mysqli_free_result($resul);
	return $returnValue;
}

function getElo($conn, $players) {
	$eloRatings = array();
	$sql = "SELECT username, elo, FROM user WHERE";
	foreach($players as $player) {
		$sql .= " OR username='" . $player . "'";
	}
	$sql .= ";";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		$eloRatings[$row[0]] = $row[1];
	}
	mysqli_free_result($result);
	return $eloRatings;
}

function setElo($conn, $player, $elo) {
	$sql = "UPDATE user SET elo=" . $elo . " WHERE username='" . $player . "';";
	mysqli_query($conn, $sql);
}

function adjustElo($conn, $eloRatings, $winner, $loser, $tie) {
	$k = 30;
	if ($tie) {
		$percentageWin = 1 / (1 + pow(10, ($eloRatings[$loser] - $eloRatings[$winner]) / 400));
		$percentageLose = 1 / (1 + pow(10, ($eloRatings[$winner] - $eloRatings[$loser]) / 400));

		$winAdjust = round($k * (0.5 - $percentageWin));
		$loseAdjust = round($k * (0.5 - $percentageLose));

		setElo($conn, $winner, $eloRatings[$winner] + $winAdjust);
		setElo($conn, $loser, $eloRatings[$loser] + $loseAdjust);
	} else {
		$percentageWin = 1 / (1 + pow(10, ($eloRatings[$loser] - $eloRatings[$winner]) / 400));
		$percentageLose = 1 / (1 + pow(10, ($eloRatings[$winner] - $eloRatings[$loser]) / 400));

		$winAdjust = round($k * (1 - $percentageWin));
		$loseAdjust = round($k * (1 - $percentageLose));

		setElo($conn, $winner, $eloRatings[$winner] + $winAdjust);
		setElo($conn, $loser, $eloRatings[$loser] + $loseAdjust);
	}
}

function resolveMatch($conn, $match_id, $timeout) {
	$aftermath = "";
	$attacker_honorable = 0;
	$defender_honorable = 0;
	$aton = 0;
	$amechs = "";
	$dmechs = "";
	$ammechs = "";
	$dmmechs = "";
	$attacker_lost_mechs = "";
	$defender_lost_mechs = "";
	$amerc_lost_mechs = "";
	$dmerc_lost_mechs = "";
	$winner = "";
	$loser = "";
	$attacker = "";
	$defender = "";
	$mercenary = "";
	$mercenary_qty = 0;
	$defender_mercenary = "";
	$defender_mercenary_qty = 0;
	$planet_name = "";
	$planet_value = 0;
	$planet_conditions = "";
	$planet_owner = "";
	$salvage_array = array(); //mechs, cbills, pilots
	$cur_timestamp = date('Y-m-d H:i:s');
	$marketLosses = array();
	$mm = array();
	$mms = array();
	$mechCounts = array();
	$mechCountsPerPlayer = array();
	
	$mechPrices = getMechValues($conn);

	echo "Match resolution initialized. Gathering match " . $match_id . " data...<br>";

	$sql = "SELECT m.attacker_lost_mechs, m.defender_lost_mechs, m.amerc_lost_mechs," .
		" m.dmerc_lost_mechs, m.winner, m.attacker, m.defender, m.mercenary, m.defender_mercenary," . 
		" m.planet_name, m.mercenary_qty, m.defender_mercenary_qty, p.cbill_value, p.match_conditions, " . 
		"p.owner_name FROM `match` AS m INNER JOIN " . 
		"planet AS p ON p.planet_name=m.planet_name WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	if ($row = $result->fetch_row()) {
		$attacker_lost_mechs = $row[0];
		$defender_lost_mechs = $row[1];
		$amerc_lost_mechs = $row[2];
		$dmerc_lost_mechs = $row[3];
		$winner = $row[4];
		if ($winner == NULL) {
			$winner = "Tie";
		}
		$attacker = $row[5];
		$defender = $row[6];
		$mercenary = $row[7];
		$defender_mercenary = $row[8];
		$planet_name = $row[9];
		$mercenary_qty = $row[10];
		$defender_mercenary_qty = $row[11];
		$planet_value = $row[12];
		$planet_conditions = $row[13];
		$planet_owner = $row[14];
	}
	mysqli_free_result($result);

	if ($winner != "Tie" && $winner != "") {
		if ($winner == $attacker) {
			$loser = $defender;
		} else {
			$loser = $attacker;
		}
	}


	///////////////////////////////TIMEOUT/////////////////////////////
	if ($timeout) {
		if ($winner == "Tie") {
			$dest_array = array();
			$sql = "SELECT mech, quantity, owner, planet_name, dropship_id FROM `match_mech` WHERE match_id=" . $match_id . ";";
			$result = mysqli_query($conn, $sql);
			while ($row = $result->fetch_row()) {
				$dest_array[$row[2]][] = array(
					'mech' => $row[0],
					'qty' => $row[1],
					'planet' => $row[3],
					'dropship' => $row[4],
					'match_id' => $match_id
				);
			}
			mysqli_free_result($result);
			$aftermath .= randomDeath($conn, $dest_array, 2);
		} else {
			echo "Defender failed to respond to attack. Gathering defenders mechs near planet " . $planet_name . "<br>";

			$dest_array = array();
			$sql = "SELECT m.mech, m.quantity, m.username, m.planet_name, m.dropship_id FROM `mech` AS m " .
				   "LEFT OUTER JOIN dropship AS d ON d.dropship_id=m.dropship_id WHERE (m.planet_name='" . $planet_name . 
				   "' OR d.planet_name='" . $planet_name . "') AND username='" . $defender . "';";
			$result = mysqli_query($conn, $sql);
			while ($row = $result->fetch_row()) {
				$dest_array[$row[2]][] = array(
					'mech' => $row[0],
					'qty' => $row[1],
					'planet' => $row[3],
					'dropship' => $row[4],
					'match_id' => 0
				);
			}
			mysqli_free_result($result);
			foreach ($dest_array as $key => $value) {
				if ($value['planet'] != "") {
					$mms[$key][$value['planet']]['mechCount'] += $value['qty'];
					$mms[$key][$value['planet']]['planet'] = true;
					$mechCounts[$value['planet']] += $value['qty'];
				} else {
					$mms[$key][$value['dropship']]['mechCount'] += $value['qty'];
					$mms[$key][$value['dropship']]['planet'] = false;
					$mechCounts[$value['dropship']] += $value['qty'];
				}
				$mechCountsPerPlayer[$key] += $value['qty'];
			}

			echo "Defender's mechs are gathered. Destroying 6 of them...<br>";
			$aftermath .= randomDeath($conn, $dest_array, 6);
			$mechCountsPerPlayer[$defender] -= 6;
			echo "Mechs Destroyed: <br>";
			echo $aftermath;
			
			if ($planet_owner == $defender) {
				if (!isset($mms[$defender][$planet_name])) {
					$mms[$defender][$planet_name]['planet'] = true;
					$mms[$defender][$planet_name]['mechCount'] = 0;
					$mechCounts[$planet_name] = 0;
				} else {
					$mms[$defender][$planet_name]['mechCount'] += 0;
					$mechCounts[$planet_name] += 0;
				}
				$match_mechs[$defender]['planets'][$planet_name]['planet'] = $planet_name;
				$match_mechs[$defender]['planets'][$planet_name]['value'] = $planet_value;
			}

			$salvage_array[$attacker]['cbills'] += rand(20000000, 27500000);
		}
	}
  //////////////////////////////////////////////////////////////

	if ($defender == $planet_owner) {
		$mechCounts[$planet_name] = getMechCount($conn, $planet_name, true);
		$mechCountsPerPlayer[$defender] = $mechCounts[$planet_name];
		$mms[$defender][$planet_name]['planet'] = true;
		$mms[$defender][$planet_name]['mechCount'] = $mechCounts[$planet_name];
	}

	$sql = "SELECT mm.mech, mm.quantity, mm.planet_name, mm.dropship_id, m.quantity, mm.owner, d.capacity, p.capacity" .
		" FROM `match_mech` AS mm LEFT OUTER JOIN dropship AS d ON mm.dropship_id=d.dropship_id AND NOT(ISNULL(mm.dropship_id))" . 
		" LEFT OUTER JOIN planet AS p ON mm.planet_name=p.planet_name AND NOT(ISNULL(mm.planet_name)) LEFT OUTER JOIN " . 
		"mech AS m ON m.mech=mm.mech AND ((mm.dropship_id=m.dropship_id AND NOT(ISNULL(mm.dropship_id))) OR " . 
		"(mm.planet_name=m.planet_name AND NOT(ISNULL(mm.planet_name)))) WHERE match_id=" . $match_id . ";";
	
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[2] != "") {
			if (!isset($mechCounts[$row[2]])) {
				$mechCounts[$row[2]] = getMechCount($conn, $row[2], true);
				if ($row[5] == $attacker || $row[5] == $mercenary) {
					$mechCountsPerPlayer[$attacker] += $mechCounts[$row[2]];
				} else {
					$mechCountsPerPlayer[$defender] += $mechCounts[$row[2]];
				}
			}

			$mm[$row[5]][$row[0] . ":" . $row[2]] = array(
				'qty' => $row[1],
				'oqty' => $row[4],
				'planet' => true
			);
			$mms[$row[5]][$row[2]]['mechCount'] = $mechCounts[$row[2]];
			$mms[$row[5]][$row[2]]['capacity'] = $row[7];
			$mms[$row[5]][$row[2]]['planet'] = true;
		} elseif ($row[3] != "") {
			if (!isset($mechCounts[$row[3]])) {
				$mechCounts[$row[3]] = getMechCount($conn, $row[3], false);
				if ($row[5] == $attacker || $row[5] == $mercenary) {
					$mechCountsPerPlayer[$attacker] += $mechCounts[$row[3]];
				} else {
					$mechCountsPerPlayer[$defender] += $mechCounts[$row[3]];
				}
			}

			$mm[$row[5]][$row[0] . ":" . $row[3]] = array(
				'qty' => $row[1],
				'oqty' => $row[4],
				'planet' => false
			);
			$mms[$row[5]][$row[3]]['mechCount'] = $mechCounts[$row[2]];
			$mms[$row[5]][$row[3]]['capacity'] = $row[6];
			$mms[$row[5]][$row[3]]['planet'] = false;
		}
	}
	mysqli_free_result($result);

	$debug = ($attacker == 'tester01' || $attacker == 'tester02');
	if ($debug) {
		foreach ($mechCountsPerPlayer as $key => $val) {
			echo "Mech Count '" . $key . "'' is: " . $val . "<br>";
		}
		echo "<br>";
		foreach ($mechCounts as $key => $val) {
			foreach ($mechCountsPerPlayer as $key => $val) {
				echo "Mech Count '" . $key . "'' is: " . $val . "<br>";
			}
		}
	}
	
	if (!$debug) {
		$sql = "DELETE FROM `match_mech` WHERE match_id=" . $match_id . ";";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
	}
	
	$aton = 0;
	foreach($mm[$attacker] as $key => $mech) {
		$aton += $mechPrices[explode(":",$key)[0]]['tons'] * $mech['qty'];
	}
	if (isset($mm[$mercenary])) {
		foreach($mm[$mercenary] as $key => $mech) {
			$aton += $mechPrices[explode(":",$key)[0]]['tons'] * $mech['qty'];
		}
	}

	$dton = 0;
	foreach($mm[$defender] as $key => $mech) {
		$dton += $mechPrices[explode(":",$key)[0]]['tons'] * $mech['qty'];
	}
	if (isset($mm[$defender_mercenary])) {
		foreach($mm[$defender_mercenary] as $key => $mech) {
			$dton += $mechPrices[explode(":",$key)[0]]['tons'] * $mech['qty'];
		}
	}
	$attacker_dishonorable = 0;
	$defender_dishonorable = 0;

	if ($aton - $dton > 50 && !$timeout) {
		$defender_honorable = 1;
	}
	if ($aton - $dton < -50 && !$timeout) {
		$attacker_honorable = 1;
	}
	if ($aton - $dton < -20 && !$timeout && $aton > 549) {
		$defender_dishonorable = 1;
	}
	if ($aton - $dton > 20 && !$timeout && $dton > 549) {
		$attacker_dishonorable = 1;
	}

	if ($debug) {
		echo "Aton: " . $aton . "<br>";
		echo "Dton: " . $dton . "<br>";
	}

	$akills = 0;
	$dkills = 0;
	$aorig_kills = 0;
	$dorig_kills = 0;
	$atype = "";
	$acbills = 0;
	$awins = 0;
	$aloses = 0;
	$dtype = "";
	$dcbills = 0;
	$dwins = 0;
	$dloses = 0;
	$amcbills = 0;
	$amwins = 0;
	$amloses = 0;
	$dmcbills = 0;
	$dmwins = 0;
	$dmloses = 0;
	$sql = "SELECT unit_type, cbills, wins, loses, kills, username FROM user WHERE username='" . $attacker . 
		"' OR username='" . $defender;
	if ($mercenary != "") {
		$sql .= "' OR username='" . $mercenary;
	}
	if ($defender_mercenary != "") {
		$sql .= "' OR username='" . $defender_mercenary;
	}
	$sql .= "';";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[5] == $attacker) {
			$atype = $row[0];
			$acbills = $row[1];
			$awins = $row[2];
			$aloses = $row[3];
			$akills = $row[4];
			$aorig_kills = $row[5];
		} elseif ($row[5] == $defender) {
			$dtype = $row[0];
			$dcbills = $row[1];
			$dwins = $row[2];
			$dloses = $row[3];
			$dkills = $row[4];
			$dorig_kills = $row[5];
		} elseif ($row[5] == $mercenary) {
			$amcbills = $row[1];
			$amwins = $row[2];
			$amloses = $row[3];
		} elseif ($row[5] == $defender_mercenary) {
			$dmcbills = $row[1];
			$dmwins = $row[2];
			$dmloses = $row[3];
		}
	}
	mysqli_free_result($result);
	
	foreach (explode(", ", $attacker_lost_mechs) as $mech) {
		$mech_qty = explode(" ", $mech)[0];
		$mech_var = explode(" ", $mech)[1];
		for ($i=0; $i < $mech_qty; $i++) {
			$dkills++;
			$salvage_array[$defender]['cbills'] += ((int) ($mechPrices[$mech_var]['sell_price'] / 4)) + 2500000;
			if (rand(0,1) == 1) {
				$aftermath .= "<span class=\"red\">" . $attacker . " lost 1 " . $mech_var . "</span><br>";
				if ($dtype == 'pirate' || ($dtype != 'pirate' && rand(0,3) == 3) ||
						($dtype == 'clan' && $defender_honorable == 1 && rand(0,1) == 1)) {
					$salvage_array[$defender]['mechs'][$mech_var] += 1;
					$aftermath .= "<span class=\"green\">" . $defender . " salvaged " . $mech_var . "</span><br>";
				} else {
					$marketLosses[$mech_var] += 1;
				}
				foreach ($mm[$attacker] as $mechKey => $mechValue) {
					if (explode(":", $mechKey)[0] == $mech_var) {
						if($mechValue['qty'] > 1) {
							$mm[$attacker][$mechKey]['qty'] -= 1;
						} else {
							unset($mm[$attacker][$mechKey]);
						}
						$mms[$attacker][explode(":", $mechKey)[1]]['mechCount'] -= 1;
						$mechCountsPerPlayer[$attacker] -= 1;
						break;
					}
				}

			}
		}
	}
	
	foreach (explode(", ", $defender_lost_mechs) as $mech) {
		$mech_qty = explode(" ", $mech)[0];
		$mech_var = explode(" ", $mech)[1];
		for ($i=0; $i < $mech_qty; $i++) {
			$akills++;
			$salvage_array[$attacker]['cbills'] += ((int) ($mechPrices[$mech_var]['sell_price'] / 4)) + 2500000;
			if (rand(0,1) == 1) {
				$aftermath .= "<span class=\"red\">" . $defender . " lost 1 " . $mech_var . "</span><br>";
			    if ($atype == 'pirate' || ($atype != 'pirate' && rand(0,3) == 3) ||
						($atype == 'clan' && $attacker_honorable == 1 && rand(0,1) == 1)) {
					$salvage_array[$attacker]['mechs'][$mech_var] += 1;
					$aftermath .= "<span class=\"green\">" . $attacker . " salvaged " . $mech_var . "</span><br>";
			    } else {
					$marketLosses[$mech_var] += 1;
			    }
				foreach ($mm[$defender] as $mechKey => $mechValue) {
					if (explode(":", $mechKey)[0] == $mech_var) {
						if($mechValue['qty'] > 1) {
							$mm[$defender][$mechKey]['qty'] -= 1;
						} else {
							unset($mm[$defender][$mechKey]);
						}
						$mms[$defender][explode(":", $mechKey)[1]]['mechCount'] -= 1;
						$mechCountsPerPlayer[$defender] -= 1;
						break;
					}
				}
			}
		}
	}

	if ($mercenary != "") {
		foreach (explode(", ", $amerc_lost_mechs) as $mech) {
			$mech_qty = explode(" ", $mech)[0];
			$mech_var = explode(" ", $mech)[1];
			for ($i=0; $i < $mech_qty; $i++) {
				$dkills++;
				$salvage_array[$defender]['cbills'] += ((int) ($mechPrices[$mech_var]['sell_price'] / 4)) + 2500000;
				if (rand(0,1) == 1) {
					$aftermath .= "<span class=\"red\">" . $mercenary . " lost 1 " . $mech_var . "</span><br>";
				    if ($dtype == 'pirate' || ($dtype != 'pirate' && rand(0,3) == 3) ||
							($dtype == 'clan' && $defender_honorable == 1 && rand(0,1) == 1)) {
						$salvage_array[$defender]['mechs'][$mech_var] += 1;
						$aftermath .= "<span class=\"green\">" . $defender . " salvaged " . $mech_var . "</span><br>";
				    } else {
						$marketLosses[$mech_var] += 1;
					}
					foreach ($mm[$mercenary] as $mechKey => $mechValue) {
						if (explode(":", $mechKey)[0] == $mech_var) {
							if($mechValue['qty'] > 1) {
								$mm[$mercenary][$mechKey]['qty'] -= 1;
							} else {
								unset($mm[$mercenary][$mechKey]);
							}
							$mms[$mercenary][explode(":", $mechKey)[1]]['mechCount'] -= 1;
							$mechCountsPerPlayer[$mercenary] -= 1;
							break;
						}
					}
				}
			}
		}
	}

	if ($defender_mercenary != "") {
		foreach (explode(", ", $dmerc_lost_mechs) as $mech) {
			$mech_qty = explode(" ", $mech)[0];
			$mech_var = explode(" ", $mech)[1];
			for ($i=0; $i < $mech_qty; $i++) {
				$akills++;
				$salvage_array[$attacker]['cbills'] += ((int) ($mechPrices[$mech_var]['sell_price'] / 4)) + 2500000;
				if (rand(0,1) == 1) {
					$aftermath .= "<span class=\"red\">" . $defender_mercenary . " lost 1 " . $mech_var . "</span><br>";
				    if ($atype == 'pirate' || ($atype != 'pirate' && rand(0,3) == 3) ||
							($atype == 'clan' && $attacker_honorable == 1 && rand(0,1) == 1)) {
						$salvage_array[$attacker]['mechs'][$mech_var] += 1;
						$aftermath .= "<span class=\"green\">" . $attacker . " salvaged " . $mech_var . "</span><br>";
				    } else {
						$marketLosses[$mech_var] += 1;
					}
					foreach ($mm[$defender_mercenary] as $mechKey => $mechValue) {
						if (explode(":", $mechKey)[0] == $mech_var) {
							if($mechValue['qty'] > 1) {
								$mm[$defender_mercenary][$mechKey]['qty'] -= 1;
							} else {
								unset($mm[$defender_mercenary][$mechKey]);
							}
							$mms[$defender_mercenary][explode(":", $mechKey)[1]]['mechCount'] -= 1;
							$mechCountsPerPlayer[$defender_mercenary] -= 1;
							break;
						}
					}
				}
			}
		}
	}
	
	if (!$debug) {
		foreach ($marketLosses as $key => $val) {
			$sql = "UPDATE market SET buy=(buy-" . $val . ") WHERE mech='" . $key . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
	}
	
	if ($winner != "Tie") {
		if ($atype == 'merc') {
			$salvage_array[$attacker]['cbills'] += rand(4000000, 12000000);
		}
		if ($dtype == 'merc') {
			$salvage_array[$defender]['cbills'] += rand(4000000, 12000000);
		}
	}

	if ($atype == 'pirate' && $winner == $attacker && !$timeout) {
		$cap_salvage = (8 - ($akills - $aorig_kills)) * rand(1000000, 5000000);
		if ($cap_salvage > 0) {
			$aftermath .= "<span class=\"green\">" . $attacker . " stole " . number_format($cap_salvage) . 
				"cbills during base capture</span><br>";
			$salvage_array[$attacker]['cbills'] += $cap_salvage;
			$salvage_array[$defender]['cbills'] -= $cap_salvage;
		}
	}
	if ($dtype == 'pirate' && $winner == $defender && !$timeout) {
		$cap_salvage = (8 - ($dkills - $dorig_kills)) * rand(1000000, 5000000);
		if ($cap_salvage > 0) {
			$aftermath .= "<span class=\"green\">" . $defender . " stole " . number_format($cap_salvage) . 
				"cbills during base capture</span><br>";
			$salvage_array[$defender]['cbills'] += $cap_salvage;
			$salvage_array[$attacker]['cbills'] -= $cap_salvage;
		}
	}

	if ($aton > 800) {
		$tonnagePenalty = rand(0, ($aton - 800) * 30000);
		$salvage_array[$attacker]['cbills'] -= $tonnagePenalty;
		$salvage_array[$defender]['cbills'] += $tonnagePenalty;
		$aftermath .= "<span class=\"red\">" . $attacker . " gave " . number_format($tonnagePenalty) . " cbills to " . $defender . " for using 800+ tons<br>";
	}
	if ($dton > 800) {
		$tonnagePenalty = rand(0, ($dton - 800) * 300000);
		$salvage_array[$defender]['cbills'] -= $tonnagePenalty;
		$salvage_array[$attacker]['cbills'] += $tonnagePenalty;
		$aftermath .= "<span class=\"red\">" . $defender . " gave " . number_format($tonnagePenalty) . " cbills to " . $attacker . " for using 800+ tons<br>";
	}

	if ($atype == 'clan' && $attacker_honorable == 1 && !$timeout) {
		$salvage_bonus = number_format(rand(1000000, 10000000));
		$aftermath .= "<span class=\"green\">" . $attacker . " earned " . ($akills - $aorig_kills) . " kills in honorable combat</span><br>";
		$aftermath .= "<span class=\"green\">" . $attacker . " earned " . $salvage_bonus . "cbills for honorable combat</span><br>";
		$salvage_array[$attacker]['cbills'] += $salvage_bonus;
	}
	if ($dtype == 'clan' && $defender_honorable == 1 && !$timeout) {
		$salvage_bonus = number_format(rand(1000000, 10000000));
		$aftermath .= "<span class=\"green\">" . $defender . " earned " . ($dkills - $dorig_kills) . " kills in honorable combat</span><br>";
		$aftermath .= "<span class=\"green\">" . $defender . " earned " . $salvage_bonus . "cbills for honorable combat</span><br>";
		$salvage_array[$defender]['cbills'] += $salvage_bonus;
	}
	if ($atype == 'clan' && $attacker_dishonorable == 1 && !$timeout) {
		$honor_loss = $salvage_array[$attacker]['cbills'] / 2 - rand(2000000, 6000000);
		$aftermath .= "<span class=\"red\">" . $attacker . " paid " . number_format($honor_loss) . "cbills in dishonorable combat</span><br>";
		$salvage_array[$attacker]['cbills'] -= $honor_loss;
		$salvage_array[$defender]['cbills'] += $honor_loss;
	}
	if ($dtype == 'clan' && $defender_dishonorable == 1 && !$timeout) {
		$honor_loss = $salvage_array[$defender]['cbills'] / 2 - rand(2000000, 6000000);
		$aftermath .= "<span class=\"red\">" . $defender . " paid " . number_format($honor_loss) . "cbills in dishonorable combat</span><br>";
		$salvage_array[$defender]['cbills'] -= $honor_loss;
		$salvage_array[$attacker]['cbills'] += $honor_loss;
	}

	if (($winner == $attacker && $defender == $planet_owner) || ($winner == $defender && $planet_owner==$attacker)) {
		$planet_steal_amount = (int) ($planet_value / 10);
		$salvage_array[$winner]['cbills'] += $planet_steal_amount;
		$salvage_array[$loser]['cbills'] -= $planet_steal_amount;
		$aftermath .= "<span class=\"green\">" . number_format($planet_steal_amount) . 
			"cbills was stolen from planet " . $planet_name . "</span><br>";
		$planet_value -= $planet_steal_amount;
	}

	if ($timeout && $winner != 'Tie') {
		$forfeit_penalty = rand(400000, 600000);
		$salvage_array[$winner]['cbills'] += $forfeit_penalty;
		$salvage_array[$loser]['cbills'] -= $forfeit_penalty;
	}

	$winRep = $mercenary == "" ? $attacker : $mercenary;
	$loseRep = $defender_mercenary == "" ? $defender : $defender_mercenary;

	$eloRatings = getElo($conn, array($winRep, $loseRep));

	

	if ($mercenary != "") {
		$merc_salv = (int) ($salvage_array[$attacker]['cbills'] * $mercenary_qty / 12);
		$salvage_array[$attacker]['cbills'] -= $merc_salv;
		$salvage_array[$mercenary]['cbills'] += $merc_salv;
		if (!$timeout) {
			$salvage_array[$attacker]['cbills'] += rand(8000000, 12000000);
		}
		$aftermath .= "<span class=\"green\">" . $attacker . " gained " . 
			number_format($salvage_array[$attacker]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
		$aftermath .= "<span class=\"green\">" . $mercenary . " gained " . 
			number_format($salvage_array[$mercenary]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
	} else {
		$aftermath .= "<span class=\"green\">" . $attacker . " gained " . 
			number_format($salvage_array[$attacker]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
	}
	if ($defender_mercenary != "" && !$timeout) {
		$merc_salv = (int) ($salvage_array[$defender]['cbills'] * $defender_mercenary_qty / 12);
		$salvage_array[$defender]['cbills'] -= $merc_salv;
		$salvage_array[$defender_mercenary]['cbills'] += $merc_salv;
		if (!$timeout) {
			$salvage_array[$defender]['cbills'] += rand(8000000, 12000000);
		}
		$aftermath .= "<span class=\"green\">" . $defender . " gained " . 
			number_format($salvage_array[$defender]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
		$aftermath .= "<span class=\"green\">" . $defender_mercenary . " gained " . 
			number_format($salvage_array[$defender_mercenary]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
	} else {
		$aftermath .= "<span class=\"green\">" . $defender . " gained " . 
			number_format($salvage_array[$defender]['cbills']) . 
			"cbills in earnings and salvaged parts/data/supplies/etc.</span><br>";
	}
	if ($mercenary != "" && !($winner == "Tie" && $timeout)) {
		$attack_hire_bonus = rand(8000000, 12000000);
		$salvage_array[$attacker]['cbills'] += $attack_hire_bonus;
		$aftermath .= "<span class=\"green\">" . $attacker . " gained " . 
			number_format($attack_hire_bonus) . 
			"cbills for hiring a mercenary.</span><br>";
	}
	if ($defender_mercenary != "" && !$timeout) {
		$defend_hire_bonus = rand(8000000, 12000000);
		$salvage_array[$defender]['cbills'] += $defend_hire_bonus;
		$aftermath .= "<span class=\"green\">" . $defender . " gained " . 
			number_format($defend_hire_bonus) . 
			"cbills for hiring a mercenary.</span><br>";
	}

	if (!$debug) {
		foreach($mm as $person => $mechs) {
			foreach ($mechs as $mechKey => $mechValue) {
				if ($mechValue['planet']) {
					if ($mechValue['oqty'] < 0) {
						$sql = "SELECT quantity FROM mech WHERE mech='" . explode(":", $mechKey)[0] .
							"' AND username='" . $person . "' AND planet_name='" . explode(":", $mechKey)[1] . "';";
						$result = mysqli_query($conn, $sql);
						if ($row = $result->fetch_row()) {
							$mm[$person][$mechKey]['oqty'] = $row[0];
						} else {
							$mm[$person][$mechKey]['oqty'] = 0;
						}
						mysqli_free_result($result);
					}

					if ($mm[$person][$mechKey]['oqty'] < 1) {
						$sql = "INSERT INTO mech VALUES ('" . explode(":", $mechKey)[0] . "', '" . $person . "', " .
							$mm[$person][$mechKey]['qty'] . ", NULL, '" . explode(":", $mechKey)[1] . "');";
					} else {
						$sql = "UPDATE mech SET quantity=" . ($mm[$person][$mechKey]['oqty'] + $mm[$person][$mechKey]['qty']) . " WHERE mech='" . explode(":", $mechKey)[0] .
							"' AND username='" . $person . "' AND planet_name='" . explode(":", $mechKey)[1] . "';";
					}
					$result = mysqli_query($conn, $sql);
					mysqli_free_result($result);

				} else {
					if ($mechValue['oqty'] < 0) {
						$sql = "SELECT m.quantity, d.owner FROM mech AS m INNER JOIN dropship AS d ON d.dropship_id=m.dropship_id WHERE mech='" . explode(":", $mechKey)[0] .
							"' AND username='" . $person . "' AND dropship_id=" . explode(":", $mechKey)[1] . ";";
						$result = mysqli_query($conn, $sql);
						if ($row = $result->fetch_row()) {
							$mm[$person][$mechKey]['oqty'] = $row[0];
						} else {
							$mm[$person][$mechKey]['oqty'] = 0;
						}
						mysqli_free_result($result);
					}

					if ($mm[$person][$mechKey]['oqty'] < 1) {
						$sql = "INSERT INTO mech VALUES ('" . explode(":", $mechKey)[0] . "', '" . $person . "', " .
							$mm[$person][$mechKey]['qty'] . ", " . explode(":", $mechKey)[1] . ", NULL);";
					} else {
						$sql = "UPDATE mech SET quantity=" . ($mm[$person][$mechKey]['oqty'] + $mm[$person][$mechKey]['qty']) . " WHERE mech='" . explode(":", $mechKey)[0] .
							"' AND username='" . $person . "' AND dropship_id=" . explode(":", $mechKey)[1] . ";";
					}
					$result = mysqli_query($conn, $sql);
					mysqli_free_result($result);
				}
			}
		}
	}


	foreach ($salvage_array as $salvagePerson => $salvage) {
		if (!array_key_exists($salvagePerson, $mm) && $salvagePerson == $attacker) {
			$recipient = $mercenary;
		} elseif (!array_key_exists($salvagePerson, $mm) && $salvagePerson == $defender) {
			$recipient = $defender_mercenary;
		} else {
			$recipient = $salvagePerson;
		}
		foreach ($salvage['mechs'] as $mechKey => $mechValue) {
			foreach ($mms[$recipient] as $locationKey => $currentLocation) {
				if ($currentLocation['capacity'] <= $currentLocation['mechCount']) {
					continue;
				}
				if ($currentLocation['mechCount'] + $mechValue > $currentLocation['capacity']) {
					$mechDiff = $currentLocation['capacity'] - $currentLocation['mechCount'];
					$currentLocation['mechCount'] = $currentLocation['capacity'];
					$mechCountsPerPlayer[$recipient] += $mechDiff;
					$mm[$recipient][$mechKey . ":" . $locationKey]['qty'] += $mechDiff;
					$salvage_array[$salvagePerson]['mechs'][$mechKey] -= $mechDiff;
					$mechValue -= $mechDiff;
				} else {
					$mms[$recipient][$locationKey]['mechCount'] += $mechValue;
					$mechCountsPerPlayer[$recipient] += $mechValue;
					$mm[$recipient][$mechKey . ":" . $locationKey]['qty'] += $mechValue;
					unset($salvage_array[$salvagePerson]['mechs'][$mechKey]);
					continue 2;
				}
			}
		}
		foreach ($salvage_array[$recipient]['mechs'] as $mechKey => $mechValue) {
			$mech_val = $mechPrices[$mechKey]['sell_price'];
			$sale_worth = $mech_val * $mechValue;
			$salvage_array[$recipient]['cbills'] += $sale_worth;
			$aftermath .= "<span class=\"green\">" . $mechValue . " " . $mechKey . " were sold for " . number_format($sale_worth) . "cbills. Unable to find room for them.</span><br>";
		}
	}
	

	foreach($mms as $person => $locationValue) {
		if ($winner == $person ||
				$winner == 'Tie' ||
				($person == $mercenary && $winner == $attacker) ||
				($person == $defender_mercenary && $winner == $defender)) {
			echo "Continue for " . $person . "<br>";
			continue;
		}
		foreach($locationValue as $locationKey => $currentLocation) {
			echo "Analyzing " . $locationKey . " for player " . $person . "<br>";
			if ((($person == $attacker || $person == $mercenary) && $mechCountsPerPlayer[$attacker] + $mechCountsPerPlayer[$mercenary] < 12) || 
					(($person == $defender || $person == $defender_mercenary) && $mechCountsPerPlayer[$defender] + $mechCountsPerPlayer[$defender_mercenary] < 12)) {
				if (!$currentLocation['planet']) {
					$aftermath .= "<span class=\"green\">" . $winner . " captured dropship " . $locationKey . " from " .
						$person . " and all mechs onboard</span><br>";
					if (!$debug) {
						$sql = "UPDATE dropship SET owner='" . $winner . "' WHERE dropship_id=" . $locationKey . ";";
						$result = mysqli_query($conn, $sql);
						mysqli_free_result($result);

						$sql = "UPDATE mech SET username='" . $winner . "' WHERE dropship_id=" . $locationKey . ";";
						$result = mysqli_query($conn, $sql);
						mysqli_free_result($result);
					}
				} else {
					if ($person == $planet_owner ||
							($person == $mercenary && $attacker == $planet_owner) ||
							($person == $defender_mercenary && $defender == $planet_owner)) {

						if (!$debug) {
							$sql = "UPDATE planet SET owner_name='" . $winner . "', cbill_value=" . ($planet_value * 0.8) .
								" WHERE planet_name='" . $planet_name . "';";
							$result = mysqli_query($conn, $sql);
							mysqli_free_result($result);

							$sql = "UPDATE mech SET username='" . $winner . "' WHERE planet_name='" . $planet_name . "';";
							$result = mysqli_query($conn, $sql);
							mysqli_free_result($result);
						}
						$aftermath .= "<span class=\"green\">" . $winner . " captured planet " . $planet_name . " and " . number_format($planet_value) . "cbills and all mechs stationed there</span><br>";

						$salvage_array[$winner]['cbills'] += $planet_value;
					}
				}
			} elseif ($currentLocation['planet'] && $person == $planet_owner ||
						($person == $mercenary && $attacker == $planet_owner) ||
						($person == $defender_mercenary && $defender == $planet_owner)) {
				if (!$debug) {
					$sql = "UPDATE planet SET cbill_value=" . $planet_value . " WHERE planet_name='" . $planet_name . "';";
					$result = mysqli_query($conn, $sql);
					mysqli_free_result($result);
				}
			}
		}
	}

	if ($debug) {
		echo "<br>" . $aftermath . "<br><br>";
		foreach ($mms as $key => $val) {
			echo "Mechs for '" . $key . "': " . var_dump($val) . "<br><br>";
		}
		echo "<br><br>";
		foreach ($mechCountsPerPlayer as $key => $val) {
			echo "Mech Count '" . $key . "': " . $val . "<br>";
		}
		die();
	}

	if ($acbills + $salvage_array[$attacker]['cbills'] < 0) {
		$sql = "UPDATE user SET cbills=0";
	} else {
		$sql = "UPDATE user SET cbills=" . ($acbills + $salvage_array[$attacker]['cbills']);
	}
	if ($attacker_dishonorable == 0 && $atype == 'clan') {
		$sql .= ", kills=" . $akills;
	}
	if ($winner == $attacker) {
		$sql .= ", wins=" . ($awins + 1) . " WHERE username='" . $attacker . "';";
	} elseif ($winner == $defender) {
		$sql .= ", loses=" . ($aloses + 1) . " WHERE username='" . $attacker . "';";
	} else {
		$sql .= " WHERE username='" . $attacker . "';";
	}
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);

	if ($dcbills + $salvage_array[$defender]['cbills'] < 0) {
		$sql = "UPDATE user SET cbills=0";
	} else {
		$sql = "UPDATE user SET cbills=" . ($dcbills + $salvage_array[$defender]['cbills']);
	}
	if ($defender_dishonorable == 0 && $dtype=='clan') {
		$sql .= ", kills=" . $dkills;
	}
	if ($winner == $defender) {
		$sql .= ", wins=" . ($dwins + 1) . " WHERE username='" . $defender . "';";
	} elseif ($winner == $attacker) {
		$sql .= ", loses=" . ($dloses + 1) . " WHERE username='" . $defender . "';";
	} else {
		$sql .= " WHERE username='" . $defender . "';";
	}
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($mercenary != "") {
		if ($amcbills + $salvage_array[$mercenary]['cbills'] < 0) {
			$sql = "UPDATE user SET cbills=0";
		} else {
			$sql = "UPDATE user SET cbills=" . ($amcbills + $salvage_array[$mercenary]['cbills']);
		}
		if ($winner == $attacker) {
			$sql .= ", wins=" . ($amwins + 1) . " WHERE username='" . $mercenary . "';";
		} elseif ($winner == $defender) {
			$sql .= ", loses=" . ($amloses + 1) . " WHERE username='" . $mercenary . "';";
		} else {
			$sql .= " WHERE username='" . $mercenary . "';";
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
	}
	if ($defender_mercenary != "") {
		if ($dmcbills + $salvage_array[$defender_mercenary]['cbills'] < 0) {
			$sql = "UPDATE user SET cbills=0";
		} else {
			$sql = "UPDATE user SET cbills=" . ($dmcbills + $salvage_array[$defender_mercenary]['cbills']);
		}
		if ($winner == $defender) {
			$sql .= ", wins=" . ($dmwins + 1) . " WHERE username='" . $defender_mercenary . "';";
		} elseif ($winner == $attacker && !$timeout) {
			$sql .= ", loses=" . ($dmloses + 1) . " WHERE username='" . $defender_mercenary . "';";
		} else {
			$sql .= " WHERE username='" . $defender_mercenary . "';";
		}
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
	}

	$sql = "UPDATE notifications SET value=0 WHERE value=" . $match_id . " AND (notification_type='report' OR notification_type='report declared');";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
	if ($winner == $attacker) {
		$sql = "INSERT INTO notifications VALUES ('win', '" . $attacker . "', '" . $cur_timestamp .
			"', '" . $defender . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('loss', '" . $defender . "', '" . $cur_timestamp .
			"', '" . $attacker . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('win', '" . $mercenary . "', '" . $cur_timestamp .
				"', '" . $defender . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		if ($defender_mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('loss', '" . $defender_mercenary . "', '" . $cur_timestamp .
				"', '" . $attacker . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
	} elseif ($winner == $defender) {
		$sql = "INSERT INTO notifications VALUES ('win', '" . $defender . "', '" . $cur_timestamp .
			"', '" . $attacker . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('loss', '" . $attacker . "', '" . $cur_timestamp .
			"', '" . $defender . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('loss', '" . $mercenary . "', '" . $cur_timestamp .
				"', '" . $defender . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		if ($defender_mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('win', '" . $defender_mercenary . "', '" . $cur_timestamp .
				"', '" . $attacker . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
	} else {
		$sql = "INSERT INTO notifications VALUES ('tie', '" . $defender . "', '" . $cur_timestamp .
			"', '" . $attacker . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$sql = "INSERT INTO notifications VALUES ('tie', '" . $attacker . "', '" . $cur_timestamp .
			"', '" . $defender . "', " . $match_id . ");";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		if ($mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('tie', '" . $mercenary . "', '" . $cur_timestamp .
				"', '" . $defender . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
		if ($defender_mercenary != "") {
			$sql = "INSERT INTO notifications VALUES ('tie', '" . $defender_mercenary . "', '" . $cur_timestamp .
				"', '" . $attacker . "', " . $match_id . ");";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
		}
	}

	$sql = "UPDATE `match` SET report_response='" . $cur_timestamp . "', resolved='" . $cur_timestamp .
		"', aftermath_report='" . $aftermath . "', last_action='" . $cur_timestamp . "' WHERE match_id=" . $match_id . ";";
	$result = mysqli_query($conn, $sql);
	mysqli_free_result($result);
}
?>