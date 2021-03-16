<?php

if(
	isset($_POST['your_password']) && trim($_POST['your_password']) != "" && trim($_POST['your_password']) == "g0d6ppr06ch" && 
	isset($_POST['pott_name']) && trim($_POST['pott_name']) != "" && 
	isset($_POST['notification_type']) && trim($_POST['notification_type']) != "" && 
	isset($_POST['notification_title']) && trim($_POST['notification_title']) != "" && 
	isset($_POST['notification_text']) && trim($_POST['notification_text']) != "" 
   ) {
	require_once("config.php");
//FISHPOT_TIPS
    include(ROOT_PATH . 'inc/db_connect.php');

    $today = date("F j, Y");

    //$notification_title = mysqli_real_escape_string($mysqli, $_POST['notification_title']);
    $pott_name = mysqli_real_escape_string($mysqli, $_POST['pott_name']);
    $pott_name = trim($pott_name);
    $notification_type = trim($_POST['notification_type']);
    $notification_title = trim($_POST['notification_title']);
    $notification_text = trim($_POST['notification_text']);

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

		if($notification_type == "single_user"){

        $query = "SELECT investor_id, pot_name, first_name, last_name, verified_tag, profile_picture, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE pot_name = '$pott_name' OR  phone = '$pott_name'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                     $row = $result->fetch_array(MYSQLI_ASSOC);
                  	$linkee_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
					$key = trim($row["fcm_token"]);
					$fcm_token_web = trim($row["fcm_token_web"]);
					$fcm_token_ios = trim($row["fcm_token_ios"]);
                    $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                    $key = $key . $fcm_token_ios . $fcm_token_web;
                	$linkee_pot_name = trim($row["pot_name"]);

		        		if(trim($key) == ""){
		        				echo "Pott Has no verification keys"; exit;
		        		}
		        } else {
		        	echo "Failed to find pott"; exit;
		        }

			  $title = $notification_title;

			  $myalert = $notification_text;
			  $not_text = $notification_text;
  $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/uploads/2017-12-161513439813.png"; 

			$fields = array(
			      "registration_ids" => $all_keys,
			      "priority" => "normal",
			      'data' => array(
			      	'notification_type' => "general_notification",
			      	'not_type_real' => "linkup",
			      	'not_pic' => $linkee_profile_picture,
			      	'not_title' => $title,
			      	'not_message' => $not_text,
			      	'not_image' => "",
			      	'not_video' => "",
			      	'not_text' => $not_text, 
			      	'not_pott_or_newsid' => "", 
			      	'pott_name' => "fishpot_inc", 
			        'not_time' => $today  
			      	)
			      );


			  $payload = json_encode($fields);


			  $curl_session = curl_init();

			  curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
			  curl_setopt($curl_session, CURLOPT_POST, true);
			  curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
			  curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
			  curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
			  curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
			  curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

			  $curl_result = curl_exec($curl_session);

			  echo $curl_result;


		} else if($notification_type == "all_users"){


  $title = $notification_title;

  $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/uploads/2017-12-161513439813.png"; 

  $myalert = $notification_text;
  $not_text = $notification_text;

$fields = array(
      "to" => "/topics/FISHPOT_TIPS",
      "priority" => "normal",
      'data' => array(
      	'notification_type' => "general_notification",
      	'not_type_real' => "linkup",
      	'not_pic' => $linkee_profile_picture,
      	'not_title' => $title,
      	'not_message' => $not_text,
      	'not_image' => "",
      	'not_video' => "",
      	'not_text' => $not_text, 
      	'not_pott_or_newsid' => "", 
      	'pott_name' => "fishpot_inc", 
        'not_time' => $today  
      	)
      );


  $payload = json_encode($fields);


  $curl_session = curl_init();

  curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
  curl_setopt($curl_session, CURLOPT_POST, true);
  curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
  curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);

  $curl_result = curl_exec($curl_session);

  echo $curl_result;


		}


//////////////////////    FCM  END      /////////////////////////

	}