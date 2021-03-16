<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');
$investor_id = $_SESSION["user_sys_id"];

$all_chats = array();
$table_name = "akasakasa";
$order_by = "sku";
$k = 0;

if(isset($_GET["ajax"]) && $_GET["ajax"] != ""){
  require_once("../inc/config.php");
  $config = "yes";
  include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $table_name = $_GET["table_name"];
  $order_by = $_GET["order_by"];
}
  $query = "SELECT sku FROM $table_name ORDER BY $order_by DESC ";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $latest_sku = $row["sku"];
	    $skip = 0;

    } else {

		$skip = 1;
    }

if($skip == 0 && isset($latest_sku) && $latest_sku != ""){
	for($latest_sku; $latest_sku > 0; $latest_sku--){

			$query = "SELECT * FROM akasakasa WHERE sku = $latest_sku AND (sender_id = '$investor_id' OR receiver_id = '$investor_id')";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {

			    $row = $result->fetch_array(MYSQLI_ASSOC);
			    $sender_id = $row["sender_id"];
			    $receiver_id = $row["receiver_id"];
			    $akasakasa_id = $row["akasakasa_id"];
			    $latest_kasa_sku = $row["latest_kasa_sku"];
			    $strStart = $row["latest_date_time"];

			    include(ROOT_PATH . 'inc/time_converter.php');
			    $done = 1;


				    if($sender_id == $investor_id){

				      $not_investor_id = $receiver_id;

				    } else {

				      $not_investor_id = $sender_id;
				      
				    }
				    
				    $all_chats[$k]["akasakasa_id"] = $akasakasa_id;
				    $all_chats[$k]["not_investor_id"] = $not_investor_id;
				    $all_chats[$k]["latest_kasa_sku"] = $latest_kasa_sku;
				    $k = $k + 1;



			  }		
	}

	if(count($all_chats) == 0){

			    $all_chats[$k]["akasakasa_id"] = "na";
			    $all_chats[$k]["not_investor_id"] = "na";
			    $all_chats[$k]["latest_kasa_sku"] = "na";
			echo json_encode($all_chats,JSON_UNESCAPED_SLASHES); //exit;

	} else {

			echo json_encode($all_chats,JSON_UNESCAPED_SLASHES); //exit;

	}



} else {

			    $all_chats[$k]["akasakasa_id"] = "na";
			    $all_chats[$k]["not_investor_id"] = "na";
			    $all_chats[$k]["latest_kasa_sku"] = "na";
			echo json_encode($all_chats,JSON_UNESCAPED_SLASHES); //exit;

}



