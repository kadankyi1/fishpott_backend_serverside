<?php

require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');

$query = "SELECT * FROM nsesa WHERE sku = 1";
			$result = $mysqli->query($query);
			if (mysqli_num_rows($result) != "0") {

				$row = $result->fetch_array(MYSQLI_ASSOC);
				$GHS_USD = $row["GHS_USD"];
				$USD_GHS = $row["USD_GHS"];
				$GHS_GBP = $row["GHS_GBP"];
				$GBP_GHS = $row["GBP_GHS"];
				$USD_GBP = $row["USD_GBP"];
				$GBP_USD = $row["GBP_USD"];
				$coins_GHS = $row["coins_GHS"];
				$coins_USD = $row["coins_USD"];
				$coins_GBP = $row["coins_GBP"];

					$signUpReturn["datareturned"][0]  = array(
						'status' => "yes", 
						'GHS_USD' => $GHS_USD,
						'USD_GHS' => $USD_GHS,
						'GHS_GBP' => $GHS_GBP,
						'GBP_GHS' => $GBP_GHS,
						'USD_GBP' => $USD_GBP,
						'GBP_USD' => $GBP_USD,
						'coins_GHS' => $coins_GHS,
						'coins_USD' => $coins_USD,
						'coins_GBP' => $coins_GBP,
						'error_set' => "0", 
						'error' => ""
		 
						);
					echo json_encode($signUpReturn); exit;
			} else {

				$signUpReturn["datareturned"][0]  = array(
					'status' => "no", 
					'user_id' => "na",
					'user_type' => "na", 
					'key' => "na", 
					'user_sys_id' => "na", 
					'e_user_type' => "na", 
					'error_set' => "1", 
					'error' => "Something went Awry. We Couldn't Verify Your Pott"

					);
				echo json_encode($signUpReturn); exit;
			}
