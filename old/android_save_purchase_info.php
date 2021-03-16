<?php

if(
isset($_POST['myid']) && trim($_POST['myid']) != "" 
&& isset($_POST['mypass']) && trim($_POST['mypass']) != "" 
&& isset($_POST['adetor_news_id']) && trim($_POST['adetor_news_id']) != "" 
&& isset($_POST['adetor_receiver_name']) && trim($_POST['adetor_receiver_name']) != "" 
&& isset($_POST['adetor_receiver_phone']) && trim($_POST['adetor_receiver_phone']) != "" 
&& isset($_POST['adetor_delivery_address']) && trim($_POST['adetor_delivery_address']) != ""
&& isset($_POST['adetor_delivery_type']) && trim($_POST['adetor_delivery_type']) != "" 
&& isset($_POST['item_quantity']) && trim($_POST['item_quantity']) != "" 
&& isset($_POST['adetor_currency']) && trim($_POST['adetor_currency']) != "" 
&& isset($_POST['adetor_price_per_item']) && trim($_POST['adetor_price_per_item']) != "" 
&& isset($_POST['delivery_charge_num']) && trim($_POST['delivery_charge_num']) != "" 
&& isset($_POST['total_charge_num']) && trim($_POST['total_charge_num']) != "" 
&& isset($_POST['adetor_status_code']) && trim($_POST['adetor_status_code']) != "" 
&& isset($_POST['adetor_pay_type']) && trim($_POST['adetor_pay_type']) != "" 
&& isset($_POST['adetor_status_message']) && trim($_POST['adetor_status_message']) != "") {
require_once("config.php");

include(ROOT_PATH . 'inc/db_connect.php');

$myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
$mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
$adetor_news_id = mysqli_real_escape_string($mysqli, $_POST['adetor_news_id']);
$adetor_receiver_name = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_name']);
$adetor_receiver_phone = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_phone']);
$adetor_delivery_address = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_address']);
$adetor_delivery_type = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_type']);
$item_quantity = mysqli_real_escape_string($mysqli, $_POST['item_quantity']);
$adetor_currency = mysqli_real_escape_string($mysqli, $_POST['adetor_currency']);
$adetor_price_per_item = mysqli_real_escape_string($mysqli, $_POST['adetor_price_per_item']);
$delivery_charge_num = mysqli_real_escape_string($mysqli, $_POST['delivery_charge_num']);
$total_charge_num = mysqli_real_escape_string($mysqli, $_POST['total_charge_num']);
$adetor_status_code = mysqli_real_escape_string($mysqli, $_POST['adetor_status_code']);
$adetor_pay_type = mysqli_real_escape_string($mysqli, $_POST['adetor_pay_type']);
$adetor_status_message = mysqli_real_escape_string($mysqli, $_POST['adetor_status_message']);

if(isset($_POST['slydepay_order_id'])){

    $slydepay_order_id = mysqli_real_escape_string($mysqli, $_POST['slydepay_order_id']);

} else {
    $slydepay_order_id = "";
}


    $myid = trim($myid);
    $mypass = trim($mypass);
    $adetor_news_id = trim($adetor_news_id);
    $adetor_receiver_name = trim($adetor_receiver_name);
    $adetor_receiver_phone = trim($adetor_receiver_phone);
    $adetor_delivery_address = trim($adetor_delivery_address);
    $adetor_delivery_type = trim($adetor_delivery_type);
    $item_quantity = trim($item_quantity);
    $item_quantity = intval($item_quantity);
    $adetor_currency = trim($adetor_currency);
    $adetor_price_per_item = trim($adetor_price_per_item);
    $delivery_charge_num = trim($delivery_charge_num);
    $delivery_charge_num = floatval($delivery_charge_num);
    $total_charge_num = trim($total_charge_num);
    $total_charge_num = floatval($total_charge_num);
    $adetor_status_code = intval($adetor_status_code);
    $adetor_pay_type = trim($adetor_pay_type);
    $adetor_status_message = trim($adetor_status_message);


    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8mb4');
    $today = date("F j, Y");
    
    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

			$query = "SELECT first_name, last_name, pot_name, profile_picture FROM investor WHERE investor_id = '$myid'";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$first_name = trim($row["first_name"]);
                $last_name = trim($row["last_name"]);
                $pot_name = trim($row["pot_name"]);
                $my_full_name = $first_name . " " . $last_name;
				$not_text = $my_full_name . " bought an item from your pott";
              $my_profile_picture = trim($row["profile_picture"]);
              
	          if (!file_exists("../pic_upload/" . $my_profile_picture)) {

	          		$my_profile_picture = "";
        		} else {

					$my_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $my_profile_picture; 
        		}
			} else {
                $my_full_name = "Someone";
				$not_text = "Someone bought an item from your pott";
	          	$my_profile_picture = "";
	          	$pot_name = "";

			}

          $query = "SELECT inputtor_id, type FROM newsfeed WHERE news_id = '$adetor_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $seller_id = trim($row["inputtor_id"]);
				if(isset($_POST["adetor_type"]) && trim($_POST["adetor_type"]) != ""){

					$adetor_type = mysqli_real_escape_string($mysqli, $_POST['adetor_type']);
					$adetor_type = trim($adetor_type);

				}else {

                	$adetor_type = trim($row["type"]);

				}

                if($adetor_type == "up4sale"){

                $query = "SELECT item_quantity, number_sold FROM up4sale WHERE up4sale_news_id = '$adetor_news_id' ";

              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $db_item_quantity = trim($row["item_quantity"]);
                        $db_item_quantity = intval($db_item_quantity);
                        $db_number_sold = trim($row["number_sold"]);
                        $db_number_sold = intval($db_number_sold) + $item_quantity;
                        $db_item_quantity = $db_item_quantity - $item_quantity;
                        if($db_item_quantity <= 0){

                            $sale_status = 1;

                        } else {

                            $sale_status = 0;

                        }

        $query = "UPDATE up4sale SET number_sold = $db_number_sold,  sale_status = $sale_status WHERE up4sale_news_id = '$adetor_news_id'";
        $result = $mysqli->query($query);
            }

                } else if ($adetor_type == "event"){

                $query = "SELECT available_tics, num_of_goers, event_name FROM event WHERE event_news_id = '$adetor_news_id' ";

              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $db_event_name = trim($row["event_name"]);
                        $db_item_quantity = trim($row["available_tics"]);
                        $db_item_quantity = intval($db_item_quantity);
                        $db_num_of_goers = trim($row["num_of_goers"]);
                        $db_num_of_goers = intval($db_num_of_goers) + $item_quantity;
                        $db_item_quantity = $db_item_quantity - $item_quantity;
                        if($db_item_quantity <= 0){

                            $sale_status = 1;

                        } else {

                            $sale_status = 0;

                        }

        $query = "UPDATE event SET num_of_goers = $db_num_of_goers,  sale_status = $sale_status WHERE event_news_id = '$adetor_news_id'";
        $result = $mysqli->query($query);
            }


                } else if ($adetor_type == "fundraiser"){

                $query = "SELECT contributed_amount, target_amount FROM fundraiser WHERE f_news_id = '$adetor_news_id' ";

              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);

                        $db_contributed_amount = trim($row["contributed_amount"]);
                        $db_target_amount = trim($row["target_amount"]);

                $db_item_quantity = floatval($db_contributed_amount) + $total_charge_num;
                $db_item_quantity_real = floatval($db_contributed_amount) + $total_charge_num;
                        $db_item_quantity = $db_target_amount - $db_item_quantity;
                        if($db_item_quantity <= 0){

                            $sale_status = 1;

                        } else {

                            $sale_status = 0;

                        }

        $query = "UPDATE fundraiser SET contributed_amount = $db_item_quantity_real,  sale_status = $sale_status WHERE f_news_id = '$adetor_news_id'";
        $result = $mysqli->query($query);
            }



                } else if ($adetor_type == "shares4sale"){

                    if($adetor_status_code != "1"){
                        exit;
                    }

              $query = "SELECT parent_shares_id, sharesOnSale_id, shares4sale_owner_id, selling_price, currency, num_on_sale, number_sold, sale_status FROM shares4sale WHERE shares_news_id = '$adetor_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $sale_status = trim($row["sale_status"]);
                $db_item_quantity = trim($row["num_on_sale"]);
                $db_item_quantity = intval($db_item_quantity);
                $db_item_quantity_on_sale = $db_item_quantity;
                $db_item_quantity_sold = trim($row["number_sold"]);
                $db_item_quantity_sold = intval($db_item_quantity_sold) + $item_quantity;
                if($db_item_quantity_sold >= $db_item_quantity){
                    $new_sale_status = 1;
                } else {
                    $new_sale_status = 0;
                }
                $item_price = trim($row["selling_price"]);
                $item_price = floatval($item_price);
                $seller_currency = trim($row["currency"]);
                $shares4sale_owner_id = trim($row["shares4sale_owner_id"]);
                $parent_shares_id = trim($row["parent_shares_id"]);
                $sharesOnSale_id = trim($row["sharesOnSale_id"]);

                if($sale_status == "1"){

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : SHARES HAVE BEEN SOLD OUT" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;

                } else if ($item_quantity > $db_item_quantity){

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : ORDER QUANTITY MORE THAN AVAILABLE ON SALE BY SELLER" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;

                } else if ($item_price <= 0){

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : PRICE IS AT ZERO" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;

                }

              $query = "SELECT num_of_shares, start_date, yield_date FROM shares_owned WHERE share_id = '$sharesOnSale_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $seller_num_of_shares = trim($row["num_of_shares"]);
                $seller_num_of_shares = intval($seller_num_of_shares);
                $new_seller_number_of_shares = $seller_num_of_shares - $item_quantity;
                $shares_start_date = trim($row["start_date"]);
                $shares_yield_date = trim($row["yield_date"]);
                if($seller_num_of_shares < $item_quantity){

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : ORDER QUANTITY MORE THAN AVAILABLE ON SALE BY SELLER" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;
                }

              } else {

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : SHARES NOT FOUND IN USERS PORTFOLIO" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;

              }

              $query = "SELECT share_name, type,  total_number, curr_max_price, yield_duration, yield_per_share FROM shares_worso WHERE parent_shares_id = '$parent_shares_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $total_num_of_shares_on_fp = trim($row["total_number"]);
                $yield_duration = trim($row["yield_duration"]);
                $yield_duration = intval($yield_duration) + 7;
                $yield_duration = strval($yield_duration);
                $share_name = trim($row["share_name"]);
                $share_type = trim($row["type"]);
                $total_num_of_shares_on_fp = intval($total_num_of_shares_on_fp);
                if($total_num_of_shares_on_fp < $item_quantity){

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : ORDER QUANTITY MORE THAN AVAILABLE ON FISHPOTT" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;


                }

            } else {

    $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
    $message = "SALE DID NOT COMPLETE. " . 
                "\n REASON : SHARES NOT FOUND ON FISHPOTT" . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;

            }

        $query = "UPDATE shares4sale SET number_sold = $db_item_quantity_sold,  sale_status = $new_sale_status WHERE shares_news_id = '$adetor_news_id'";
        $result = $mysqli->query($query);
        if($result != 1){

               $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
                $message = "SHARES NUMBER SOLD QUANTITY DID NOT UPDATE. " . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;
  
        }


        $query = "UPDATE shares_owned SET num_of_shares = $new_seller_number_of_shares WHERE share_id = '$sharesOnSale_id'";
        $result = $mysqli->query($query);
        if($result != 1){

               $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
                $message = "SHARES SOLD QUANTITY DID NOT UPDATE ON SELLERS ACCOUNT. " . 
                "\n Buyer ID : " . $myid . 
                "\n Item News ID : " . $adetor_news_id . 
                "\n QUANTITY : " . $db_item_quantity . 
                "\n CURRENCY : " . $adetor_currency . 
                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                "\n TOTAL CHARGE : " . $total_charge_num . 
                "\n Seller ID : " . $shares4sale_owner_id;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
        exit;
  
        }

            $start_date_time = date("Y-m-d");
            $start_date_time = trim($start_date_time);
            $yield_duration  = "+" . $yield_duration . " days";
            $yield_date_time = date('Y-m-d', strtotime($yield_duration));

