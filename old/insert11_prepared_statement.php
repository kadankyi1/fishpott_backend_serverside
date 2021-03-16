<?php
/*

i   corresponding variable has type integer
d   corresponding variable has type double
s   corresponding variable has type string
b   corresponding variable is a blob and will be sent in packets

*/

/* create a prepared statement */
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
	$column4_name = $_POST["column4_name"];
	$column4_value = $_POST["column4_value"];
	$column5_name = $_POST["column5_name"];
	$column5_value = $_POST["column5_value"];
	$column6_name = $_POST["column6_name"];
	$column6_value = $_POST["column6_value"];
	$column7_name = $_POST["column7_name"];
	$column7_value = $_POST["column7_value"];
	$column8_name = $_POST["column8_name"];
	$column8_value = $_POST["column8_value"];
	$column9_name = $_POST["column9_name"];
	$column9_value = $_POST["column9_value"];
	$column10_name = $_POST["column10_name"];
	$column10_value = $_POST["column10_value"];
	$column11_name = $_POST["column11_name"];
	$column11_value = $_POST["column11_value"];
	$pam1 = $_POST["pam1"];
	$pam2 = $_POST["pam2"];
	$pam3 = $_POST["pam3"];
	$pam4 = $_POST["pam4"];
	$pam5 = $_POST["pam5"];
	$pam6 = $_POST["pam6"];
	$pam7 = $_POST["pam7"];
	$pam8 = $_POST["pam8"];
	$pam9 = $_POST["pam9"];
	$pam10 = $_POST["pam10"];
	$pam11 = $_POST["pam11"];

}

$stmt = $mysqli->prepare("INSERT INTO $table_name ($column1_name, $column2_name, $column3_name,  $column4_name, $column5_name, $column6_name, $column7_name, $column8_name, $column9_name, $column10_name, $column11_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1$pam2$pam3$pam4$pam5$pam6$pam7$pam8$pam9$pam10$pam11", $column1_value, $column2_value, $column3_value, $column4_value, $column5_value, $column6_value, $column7_value, $column8_value, $column9_value, $column10_value, $column11_value);

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