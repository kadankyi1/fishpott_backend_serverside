<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

	session_start();	
	require_once("config.php");

	include(ROOT_PATH . 'inc/db_connect.php');

	$not_news_id = trim($_POST["not_news_id"]);
	$not_update_table = trim($_POST["not_update_table"]);
	$not_column_change_name = trim($_POST["not_column_change_name"]);
	$not_check_row_name = trim($_POST["not_check_row_name"]);
	$not_check2_row_value = trim($_POST["not_check2_row_value"]);

	$item_1 = $not_column_change_name;
	$item_2 = $not_check_row_name;

	$table_name = $not_update_table;

	$column1_name = $not_column_change_name;
	$column1_value = 0;

	$column2_name = $not_check_row_name;
	$column2_value = $not_news_id;

	$pam1 = "i";
	$pam2 = "s";

	if($not_check2_row_value == 0) {

		include(ROOT_PATH . 'inc/select2_where2_prepared_statement.php');

	} else {

		$column3_name = "sku";
		$column3_value = $not_check2_row_value;
		$pam3 = "i";
		include(ROOT_PATH . 'inc/select2_where3_prepared_statement.php');

	}


	$check_item = $item_1;
	if($check_item != "0"){$check_item = "na";}

	if($done == 1 && $check_item == "0") {


		include(ROOT_PATH . 'inc/db_connect.php');

		$column1_name = $not_column_change_name;
		$column1_value = 1;

		$row_check = $not_check_row_name;
		$row_check_value = $not_news_id;

		$pam1 = "i";
		$pam2 = "s";
		$done = 0;
		include(ROOT_PATH . 'inc/update1_prepared_statement.php');

		$check_item = "na";

		

			$seenNotificationReturn  = array(

				'return_status' => $done

			);
			echo json_encode($seenNotificationReturn,JSON_UNESCAPED_SLASHES);


	} elseif($check_item == "na") {



			$seenNotificationReturn  = array(

				'return_status' => 0

			);
			echo json_encode($seenNotificationReturn,JSON_UNESCAPED_SLASHES);

	}
}
