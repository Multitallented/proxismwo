<?php
if ($action == 'Create Post' && $forum) {
		$title = mysqli_real_escape_string($conn, $_POST['title']);
		$body = mysqli_real_escape_string($conn, $_POST['body']);

		$body = sanitizePost($body);

		$sql = "INSERT INTO post (post_name, body, owner, parent_forum) VALUES " . 
			   "('" . $title . "', '" . $body . "', '" . $username . "', '" . $forum . "');";
		$result = mysqli_query($conn, $sql);
		mysqli_free_result($result);
		$_SESSION['flashMessage'] = "Post " . $title . " saved successfully";
		header('Location: forum.php?f=' . $forum);
		die();
}

function sanitizePost($sanPost) {
	$string = $sanPost;
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
	$xml = simplexml_load_string();
	if (count(libxml_get_errors())!=0) {
		$liberror = libxml_get_last_error();
		$_SESSION['flashMessage'] = "You have invalid html tags. Post not saved<br>" . $liberror->message;\
		header('Location: forum.php');
		die();
	}

	
	return $sanPost;
}

function removeEscape($sanPost) {
	$sanPost = str_replace("\\", "", $sanPost);
	$sanPost = str_replace("<br>", "\r\n", $sanPost);
	preg_match_all('/<img[^>]+>/i', $sanPost, $imgsrcs);
	foreach ($imgsrcs[0] as $tempname) {
		$tempname = str_replace("<img src=\"", "", $tempname);
		$tempname = str_replace("<img src='", "", $tempname);
		$tempname = str_replace("\" />", "", $tempname);
		$tempname = str_replace("' />", "", $tempname);
		$image_info=getimagesize($tempname);

		$allowed_types=array(IMAGETYPE_PNG,IMAGETYPE_JPEG,IMAGETYPE_GIF);

		if(!in_array($image_info[2],$allowed_types)) {
			$sanPost = str_replace("<img src=\"" . $tempname . "\" />", "[redacted]", $sanPost);
			$sanPost = str_replace("<img src='" . $tempname . "' />", "[redacted]", $sanPost);
		}
	}
	return $sanPost;
}
?>