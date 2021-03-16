<?php


	if($login_type == "phone") {

			$email_or_phone = mysqli_real_escape_string($mysqli, $email_or_phone);

			$table_name = "wuramu"; 

			$column1_name = "number_login"; 
			$column1_value = $email_or_phone;


			$item_1 = "number_login";
			$item_2 = "email_login";
			$item_3 = "password";
			$item_4 = "login_type";
			$item_5 = "id";
			$item_6 = "flag";
			$pam1 = "s";



			include(ROOT_PATH . 'inc/select6_where1_prepared_statement.php');
			//echo "done : " . $done; exit;
			if($done== 0 || $item_1 == "number_login" || $item_2 == "email_login" || $item_3 == "password" || $item_4 == "login_type" || $item_5 == "id") {


						$login = "no";


				} else {

					$db_phone = $item_1;
					$db_password = $item_3;
					$db_user_type = $item_4;
					if($email_or_phone == $db_phone && $e_password == $db_password){

						$login = "yes";


						$user_sys_id = $item_5;

					} else {

						$login = "no";
					}

				}

	} else {

			$email_or_phone = mysqli_real_escape_string($mysqli, $email_or_phone);

			$table_name = "wuramu"; 

			$column1_name = "email_login"; 
			$column1_value = $email_or_phone;


			$item_1 = "number_login";
			$item_2 = "email_login";
			$item_3 = "password";
			$item_4 = "login_type";
			$item_5 = "id";
			$item_6 = "flag";
			$pam1 = "s";



			include(ROOT_PATH . 'inc/select6_where1_prepared_statement.php');
			if($done== 0 || $item_1 == "number_login" || $item_2 == "email_login" || $item_3 == "password" || $item_4 == "login_type" || $item_5 == "id") {


						$login = "no";


				} else {

					$db_email = $item_2;
					$db_password = $item_3;
					$db_user_type = $item_4;

					if($email_or_phone == $db_email && $e_password == $db_password){

						$login = "yes";


						$user_sys_id = $item_5;
						//echo "user_sys_id : " . $user_sys_id; exit;
					} else {

						$login = "no";
					}

				}


	}

