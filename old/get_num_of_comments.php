<?php

$stmt = $mysqli->prepare("SELECT $item_1 FROM $table_name WHERE news_id = ? ORDER BY sku ASC");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("s", $news_id);

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

if($done == 1 && $item_1 != "sku" && $item_1 != ""){


  $first_comment = $item_1;
  $count_comments = 0;
//echo $first_comment . "<br>";
//echo $latest_sku_comments . "<br>"; 
  //exit;
include(ROOT_PATH . 'inc/db_connect_autologout.php');

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM comments WHERE news_id = ?;");
/* BK: always check whether the prepare() succeeded */
if ($stmt === false) {
    $done = 0;
} else {
    $stmt->bind_param("s", $news_id);

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

  $count_comments = $item_1;
} else {

  $count_comments = 0;

}

?>