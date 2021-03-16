<?php
session_start();
$config = 1;
require_once("../inc/config.php");
$investor_id = $_SESSION["user_sys_id"];
include(ROOT_PATH . 'inc/db_connect.php');
$cur_akasa_id = $_GET["chatid"];
$sku = $_GET["sku"];
$unseen_msg = array();
$j = 0;
for($i = $sku; $i > 0; $i--) {

  $query = "SELECT sku, sender_id, receiver_id, kaka_nake_status FROM kasa WHERE sku = $i AND akasakasa_id = '$cur_akasa_id'";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $unseen_msg[$j]["msg_sku"] = $row["sku"];

      if($row["sender_id"] == $investor_id) {

      		$unseen_msg[$j]["class_name"] = "me";

      } else {

      		$unseen_msg[$j]["class_name"] = "you";
      }

      if($row["kaka_nake_status"] == 1) {

      	break;
      }

      $j = $j + 1;
    } 

}

echo json_encode($unseen_msg,JSON_UNESCAPED_SLASHES);

