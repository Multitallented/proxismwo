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
	<h2>Tutorial</h2><br>
	<div class='center'>
		<iframe width="560" height="315" src="//www.youtube.com/embed/HXd1MB-cbNM" frameborder="0" allowfullscreen></iframe>
		<!--<iframe width="560" height="315" src="http://www.youtube.com/embed/-r4si4jb6-I" frameborder="0" allowfullscreen></iframe>-->
	</div>
	<p class='grey'>Proxis is a diverse planetary league that was designed to fill the void that is community warfare in Mechwarrior Online. It's goals are to provide a diverse set of units that fight
	in their own unique ways without forcing teams to fight matches that they will not enjoy.
	</p><br><br>
	<h3>Table of Contents</h3><br>
	<div style='margin-left: 20px;'>
		<strong>1. Pre-Registration</strong><br><br>
		<a style='margin-left: 20px;' href='#unit-types'>Unit Types</a><br><br>
		<a style='margin-left: 20px;' href='#unit-roster'>Unit Rosters</a><br><br>
		<strong>2. Setup, Buying, Beginnings, etc.</strong><br><br>
		<a style='margin-left: 20px;' href='#initial'>Initial Resources and Recommendations</a><br><br>
		<strong>3. Attack/Defend</strong><br><br>
		<a style='margin-left: 20px;' href='#attack'>Attacking</a><br><br>
		<a style='margin-left: 20px;' href='#defend'>Defending</a><br><br>
		<strong>4. Hiring Mercs</strong><br><br>
		<a style='margin-left: 20px;' href='#hiring'>Mercenary Contracts</a><br><br>
		<strong>5. Planet Conquest</strong><br><br>
		<a style='margin-left: 20px;' href='#dropship-movement'>Dropship Movement</a><br><br>
		<a style='margin-left: 20px;' href='#conquest'>Faction Conquest</a><br><br>
		<strong>6. Specific Stats and Numbers</strong><br><br>
		<a style='margin-left: 20px;' href='#stats'>Match Rewards</a><br><br>
		<strong>7. Recovery</strong><br><br>
		<a style='margin-left: 20px;' href='#wellfare'>Space Wellfare</a><br><br>
	</div>
	<div class='hr'></div><br>
	<h4 id='unit-types'>Unit Types</h4><br>
	<p class='grey'><strong>Factions (aka Houses)</strong> are the richest of the units. With an abundance of planets and cbills, factions are very short on dropships. To make up for this
	factions can use their excess cbills to garrison their planets and hire mercenaries. Because planets won by mercenaries go to their client faction, mercenaries should expect cbill donations before accepting contracts.
	Factions win by acquiring planets which in turn provide daily income on login. Factions are ranked by number of planets. If there is a tie then wins are counted.<br><br>

	Factions start with 1050M cbills, 7 planets, and 1 dropship24</p><br>

	<p class='grey'><strong>Clans</strong> are extremely powerful due to their wealth of cbills. However, their sense of power and honor is constantly against them. Clans gain planets, cbills,
	and kills through honorable combat and lose cbills in dishonorable combat. If a clan fights a unit that is significantly under powered (brings over 20 tons less), then the clan's winnings are drastically reduced to the
	point that they can lose cbills even if victorious. Clans have their own currency and as a result can't donate to non-clan units or hire mercenaries. They can however hire other clans as mercenaries. Clans are ranked by honorable kills
	(kills in an even tonnage match).<br><br>

	Clans start with 750M cbills, 4 planets, and 1 dropship52</p><br>

	<p class='grey'><strong>Mercenaries</strong> are unique in, but they start with average cbills. The reason they are unique
	is because they can sit in dropships from a distance of 1000 and take contracts without putting their dropships/planets at stake. This also means
	they are the fastest responders with the longest attack range out of any unit. If a mercenary unit has enough allies, they can
	take contracts and earn money faster than any other unit. Because they can use allied planet production, they don't need planets.<br><br>

	Mercenaries start with 350M cbills, 1 planet, and 2 dropship28s</p><br>

	<p class='grey'><strong>Pirates</strong> are dirt poor in cbills, but make up for it in stealth. Because pirates have second rate mechs, they raid planets for salvage as their primary source of cbills.
	Due to their sneaky nature, pirate planets are hidden on the map unless explored with a dropship 400 distance away and pirates can attack/defend at a distance. Pirates pay double cost in mech production
	which makes them rely on salvage for mechs. Pirates	can also hire mercenaries. Pirates are ranked by cbills.<br><br>

	Pirates start with 250M cbills, 2 planets, and 3 dropship16s</p><br>

	<p>All teams start on a safe planet. If the unit is a pirate or mercenary, then that safe planet will become a normal planet after 3 days.</p><br>

	<table>
	<th style='width: 100px;'></th><th style='width: 200px;'>Faction</th><th style='width: 200px;'>Mercenary</th><th style='width: 200px;'>Clan</th><th style='width: 200px;'>Pirate</th>
	<tr><td><b>Starting Cbills</b></td><td>1050M</td><td>300M</td><td>700M</td><td>250M</td></tr>
	<tr class='grey'><td><b>Starting Dropship Capacity</b></td><td>28 * 1</td><td>28 * 2</td><td>52 * 1</td><td>16 * 3</td></tr>
	<tr><td><b>Starting Planets</b></td><td>7</td><td>1</td><td>4</td><td>2</td></tr>
	<tr style='vertical-align: top;'><td><b>Quirks</b></td><td><span class='red'>Double dropship costs</span></td>
		<td><span class='green'>-Can be hired within 1000 of the contracted planet.<br>-Hiring unit gets 9M to 11M cbills.<br>-Cbill and salvage split evenly based on number of pilots hired.</span></td>
		<td><span class='green'>-All production on homeworlds<br><span class='red'>-Double cost production off homeworld.<br>
		-Can only donate/recieve cbills with other clans.<br>-Can hire other clans but not mercenaries.</span></td>
		<td><span class='green'>-Planets invisible to others without dropship within 400.<br>-Can attack up to 800 away.</span><br><span class='red'>-Double production costs.</span><br><span class='green'>-No dropship movement restrictions when not under attack.</span></td></tr>
	<tr class='grey'><td><b>Match Mech Salvage</b></td><td>25% mech salvage</td><td>25% mech salvage</td><td><span class='green'>If honorable, 50% mech salvage, 100% pilot salvage.</span><br>
		<span class='red'>If dishonorable, 100% pilot death.</span></td><td><span class='green'>100% mech salvage</span></td></tr>
	<tr><td><b>Extra Match CBill Salvage</b></td>
		<td>None</td>
		<td><span class='green'>+4M to 12M when not hired</span></td>
		<td><span class='green'>+1M to 10M when honorable</span><br><span class='red'>-50% - 2M to 6M (less than half) of all cbill salvage given to opponent if dishonorable</span></td>
		<td><span class='green'>+1M to 5M for each mech not killed if pirate won through base capture stolen from enemy</span></td></tr>
	<tr class='grey'><td><b>Leaderboard</b></td><td>Planets/Wins</td><td>Wins/Loses</td><td>Kills/Planets</td><td>CBills</td></tr>
	</table><br>

	<h4 id='unit-roster'>Unit Rosters</h4><br>
	<p class='grey'>To register you must have at least 8 people in your unit. This is a minimum, not a maximum. If you have more that 16 people
	then feel free to sign up a second unit if you want. If you sign up as a mercenary unit keep in mind that other factions will want
	to hire less that 8 pilots from you occasionally. This would mean that if your unit was hired to supply 3 pilots, you would send
	3 people from your unit to compete for that team.<br><br>

	Also it is worth noting that there are no rules on team changing during the league. Since teams can die off and enter the league
	at any point rosters should be flexible so long as your unit can still field 8 people. Do not abuse team changing or else your unit(s)
	could be flagged and penalized within reason.
	</p><br>
	
	<div class='hr'></div><br>

	<h4 id='initial'>Initial Resources and Recomendations</h4><br>

	<p class='grey'>Every unit starts with at least 1 dropship which you can find on the right side of your profile page. You can also find
	this dropship in orbit around your starting planet. First I recommend that you buy some mechs on your dropship using the dropship's
	mechlab. Don't worry if you mess up you can always ask an admin to help.
 	To move your dropship, go to the map, click on your dropship, then click on the planet you want to move it to. Dropships
	can only move once per day. Also keep in mind that you will need pilots and mechs to defend if you get attacked, so don't go too far.
	You have 4 days to respond to attacks, and if you can't defend you can always hire mercenaries. Also you can allow players access
	to your production by declaring them an ally on their profile. Keep in mind that they need to also declare you an ally for them to
	show up green on your map. Therefore, you should send a message to whoever you want to ally with because currently there is no notification
	that they will recieve when you ally them.<br><br>

	Also, it is worth mentioning that you do gain cbills for holding planets. This money is stored on planets as part of the planet's value
	and can be stolen when captured.</p><br>

	<p class='grey'>For Factions I recommend that you immediately start trying to claim Unowned planets to expand your borders and
	gain cbills. If you get attacked, then hire mercenaries to defend.</p><br>

	<p class='grey'>For Clans I recommend that you begin attacking people as soon as possible as this is how you gain kills. If the fights are
	fair then you will also gain a lot of cbills.</p><br>

	<p class='grey'>For Mercenaries I recommend that you make alliances and build your mechs and dropships until you get hired. Taking
	planets is detrimental and I only recommend it if you need the production. Your mech production should largely come from allied planets.</p><br>

	<p class='grey'>For Pirates I would start picking on a single faction or clan as soon as possible. Try to remain hidden using the fact
	that you have to be within 800 of the planet you're attacking. With any luck you will capture undefended planets and even pick up some good salvage.</p><br>

	<div class='hr'></div><br>

	<h4 id='attack'>Attacking</h4><br>

	<p class='grey'>To attack, click on a planet, select a player on that planet and click attack. If no mechs appear in the left
	column of the attack page, then you will have to move some dropships or hire mercenaries to attack. When attacking you need
	at least 8 pilots. Pilots and mechs are essentially the resources consumed during fights. Because you can't buy mechs in hostile
	territory, you will need to break your attack and retreat to allied territory to buy mechs. Once you declare an attack or defend
	your mechs and pilots will disappear, but will be returned at the end of the match (if they survive).<br><br>

	Keep in mind that once you commit mechs from a dropship to an attack, the dropship cannot move until that match is done.<br><br>

	You can make multiple attacks against the same person so long as you both have mechs available. If you wish to fight multiple matches, then
	try attacking a player often (on the same planet or others). Teams buy and move mechs all the time, and you could find a weakness.</p><br>

	<h4 id='defend'>Defending</h4><br>

	<p class='grey'>In the event that you are attacked, you will get a notification on your profile page and via email
	(your email is only visible to you and is completely optional).<br><br>

	If you do not respond to this attack within 4 days, you will automatically forfeit the match and lose 5 to 7 mechs.<br><br>

	If by the end of the match you have less than 12 mechs on a dropship or planet, the enemy will capture that dropship/planet and all mechs on it.<br><br>

	If you do not have any mechs in the left column of the defend page, you will need to move some dropships, buy some mechs, and/or
	hire some mercenaries to aid you. Keep in mind that you only have 4 days to do this.<br><br>

	Once you do respond to an attack, you have 7 days to submit a screenshot (see reporting matches). If you or your opponent fails to do this, both teams will
	lose 2 mechs at random. If a team fails to respond to a match, you may alert an admin. Your mechs will be returned to you. If a team consistently
	fails to schedule or respond to matches then they will be ejected from the league.</p><br>

	<div class='hr'></div><br>

	<h4 id='hiring'>Mercenary Contracts</h4><br>

	<p class='grey'>For employers to hire mercenaries they simply need to go attack a planet. In the attack page where you select what
	mechs you want to use, you can enter who you want to hire and how many pilots. If you are going to hire 12 mercenaries, then you don't
	need a dropship to be orbiting the planet you are attacking.<br><br>
	
	Mercenaries are just like other units in attacking and defending, except when they are hired. First of all, they do
	not need dropships on the planet they are contracting with, but rather can pool dropships from anywhere within 1000 of that planet.
	The dropships are not moved when accepting or completing a contract. Also, cbill salvage is distributed evenly based on the number of
	pilots hired, but mechs, dropships, and planets captured are not distributed. For this reason, it is highly recommended that mercenaries
	form their own fees and rates for contracts because employers can donate cbills as payment for services rendered. To help with this
	and to encourage hiring mercenaries, a player hiring any number of mercenary pilots will be awarded an extra 8-12M cbills<br><br>

	Do not accept contracts without hesitation. Even if you are only sending 1 pilot to fight, a loss for that fight will go on your
	permanent record (which is what is tracked on the leader board).<br><br>

	Also, it is possible for a unit on the other side of the galaxy to hire 12 mercenaries to attack from up to 1000 away. 
	I call this the merc blitz.</p><br>

	<div class='hr'></div><br>

	<h4 id='dropship-movement'>Dropship Movement</h4><br>

	<p class='grey'>Dropships can only move 500 per day. This cooldown starts when you move the dropship and lasts for 24 hours.
	Dropships can't move once they have committed mechs to an attack, but pirates and hired mercenaries
	can move their dropships so long as they aren't on the planet where the battle is taking place. All dropships orbiting planets
	you own can be seen by hovering your mouse over the planet. When navigating the map, you can zoom in and out using ctrl + and ctrl -</p><br>

	<h4 id='conquest'>Faction Conquest</h4><br>

	<p class='grey'>For factions, planets are the primary determinant on the leaderboard. However, due to the lack of dropships,
	factions start out already spread too thin. The primary way factions gain cbills is by claiming new worlds, which gives them even
	more territory to defend.<br><br>

	Any unit can claim Unowned planets with a dropship in orbit by paying the cbill value of the planet.</p><br>

	<div class='hr'></div><br>

	<h4 id='stats'>Match Stats</h4><br>

	<p class='grey'>Forfieting or failing to repond to an attack within 4 days will cause you to lose 5 to 7 mechs and 4-6M cbills. These mechs
	are taken from the planet first and the biggest (most mechs in bay) dropship next.<br><br>

	Matches are played in a best of 3 games. The final game is the one that is reported via screenshot to the report page.<br><br>

	If you do not report a match within 7 days of putting up a defense, both sides will lose 2 mechs at random. If sufficient proof
	is available that indicates you genuinely attempted to schedule a match with the opponent, then these mechs and pilots will be
	given back to you and your opponent will be warned (see rules on failure to schedule matches). Players can always extend the deadline to
	complete a match if both teams click the extension button on the match page.<br><br>

	If the attacker submits a report and the other side does not accept/disagree with the report within 1 day, then the report is accepted automatically.<br><br>

	If a mech gets killed in game, it has a 50% chance to be destroyed in your bay. If destroyed, the enemy has a 16% chance to salvage the
	mech. If the enemy is an honorable clan, they have a 50% chance to salvage the mech. Pirates automatically salvage destroyed mechs. For
	each destroyed mech, your opponent gains 25% of that mechs value plus 2.5M in cbills stolen from the opponent.<br><br>

	Mercenaries get an extra 4M - 12M cbills if they were not hired. Factions get 8M - 12M for hiring a mercenary. Pirates steal 1M - 5M 
	cbills for each mech they do not kill (base cap). Honorable clans get 1M - 10M bonus cbills and 1 - 5 bonus pilots. Dishonorable clans
	give half of their cbill salvage and 2M - 6M cbills on top of that to the enemy.</p><br>

	<div class='hr'></div><br>

	<h4 id='wellfare'>Space Wellfare</h4><br>

	<p class='grey'>A unit with assets and cbills totalling less than 125M becomes elligble for the space wellfare program. Units in this program
	will gain 15M cbills every day that they log in until they are no longer elligible for wellfare. When calculating asset value, the normal mech sell price
	(not the double cost for pirates and non-safe world clans rate) is used. Dropships and planets are not factored in.</p><br>
	
	<br>
	</div>
	<?php echo $footer; ?>
  </div>
</body>
</html>