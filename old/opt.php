<?php
$o = array("add funds", "withdraw funds", "transfer shares", "cashout net worth",
			"change email_phone", "change profile picture", "change password", "contact fishPot", "newsfeed", "message", "notifications", "view user profile", "search");
$e_o = array("","","","","","","","", "", "", "", "", "");

$secret = 'g0dh6v36llth3p0w3r';

for ($i=0; $i < 13; $i++) { 

		if(isset($o)){

			$e_o[$i] = md5($secret . $o[$i]);

		}
}

$_SESSION["add_funds"] = $e_o["0"];
$_SESSION["withdraw_funds"] = $e_o["1"];
$_SESSION["transfer_shares"] = $e_o["2"];
$_SESSION["cash_out_net_worth"] = $e_o["3"];
$_SESSION["change_email_phone"] = $e_o["4"];
$_SESSION["change_profile_pic "] = $e_o["5"];
$_SESSION["change_password"] = $e_o["6"];
$_SESSION["contact_fishpot"] = $e_o["7"];
$_SESSION["newsfeed"] = $e_o["8"];
$_SESSION["purchased"] = $e_o["9"];
$_SESSION["notifications"] = $e_o["10"];
$_SESSION["user_profile"] = $e_o["11"];
$_SESSION["search"] = $e_o["12"];
?>