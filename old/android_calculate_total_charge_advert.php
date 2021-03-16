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
    $item_order_quantity = intval($item_order_quantity);
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


/*************************** CALCULATE TOTAL CHARGE HERE **********************************/
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
                      $ad_charge = trim($row["ad_charge"]);
                      $ad_charge = floatval($ad_charge);
                      $rates = 1;

                    } else {
	              		 $next  = array(
			                      'hit_status' => "0"
			                      );
				              array_push($newsfeedReturn["hits"], $next);	
	    				         echo json_encode($newsfeedReturn); exit;
                    }
              $seller_country = "Ghana";
              $i_country = $mycountry;

        $convert_amt = floatval($ad_charge);

          include(ROOT_PATH . 'inc/android_currency_converter.php');

          $price_per_ad_str = $new_amt_user_str;
          unset($new_amt_user);
          unset($new_amt_user_str);
          unset($new_amt_user_currency);

        $total_item_quantity_price = $item_order_quantity * $ad_charge;
        $convert_amt = floatval($total_item_quantity_price);

              $i_country = "Ghana";

          include(ROOT_PATH . 'inc/android_currency_converter.php');
              $new_amt_cedis = $new_amt_user;

                unset($shares_conversion);
                unset($new_amt_user);
                unset($new_amt_user_currency);
                unset($new_amt_user_str);
                unset($convert_amt);

              $i_country = $mycountry;

        $total_item_quantity_price = $item_order_quantity * $ad_charge;
        $convert_amt = floatval($total_item_quantity_price);

          include(ROOT_PATH . 'inc/android_currency_converter.php');

          if(!isset($new_amt_user_str) || trim($new_amt_user_str) == "" ){

                     $next  = array(
                            'hit_status' => "0"
                            );
                      array_push($newsfeedReturn["hits"], $next); 
                       echo json_encode($newsfeedReturn); exit;

          } else {


          	
                      $next  = array(
                                'hit_status' => "1",
                                'currency' => $new_amt_user_currency,
                                'price_per_ad_str' => $price_per_ad_str,
                                'total_charge_str' => $new_amt_user_str,
                                'total_charge_num' => $new_amt_user,
                                'total_charge_num_cedis' => $new_amt_cedis
                                );
                    array_push($newsfeedReturn["hits"], $next); 
                  echo json_encode($newsfeedReturn); exit;

          }

          } // END OF PASSWORD CHECK
    }
}