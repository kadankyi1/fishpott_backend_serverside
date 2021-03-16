<?php
if(isset($_GET["ajax"]) && $_GET["ajax"] != ""){
  require_once("../inc/config.php");
  $config = "yes";
  include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $table_name = $_GET["table_name"];
  if(isset($_GET["order_by"])) {

    $order_by = $_GET["order_by"];

  } else {
    $skip_this = 1;
    $order_by = "sku";
  }
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

if(isset($_GET["ajax"]) && $_GET["ajax"] != "" && !isset($skip_this)){

        $latest_sku_array  = array(

          'latest_sku' => $latest_sku 

          );
        echo json_encode($latest_sku_array,JSON_UNESCAPED_SLASHES);


}
