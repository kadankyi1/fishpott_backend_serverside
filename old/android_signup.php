<?php
//$content = file_get_contents('php://input');

//$input = json_decode($content);

//var_dump($input); exit

//$firstName = $input->first_name; 


require_once("config.php");

 

//$firstName = $input->first_name;


if($_SERVER["REQUEST_METHOD"] == "POST") {
	include(ROOT_PATH . 'inc/db_connect.php');


$first_name = $_POST["firstname"];
$first_name = trim($first_name);

$last_name = $_POST["lastname"];
$last_name = trim($last_name);

$pot_name = $_POST["pottname"];
$pot_name = trim($pot_name);

$pot_name = strtolower($pot_name);

if($pot_name == ""){
	$signUpReturn["datareturned"][0]  = array(
	'status' => "no", 
	'user_id' => "na", 
	'user_type' => "",
	'key' => "", 
	'user_sys_id' => "", 
	'e_user_type' => "",  
	'error_set' => 1, 
	'error' => "Pottname cannot be empty"
	);
echo json_encode($signUpReturn); exit;

}
if(strlen($pot_name) < 5){
	$signUpReturn["datareturned"][0]  = array(
	'status' => "no", 
	'user_id' => "na", 
	'user_type' => "",
	'key' => "", 
	'user_sys_id' => "", 
	'e_user_type' => "",  
	'error_set' => 1, 
	'error' => "Pottname is less than 5 letters"
	);
echo json_encode($signUpReturn); exit;
}

if(trim($pot_name) == "mylinkups" || trim($pot_name) == "@mylinkups"){
	$signUpReturn["datareturned"][0]  = array(
	'status' => "no", 
	'user_id' => "na", 
	'user_type' => "",
	'key' => "", 
	'user_sys_id' => "", 
	'e_user_type' => "",  
	'error_set' => 1, 
	'error' => "Choose a different pottname"
	);
echo json_encode($signUpReturn); exit;
}


    if (ctype_alpha($pot_name)) {
        //echo "The string $testcase consists of all letters.\n";
    } else {
	$signUpReturn["datareturned"][0]  = array(
	'status' => "no", 
	'user_id' => "na", 
	'user_type' => "",
	'key' => "", 
	'user_sys_id' => "", 
	'e_user_type' => "",  
	'error_set' => 1, 
	'error' => "Pottname must contain only letters"
	);
echo json_encode($signUpReturn); exit;
    }


$dob = $_POST["dob"];
$dob = trim($dob);

$country = $_POST["country"];
$country = trim($country);

$password = $_POST["password"];
$password = trim($password);

$sex = $_POST["sex"];
$sex = trim($sex);

$user_or_e = trim($_POST["emailorphone"]);

if(isset($_POST["acc_type"])){

	if(trim($_POST["acc_type"]) == "BA"){

		$last_name = "";
		$sex = "business";
		$account_type = "business";

	} else if (trim($_POST["acc_type"]) == "PA" && $last_name == ""){

		$account_type = "investor";

		$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na",
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "Something Went Awry. You Should Complete All Fields"

				);
			echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;


	} else if (trim($_POST["acc_type"]) == "PA" && $last_name == "???"){

		$account_type = "investor";
		$last_name = "";
		/*
		$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na",
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "Something Went Awry. You Should Complete All Fields"

				);
			echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;
		*/

	}



} else {

	$account_type = "investor";

}


