<?php

session_start();	
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1) {

    $investor_id = $_SESSION["user_sys_id"];


} else {

	include(ROOT_PATH . 'inc/get_fold.php');

	include(ROOT_PATH . 'inc/set_check_login_type.php');

	include(ROOT_PATH . 'inc/id_unfold.php');


}
    $link_investor_id = $_GET["link_investor_id"];

	$table_name = "linkups";
	$column1_name = "sender_id";
	$column1_value = "'" . $investor_id . "'";
	$column2_name = "receiver_id";
	$column2_value = "'" . $link_investor_id . "'";

	include(ROOT_PATH . 'inc/select_items_2_conditions.php');

	$found_1 = $found_item;

	$column1_name = "sender_id";
	$column1_value = "'" . $link_investor_id . "'";
	$column2_name = "receiver_id";
	$column2_value = "'" . $investor_id . "'";




	include(ROOT_PATH . 'inc/select_items_2_conditions.php');

 	$found_2 = $found_item;


 	if($found_1 == 0 && $found_2 == 0) {


		$link_date_started = date("Y-m-d");

			$column1_name = "sender_id";
			$column2_name = "receiver_id";
			$column3_name = "status";
			$column4_name = "date_started";
			$column5_name = "sku";

			$column1_value = $investor_id;
			$column2_value = $link_investor_id;
			$column3_value = 0;
			$column4_value = $link_date_started;
			$column5_value = "";

			$pam1 = "s";
			$pam2 = "s";
			$pam3 = "i";
			$pam4 = "s";
			$pam5 = "i";

		include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
		include(ROOT_PATH . 'inc/db_connect.php');

		if($done == 1) {

			$sendlinkUpStatus  = array(
				'send_link_up_status' => 1
				);
			echo json_encode($sendlinkUpStatus,JSON_UNESCAPED_SLASHES); //exit;


		} else {

			$sendlinkUpStatus  = array(
				'send_link_up_status' => 0
				);
			echo json_encode($sendlinkUpStatus,JSON_UNESCAPED_SLASHES); //exit;

		}
	} else {

		$sendlinkUpStatus  = array(
			'send_link_up_status' => 0
			);
		echo json_encode($sendlinkUpStatus,JSON_UNESCAPED_SLASHES); //exit;

	}