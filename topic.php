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
	$conn = getConnection();
	if (isset($_SESSION['username'])) {
		$username = strtolower(mysqli_real_escape_string($conn, $_SESSION['username']));
	}
	$forum="";
	if (isset($_GET['f'])) {
		$forum = mysqli_real_escape_string($conn, $_GET['f']);
	}
	if (isset($_POST['f'])) {
		$forum = mysqli_real_escape_string($conn, $_POST['f']);
	}
	$topic="";
	if (isset($_GET['t'])) {
		$topic = mysqli_real_escape_string($conn, $_GET['t']);
	}
	if (isset($_POST['t'])) {
		$topic = mysqli_real_escape_string($conn, $_POST['t']);
	}
	$action = "";
	if (isset($_POST['a'])) {
		//$action = mysqli_real_escape_string($conn, $_POST['a']);
	}

	include 'forum-util.php';

	$sql_where = "ISNULL(parent_forum)";
	if ($forum) {
		$sql_where = "parent_forum='" . $forum . "'";
	}

$sql = "SELECT post_name, body, owner, modified, created FROM post WHERE id=" . $topic . ";";
$result = mysqli_query($conn, $sql);
$posts = "<table><colgroup><col style='width: 20%;'></col><col style='width: 80%;'></col></colgroup><th></th><th>";
if ($row = $result->fetch_row()) {
	$posts .= $row[0] . "</th><tr><td><a href='profile.php?u=" . $row[2] . "'>" . $row[2] .
		"</a><br><span class='grey'>" . $row[4] . "</span></td><td><p>" . removeEscape($row[1]) .
		"</p>";
	if ($row[4] != $row[3]) {
		$posts .= "<p class='grey'>Edited on " . $row[3] . "</p>";
	}
	$posts .= "</td></tr>";
}
@mysqli_free_result($result);

$sql = "SELECT comment_id, owner, body, modified, created FROM comment WHERE parent_topic=" . $topic . " ORDER BY created;";
$result = mysqli_query($conn, $sql);
while ($row = $result->fetch_row()) {
	$posts .= "<tr><td><a href='profile.php?u=" . $row[1] . "'>" . $row[1] . "</a><br><span class='grey'>" .
			  $row[4] . "</span></td><td><p>" . removeEscape($row[2]) . "</p>";
	if ($row[4] != $row[3]) {
		$posts .= "<p class='grey'>Edited on " . $row[3] . "</p>";
	}
	$posts .= "</td></tr>";
	
}
$posts .= "</table>";
@mysqli_free_result($result);
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

	<h3 style='display: inline;'>Forums</h3>

	<?php if ($forum) { ?>
		&gt; <a href='forum.php'><h4 style='display: inline;'>Board Index</h4></a>
	<?php } ?>
	<br><br>

	<?php echo $posts; ?>

	<br>

<br>

	

	<?php if ($username) { ?>
		<h3>Add Comment</h3><br>
		<form action='topic.php' method='post'>
			<input type='hidden' class='hide' name='t' value='<?php echo $topic; ?>' />
			<textarea name='body' style='width: 80%; height: 200px;'></textarea><br><br>
			<input name='a' type='submit' value='Reply' />
		</form>
	<?php } ?>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>