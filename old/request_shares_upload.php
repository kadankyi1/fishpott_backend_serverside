<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/get_fold.php');

include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect.php');
include(ROOT_PATH . 'inc/get_user_info.php');

if($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["check_human"] == "" && $_POST["shares_type"] != "") {


	$shares_type = $_POST["shares_type"];
	$company_country = $_POST["settle_country"];
	$registration_number = $_POST["subject"];

	if(strlen($registration_number) > 30) {

		$send = "no";

	} else {

		$send = "yes";			

	}

	$message = $_POST["message"];


	if($message == "" || $registration_number == ""){
		$send = "no";			
	} else {

		$send = "yes";			

	}

	$opt = $_SESSION["cash_out_net_worth"];

	if($login_type == "phone") {

		$email_or_phone = $i_phone;
	} else {

		$email_or_phone = $i_email;

	}
	if($send != "no") {


		include(ROOT_PATH . 'inc/db_connect.php');
		$registration_number = mysqli_real_escape_string($mysqli, $registration_number);
		$message = mysqli_real_escape_string($mysqli, $message);
		$shares_type = mysqli_real_escape_string($mysqli, $shares_type);
		$company_country = mysqli_real_escape_string($mysqli, $company_country);

//echo "shares_type : " . $shares_type . "<br>";
//echo "company_country : " . $company_country . "<br>";
//echo "registration_number : " . $registration_number . "<br>";
//echo "message : NEW " . $message . "<br>"; exit;


		$message = "SHARES UPLOAD REQUEST.  Company Registration Number : " . $registration_number . "  Company Country :  " . $company_country . "   Shares Type Requested  :  " . $shares_type . "    Reason For Upload  :  "  . $message;

			$table_name = "contact";
			$column1_name = "user";
			$column2_name = "subject";
			$column3_name = "message";

			$column1_value = $email_or_phone;
			$column2_value = "SHARES UPLOAD REQUEST";
			$column3_value = $message;
			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			if($done == 1) {

  $email_body = "SHARES UPLOAD REQUEST \n";
  $email_body = $email_body . "Company Name: " . $i_full_name . "\n";
  $email_body = $email_body . "Company Registration Number : " . $registration_number . "\n";
  $email_body = $email_body . "Company Contact: " . $email_or_phone . "\n";
  $email_body = $email_body . "Reason Why We Should Upload  : " . $message;
  $headers = "From: FISHPOTT NETWORK";
  mail("fishpottcompany@gmail.com","SHARES UPLOAD REQUEST (FISHPOTT NETWORK)",$email_body,  $headers);


				$_SESSION["err"] = 1;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");		


			} else {
				$_SESSION["err"] = 0;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");		
			
			}


	} else {

		$_SESSION["err"] = 0;
		header("Location: ../user/index.php?fold=$e_investor_id&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");		
	}

}

