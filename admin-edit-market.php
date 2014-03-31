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
				$_SESSION['flashMessage'] = "You must be logged in on an admin account to view this page";
				header('Location: /mwo/');
				die();
			}
			$conn = getConnection();
			$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));


			//Check to make sure the user is an admin
			$sql = "SELECT admin FROM user WHERE username ='" . $username . "';";
			$result = mysqli_query($conn, $sql);
			if ($row = $result->fetch_row()) {
				if ($row[0] == 0) {
					$_SESSION['flashMessage'] = "You must be logged in on an admin account to view this page";
					header('Location: index.php');
					die();
				}
			}
			mysqli_free_result($result);

			if (!isset($_GET['m'])) {
				$_SESSION['flashMessage'] = "Please select a mech";
				header('Location: admin-market.php');
				die();
			}
			$m = mysqli_real_escape_string($conn, $_GET['m']);

			if (isset($_GET['qty'])) {
				$qty = mysqli_real_escape_string($conn, $_GET['qty']);
				$sql = "UPDATE market SET buy=" . $qty . " WHERE mech='" . $m . "';";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$tempFlash = $m . " qty set to " . $qty;
			}
			if (isset($_GET['vol'])) {
				$vol = mysqli_real_escape_string($conn, $_GET['vol']);
				$sql = "UPDATE market SET volatility=" . $vol . " WHERE mech='" . $m . "';";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				$tempFlash = $m . " volatility set to " . $vol;
			}
			?>

			<div class='clearfix'></div>
			<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

			<?php $mechPrices = getMechValues($conn);
			$mech = $mechPrices[$m];
			if (isset($tempFlash)) { ?>
				<span class='red'><?php echo $tempFlash; ?></span><br>
			<?php } ?>

			<a href="admin.php">Admin Home</a> | <a href="admin-market.php">Market Manager</a><br><br>

			<h3><?php echo $m ?> (<?php echo $mech['buy']; ?> mechs, buy/sell
				<?php echo number_format($mech['buy_price']) . "/" . number_format($mech['sell_price']) .
					" : base " . number_format($mech['base_price']); ?></h3><br>

			Qty:
			<form action='admin-edit-market.php' method='get'>
				<input type='hidden' class='hide' name='m' value='<?php echo $m; ?>' />
				<input type='textfield' name='qty' value='<?php echo $mech['buy'] ?>' />
				<input type='submit' name='a' value='Save' />
			</form><br><br>

			Volatility:
			<form action='admin-edit-market.php' method='get'>
				<input type='hidden' class='hide' name='m' value='<?php echo $m; ?>' />
				<input type='textfield' name='vol' value='<?php echo $mech['volatility'] ?>' />
				<input type='submit' name='a' value='Save' />
			</form>

			<br>

			<?php mysqli_close($conn); echo $footer; ?>
		</div>
	</body>
</html>