<?php

$cnt = 0;

$join = 1;
$item_1 = 0;
$table1_name = "comments";
$table2_name = "newsfeed";
$tb1_column_match = "news_id";
$tb2_column_match = "news_id";
$row_chk1_tb = "newsfeed";
$row_chk1_tb_column = "inputtor_id";
$row_chk1_tb_value = $investor_id;
$row_chk2_tb = "comments";
$row_chk2_tb_column = "comment_nkae_status";
$row_chk2_tb_value = 0;
$row_chk3_tb = "comments";
$row_chk3_tb_column = "inputtor_id";
$row_chk3_tb_value = $investor_id;
$pam1 = "s";
$pam2 = "i";
$pam3 = "s";
include(ROOT_PATH . 'inc/count_2table_join_where3_prepared_statement.php');

if($item_1 != "") {

	$cnt = $cnt + $item_1;
}


include(ROOT_PATH . 'inc/db_connect_autologout.php');

$join = 1;
$item_1 = 0;
$table1_name = "likes";
$table2_name = "newsfeed";
$tb1_column_match = "likes_news_id";
$tb2_column_match = "news_id";
$row_chk1_tb = "newsfeed";
$row_chk1_tb_column = "inputtor_id";
$row_chk1_tb_value = $investor_id;
$row_chk2_tb = "likes";
$row_chk2_tb_column = "likes_nkae_status";
$row_chk2_tb_value = 0;
$row_chk3_tb = "likes";
$row_chk3_tb_column = "liker_investor_id";
$row_chk3_tb_value = $investor_id;
$pam1 = "s";
$pam2 = "i";
$pam3 = "s";
include(ROOT_PATH . 'inc/count_2table_join_where3_prepared_statement.php');

if($item_1 != "") {

	$cnt = $cnt + $item_1;
	
}


include(ROOT_PATH . 'inc/db_connect_autologout.php');
$join = 1;
$item_1 = 0;
$table1_name = "likes";
$table2_name = "newsfeed";
$tb1_column_match = "likes_news_id";
$tb2_column_match = "news_id";
$row_chk1_tb = "newsfeed";
$row_chk1_tb_column = "inputtor_id";
$row_chk1_tb_value = $investor_id;
$row_chk2_tb = "likes";
$row_chk2_tb_column = "likes_nkae_status";
$row_chk2_tb_value = 0;
$row_chk3_tb = "likes";
$row_chk3_tb_column = "liker_investor_id";
$row_chk3_tb_value = $investor_id;
$pam1 = "s";
$pam2 = "i";
$pam3 = "s";
include(ROOT_PATH . 'inc/count_2table_join_where3_prepared_statement.php');
//include(ROOT_PATH . 'inc/count_2table_join_where3_prepared_statement.php');

if($item_1 != "") {

	$cnt = $cnt + $item_1;
	
}

include(ROOT_PATH . 'inc/db_connect_autologout.php');
$item_1 = 0;
$table1_name = "linkups";
$row_chk1_tb_value = $investor_id;
$row_chk2_tb_value = $investor_id;
$row_chk3_tb_value = 0;
$pam1 = "s";
$pam2 = "s";
$pam3 = "i";

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM linkups WHERE (sender_id = ? OR receiver_id = ?) AND linkups_nkae_status = ?");
if ($stmt === false) {
    $done = 0;
} else {

$stmt->bind_param("$pam1$pam2$pam3", $row_chk1_tb_value, $row_chk2_tb_value, $row_chk3_tb_value);

$status = $stmt->execute();
$stmt->bind_result($item_1);
$stmt->fetch();

if ($status === false) {
    $done = 0;
} else {
        $done = 1;
    }
}

 $stmt->close();
/* close connection */
$mysqli->close();
if($item_1 != "") {

	$cnt = $cnt + $item_1;
	
}



?>
