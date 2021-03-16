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

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1) {
	require_once("config.php");
	include(ROOT_PATH . 'inc/db_connect.php');
	$table_name = $_GET["table_name"];

	$item_1 = $_GET["item_1"];

	$column1_name = $_GET["column1_name"];
	$column1_value = $_GET["column1_value"];

	$pam1 = $_GET["pam1"];


}
$stmt = $mysqli->prepare("SELECT $item_1 FROM $table_name WHERE $column1_name = ?");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
    $skip = 1;
} else {
    $stmt->bind_param("$pam1", $column1_value);

$status = $stmt->execute();
$stmt->bind_result($item_1);
$stmt->fetch();

if ($status === false) {
    $done = 0;
    $skip = 1;
} else {
        $done = 1;
        $skip = 0;
    }
}

 $stmt->close();
/* close connection */
$mysqli->close();

if(isset($_GET["ajax"]) && $_GET["ajax"] == 1) {
	include(ROOT_PATH . 'inc/db_connect.php');

	if($done == 1 && $item_1 != "") {

				$rates  = array(

					'item_1' => $item_1, 
					'set' => 1

					);
				echo json_encode($rates,JSON_UNESCAPED_SLASHES);

		} else {

				$rates  = array(

					'set' => 0

					);
				echo json_encode($rates,JSON_UNESCAPED_SLASHES);

		}
}

?>