<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);

    $myid = trim($myid);
    $mypass = trim($mypass);

    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);
          $newsfeedReturn["chats"] = array();

            $query = "SELECT pot_name FROM investor WHERE investor_id = '$myid'";   
            $result = $mysqli->query($query);
                
            if (mysqli_num_rows($result) != 0) {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $blocked_pottname = trim($row["pot_name"]);

            } else {
              exit;
            }

    $query = "SELECT pot_name FROM investor WHERE investor_id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpot_name = trim($row["pot_name"]);
        } else {
          $dbpot_name = "";
        }

          if($mypass == $dbpass && $dbflag == 0 && $dbpot_name != "") {

            $query = "SELECT * FROM akasakasa_details WHERE investor_id = '$myid' OR receiver_pottname = '$dbpot_name'";   
            $result = $mysqli->query($query);
                
              while($row=$result->fetch_array()) {
               
                      $chat_investor_id = trim($row["investor_id"]);
                      $chat_table = trim($row["chat_table"]);
                      $receiver_pottname = trim($row["receiver_pottname"]);
                      $msg_datetime = trim($row["msg_datetime"]);
                      $msg = trim($row["msg"]);

            $query2 = "SELECT investor_id FROM investor WHERE pot_name = '$receiver_pottname'";   
            $result2 = $mysqli->query($query2);
                
            if (mysqli_num_rows($result2) != 0) {

                  $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                  $sender_id = trim($row2["investor_id"]);

            } else {
              continue;
            }


            $this_blocked_id = $sender_id . "_" . $blocked_pottname;

            //echo "this_blocked_id : " . $this_blocked_id;

            $query2 = "SELECT blocked_id FROM mern_ha_me_fuo WHERE block_action_id = '$this_blocked_id'";   
            $result2 = $mysqli->query($query2);
                
            if (mysqli_num_rows($result2) != 0) {
              //echo "NOTIFICATION BLOCKED";
              continue;

            }


                      if($receiver_pottname == $dbpot_name){

$query = "SELECT first_name, last_name, profile_picture, verified_tag, pot_name  FROM investor WHERE investor_id = '$chat_investor_id'";
                        
                      } else {

$query = "SELECT first_name, last_name, profile_picture, verified_tag, pot_name FROM investor WHERE pot_name = '$receiver_pottname'";

                      }


                          $result2 = $mysqli->query($query);

                      if (mysqli_num_rows($result2) != "0") {

                          $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                          $first_name = trim($row2["first_name"]);
                          $last_name = trim($row2["last_name"]);
                          $chat_pot_name = trim($row2["pot_name"]);
                          $receiver_full_name = $first_name . " " . $last_name;
                          $verified_tag = trim($row2["verified_tag"]);
                          $profile_picture = trim($row2["profile_picture"]);
                          $strStart = $row["msg_datetime"];

                      if($receiver_pottname == $dbpot_name){

                        $receiver_pottname  = $chat_pot_name;
                        
                      }

                          include(ROOT_PATH . 'inc/time_converter.php');
                          if (!file_exists("../pic_upload/" . $profile_picture)) {

                              $profile_picture = "";

                              } else {

                                $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                              }

                                if($chat_table != "" && $receiver_pottname != ""){


                                    
                                       $next  = array(

                                      'chat_id' => $chat_table, 
                                      'receiver_full_name' => $receiver_full_name,
                                      'receiver_pottname' => $receiver_pottname, 
                                      'verified_tag' => $verified_tag, 
                                      'profile_picture' => $profile_picture, 
                                      'last_msg' => $msg, 
                                      'last_msg_time' => $date_time

                                      );
                                      array_push($newsfeedReturn["chats"], $next);    
                                unset($chat_table);
                                unset($receiver_pottname);

                                }

                        }


                } // WHILE LOOP END

            echo json_encode($newsfeedReturn);

            }/////// END OF PASSWORD CHECK


          }

        }
