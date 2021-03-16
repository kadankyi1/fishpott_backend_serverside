<?php

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table1_name WHERE $row_chk1_tb_column = ?");

if ($stmt === false) {
    $done = 0;
} else {

$stmt->bind_param("$pam1", $row_chk1_tb_value);

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
$join = "";

?>