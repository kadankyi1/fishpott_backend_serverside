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
                      $GBP_GHS = $row["GBP_GHS"];

                    } else {
                    $next  = array(
                            'hit_status' => "0"
                            );
                array_push($newsfeedReturn["hits"], $next); 
              echo json_encode($newsfeedReturn); exit;
                    }
		  		$newsfeedReturn["hits"] = array();

              $query = "SELECT parent_shares_id, sharesOnSale_id, shares4sale_owner_id, selling_price, currency, num_on_sale, number_sold, sale_status FROM shares4sale WHERE shares_news_id = '$generic_item_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $sale_status = trim($row["sale_status"]);
                $item_quantity = trim($row["num_on_sale"]);
                $item_quantity = intval($item_quantity);
                $item_price = trim($row["selling_price"]);
                $item_price = floatval($item_price);
                $seller_currency = trim($row["currency"]);
                $shares4sale_owner_id = trim($row["shares4sale_owner_id"]);
                $parent_shares_id = trim($row["parent_shares_id"]);
                $sharesOnSale_id = trim($row["sharesOnSale_id"]);

                if($sale_status == "1"){

                	$next  = array(
		                      'hit_status' => "2"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

                } else if ($item_order_quantity > intval($item_quantity)){

                	$next  = array(
		                      'hit_status' => "3"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

                } else if ($item_price <= 0){

                	$next  = array(
		                      'hit_status' => "4"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

                }

              $query = "SELECT num_of_shares, start_date, yield_date FROM shares_owned WHERE share_id = '$sharesOnSale_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $seller_num_of_shares = trim($row["num_of_shares"]);
                $seller_num_of_shares = intval($seller_num_of_shares);
                $shares_start_date = trim($row["start_date"]);
                $shares_yield_date = trim($row["yield_date"]);
                if($seller_num_of_shares < $item_order_quantity){
                    $next  = array(
                            'hit_status' => "5"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;
                }

              } else {

                $next  = array(
                          'hit_status' => "0"
                          );
                array_push($newsfeedReturn["hits"], $next); 
                echo json_encode($newsfeedReturn); exit;
              }

              $query = "SELECT total_number, curr_max_price, yield_duration, yield_per_share FROM shares_worso WHERE parent_shares_id = '$parent_shares_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $total_num_of_shares_on_fp = trim($row["total_number"]);
                $total_num_of_shares_on_fp = intval($total_num_of_shares_on_fp);
                $curr_max_price = trim($row["curr_max_price"]);
                $curr_max_price = floatval($curr_max_price);
                $yield_duration = trim($row["yield_duration"]);
                $yield_per_share = trim($row["yield_per_share"]);
                $yield_per_share = floatval($yield_per_share);

        $convert_amt = $yield_per_share * $item_order_quantity;              
              $seller_country = "Ghana";
              $i_country = $mycountry;
              $shares_conversion = 1;
          include(ROOT_PATH . 'inc/android_currency_converter.php');
                if(!isset($new_amt_user)){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } else if($new_amt_user <= 0){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } 

          $yield_duration_info = "You make " . $new_amt_user_str . " every " . $yield_duration . "days on the total of the shares you are about to buy.";

                unset($shares_conversion);
                unset($new_amt_user);
                unset($new_amt_user_currency);
                unset($new_amt_user_str);
                unset($convert_amt);


                if($total_num_of_shares_on_fp < $item_order_quantity){
                    $next  = array(
                            'hit_status' => "6"
                            );
                    array_push($newsfeedReturn["hits"], $next); 

    $subject = "STRANGE ACTIVITY ON FISHPOTT";
    $message = "SOME IS TRYING TO BUY MORE SHARES THAN WHAT IS ON FISHPOTT. Buyer ID : " . $myid . "\n Item News ID : " . $generic_item_news_id . "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);

                    echo json_encode($newsfeedReturn); exit;

                } //END

        $convert_amt = floatval($item_price);

              if($seller_currency == "GHS" || $seller_currency == "Ghc") {

                $seller_country = "Ghana";

              } elseif($seller_currency == "GBP") {

                $seller_country = "United Kingdom";

              } else {

                $seller_country = "USA";
              }
              $i_country = "Ghana";
              $shares_conversion = 1;
          include(ROOT_PATH . 'inc/android_currency_converter.php');
                if(!isset($new_amt_user)){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } else if($curr_max_price < $new_amt_user){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                }  else if($new_amt_user <= 0){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } 

                unset($shares_conversion);
                unset($new_amt_user);
                unset($new_amt_user_currency);
                unset($new_amt_user_str);
                unset($convert_amt);


              } else {

                $next  = array(
                          'hit_status' => "0"
                          );
                array_push($newsfeedReturn["hits"], $next); 
                echo json_encode($newsfeedReturn); exit;
              }

/*************************** CALCULATE TOTAL CHARGE HERE **********************************/


        $convert_amt = floatval($item_price);

              if($seller_currency == "GHS" || $seller_currency == "Ghc") {

                $seller_country = "Ghana";

              } elseif($seller_currency == "GBP") {

                $seller_country = "United Kingdom";

              } else {

                $seller_country = "USA";
              }
              $i_country = $mycountry;
              $shares_conversion = 1;
          include(ROOT_PATH . 'inc/android_currency_converter.php');
                if(!isset($new_amt_user)){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } else if($curr_max_price < $new_amt_user){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                }  else if($new_amt_user <= 0){

                    $next  = array(
                            'hit_status' => "7"
                            );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } 
                $item_price_inmycurrency = $new_amt_user_str;

                unset($shares_conversion);
                unset($new_amt_user);
                unset($new_amt_user_currency);
                unset($new_amt_user_str);
                unset($convert_amt);

        $total_item_quantity_price = $item_order_quantity * $item_price;
        $convert_amt = floatval($total_item_quantity_price);

              if($seller_currency == "GHS" || $seller_currency == "Ghc") {

                $seller_country = "Ghana";

              } elseif($seller_currency == "GBP") {

                $seller_country = "United Kingdom";

              } else {

                $seller_country = "USA";
              }
              $i_country = "Ghana";              
              $shares_conversion = 1;
          include(ROOT_PATH . 'inc/android_currency_converter.php');

              $new_amt_cedis = $new_amt_user;

                unset($shares_conversion);
                unset($new_amt_user);
                unset($new_amt_user_currency);
                unset($new_amt_user_str);
                unset($convert_amt);

        $total_item_quantity_price = $item_order_quantity * $item_price;
        $convert_amt = floatval($total_item_quantity_price);

              if($seller_currency == "GHS" || $seller_currency == "Ghc") {

                $seller_country = "Ghana";

              } elseif($seller_currency == "GBP") {

                $seller_country = "United Kingdom";

              } else {

                $seller_country = "USA";
              }
              $i_country = $mycountry;

          include(ROOT_PATH . 'inc/android_currency_converter.php');


          
			              	$next  = array(
					                      'hit_status' => "1",
					                      'currency' => $new_amt_user_currency,
                                'item_price_for_one' => $item_price_inmycurrency,
					                      'total_charge_str' => $new_amt_user_str,
                                'yield_duration_info' => $yield_duration_info,
                                'total_charge_num' => $new_amt_user,
                                'total_charge_num_cedis' => $new_amt_cedis
					                      );
						        array_push($newsfeedReturn["hits"], $next);	
			    				echo json_encode($newsfeedReturn); exit;

              } else {

              	$next  = array(
		                      'hit_status' => "0"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

              }

    				echo json_encode($newsfeedReturn); exit;

          } // END OF PASSWORD CHECK
    }
}