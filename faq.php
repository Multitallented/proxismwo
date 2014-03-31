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
	<div style='padding-left: 20px;'>
	<h2>Frequently Asked Questions</h2><br>
	<ul>
		<li>Q: Where can I donate?</li>
	</ul>
	<!-- ///////// DONATION FORM /////////////// -->
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
			">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		</form>
	<!-- ///////// END FORM /////////////////// -->
	<br><br>
	<ul>
		<li>Q: I forgot my password</li>
		<li style='color: white; margin-bottom: 0;'>Q: I want to change my unit_name</li>
		<li>Q: I entered the wrong url for my profile</li>
		<li>A: Contact <a href='http://mwomercs.com/forums/user/160085-multitallented/' target='_blank'>Multitallented</a> to get it changed.</li>
		<br>
		<li>Q: Is there a problem if I register 2 separate units?</li>
		<li>A: So long as you have 8 different people for each team, then there is no problem. However, you should play at least 1 match before you register a second unit.</li>
		<br>
		<li>Q: Can only 8 people play on my roster?</li>
		<li>A: You can sign up as many people as you want. 8 is just the minimum.</li>
		<br>
		<li>Q: How do I help?</li>
		<li>A: Get the word out! Tell people about the league. Also you can send feedback to <a href='http://mwomercs.com/forums/user/160085-multitallented/' target='_blank'>Multitallented</a></li>
		<br>
		<li>Q: Help I can't sign up!?!</li>
		<li>A: Please double check that you are entering all the fields correctly. Take note of any red error messages.
			If you are still having problems, ask <a href='http://mwomercs.com/forums/user/160085-multitallented/' target='_blank'>Multitallented</a> to create an account for you.
			Be prepared to show that you are unit leader with a roster of at least 8. 
			Keep in mind that you can't be in 2 units at the same time.</li>
		<br>
		<li>Q: I declared someone an ally, but they are still red on my map.</li>
		<li>A: Declaring someone an ally makes you green on their map. Get them to declare you an ally for them to show up green on your map.</li>
		<br>
		<li>Q: I want to attack a pirate, but I don't see any planets owned by that pirate.</li>
		<li>A: Pirate planets are hidden unless you have a dropship within 400 of the planet.</li>
		<br>
		<li>Q: My opponent hasn't responded to my attack/report and it is passed the deadline, but it doesn't show that he forfeited the match.</li>
		<li>A: Try logging out then logging back in. If that doesn't fix it, contact <a href='http://mwomercs.com/forums/user/160085-multitallented/' target='_blank'>Multitallented</a>.</li>
		<br>
		<li>Q: My opponent refuses to play a match despite having replied to my attack!</li>
		<li>A: It is within the rules to refuse to play/report a match. Both teams will recieve cbills (from a Tie game) at the end of 7 days along with their mechs and pilots</li>
		<br>
		<li>Q: I'm a merc and my client reported a match, but I can't see the final score screen?</li>
		<li>A: This is because the opponent hasn't confirmed the report. The score screen will be available upon confirmation. The report will be automatically confirmed if no response is made within 1 day.</li>

	</ul><br>
	</div>
	<?php echo $footer; ?>
  </div>
</body>
</html>