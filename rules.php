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
	<h2>Rules</h2><br>

	<h4 id="conduct">Conduct</h4><br>
	<ol>
		<li>Teams found to be colluding with each other to exploit the system will be punished,
		 suspended, or ejected depending on the severity.</li>
		<li>Teams who purposely ignore pending matches can be flagged. If the team continues to be absent,
		 then they will be ejected from the league.</li>
	</ol><br>

	<h4 id="signup">Signups</h4><br>
	<ol>
		<li>You must have a roster of at least 8 people</li>
		<li>You must be able to play during 11:00am - 9:00pm PST. If enough EU/Asian teams want to do this, then 
		 I'll look into making a separate league.</li>
              <li>Your unit must not be led by the same person/people of an existing unit in Proxis. Alliances are fine.
		 If you are found to be violating this rule, both units are subject to punishment/removal.</li>
		<li>No individual person can register in 2 separate units (but can switch teams without notice at any time).
		 Admins do not need to know complete rosters or when people transfer to other teams.</li>
		<li><a class='bttn' href="register.php">Register</a></li>
	</ol><br>
	<h4 id="procedure">Match Procedure</h4><br>
	<ol>
		<li>When a unit attacks another unit, the defender has 4 days to respond before forfeiting a planet or cbills</li>
		<li>Once a defender has responded to an attack, the teams have 7 days to schedule, fight, and report the match</li>
		<li>To help with sync dropping, if both teams agree, the match may be played in conquest mode. If an agreement can't be made then the match must be played in assault mode</li>
		<li>Please screenshot/video record the score screen as you will need it when you report the match</li>
		<li>Players may intentionally refuse to play a match by not reporting a screenshot. Both teams will get their pilots and mechs back at the end of the 7 days along with the normal cbill losses for a tie</li>
		<li style='color: red;'>If a player refuses to play a match after declaring a defense, then the opponent may escalate the issue to an <a href='search.php?search=admin'>admin</a>. If sufficient evidence
			is provided showing the other unit is un-responsive or purposefully not scheduling a match, then the offending player will be flagged. Players will
			only be flagged if they have not responded to any communication for scheduling, if they have not proposed any dates for the match, 
			or if they have consistently failed to schedule previous matches.</li>
		<li class='red'>If a player is flagged for failing to schedule a match, their opponent will get a refund of any lost mechs/pilots,
			they may be penalized in pilots/mechs, and they may be ejected from the league depending on how many matches they have purposely
			failed to schedule.</li>
		<li>A match consists of a best of 3 games. The final game is the one that is submitted via screenshot on the report page</li>
		<li>Once a team has reported a match, the other team has 1 day to accept or deny that report</li>
		<li>If a team has not accepted or denied a report, then the report will automatically be accepted</li>
		<li>If a report is denied, an admin will resolve the report by arbitration (admin decisions are final)</li>
		<li>After a report has been completed, both teams will get back their mechs and pilots that survived, plus any salvage/bondsmen</li>
		<li>Also at this time, the planet will be awarded to winner (or the winner's client for mercs)</li>
		<li>If the game is a tie, then the planet stays with the defender</li>
	</ol><br>

	<h4 id="matches">Match Rules</h4><br>
	<ol>
		<li>Evidence of a disqualified match must be recorded by screenshot in case the report needs admin intervention</li>
		<li>If a player quits or disconnects before the first 20 seconds after the countdown reaches 0, redo the match</li>
		<li>Disconnecting to avoid playing on a specific map will result in a loss</li>
		<li>Removing map files from your game will get you banned from the league</li>
		<li>Altering configs and tampering with files to give you an unfair advantage (ie removing trees, etc) will get you banned from the league</li>
		<li>If a team does not use their declared drop deck (as specified on the match page), redo the match. If they fail to do this until after all matches are played then they forfiet the match 12 - 0.</li>
		<li>If a team does not follow the planet specific restrictions (as specified on the match page), redo the match</li>
		<li>All matches are Assault Mode unless the match details say otherwise. If both teams agree, the match can be played in Conquest mode.</li>
	</ol><br>

	<h4 id="patches">Patches</h4><br>
	<ol>
		<li>Planet production is subject to change as new mechs are introduced into the game.</li>
		<li>As new players join the league, new planets will be added to the map.</li>
		<li>Players are free to quit the league at any time. This may cause planets to be deleted. Any affected players are subject to compensation to be determined
			by an admin.</li>
		<li>Cbill rewards, production costs, etc. are subject to change for balance purposes. These will be announced on
		a new patch page. Balance changes will be rare during the season. I'll try to hold off until the next season.</li>
	</ol><br>
	</div>
	<?php echo $footer; ?>
  </div>
</body>
</html>