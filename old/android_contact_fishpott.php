<?php
if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['info']) && trim($_POST['info'])) {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $message = mysqli_real_escape_string($mysqli, $_POST['info']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $message = trim($message);

	$query = "SELECT first_name, last_name, phone, email, pot_name FROM investor WHERE investor_id = '$myid'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {
		$row = $result->fetch_array(MYSQLI_ASSOC);
		$first_name = trim($row["first_name"]);
		$last_name = trim($row["last_name"]);
		$phone = trim($row["phone"]);
		$email = trim($row["email"]);
		$pot_name = trim($row["pot_name"]);
		$full_name = $first_name . " " . $last_name;
	} else {

		$send = "no";
	}

    $investor_id = $myid;

	if($message == ""){
		$send = "no";			
	} else {

		$send = "yes";			

	}

	if($send != "no") {

		$subject = "Inquiry from " . $full_name . "(" . $phone . " / " . $email . ")";

		if(isset($_POST["report_news_id"]) && trim($_POST["report_news_id"]) != ""){

		$subject = "REPORT NEWS WITH ID " . $_POST["report_news_id"] . " From USER : "  . $full_name . " (" . $phone . " / " . $email . ") - PottName : " . $pot_name;

		}

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
			  echo "Received";
			} else {

				echo "Something went Awry";

			}


	}


}
