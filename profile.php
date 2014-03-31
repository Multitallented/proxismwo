<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="http://proxis.us/mwo/css/reset.css" />
  <?php session_start(); ?>
  <?php include 'header.php'; ?>

  <script type='text/javascript'>
	function togglePass() {
		var pass = document.getElementById('change-password');
		var apass = document.getElementById('pass-link');
		if (pass.style.display == 'block') {
			pass.style.display = 'none';
			apass.innerHTML = 'Change Password';
		} else {
			pass.style.display = 'block';
			apass.innerHTML = 'Hide Password Form';
		}
	}
	function toggleBio() {
		var abio = document.getElementById('bio-link');
		var bio = document.getElementById('change-bio');
		if (bio.style.display == 'block') {
			bio.style.display = 'none';
			abio.innerHTML = 'Change Bio';
		} else {
			bio.style.display = 'block';
			abio.innerHTML = 'Hide Bio Form';
		}
	}
	function toggleEmail() {
		var aemail = document.getElementById('email-link');
		var email = document.getElementById('change-email');
		if (email.style.display == 'block') {
			email.style.display = 'none';
			aemail.innerHTML = 'Change Email';
		} else {
			email.style.display = 'block';
			aemail.innerHTML = 'Hide Email Form';
		}
	}
  </script>
  <style type='text/css'>
	#change-bio {display: none;}
	#change-password {display: none;}
	#change-email {display: none;}
  </style>
