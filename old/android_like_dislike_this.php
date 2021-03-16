<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['likeData_news_id']) && trim($_POST['likeData_news_id']) != "" && isset($_POST['likeData_like_type']) && trim($_POST['likeData_like_type']) != "") {

	require_once("config.php");


	include(ROOT_PATH . 'inc/db_connect.php');

	$myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);

    $likeData_news_id = mysqli_real_escape_string($mysqli, $_POST['likeData_news_id']);
    $likeData_like_type = mysqli_real_escape_string($mysqli, $_POST['likeData_like_type']);

	$likeData_investor_id = $myid;

	$myid = trim($myid);
	$mypass = trim($mypass);
	$likeData_news_id = trim($likeData_news_id);
	$likeData_like_type = trim($likeData_like_type);
	$today = date("F j, Y");
	$like_data_time = date("Y-m-d H:i:s");

	mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

    	$row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);

          if($mypass == $dbpass && $dbflag == 0) {

			$query = "SELECT first_name, last_name FROM investor WHERE investor_id = '$myid'";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$first_name = trim($row["first_name"]);
				$last_name = trim($row["last_name"]);
				$full_name = $first_name . " " . $last_name;
			} else {

				$full_name = "Someone";
			}

    $query = "SELECT like_type, sku FROM likes WHERE liker_investor_id = '$likeData_investor_id' AND likes_news_id = '$likeData_news_id'";

    $result = $mysqli->query($query);

	if (mysqli_num_rows($result) == "0") {

		$query = "INSERT INTO likes (sku, likes_news_id, liker_investor_id, like_type, date_time)
		  VALUES ('', '$likeData_news_id', '$likeData_investor_id', '$likeData_like_type', '$like_data_time')";   


	    $result = $mysqli->query($query);
	    if ($result != "0") {

			    $query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$likeData_news_id'";

			    $result = $mysqli->query($query);
				
				if (mysqli_num_rows($result) != "0") {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$inputtor_id = trim($row["inputtor_id"]);

  if($inputtor_id != $myid){

			            include(ROOT_PATH . 'inc/db_connect.php');
			            $table_name = "nkae";

			            $column1_name = "wo_id";
			            $column2_name = "orno_id";
			            $column3_name = "type";
			            $column4_name = "info_1";
			            $column5_name = "asem_id";

			            if($likeData_like_type == 1){
			            	$noti_type = "like";
			            } else {
			            	$noti_type = "dislike";
			            }

			            $column1_value = $inputtor_id;
			            $column2_value = $myid;
			            $column3_value = $noti_type;
			            $column4_value = "";
			            $column5_value = $likeData_news_id;

			            $pam1 = "s";
			            $pam2 = "s";
			            $pam3 = "s";
			            $pam4 = "s";
			            $pam5 = "s";

			            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
			            include(ROOT_PATH . 'inc/db_connect.php');

			        }


					$query = "SELECT net_worth, pot_name, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$inputtor_id'";

				    $result = $mysqli->query($query);

					if (mysqli_num_rows($result) != "0") {

						$row = $result->fetch_array(MYSQLI_ASSOC);
						$pot_name = trim($row["pot_name"]);
						$key = trim($row["fcm_token"]);
						$fcm_token_web = trim($row["fcm_token_web"]);
						$fcm_token_ios = trim($row["fcm_token_ios"]);
						$all_keys = [$key, $fcm_token_ios, $fcm_token_web];
						$key = $key . $fcm_token_ios . $fcm_token_web;


						if($likeData_like_type == 0){
							$net_worth = trim($row["net_worth"]) - 1;
							$myalert = $full_name . " dislikes your post. Your pott pearls decreased to " . $net_worth;
							$not_like_type = "dislike";
						} elseif ($likeData_like_type == 1){
							$net_worth = trim($row["net_worth"]) + 1;
							$myalert = $full_name . " likes your post. Your pott pearls increased to " . $net_worth;
							$not_like_type = "like";
						} else {
							$net_worth = trim($row["net_worth"]);
							$myalert = $full_name . " reacted to your post.";
							$not_like_type = "like";
						}
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

                $linkee_profile_picture = "https://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = $myalert;


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
        'not_type_real' => $not_like_type,
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => $likeData_news_id, 
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

						$query = "UPDATE investor SET net_worth = $net_worth WHERE investor_id = '$inputtor_id'";
						$result = $mysqli->query($query);

						

					}	


				}


	    }



	}  else {

	$row = $result->fetch_array(MYSQLI_ASSOC);
	$db_like_type = trim($row["like_type"]);
	$like_sku = trim($row["sku"]);
	$like_sku = intval($like_sku);

	if($db_like_type != $likeData_like_type) {

		$query = "UPDATE likes SET like_type = $likeData_like_type WHERE likes_news_id = '$likeData_news_id' AND liker_investor_id = '$likeData_investor_id'";
		$result = $mysqli->query($query);
		if($result != "0"){
			$query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$likeData_news_id'";

			    $result = $mysqli->query($query);
				
				if (mysqli_num_rows($result) != "0") {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$inputtor_id = trim($row["inputtor_id"]);

			            include(ROOT_PATH . 'inc/db_connect.php');
			            $table_name = "nkae";

			            $column1_name = "wo_id";
			            $column2_name = "orno_id";
			            $column3_name = "type";
			            $column4_name = "info_1";
			            $column5_name = "asem_id";

			            if($likeData_like_type == 1){
			            	$noti_type = "like";
			            } else {
			            	$noti_type = "dislike";
			            }

			            $column1_value = $inputtor_id;
			            $column2_value = $myid;
			            $column3_value = $noti_type;
			            $column4_value = "";
			            $column5_value = $likeData_news_id;

			            $pam1 = "s";
			            $pam2 = "s";
			            $pam3 = "s";
			            $pam4 = "s";
			            $pam5 = "s";

			            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
			            include(ROOT_PATH . 'inc/db_connect.php');
					
					$query = "SELECT net_worth, pot_name, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$inputtor_id'";

				    $result = $mysqli->query($query);

					if (mysqli_num_rows($result) != "0") {

						$row = $result->fetch_array(MYSQLI_ASSOC);
						$pot_name = trim($row["pot_name"]);
						$key = trim($row["fcm_token"]);
						$fcm_token_web = trim($row["fcm_token_web"]);
						$fcm_token_ios = trim($row["fcm_token_ios"]);
						$all_keys = [$key, $fcm_token_ios, $fcm_token_web];
						$key = $key . $fcm_token_ios . $fcm_token_web;
						if($likeData_like_type == 0){
							$net_worth = trim($row["net_worth"]) - 2;
							$myalert = $full_name . " dislikes your post. Your pott pearls decreased to " . $net_worth;
							$not_like_type = "dislike";
						} elseif ($likeData_like_type == 1){
							$net_worth = trim($row["net_worth"]) + 2;
							$myalert = $full_name . " likes your post. Your pott pearls increased to " . $net_worth;
							$not_like_type = "like";
						} else {
							$net_worth = trim($row["net_worth"]);
							$myalert = $full_name . " reacted to your post.";
							$not_like_type = "like";
						}


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

                $linkee_profile_picture = "https://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = $myalert;


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
        'not_type_real' => $not_like_type,
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => $likeData_news_id, 
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
				    	
						$query = "UPDATE investor SET net_worth = $net_worth WHERE investor_id = '$inputtor_id'";
						$result = $mysqli->query($query);

						

					}	


				}
		}

		} else {

				$query = "DELETE FROM likes WHERE sku = $like_sku";
				$result = $mysqli->query($query);
				//if($result != "0"){
					$query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$likeData_news_id'";

			    $result = $mysqli->query($query);
				
				if (mysqli_num_rows($result) != "0") {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$inputtor_id = trim($row["inputtor_id"]);
					$query = "SELECT net_worth FROM investor WHERE investor_id = '$inputtor_id'";

				    $result = $mysqli->query($query);

					if (mysqli_num_rows($result) != "0") {

						$row = $result->fetch_array(MYSQLI_ASSOC);
						if($likeData_like_type == 0){
							$net_worth = trim($row["net_worth"]) + 1;
						} elseif ($likeData_like_type == 1){
							$net_worth = trim($row["net_worth"]) - 1;
						} else {
							$net_worth = trim($row["net_worth"]);
						}
						
						$query = "UPDATE investor SET net_worth = $net_worth WHERE investor_id = '$inputtor_id'";
						$result = $mysqli->query($query);

						

					}	


				}
				//}

			}

	}

   }

  }
}