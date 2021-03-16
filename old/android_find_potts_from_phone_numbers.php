<?php

if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['mycountry']) && trim($_POST['mycountry']) != "" && 
  isset($_POST['numbers']) && trim($_POST['numbers']) != "") {
  require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $mycountry = mysqli_real_escape_string($mysqli, $_POST['mycountry']);
    $numbers = mysqli_real_escape_string($mysqli, $_POST['numbers']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $mycountry = trim($mycountry);
    $numbers = trim($numbers);

    $phone_numbers_array = explode(" ", $numbers);

    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8mb4');
    $today = date("Y-m-d");
    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

              $query = "SELECT pot_name, nkurofuo_fetch_date, fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $my_potname = trim($row["pot_name"]);
                      $nkurofuo_fetch_date = trim($row["nkurofuo_fetch_date"]);
                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;

                } else {
                  exit;
                }


            // GETTING THE RIGHT COUNTRY CODE
            if(strtolower($mycountry) == "ghana"){
              $my_ext = "+233";
            }

                $now = time(); // or your date as well
                $your_date = strtotime($nkurofuo_fetch_date);
                $datediff = $now - $your_date;

                $diff = (floor($datediff / (60 * 60 * 24)) + 1);


                if($diff > 2 || $nkurofuo_fetch_date == ""){

                    for ($i=0; $i < count($phone_numbers_array); $i++) { 

                      $this_phone_number = $phone_numbers_array[$i];
                      $phone_length = strlen($this_phone_number);

                      if ($phone_length > 12) {

                          //TAKE NOTHING OFF THE STARTING OF THE STRING AND RETURN THE FIRST CHARACTER
                          if(substr($this_phone_number, 0, 1) == "0"){
                            $this_new_phone_number = substr($this_phone_number, 5, $phone_length-5);
                          }
                          # if string contains +62 or 62 do something with this number

                      } else {

                          if(substr($this_phone_number, 0, 1) == "0"){
                            $this_new_phone_number = $my_ext . substr($this_phone_number, 1, $phone_length-1);
                          }
                          # do nothing because string doesn't contain +62 or 62

                      }   // end of getting correct phone number

                      $query="SELECT * FROM investor WHERE phone LIKE '%$this_new_phone_number%'";
                      $result = $mysqli->query($query);

                      //$row = $result->fetch_row();

                      while($row=$result->fetch_array()) {

                          $first_name = $row["first_name"];
                          $last_name = $row["last_name"];
                          $this_full_name = $first_name . " " . $last_name;
                          $linkee_pot_name = trim($row["pot_name"]);
                          $linkee_id = trim($row["investor_id"]);
                          $linkee_profile_picture = trim($row["profile_picture"]);
                          if (!file_exists("../pic_upload/" . $linkee_profile_picture)) {

                            $linkee_profile_picture = "";

                          } else {

                            $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $linkee_profile_picture; 
                          }

                       $query = "SELECT status FROM linkups WHERE (sender_id = '$myid' AND receiver_id = '$linkee_id') OR (sender_id = '$linkee_id' AND receiver_id = '$myid') ";

                          //$numrows = mysql_num_rows($query);
                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0") {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              
                              $status = intval($row["status"]);
                              if($status != 1){
                                $send = 1;
                              }

                          } else {
                            $send = 1;
                          }

                          if($my_potname != $linkee_pot_name && $send == 1){
                            $myalert = "Consider linking up with " . $this_full_name;

                          //////////////////////    FCM  START      /////////////////////////
                            
                            $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

                            $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

                            $headers = array(
                              'Authorization:key=' . $server_key, 
                              'Content-Type:application/json');

                            $title = "Link-Up suggestion";
                            $today_now = date("F j, Y");

                            $fields = array(
                                  "registration_ids" => $all_keys,
                                  "priority" => "normal",
                                    'data' => array(
                                      'notification_type' => "general_notification",
                                      'not_type_real' => "linkup",
                                      'not_pic' => $linkee_profile_picture,
                                      'not_title' => $title,
                                      'not_message' => $myalert,
                                      'not_image' => "",
                                      'not_video' => "",
                                      'not_text' => $myalert, 
                                      'not_pott_or_newsid' => "", 
                                      'pott_name' => $linkee_pot_name, 
                                      'not_time' => $today_now  
                                      )
                                    );

                            $payload = json_encode($fields);

                            if($key != ""){

                            //var_dump($all_keys);
                            //var_dump($fields);

                            $curl_session = curl_init();

                            curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
                            curl_setopt($curl_session, CURLOPT_POST, true);
                            curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                            curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);


                            }
                            

                          //////////////////////    FCM  END      /////////////////////////
                          }

                      } // end of while loop

                    } // end of for loop

                } else {
                  exit;
                }

                //exit;
              //// SETTING THE LATEST DATE OF POTT FINDING
                $query = "UPDATE investor SET nkurofuo_fetch_date = '$today' WHERE investor_id = '$myid'";
                $result = $mysqli->query($query);

            
                }


          }/////// END OF PASSWORD CHECK

        }
