<?php
session_start();

require_once("config.php");
if($_SERVER["REQUEST_METHOD"] == "POST") {
	include(ROOT_PATH . 'inc/db_connect.php');
	$login_type = trim($_POST["login_type"]);
	$email_or_phone = trim($_POST["email_or_phone"]);
	$email_or_phone = mysqli_real_escape_string($mysqli,$email_or_phone);

	$table_name = "wuramu"; 

	if($login_type == "phone"){

		$column1_name = "number_login"; 

	} else {

		$column1_name = "email_login"; 

	}

	$column1_value = $email_or_phone;

	$item_1 = "number_login";
	$item_2 = "email_login";
	$item_3 = "password";
	$item_4 = "login_type";
	$item_5 = "id";
	$item_6 = "flag";
	$pam1 = "s";


	include(ROOT_PATH . 'inc/select6_where1_prepared_statement.php');
	//echo "item_1 : " . $item_1 . "<br>";
	//echo "done : " . $done; exit;
	if($item_1 == "number_login" || $item_1 == "") {

		$_SESSION["chg"] = 0;
		if($item_6 == 1) {

			$_SESSION["i"] = "Your Account Has Been Suspended. Contact FishPott To Clarify Some Issues";
			$_SESSION["t"] = 1;

		} else {

			$_SESSION["i"] = "We Could Not Find Your Pott";
			$_SESSION["t"] = 1;

		}
		header("Location: ../forgot.php");
	} else {
		include(ROOT_PATH . 'inc/db_connect.php');
		$table_name = "sesa_pass_link";
		$reset_code = uniqid($email_or_phone, TRUE);
		$column1_name = "investor_id";
		$column2_name = "change_type";
		$column3_name = "sesa_datetime";
		$column4_name = "sesa_code";
		$column5_name = "sesa_flag";

		$column1_value = $item_5;
		$column2_value = $login_type;
		$column3_value = date("Y-m-d H:i:s");
		$column4_value = $reset_code;
		$column5_value = 0;

		$pam1 = "s";
		$pam2 = "s";
		$pam3 = "s";
		$pam4 = "s";
		$pam5 = "i";
		$done = 0;
		include(ROOT_PATH . 'inc/insert5_prepared_statement.php');

		if($done == 1) {

			$_SESSION["t"] = 0;
			$_SESSION["chg"] = 1;
			$_SESSION["eni"] = $reset_code;
			header("Location: ../forgot.php?xi=$reset_code");

		} else {

			$_SESSION["i"] = "We Could Not Find Your Pott";
			$_SESSION["t"] = 1;

			header("Location: ../forgot.php");

		}

	}
} elseif (isset($_GET["xi"]) && $_GET["xi"] != "") {

	$table_name = "sesa_pass_link"; 

	$column1_name = "sesa_code"; 
	$column1_value = $_GET["xi"];

	$item_1 = "sesa_flag";
	$item_2 = "sesa_datetime";

	$pam1 = "s";


	include(ROOT_PATH . 'inc/select2_where1_prepared_statement.php');

	if($item_1 == "sesa_flag" || $item_1 == "") {

			$_SESSION["i"] = "Reset link has expired";
			$_SESSION["t"] = 1;

	}

}

