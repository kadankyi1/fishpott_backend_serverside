<?php

if(
isset($_POST['myid']) && trim($_POST['myid']) != "" 
&& isset($_POST['mypass']) && trim($_POST['mypass']) != "" 
&& isset($_POST['total_items']) && trim($_POST['total_items']) != "" 
&& isset($_POST['item_newsid_1']) && trim($_POST['item_newsid_1']) != "" 
&& isset($_POST['item_quantity_1']) && trim($_POST['item_quantity_1']) != "" 
&& isset($_POST['adetor_receiver_name']) && trim($_POST['adetor_receiver_name']) != "" 
&& isset($_POST['adetor_receiver_phone']) && trim($_POST['adetor_receiver_phone']) != "" 
&& isset($_POST['adetor_delivery_address']) && trim($_POST['adetor_delivery_address']) != ""
&& isset($_POST['adetor_delivery_type']) && trim($_POST['adetor_delivery_type']) != "" 
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
$total_items = mysqli_real_escape_string($mysqli, $_POST['total_items']);
$item_newsid_1 = mysqli_real_escape_string($mysqli, $_POST['item_newsid_1']);
$item_quantity_1 = mysqli_real_escape_string($mysqli, $_POST['item_quantity_1']);
$adetor_receiver_name = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_name']);
$adetor_receiver_phone = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_phone']);
$adetor_delivery_address = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_address']);
$adetor_delivery_type = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_type']);
$adetor_currency = mysqli_real_escape_string($mysqli, $_POST['adetor_currency']);
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
    $total_items = intval($total_items);
    $adetor_receiver_name = trim($adetor_receiver_name);
    $adetor_receiver_phone = trim($adetor_receiver_phone);
    $adetor_delivery_address = trim($adetor_delivery_address);
    $adetor_delivery_type = trim($adetor_delivery_type);
    $adetor_currency = trim($adetor_currency);
    $adetor_price_per_item = trim($adetor_price_per_item);
    $delivery_charge_num = trim($delivery_charge_num);
    $delivery_charge_num = floatval($delivery_charge_num);
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

                $adetor_type = "up4sale";

                if($adetor_type == "up4sale"){

            $all_news_id = "";
            $success_news_id = "";
            $failed_news_id = "";
            $all_seller_ids = array();

            //echo "total_items : " . $total_items;

            for($y = 1; $y <= $total_items; $y++){

                $item_newsid_index = "item_newsid_" . strval($y);
                $item_quantity_index = "item_quantity_" . strval($y);
                $adetor_news_id = mysqli_real_escape_string($mysqli, $_POST[$item_newsid_index]);
                $item_quantity = intval($_POST[$item_quantity_index]);

                if($y == $total_items){
                    $all_news_id = $all_news_id . "," . $adetor_news_id;
                } else if ($y == 1){
                    $all_news_id = $adetor_news_id;
                } else {
                    $all_news_id = $all_news_id . "," . $adetor_news_id;
                }

                //echo $item_newsid_index . " : " . $adetor_news_id;


                $query = "SELECT item_quantity, number_sold, seller_id FROM up4sale WHERE up4sale_news_id = '$adetor_news_id' ";

                  $result = $mysqli->query($query);
                  if (mysqli_num_rows($result) != "0") {



                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $db_seller_id = trim($row["seller_id"]);
                        $all_seller_ids[$y] = $db_seller_id;
                        $db_item_quantity = trim($row["item_quantity"]);
                        $db_item_quantity = intval($db_item_quantity);
                        $db_number_sold = trim($row["number_sold"]);
                        $db_number_sold = intval($db_number_sold) + $item_quantity;
                        $db_item_quantity = $db_item_quantity - $item_quantity;
                        if($db_item_quantity <= 0){

                            if($db_item_quantity < 0){

                                if($y == $total_items){
                                    $failed_news_id = $failed_news_id . "," . $adetor_news_id;
                                } else if($y == 1){
                                    $failed_news_id = $adetor_news_id;
                                } else {
                                    $failed_news_id = $failed_news_id . "," . $adetor_news_id;
                                }

                            } elseif($db_item_quantity >= 0) {

                                if($y == $total_items){
                                    $success_news_id = $success_news_id . "," . $adetor_news_id;
                                } else if($y == 1){
                                    $success_news_id = $adetor_news_id;
                                }  else {
                                  $success_news_id = $success_news_id . "," . $adetor_news_id;
                                }
                            }


                            $sale_status = 1;

                        } else {

                                if($y == $total_items){
                                    $success_news_id = $success_news_id . "," . $adetor_news_id;
                                } else if($y == 1){
                                    $success_news_id = $adetor_news_id;
                                }   else {
                                  $success_news_id = $success_news_id . "," . $adetor_news_id;
                                }

                            $sale_status = 0;

                        }

            $query = "UPDATE up4sale SET number_sold = $db_number_sold,  sale_status = $sale_status WHERE up4sale_news_id = '$adetor_news_id'";
            $result = $mysqli->query($query);

                } else {

                    if($y == $total_items){
                        $failed_news_id = $failed_news_id . "," . $adetor_news_id;
                    } elseif($y == 1){
                        $failed_news_id = $adetor_news_id;
                    } else {
                        $failed_news_id = $failed_news_id . "," . $adetor_news_id;
                    }
                }


            }



                }

            if($adetor_status_code != "1"){

        $subject = "New FAILED PURCHASE ON FISHPOTT (" . $adetor_status_message . ")";
        $message = "Buyer ID : " . $myid . "\n Buyer Name : " . $dbfull_name . "\n ItemS News ID : " . $all_news_id;

              $headers = "From: <info@fishpott.com>FishPott App";
              mail("info@fishpott.com",$subject,$message
                ,  $headers);
              exit;


            }


            $cart_id = uniqid() . "_" . uniqid();
            $all_adetor_ids = "";
        for($y = 1; $y <= $total_items; $y++){

            $item_newsid_index = "item_newsid_" . strval($y);
            $item_quantity_index = "item_quantity_" . strval($y);

            $adetor_news_id = mysqli_real_escape_string($mysqli, $_POST[$item_newsid_index]);

            $item_quantity = intval($_POST[$item_quantity_index]);

            $short_id = uniqid();
            $seller_id = $all_seller_ids[$y];
            $adetor_type = "up4sale";


            if($y == $total_items){
                $all_adetor_ids = $all_adetor_ids . "," . $short_id;
            } else if($y == 1){
                $all_adetor_ids = $short_id;
            } else {
                $all_adetor_ids = $all_adetor_ids . "," . $short_id;
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
            $column7_name = "cart_id";
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
            $column7_value = $cart_id;
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

                $not_text = $my_full_name . " bought an item from your pott's yardsale";


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

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$inputtor_id'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                    //echo $done;

                      $row = $result->fetch_array(MYSQLI_ASSOC);

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

            }

        }
    }
                if(trim($failed_news_id) != ""){

                    $not_text = "Cart purchase is complete and FishPott will have your items delivered. Transaction code is " . $cart_id . " . Some items were sold out. You will be reimbursed for those items";

                } else {

                    $not_text = "Your yardsale purchase from your cart is complete and FishPott will have your items delivered. Your transaction code is " . $cart_id;

                }


                //echo "all_adetor_ids" . " : " . $all_adetor_ids;
                //echo "success_news_id" . " : " . $success_news_id;
                //echo "failed_news_id" . " : " . $failed_news_id;

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

                        $table_name = "adetor_cart_sales";

                        $column1_name = "cart_id";
                        $column2_name = "all_items_newsids";
                        $column3_name = "success_items_newsids";
                        $column4_name = "failed_items_newsids";
                        $column5_name = "total_number_items";
                        $column6_name = "all_adetor_ids";

                        $column1_value = $cart_id;
                        $column2_value = $all_news_id;
                        $column3_value = $success_news_id;
                        $column4_value = $failed_news_id;
                        $column5_value = $total_items;
                        $column6_value = $all_adetor_ids;

                        $pam1 = "s";
                        $pam2 = "s";
                        $pam3 = "s";
                        $pam4 = "s";
                        $pam5 = "i";
                        $pam6 = "s";

                        include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
                        include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                    echo 1;

                      $row = $result->fetch_array(MYSQLI_ASSOC);

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
			          	'not_pic' => $my_profile_picture,
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

//////////////////////    FCM  END   SECOND   /////////////////////////  

                  } // TOKEN FETCH END SECOND

          } // END OF PASSWORD CHECK

        }

    }
