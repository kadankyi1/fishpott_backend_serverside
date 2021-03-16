<?php

require_once("config.php");


if($_SERVER["REQUEST_METHOD"] == "POST") {

	$email_or_phone = trim($_POST["email_or_phone"]);
	$user_id = $email_or_phone;
	//$login_type = trim($_POST["login_type"]);

// Variable to check

// Validate email
if (filter_var($user_id, FILTER_VALIDATE_EMAIL)) {

	$login_type = "email";

} else {

	$login_type = "phone";

}
if($_POST["check_human"] == "0549937447" || $_POST["check_human"] == "0207393447"){

	$_POST["check_human"] = "";

}
$password = trim($_POST["password"]);

//echo "check_human : " . $_POST["check_human"] . "<br>";
//echo "email_or_phone : " . $_POST["email_or_phone"] . "<br>";
//echo "password : " . $_POST["password"] . "<br>";
//echo "email_or_phone : " . $login_type; exit;

	if($_POST["check_human"] != ""){

		header("Location: ../index.php?error=err");
		exit;

	}

	include(ROOT_PATH . 'inc/pw_fold.php');


	include(ROOT_PATH . 'inc/db_connect.php');

	include(ROOT_PATH . 'inc/check_logger.php');
	$user_type = $db_user_type;
	if($login == "yes") {
		
		include(ROOT_PATH . 'inc/id_fold.php');
		session_start();

		$table_name = "investor";

		$column1_name = "coins_secure_datetime";
		$column1_value = date("Y-m-d H:i:s");

		$row_check = "investor_id";
		$row_check_value = $user_sys_id;

		$pam1 = "s";
		$pam2 = "s";
		include(ROOT_PATH . 'inc/db_connect.php');
		include(ROOT_PATH . 'inc/update1_prepared_statement.php');



		$_SESSION["e_user"] = $e_user_id;
		$_SESSION["user"] = $user_id;
		$_SESSION["login_type"] = $e_login_type;
		$_SESSION["user_sys_id"] = $user_sys_id;
		$_SESSION["user_type"] = $e_user_type;
	
		if($db_user_type == "investor") {

			header("Location: ../user/index.php?fold=$e_user_id&login=$e_login_type&u_type=$e_user_type");
			$_SESSION["welcome"] = 1;

		} elseif ($db_user_type == "business") {
			//echo "string"; exit;
		$_SESSION["b_straight"] = 1;

			header("Location: ../user/index.php?fold=$e_user_id&login=$e_login_type&u_type=$e_user_type");
			$_SESSION["welcome"] = 1;
		}

	} else {

		header("Location: ../index.php?error=invalid login");

	}
}