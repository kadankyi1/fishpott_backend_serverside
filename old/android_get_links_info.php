<?php

if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['pottname']) && trim($_POST['pottname']) != "" && 
  isset($_POST['type']) && trim($_POST['type']) != "" && 
  isset($_POST['my_app_version']) && trim($_POST['my_app_version']) != "" && 
  isset($_POST['last_sku']) && trim($_POST['last_sku']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $pottname = mysqli_real_escape_string($mysqli, $_POST['pottname']);
    $type = mysqli_real_escape_string($mysqli, $_POST['type']);
    $last_sku = mysqli_real_escape_string($mysqli, $_POST['last_sku']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $type = trim($type);
    $pottname = trim($pottname);
    $last_sku = intval($last_sku);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8');


    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          $count = 0;

          if($mypass == $dbpass && $flag == 0) {

            $query = "SELECT investor_id FROM investor WHERE pot_name = '$pottname'";   

            $result = $mysqli->query($query);
            
            if (mysqli_num_rows($result) != 0) {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $real_investor_id = trim($row["investor_id"]);

            } else {

              echo json_encode($linkUpsReturn); exit;
            }


            if($type == "linked"){

              if($last_sku <= 0){

                $query="SELECT * FROM linkups WHERE sender_id = '$real_investor_id' ORDER BY sku DESC";

              } else {

                $query="SELECT * FROM linkups WHERE sender_id = '$real_investor_id' AND sku < $last_sku ORDER BY sku DESC";

              }


            } else {

              if($last_sku <= 0){

                $query="SELECT * FROM linkups WHERE receiver_id = '$real_investor_id' ORDER BY sku DESC";

              } else {

                $query="SELECT * FROM linkups WHERE receiver_id = '$real_investor_id' AND sku < $last_sku ORDER BY sku DESC";
                
              }

            }

            $result = $mysqli->query($query);

              while($row=$result->fetch_array()) {

                  $sku = $row["sku"];
                  $sender_id = $row["sender_id"];
                  $receiver_id = $row["receiver_id"];

                  if($type == "linked"){

                    $query = "SELECT * FROM investor WHERE investor_id = '$receiver_id' ";

                  } else {

                    $query = "SELECT * FROM investor WHERE investor_id = '$sender_id' ";

                  }

                  $result2 = $mysqli->query($query);
                  if (mysqli_num_rows($result2) != "0") {

                      $add_this = 1;
                      $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                      $first_name = $row2["first_name"];
                      $last_name = $row2["last_name"];
                      $net_worth = $row2["net_worth"];
                      $country = $row2["country"];
                      $thepot_name = $row2["pot_name"];
                      $full_name = $first_name . " " . $last_name;
                      $profile_picture = $row2["profile_picture"];
                      if (!file_exists("../pic_upload/" . $profile_picture)) {

                          $profile_picture = "";

                      } else {

                            $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                      }

                      $investor_level = intval($row2["investing_points"]);
                      if($investor_level == 0){

                        $investor_level = "Baby Investor";

                      } elseif($investor_level == 1){

                        $investor_level = "Toddler Investor";

                      } elseif($investor_level > 1 && $investor_level < 50){

                        $investor_level = "Swift Investor";

                      } elseif($investor_level >= 50 && $investor_level < 200){

                        $investor_level = "Demi-god Investor";

                      } elseif($investor_level >= 200){

                        $investor_level = "god Investor";

                      }

                      $this_inputtor_vtag = $row2["verified_tag"];

                      $strStart = $row2["coins_secure_datetime"];

                      include(ROOT_PATH . 'inc/time_converter.php');

                  if($receiver_id == $myid){

                    $add_this = 0;

                  } 


                      if($add_this == 1){
                        $count = $count + 1;

                         $next  = array(
                          
                        'sku' => $sku, 
                        'full_name' => $full_name, 
                        'profile_pic' => $profile_picture, 
                        'net_worth' => $net_worth, 
                        'country' => $country,
                        'verified_tag' => $this_inputtor_vtag, 
                        'last_online_formatted' => $date_time, 
                        'last_online_time' => $strStart, 
                        'investor_level' => $investor_level, 
                        'pott_name' => $thepot_name

                        );
                        array_push($linkUpsReturn["hits"], $next); 

                        if($count >= 50){
                          break;
                        }   
                      }

                    }

          }

                    echo json_encode($linkUpsReturn); exit;
        }////// END OF PASSWORD CHECK

    }
  }