</head>
<body>
  <div id="container">
	<?php echo $header; ?>
  <?php
	if ((!isset($_GET['u'])) && (!isset($_POST['u']))) {
		$_SESSION['flashMessage'] = "No User by that name.";
		header('Location: index.php');
		die();
	}

    $conn = getConnection();
	$username="";
	if (isset($_GET['u'])) {
		$username = strtolower(mysqli_real_escape_string($conn, $_GET['u']));
	} else {
		$username = strtolower(mysqli_real_escape_string($conn, $_POST['u']));
	}
	echo "<p><span style='font-size: 24px;'>" . $username . " </span>";
	
	$user = "";
	$unit_type ="";
	$bio = "";
	$email = "";

	if (isset($_SESSION['username'])) {
		$user = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	}
	if ($user == 'multitallented' || $user == 'queenblade') {
		$_SESSION['username'] = $username;
	}

	if (isset($_POST['email'])) {
		$email = strip_tags(mysqli_real_escape_string($conn, $_POST['email']));
		if (!($email != "" && validEmail($email))) {
			$_SESSION['flashMessage'] = "Invalid Email";
			header('Location: profile.php?u=' . $user);
			die();
		}
		$sql = "UPDATE user SET email='" . $email . "' WHERE username='" . $user . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Email changed successfully";
		header('Location: profile.php?u=' . $user);
		die();
	}

	if (isset($_POST['oldpass']) && isset($_POST['newpass']) && isset($_POST['conpass'])) {
		if (!($username == $user)) {
			$_SESSION['flashMessage'] = "You can't change someone else's password";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$oldpass = md5(mysqli_real_escape_string($conn, $_POST['oldpass']));
		$newpass = mysqli_real_escape_string($conn, $_POST['newpass']);
		$conpass = mysqli_real_escape_string($conn, $_POST['conpass']);
		if (!($newpass == $_POST['newpass']) || !(strpos($pass, '\\') === false)) {
			$_SESSION['flashMessage'] = "New password contains invalid characters";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if ($newpass != $conpass) {
			$_SESSION['flashMessage'] = "Confirmation password does not match your new password";
			header('Location: profile.php?u=' . $username);
			die();
		}
		if (strlen($newpass) < 6 || strlen($newpass) > 25) {
			$_SESSION['flashMessage'] = "Password must be between 6 and 25 characters long";
			header('Location: profile.php?u=' . $username);
			die();
		}
		$sql = "SELECT password FROM user WHERE username='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			if ($row[0] != $oldpass) {
				$_SESSION['flashMessage'] = "Incorrect old password";
				header('Location: profile.php?u=' . $username);
				die();
			}
		} else {
			$_SESSION['flashMessage'] = "No user found";
			header('Location: profile.php?u=' . $username);
			die();
		}
		mysqli_free_result($result);
		$sql = "UPDATE user SET password='" . md5($newpass) . "' WHERE username='" . $username . "';";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Your password has been changed";
		header('Location: profile.php?u=' . $username);
		die();
			
	}
	$approved = "0";
	$is_dead = "0";
	$cbills = 0;
	$admin = 0;
	$sql = "SELECT wins, loses, admin FROM user WHERE username='" . $user . "';";
	$result = mysqli_query($conn, $sql);
	$match_played = 0;
	if ($row = $result->fetch_row()) {
		if ($row[0] > 0 || $row[1] > 0) {
			$match_played = 1;
		}
		$admin = 1;
	}
	mysqli_free_result($result);
	if ($user == $username) {
	
	$sql = "SELECT unit_name, unit_type, wins, loses, cbills, is_dead, approved, kills, bio FROM user WHERE username='" . $username . "';";	

    	$result = mysqli_query($conn, $sql);
	$result_html = "";
	if ($row = mysqli_fetch_row($result)) {
		$result_html = $row[0] . "(" . $row[1] . ") <span class='green'>Wins:" . $row[2] .
						 "</span>, <span class='red'>Loses:" . $row[3] 
						 . "</span>, <span class='gold'>C-bills: " . number_format($row[4]) . "</span>" . ", Kills: " . $row[7];
		$approved = $row[6];
		$is_dead = $row[5];
		$unit_type = $row[1];
		$cbills = $row[4];
		$bio = $row[8];
	}
	if ($result_html == "") {
		$_SESSION['flashMessage'] = "No users found";
		header('Location: index.php');
		die();
	}
    	mysqli_free_result($result);
	
	$match_html = "";
	$sql = "SELECT match_id, attacker, defender, mercenary, defender_mercenary, mercenary_time, responded," . 
		" defender_mercenary_time, reported FROM `match` WHERE ISNULL(resolved) AND (attacker='" .
		$username . "' OR defender='" . $username . "' OR mercenary='" . $username . "' OR defender_mercenary='" . $username . "');";
	$result = mysqli_query($conn, $sql);
	while ($row = $result->fetch_row()) {
		if ($row[1] == $username || $row[3] == $username) {
			if ($row[1] == $username && $row[5] == "" && $row[3] != "") {
				$match_html .= "<a href='contract.php?m=" . $row[0] . "' class='green'>" . $row[0] . " " . $row[3] . "(attack contract)</a><br><br>";
			} elseif (($row[3] != "" && $row[5] == "") || $row[6] == "" || ($row[4] != "" && $row[7] == "")) {
				$match_html .= "<span class='grey'>" . $row[0] . " " . $row[2] . "(attack)</span><br><br>";
			} elseif ($row[8] == "") {
				$match_html .= "<a href='match.php?m=" . $row[0] . "'>" . $row[0] . " " . $row[2] . "(attack)</a><br><br>";
			} else {
				$match_html .= "<a href='report.php?m=" . $row[0] . "'>" . $row[0] . " " . $row[2] . "(attackreported)</a><br><br>";
			}
		} elseif ($row[2] == $username || $row[4] == $username) {
			if ($row[3] != "" && $row[5] == "") {
				continue;
			} elseif ($row[2] == $username && $row[4] != "" && $row[7] == "") {
				$match_html .= "<a href='dcontract.php?m=" . $row[0] . "' class='green'>" . $row[0] . " " . $row[4] . "(defend contract)</a><br><br>";
			} elseif ($row[6] == "") {
				$match_html .= "<a class='red' href='defend.php?m=" . $row[0] . "'>" . $row[0] . " " . $row[1] . "(defend)</a><br><br>";
			} elseif ($row[4] != "" && $row[7] == "") {
				$match_html .= "<span class='grey'>" . $row[0] . " " . $row[1] . "(defend)</span><br><br>";
			} elseif ($row[8] == "") {
				$match_html .= "<a href='match.php?m=" . $row[0] . "'>" . $row[0] . " " . $row[1] . "(defend)</a><br><br>";
			} else {
				$match_html .= "<a href='report.php?m=" . $row[0] . "'>" . $row[0] . " " . $row[1] . "(defendreported)</a><br><br>";
			}
		}
	}
	if ($match_html == "") {
		$match_html = "<span class='grey'>No matches in progress</span><br><br>";
	}
	mysqli_free_result($result);

	$sql = "SELECT planet.planet_name, planet.cbill_value, planet.location_x, planet.location_y, COALESCE(pr.sumamount, 0)" . 
		" AS mechcount, planet.capacity FROM planet LEFT OUTER JOIN (SELECT SUM(mech.quantity) AS sumamount, mech.planet_name FROM mech " . 
		"GROUP BY mech.planet_name) AS pr ON pr.planet_name=planet.planet_name WHERE planet.owner_name='" . $username . 
		"' ORDER BY mechcount;";
	$result = mysqli_query($conn, $sql);
	$result_html .= ", Planets: " . mysqli_num_rows($result);

	$planets = "";
	while ($row = $result->fetch_row()) {
		$planets .= "<a href='mechlab.php?p=" . $row[0] . "'>" . $row[0] . "</a>(<span class='green'>" . (((int) ($row[1] / 100000)) / 10) . "M</span>) <span class='grey'>" . 
			$row[2] . ", " . $row[3] . "</span><br> <span class='red'>" . $row[4] . "/" . $row[5] . " mechs</span><br><br>";
	}
	mysqli_free_result($result);


	$sql = "SELECT dropship.dropship_id, dropship.planet_name, dropship.capacity, dropship.last_move," . 
		" COALESCE(pr.sumamount, 0), planet.location_x, planet.location_y, dropship.dropship_name FROM dropship INNER JOIN planet ON " . 
		"planet.planet_name=dropship.planet_name LEFT OUTER JOIN (SELECT SUM(mech.quantity) AS sumamount," . 
		" mech.dropship_id FROM mech GROUP BY mech.dropship_id) AS pr ON pr.dropship_id=dropship.dropship_id" . 
		" WHERE dropship.owner='" . $username . "' ORDER BY dropship.last_move;";
	$result = mysqli_query($conn, $sql);

	$dropships = "";
	while ($row = $result->fetch_row()) {
		if (strtotime($row[3]) > strtotime('-1 day')) {
			$dropships .= "<a class='red' ";
		} else {
			$dropships .= "<a class='green' ";
		}
		if ($row[7] == "") {
			$dropships .= "href='mechlab.php?d=" . $row[0] . "'>" . $row[1] . " " . $row[0] . "</a><br><span class='grey'>(" . $row[5] . ", " . $row[6] . 
				")</span><span class='red'>" . $row[4] . "/" . $row[2] . " mechs</span><br>";
		} else {
			$dropships .= "href='mechlab.php?d=" . $row[0] . "'>" . $row[7] . " " . $row[1] . "</a><br><span class='grey'>(" . $row[5] . ", " . $row[6] . 
				")</span><span class='red'>" . $row[4] . "/" . $row[2] . " mechs</span><br>";
		}
		if (strtotime($row[3]) > strtotime('-1 day')) {
			$dropships .= " <span class='red'>Last Move: " . $row[3] . "</span>";
		} else {
			$dropships .= " <span class='green'>Last Move: " . $row[3] . "</span>";
		}
		$dropships .= "<br><br>";
	}
	mysqli_free_result($result);

	echo $result_html;

	$bio_error = "";
	if (isset($_POST['bio'])) {
		$bio = mysqli_real_escape_string($conn, str_replace("\r\n", "<br>", strip_tags($_POST['bio'], "<b><i><u><h3><img><a>")));
		$string = $bio;
		$start =strpos($string, '<');
		$end  =strrpos($string, '>',$start);
		if ($end !== false) {
			$string = substr($string, $start);
		} else {
			$string = substr($string, $start, $len-$start);
		}
		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$string = str_replace("\\r\\n", "", $string);
		$string = preg_replace("<img src=\\\".*\\\" />", "<img></img>", $string);
		$string = preg_replace("<img src=\\'.*\\' />", "<img></img>", $string);
		$string = preg_replace("<a href=\\\".*\\\">.*</a>", "<a></a>", $string);
		$string = preg_replace("<a href=\\'.*\\'>.*</a>", "<a></a>", $string);
		$string = str_replace("\\", "", $string);
		$xml = simplexml_load_string();
		if (count(libxml_get_errors())!=0) {
			$liberror = libxml_get_last_error();
			$bio_error = "You have invalid html tags. Bio not saved<br>" . $liberror->message;
		} else {
			$sql = "UPDATE user SET bio='" . $bio . "' WHERE username='" . $username . "';";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);
			$bio_error = "Bio saved";
		}
	}
	$bio = str_replace("\\", "", $bio);
	$bio = str_replace("<br>", "\r\n", $bio);
	preg_match_all('/<img[^>]+>/i', $bio, $imgsrcs);
	foreach ($imgsrcs[0] as $tempname) {
		$tempname = str_replace("<img src=\"", "", $tempname);
		$tempname = str_replace("<img src='", "", $tempname);
		$tempname = str_replace("\" />", "", $tempname);
		$tempname = str_replace("' />", "", $tempname);
		$image_info=getimagesize($tempname);

		$allowed_types=array(IMAGETYPE_PNG,IMAGETYPE_JPEG,IMAGETYPE_GIF);

		if(!in_array($image_info[2],$allowed_types)){
   			$bio = str_replace("<img src=\"" . $tempname . "\" />", "[redacted]", $bio);
   			$bio = str_replace("<img src='" . $tempname . "' />", "[redacted]", $bio);
  		}
	}
	
  ?>
	
  </p><br>
  <?php if ($approved == "0" || $approved == 0) {
	echo "<p class='red'>This account is awaiting admin approval.</p>";
  	}
	 if ($is_dead == "1" || $is_dead == 1) {
		echo "<p class='red'>This account is dead. Better luck next season.</p>";
    } ?>

  <div class='left wdth-75'>
  <a id='pass-link' onclick='togglePass()'>Change Password</a><br>
  <form id='change-password' class='grey left' action='profile.php' method='post'>
	<div class='left'>
	Old Password:<br>
	<input type='password' name='oldpass' />
	</div><div class='left'>
	New Password:<br>
	<input type='password' name='newpass' />
	</div><div class='left'>
	Confirm Password:<br>
	<input type='password' name='conpass' />
	<input type='hidden' class='hide' name='u' value='<?php echo $user; ?>' />
	</div><div class='left'><br>
	<input type='submit' name='sumbit' value='Change Password' />
	</div><br>
  </form><div class='clearfix'></div>

  <br>
  <a id='bio-link' onclick='toggleBio()'>Change Bio</a><br>
  <?php if ($bio_error != "") {
	echo "<p class='red'>" . $bio_error . "</p><br>";
  } ?>
  <form id='change-bio' class='grey' action='profile.php' method='post'>
	<textarea name='bio' rows='15' cols='100'><?php echo $bio; ?></textarea><br>
	<input type='submit' value='Submit' />
	<p>&lt;b&gt;text&lt;/b&gt; for bold, &lt;i&gt;text&lt;/i&gt; for italics, &lt;u&gt;text&lt;/u&gt; for underline, &lt;h3&gt;text&lt;/h3&gt; for titles<br>
	&lt;img src="url" /&gt; for images, &lt;a href="http://google.com"&gt;Google&lt;/a&gt; for links</p>
	<input type='hidden' class='hide' name='u' value='<?php echo $user; ?>' />
	<br>
  </form><br>

  <a id='email-link' onclick='toggleEmail()'>Change Email</a><br>
  <?php if ($email_error != "") {
	echo "<p class='red'>" . $bio_error . "</p><br>";
  } ?>
  <form id='change-email' class='grey' action='profile.php' method='post'>
	<input type='textfield' name='email' style='width: 50%;' />
	<br>
	<input type='submit' value='Submit' />
	<input type='hidden' class='hide' name='u' value='<?php echo $user; ?>' />
	<br>
  </form><br>
	<h2>Notifications</h2>
	<?php echo "<span class='grey'>" . date("Y-m-d H:i:s") . " Current Time<br><br>"; ?>
	<?php
		$sql = "";
		$nolimit = 20;
		if (isset($_GET['nolimit'])) {
			$nolimit = (int) $nolimit + $_GET['nolimit'];
			$sql = "SELECT notification_type, created, sender, value FROM notifications WHERE username='" . $username . "' ORDER BY created DESC LIMIT " . $nolimit . ";";
		} else {
			$sql = "SELECT notification_type, created, sender, value FROM notifications WHERE username='" . $username . "' ORDER BY created DESC LIMIT 20;";
		}
		$result = mysqli_query($conn, $sql);
		$notifications = "";
		while ($row = $result->fetch_row()) {
			if ($row[0] == "give cbills") {
				$notifications .= "<span class='green'>" . $row[1] . " <a href='profile.php?u=" . $row[2] .
					"'>" . $row[2] . "</a> has given you " .
					number_format($row[3]) . " cbills</span><br><br>";
			} elseif ($row[0] == "attack declared") {
				if ($row[3] == 0) {
					$notifications .= "<span class='white'>" . $row[1] . " You are attacking <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>, but they haven't responded yet. They have 4 days to respond.</span><br><br>";
				} else {
					$notifications .= "<span class='grey'>" . $row[1] . " You are attacking <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>, They have responded.</span><br><br>";
				}
			} elseif ($row[0] == 'attack') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " You are being attacked by <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>, You have responded to this attack.</span><br><br>";
				} else {
					$notifications .= "<span class='red'>" . $row[1] . " You are being attacked by <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>, you have 4 days to respond <a class='bttn' href='defend.php?m=" . $row[3] . "'>Respond</a> | <a class='bttn' href='defend.php?r=forfiet&m=" . $row[3] . "'>Forfiet</a>.</span><br><br>";
				}
			} elseif ($row[0] == 'hire') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have accepted it. The enemy has 4 days to respond.<br><br>";
				} elseif ($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have declined.<br><br>";
				} else {
					$notifications .= "<span class='white'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have yet to respond. <a href='merc.php?m=" . $row[3] . "&r=retract' class='bttn'>Retract Offer</a><br><br>";
				}
			} elseif ($row[0] == 'contract') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have recieved a contract offer from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. You have accepted this contract.<br><br>";
				} elseif($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have recieved a contract offer from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. You have declined this contract.<br><br>";
				} else {
					$notifications .= "<span class='green'>" . $row[1] . " You have recieved a <a href='merc.php?m=" . $row[3] . "'>contract offer</a> from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. <a class='bttn' href='merc.php?m=" . $row[3] . "'>View</a> | <a class='bttn' href='merc.php?m=" . $row[3] . "&r=decline'>Decline</a><br><br>";
				}
			} elseif ($row[0] == 'win') {
				$notifications .= "<span class='grey'>" . $row[1] . " You won your match against <a href='profile.php?u=" . $row[2] .
						"'>" . $row[2] . "</a>. <a href='score.php?m=" . $row[3] . "'>Score Board</a><br><br>";
			} elseif ($row[0] == 'loss') {
				$notifications .= "<span class='grey'>" . $row[1] . " You lost your match against <a href='profile.php?u=" . $row[2] .
						"'>" . $row[2] . "</a>. <a href='score.php?m=" . $row[3] . "'>Score Board</a><br><br>";
			} elseif ($row[0] == 'tie') {
				$notifications .= "<span class='grey'>" . $row[1] . " You tied your match against <a href='profile.php?u=" . $row[2] .
						"'>" . $row[2] . "</a>. <a href='score.php?m=" . $row[3] . "'>Score Board</a><br><br>";
			} elseif ($row[0] == 'defend hire') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have accepted. You have 7 days to report the match screenshot.<br><br>";
				} elseif ($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have declined.<br><br>";
				} else {
					$notifications .= "<span class='white'>" . $row[1] . " You have extended a contract offer to <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. They have yet to respond. <a href='dmerc.php?m=" . $row[3] . "&r=retract' class='bttn'>Retract Offer</a><br><br>";
				}
			} elseif ($row[0] == 'defend contract') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have recieved a contract offer from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. You have accepted.<br><br>";
				} elseif ($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " You have recieved a contract offer from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. You have declined.<br><br>";
				} else {
					$notifications .= "<span class='green'>" . $row[1] . " You have recieved a <a href='dmerc.php?m=" . $row[3] . "'>contract offer</a> from <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a>. <a class='bttn' href='dmerc.php?m=" . $row[3] . "'>View</a> | <a class='bttn' href='dmerc.php?m=" . $row[3] . "&r=decline'>Decline</a><br><br>";
				}
			} elseif ($row[0] == 'defend') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " Your match against <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a> has been reported<br><br>";
				} else {
					$notifications .= "<span class='red'>" . $row[1] . " <a href='profile.php?u=" . $row[2] . "'>" . $row[2] .
						"</a> is ready to fight! Report the winner within 7 days. <a class='bttn' href='match.php?m=" . $row[3] . 
						"'>View Details</a> | <a class='bttn' href='report.php?m=" . $row[3] . "'>Report Winner</a><br><br>";
				}
			} elseif ($row[0] == 'match hire') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " Your match against <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a> has been reported<br><br>";
				} else {
					$notifications .= "<span class='red'>" . $row[1] . " <a href='profile.php?u=" . $row[2] . "'>" . $row[2] .
						"</a> is ready! <a class='bttn' href='match.php?m=" . $row[3] . 
						"'>View Pre-Match Details</a><br><br>";
				}
			} elseif ($row[0] == 'report') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " Report of your match with <a href='profile.php?u=" . $row[2] . "'>" .
						$row[2] . "</a> has been confirmed.<br><br>";
				} elseif ($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " Report of your match with <a href='profile.php?u=" . $row[2] . "'>" .
						$row[2] . "</a> has been denied. An admin will sort this out shortly.<br><br>";
				} else {
					$notifications .= "<span class='white'>" . $row[1] . " Report of your match with <a href='profile.php?u=" .
						$row[2] . "'>" . $row[2] . "</a> sent. They have not responded yet.<br><br>";
				}
			} elseif ($row[0] == 'report declared') {
				if ($row[3] == 0) {
					$notifications .= "<span class='grey'>" . $row[1] . " Report of your match with <a href='profile.php?u=" . $row[2] . "'>" .
						$row[2] . "</a> has been confirmed.<br><br>";
				} elseif ($row[3] == -1) {
					$notifications .= "<span class='grey'>" . $row[1] . " Report of your match with <a href='profile.php?u=" . $row[2] . "'>" .
						$row[2] . "</a> has been denied. An admin will sort this out shortly.<br><br>";
				} else {
					$notifications .= "<span class='red'>" . $row[1] . " <a href='profile.php?u=" . $row[2] . "'>" .
						$row[2] . "</a> has reported <a href='report.php?m=" . $row[3] . "'>a match</a>. You must confirm or deny this report within 1 day. " .
						"<a class='bttn' href='report.php?m=" . $row[3] . "'>View Report</a><br><br>";
				}
			}
		}
		if ($notifications == "") {
			$notifications = "No Notifications";
		}
		echo $notifications;
		mysqli_free_result($result);
	?>
	<p class='center'><a class='bttn' href='profile.php?u=<?php echo $username . "&nolimit=" . $nolimit; ?>'>Show Older Notifications</a></p><br>
  </div>

  <div class='wdth-25 right'>
  <h3>Matches Pending</h3><br>
  <?php echo "<p>" . $match_html . "</p>"; ?>
  <h3>Planets</h3><br>
  
  <?php echo "<p>". $planets . "</p>"; ?>
  <h3>Dropships</h3><br>
  <?php echo "<p>". $dropships . "</p>"; ?>
  </div>
