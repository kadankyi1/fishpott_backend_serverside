<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['news_id']) && trim($_POST['news_id']) != "" && isset($_POST['myshare_addition'])) {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');
    $share_news_id = trim($_POST['news_id']);
    $addNewsText = trim($_POST['myshare_addition']);

    //$myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    //$mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $share_news_id = mysqli_real_escape_string($mysqli, $share_news_id);
    //$addNewsText = mysqli_real_escape_string($mysqli, $addNewsText);

    $myid = $_POST['myid'];
    $mypass = $_POST['mypass'];
    $share_news_id = $share_news_id;

    $addNewsText = $addNewsText;
    $addNewsText2 = str_replace("@mylinkups","",$addNewsText);

    $today = date("F j, Y");
    $myid = trim($myid);
    $mypass = trim($mypass);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

            $news_id = uniqid($myid, TRUE);
            $date_time = date("Y-m-d H:i:s");
            $date_time = trim($date_time);

            $table_name = "newsfeed";
            $column1_name = "type";
            $column2_name = "inputtor_type";
            $column3_name = "inputtor_id";
            $column4_name = "news_id";
            $column5_name = "date_time";
            $column6_name = "news";
            $column7_name = "news_image";
            $column8_name = "news_video";
            $column9_name = "news_aud";
            $column10_name = "news_id_ref";

            $column1_value = "shared_news";
            $column2_value = "investor";
            $column3_value = $myid;
            $column4_value = $news_id;
            $column5_value = $date_time;
            $column6_value = $addNewsText2;
            $column7_value = "";
            $column8_value = "";
            $column9_value = "";
            $column10_value = $share_news_id;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "s";
            $pam9 = "s";
            $pam10 = "s";
            include(ROOT_PATH . 'inc/insert10_prepared_statement.php');

            if($done == 1 && $addNewsText != ""){

          include(ROOT_PATH . 'inc/db_connect.php');

          preg_match_all("/\B@[a-zA-Z0-9]+/i", $addNewsText, $mentions);
          $mentions = array_map(function($str){ return substr($str, 1); }, $mentions[0]);

          if (in_array("mylinkups", $mentions)){

              $query2="SELECT * FROM linkups WHERE receiver_id = '$myid' AND status = 1";
              $result2 = $mysqli->query($query2);

                while($row2 =$result2->fetch_array()) {

                    $sender_id = $row2["sender_id"];

                    $query = "SELECT pot_name FROM investor WHERE investor_id = '$sender_id'";   

                    $result = $mysqli->query($query);
                      
                    if (mysqli_num_rows($result) != 0) {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $mention_pottname = trim($row["pot_name"]);

                        if (in_array($mention_pottname, $mentions)){

                        } else {
                          array_push($mentions, $mention_pottname);
                        }


                    } // end of query-if statement

                }// END OF WHILE LOOP

             }


            foreach($mentions as $mentionedUser){

                $query = "SELECT first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {
 
                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $receiver_first_name = trim($row["first_name"]);
                      $receiver_last_name = trim($row["last_name"]);
                      $receiver_fullname = $receiver_first_name . " " . $receiver_last_name;
                      $receiver_verified_tag = trim($row["verified_tag"]);
                      $receiver_profile_picture = trim($row["profile_picture"]);
                      
                if (!file_exists("../pic_upload/" . $receiver_profile_picture)) {

                    $receiver_profile_picture = "";

                  } else {

  $receiver_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $receiver_profile_picture;

                  }
                      $chat_date = date("F j, Y, g:i a");

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios, investor_id FROM investor WHERE pot_name = '$mentionedUser'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {
                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;
                      $linkee_id = trim($row["investor_id"]);

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott";

  $myalert = $receiver_fullname . " mentioned you in a repost";

$fields = array(
      "registration_ids" => $all_keys,
      "priority" => "normal",
      'data' => array(
        'notification_type' => "general_notification",
        'not_type_real' => "like",
        'not_pic' => $receiver_profile_picture,
        'not_title' => $title,
        'not_message' => $myalert,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $myalert, 
        'not_pott_or_newsid' => $news_id, 
        'pott_name' => $mentionedUser, 
        'not_time' => $today  
        )
      );

  $payload = json_encode($fields);

  if($key != "" && $linkee_id != $myid){

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

            }
                  

                  include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$share_news_id'";

                $result = $mysqli->query($query);
                
                if (mysqli_num_rows($result) != "0") {
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $inputtor_id = trim($row["inputtor_id"]);

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$inputtor_id'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;


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

                $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = $linkee_full_name . " shared your post";


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
        'not_type_real' => "share",
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => $share_news_id, 
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


  if($inputtor_id != $myid){

                  include(ROOT_PATH . 'inc/db_connect.php');
            $table_name = "nkae";

            $column1_name = "wo_id";
            $column2_name = "orno_id";
            $column3_name = "type";
            $column4_name = "info_1";
            $column5_name = "asem_id";

            $column1_value = $inputtor_id;
            $column2_value = $myid;
            $column3_value = "share";
            $column4_value = $myalert;
            $column5_value = $share_news_id;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";

            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
            include(ROOT_PATH . 'inc/db_connect.php');
          }
              

                  }


            }   



          }

        }

    }
