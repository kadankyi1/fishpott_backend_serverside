<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/get_fold.php');

include(ROOT_PATH . 'inc/set_check_login_type.php');

include(ROOT_PATH . 'inc/id_unfold.php');
include(ROOT_PATH . 'inc/db_connect.php');
include(ROOT_PATH . 'inc/get_user_info.php');

if($_SERVER["REQUEST_METHOD"] == "POST") {

	$settle_type = $_POST["settle_type"];
	$settle_country = $_POST["settle_country"];
	$bank_network_name = $_POST["bank_network_name"];
	$acc_bank_mm_num = $_POST["acc_bank_mm_num"];
	$acc_bank_rou_num = $_POST["acc_bank_rou_num"];

	$password = $_POST["my_password"];
	include(ROOT_PATH . 'inc/pw_fold.php');
	$email_or_phone =  $_SESSION["user"];
	include(ROOT_PATH . 'inc/check_logger.php');

	include(ROOT_PATH . 'inc/db_connect.php');
	$settle_type = mysqli_real_escape_string($mysqli, $settle_type);
	$settle_country = mysqli_real_escape_string($mysqli, $settle_country);
	$bank_network_name = mysqli_real_escape_string($mysqli, $bank_network_name);
	$acc_bank_mm_num = mysqli_real_escape_string($mysqli, $acc_bank_mm_num);
	$acc_bank_rou_num = mysqli_real_escape_string($mysqli, $acc_bank_rou_num);

	if($settle_type != "" && $settle_country != "" && $bank_network_name != "" && $acc_bank_mm_num != "" && $login == "yes") {

		$query = "SELECT settle_type FROM fa_misika_faha WHERE investor_id = '$investor_id'";   
		$result = $mysqli->query($query);
				
		if (mysqli_num_rows($result) != 0) {

			//$query = "UPDATE fa_misika_faha SET settle_type = $settle_type, country = $settle_country, receiver_institution_name = $bank_network_name, b_acc_num_mm_num = $acc_bank_mm_num, routing_number = $acc_bank_rou_num  WHERE investor_id = '$investor_id'";

			$table_name = "fa_misika_faha";
			$column1_name = "routing_number";
			$column2_name = "settle_type";
			$column3_name = "country";
			$column4_name = "receiver_institution_name";
			$column5_name = "b_acc_num_mm_num";
			$row_check = "investor_id";

			$column1_value = $acc_bank_rou_num;
			$column2_value = $settle_type;
			$column3_value = $settle_country;
			$column4_value = $bank_network_name;
			$column5_value = $acc_bank_mm_num;
			$row_check_value = $investor_id;

			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			$pam4 = "s";
			$pam5 = "s";
			$pam6 = "s";

			include(ROOT_PATH . 'inc/update5_where1_prepared_statement.php');


			if ($done == 1) {

				$_SESSION["err"] = 1;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=0f29400a2488cb0a3888eff6c63f3acb&login=$old_e_login&u_type=$old_e_u_type");		

			} else {

				$_SESSION["err"] = 0;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=0f29400a2488cb0a3888eff6c63f3acb&login=$old_e_login&u_type=$old_e_u_type");		

			}

		} else {

			$table_name = "fa_misika_faha";
			$column1_name = "investor_id";
			$column2_name = "settle_type";
			$column3_name = "country";
			$column4_name = "receiver_institution_name";
			$column5_name = "b_acc_num_mm_num";
			$column6_name = "routing_number";

			$column1_value = $investor_id;
			$column2_value = $settle_type;
			$column3_value = $settle_country;
			$column4_value = $bank_network_name;
			$column5_value = $acc_bank_mm_num;
			$column6_value = $acc_bank_rou_num;

			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "s";
			$pam4 = "s";
			$pam5 = "s";
			$pam6 = "s";

			include(ROOT_PATH . 'inc/insert6_prepared_statement.php');

			if($done == 1) {

				$_SESSION["err"] = 1;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=0f29400a2488cb0a3888eff6c63f3acb&login=$old_e_login&u_type=$old_e_u_type");		


			} else {
				$_SESSION["err"] = 0;
				header("Location: ../user/index.php?fold=$e_investor_id&e_o=0f29400a2488cb0a3888eff6c63f3acb&login=$old_e_login&u_type=$old_e_u_type");		
			
			}

		}



	} else {

		$_SESSION["err"] = 0;
		header("Location: ../user/index.php?fold=$e_investor_id&e_o=0f29400a2488cb0a3888eff6c63f3acb&login=$old_e_login&u_type=$old_e_u_type");		
	}

}

