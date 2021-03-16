<?php
session_start();
/*

i   corresponding variable has type integer
d   corresponding variable has type double
s   corresponding variable has type string
b   corresponding variable is a blob and will be sent in packets

*/

/* create a prepared statement */

if(isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
	require_once("config.php");
	include(ROOT_PATH . 'inc/db_connect.php');
	$table_name = "investor";

	$item_1 = "net_worth";

	$column1_name = "investor_id";
	$column1_value = $_SESSION["user_sys_id"];
	$pam1 = "s";

	include(ROOT_PATH . 'inc/select1_where1_prepared_statement.php');
	include(ROOT_PATH . 'inc/db_connect.php');
	if($done == 1 && $item_1 != "" && $item_1 != "net_worth" ) {
		$item_charge = $_POST["itemcharge"];
		if($item_1 > $item_charge) {
			$table_name = $_POST["table_name"];
			$column1_name = $_POST["column1_name"];

			$new_coins = $item_1 - $item_charge;
			$column1_value = $new_coins;
			$row_check = $_POST["row_check"];
			$row_check_value = $_POST["row_check_value"];
			$pam1 = $_POST["pam1"];
			$pam2 = $_POST["pam2"];
			$s_index = $_POST["s_index"];
			$_SESSION[$s_index][7] = $_POST["item_quant"];
			$_SESSION[$s_index][8] = "Pott Coins";
			if($_SESSION[$s_index][2] == "up4sale") {

				$_SESSION[$s_index][4] = $_POST["add"] . ",  " . $_POST["reg"] . ", " . $_POST["cntry"];
				$_SESSION[$s_index][5] = $_POST["this_lat"];
				$_SESSION[$s_index][6] = $_POST["this_long"];
			}
			$stmt = $mysqli->prepare("UPDATE  $table_name SET  $column1_name =? WHERE $row_check =?");
			/* BK: always check whether the prepare() succeeded */
			if ($stmt === false) {
			    $done = 0;
			} else {
			    $stmt->bind_param("$pam1$pam2", $column1_value, $row_check_value);

			$status = $stmt->execute();

			if ($status === false) {
			    $done = 0;
			} else {
			        $done = 1;

			    }
			}

			 $stmt->close();
			/* close connection */
			$mysqli->close();

				if(isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
					include(ROOT_PATH . 'inc/db_connect.php');
			        echo $done;

				}

		} else {

			echo 0;
		}
	} else {

		echo 0;
	}

}


?>