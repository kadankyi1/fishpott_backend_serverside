<?php

if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['generic_item_news_id']) && trim($_POST['generic_item_news_id']) != "" && 
	isset($_POST['item_order_quantity']) && trim($_POST['item_order_quantity']) != ""&& 
	isset($_POST['mycountry'])) {
	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $generic_item_news_id = mysqli_real_escape_string($mysqli, $_POST['generic_item_news_id']);
    $item_order_quantity = mysqli_real_escape_string($mysqli, $_POST['item_order_quantity']);
    $mycountry = mysqli_real_escape_string($mysqli, $_POST['mycountry']);

    $myid = trim($myid);
    $investor_id = $myid;
    $mypass = trim($mypass);
    $generic_item_news_id = trim($generic_item_news_id);
    $item_order_quantity = trim($item_order_quantity);
    $item_order_quantity = floatval($item_order_quantity);
    $mycountry = trim($mycountry);
  	if($mycountry != "Ghana" && $mycountry != "United Kingdom" && $mycountry != "USA"){

      $mycountry = "USA";
      
    }

    $query = "SELECT password, flag, login_type FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dblogin_type = trim($row["login_type"]);

          if($mypass == $dbpass && $dbflag == 0) {
		  		    $newsfeedReturn["hits"] = array();

              $query = "SELECT * FROM nsesa WHERE sku = 1";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $GHS_USD = $row["GHS_USD"];
                      $USD_GHS = $row["USD_GHS"];
                      $GHS_GBP = $row["GHS_GBP"];
                      $GBP_GHS = $row["GBP_GHS"];
                      $USD_GBP = $row["USD_GBP"];
                      $GBP_USD = $row["GBP_USD"];
                      $coins_GHS = $row["coins_GHS"];
                      $coins_USD = $row["coins_USD"];
                      $coins_GBP = $row["coins_GBP"];
                      $rates = 1;

                    } else {
                      $next  = array(
                              'hit_status' => "0"
                              );
                      array_push($newsfeedReturn["hits"], $next); 
                      echo json_encode($newsfeedReturn); exit;
                    }

        $total_item_quantity_price = $item_order_quantity;
        $convert_amt = floatval($item_order_quantity);

              $seller_country = trim($mycountry);
              $i_country = "Ghana";

          include(ROOT_PATH . 'inc/android_currency_converter.php');
              $new_amt_cedis = $new_amt_user;

              if($mycountry == "Ghana") {

                $my_currency = "GHc";

              } elseif($mycountry == "United Kingdom") {

                $my_currency = "GBP";

              } else {

                $my_currency = "USD";
              }

              $ttl_str = $my_currency . " " . $item_order_quantity;

              
              
                      $next  = array(
                                'hit_status' => "1",
                                'currency' => $my_currency,
                                'total_charge_str' => $ttl_str,
                                'total_charge_num' => $item_order_quantity,
                                'total_charge_num_cedis' => $new_amt_cedis
                                );
                    array_push($newsfeedReturn["hits"], $next); 
                  echo json_encode($newsfeedReturn); exit;

          } // END OF PASSWORD CHECK
    }
}