<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['victim_pottname']) && trim($_POST['victim_pottname']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $victim_pottname = mysqli_real_escape_string($mysqli, $_POST['victim_pottname']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $victim_pottname = trim($victim_pottname);
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


                $query = "SELECT net_worth, coins_secure_datetime, fcm_token, fcm_token_web, fcm_token_ios, first_name, last_name, verified_tag, profile_picture, investor_id FROM investor WHERE pot_name = '$victim_pottname'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $victim_investor_id = trim($row["investor_id"]);
                      $last_login = trim($row["coins_secure_datetime"]);
                      $victim_pearls = trim($row["net_worth"]);
              		  
                      $victim_pearls = intval($victim_pearls);
                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;

                      $now = time(); // or your date as well
                      $your_date = strtotime($last_login);
                      $datediff = $now - $your_date;

                      $diff = (floor($datediff / (60 * 60 * 24)) + 1);

                      $query = "SELECT date_time FROM newsfeed WHERE inputtor_id = '$victim_investor_id' ORDER BY sku DESC";   

                      $result = $mysqli->query($query);
                          
                      if (mysqli_num_rows($result) != 0) {

                            $row = $result->fetch_array(MYSQLI_ASSOC);
                            $victim_last_news_date_time = trim($row["date_time"]);

                            $your_date2 = strtotime($victim_last_news_date_time);
                            $datediff2 = $now - $your_date2;

                            $diff2 = (floor($datediff2 / (60 * 60 * 24)) + 1);

                        } else {
                          $diff2 = 5;
                        }

                    } else {

                        echo "This Pott could not be found"; exit;
                    }

                    $query = "SELECT status FROM awiawia_day WHERE sku = 1";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $poach_day_status = trim($row["status"]);
                      $poach_day_status = intval($poach_day_status);
                      if($poach_day_status == 0 || $diff <= 0){
                          $poach_day_status = 0;
                      } else {
                        $poach_day_status = 1;
                      }

                    } else {
                        echo "Poach Day Status not detected"; exit;
                    }

/*
                        echo "POACH DAY STATUS : " . $poach_day_status . "\n<br>";
                        echo "LAST SEEN DATE : " . $last_login . "\n<br>";
                        echo "LAST SEEN DAYS PAST : " . $diff . "\n<br>";
                        echo "LAST NEWS DATE : " . $victim_last_news_date_time . "\n<br>";
                        echo "LAST NEWS DAYS PAST: " . $diff2;  exit;
*/
                $query = "SELECT * FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                    if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $my_pearls = trim($row["net_worth"]);
                      $my_pearls = intval($my_pearls);

                      $poacher_pottname = trim($row["pot_name"]);
                      $linkee_profile_picture = trim($row["profile_picture"]);

			          if (!file_exists("../pic_upload/" . $linkee_profile_picture)) {

			          		$linkee_profile_picture = "";
		        		} else {

							$linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $linkee_profile_picture; 
		        		}


                    } else {

                        echo "This Pott could not be found"; exit;
                    }

                    if($victim_pearls < 20){


                      echo "This pott is broke. Why bother?.."; exit;

                    }

                    if($diff > 2 || $poach_day_status == 1 || ($diff2 >= 6 && $diff <= 0)){
                        //echo "ABOUT TO POACH"; exit;
                      if($victim_pearls > 20){

                          $poach_amt = mt_rand(20, $victim_pearls);

                      } else {

                        $poach_amt = $victim_pearls;

                      }

                      $my_pearls = $my_pearls + $poach_amt;
                      $victim_pearls = $victim_pearls - $poach_amt;

$query = "UPDATE investor SET net_worth = $victim_pearls WHERE pot_name = '$victim_pottname'";
                      $result = $mysqli->query($query);

                      if($result == true){

                        /////////
$query = "UPDATE investor SET net_worth = $my_pearls WHERE investor_id = '$myid'";
                      $result = $mysqli->query($query);

                      if($result == true){

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "Poach";

  $myalert = $dbfull_name . " just poached your pott pearls. You now have " . $victim_pearls . " pott pearls";

  $fields = array(
        "registration_ids" => $all_keys,
        "priority" => "normal",
          'data' => array(
          	'notification_type' => "general_notification",
          	'not_type_real' => "linkup",
          	'not_pic' => $linkee_profile_picture,
          	'not_title' => "You've been poached",
          	'not_message' => $myalert,
          	'not_image' => "",
          	'not_video' => "",
          	'not_text' => $myalert, 
          	'not_pott_or_newsid' => "", 
          	'pott_name' => $poacher_pottname, 
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

  }

//////////////////////    FCM  END      /////////////////////////
                        echo "Nice :-). " . $poach_amt . " pearls poached.. Get enough pearls and you can use them to shop on fishpot_inc";

                      } else {
                        echo "Poach failed.. Try later...";
                      }
                        /////////

                      } else {
                        echo "Poach failed.. Try later...";
                      }


                    } else {

                      echo "Oopps...Pearls are protected..";

                    }




            
                }


          }/////// END OF PASSWORD CHECK

        }
