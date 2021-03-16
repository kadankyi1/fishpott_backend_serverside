<?php

require_once("config.php");


if($_SERVER["REQUEST_METHOD"] == "POST") {
	include(ROOT_PATH . 'inc/db_connect.php');
	$first_name = trim($_POST["first_name"]);
	$last_name = trim($_POST["last_name"]);
	$pot_name = trim($_POST["pot_name"]);
	$dob = trim($_POST["dob"]);
	$country = trim($_POST["country"]);
	if($country == "Ghana") {

		$currency = "GHS";

	} elseif($country == "United Kingdom"){

		$currency = "GBP";

	} else {

		$currency = "USD";

	}

	//$first_name = mysqli_real_escape_string($mysqli, $first_name);
	//$last_name = mysqli_real_escape_string($mysqli, $last_name);
	//$pot_name = mysqli_real_escape_string($mysqli, $pot_name);
	//$dob = mysqli_real_escape_string($mysqli, $dob);
	//$country = mysqli_real_escape_string($mysqli, $country);

	if($country == ""){

		$create_account = "no";
	}

	$password = trim($_POST["password"]);
	//$password = mysqli_real_escape_string($mysqli, $password);
	$user_or_e = trim($_POST["email_or_phone"]);
	if (filter_var($user_or_e, FILTER_VALIDATE_EMAIL)) {

		$login_type = "email";

	} else {

		$login_type = "phone";

	}
	//$login_type = trim($_POST["login_type"]);
	//$login_type = mysqli_real_escape_string($mysqli, $login_type);
	include(ROOT_PATH . 'inc/pw_fold.php');
	$sex = trim($_POST["sex"]);

	if($login_type == "phone"){

		$phone = trim($_POST["email_or_phone"]);

		$phone_length = strlen($phone);

		if($phone_length > 15) {
			
				$_POST["check_human"] = "wrong";

		}
		//$phone = mysqli_real_escape_string($mysqli, $phone);
		$email = $pot_name . "@fishpot.com";
		$check = $phone;
		$column_name = "phone";

	} else {

		$email = trim($_POST["email_or_phone"]);

		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				$_POST["check_human"] = "wrong";
			}
		//$email = mysqli_real_escape_string($mysqli, $email);
		$phone = $pot_name . "@fishpot.com";
		$check = $email;
		$column_name = "email";
	}


	if(isset($_POST["check_human"]) && $_POST["check_human"] == "wrong"){


		header("Location: ../index.php?error=err");

	} else {


		$user_table = "investor";
		$user_type = "investor";
		include(ROOT_PATH . 'inc/check_user.php'); 
		$investor_id = uniqid($check, TRUE);

		if($create_account != "no") { $create_account = "yes";}
		if($create_account == "yes"){
			$t_date = date("Y-m-d H:i:s");

			include(ROOT_PATH . 'inc/add_investor.php'); 
		//echo "create_account 2: " . $create_account;
		//echo "done : " . $done; exit;
			if ($done == "1"){

					if($login_type == "phone"){

							$user_id = $phone;
					} else {

							$user_id = $email;

					}
					include(ROOT_PATH . 'inc/id_fold.php');


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
						$column6_value = "investor";
						$column7_value = $first_name . " " . $last_name;

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
								session_start();
								$_SESSION["e_user"] = $e_user_id;
								$_SESSION["user"] = $user_id;
								$_SESSION["user_type"] = $e_user_type;
								$_SESSION["user_sys_id"] = $investor_id;
								$_SESSION["e_user_type"] = $investor_id;
								header("Location: ../pic_upload/index.php?fold=$e_user_id&login=$e_login_type&u_type=$e_user_type");
								$_SESSION["welcome"] = 1;
							} else {

								header("Location: ../index.php?error=error4");
							}
				} else{

					header("Location: ../index.php?error=error2");

				} 
			}	else {
					header("Location: ../index.php?error=error4");		
				}
		
	}


} 