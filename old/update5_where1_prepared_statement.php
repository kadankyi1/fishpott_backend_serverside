<?php
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
	$table_name = $_POST["table_name"];
	$column1_name = $_POST["column1_name"];
	$column1_value = $_POST["column1_value"];
	$row_check = $_POST["row_check"];
	$row_check_value = $_POST["row_check_value"];
	$pam1 = $_POST["pam1"];
	$pam2 = $_POST["pam2"];

}

$stmt = $mysqli->prepare("UPDATE  $table_name SET  $column1_name =?, $column2_name =?, $column3_name =?, $column4_name =?, $column5_name =? WHERE $row_check =?");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1$pam2$pam3$pam4$pam5$pam6", $column1_value, $column2_value, $column3_value, $column4_value, $column5_value, $row_check_value);

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

?>