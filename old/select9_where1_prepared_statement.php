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

$stmt = $mysqli->prepare("SELECT $item_1, $item_2, $item_3, $item_4, $item_5, $item_6 , $item_7, $item_8, $item_9 FROM $table_name WHERE $column1_name = ?");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1", $column1_value);

$status = $stmt->execute();
$stmt->bind_result($item_1, $item_2, $item_3, $item_4, $item_5, $item_6, $item_7, $item_8, $item_9);
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
?>