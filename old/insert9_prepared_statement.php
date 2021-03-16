<?php
/*

i   corresponding variable has type integer
d   corresponding variable has type double
s   corresponding variable has type string
b   corresponding variable is a blob and will be sent in packets

*/

/* create a prepared statement */

$stmt = $mysqli->prepare("INSERT INTO $table_name ($column1_name, $column2_name, $column3_name,  $column4_name, $column5_name, $column6_name, $column7_name, $column8_name, $column9_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("$pam1$pam2$pam3$pam4$pam5$pam6$pam7$pam8$pam9", $column1_value, $column2_value, $column3_value, $column4_value, $column5_value, $column6_value, $column7_value, $column8_value, $column9_value);

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
?>