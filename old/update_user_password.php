<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/get_fold.php');

include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect.php');
include(ROOT_PATH . 'inc/get_user_info.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {


	$i_o_password = $_POST["i_o_password"];


	$i_n_password = $_POST["i_n_password"];
	$i_n_r_password = $_POST["i_n_r_password"];

	if($i_o_password == "" || $i_n_password = "" || $i_n_r_password = ""){
		$update = "no";			
	}

	$opt = $_SESSION["change_password"];

	if($i_n_password != $i_n_r_password) {

		$update = "no";		
	} else {

		$update = "yes";		

	}

	if($update != "no") {

	if(	$login_type == "phone") {

		$email_or_phone = $i_phone;
	} else {

		$email_or_phone = $i_email;

	}

		$password = $i_o_password;

		include(ROOT_PATH . 'inc/pw_fold.php');

		include(ROOT_PATH . 'inc/check_logger.php');

		if($login == "yes") {

			$table_name = "investor";
			$column1_name = "password";
			$password = $i_n_password;

			include(ROOT_PATH . 'inc/pw_fold.php');
			$column1_value = $e_password;
			$row_check = "investor_id";
			$row_check_value = $investor_id;
			$pam1 = "s";
			$pam2 = "s";
			include(ROOT_PATH . 'inc/update1_prepared_statement.php');
		//echo $done; exit;

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

	} else {

		$_SESSION["err"] = 0;
		header("Location: ../user/index.php?fold=$e_investor_id&e_o=$opt&login=$old_e_login&u_type=$old_e_u_type");		
	}

}

