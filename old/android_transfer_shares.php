<?php
/*********************************************************************************

      NEW SHARES ARE NAMED BY ADDING THE PARENT SHARES ID, AN UNDERSCORE, THE POTT NAME, AN UNDERSCORE, THE START DATE, AN UNDERSCORE AND END DATE


**********************************************************************************/
if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['shares_num']) && trim($_POST['shares_num']) != "" && 
  isset($_POST['shares_id']) && trim($_POST['shares_id']) != "" && 
  isset($_POST['raw_pass']) && trim($_POST['raw_pass']) != "" && 
  isset($_POST['receiver_potname']) && trim($_POST['receiver_potname']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $shares_num = mysqli_real_escape_string($mysqli, $_POST['shares_num']);
    $shares_id = mysqli_real_escape_string($mysqli, $_POST['shares_id']);
    $raw_pass = mysqli_real_escape_string($mysqli, $_POST['raw_pass']);
    $receiver_potname = mysqli_real_escape_string($mysqli, $_POST['receiver_potname']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $shares_num = trim($shares_num);
    $shares_num = intval($shares_num);
    $shares_id = trim($shares_id);
    $raw_pass = trim($raw_pass);
    $receiver_potname = trim($receiver_potname);

    if($receiver_potname == "fishpot_inc"){

    $cashout_request_mail_message = "Cashout for " . $myid . ". Shares Number : " . $shares_num . ". OLD Shares ID : " . $shares_id . ". Receiver Pottname : " . $receiver_potname . ". New Shares ID : ";

    }


  $password = $raw_pass;

  //echo "share id : " . $shares_id;

  include(ROOT_PATH . 'inc/pw_fold.php');

    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8mb4');
    $today = date("F j, Y");

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0 && $e_password == $dbpass ) {

            $query = "SELECT investor_id, pot_name, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE pot_name = '$receiver_potname'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $receiver_id = $row["investor_id"];
              $receiver_pot_name = $row["pot_name"];
              $key = trim($row["fcm_token"]);
              $fcm_token_web = trim($row["fcm_token_web"]);
              $fcm_token_ios = trim($row["fcm_token_ios"]);
              $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
              $key = $key . $fcm_token_ios . $fcm_token_web;

              $query = "SELECT num_of_shares, parent_shares_id, share_name, start_date, yield_date, shares_type FROM shares_owned WHERE share_id = '$shares_id'";   
              $result = $mysqli->query($query);
                  
              if (mysqli_num_rows($result) != 0) {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $num_of_shares = trim($row["num_of_shares"]);
    			$num_of_shares = intval($num_of_shares);
                $parent_shares_id = $row["parent_shares_id"];
                $share_name = $row["share_name"];
                $start_date = $row["start_date"];
                $yield_date = $row["yield_date"];
                $shares_type = $row["shares_type"];

                if($shares_num > $num_of_shares) {

                  echo "You can't transfer more shares than you have..."; exit;

                } else {

              $query = "SELECT total_number FROM shares_worso WHERE parent_shares_id = '$parent_shares_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $total_num_of_shares_on_fp = trim($row["total_number"]);
                $total_num_of_shares_on_fp = intval($total_num_of_shares_on_fp);
                if($total_num_of_shares_on_fp < $shares_num){


    $subject = "STRANGE ACTIVITY ON FISHPOTT (FAILED TRANSFER)";
    $message = "SOMEONE JUST TRIED TO TRANSFER A NUMBER OF SHARES THAT IS MORE THAN WHAT IS AVAILABLE ON FISHPOTT. " . 
                "\n REASON : ORDER QUANTITY MORE THAN AVAILABLE ON FISHPOTT" . 
                "\n TRANSFERER ID : " . $myid . 
                "\n RECEIVER POTTNAME : " . $receiver_potname . 
                "\n QUANTITY TO TRANSFER: " . $shares_num .  
                "\n QUANTITY AVAILABLE : " . $total_num_of_shares_on_fp;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
                  echo "You cannot have more shares than the quantity available on FishPott. This is an error, and has been recorded.."; exit;


                }

            } else {

    $subject = "STRANGE ACTIVITY ON FISHPOTT (FAILED TRANSFER)";
    $message = "SOMEONE JUST TRIED TO TRANSFER SHARES THAT ARE NO MORE AVAILABLE ON FISHPOTT" .
                "\n TRANSFERER ID : " . $myid . 
                "\n RECEIVER POTTNAME : " . $receiver_potname . 
                "\n QUANTITY TO TRANSFER: " . $shares_num;

        $headers = "From: <info@fishpott.com>FishPott App";
        mail("info@fishpott.com",$subject,$message
          ,  $headers);
                  echo "The shares you tried to transfer are no longer traded on FishPott. Please contact FishPott to resolve the issue.."; exit;

            }
                  $new_num_of_shares_investor = $num_of_shares - $shares_num;
              $query = "UPDATE shares_owned SET num_of_shares = $new_num_of_shares_investor WHERE share_id = '$shares_id' AND owner_id = '$investor_id'";
              $result = $mysqli->query($query);

              if ($result == true) {
/*********************************************************************************

      NEW SHARES ARE NAMED BY ADDING THE PARENT SHARES ID, AN UNDERSCORE, THE POTT NAME, AN UNDERSCORE, THE START DATE, AN UNDERSCORE AND END DATE


**********************************************************************************/
                      $this_share_id = $parent_shares_id . "_" . $receiver_pot_name . "_" . $start_date . "_" . $yield_date;



                      $query = "SELECT num_of_shares FROM shares_owned WHERE share_id = '$this_share_id' AND owner_id = '$receiver_id'";   
                      $result = $mysqli->query($query);
                          
                      if (mysqli_num_rows($result) != 0) {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $this_num_of_shares = intval($row["num_of_shares"]);
                    $this_new_num_of_shares_receiver = $this_num_of_shares + $shares_num;


                          $query = "UPDATE shares_owned SET num_of_shares = $this_new_num_of_shares_receiver WHERE share_id = '$this_share_id' AND owner_id = '$receiver_id'";
                          $result = $mysqli->query($query);

                          if ($result == true) {

                     if(isset($cashout_request_mail_message) && trim($cashout_request_mail_message) != ""){

					    	$subject = "SHARES TRANSFER TO CASHOUT ON FISHPOTT APP";
					    	$message = $cashout_request_mail_message . $this_share_id;

					        $headers = "From: <info@fishpott.com>FishPott App";
					        mail("info@fishpott.com",$subject,$message, $headers);


                         }


                                $table_name = "y3n_transfers";

                                $column1_name = "sender_id";
                                $column2_name = "receiver_id";
                                $column3_name = "shares_parent_id";
                                $column4_name = "date_time";
                                $column5_name = "num_shares_transfered";
                                $column6_name = "shares_parent_name";

                                $column1_value = $investor_id;
                                $column2_value = $receiver_id;
                                $column3_value = $parent_shares_id;
                                $column4_value = date("Y-m-d H:i:s");
                                $column5_value = $shares_num;
                                $column6_value = $share_name;

                                $pam1 = "s";
                                $pam2 = "s";
                                $pam3 = "s";
                                $pam4 = "s";
                                $pam5 = "i";
                                $pam6 = "s";

                                $done = 0;
                                include(ROOT_PATH . 'inc/insert6_prepared_statement.php');

                                if($done == 1){
                      include(ROOT_PATH . 'inc/db_connect.php');
//////////////////////    FCM  START      /////////////////////////
                      
        $query = "SELECT investor_id, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                    $linkee_id = trim($row["investor_id"]);
                    $linkee_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
                  $linkee_pot_name = trim($row["pot_name"]);
                  $linkee_profile_picture = trim($row["profile_picture"]);
                if (!file_exists("../pic_upload/" . $linkee_profile_picture)) {

                    $linkee_profile_picture = "";

                } else {

                $linkee_profile_picture = "http://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = $linkee_full_name . " transferred " . $shares_num . " " . $share_name . " to you.";


  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott Shares Transfer";

  $myalert = $not_text;

$fields = array(
      "registration_ids" => $all_keys,
      "priority" => "normal",
      'data' => array(
        'notification_type' => "general_notification",
        'not_type_real' => "transfer",
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => "", 
        'pott_name' => $linkee_pot_name, 
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


  }

}

//////////////////////    FCM  END      /////////////////////////

                                }


                                echo "Tranfer Complete"; exit;



                          } else {

                  echo "Something Went Awry. Transfer Didn't Complete. Contact FishPott Immediately. Error code : 222 "; exit;

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

                      $receiver_share_id = $parent_shares_id . "_" . $receiver_pot_name . "_" . $start_date . "_" . $yield_date;
                      
                      $column1_value = $receiver_share_id;
                      $column2_value = $parent_shares_id;
                      $column3_value = $share_name;
                      $column4_value = $receiver_id;
                      $column5_value = 0.00;
                      $column6_value = $shares_num;
                      $column7_value = $start_date;
                      $column8_value = $yield_date;
                      $column9_value = $shares_type;

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

                      //echo "start_date : " . $start_date;
                      //echo "yield_date : " . $yield_date;
                      //echo "shares_type : " . $shares_type;


                      if($done == 1){

                     if(isset($cashout_request_mail_message) && trim($cashout_request_mail_message) != ""){

	              	    $subject = "SHARES TRANSFER TO CASHOUT ON FISHPOTT APP";
				    	$message = $cashout_request_mail_message . $receiver_share_id;

				        $headers = "From: <info@fishpott.com>FishPott App";
				        mail("info@fishpott.com",$subject,$message, $headers);
				    }


                      $table_name = "y3n_transfers";

                      $column1_name = "sender_id";
                      $column2_name = "receiver_id";
                      $column3_name = "shares_parent_id";
                      $column4_name = "date_time";
                      $column5_name = "num_shares_transfered";
                      $column6_name = "shares_parent_name";

                      $column1_value = $investor_id;
                      $column2_value = $receiver_id;
                      $column3_value = $parent_shares_id;
                      $column4_value = date("Y-m-d H:i:s");
                      $column5_value = $shares_num;
                      $column6_value = $share_name;

                      $pam1 = "s";
                      $pam2 = "s";
                      $pam3 = "s";
                      $pam4 = "s";
                      $pam5 = "i";
                      $pam6 = "s";

                      $done = 0;
                      include(ROOT_PATH . 'inc/insert6_prepared_statement.php');
//////////////////////    FCM  START      /////////////////////////
                      include(ROOT_PATH . 'inc/db_connect.php');
        $query = "SELECT investor_id, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                    $linkee_id = trim($row["investor_id"]);
                    $linkee_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
                  $linkee_pot_name = trim($row["pot_name"]);
                  $linkee_profile_picture = trim($row["profile_picture"]);
                if (!file_exists("../pic_upload/" . $linkee_profile_picture)) {

                    $linkee_profile_picture = "";

                } else {

                $linkee_profile_picture = "http://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = $linkee_full_name . " transferred " . $shares_num . " " . $share_name . " to you.";


  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott Shares Transfer";

  $myalert = $not_text;

$fields = array('to' => $key,
      'data' => array(
        'notification_type' => "general_notification",
        'not_type_real' => "transfer",
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => "", 
        'pott_name' => $linkee_pot_name, 
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


  }

}

//////////////////////    FCM  END      /////////////////////////
                      echo "Transfer complete"; exit;

                      } else {

                  echo "Something Went Awry. Transfer Didn't Complete. Contact FishPott Immediately. Error code : 111 "; exit;

                      }

                    }
                  } else {

                  echo "Something Went Awry. Transfer Didn't Complete. Contact FishPott"; exit;
                }



                }


              } else {

                  echo "Shares could not be found..."; exit;
              }

            } else {

                  echo "We Couldn't Fetch The Receiver"; exit;

            } 


          } else {
                  echo "Something went awry"; exit;
    }


        } else {
                  echo "Something went awry"; exit;
    }


    } else {
                  echo "Something went awry"; exit;
    }
