<?php

if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['mycountry']) && trim($_POST['mycountry']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $mycountry = mysqli_real_escape_string($mysqli, $_POST['mycountry']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $mycountry = trim($mycountry);

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
                      if($mycountry == "Ghana"){

                        $currency_rate = $coins_GHS;
                        $currency_sign = "Ghc";
                        
                      } else if($mycountry == "United Kingdom"){

                        $currency_rate = $coins_GBP;
                        $currency_sign = "GBP";

                      } else {

                        $currency_rate = $coins_USD;
                        $currency_sign = "USD";
                        
                      }
                    } else {

                        echo "Exchange rate for pearls could not be determined. Please try again later"; exit;
                    }

                    $query = "SELECT * FROM fa_misika_faha WHERE investor_id = '$myid'";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                    } else {

                        echo "Please set your SETTLEMENT ACCOUNT where your payments can be processed and sent to."; exit;
                    }


              $query = "SELECT net_worth, coins_secure_datetime, fcm_token, fcm_token_web, fcm_token_ios, first_name, last_name, verified_tag, profile_picture, investor_id FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $last_login = trim($row["coins_secure_datetime"]);
                      $net_worth = trim($row["net_worth"]);
                      $net_worth = intval($net_worth);
                      $amount_to_be_paid = floatval($currency_rate) * $net_worth;

                      if($amount_to_be_paid < 20){

                        echo "Cashout cannot be less than 20 " . $currency_sign . ". Get more pearls to cash out."; exit;
                      }


                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;

$query = "UPDATE investor SET net_worth = 0 WHERE investor_id = '$myid'";
                      $result = $mysqli->query($query);

                      if($result == true){

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott Cashout";
  $not_picture = "http://fishpott.com/pic_upload/uploads/2017-12-161513439813.png"; 
  $myalert = "Cashout made for " . $net_worth . " pott pearls to equivalent " . $amount_to_be_paid . " " . $currency_sign . " Expect settlement within 24 hours." ;

  $fields = array(
        "registration_ids" => $all_keys,
        "priority" => "normal",
          'data' => array(
            'notification_type' => "general_notification",
            'not_type_real' => "linkup",
            'not_pic' => $not_picture,
            'not_title' => $myalert,
            'not_message' => $myalert,
            'not_image' => "",
            'not_video' => "",
            'not_text' => $myalert, 
            'not_pott_or_newsid' => "fishpot_inc", 
            'pott_name' => "fishpot_inc", 
            'not_time' => $today  
            )
          );

  $payload = json_encode($fields);

  if($key != "" && $inputtor_id != $myid){

  $curl_session = curl_init();

  curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
  curl_setopt($curl_session, CURLOPT_POST, true);
  curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

  $curl_result = curl_exec($curl_session);

                        include(ROOT_PATH . 'inc/db_connect.php');
                        $table_name = "nkae";

                        $column1_name = "wo_id";
                        $column2_name = "orno_id";
                        $column3_name = "type";
                        $column4_name = "info_1";
                        $column5_name = "asem_id";

                        $column1_value = $myid;
                        $column2_value = "";
                        $column3_value = "poach";
                        $column4_value = "";
                        $column5_value = "";

                        $pam1 = "s";
                        $pam2 = "s";
                        $pam3 = "s";
                        $pam4 = "s";
                        $pam5 = "s";

                        include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
                        include(ROOT_PATH . 'inc/db_connect.php');

                    $message = "CashOut- User ID : " . $myid . "  : " . $myalert;

                    $table_name = "contact";
                    $column1_name = "user";
                    $column2_name = "subject";
                    $column3_name = "message";

                    $column1_value = "(User ID : " . $myid . " )";
                    $column2_value = "CashOut";
                    $column3_value = $message;
                    $pam1 = "s";
                    $pam2 = "s";
                    $pam3 = "s";
                    include(ROOT_PATH . 'inc/insert3_prepared_statement.php');
                    include(ROOT_PATH . 'inc/db_connect.php');

                      $headers = "From: <info@fishpott.com>FishPott App";
                      //mail("info@fishpott.com",$subject,$message,  $headers);
                      mail("fishpottcompany@gmail.com","Cashout",$message,  $headers);

  }

//////////////////////    FCM  END      /////////////////////////
                        echo $myalert . " Ensure you have set a settlement account to receive funds";

                      } else {
                        echo "Cashout failed.. Try later...";
                      }


                    } else {

                        echo "Your Pott could not be found. Contact FishPot Inc. if this continues"; exit;
                    }

            
                }


          }/////// END OF PASSWORD CHECK

        }
