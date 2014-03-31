<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="css/reset.css" />
		<?php session_start(); ?>
		<?php
			include 'header.php';
			$conn = getConnection();
			$mechPrices = getMechValues($conn);
		?>
	</head>
	<body>
		<div id="container">
			<?php echo $header; ?>
			<h2>Market</h2>
			<p>Total Mechs in Circulation: <?php echo $mechPrices['count']; ?></p>
			<p>Total Mechs Variants in MWO: <?php echo $mechPrices['variety']; ?></p>
			<table>
				<th>Mech</th><th>Tons</th><th>Qty</th><th>Buy Price</th><th>Sell Price</th><th>Base Price</th>
				<?php
					foreach($mechPrices as $key => $mechPrice) {
						if ($key == 'count' || $key == 'variety') {
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
							<td><?php echo $key; ?></td>
							<td><?php echo $mechPrice['tons']; ?></td>
							<td><?php echo $mechPrice['buy']; ?></td>
							<td><?php echo number_format($mechPrice['buy_price']); ?></td>
							<td><?php echo number_format($mechPrice['sell_price']); ?></td>
							<td><?php echo number_format($mechPrice['base_price']); ?></td>
						</tr>
				<?php }	?>
			</table>
		</div>
	</body>
</html>