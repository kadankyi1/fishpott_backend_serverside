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
	$column2_name = $_POST["column2_name"];
	$column2_value = $_POST["column2_value"];
	$column3_name = $_POST["column3_name"];
	$column3_value = $_POST["column3_value"];
	$column4_value = $_POST["column4_value"];
	$pam1 = $_POST["pam1"];
	$pam2 = $_POST["pam2"];
	$pam3 = $_POST["pam3"];
	$pam4 = $_POST["pam4"];

}

$stmt = $mysqli->prepare("INSERT INTO $table_name ($column1_name, $column2_name, $column3_name, $column4_name) VALUES (?, ?, ?, ?)");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1$pam2$pam3$pam4", $column1_value, $column2_value, $column3_value, $column4_value);

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