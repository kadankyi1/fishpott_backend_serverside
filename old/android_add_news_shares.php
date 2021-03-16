<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['news_type']) && trim($_POST['news_type']) != "" && trim($_POST['news_type']) == "shares4sale" && isset($_POST['inputtor_type']) && trim($_POST['inputtor_type']) != "" && isset($_POST['set_price']) && trim($_POST['set_price']) != "" && isset($_POST['target']) && trim($_POST['target']) != "" && isset($_POST['this_currency']) && trim($_POST['this_currency']) != "" && isset($_POST['item_current_location']) && trim($_POST['item_current_location']) != ""  && isset($_POST['addNewsText']) && trim($_POST['addNewsText']) != "" && isset($_POST['raw_pass']) && trim($_POST['raw_pass']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $newsType = mysqli_real_escape_string($mysqli, $_POST['news_type']);
    $inputtor_type = mysqli_real_escape_string($mysqli, $_POST['inputtor_type']);
    $raw_pass = mysqli_real_escape_string($mysqli, $_POST['raw_pass']);

    $chosen_shares = mysqli_real_escape_string($mysqli, $_POST['item_current_location']);
    $set_price = mysqli_real_escape_string($mysqli, $_POST['set_price']);
    $num_on_sale = mysqli_real_escape_string($mysqli, $_POST['target']);
    $payment_way = "MM";
    $this_currency = mysqli_real_escape_string($mysqli, $_POST['this_currency']);
    //$parent_shares_id = mysqli_real_escape_string($mysqli, $_POST['item_current_location']);
    $addNewsText = mysqli_real_escape_string($mysqli, $_POST['addNewsText']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $newsType = trim($newsType);
    $inputtor_type = trim($inputtor_type);
    $chosen_shares = trim($chosen_shares);
    $set_price = trim($set_price);
    $num_on_sale = trim($num_on_sale);
    $raw_pass = trim($raw_pass);
    $this_currency = trim($this_currency);
    //$parent_shares_id = trim($parent_shares_id);
    $investor_id = $myid;
	define('KB', 1024);
	define('MB', 1048576);
	define('GB', 1073741824);
	define('TB', 1099511627776);
	if(isset($_POST['share_news_id'])){

    $share_news_id = mysqli_real_escape_string($mysqli, $_POST['share_news_id']);

	} else {

	$share_news_id = "";

	}

  $password = $raw_pass;

  include(ROOT_PATH . 'inc/pw_fold.php');

	$t = time();
	$r_t = date("Y-m-d",$t);
	$ext = $r_t . $t;
    $today = date("F j, Y");

	$date_time = date("Y-m-d H:i:s");
	$date_time = trim($date_time);

    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);

          if($mypass == $dbpass && $flag == 0 && $e_password == $dbpass) {
				$news_id = uniqid($investor_id, TRUE);

				$query = "SELECT num_of_shares, parent_shares_id FROM shares_owned WHERE share_id = '$chosen_shares' AND  owner_id = '$myid'";   

				$result = $mysqli->query($query);

				if (mysqli_num_rows($result) != 0) {

						$row = $result->fetch_array(MYSQLI_ASSOC);
						$parent_shares_id = trim($row["parent_shares_id"]);
						$sys_num_of_shares = trim($row["num_of_shares"]);
						$sys_num_of_shares = intval($sys_num_of_shares);

						if($num_on_sale > $sys_num_of_shares){
							echo "You can't sell more shares than you have.";
							exit;
						} 

				} else {
					echo "We couldn't find your shares.";
					exit;
				}

				$query = "SELECT curr_max_price FROM shares_worso WHERE  parent_shares_id = '$parent_shares_id'";   

				$result = $mysqli->query($query);

				if (mysqli_num_rows($result) != 0) {

						$row = $result->fetch_array(MYSQLI_ASSOC);
						$sys_curr_max_price = trim($row["curr_max_price"]);
						$sys_curr_max_price = floatval($sys_curr_max_price);

						if($set_price > $sys_curr_max_price){

							echo "You can't sell shares at a price greater than the current maximum price.";
							exit;
						} 

				} else {
					echo "Something went awry. Try again later 1";
					exit;
				}


				if(!isset($audAdd)) { $audAdd = 0;}
				if(!isset($imgAdd)) { $imgAdd = 0;}
				if(!isset($videoAdd)) { $videoAdd = 0;}
				if(isset($_POST['addNewsText']) && $_POST['addNewsText'] != "") {
					$addNewsText = $_POST['addNewsText'];
					$addNewsText2 = $addNewsText;
    				//$addNewsText2 = str_replace("@mylinkups","",$addNewsText);
					//$addNewsText = mysqli_real_escape_string($mysqli, $addNewsText);
					$textAdd = 1;

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

						$table_name = "investor";
						$item_1 = "investor_id";
						$column1_name = "pot_name";
						$column1_value = $mentionedUser;
						$pam1 = "s";
						include(ROOT_PATH . 'inc/db_connect.php');
						include(ROOT_PATH . 'inc/select1_where1_prepared_statement.php');
						include(ROOT_PATH . 'inc/db_connect.php');
						if($item_1 != "" && $item_1 != "investor_id") {
								$tagged_i = $item_1;
								$table_name = "mafrewo";

								$column1_name = "news_id";
								$column1_value = $news_id;

								$column2_name = "i_tagged_id";							
								$column2_value = $tagged_i;

								$column3_name = "tagger_id";							
								$column3_value = $investor_id;

								$pam1 = "s";
								$pam2 = "s";
								$pam3 = "s";

								include(ROOT_PATH . 'inc/insert3_prepared_statement.php');
								include(ROOT_PATH . 'inc/db_connect.php');


						  }

					}


				} else {

					$textAdd = 0;
				}

				include(ROOT_PATH . 'inc/db_connect.php');

				 if ($status == 1) {

						if ($videoAdd == 1) {

				    		if (move_uploaded_file($_FILES["upload_news_video"]["tmp_name"], $target_video)) {

				    			$video_added = 1;
				    			$dontsendmsg = 0;
				    		} else {

				    			$video_added = 0;
				    			$dontsendmsg = 1;
				    		}

						} else {
				    		
							$video_db_name = "";
						}

						if ($audAdd == 1) {
				    		if (move_uploaded_file($_FILES["upload_news_aud"]["tmp_name"], $target_aud)) {

				    			$aud_added = 1;
				    			$dontsendmsg = 0;
				    		} else {

				    			$aud_added = 0;
				    			$dontsendmsg = 1;
				    		}
						}  else {

							$aud_db_name = "";
						}

						if ($imgAdd == 1) {

				    		if (move_uploaded_file($_FILES["upload_news_pic"]["tmp_name"], $target_pic)) {

				    			$img_added = 1;
				    			$dontsendmsg = 0;
				    		} else {

				    			$img_added = 0;
				    			$dontsendmsg = 1;
				    		}

						}  else {

							$pic_db_name = "";
						}
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

								$column1_value = $newsType;
								$column2_value = $inputtor_type;
								$column3_value = $investor_id;
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
								$done = 0;
								include(ROOT_PATH . 'inc/insert10_prepared_statement.php');
								if($done == 1  && trim($addNewsText) != "") {

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
                      $linkee_id = trim($row["investor_id"]);

  $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
  $key = $key . $fcm_token_ios . $fcm_token_web;

//////////////////////    FCM  START      /////////////////////////

  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott";

  $myalert = $receiver_fullname . " mentioned you in a post";

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




	} // END

								include(ROOT_PATH . 'inc/db_connect.php');
								$table_name = "shares4sale";

								$column1_name = "shares4sale_owner_id";
								$column2_name = "sharesOnSale_id";
								$column3_name = "selling_price";
								$column4_name = "num_on_sale";
								$column5_name = "shares_news_id";
								$column6_name = "currency";
								$column7_name = "parent_shares_id";

								$column1_value = $investor_id;
								$column2_value = $chosen_shares;
								$column3_value = $set_price;
								$column4_value = $num_on_sale;
								$column5_value = $news_id;
								$column6_value = $this_currency;
								$column7_value = $parent_shares_id;

								$pam1 = "s";
								$pam2 = "s";
								$pam3 = "s";
								$pam4 = "i";
								$pam5 = "s";
								$pam6 = "s";
								$pam7 = "s";
								include(ROOT_PATH . 'inc/insert7_prepared_statement.php');
						
								//echo "done : " . $done . "<br>";
					        } else {
					        	//echo "0 HERE";
					        }


      		}// END OF PASSWORD CHECK


    }
}
