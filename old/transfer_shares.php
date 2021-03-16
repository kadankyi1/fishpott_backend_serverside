<?php
session_start();
require_once("config.php");

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["user"]) && $_SESSION["user"] != "" && isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") {
	$old_e_login = $_POST["old_e_login"];
	$old_e_u_type = $_POST["old_e_u_type"];
include(ROOT_PATH . 'inc/set_check_login_type.php');
include(ROOT_PATH . 'inc/db_connect.php');

	$password = $_POST["my_pass"];
	include(ROOT_PATH . 'inc/pw_fold.php');
	$email_or_phone =  $_SESSION["user"];
	include(ROOT_PATH . 'inc/check_logger.php');
	if($login != "yes"){
		$transferReturn  = array(
			'status' => 0,
			'msg' => "Something Went Wrong. The 3rd Eye Has Recorded This"

			);
		echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES); exit;

	}
    $investor_id = $_SESSION["user_sys_id"];
	$share_id = $_POST["share_id"];
	$shares_num = $_POST["shares_num"];
	$receiver_potname = $_POST["receiver_potname"];

	include(ROOT_PATH . 'inc/db_connect.php');
	$shares_num = mysqli_real_escape_string($mysqli, $shares_num);
	$shares_num = intval($shares_num);
	$receiver_potname = mysqli_real_escape_string($mysqli, $receiver_potname);

	$query = "SELECT investor_id, pot_name FROM investor WHERE pot_name = '$receiver_potname'";   
	$result = $mysqli->query($query);
			
	if (mysqli_num_rows($result) != 0) {

		$row = $result->fetch_array(MYSQLI_ASSOC);
		$receiver_id = $row["investor_id"];
		$receiver_pot_name = $row["pot_name"];

	} else {

		$transferReturn  = array(
			'status' => 0,
			'msg' => "We Couldn't Fetch The Receiver"

			);
		echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES); exit;

	}	

	if($share_id != "" && $shares_num != "" && $receiver_potname != "" && isset($receiver_id) && $receiver_id != "" && $investor_id != $receiver_id) {

			$query = "SELECT num_of_shares, parent_shares_id, share_name FROM shares_owned WHERE share_id = '$share_id' AND owner_id = '$investor_id'";   
			$result = $mysqli->query($query);
					
			if (mysqli_num_rows($result) != 0) {

				$row = $result->fetch_array(MYSQLI_ASSOC);
				$num_of_shares = $row["num_of_shares"];
				$parent_shares_id = $row["parent_shares_id"];
				$share_name = $row["share_name"];

				if($shares_num > $num_of_shares) {

					$transferReturn  = array(
						'status' => 0,
						'msg' => "Something Went Awry"

						);
					echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

				} else {

						$new_num_of_shares_investor = $num_of_shares - $shares_num;

						$query = "SELECT num_of_shares, share_id FROM shares_owned WHERE parent_shares_id = '$parent_shares_id' AND owner_id = '$receiver_id'";   
						$result = $mysqli->query($query);
								
						if (mysqli_num_rows($result) != 0) {

							$row = $result->fetch_array(MYSQLI_ASSOC);
							$receiver_curr_num_of_shares = $row["num_of_shares"];
							$receiver_share_id = $row["share_id"];
							$new_num_of_shares_receiver = $receiver_curr_num_of_shares + $shares_num;

							$query = "UPDATE shares_owned SET num_of_shares = $new_num_of_shares_investor WHERE share_id = '$share_id' AND owner_id = '$investor_id'";
							$result = $mysqli->query($query);

							if ($result == true) {

									$query = "UPDATE shares_owned SET num_of_shares = $new_num_of_shares_receiver WHERE share_id = '$receiver_share_id' AND owner_id = '$receiver_id'";
									$result = $mysqli->query($query);

									if ($result == true) {


											$table_name = "y3n_transfers";

											$column1_name = "sender_id";
											$column2_name = "receiver_id";
											$column3_name = "shares_parent_id";
											$column4_name = "date_time";
											$column5_name = "num_shares_transfered";
											$column6_name = "shares_parent_name";

											$column1_value = $investor_id;
											$column2_value = $receiver_id;
											$column3_value = $parent_shares_id;
											$column4_value = date("Y-m-d H:i:s");
											$column5_value = $shares_num;
											$column6_value = $share_name;

											$pam1 = "s";
											$pam2 = "s";
											$pam3 = "s";
											$pam4 = "s";
											$pam5 = "i";
											$pam6 = "s";

											$done = 0;
											include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
												$transferReturn  = array(
													'status' => 1,
													'info_1' => $share_name . "( " . $new_num_of_shares_investor . " ) ",
													'info_2' => $new_num_of_shares_investor,
													'msg' => "Transfer Complete"

													);
												echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES); exit;
									} else {
										$transferReturn  = array(
											'status' => 0,
											'msg' => "Something Went Awry. Transfer Didn't Complete. Contact FishPott"

											);
										echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

									}

							} else {
								$transferReturn  = array(
									'status' => 0,
									'msg' => "Something Went Awry. Transfer Didn't Complete. Contact FishPott"

									);
								echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

							}

						} else {

									$query = "UPDATE shares_owned SET num_of_shares = $new_num_of_shares_investor WHERE share_id = '$share_id' AND owner_id = '$investor_id'";
									$result = $mysqli->query($query);

									if ($result == true) {

										$trans_shares_id = $parent_shares_id . $receiver_potname;

											$table_name = "shares_owned";

											$column1_name = "share_id";
											$column2_name = "parent_shares_id";
											$column3_name = "share_name";
											$column4_name = "owner_id";
											$column5_name = "cost_price_per_share";
											$column6_name = "num_of_shares";

											$receiver_share_id = $parent_shares_id . $receiver_pot_name;
											
											$column1_value = $receiver_share_id;
											$column2_value = $parent_shares_id;
											$column3_value = $share_name;
											$column4_value = $receiver_id;
											$column5_value = 0.00;
											$column6_value = $shares_num;

											$pam1 = "s";
											$pam2 = "s";
											$pam3 = "s";
											$pam4 = "s";
											$pam5 = "d";
											$pam6 = "s";

											$done = 0;
											include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
											include(ROOT_PATH . 'inc/db_connect.php');
											if($done == 1){

											$table_name = "y3n_transfers";

											$column1_name = "sender_id";
											$column2_name = "receiver_id";
											$column3_name = "shares_parent_id";
											$column4_name = "date_time";
											$column5_name = "num_shares_transfered";
											$column6_name = "shares_parent_name";

											$column1_value = $investor_id;
											$column2_value = $receiver_id;
											$column3_value = $parent_shares_id;
											$column4_value = date("Y-m-d H:i:s");
											$column5_value = $shares_num;
											$column6_value = $share_name;

											$pam1 = "s";
											$pam2 = "s";
											$pam3 = "s";
											$pam4 = "s";
											$pam5 = "i";
											$pam6 = "s";

											$done = 0;
											include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
											include(ROOT_PATH . 'inc/db_connect.php');
												$transferReturn  = array(
													'status' => 1,
													'info_1' => $share_name . "( " . $new_num_of_shares_investor . " ) ",
													'info_2' => $new_num_of_shares_investor,
													'msg' => "Transfer Complete"

													);
												echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

											} else {
												$transferReturn  = array(
													'status' => 0,
													'msg' => "Something Went Awry. Transfer Didn't Complete. Contact FishPott"

													);
												echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

											}

									} else {

											$transferReturn  = array(
												'status' => 0,
												'msg' => "Something Went Awry"

												);
											echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;
									}
						
						}	
				}

			} else {

				$transferReturn  = array(
					'status' => 0,
					'msg' => "Something Went Awry"

					);
				echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;

			}	

	} else {

				$transferReturn  = array(
					'status' => 0,
					'msg' => "Something Went Awry"

					);
				echo json_encode($transferReturn,JSON_UNESCAPED_SLASHES);  exit;
	}

}

