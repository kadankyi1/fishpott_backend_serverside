<?php
//$content = file_get_contents('php://input');

//$input = json_decode($content);

require_once("config.php");


if($_SERVER["REQUEST_METHOD"] == "POST") {
	$android = 1;
	
	include(ROOT_PATH . 'inc/db_connect.php');

	$email_or_phone = $_POST["emailorphone"];
	$email_or_phone = trim($email_or_phone);

	$password = $_POST["password"];
	$password = trim($password);

	$email_or_phone = mysqli_real_escape_string($mysqli, $email_or_phone);

	$password = mysqli_real_escape_string($mysqli, $password);


	if (filter_var($email_or_phone, FILTER_VALIDATE_EMAIL)) {

		$login_type = "email";

	} else {

		$login_type = "phone";

	}

	$user_id = $email_or_phone;


	include(ROOT_PATH . 'inc/pw_fold.php');


	include(ROOT_PATH . 'inc/check_logger.php');
	$user_type = $db_user_type;
 
	if($login == "yes") {
		
		include(ROOT_PATH . 'inc/id_fold.php');
		session_start();

		$_SESSION["e_user"] = $e_user_id;
		$_SESSION["user"] = $user_id;
		$_SESSION["login_type"] = $e_login_type;
		$_SESSION["user_sys_id"] = $user_sys_id;
		$_SESSION["user_type"] = $e_user_type;

		include(ROOT_PATH . 'inc/db_connect.php');

		$query = "SELECT * FROM	investor WHERE investor_id = '$user_sys_id' ";
		//echo "user_sys_id : " . $user_sys_id; exit;

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {
			$row = $result->fetch_array(MYSQLI_ASSOC);
			$investor_level = intval($row["investing_points"]);
			if($investor_level == 0){

				$investor_level = "Baby Investor";

			} elseif($investor_level == 1){

				$investor_level = "Toddler Investor";

			} elseif($investor_level > 1 && $investor_level < 50){

				$investor_level = "Swift Investor";

			} elseif($investor_level >= 50 && $investor_level < 200){

				$investor_level = "Demi-god Investor";

			} elseif($investor_level >= 200){

				$investor_level = "god Investor";

			}

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
			$i_profile_picture = trim($row["profile_picture"]);
			if($i_profile_picture != ""){
				if (!file_exists("../pic_upload/" . $i_profile_picture)) {

						$i_profile_picture = "";
				} else {

					$i_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $i_profile_picture; 
				}
			} else {
				$i_profile_picture = "";
				
			}
			$i_pot_name = $row["pot_name"];
			$i_verified_tag = $row["verified_tag"];

			$query = "SELECT * FROM nsesa WHERE sku = 1";
			$result = $mysqli->query($query);
			if (mysqli_num_rows($result) != "0") {

				$row = $result->fetch_array(MYSQLI_ASSOC);
				$GHS_USD = $row["GHS_USD"];
				$USD_GHS = $row["USD_GHS"];
				$GHS_GBP = $row["GHS_GBP"];
				$GBP_GHS = $row["GBP_GHS"];
				$USD_GBP = $row["USD_GBP"];
				$GBP_USD = $row["GBP_USD"];
				$coins_GHS = $row["coins_GHS"];
				$coins_USD = $row["coins_USD"];
				$coins_GBP = $row["coins_GBP"];

			} else {

				$db_user_type = "";
			}

		if($db_user_type == "investor") {

		$login_data_time = date("Y-m-d H:i:s");
		$query = "UPDATE investor SET coins_secure_datetime = '$login_data_time' WHERE investor_id = '$user_sys_id'";
		$result = $mysqli->query($query);

			$signUpReturn["datareturned"][0]  = array(
				'status' => "yes", 
				'user_id' => $e_user_id, 
				'user_type' => "investor",
				'key' => $e_password, 
				'user_sys_id' => $user_sys_id, 
				'e_user_type' => $e_user_type,
				'i_full_name' => $i_full_name,
				'i_status' => $i_status,
				'i_country' => $i_country,
				'i_phone' => $i_phone,
				'i_email' => $i_email,
				'investor_level' => $investor_level,
				'i_net_worth' => $i_net_worth,
				'i_profile_picture' => $i_profile_picture,
				'i_pot_name' => $i_pot_name, 
				'i_verified_tag' => $i_verified_tag,
				'GHS_USD' => $GHS_USD,
				'USD_GHS' => $USD_GHS,
				'GHS_GBP' => $GHS_GBP,
				'GBP_GHS' => $GBP_GHS,
				'USD_GBP' => $USD_GBP,
				'GBP_USD' => $GBP_USD,
				'coins_GHS' => $coins_GHS,
				'coins_USD' => $coins_USD,
				'coins_GBP' => $coins_GBP,
				'error_set' => "0", 
				'error' => ""
 
				);
			echo json_encode($signUpReturn); exit;
			//$_SESSION["welcome"] = 1;
			//echo $investor_id; exit;


		} elseif ($db_user_type == "business") {
			
		$login_data_time = date("Y-m-d H:i:s");
		$query = "UPDATE investor SET coins_secure_datetime = '$login_data_time' WHERE investor_id = '$user_sys_id'";
		$result = $mysqli->query($query);

			$signUpReturn["datareturned"][0]  = array(
				'status' => "yes", 
				'user_id' => $e_user_id, 
				'user_type' => "business",
				'key' => $e_password, 
				'user_sys_id' => $user_sys_id, 
				'e_user_type' => $e_user_type,
				'i_full_name' => $i_full_name,
				'i_status' => $i_status,
				'i_country' => $i_country,
				'i_phone' => $i_phone,
				'i_email' => $i_email,
				'investor_level' => $investor_level,
				'i_net_worth' => $i_net_worth,
				'i_profile_picture' => $i_profile_picture,
				'i_pot_name' => $i_pot_name, 
				'i_verified_tag' => $i_verified_tag,
				'GHS_USD' => $GHS_USD,
				'USD_GHS' => $USD_GHS,
				'GHS_GBP' => $GHS_GBP,
				'GBP_GHS' => $GBP_GHS,
				'USD_GBP' => $USD_GBP,
				'GBP_USD' => $GBP_USD,
				'coins_GHS' => $coins_GHS,
				'coins_USD' => $coins_USD,
				'coins_GBP' => $coins_GBP,
				'error_set' => "0", 
				'error' => ""
 
				);
			echo json_encode($signUpReturn); exit;
			//$_SESSION["welcome"] = 1;
			//echo $investor_id; exit;

		} else {

		$signUpReturn["datareturned"][0]  = array(
			'status' => "no", 
			'user_id' => "na",
			'user_type' => "na", 
			'key' => "na", 
			'user_sys_id' => "na", 
			'e_user_type' => "na", 
			'error_set' => "1", 
			'error' => "Something went Awry. We Couldn't Verify Your Pott"

			);
		echo json_encode($signUpReturn); exit;
	   }

	  } else {

		$signUpReturn["datareturned"][0]  = array(
			'status' => "no", 
			'user_id' => "na",
			'user_type' => "na", 
			'key' => "na", 
			'user_sys_id' => "na", 
			'e_user_type' => "na", 
			'error_set' => "1", 
			'error' => "Something went Awry. We Couldn't Verify Your Pott"

			);
		echo json_encode($signUpReturn); exit;
	  }
	} else {

		$signUpReturn["datareturned"][0]  = array(
			'status' => "no", 
			'user_id' => "na",
			'user_type' => "na", 
			'key' => "na", 
			'user_sys_id' => "na", 
			'e_user_type' => "na", 
			'error_set' => "1", 
			'error' => "Something went Awry. We Couldn't Verify Your Pott"

			);
		echo json_encode($signUpReturn); exit;
		//echo "error occured"; exit;

	}
}