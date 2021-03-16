<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/get_fold.php');
include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {

	if(isset($_POST["i_email"])) {

		$i_email = trim($_POST["i_email"]);

		$i_email = filter_var($i_email, FILTER_SANITIZE_EMAIL);

		if (!filter_var($i_email, FILTER_VALIDATE_EMAIL) === false) {

			$update = "yes";

		} else {

			$update = "no";
		}
	}

	if(isset($_POST["i_phone"])){
	
		$i_phone = trim($_POST["i_phone"]);

		if(!ctype_digit($i_phone)) {
			$update = "";
		}

		//settype($i_phone, "integer"); 
		//echo $i_phone; exit;

		$phone_length = strlen($i_phone);


		if($phone_length > 13) {
			
			$update = "";

		}

	}
	
	include(ROOT_PATH . 'inc/db_connect.php');


	if($update == "yes" && $status == 1) {
		
		$table_name = "investor";
		$column1_name = "phone";
		$column1_value = $i_phone;
		$column2_name = "email";
		$column2_value = $i_email;
		$row_check = "investor_id";
		$row_check_value = $investor_id;
		$opt = $_SESSION["change_email_phone"];

		include(ROOT_PATH . 'inc/update2_query.php');

		//echo $done; exit;
		if($done = 1) {
			include(ROOT_PATH . 'inc/id_fold.php');
			$_SESSION["e_user"] = $e_investor_id;
			$_SESSION["user"] = $investor_id;
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