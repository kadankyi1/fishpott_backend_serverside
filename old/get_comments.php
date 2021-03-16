<?php

session_start();
require_once("config.php");

include(ROOT_PATH . 'inc/id_unfold.php');

$old_e_login = $_SESSION["login_type"];
$old_e_u_type = $_SESSION["user_type"];

include(ROOT_PATH . 'inc/set_check_login_type.php');

$comment_input = trim($_POST["comment"]);

if ($comment_input != "") {
	$news_id = $_POST["news_id"];
	$comment_inputtor_id = $investor_id;
	$comment_date = date("Y-m-d H:i:s");

	$table_name = "comments";

	$column1_name = "sku";
	$column2_name = "news_id";
	$column3_name = "inputtor_id";
	$column4_name = "date_time";
	$column5_name = "comment";

	$column1_value = "";
	$column2_value = $news_id;
	$column3_value = $comment_inputtor_id;
	$column4_value = $comment_date;
	$column5_value = $comment_input;

	$pam1 = "i";
	$pam2 = "s";
	$pam3 = "s";
	$pam4 = "s";
	$pam5 = "s";
	include(ROOT_PATH . 'inc/db_connect.php');
	include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
	exit;
}