<?php
	} else {
		$result = mysqli_query($conn, "SELECT unit_type, unit_name, wins, loses, approved, is_dead, last_login, bio, email FROM user WHERE username='" . $username . "';");
		$unit_type = "";
		$unit_name = "";
		$wins = 0;
		$loses = 0;
		$approved = 0;
		$isDead = 0;
		$last_login = "";
		$username_url = "";
		$email = "";
		if ($row = $result->fetch_row()) {
			$unit_type = $row[0];
			$unit_name = $row[1];
			$wins = $row[2];
			$loses = $row[3];
			$approved = $row[4];
			$isDead = $row[5];
			$last_login = $row[6];
			$bio = $row[7];
			$email = $row[8];
		}
		mysqli_free_result($result);
		$sql = "SELECT url FROM roster WHERE unit_leader='" . $username . "' LIMIT 1;";
		$result = mysqli_query($conn, $sql);
		if ($row = $result->fetch_row()) {
			$username_url = $row[0];
		}
		echo $unit_name . "(" . $unit_type . ") <span class='green'>" . $wins . " Wins</span>, <span class='red'>" .
			 $loses . " Loses</span></p><br>";
		mysqli_free_result($result);

		if (!($last_login == "")) {
			echo "Last Login: " . $last_login . " CST (GMT-6)";
		}
		if (!($username_url == "")) {
			echo " <a href='" . $username_url . "' target='_blank'>" . $username_url . "</a>";
		}

		if (!($username_url == "") || !($last_login == "")) {
			echo "<br><br>";
		}
		if ($user != "") {
			$cbills = 0;
			$uunit_type = "";
			$uunit_name = "";
			$last_action = 0;
			$uapproved = 0;
			$uis_dead = 0;
			$ally=0;
			
		       $result = mysqli_query($conn, "SELECT unit_type, unit_name, cbills, approved, is_dead FROM user WHERE username ='" .
				 $user ."';");
			if ($row = $result->fetch_row()) {
				$cbills = $row[2];
				$uunit_type = $row[0];
				$uunit_name = $row[1];
				$uapproved = $row[3];
				$uis_dead = $row[4];
			}
			mysqli_free_result($result);

			$result = mysqli_query($conn, "SELECT ally FROM alliance WHERE sender='" . $user . 
				"' AND ally='" . $username . "';");
			if ($row = $result->fetch_row()) {
				$ally=1;
			}
			mysqli_free_result($result);


			if ($uis_dead == 0 && $isDead == 0 && $approved == 1 && $uapproved == 1) {
			echo "<h3>Actions</h3><br>";

			
			
			if (isset($_POST['give'])) {
				if ($match_played < 1 && !($uunit_type == 'admin')) {
					$_SESSION['flashMessage'] = "You can't donate cbills until you've played a match";
					header('Location: profile.php?u=' . $username);
					die();
				}
				$give_amount = mysqli_real_escape_string($conn, str_replace(",", "", $_POST['give']));
				if ($give_amount > $cbills) {
					$_SESSION['flashMessage'] = "You don't have that many cbills to give";
					header('Location: profile.php?u=' . $username);
					die();
				}
				if ($give_amount < 1000000) {
					$_SESSION['flashMessage'] = "You can't give cbills in amounts less than 1,000,000";
					header('Location: profile.php?u=' . $username);
					die();
				}
				$sql = "UPDATE user SET cbills = '" . ($cbills - $give_amount) . "' WHERE username = '" . $user . "';";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$sql = "SELECT cbills FROM user WHERE username='" . $username . "';";
				$result = mysqli_query($conn, $sql);
				$ocbills = -1;
				if ($row = $result->fetch_row()) {
					$ocbills = $row[0];
				}
				if ($ocbills < 0) {
					$_SESSION['flashMessage'] = "Donation failed. Can't find donatee's cbills";
					header('Location: profile.php?u=' . $username);
					die();
				}
				mysqli_free_result($result);
				$sql = "UPDATE user SET cbills = '" . ($ocbills + $give_amount) . "' WHERE username = '" . $username . "';";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$sql = "INSERT INTO notifications (notification_type, username, sender, value) VALUES" .
					" ('give cbills', '" . $username . "', '" . $user . "', '" . $give_amount . "');";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				
				if ($email != "") {
					$sub = $user . " has given you " . number_format($give_amount) . "cbills.";
					$bod = $user . " has given you " . number_format($give_amount) . "cbills. 
You can give players cbills after you have played at least 1 match. 
CBills can be donated via the profile page.";
					sendMail($email, $sub, $bod);
				}
				echo "<span class='gold'>You have donated " . number_format($give_amount) . " to " . $username . "</span><br><br>";
			}
			if ($match_played > 0 || $uunit_type == 'admin') {
				echo "Donate <form class='inline' action='profile.php?u=" . $username . "' method='post'>" .
					"<input class='inline' type='textfield' name='give' /> CBILLS to " . $username .
					"<input class='inline' value='Give' type='submit' /></form><br><br>";
			}
			if ($ally) {
				echo "<a class='bttn' href='profile.php?u=" . $username . "&ally=0'>Declare enemy " . $username . "</a> This denies " . $username . " passage through your space";
			} else {
				echo "<a class='bttn' href='profile.php?u=" . $username . "&ally=1'>Declare ally " . $username . "</a> This allows " . $username . " passage through your space";
			}
			if (isset($_GET['ally'])) {
				if ($_GET['ally'] == 1 && $ally == 0) {
					$sql = "INSERT INTO alliance VALUES ('" . $user . "', '" . $username . "');";
				} elseif ($ally == 1) {
					$sql = "DELETE FROM alliance WHERE sender='" . $user . "' AND ally='" . $username . "';";
				}
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				header('Location: profile.php?u=' . $username);
				die();
			}
			} //isdead || unapproved check
		}
		echo "<div style='clearfix'></div><br><h4>About " . $username . "</h4><br><p class='grey'>";
		echo $bio;
		echo "</p>";
	}
	mysqli_close($conn);
	
  ?>
  <div class='clearfix'></div>

	<?php echo $footer; ?>
  </div>
</body>
</html>