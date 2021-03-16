<?php
if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['news_type']) && trim($_POST['news_type']) != "" && isset($_POST['news_id']) && trim($_POST['news_id']) != "" && isset($_POST['report_txt']) && trim($_POST['report_txt']) != "") {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $news_type = mysqli_real_escape_string($mysqli, $_POST['news_type']);
    $news_id = mysqli_real_escape_string($mysqli, $_POST['news_id']);
    $message = mysqli_real_escape_string($mysqli, $_POST['report_txt']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $message = trim($message);

	$query = "SELECT first_name, last_name, phone, email FROM investor WHERE investor_id = '$myid'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$first_name = trim($row["first_name"]);
		$last_name = trim($row["last_name"]);
		$phone = trim($row["phone"]);
		$email = trim($row["email"]);
		$full_name = $first_name . " " . $last_name;
	} else {

		$send = "no";
	}

    $investor_id = $myid;

	if($message == ""){
		$send = "no";			
	} else {
		$message = "( " . $news_id  . ")" . $message;
		$send = "yes";			

	}

	if($send != "no") {

		$subject = "Report " . $news_type . " ( " . $news_id . " )  from " . $full_name . "(" . $phone . " / " . $email . ")";

		include(ROOT_PATH . 'inc/db_connect.php');

			$table_name = "contact";
			$column1_name = "user";
			$column2_name = "subject";
			$column3_name = "message";

			$column1_value = $full_name . "(" . $phone . " / " . $email . ")";
			$column2_value = $subject;
			$column3_value = $message;
			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			include(ROOT_PATH . 'inc/insert3_prepared_statement.php');

			if($done == 1) {

			  $headers = "From: <info@fishpott.com>FishPott App";
			  mail("info@fishpott.com",$subject,$message,  $headers);
			  echo "Inquiry sent";
			} else {

				echo "Something went Awry";

			}


	}


}
