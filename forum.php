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
	$action = "";
	if (isset($_POST['a'])) {
		$action = mysqli_real_escape_string($conn, $_POST['a']);
	}

	include 'forum-util.php';

	$sql_where = "ISNULL(parent_forum)";
	if ($forum) {
		$sql_where = "parent_forum='" . $forum . "'";
	}

$sql = "SELECT forum_name, description, allow_obs FROM forum WHERE " . $sql_where . " ORDER BY weight;";
$result = mysqli_query($conn, $sql);
$num_matches = mysqli_num_rows($result);
$cmatches = "<table><colgroup><col style='width: 25%;'></col><col style='width: 75%;'></col></colgroup>" .
		"<th>Forum</th><th>Last Posts</th>";
while ($row = $result->fetch_row()) {
	if (!$row[2] && !$username) {
		continue;
	}
	$cmatches .= "<tr><td><a href='forum.php?f=" . $row[0] . "'><h3>" . $row[0] . "</h3></a><p>" . $row[1] . "</p></td><td>";

	$sql = "SELECT id, post_name, owner, modified FROM post WHERE parent_forum='" . $row[0] . "' ORDER BY modified LIMIT 5;";
	$result1 = mysqli_query($conn, $sql);
	$t = 0;
	while ($row1 = $result1->fetch_row()) {
		if ($t > 0) {
			$cmatches .= ", ";
		}
		$cmatches .= "<a href='topic.php?t=" . $row1[0] . "'>" . $row1[1] . 
					 "</a> by <a href='profile.php?u=" . $row1[2] . "'>" . $row1[2] . "</a><br>";
		$t++;
	}

	$cmatches .= "</td></tr>";
}
mysqli_free_result($result);
$cmatches .= "</table>";
if (!$num_matches) {
	$cmatches = "";
}

$sql = "SELECT post_name, body, owner, id, modified FROM post WHERE parent_forum='" . $forum . "' ORDER BY modified;";
$result = mysqli_query($conn, $sql);
$numRows = mysqli_num_rows($result);
$posts = "<table><colgroup><col style='width: 80%;'></col><col style='width: 20%;'></col></colgroup><th>Topics</th><th></th>";
while ($row = $result->fetch_row()) {
	$posts .= "<tr><td><a href='topic.php?t=" . $row[3] . "'><h4><strong>" . $row[0] . "</strong></h4></a><br>";
	$posts .= "<span class='grey'>" . substr(strip_tags($row[1]), 0, 100) . "</span></td><td>";
	$posts .= "<a href='profile.php?u=" . $row[2] . "'>" . $row[2] . "</a><br><br>";
	$posts .= "<span class='grey'>" . $row[4] . "</span></td><tr>"; 
}
$posts .= "</table>";
if ($numRows < 1) {
	$posts = "";
}
mysqli_free_result($result);
?>

<div class='clearfix'></div>
<?php echo "<span class='grey'>Current Time: " . date('Y-m-d H:i:s') . "</span><br><br>"; ?>

	<h3 style='display: inline;'>Forums</h3>

	<?php if ($forum) { ?>
		&gt; <a href='forum.php'><h4 style='display: inline;'>Board Index</h4></a>
	<?php } ?>
	<br><br>

	<?php echo $cmatches; ?>

	<br>

	<?php echo $posts; ?>

<br>

	

	<?php if ($forum && $username) { ?>
		<h3>Add Topic</h3><br>
		<form action='forum.php' method='post'>
			<input type='hidden' class='hide' name='f' value='<?php echo $forum; ?>' />
			<input type='textfield' style='width: 80%;' name='title' placeholder='Title' /><br><br>
			<textarea name='body' style='width: 80%; height: 200px;'></textarea><br><br>
			<input name='a' type='submit' value='Create Post' />
		</form>
	<?php } ?>

	<?php mysqli_close($conn); echo $footer; ?>
  </div>
</body>
</html>