<?php

if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['receiver_pottname']) && trim($_POST['receiver_pottname']) != "" && 
	isset($_POST['msg_datetime']) && 
	isset($_POST['msg'])&& 
	isset($_POST['chat_table']) && trim($_POST['chat_table']) != ""
	) {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $receiver_pottname = mysqli_real_escape_string($mysqli, $_POST['receiver_pottname']);
    $msg_datetime = mysqli_real_escape_string($mysqli, $_POST['msg_datetime']);
    //$msg = mysqli_real_escape_string($mysqli, $_POST['msg']);
    $chat_table = mysqli_real_escape_string($mysqli, $_POST['chat_table']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $receiver_pottname = trim($receiver_pottname);
    $msg = trim($_POST['msg']);
    $chat_table = trim($chat_table);
    $investor_id = $myid;
	  $msg_datetime = date("Y-m-d H:i:s");
    mysqli_set_charset($mysqli, 'utf8mb4');


    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

            $query = "SELECT investor_id FROM investor WHERE pot_name = '$receiver_pottname'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $sender_id = trim($row["investor_id"]);

            } else {
              exit;
            }

            $query = "SELECT pot_name FROM investor WHERE investor_id = '$myid'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $blocked_pottname = trim($row["pot_name"]);

            } else {
              exit;
            }

            $this_blocked_id = $sender_id . "_" . $blocked_pottname;

            //echo "this_blocked_id : " . $this_blocked_id;

            $query = "SELECT blocked_id FROM mern_ha_me_fuo WHERE block_action_id = '$this_blocked_id'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {
              //echo "NOTIFICATION BLOCKED";
              exit;

            }

            $query = "SELECT sku FROM akasakasa_details WHERE chat_table = '$chat_table'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $sku = trim($row["sku"]);

				  $query = "UPDATE akasakasa_details SET msg_datetime = '$msg_datetime', msg = '$msg' WHERE sku = $sku";
				  $result = $mysqli->query($query);

			} else {

						$table_name = "akasakasa_details";

						$column1_name = "investor_id";
						$column2_name = "chat_table";
						$column3_name = "receiver_pottname";
						$column4_name = "msg_datetime";
						$column5_name = "msg";

						$column1_value = $myid;
						$column2_value = $chat_table;
						$column3_value = $receiver_pottname;
						$column4_value = $msg_datetime;
						$column5_value = $msg;

						$pam1 = "s";
						$pam2 = "s";
						$pam3 = "s";
						$pam4 = "s";
						$pam5 = "s";

						include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
						include(ROOT_PATH . 'inc/db_connect.php');


			}

                $query = "SELECT  investor_id, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE pot_name = '$receiver_pottname'";   
                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $key = trim($row["fcm_token"]);
                      $linkee_id = trim($row["investor_id"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;
  				} else {
  					exit;
  				}


                $query = "SELECT first_name, last_name, verified_tag, profile_picture, pot_name FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $receiver_first_name = trim($row["first_name"]);
                      $real_pot_name = trim($row["pot_name"]);
                      $receiver_last_name = trim($row["last_name"]);
                      $receiver_fullname = $receiver_first_name . " " . $receiver_last_name;
                      $receiver_verified_tag = trim($row["verified_tag"]);
                      $receiver_profile_picture = trim($row["profile_picture"]);
                      
			          if (!file_exists("../pic_upload/" . $receiver_profile_picture)) {

			          		$receiver_profile_picture = "";
	            		} else {

	$receiver_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $receiver_profile_picture; 
	            		}
                      $chat_date = date("F j, Y");

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "New Message";

  $myalert = $msg;

  $fields = array(
      "registration_ids" => $all_keys,
      "priority" => "normal",
          'data' => array(
          	'notification_type' => "chat",
          	'chat_table' => $chat_table,
          	'receiver_fullname' => $receiver_fullname,
          	'receiver_pottname' => $real_pot_name,
          	'receiver_verified_tag' => $receiver_verified_tag,
          	'receiver_profile_picture' => $receiver_profile_picture,
          	'chat_date' => $chat_date, 
          	'chat_message' => $myalert 
          	)
          );

  $payload = json_encode($fields);

  if($key != ""){
  //if($key != "" && $linkee_id != $myid){

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


          }/////// END OF PASSWORD CHECK

        }
