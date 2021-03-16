<?php

if($join == 1) {

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table1_name INNER JOIN $table2_name  ON $table1_name.$tb1_column_match = $table2_name.$tb2_column_match WHERE $row_chk1_tb.$row_chk1_tb_column = ? AND $row_chk2_tb.$row_chk2_tb_column = ? AND $row_chk3_tb.$row_chk3_tb_column != ?");
} elseif($join == 0) {

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM $table1_name WHERE $row_chk1_tb_column = ? AND $row_chk2_tb_column = ? AND $row_chk3_tb_column != ?");

}
/* BK: always check whether the prepare() succeeded */
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
$join = "";

?>