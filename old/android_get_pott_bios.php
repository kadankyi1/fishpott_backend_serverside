<?php
if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['pott_name']) && trim($_POST['pott_name']) != "" ) {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $pott_name = mysqli_real_escape_string($mysqli, $_POST['pott_name']);
    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
    	  $newsfeedReturn = array();
		  $newsfeedReturn["pott_info"] = array();
          if($dbpass == $mypass && $flag == 0){

            $query = "SELECT * FROM investor WHERE pot_name = '$pott_name'";

                              //$numrows = mysql_num_rows($query);
                              $result = $mysqli->query($query);

                              if (mysqli_num_rows($result) != "0") {

                                  $row = $result->fetch_array(MYSQLI_ASSOC);

                                  $investor_level = intval($row["investing_points"]);
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


                                    $db_first_name = $row["first_name"];
                                    $db_last_name = $row["last_name"];
                                    $db_full_name = $db_first_name . " " . $db_last_name;
                                    $db_dob = $row["dob"];
                                    //$db_phone = $row["phone"];
                                    //$db_email = $row["email"];
                                    //$db_sex = $row["sex"];
                                    $db_investor_id = $row["investor_id"];
                                    $db_country = $row["country"];
                                    $db_net_worth = $row["net_worth"];
                                    $db_profile_picture = $row["profile_picture"];
                                    $db_status = $row["status"];
                                    $db_verified_tag = $row["verified_tag"];

                                    if (!file_exists("../pic_upload/" . $db_profile_picture)) {

                                        $db_profile_picture = "";
                                        
                                        } else {

                                          $db_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $db_profile_picture; 
                                        }

           $query = "SELECT status FROM linkups WHERE (sender_id = '$myid' AND receiver_id = '$db_investor_id') OR (sender_id = '$db_investor_id' AND receiver_id = '$myid') ";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                              $status = $row["status"];
                              if($status == 1){
                     			$our_link = "1";
                              } else {
                     			$our_link = "0";
                              }

                    } else {
                     	$our_link = "0";
                    }

                    $query = "SELECT COUNT(*) FROM linkups WHERE sender_id = '$db_investor_id'";

                    $result = $mysqli->query($query);

                    if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $linkups = $row["COUNT(*)"];

                    } else {
                        $linkups = "0";
                    }

                    $query = "SELECT COUNT(*) FROM linkups WHERE receiver_id = '$db_investor_id'";

                    $result = $mysqli->query($query);

                    if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $links = $row["COUNT(*)"];

                    } else {
                        $links = "0";
                    }

                    $this_blocked_id = $myid . "_" . $pott_name;

                    $query = "SELECT blocked_pottname FROM mern_ha_me_fuo WHERE block_action_id = '$this_blocked_id'";   

                    $result = $mysqli->query($query);
                        
                    if (mysqli_num_rows($result) != 0) {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $blocked_status = "1";

                      } else {

                          $blocked_status = "0";
                          
                      }

                //$db_full_name = htmlspecialchars($db_full_name);

                                   $next  = array(

                                  'full_name' => $db_full_name, 
                                  'dob' => $db_dob,
                                  'country' => $db_country,
                                  'investor_level' => $investor_level,
                                  'net_worth' => $db_net_worth,
                                  'profile_picture' => $db_profile_picture, 
                                  'status' => $db_status, 
                                  'our_link' => $our_link, 
                                  'mylinkups' => $linkups,
                                  'linkstome' => $links, 
                                  'verified_tag' => $db_verified_tag, 
                                  'blocked_status' => $blocked_status, 
                                  'investor_id' => $db_investor_id

                                  );
                                  array_push($newsfeedReturn["pott_info"], $next);    
                                  echo json_encode($newsfeedReturn); exit;
                                } else {

                                    echo json_encode($newsfeedReturn); exit;
                                }

          }




      }

}