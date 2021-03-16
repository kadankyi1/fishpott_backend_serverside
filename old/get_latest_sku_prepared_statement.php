<?php

$stmt = $mysqli->prepare("SELECT $item_1 FROM $table_name ORDER BY sku DESC");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {

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
?>