/*********************************************************************************

      NEW SHARES ARE NAMED BY ADDING THE PARENT SHARES ID, AN UNDERSCORE, THE POTT NAME, AN UNDERSCORE, THE START DATE, AN UNDERSCORE AND END DATE


**********************************************************************************/

              $buyer_shares_id = $parent_shares_id . "_" . $pot_name . "_" . $start_date_time . "_" . $yield_date_time;

              //echo "yield_date_time : " . $yield_date_time;


              $query = "SELECT num_of_shares FROM shares_owned WHERE share_id = '$buyer_shares_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $buyer_num_of_shares = trim($row["num_of_shares"]);
                $buyer_num_of_shares = intval($buyer_num_of_shares) + $item_quantity;

                        $query = "UPDATE shares_owned SET num_of_shares = $buyer_num_of_shares WHERE share_id = '$buyer_shares_id'";
                        $result = $mysqli->query($query);
                        if($result != 1){

                               $subject = "STRANGE ACTIVITY ON FISHPOTT ( Purchase " . $adetor_status_message . ")";
                                $message = "SHARES SOLD QUANTITY DID NOT UPDATE ON BUYERS ACCOUNT. " . 
                                "\n Buyer ID : " . $myid . 
                                "\n Item News ID : " . $adetor_news_id . 
                                "\n QUANTITY : " . $db_item_quantity . 
                                "\n CURRENCY : " . $adetor_currency . 
                                "\n PRICE PER SHARE : " . $adetor_price_per_item . 
                                "\n TOTAL CHARGE : " . $total_charge_num . 
                                "\n SHARE ID : " . $buyer_shares_id . 
                                "\n Seller ID : " . $shares4sale_owner_id;

                        $headers = "From: <info@fishpott.com>FishPott App";
                        mail("info@fishpott.com",$subject,$message
                          ,  $headers);
                        exit;
                  
                        } else {

                     $table_name = "y3n_transfers";

                      $column1_name = "sender_id";
                      $column2_name = "receiver_id";
                      $column3_name = "shares_parent_id";
                      $column4_name = "date_time";
                      $column5_name = "num_shares_transfered";
                      $column6_name = "shares_parent_name";

                      $column1_value = $shares4sale_owner_id;
                      $column2_value = $myid;
                      $column3_value = $parent_shares_id;
                      $column4_value = date("Y-m-d H:i:s");
                      $column5_value = $item_quantity;
                      $column6_value = $share_name;

                      $pam1 = "s";
                      $pam2 = "s";
                      $pam3 = "s";
                      $pam4 = "s";
                      $pam5 = "i";
                      $pam6 = "s";

                      $done = 0;
                      include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
                      include(ROOT_PATH . 'inc/db_connect.php');
                      if($done != 1){

                            $subject = "STRANGE ACTIVITY ON FISHPOTT ( Purchase " . $adetor_status_message . ")";
                                            $message = "SHARES SOLD DID NOT REFLECT ON TRANSFERS TABLE. " . 
                                            "\n Buyer ID : " . $myid . 
                                            "\n Item News ID : " . $adetor_news_id . 
                                            "\n QUANTITY : " . $db_item_quantity . 
                                            "\n CURRENCY : " . $adetor_currency . 
                                            "\n PRICE PER SHARE : " . $adetor_price_per_item .
                                            "\n SHARE ID : " . $buyer_shares_id .  
                                            "\n TOTAL CHARGE : " . $total_charge_num . 
                                            "\n Seller ID : " . $shares4sale_owner_id;

                                    $headers = "From: <info@fishpott.com>FishPott App";
                                    mail("info@fishpott.com",$subject,$message
                                      ,  $headers);
                                    exit;
                        }
                            
                        }
            } else {

                      $table_name = "shares_owned";

                      $column1_name = "share_id";
                      $column2_name = "parent_shares_id";
                      $column3_name = "share_name";
                      $column4_name = "owner_id";
                      $column5_name = "cost_price_per_share";
                      $column6_name = "num_of_shares";
                      $column7_name = "start_date";
                      $column8_name = "yield_date";
                      $column9_name = "shares_type";
                      
                      $column1_value = $buyer_shares_id;
                      $column2_value = $parent_shares_id;
                      $column3_value = $share_name;
                      $column4_value = $myid;
                      $column5_value = 0.00;
                      $column6_value = $item_quantity;
                      $column7_value = $start_date_time;
                      $column8_value = $yield_date_time;
                      $column9_value = $share_type;

                      $pam1 = "s";
                      $pam2 = "s";
                      $pam3 = "s";
                      $pam4 = "s";
                      $pam5 = "d";
                      $pam6 = "s";
                      $pam7 = "s";
                      $pam8 = "s";
                      $pam9 = "s";

                      $done = 0;
                      include(ROOT_PATH . 'inc/insert9_prepared_statement.php');
                      include(ROOT_PATH . 'inc/db_connect.php');

                      if($done == 1){

                      $table_name = "y3n_transfers";

                      $column1_name = "sender_id";
                      $column2_name = "receiver_id";
                      $column3_name = "shares_parent_id";
                      $column4_name = "date_time";
                      $column5_name = "num_shares_transfered";
                      $column6_name = "shares_parent_name";

                      $column1_value = $shares4sale_owner_id;
                      $column2_value = $myid;
                      $column3_value = $parent_shares_id;
                      $column4_value = date("Y-m-d H:i:s");
                      $column5_value = $item_quantity;
                      $column6_value = $share_name;

                      $pam1 = "s";
                      $pam2 = "s";
                      $pam3 = "s";
                      $pam4 = "s";
                      $pam5 = "i";
                      $pam6 = "s";

                      $done = 0;
                      include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
                      include(ROOT_PATH . 'inc/db_connect.php');
                      if($done != 1){

                            $subject = "STRANGE ACTIVITY ON FISHPOTT ( Purchase " . $adetor_status_message . ")";
                                            $message = "SHARES SOLD DID NOT REFLECT ON TRANSFERS TABLE. " . 
                                            "\n Buyer ID : " . $myid . 
                                            "\n Item News ID : " . $adetor_news_id . 
                                            "\n QUANTITY : " . $db_item_quantity . 
                                            "\n CURRENCY : " . $adetor_currency . 
                                            "\n PRICE PER SHARE : " . $adetor_price_per_item .
                                            "\n SHARE ID : " . $buyer_shares_id .  
                                            "\n TOTAL CHARGE : " . $total_charge_num . 
                                            "\n Seller ID : " . $shares4sale_owner_id;

                                    $headers = "From: <info@fishpott.com>FishPott App";
                                    mail("info@fishpott.com",$subject,$message
                                      ,  $headers);
                                    exit;
                        }
                    } else  {

                               $subject = "STRANGE ACTIVITY ON FISHPOTT ( Purchase " . $adetor_status_message . ")";
                                $message = "SHARES SOLD QUANTITY DID NOT UPDATE ON BUYERS ACCOUNT. " . 
                                "\n Buyer ID : " . $myid . 
                                "\n Item News ID : " . $adetor_news_id . 
                                "\n QUANTITY : " . $db_item_quantity . 
                                "\n CURRENCY : " . $adetor_currency . 
                                "\n PRICE PER SHARE : " . $adetor_price_per_item .
                                "\n SHARE ID : " . $buyer_shares_id .  
                                "\n TOTAL CHARGE : " . $total_charge_num . 
                                "\n Seller ID : " . $shares4sale_owner_id;

                        $headers = "From: <info@fishpott.com>FishPott App";
                        mail("info@fishpott.com",$subject,$message
                          ,  $headers);
                        exit;
                      
                      } 
                  }
            } else {

                            $subject = "STRANGE ACTIVITY ON FISHPOTT (" . $adetor_status_message . ")";
                            $message = "SALE DID NOT COMPLETE. " . 
                                        "\n REASON : SHARES NOT FOUND" . 
                                        "\n Buyer ID : " . $myid . 
                                        "\n Item News ID : " . $adetor_news_id;

                                $headers = "From: <info@fishpott.com>FishPott App";
                                mail("info@fishpott.com",$subject,$message
                                  ,  $headers);
                                exit;

                 }
             } else if ($adetor_type == "advert"){

                        $date_time = date("Y-m-d");
                        $date_time = trim($date_time);

                        $short_id = uniqid();

                        include(ROOT_PATH . 'inc/db_connect.php');
                        $table_name = "ad_adetor";

                        $column1_name = "ad_adetor_id";
                        $column2_name = "ad_adetor_news_id";
                        $column3_name = "ad_adetor_target_country";
                        $column4_name = "ad_adetor_target_age_start";
                        $column5_name = "ad_adetor_target_age_end";
                        $column6_name = "ad_adetor_duration";
                        $column7_name = "ad_adetor_start_date";

                        $column1_value = $short_id;
                        $column2_value = $adetor_news_id;
                        $column3_value = $adetor_delivery_type;
                        $column4_value = intval($adetor_receiver_name);
                        $column5_value = intval($adetor_receiver_phone);
                        $column6_value = intval($item_quantity);
                        $column7_value = $date_time;

                        $pam1 = "s";
                        $pam2 = "s";
                        $pam3 = "s";
                        $pam4 = "i";
                        $pam5 = "i";
                        $pam6 = "i";
                        $pam7 = "s";

                        include(ROOT_PATH . 'inc/insert7_prepared_statement.php');
                        include(ROOT_PATH . 'inc/db_connect.php');
                        
                        if($done != 1){
                            $subject = "STRANGE ACTIVITY ON FISHPOTT ( Purchase " . $adetor_status_message . ")";
                                            $message = "ADVERT FAILED TO SAVE TO PLACEMENT TABLE. " . 
                                            "\n Buyer ID : " . $myid . 
                                            "\n Item News ID : " . $adetor_news_id . 
                                            "\n PLACEMENT DAYS : " . $item_quantity . 
                                            "\n CURRENCY : " . $adetor_currency;

                                    $headers = "From: <info@fishpott.com>FishPott App";
                                    mail("info@fishpott.com",$subject,$message
                                      ,  $headers);
                                    exit;
                        }

             }
            

            } else {

                	$adetor_type = "unknown";
                	$seller_id = "unknown";
            }

            if($adetor_status_code != "1"){

        $subject = "New FAILED PURCHASE ON FISHPOTT (" . $adetor_status_message . ")";
        $message = "Buyer ID : " . $myid . "\n Buyer Name : " . $dbfull_name . "\n Item News ID : " . $adetor_news_id . "\n Seller ID : " . $inputtor_id;

              $headers = "From: <info@fishpott.com>FishPott App";
              mail("info@fishpott.com",$subject,$message
                ,  $headers);
              exit;


            }


            if($adetor_type == "event" && $item_quantity > 1){

                $not_text_event = "Your  tickets purchase for " . $db_event_name . " is complete. Your ticket codes are ";

                for ($i=0; $i < $item_quantity ; $i++) { 


            $short_id = uniqid();
            if($i == $item_quantity - 1){

            $not_text_event = $not_text_event . $short_id;

            } else {
            	$not_text_event = $not_text_event . $short_id . ", ";
            }

            $date_time = date("Y-m-d H:i:s");
            $date_time = trim($date_time);

            $table_name = "adetor";

            $column1_name = "adetor_news_id";
            $column2_name = "adetor_id_short";
            $column3_name = "adetor_type";
            $column4_name = "seller_id";
            $column5_name = "buyer_id";
            $column6_name = "transaction_currency";
            $column7_name = "price_per_item_num";
            $column8_name = "item_quantity";
            $column9_name = "receiver_name";
            $column10_name = "receiver_phone";
            $column11_name = "delivery_address";
            $column12_name = "delivery_type";
            $column13_name = "delivery_charge_num";
            $column14_name = "total_charge_num";
            $column15_name = "adetor_pay_type";
            $column16_name = "date_time";
            $column17_name = "adetor_status_code";
            $column18_name = "adetor_status_message";
            $column19_name = "slydepay_order_id";

            $column1_value = $adetor_news_id;
            $column2_value = $short_id;
            $column3_value = $adetor_type;
            $column4_value = $seller_id;
            $column5_value = $myid;
            $column6_value = $adetor_currency;
            $column7_value = $adetor_price_per_item;
            $column8_value = 1;
            $column9_value = $adetor_receiver_name;
            $column10_value = $adetor_receiver_phone;
            $column11_value = $adetor_delivery_address;
            $column12_value = $adetor_delivery_type;
            $column13_value = $delivery_charge_num;
            $column14_value = $adetor_price_per_item;
            $column15_value = $adetor_pay_type;
            $column16_value = $date_time;
            $column17_value = $adetor_status_code;
            $column18_value = $adetor_status_message;
            $column19_value = $slydepay_order_id;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "i";
            $pam9 = "s";
            $pam10 = "s";
            $pam11 = "s";
            $pam12 = "s";
            $pam13 = "d";
            $pam14 = "d";
            $pam15 = "s";
            $pam16 = "s";
            $pam17 = "i";
            $pam18 = "s";
            $pam19 = "s";

            include(ROOT_PATH . 'inc/insert19_prepared_statement.php');

                  include(ROOT_PATH . 'inc/db_connect.php');


                }



            } else {

                //echo "here 2";

            if(!isset($short_id)){
                $short_id = uniqid();
            }
            $date_time = date("Y-m-d H:i:s");
            $date_time = trim($date_time);

            $table_name = "adetor";

            $column1_name = "adetor_news_id";
            $column2_name = "adetor_id_short";
            $column3_name = "adetor_type";
            $column4_name = "seller_id";
            $column5_name = "buyer_id";
            $column6_name = "transaction_currency";
            $column7_name = "price_per_item_num";
            $column8_name = "item_quantity";
            $column9_name = "receiver_name";
            $column10_name = "receiver_phone";
            $column11_name = "delivery_address";
            $column12_name = "delivery_type";
            $column13_name = "delivery_charge_num";
            $column14_name = "total_charge_num";
            $column15_name = "adetor_pay_type";
            $column16_name = "date_time";
            $column17_name = "adetor_status_code";
            $column18_name = "adetor_status_message";
            $column19_name = "slydepay_order_id";

            $column1_value = $adetor_news_id;
            $column2_value = $short_id;
            $column3_value = $adetor_type;
            $column4_value = $seller_id;
            $column5_value = $myid;
            $column6_value = $adetor_currency;
            $column7_value = $adetor_price_per_item;
            $column8_value = $item_quantity;
            $column9_value = $adetor_receiver_name;
            $column10_value = $adetor_receiver_phone;
            $column11_value = $adetor_delivery_address;
            $column12_value = $adetor_delivery_type;
            $column13_value = $delivery_charge_num;
            $column14_value = $total_charge_num;
            $column15_value = $adetor_pay_type;
            $column16_value = $date_time;
            $column17_value = $adetor_status_code;
            $column18_value = $adetor_status_message;
            $column19_value = $slydepay_order_id;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "i";
            $pam9 = "s";
            $pam10 = "s";
            $pam11 = "s";
            $pam12 = "s";
            $pam13 = "d";
            $pam14 = "d";
            $pam15 = "s";
            $pam16 = "s";
            $pam17 = "i";
            $pam18 = "s";
            $pam19 = "s";

            include(ROOT_PATH . 'inc/insert19_prepared_statement.php');

                  include(ROOT_PATH . 'inc/db_connect.php');

            }




		    $query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$adetor_news_id'";

			    $result = $mysqli->query($query);
				
				if (mysqli_num_rows($result) != "0") {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$inputtor_id = trim($row["inputtor_id"]);

		$subject = "New PURCHASE ON FISHPOTT (" . $adetor_status_message . ")";
		$message = "Buyer ID : " . $myid . "\n Buyer Name : " . $dbfull_name . "\n Item News ID : " . $adetor_news_id . "\n Seller ID : " . $inputtor_id;

			  $headers = "From: <info@fishpott.com>FishPott App";
			  mail("info@fishpott.com",$subject,$message
			  	,  $headers);

              if($adetor_type == "up4sale"){

                $not_text = $my_full_name . " bought an item from your pott's yardsale";

              } else if ($adetor_type == "shares4sale"){

                $not_text = $my_full_name . " bought shares from your pott's stock sales";

              } else if ($adetor_type == "fundraiser"){

                $not_text = $my_full_name . " contributed to your fundraiser";

              } else if ($adetor_type == "event"){

                $not_text = $my_full_name . " bought a ticket for an event on your pott";

              } else if ($adetor_type == "advert"){
                
                $not_text = "An advert has been placed for your news, The advert placement purchase is complete and under review.";

              } else {

                $not_text = "An advert has been placed for your news";
                //$not_text = "There's been a transaction relating to your pott";

              }


  					//if($inputtor_id != $myid){

			            include(ROOT_PATH . 'inc/db_connect.php');
			            $table_name = "nkae";

			            $column1_name = "wo_id";
			            $column2_name = "orno_id";
			            $column3_name = "type";
			            $column4_name = "info_1";
			            $column5_name = "asem_id";

			            $noti_type = "purchase";

			            $column1_value = $inputtor_id;
			            $column2_value = $myid;
			            $column3_value = $noti_type;
			            $column4_value = $not_text;
			            $column5_value = $adetor_news_id;

			            $pam1 = "s";
			            $pam2 = "s";
			            $pam3 = "s";
			            $pam4 = "s";
			            $pam5 = "s";

			            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
			            include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT investing_points, fcm_token, fcm_token_web, fcm_token_ios, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$inputtor_id'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                    echo $done;

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                    $investing_points = intval($row["investing_points"]) + 1;
                    $key = trim($row["fcm_token"]);
                    $fcm_token_web = trim($row["fcm_token_web"]);
                    $fcm_token_ios = trim($row["fcm_token_ios"]);
                    $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                    $key = $key . $fcm_token_ios . $fcm_token_web;

                    $seller_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
                	$seller_pot_name = trim($row["pot_name"]);
              		$seller_profile_picture = trim($row["profile_picture"]);
              
	          if (!file_exists("../pic_upload/" . $seller_profile_picture)) {

	          		$seller_profile_picture = "";
        		} else {

					$seller_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $seller_profile_picture; 
        		}

//////////////////////    FCM  START      /////////////////////////

			  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

			  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

			  $headers = array(
			    'Authorization:key=' . $server_key, 
			    'Content-Type:application/json');

			  $title = "FishPott";

			  $myalert = $not_text;

			  $fields = array(
                    "registration_ids" => $all_keys,
                    "priority" => "normal",
			          'data' => array(
			          	'notification_type' => "general_notification",
			          	'not_type_real' => "purchase",
			          	'not_pic' => $my_profile_picture,
			          	'not_title' => $title,
			          	'not_message' => $not_text,
			          	'not_image' => "",
			          	'not_video' => "",
			          	'not_text' => $not_text, 
			          	'not_pott_or_newsid' => $adetor_news_id, 
			          	'pott_name' => $pot_name, 
                        'not_time' => $today  
			          	)
			          );

			  $payload = json_encode($fields);

			  if($key != ""){

			  	if ($adetor_type == "shares4sale"){

$query = "UPDATE investor SET investing_points = $investing_points WHERE investor_id = '$inputtor_id'";
		$result = $mysqli->query($query);

			  	}

			  $curl_session = curl_init();

			  curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
			  curl_setopt($curl_session, CURLOPT_POST, true);
			  curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
			  curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
			  curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
			  curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			  curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

			  $curl_result = curl_exec($curl_session);

			  }

//////////////////////    FCM  END      /////////////////////////  

              if($adetor_type == "up4sale"){

                $not_text = "Your yardsale purchase from " . $seller_full_name . "'s pott is complete and FishPott will have your item delivered. Your transaction code is " . $short_id;

              } else if ($adetor_type == "shares4sale"){

                $not_text = "Your shares purchase from " . $seller_full_name . " is complete. Your transaction code is " . $short_id;

              } else if ($adetor_type == "fundraiser"){

                $not_text = "Your fundraiser contribution to " . $seller_full_name . " is complete. Your transaction code is " . $short_id;

              } else if ($adetor_type == "event"){
                    if(isset($not_text_event)){

                    $not_text = $not_text_event;

                    } else {

                $not_text = "Your event ticket purchase from " . $seller_full_name . "'s pott is complete. Your " . $db_event_name . " event ticket code is " . $short_id;

                    }

              } else if ($adetor == "advert"){
                
                $not_text = "An advert has been placed for your news, The advert placement purchase is complete and under review.";

              } else {

                $not_text = "An advert has been placed for your news";
                //$not_text = "There's been a transaction relating to your pott";

              }


                    //if($inputtor_id != $myid){

                        include(ROOT_PATH . 'inc/db_connect.php');
                        $table_name = "nkae";

                        $column1_name = "wo_id";
                        $column2_name = "orno_id";
                        $column3_name = "type";
                        $column4_name = "info_1";
                        $column5_name = "asem_id";

                        $noti_type = "purchase";

                        $column1_value = $myid;
                        $column2_value = $inputtor_id;
                        $column3_value = $noti_type;
                        $column4_value = $not_text;
                        $column5_value = $adetor_news_id;

                        $pam1 = "s";
                        $pam2 = "s";
                        $pam3 = "s";
                        $pam4 = "s";
                        $pam5 = "s";

                        include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
                        include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT investing_points, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                      $my_investing_points = intval($row["investing_points"]) + 1;
                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;

//////////////////////    FCM  START      /////////////////////////

              $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

              $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

              $headers = array(
                'Authorization:key=' . $server_key, 
                'Content-Type:application/json');

              $title = "FishPott";

              $myalert = $not_text;

			  $fields = array(
                "registration_ids" => $all_keys,
                "priority" => "normal",
			          'data' => array(
			          	'notification_type' => "general_notification",
			          	'not_type_real' => "purchase",
			          	'not_pic' => $seller_profile_picture,
			          	'not_title' => $title,
			          	'not_message' => $not_text,
			          	'not_image' => "",
			          	'not_video' => "",
			          	'not_text' => $not_text, 
			          	'not_pott_or_newsid' => $adetor_news_id, 
			          	'pott_name' => $seller_pot_name, 
                        'not_time' => $today  
			          	)
			          );

              $payload = json_encode($fields);

              if($key != ""){

			  	if ($adetor_type == "shares4sale"){

$query = "UPDATE investor SET investing_points = $my_investing_points WHERE investor_id = '$myid'";
		$result = $mysqli->query($query);

			  	}

              $curl_session = curl_init();

              curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
              curl_setopt($curl_session, CURLOPT_POST, true);
              curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
              curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
              curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
              curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

              $curl_result = curl_exec($curl_session);

                  if($adetor_type == "shares4sale"){

                              $title = "FishPott";

                              $myalert = "You have new shares";
                              $not_text = $myalert;
  $seller_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/uploads/2017-12-161513439813.png"; 

                              $fields = array(
                                    "registration_ids" => $all_keys,
                                    "priority" => "normal",
                                      'data' => array(
                                        'notification_type' => "general_notification",
                                        'not_type_real' => "transfer",
                                        'not_pic' => $seller_profile_picture,
                                        'not_title' => $title,
                                        'not_message' => $not_text,
                                        'not_image' => "",
                                        'not_video' => "",
                                        'not_text' => $not_text, 
                                        'not_pott_or_newsid' => $adetor_news_id, 
                                        'pott_name' => $seller_pot_name, 
                                        'not_time' => $today  
                                        )
                                      );

                              $payload = json_encode($fields);

                              if($key != ""){

                              $curl_session = curl_init();

                              curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                              curl_setopt($curl_session, CURLOPT_POST, true);
                              curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
                              curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                              curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                              curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                              curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

                              $curl_result = curl_exec($curl_session);

                              }

                    
                  }

              }

//////////////////////    FCM  END   SECOND   /////////////////////////  

                  } // TOKEN FETCH END SECOND



        } // TOKEN FETCH END


//////////////////////////
			    }

          } // END OF PASSWORD CHECK

        }

    }
