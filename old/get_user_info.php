<?php

if(isset($pot_name) && $pot_name != "") {

	$query = "SELECT * FROM investor WHERE pot_name = '$pot_name'";   

} else {

	$query = "SELECT * FROM investor WHERE investor_id = '$investor_id'";   

}

$result = $mysqli->query($query);
		

if (mysqli_num_rows($result) != 0) {

$row = $result->fetch_array(MYSQLI_ASSOC);
$user_type = "investor";
$i_first_name = $row["first_name"];
$i_last_name = $row["last_name"];
$i_full_name = $i_first_name . " " . $i_last_name;
$i_dob = $row["dob"];
$i_phone = $row["phone"];
$i_email = $row["email"];
$db_investor_id = $row["investor_id"];
$db_i_password = $row["password"];
$i_sex = $row["sex"];
$i_status = $row["status"];
$i_country = $row["country"];
$i_net_worth = $row["net_worth"];
$i_coins_secure_datetime = $row["coins_secure_datetime"];
$i_profile_picture = $row["profile_picture"];
$i_pot_name = $row["pot_name"];
$i_verified_tag = $row["verified_tag"];


} else {
	$no_info = 1;
	if(!isset($pot_name)) {
		include(ROOT_PATH . 'inc/auto_logout.php');
	}

}

