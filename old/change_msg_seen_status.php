<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');
$investor_id = $_SESSION["user_sys_id"];
$akasakasa_id = $_GET["chatid"];

  $query = "SELECT sku FROM kasa WHERE akasakasa_id = '$akasakasa_id' AND kaka_nake_status = 0 AND receiver_id = '$investor_id' ORDER BY sku ASC";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $last_unseen_msg = $row["sku"];

}  


$query = "SELECT sku FROM kasa WHERE akasakasa_id = '$akasakasa_id' AND kaka_nake_status = 0 AND receiver_id = '$investor_id' ORDER BY sku DESC";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $newest_msg = $row["sku"];

} else {

  $newest_msg = -1;

}

//echo "investor_id : " . $investor_id . "\n";
//echo "akasakasa_id : " . $akasakasa_id . "\n";
//echo "last_unseen_msg : " . $last_unseen_msg . "\n";
//echo "newest_msg : " . $newest_msg; exit;

$cnt_seen_msg = 0;

while($newest_msg > 0 && $newest_msg >= $last_unseen_msg) {



      $query = "UPDATE  kasa SET  kaka_nake_status =  1 WHERE akasakasa_id =  '$akasakasa_id' AND sku = $newest_msg ";
      			
      $result = $mysqli->query($query);


      if ($result == "1") {

        $cnt_seen_msg = $cnt_seen_msg + 1;

      	      $done = 1;

        }
          $newest_msg--;
  }


      $seenmsg_cnt  = array(

        'cnt_seen_msg' => $cnt_seen_msg

      );

      echo json_encode($seenmsg_cnt,JSON_UNESCAPED_SLASHES);

