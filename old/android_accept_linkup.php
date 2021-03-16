<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['accepted_person_id']) && trim($_POST['accepted_person_id']) != "" && isset($_POST['type']) && trim($_POST['type']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');
    $linkee_id = trim($_POST['accepted_person_id']);

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $action_type = mysqli_real_escape_string($mysqli, $_POST['type']);
    $linkee_id = mysqli_real_escape_string($mysqli, $linkee_id);
	$today = date("F j, Y");

    $myid = trim($myid);
    $mypass = trim($mypass);
    $action_type = trim($action_type);
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

          $query = "SELECT status, sku FROM linkups WHERE (sender_id = '$myid' AND receiver_id = '$linkee_id') OR (sender_id = '$linkee_id' AND receiver_id = '$myid') ";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              
              $sku = $row["sku"];
              if($action_type == "0"){
          $query = "DELETE FROM nkae WHERE wo_id = '$myid' AND orno_id = '$linkee_id' AND type = 'linkup'";
              $result = $mysqli->query($query);
          $query = "DELETE FROM linkups WHERE sku = $sku";
              $result = $mysqli->query($query); 
              echo "Linkup Request Deleted"; exit;

              } 
              $query = "UPDATE  linkups SET  status = 1 WHERE  sku = $sku ";
              $result = $mysqli->query($query);

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$linkee_id'";   

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
                    $linkee_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
                  $linkee_pot_name = trim($row["pot_name"]);
                  $linkee_profile_picture = trim($row["profile_picture"]);
                if (!file_exists("../pic_upload/" . $linkee_profile_picture)) {

                    $linkee_profile_picture = "";

                } else {

                $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                }
                $not_text = "You got linked with " . $linkee_full_name;


  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott | Linkup Accepted";

  $myalert = $not_text;

$fields = array(
      "registration_ids" => $all_keys,
      "priority" => "normal",
      'data' => array(
        'notification_type' => "general_notification",
        'not_type_real' => "linkup_accepted",
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
  $myalert = "You got link with " . $dbfull_name;
            include(ROOT_PATH . 'inc/db_connect.php');
            $table_name = "nkae";

            $column1_name = "wo_id";
            $column2_name = "orno_id";
            $column3_name = "type";
            $column4_name = "info_1";
            $column5_name = "asem_id";

            $column1_value = $linkee_id;
            $column2_value = $myid;
            $column3_value = "linkup_accepted";
            $column4_value = $myalert;
            $column5_value = $myid;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";

            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
            include(ROOT_PATH . 'inc/db_connect.php');

          $query = "DELETE FROM nkae WHERE wo_id = '$myid' AND orno_id = '$linkee_id' AND type = 'linkup'";
            $result = $mysqli->query($query);

            echo "Linkup Accepted";


            }

      }


    }
}
