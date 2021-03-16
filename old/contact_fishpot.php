<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/get_fold.php');

include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect.php');
include(ROOT_PATH . 'inc/get_user_info.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {


	$subject = $_POST["subject"];

	if(strlen($subject) > 20) {
		$send = "no";

	} else {

		$send = "yes";			

	}

	$message = $_POST["message"];


	if($message == "" || $subject == ""){
		$send = "no";			
	} else {

		$send = "yes";			

	}

	$opt = $_SESSION["contact_fishpot"];
	if(	$login_type == "phone") {

		$email_or_phone = $i_phone;
	} else {

		$email_or_phone = $i_email;

	}
	if($send != "no") {

		include(ROOT_PATH . 'inc/db_connect.php');
		$subject = mysqli_real_escape_string($mysqli, $subject);
		$message = mysqli_real_escape_string($mysqli, $message);

			$table_name = "contact";
			$column1_name = "user";
			$column2_name = "subject";
			$column3_name = "message";

			$column1_value = $email_or_phone;
			$column2_value = $subject;
			$column3_value = $message;
			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			if($done == 1) {
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

