<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="css/reset.css" />
  <?php session_start();
		include 'header.php'; 
  		
  ?>
</head>
<body>
<div id="container">
	<?php echo $header; ?>
You should be redirected momentarily<br>

	<?php
		if (isset($_SESSION['username'])) {
			unset($_SESSION['username']);
		}
		header('Location: index.php');
	?>

	<?php echo $footer; ?>
</div>

</body>
</html>