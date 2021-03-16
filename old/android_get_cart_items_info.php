<?php

if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['i_country']) && trim($_POST['i_country']) != "" && 
	isset($_POST['item_1']) && trim($_POST['item_1']) != "" && 
	isset($_POST['items_number']) && trim($_POST['items_number']) != "") {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $i_country = mysqli_real_escape_string($mysqli, $_POST['i_country']);
    $item_1 = mysqli_real_escape_string($mysqli, $_POST['item_1']);
    $items_number = mysqli_real_escape_string($mysqli, $_POST['items_number']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $i_country = trim($i_country);
    $item_1 = trim($item_1);
    $items_number = intval($items_number);
    if($i_country != "Ghana" && $i_country != "United Kingdom" && $i_country != "USA"){

      $i_country = "USA";
      
    }


    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);
          $newsfeedReturn["hits"] = array();

          if($mypass == $dbpass && $dbflag == 0) {

            $newsfeedReturn["seller_info"] = array();
            $newsfeedReturn["item_info"] = array();

                    $query = "SELECT * FROM nsesa WHERE sku = 1";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $GHS_USD = $row["GHS_USD"];
                      $USD_GHS = $row["USD_GHS"];
                      $GHS_GBP = $row["GHS_GBP"];
                      $GBP_GHS = $row["GBP_GHS"];
                      $USD_GBP = $row["USD_GBP"];
                      $GBP_USD = $row["GBP_USD"];
                      $coins_GHS = $row["coins_GHS"];
                      $coins_USD = $row["coins_USD"];
                      $coins_GBP = $row["coins_GBP"];
                      $rates = 1;
                    } else {

                        echo json_encode($newsfeedReturn); exit;
                    }


            /************************************************************************

                                 POTT DATA FETCH START

            ************************************************************************/


            /************************************************************************/


                for($i == 0; $i < $items_number; $i++){

                	$item_index_num = $i + 1;
                	$item_index = "item_" . strval($item_index_num);
                	$news_id = trim($_POST[$item_index]);


              $query = "SELECT * FROM newsfeed WHERE news_id = '$news_id' ";

                $result = $mysqli->query($query);

              if (mysqli_num_rows($result) != "0") {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $sku = $row["sku"];
                  $type = $row["type"];
                  $inputtor_type = $row["inputtor_type"];
                  $inputtor_id = $row["inputtor_id"];

                  $strStart = $row["date_time"];

                  include(ROOT_PATH . 'inc/time_converter.php');

                  $news = trim($row["news"]);
                  $sponsored_tag = trim($row["sponsored_tag"]);
                  $news_id = trim($row["news_id"]);
                  $news_image = trim($row["news_image"]);
                  $news_aud = trim($row["news_aud"]);
                  $news_video = trim($row["news_video"]);
                  $news_id_ref = trim($row["news_id_ref"]);
                  $flag = trim($row["flag"]);
                  $skip_add = 0;

                  if($flag != 0){
                    continue;
                  }

                if(!isset($pott_mentions_tick)){
                  $pott_mentions_tick = "";
                  $pott_mentions_tick = "";
                }

                if(!isset($url_card_tick) || $url_card_tick == ""){
                      $url_card_tick = "";
                      $my_url_mentions = "";
                      $url_title = "";
                      $url_image = "";
                } 

                    if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                        $news_image = "";
                        } else {

                          $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                        }
                    if (trim($news_aud) == "" || !file_exists("../user/" . $news_aud)) {

                        $news_aud = "";
                        } else {

                          $news_aud = HTTP_HEAD . "://fishpott.com/user/" . $news_aud; 
                        }

                    if (trim($news_video) == "" || !file_exists("../user/" . $news_video)) {

                        $news_video = "";
                        } else {

                          $news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video; 
                        }

                  $query = "SELECT * FROM investor WHERE investor_id = '$inputtor_id' ";
                  $result = $mysqli->query($query);
                  if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $first_name = $row["first_name"];
                      $last_name = $row["last_name"];
                      $thepot_name = $row["pot_name"];
                      $full_name = $first_name . " " . $last_name;
                      $profile_picture = $row["profile_picture"];
                      $investor_verified_tag = $row["verified_tag"];
                      if(strval($investor_verified_tag) != "1"){
                        continue;
                      }
                      if (!file_exists("../pic_upload/" . $profile_picture)) {

                          $profile_picture = "";
                          } else {

                            $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                          }

                      $this_inputtor_vtag = $row["verified_tag"];
                    } else {

                          $seller_country = "na";
                          $skip_add = 1;
                          $news_image = "";
                    }

                    $query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$news_id' AND like_type = 1";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $p_num_of_likes = $row["COUNT(*)"];

                    if(!isset($p_num_of_likes)  || $p_num_of_likes == 0) {

                        $p_num_of_likes == " ";          
                    }

                    $query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$news_id' AND like_type = 0";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $p_num_of_dislikes = $row["COUNT(*)"];

                    if(!isset($p_num_of_dislikes) || $p_num_of_dislikes == 0) {

                        $p_num_of_dislikes == " ";          
                    }

                    $query = "SELECT * FROM likes WHERE likes_news_id = '$news_id' AND liker_investor_id = '$investor_id' ";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $db_like_type = $row["like_type"];

                          if($db_like_type == 1) {

                              $set_like_btn_color =  1;
                              $set_dis_like_btn_color =  0;

                          } else {

                            $set_like_btn_color =  0;
                              $set_dis_like_btn_color =  1;

                          }

                      }

                  if ($type == "up4sale") {

                    $type_title = "Up 4 Sale";

                      $query = "SELECT * FROM up4sale WHERE up4sale_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $sale_status = $row["sale_status"];
                        $up4sale_item_name = $row["item_name"];
                        $item_weight_type = $row["item_weight_type"];
                        $up4sale_item_location = $row["item_location"];
                        $rest = substr($up4sale_item_location, 0,1);
                        $up4sale_item_delivery = $row["item_delivery"];
                        $currency = trim($row["currency"]);
                        $up4sale_item_price = $row["item_price"];
                        $number_sold = $row["number_sold"];

                        $number_sold = trim($row["number_sold"]);
                        $number_sold = intval($number_sold);

                        $item_quantity = trim($row["item_quantity"]);
                        $item_quantity = intval($item_quantity);

                        $item_quantity = $item_quantity - $number_sold;


                        $verified_tag = $row["verified_tag"];
                        $convert_amt = floatval($up4sale_item_price);
                        if(substr($up4sale_item_location, 1,1) == "."){

                            $coor = 1;

                        } else if(substr($up4sale_item_location, 0,5) == "ferry"){

                            $query = "SELECT add_long, add_lat FROM addressofmine WHERE add_id = '$up4sale_item_location' ";
                            $result = $mysqli->query($query);
                            if (mysqli_num_rows($result) != "0") {

                                $row = $result->fetch_array(MYSQLI_ASSOC);
                                $add_long = $row["add_long"];
                                $add_lat = $row["add_lat"];
                                $up4sale_item_location = $add_lat . "," . $add_long;
                                $coor = 1;
                            } else {

                                $up4sale_item_location = "";

                            }

                        }
                         if(isset($coor) && $coor == 1){
                  $url= 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $up4sale_item_location . '&sensor=false&key=' . GOOGLE_MAP_KEY;
                                  $geocode=file_get_contents($url);
                                  $output= json_decode($geocode);

                                  for($j=0;$j<count($output->results[0]->address_components);$j++){
                                      if($j == 0 && $output->results[0]->address_components[$j]->long_name != ""){
                                            $up4sale_item_location = 'Street Name/Number :  '. $output->results[0]->address_components[$j]->long_name;

                                      } else {

                                        $up4sale_item_location = $output->results[0]->address_components[$j]->long_name;
                                      }

                                  }
                              }
                              unset($coor);
                              if($item_quantity > 1) {

                                $more = "You can buy up to " . $item_quantity . "  pieces of this item";

                              } else {

                                $more = "";
                              }

                        $item_type = "up4sale";
                        $item_name = $up4sale_item_name;
                        $item_location = $up4sale_item_location;
                        $item_weight_type = $item_weight_type;
                        $item_quantity = $item_quantity;
                        $item_verified_tag = $verified_tag;
                        $sale_status = $sale_status;
                        if($item_quantity <= 0){
                            $sale_status = 1;

                        }

                      } else {

                          $skip_add = 1;
                          $convert_amt = "na";
                      }

                    
                  } else {
                  	continue;
                  }

                  if($type != "shared_news"){

                            $news_id_ref = "";
                            $sn_profile_picture = "";
                            $sn_full_name = "";
                            $sn_type = "";
                            $sn_news = "";
                            $sn_news_image = "";
                            $sn_news_video = "";
                            $sn_news_aud = "";

                  }
                if(isset($convert_amt) && $convert_amt != ""){

                        if($currency == "GHS" || $currency == "Ghc") {

                          $seller_country = "Ghana";

                        } elseif($currency == "GBP") {

                          $seller_country = "United Kingdom";

                        } else {

                          $seller_country = "USA";
                        }

                        include(ROOT_PATH . 'inc/android_currency_converter.php');
                  } else {

                          $sale_status = "3";
                          $new_amt_user_str = "FREE";
                          $new_amt_user_currency = "USD";
                          $new_amt_pg = 0;
                  }


                if($type == "up4sale") { 

                        $news_sub = $up4sale_item_name . " " . $new_amt_user_str . " " . $up4sale_item_location . " Delivery : " . $up4sale_item_delivery . ". " .  $more;

                } elseif ($type == "shares4sale")  {

                          $news_sub = $share_name . " ," . $country_origin . ".  Quantity : " . $num_on_sale . "Price Per Share : " . $new_amt_user_str;

                } elseif ($type == "fundraiser")  {

                        $news_sub = $fundraiser_name . ", Target Amount : " . $new_amt_user_str . ", Closing Date : " . $fundraiser_start_date;

                } elseif ($type == "event")  {

                        $news_sub = $event_name . " at " . $event_venue . " on " . $event_date . ", " . $event_time . ". Rate : " . $new_amt_user_str;


                } elseif($type == "shared_news"){
                  if($inputtor_id != $sn_inputtor_id){

                    $news_sub = $full_name . " reposted on " .  $sn_full_name;

                  } else {

                    $news_sub = $full_name . " reposted";

                  }
                        
                }


                $table_name = "comments"; $item_1 = "sku"; $done = 0; 
              include(ROOT_PATH . 'inc/get_num_of_comments.php'); 
              include(ROOT_PATH . 'inc/db_connect_autologout.php'); 

              $table_name = "comments";
              $item_1 = "sku";
              $item_2 = "news_id";
              $column1_name = "inputtor_id";
              $column1_value = $investor_id;
              $column2_name = "news_id";
              $column2_value = $news_id;
              $pam1 = "s";
              $pam2 = "s";

              include(ROOT_PATH . 'inc/select2_where2_prepared_statement.php'); 

              if ($item_1 != "sku" && $item_1 != "" && $item_2 != "news_id" && $item_2 != ""){

                $user_commentted = 1;
                $item_1 = "sku";
                $item_2 = "news_id";


              } else {

                $user_commentted = 0;
                $item_1 = "sku";
                $item_2 = "news_id";

              }

              $table_name = "adetor";
              $order_by = "sku";
              include(ROOT_PATH . 'inc/db_connect_autologout.php'); 
              include(ROOT_PATH . 'inc/get_latest_sku.php'); 
              if(!isset($latest_sku) || $latest_sku != ""){

                $latest_sku = 0;
              }
              $p_num_of_buys = 1;
              $adetor_cut = 0;
              for($latest_sku; $latest_sku > 0; $latest_sku--){

                $query = "SELECT adetor_id_1 FROM adetor WHERE adetor_news_id = '$news_id' AND sku = $latest_sku";  
                  $result = $mysqli->query($query);

                  if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $p_num_of_buys = $p_num_of_buys + 1;

                    }

                    $adetor_cut = $adetor_cut + 1;
                    if ($adetor_cut == 100) {
                      break;
                    }
                  }

                    $sql = "SELECT COUNT(*) FROM newsfeed WHERE news_id_ref = '$news_id'";
                  $result = $mysqli->query($sql);

                  $row = $result->fetch_array(MYSQLI_ASSOC);


                  $count_shared = $row["COUNT(*)"];

                  if(!isset($news_sub)){
                    $news_sub = "";
                  }

                  if(!isset($type_title)){
                    $type_title = "";
                  }

                  if(!isset($this_inputtor_vtag)){
                    $this_inputtor_vtag = "";
                  }

                  if(!isset($set_like_btn_color)){
                    $set_like_btn_color = "";
                  }

                  if(!isset($set_dis_like_btn_color)){
                    $set_dis_like_btn_color = "";
                  }

                  if(!isset($shared_news_set_like_btn_color)){
                    $shared_news_set_like_btn_color = "";
                  }

                  if(!isset($shared_news_set_dis_like_btn_color)){
                    $shared_news_set_dis_like_btn_color = "";
                  }

              include(ROOT_PATH . 'inc/db_connect_autologout.php'); 

              if($type == "shared_news"){
                $news_video = $sn_news_video;
                if($sn_full_name == ""){
                  $skip_add = 1;
                }
              }

              if(!isset($skip_add) || $skip_add != 1){
                    if($new_amt_user_currency == "GHS") {

                      $new_amt_user_currency = "Gh¢";

                    }
                    //$news = htmlspecialchars($news);
                    //$news_sub = htmlspecialchars($news_sub);
                    //$news = htmlspecialchars($news);
                          $next  = array(
                            'hit_status' => "1",
                            'sale_status' => $sale_status,
                            'seller_verified_status' => $investor_verified_tag,
                            'item_type' => $item_type,
                            'item_news_id' => $news_id,
                            'item_name' => $item_name,
                            'item_image' => $news_image,
                            'item_video' => $news_video,
                            'price_per_item' => $new_amt_user,
                            'this_transaction_currency' => $new_amt_user_currency,
                            'item_weight_type' => $item_weight_type,
                            'item_location' => $item_location,
                            'item_quantity' => $item_quantity,
                            'item_location' => $item_location,
                            'item_verified_tag' => $item_verified_tag,
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $thepot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $type,
                            'news_type_title' => $type_title,
                            'news_maker_verified_status' => $this_inputtor_vtag,
                            'news_main' => $news,
                            'news_sub' => $news_sub,
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => $news_aud,
                            'news_set_like_color' => $set_like_btn_color,
                            'news_likes_num' => $p_num_of_likes,
                            'news_set_dislike_color' => $set_dis_like_btn_color,
                            'news_dislikes_num' => $p_num_of_dislikes,                      
                            'shared_news_set_like_color' => $shared_news_set_like_btn_color,
                            'shared_news_set_dislike_color' => $shared_news_set_dis_like_btn_color,
                            'news_set_comment_color' => $user_commentted,
                            'news_comment_num' => $count_comments,
                            'news_share_num' => $count_shared,
                            'news_set_buy_color' => "1",
                            'news_buy_num' => $p_num_of_buys,
                            'news_url_web_address' => $full_url,
                            'news_url_title' =>  $url_title,
                            'news_url_image' =>  $url_image,
                            'news_set_url_card' => $url_card_tick,
                            'news_sponsored_tag' => $sponsored_tag
                            );
                          array_push($newsfeedReturn["hits"], $next);
                      }

              } else {

              	continue;

              }
          }
                  
          echo json_encode($newsfeedReturn); exit;

          } //END OF PASSWORD CHECK

        }

    }
