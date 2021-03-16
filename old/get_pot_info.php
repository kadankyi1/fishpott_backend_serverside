<?php


	$query = "SELECT * FROM investor WHERE investor_id = '$p_investor_id'";   


$result = $mysqli->query($query);
		

if (mysqli_num_rows($result) != 0) {

$row = $result->fetch_array(MYSQLI_ASSOC);
$user_type = "investor";
$p_i_first_name = $row["first_name"];
$p_i_last_name = $row["last_name"];
$p_i_full_name = $p_i_first_name . " " . $p_i_last_name;
$p_i_dob = $row["dob"];
$p_p_i_phone = $row["phone"];
$p_i_email = $row["email"];
$p_db_investor_id = $row["investor_id"];
$p_db_i_password = $row["password"];
$p_i_sex = $row["sex"];
$p_i_status = $row["status"];
$p_i_country = $row["country"];
$p_i_net_worth = $row["net_worth"];
$p_i_coins_secure_datetime = $row["coins_secure_datetime"];
$p_i_profile_picture = $row["profile_picture"];
$p_i_pot_name = $row["pot_name"];
$p_i_verified_tag = $row["verified_tag"];


} else {
	//if(!isset($pot_name)) {
		include(ROOT_PATH . 'inc/auto_logout.php');
	//}

}

