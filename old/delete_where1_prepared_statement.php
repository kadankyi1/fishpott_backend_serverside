<?php
/*

i   corresponding variable has type integer
d   corresponding variable has type double
s   corresponding variable has type string
b   corresponding variable is a blob and will be sent in packets

*/
/* create a prepared statement */
//$order_by = "sku";
//include(ROOT_PATH . 'inc/get_latest_sku.php');

//$num = 1;
//while($test_count) {
if(isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
	require_once("config.php");
	include(ROOT_PATH . 'inc/db_connect.php');
	$table_name = $_POST["table_name"];
	$column1_name = $_POST["column1_name"];
	$column1_value = $_POST["column1_value"];
	$pam1 = $_POST["pam1"];

}

$stmt = $mysqli->prepare("DELETE FROM $table_name WHERE $column1_name = ?");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1", $column1_value);

$status = $stmt->execute();
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

	if(isset($_POST["ajax"]) && $_POST["ajax"] == 1) {
		include(ROOT_PATH . 'inc/db_connect.php');
        echo $done;

	}

?>