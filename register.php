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
	$username = 0;
	if (isset($_SESSION['username']) && $_SESSION['username'] != "multitallented") {
		unset($_SESSION['username']);
		$_SESSION['flashMessage'] = "At least log out before spamming me.";
		header('Location: register.php');
		die();
	} elseif (isset($_SESSION['username']) && ($_SESSION['username'] == "multitallented")) {
		$username = 1;
	}

	function generateRandomString($length = 10) {
    		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    		$randomString = '';
    		for ($i = 0; $i < $length; $i++) {
       		$randomString .= $characters[rand(0, strlen($characters) - 1)];
    		}
    		return $randomString;
	}
	$label_captcha = "";
	if (isset($_POST['username']) && isset($_POST['pass']) && isset($_POST['cpass'])
		&& isset($_POST['url']) && isset($_POST['uname']) && isset($_POST['utype'])
		&& isset($_POST['url1']) && isset($_POST['url2']) && isset($_POST['url3'])
		&& isset($_POST['url4']) && isset($_POST['url5']) && isset($_POST['url6'])
		&& isset($_POST['url7']) && isset($_POST['captcha'])) {

		if (isset($_SESSION['captcha'])) {
			$captcha = $_SESSION['captcha'];
			unset($_SESSION['captcha']);
		}
		$conn = getConnection();
		$user = trim(strtolower(strip_tags(mysqli_real_escape_string($conn, $_POST['username']))));
		$pass = trim(mysqli_real_escape_string($conn, $_POST['pass']));
		$cpass = trim(mysqli_real_escape_string($conn, $_POST['cpass']));
		$url = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url'])));
		$uname = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['uname'])));
		$utype = trim(mysqli_real_escape_string($conn, $_POST['utype']));
		$url1 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url1'])));
		$url2 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url2'])));
		$url3 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url3'])));
		$url4 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url4'])));
		$url5 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url5'])));
		$url6 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url6'])));
		$url7 = trim(strip_tags(mysqli_real_escape_string($conn, $_POST['url7'])));
		$email = "";
		if (isset($_POST['email'])) {
			$email = trim(mysqli_real_escape_string($conn, $_POST['email']));
		}
		$valid_form = 1;
		$username_error = "";
		if (strlen($user) > 25) {
			$username_error = "<span class='red'>Username is too long (25 character maximum)</span><br>";
			$valid_form = 0;
		} elseif (strlen($pass) < 3) {
			$username_error = "<span class='red'>Username is too short (3 character minimum)</span><br>";
			$valid_form = 0;
		} elseif ((!(strpos($user, '\\') === false)) || (!(strpos($user, '<') === false)) || (!(strpos($user, '>') === false))) {
			$username_error = "<span class='red'>Username contains invalid characters</span><br>";
			$valid_form = 0;
		} else {
			$sql = "SELECT username FROM user WHERE username='" . $user . "';";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				$username_error = "<span class='red'>Username already taken</span><br>";
				$valid_form = 0;
			}
			mysqli_free_result($result);
		}

		$password_error = "";
		if (strlen($pass) > 25) {
			$password_error = "<span class='red'>Password is too long (25 character maximum)</span><br>";
			$valid_form = 0;
		} elseif (strlen($pass) < 6) {
			$password_error = "<span class='red'>Password is too short (6 character minimum)</span><br>";
			$valid_form = 0;
		} elseif (!(strpos($pass, '\\') === false)) {
			$password_error = "<span class='red'>Password contains invalid characters</span><br>";
			$valid_form = 0;
		} elseif ($cpass != $pass) {
			$password_error = "<span class='red'>Passwords do not match</span><br>";
			$valid_form = 0;
		}
		
		$email_error = "";
		if ($email != "" && !validEmail($email)) {
			$email_error = "<span class='red'>Not a valid email</span><br>";
			$valid_form = 0;
		}

		$url_error = checkURL($conn, $url);

		$uname_error = "";
		if (strlen($uname) > 50) {
			$uname_error = "<span class='red'>Unit name is too long (50 character maximum)</span><br>";
			$valid_form = 0;
		} elseif (strlen($uname) < 3) {
			$uname_error = "<span class='red'>Unit name is too short (3 character minimum)</span><br>";
			$valid_form = 0;
		} elseif (!(strpos($uname, '\\') === false)) {
			$uname_error = "<span class='red'>Unit name contains invalid characters</span><br>";
			$valid_form = 0;
		} else {
			$sql = "SELECT username FROM user WHERE unit_name='" . $uname . "';";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				$uname_error = "<span class='red'>Unit name already taken</span><br>";
				$valid_form = 0;
			}
			mysqli_free_result($result);
		}
		$utype_error = "";
		if (!($utype == "merc" || $utype == "faction" || $utype == "clan" || $utype == "pirate")) {
			$utype_error = "<span class='red'>Please select a unit type</span><br>";
			$valid_form = 0;
		}
		
		$url1_error = checkURL($conn, $url1);
		$url2_error = checkURL($conn, $url2);
		$url3_error = checkURL($conn, $url3);
		$url4_error = checkURL($conn, $url4);
		$url5_error = checkURL($conn, $url5);
		$url6_error = checkURL($conn, $url6);
		$url7_error = checkURL($conn, $url7);
		$temp_array = array($url, $url1, $url2, $url3, $url4, $url5, $url6, $url7);
		if ($temp_array != array_unique($temp_array)) {
			$url1_error = "<span class='red'>Do not enter duplicate URLs</span><br>";
			$valid_form = 0;
		}
		if ($_POST['captcha'] != $captcha) {
			$captcha_error = "<span class='red'>Incorrect Captcha. Try Again</span><br>";
			$valid_form = 0;
		}
		if ($valid_form == 0) {
			$label_captcha = $_SESSION['captcha'] = generateRandomString(rand(5,7));
			$label_captcha = substr_replace($label_captcha, "<span class='under'>" .
				generateRandomString(rand(1,4)) . "</span>", rand(0, 4), 0);
		} else {
			$pass = md5($pass);
			if ($utype == "faction") {
				$sql = "INSERT INTO user VALUES ('" . $user . "', '" . $pass . "', 0, 0, 0, 0, 'faction', '" .
					$uname . "', 0, 0, 0, '" . date('Y-m-d H:i:s') . "', NULL, '', '" . $email . "', 1000);";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
			} elseif ($utype == "merc") {
				$sql = "INSERT INTO user VALUES ('" . $user . "', '" . $pass . "', 0, 0, 0, 0, 'merc', '" .
					$uname . "', 0, 0, 0, '" . date('Y-m-d H:i:s') . "', NULL, '', '" . $email . "', 1000);";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
			} elseif ($utype == "clan") {
				$sql = "INSERT INTO user VALUES ('" . $user . "', '" . $pass . "', 0, 0, 0, 0, 'clan', '" .
					$uname . "', 0, 0, 0, '" . date('Y-m-d H:i:s') . "', NULL, '', '" . $email . "', 1000);";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
			} elseif ($utype == "pirate") {
				$sql = "INSERT INTO user VALUES ('" . $user . "', '" . $pass . "', 0, 0, 0, 0, 'pirate', '" . 
					$uname . "', 0, 0, 0, '" . date('Y-m-d H:i:s') . "', NULL, '', '" . $email . "', 1000);";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
			}
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);

			$sql = "INSERT INTO roster VALUES ('" . $user . "', '" . $url . "'), " . 
				"('" . $user . "', '" . $url1 . "'), " .
				"('" . $user . "', '" . $url2 . "'), " .
				"('" . $user . "', '" . $url3 . "'), " .
				"('" . $user . "', '" . $url4 . "'), " .
				"('" . $user . "', '" . $url5 . "'), " .
				"('" . $user . "', '" . $url6 . "'), " .
				"('" . $user . "', '" . $url7 . "');";
			$result = mysqli_query($conn, $sql);
			mysqli_free_result($result);

			$_SESSION['username'] = $user;
			$_SESSION['flashMessage'] = "Account created successfully. Please wait for an admin to approve your account.";
			header('Location: /mwo/');
		}
		mysqli_close($conn);
	} else {
		$label_captcha = $_SESSION['captcha'] = generateRandomString(rand(5,7));
		$label_captcha = substr_replace($label_captcha, "<span class='under'>" .
			generateRandomString(rand(1,4)) . "</span>", rand(0, 4), 0);
	}
	function checkURL($conn1, $check_url) {
		if (strpos($check_url, '/') === false) {
			$valid_form = 0;
			return "<span class='red'>Not a valid MWO profile URL</span><br>";
		}
		$temp = explode("/", $check_url);
		if (strpos($temp[count($temp)-2], '-') === false) {
			$valid_form = 0;
			return "<span class='red'>Not a valid MWO profile URL</span><br>";
		}
		$temp = explode("-", $temp[count($temp)-2]);
		unset($temp[0]);
		$temp = implode(" ", $temp);
		$url_name = trim(strtolower(mysqli_real_escape_string($conn1, $temp)));

		if ($url_name == "") {
			$valid_form = 0;
			return "<span class='red'>Not a valid MWO profile URL</span><br>";
		}
		$sql = "SELECT unit_leader FROM roster WHERE url='" . $check_url . "';";
		$result = mysqli_query($conn1, $sql);
		if ($row = $result->fetch_row()) {
			$url_error = "<span class='red'>This user is already on ". $row[0] . "'s team</span><br>";
			$valid_form = 0;
		}
		mysqli_free_result($result);
		return "";
	}
  ?>

	<h2>Registration</h2><br>
	<p>Note: if you are affiliated with a house, please sign up as a faction and not a mercenary unit. You can ally with each other once in game.</p><br>
	<form action='register.php' method='post'>

	<?php echo $username_error; ?>
	Username<span class='red'>*</span>: <input class='inline' type='textfield' name='username'
		<?php if (isset($_POST['username']) && $username_error == "") { echo "value='". $_POST['username'] . "'"; } ?> /><br>
	<?php echo $password_error; ?>
	Password<span class='red'>*</span>: <input class='inline' type='password' name='pass'
		<?php if (isset($_POST['pass']) && $password_error == "") { echo "value='". $_POST['pass'] . "'"; } ?> /><br>
	Confirm password<span class='red'>*</span>: <input class='inline' type='password' name='cpass'
		<?php if (isset($_POST['cpass']) && $password_error == "") { echo "value='". $_POST['cpass'] . "'"; } ?> /><br>
	<?php echo $url_error; ?>

	Email: <input class='inline' type='textfield' name='email' <?php if (isset($_POST['email']) && $email_error == "") {
		echo "value='" . $_POST['email'] . "'"; } ?> style='width: 50%;' /><br>
	<?php echo $email_error; ?>

	MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url'
		<?php if (isset($_POST['url']) && $url_error == "") { echo "value='". $_POST['url'] . "'"; } ?> /><br>
	<?php echo $uname_error; ?>
	Unit Name<span class='red'>*</span>: <input class='inline wdth-33' type='textfield' name='uname'
		<?php if (isset($_POST['uname']) && $uname_error == "") { echo "value='". $_POST['uname'] . "'"; } ?> /><br>
	<?php echo $utype_error; ?>
	Unit Type<span class='red'>*</span>: <select class='inline' name='utype'>
		<option value='none'>(Choose One)</option>
		<option value='merc'>Mercenary</option>
		<option value='faction'>Faction/House</option>
		<option value='clan'>Clan</option>
		<option value='pirate'>Pirate</option>
	</select>
	<br><br><h4 style='border-top: 1px solid grey; border-bottom: 1px solid grey;'>Unit Roster - You need at least 8 pilots - there is no maximum</h4><br>
	<?php echo $url1_error; ?>
	Pilot 1 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url1'
		<?php if (isset($_POST['url1']) && $url1_error == "") { echo "value='". $_POST['url1'] . "'"; } ?> /><br>
	<?php echo $url2_error; ?>
	Pilot 2 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url2'
		<?php if (isset($_POST['url2']) && $url2_error == "") { echo "value='". $_POST['url2'] . "'"; } ?> /><br>
	<?php echo $url3_error; ?>
	Pilot 3 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url3'
		<?php if (isset($_POST['url3']) && $url3_error == "") { echo "value='". $_POST['url3'] . "'"; } ?> /><br>
	<?php echo $url4_error; ?>
	Pilot 4 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url4'
		<?php if (isset($_POST['url4']) && $url4_error == "") { echo "value='". $_POST['url4'] . "'"; } ?> /><br>
	<?php echo $url5_error; ?>
	Pilot 5 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url5'
		<?php if (isset($_POST['url5']) && $url5_error == "") { echo "value='". $_POST['url5'] . "'"; } ?> /><br>
	<?php echo $url6_error; ?>
	Pilot 6 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url6'
		<?php if (isset($_POST['url6']) && $url6_error == "") { echo "value='". $_POST['url6'] . "'"; } ?> /><br>
	<?php echo $url7_error; ?>
	Pilot 7 MWO Account URL<span class='red'>*</span>: <input class='inline wdth-50' type='textfield' name='url7'
		<?php if (isset($_POST['url7']) && $url7_error == "") { echo "value='". $_POST['url7'] . "'"; } ?> /><br>
	<?php echo $captcha_error; ?>
	Enter the following non-underlined letters (<?php echo $label_captcha; ?>)<span class='red'>*</span>: <input class='inline' type='textfield' name='captcha' /><br>
	<br>
	<input class='inline' type='submit' value='Register' />
	
	</form>
	<br><span class='red'>* Denotes a required field</span>

	<?php echo $footer; ?>
  </div>
</body>
</html>