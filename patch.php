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
	<h2>Patches</h2><br>
	
	<h3>3/14/2013 - The Honor Patch</h3>
	
	<h4>Clans:</h4><br>
	<p class='grey'>Clan winnings will change to be less extreme. Currently a clan is either honorable or dishonorable. 
	Either way one side is being penalized even if both sides are taking the same tonnage. These changes are an
	attempt to increase the honor aspect of the clans while balancing their "clan tech" and resources. Clans still
	have the option to fight at same or higher tonnage, but should have to fight under-tonnage to get the large
	honorable income.</p><br>
	<ol>
		<li>Clans will now be honorable only if their 8 mechs are collectively 50 tons lighter than the opponent's.</li>
		<li>Clans will still be dishonorable if their 8 mechs are collectively 20 tons heavier than the opponent's.</li>
		<li>A new neutral zone between -50 and +20 tons is being created. This zone is neither honorable or dishonorable.</li>
	</ol><br>
	<p class='grey'>The payout rates are comparable to other unit salvage. Neutral zone payouts are as follows:</p><br>
	<ol>
		<li>Clans will recieve 1 to 2 pilots and 22M - 28M cbills for playing the match.</li>
		<li>Clans have a 50% chance to lose killed mechs and a 14% chance to lose pilots of those mechs.</li>
		<li>Clans have a 16% chance to salvage an enemy killed mech.</li>
		<li>Clans will also gain 25% of all cbill salvage from enemy killed mechs.</li>
		<li>Clans will still earn kills while in this neutral zone</li>
	</ol><br>
	<h4>New Features</h4><br>
	<p class='grey'>Players now have biographies on their profile page. This should help advertise your unit, co-ordinate scheduling, show contract fees for mercenaries, etc.
	It has limited html tags, namely underline, bold, italics, images, and links. Do not abuse this section or you will be penalized, suspended, or banned according to the severity.
	</p><br>
	<h4>Bugfixes</h4><br>
	<ol>
		<li>Fixed the dropship movement bug. Dropships can now no longer be moved to the planet they are currently on. - Thanks <a href='profile.php?u=topgun viper'>topgun viper</a></li>
		<li>Anonymous users can no longer claim Unowned planets making the owner blank</li>
		<li>If you try to attack a player twice, it will no longer try to send you to the not-yet-created pre-match page. - Thanks <a href='profile.php?u=jrgong'>jrgong</a></li>
		<li>Moving pilots to or from a dropship no longer triggers the dropship movement cooldown. - Thanks <a href='profile.php?u=jrgong'>jrgong</a></li>
		<li>Clicking on the mercenary textfield but not entering a name in the attack page no longer hires an unnamed mercenary. - Thanks <a href='profile.php?u=topgun viper'>topgun viper</a></li>
		<li>Fixed the dropship movement bug. Dropships can now no longer be moved to the planet they are currently on. - Thanks <a href='profile.php?u=topgun viper'>topgun viper</a></li>
		<li>Fixed a bug preventing pirate ships from attacking from a distance. - Thanks <a href='profile.php?u=topgun viper'>topgun viper</a></li>
	</ol>
	<br>
	</div>
	<?php echo $footer; ?>
  </div>
</body>
</html>