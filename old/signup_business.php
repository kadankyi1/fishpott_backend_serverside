<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/check_session.php');
include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect_autologout.php');
//echo "CHECK ON GOING 333"; exit;
if($_SERVER["REQUEST_METHOD"] == "POST") {
if($_POST["check_human"] == "0549937447"){

	$_POST["check_human"] = "";
}
	include(ROOT_PATH . 'inc/db_connect.php');
	$b_name = trim($_POST["b_name"]);
	$b_pot_name = trim($_POST["b_pot_name"]);
	$head_office_address = trim($_POST["head_office_address"]);
	$date_started = trim($_POST["date_started"]);

	$investor_id = $_SESSION["user_sys_id"];
	include(ROOT_PATH . 'inc/get_user_info.php');
	//$b_country = trim($_POST["country"]);

	$b_country = trim($i_country);
	if($b_country == "Ghana") {

		$currency = "GHS";

	} elseif($b_country == "United Kingdom"){

		$currency = "GBP";

	} else {

		$currency = "USD";

	}

	$create_account = "yes";
	if($b_country == ""){

		$create_account = "no";
	}

	$b_password = trim($_POST["password"]);

	if($b_password == ""){

		$create_account = "no";
	}

	//$b_login_type = trim($_POST["login_type"]);
	$user_or_e = trim($_POST["email_or_phone"]);
	if (filter_var($user_or_e, FILTER_VALIDATE_EMAIL)) {

		$b_login_type = "email";

	} else {

		$b_login_type = "phone";

	}
//echo "CHECK ON GOING 444"; exit;
	include(ROOT_PATH . 'inc/b_pw_fold.php');

	if($b_login_type == "phone"){

		$b_phone = trim($_POST["email_or_phone"]);

		$b_phone_length = strlen($b_phone);

		if($b_phone_length > 15) {
			
				$_POST["check_human"] = "wrong";
				$create_account = "no";

		}

		$b_email = $b_pot_name . "@fishpot.com";
		$check = $b_phone;
		$column_name = "bness_phone";
	} else {

		$b_email = trim($_POST["email_or_phone"]);

		if (filter_var($b_email, FILTER_VALIDATE_EMAIL) === false) {
				$_POST["check_human"] = "wrong";
				$create_account = "no";
			}

		$b_phone = $b_pot_name . "@fishpot.com";
		$check = $b_email;
		$column_name = "bness_email";
	}

	$b_name = mysqli_real_escape_string($mysqli, $b_name);
	$head_office_address = mysqli_real_escape_string($mysqli, $head_office_address);
	$b_country = mysqli_real_escape_string($mysqli, $b_country);
	$b_phone = mysqli_real_escape_string($mysqli, $b_phone);
	$b_email = mysqli_real_escape_string($mysqli, $b_email);

	//echo "b_name : " . $b_name . "<br>";
	//echo "head_office_address : " . $head_office_address . "<br>";
	//echo "country : " . $country . "<br>";
	//echo "phone : " . $phone . "<br>";
	//echo "email : " . $email . "<br>"; exit;

	if(isset($_POST["check_human"]) && $_POST["check_human"] == "wrong"){



	} else {


		$user_table = "adwuma";
		$b_user_type = "business";
		include(ROOT_PATH . 'inc/check_user.php'); 

		if($create_account == "no"){ 

			$error = "This Phone/Email Has Already Been Used";
			$_SESSION["error"] = $error;
		header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$old_e_u_type");
		
		} 


		if($create_account != "no"){

			$user_table = "investor";
			$column_name = "pot_name";
			$check = $b_pot_name;
			include(ROOT_PATH . 'inc/check_user.php');
			if($create_account == "no"){ 

				$error = "Pot Name Has Already Been Taken";
				$_SESSION["error"] = $error;
				header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$old_e_u_type");
			} 

		}


		$business_id = uniqid($check, TRUE);
		if($create_account != "no") { $create_account = "yes";}
/*
		echo "investor_id : " . $investor_id . "<br>";
		echo "business_id : " . $business_id . "<br>";
		echo "COMPANY NAME : " . $b_name . "<br>";
		echo "COMPANY ADDRESS : " . $head_office_address . "<br>";
		echo "COMPANY COUNTRY : " . $b_country . "<br>";
		echo "PASSWORD : " . $b_password . "<br>";
		echo "login_type : " . $b_login_type . "<br>";
		echo "column_name : " . $column_name . "<br>";
		echo "investor_id : " . $investor_id . "<br>";
		echo "b_pot_name : " . $b_pot_name . "<br>";
		echo "phone : " . $b_phone . "<br>";
		echo "email : " . $b_email . "<br>";
		echo "create account : " . $create_account . "<br>"; exit;
*/

		if($create_account == "yes"){

            $table_name = "adwuma";

            $column1_name = "investor_id";
            $column2_name = "bness_id";
            $column3_name = "bness_legal_name";
            $column4_name = "bness_pot_name";
            $column5_name = "bness_email";
            $column6_name = "bness_phone";
            $column7_name = "bness_addresss";
            $column8_name = "bness_country";

            $column1_value = $investor_id;
            $column2_value = $business_id;
            $column3_value = $b_name;
            $column4_value = $b_pot_name;
            $column5_value = $b_email;
            $column6_value = $b_phone;
            $column7_value = $head_office_address;
            $column8_value = $b_country;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "s";
			
			$done = 0;
            include(ROOT_PATH . 'inc/insert8_prepared_statement.php');
			include(ROOT_PATH . 'inc/db_connect.php');
			//echo "done is : " . $done; exit;
			if ($done == "1"){

            $table_name = "investor";

            $column1_name = "first_name";
            $column2_name = "pot_name";
            $column3_name = "dob";
            $column4_name = "phone";
            $column5_name = "email";
            $column6_name = "investor_id";
            $column7_name = "country";
            $column8_name = "net_worth";
            $column9_name = "coins_secure_datetime";
            $column10_name = "currency";

            $column1_value = $b_name;
            $column2_value = $b_pot_name;
            $column3_value = $date_started;
            $column4_value = $b_phone . "_" . $b_pot_name;
            $column5_value = $b_email . "_" . $b_pot_name;
            $column6_value = $business_id;
            $column7_value = $b_country;
            $column8_value = 20;
            $column9_value = date("Y-m-d H:i:s");
            $column10_value = $currency;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "i";
            $pam9 = "s";
            $pam10 = "s";
			
			$done = 0;


            include(ROOT_PATH . 'inc/insert9_prepared_statement.php');
			include(ROOT_PATH . 'inc/db_connect.php');
						if ($done == "1"){

									if($b_login_type == "phone"){

											$b_user_id = $b_phone;
									} else {

											$b_user_id = $b_email;

									}
								include(ROOT_PATH . 'inc/b_id_fold.php');



						$table_name = "wuramu";

						$column1_name = "flag";
						$column2_name = "id";
						$column3_name = "number_login";
						$column4_name = "email_login";
						$column5_name = "password";
						$column6_name = "login_type";
						$column7_name = "full_name";

						$column1_value = 0;
						$column2_value = $business_id;
						$column3_value = $b_phone;
						$column4_value = $b_email;
						$column5_value = $b_e_password;
						$column6_value = "business";
						$column7_value = $b_name;

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

								$_SESSION["b_e_user"] = $b_e_user_id;
								$_SESSION["b_user"] = $b_user_id;
								$_SESSION["b_user_type"] = $b_e_user_type;
								$_SESSION["b_user_sys_id"] = $business_id;
								$_SESSION["b_login_type"] = $b_login_type;
								$_SESSION["type"] = "business";
								header("Location: ../b_pic_upload/index.php?fold=$b_e_user_id&login=$b_e_login_type&u_type=$b_e_user_type");

							} else {
								$error = "Something Went Awry1";
								$_SESSION["error"] = $error;
								header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$e_b_user_type");

							}

						} else {
								$error = "Something Went Awry2";
								$_SESSION["error"] = $error;
								header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$old_e_u_type");



						}
				} else{
								$error = "Something Went Awry3";
								$_SESSION["error"] = $error;
								header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$old_e_u_type");


				} 
			}	else {
								$error = "Something Went Awry4";
								$_SESSION["error"] = $error;
								header("Location: ../business_signup/index.php?fold=$e_user_id&login=$e_login_type&u_type=$old_e_u_type");
				}
		
	}


} 