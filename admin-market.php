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
			mysqli_free_result($result); ?>

			<div class='clearfix'></div>
			<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

			<?php if (isset($_GET['a']) && $_GET['a'] == 'del') {
				$m = mysqli_real_escape_string($conn, $_GET['m']);
				$sql = "DELETE FROM `market` WHERE mech='" . $m . "';";
				$result = mysqli_query($conn, $sql);
				mysqli_free_result($result);
				?>
				<span class='red'><?php echo $m; ?> was deleted.</span><br><br>
			<?php } ?>

			<?php $mechPrices = getMechValues($conn); ?>

			<a href="admin.php">Admin Home</a><br><br>

			<h3>Market (<?php echo $mechPrices['count']; ?> mechs)</h3><br>

			<table><th>Mech</th><th>Qty</th><th>Volatility</th><th>Buy</th><th>Sell</th><th>Base</th><th>Action</th>
			<?php foreach ($mechPrices as $mech => $mechPrice) {
				if ($mech == 'variety' || $mech == 'count') {
					continue;
				}
				if ($mechPrice['buy_price'] * 0.75 > $mechPrice['base_price']) { ?>
					<tr class='red'>
				<?php } elseif ($mechPrice['buy_price'] * 0.85 > $mechPrice['base_price']) { ?>
					<tr class='gold'>
				<?php } elseif ($mechPrice['buy_price'] > $mechPrice['base_price']) {?>
					<tr class='green'>
				<?php } else { ?>
					<tr>
				<?php } ?>
					<td><?php echo $mech; ?></td>
					<td><?php echo $mechPrice['buy']; ?></td>
					<td><?php echo $mechPrice['volatility']; ?></td>
					<td><?php echo number_format($mechPrice['buy_price']); ?></td>
					<td><?php echo number_format($mechPrice['sell_price']); ?></td>
					<td><?php echo number_format($mechPrice['base_price']); ?></td>
					<td>
						<a class='bttn' href='admin-edit-market.php?m=<?php echo $mech; ?>'>Edit</a>
						<a class='bttn' href='admin-market.php?m=<?php echo $mech ?>&a=del'>Delete</a>
					</td>
				</tr>
				<?php } ?>
			</table>

			<br>

			<?php mysqli_close($conn); echo $footer; ?>
		</div>
	</body>
</html>