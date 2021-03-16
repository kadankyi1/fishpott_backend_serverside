<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['receiver_pottname']) && trim($_POST['receiver_pottname']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $receiver_pottname = mysqli_real_escape_string($mysqli, $_POST['receiver_pottname']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $receiver_pottname = trim($receiver_pottname);
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

                $query = "SELECT investor_id, fcm_token, fcm_token_web, fcm_token_ios, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE pot_name = '$receiver_pottname'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;
                      $linkee_id = trim($row["investor_id"]);


                      if($linkee_id == $investor_id){
                        echo "Just you.. linkup failed."; exit;
                      }
                       $query = "SELECT status, sku FROM linkups WHERE (sender_id = '$myid' AND receiver_id = '$linkee_id') OR (sender_id = '$linkee_id' AND receiver_id = '$myid') ";

                          //$numrows = mysql_num_rows($query);
                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0") {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              
                              $status = intval($row["status"]);
                              $linkup_sku = intval($row["sku"]);
							  $query = "DELETE FROM linkups WHERE  sku = $linkup_sku ";
							  $result = $mysqli->query($query);

							  echo "You are no longer linked to this pott. You will not see news from this pott"; exit;


                            } else {

                                  $table_name = "linkups";

                                  $link_date_started = date("Y-m-d");

                                  $column1_name = "sender_id";
                                  $column2_name = "receiver_id";
                                  $column3_name = "status";
                                  $column4_name = "date_started";

                                  $column1_value = $myid;
                                  $column2_value = $linkee_id;
                                  $column3_value = 1;
                                  $column4_value = $link_date_started;

                                  $pam1 = "s";
                                  $pam2 = "s";
                                  $pam3 = "i";
                                  $pam4 = "s";

                                  $done = 0;

                                include(ROOT_PATH . 'inc/insert4_prepared_statement.php');
                              include(ROOT_PATH . 'inc/db_connect.php');
                              if($done == 1){

                      
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
		        		$not_text = $linkee_full_name . " has linked to your pott to see more posts from you.";


//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott Linkup";

  $myalert = $dbfull_name . " has linked to your pott to see more posts from you.";

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
      	'pott_name' => $linkee_pot_name, 
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


            include(ROOT_PATH . 'inc/db_connect.php');
            $table_name = "nkae";

            $myalert = $dbfull_name . " wants to link up with you.";

            $column1_name = "wo_id";
            $column2_name = "orno_id";
            $column3_name = "type";
            $column4_name = "info_1";
            $column5_name = "asem_id";

            $column1_value = $linkee_id;
            $column2_value = $myid;
            $column3_value = "linkup";
            $column4_value = $myalert;
            $column5_value = $myid;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";

            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
            include(ROOT_PATH . 'inc/db_connect.php');
                                echo "You are linked up. You will see more posts from this pott"; exit;
                              } else {

                                echo "Something went awry"; exit;
                              }

                            }
                      } else {
                        echo "This Pott could not be found"; exit;
                        }
            
                    }


          }/////// END OF PASSWORD CHECK

        }