//echo "country is :  " .  $country;
//exit;

	$first_name = mysqli_real_escape_string($mysqli, $first_name);
	$last_name = mysqli_real_escape_string($mysqli, $last_name);
	$pot_name = mysqli_real_escape_string($mysqli, $pot_name);
	//$dob = mysqli_real_escape_string($mysqli, $dob);
	$country = mysqli_real_escape_string($mysqli, $country);


	if($country == "" || $first_name == "" || $pot_name == "" || $dob == "" || $password == "" || $sex == "" || $user_or_e == "" || $sex == "Choose Gender" || $country == "Choose Country"){

		$create_account = "no";
		$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na",
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "Something Went Awry. You Should Complete All Fields"

				);
			echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;


	}


	if (filter_var($user_or_e, FILTER_VALIDATE_EMAIL)) {

		$login_type = "email";

	} else {

		$login_type = "phone";

	}

	include(ROOT_PATH . 'inc/pw_fold.php');



	if($login_type == "phone"){

$phone = $_POST["emailorphone"];
$phone = trim($phone);

		$phone_length = strlen($phone);

		if($phone_length > 15) {
			

			$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na",
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na", 
				'error_set' => 1, 
				'error' => "Something Went Awry Because Of Your Phone Number"

				);
			echo json_encode($signUpReturn); exit;
		}
		//$phone = mysqli_real_escape_string($mysqli, $phone);
		$email = $pot_name . "@fishpot.com";
		$check = $phone;
		$column_name = "phone";

	} elseif ($login_type == "email") {

		$email = $_POST["emailorphone"];
		$email = trim($email);


		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {

				$signUpReturn["datareturned"][0]  = array(
					'status' => "no", 
					'user_id' => "na",
					'user_type' => "na",
					'key' => "na", 
					'user_sys_id' => "na", 
					'e_user_type' => "na", 
					'error_set' => 1, 
					'error' => "Something Went Awry Because Of Your Email"

					);
				echo json_encode($signUpReturn); exit;

			}
		//$email = mysqli_real_escape_string($mysqli, $email);
		$phone = $pot_name . "@fishpot.com";
		$check = $email;
		$column_name = "email";
	} else {

				$signUpReturn["datareturned"][0]  = array(
					'status' => "no", 
					'user_id' => "na",
					'user_type' => "na",
					'key' => "na", 
					'user_sys_id' => "na", 
					'e_user_type' => "na",
					'error_set' => 1, 
					'error' => "Something Went Awry. Not Sure If You Are Using A Phone Or Email"

					);
				echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;
	}


	if(isset($_POST["check_human"]) && $_POST["check_human"] == "wrong"){


			$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na", 
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "Something Went Awry. We Can't Tell Why"

				);
			echo json_encode($signUpReturn); exit;

	} else {


		$user_table = "investor";
		$user_type = "investor";

		include(ROOT_PATH . 'inc/check_user.php'); 
		if($create_account != "no"){


		} else {

			$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na",
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "We Already Have This User"

				);
			echo json_encode($signUpReturn,JSON_UNESCAPED_SLASHES); exit;

		}


		$user_table = "investor";
		$column_name = "pot_name";
		$check = $pot_name;
		include(ROOT_PATH . 'inc/check_user.php'); 
		if($create_account != "no"){


		} else {

			$signUpReturn["datareturned"][0]  = array(
				'status' => "no", 
				'user_id' => "na", 
				'user_type' => "na",
				'key' => "na", 
				'user_sys_id' => "na", 
				'e_user_type' => "na",
				'error_set' => 1, 
				'error' => "Pot Name Already In Use"

				);
			echo json_encode($signUpReturn); exit;

		}
		$investor_id = uniqid($check, TRUE);
		if($create_account != "no") { $create_account = "yes";}
		if($create_account == "yes"){

		$table_name = "investor";

		$column1_name = "first_name";
		$column2_name = "last_name";
		$column3_name = "pot_name";
		$column4_name = "dob";
		$column5_name = "country";
		$column6_name = "sex";
		$column7_name = "net_worth";
		$column8_name = "phone";
		$column9_name = "email";
		$column10_name = "investor_id";
		$column11_name = "coins_secure_datetime";
		$column12_name = "status";

		$column1_value = $first_name;
		$column2_value = $last_name;
		$column3_value = $pot_name;
		$column4_value = $dob;
		$column5_value = $country;
		$column6_value = $sex;
		$column7_value = 10;
		$column8_value = $phone;
		$column9_value = $email;
		$column10_value = $investor_id;
		$column11_value = date("Y-m-d H:i:s");
		$column12_value = "Hey, I'm out here fishing";

		$pam1 = "s";
		$pam2 = "s";
		$pam3 = "s";
		$pam4 = "s";
		$pam5 = "s";
		$pam6 = "s";
		$pam7 = "i";
		$pam8 = "s";
		$pam9 = "s";
		$pam10 = "s";
		$pam11 = "s";
		$pam12 = "s";

		$done = 0;
		include(ROOT_PATH . 'inc/insert12_prepared_statement.php');
		include(ROOT_PATH . 'inc/db_connect.php');

			if ($done == "1"){

					if($login_type == "phone"){

							$user_id = $phone;
					} else {

							$user_id = $email;

					}
					include(ROOT_PATH . 'inc/id_fold.php');

					//session_start();
					//$_SESSION["e_user"] = $e_user_id;
					//$_SESSION["user"] = $user_id;
					//$_SESSION["user_type"] = $e_user_type;
					//$_SESSION["user_sys_id"] = $investor_id;
					$this_full_name = $first_name . " " . $last_name;


						$table_name = "wuramu";

						$column1_name = "flag";
						$column2_name = "id";
						$column3_name = "number_login";
						$column4_name = "email_login";
						$column5_name = "password";
						$column6_name = "login_type";
						$column7_name = "full_name";

						$column1_value = 0;
						$column2_value = $investor_id;
						$column3_value = $phone;
						$column4_value = $email;
						$column5_value = $e_password;
						$column6_value = $account_type;
						$column7_value = $this_full_name;

						$pam1 = "i";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";
						$pam6 = "s";
						$pam7 = "s";

						$done = 0;
						include(ROOT_PATH . 'inc/insert7_prepared_statement.php');
						include(ROOT_PATH . 'inc/db_connect.php');


						if($done == 1) {

								$full_name = $first_name . " " . $last_name;

								$signUpReturn["datareturned"][0]  = array(
									'status' => "yes", 
									'user_id' => $e_user_id, 
									'user_type' => $account_type,
									'key' => $e_password, 
									'user_sys_id' => $investor_id, 
									'e_user_type' => $e_user_type,
									'i_full_name' => $full_name,
									'i_status' => "Hey, I'm out here fishing...",
									'investor_level' => "Baby Investor",
									'i_country' => $country,
									'i_phone' => $phone,
									'i_email' => $email,
									'i_net_worth' => 10,
									'i_profile_picture' => "",
									'i_pot_name' => $pot_name, 
									'i_verified_tag' => 0,  
									'error_set' => "0", 
									'error' => ""

									);
								echo json_encode($signUpReturn); exit;

							} else {

								$signUpReturn["datareturned"][0]  = array(
									'status' => "no", 
									'user_id' => "na",
									'user_type' => "na",
									'key' => "na", 
									'user_sys_id' => "na", 
									'e_user_type' => "na",
									'error_set' => 1, 
									'error' => "Something went Awry. It's Not Your Fault. Try Again later"

									);
								echo json_encode($signUpReturn); exit;

							}
				} else{

								$signUpReturn["datareturned"][0]  = array(
									'status' => "no", 
									'user_id' => "na", 
									'user_type' => "investor",
									'key' => $e_password, 
									'user_sys_id' => $investor_id, 
									'e_user_type' => $e_user_type,  
									'error_set' => 1, 
									'error' => "Something went Awry. You Should Try Again later"

									);
								echo json_encode($signUpReturn); exit;

				} 
			}	else {

								$signUpReturn["datareturned"][0]  = array(
									'status' => "no", 
									'user_id' => "na", 
									'user_type' => "investor",
									'key' => $e_password, 
									'user_sys_id' => $investor_id, 
									'e_user_type' => $e_user_type,  
									'error_set' => 1, 
									'error' => "Something went Awry. You Should Try Again later"
									);
								echo json_encode($signUpReturn); exit;

				}
		
	}


}

?>