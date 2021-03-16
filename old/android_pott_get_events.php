<?php

function check_https($url){
$ch = curl_init ('https://'.$url);

curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); //its a  HEAD
curl_setopt ($ch, CURLOPT_NOBODY, true);          // no body

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);  // in case of redirects
curl_setopt ($ch, CURLOPT_VERBOSE,        0); //turn on if debugging
curl_setopt ($ch, CURLOPT_HEADER,         1);     //head only wanted

curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);    // we dont want to wait forever

curl_exec ( $ch ) ;

$header = curl_getinfo($ch,CURLINFO_HTTP_CODE);
//var_dump ($header);

if($header===0){//no ssl
return false;
}else{//maybe you want to check for 200
return true;
}

}

if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['news_sku']) && trim($_POST['news_sku']) != "" && 
  isset($_POST['gettype']) && trim($_POST['gettype']) != "" && 
  isset($_POST['pott_sys_id']) && trim($_POST['pott_sys_id']) != "" && 
  isset($_POST['i_country']) && trim($_POST['i_country']) != "") {
  require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8mb4');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $news_sku = mysqli_real_escape_string($mysqli, $_POST['news_sku']);
    $gettype = mysqli_real_escape_string($mysqli, $_POST['gettype']);
    $pott_sys_id = mysqli_real_escape_string($mysqli, $_POST['pott_sys_id']);
    $i_country = mysqli_real_escape_string($mysqli, $_POST['i_country']);

    $myid = trim($myid);
    $investor_id = $myid;
    $mypass = trim($mypass);
    $news_sku = trim($news_sku);
    $news_sku = intval($news_sku);
    $news_sku_real = $news_sku;
    $gettype = trim($gettype);
    $i_country = trim($i_country);
    if($i_country != "Ghana" && $i_country != "United Kingdom" && $i_country != "USA"){

      $i_country = "USA";
      
    }

    $today = date("F j, Y");
    $query = "SELECT password FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);

          if($mypass == $dbpass) {

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


            $newsfeedReturn["hits"] = array();
            $newsfeedReturn["transfers"] = array();
            $newsfeedReturn["general_notification"] = array();
            $newsfeedReturn["new_news_count"] = array();
            $newsfeedReturn["chats"] = array();
            $ads = array();

              $ads_cnt = 0;
    $query="SELECT ad_adetor_news_id, ad_adetor_start_date, ad_adetor_duration, ad_adetor_days_done, sku, ad_adetor_id FROM ad_adetor WHERE ad_adetor_target_country = '$i_country' AND ad_adetor_complete = 0 ORDER BY sku DESC";

            $result = $mysqli->query($query);
              while($row=$result->fetch_array()) {

                  $ad_sku = trim($row["sku"]);
                  $ad_sku = intval($ad_sku);
                  $ad_adetor_id = trim($row["ad_adetor_id"]);
                  $ad_adetor_news_id = trim($row["ad_adetor_news_id"]);
                  $ad_adetor_start_date = trim($row["ad_adetor_start_date"]);
                  $ad_adetor_duration = trim($row["ad_adetor_duration"]);
                  $ad_adetor_duration_num = intval($ad_adetor_duration);
                  $ad_adetor_days_done = trim($row["ad_adetor_days_done"]);
                  $ad_adetor_days_done_num = intval($ad_adetor_days_done);

                $start_date_time = $ad_adetor_start_date;
                $start_date_time = trim($start_date_time);
                $yield_duration  = "+" . $ad_adetor_duration . " days";
                $end_date = date('Y-m-d', strtotime($yield_duration));

                  $now = time(); // or your date as well
                  $your_date = strtotime($start_date_time);
                  $datediff = $now - $your_date;


                  $diff = (floor($datediff / (60 * 60 * 24)) + 1);


                if($diff >= $ad_adetor_duration_num){


            $query = "SELECT buyer_id FROM adetor WHERE adetor_id_short = '$ad_adetor_id' ";
              $result2 = $mysqli->query($query);
              if (mysqli_num_rows($result2) != "0") {

                $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                $buyer_id = $row2["buyer_id"];

//////////////////////    FCM  START      /////////////////////////
                      
        $query = "SELECT investor_id, pot_name, first_name, last_name, verified_tag, profile_picture, fcm_token FROM investor WHERE investor_id = '$buyer_id'";   

                $result2 = $mysqli->query($query);
                    
                if (mysqli_num_rows($result2) != 0) {

                      $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                    $linkee_id = trim($row2["investor_id"]);
                    $key = trim($row2["fcm_token"]);
                $linkee_full_name = trim($row2["first_name"]) . " " . trim($row2["last_name"]);
                  $linkee_pot_name = trim($row2["pot_name"]);                
  $linkee_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/uploads/2017-12-161513439813.png"; 

                $not_text = "Your promotion has ended";


  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

  $headers = array(
    'Authorization:key=' . $server_key, 
    'Content-Type:application/json');

  $title = "FishPott";

  $myalert = $not_text;

$fields = array('to' => $key,
      'data' => array(
        'notification_type' => "general_notification",
        'not_type_real' => "like",
        'not_pic' => $linkee_profile_picture,
        'not_title' => $title,
        'not_message' => $not_text,
        'not_image' => "",
        'not_video' => "",
        'not_text' => $not_text, 
        'not_pott_or_newsid' => $ad_adetor_news_id, 
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

}

//////////////////////    FCM  END      /////////////////////////                 

                }

      $query = "UPDATE  ad_adetor SET  ad_adetor_complete = 1 WHERE  sku = $ad_sku ";
      $result2 = $mysqli->query($query);

                } else {


                  $ads[$ads_cnt] = $ad_adetor_news_id;
                  $ads_cnt = $ads_cnt + 1;

                }

              }

              if(!isset($skip_add)){
                $skip_add = 0;
              }

            if($news_sku == "0" || $gettype == "up"){
                    $f_Ids = array();
                    $f_Ids_cnt = 0;

                    $table_name = "event";
                    $order_by = "sku";
                    include(ROOT_PATH . 'inc/get_latest_sku.php');
                    if($skip == 1){

                      echo $newsfeedReturn; exit;

                    }
                  $news_sku = $latest_sku;
                  $gettype = "down";

              } 

                    for ($news_sku; $news_sku > 0; $news_sku--) { 

                        $query = "SELECT * FROM event WHERE sku = $news_sku";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0") {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $f_Ids["sku"][$f_Ids_cnt] = trim($row["sku"]);
                              $f_Ids["news_id"][$f_Ids_cnt] = trim($row["event_news_id"]);
                              $f_Ids_cnt = $f_Ids_cnt + 1;
                              if($f_Ids_cnt == 50){
                                break;
                              }
                            }
                  }

                if($gettype == "down"){

                  $i = 0;

                  $stop_check = $f_Ids_cnt;
                  $i_stop = 0;

                    if($f_Ids_cnt <= 0){

                      echo $newsfeedReturn; exit;

                    }


                  $ads_cnt = 0;
                  $ads_cnt_for_reset = 0;
            for ($i; $i < $stop_check; $i++) { 

              if($ads_cnt_for_reset == 2 && count($ads) > $ads_cnt){

                $ads_cnt_for_reset = 0;
                $ad_news_id = $ads[$ads_cnt];
                $add_set = 1;
                $ads_cnt = $ads_cnt + 1;
                $query = "SELECT * FROM newsfeed WHERE news_id = '$ad_news_id'  AND flag = 0";

              } else {

                $news_id = $f_Ids["news_id"][$i];

                $ads_cnt_for_reset = $ads_cnt_for_reset + 1;
                $query = "SELECT * FROM newsfeed WHERE news_id = '$news_id'  AND flag = 0 AND inputtor_id = '$pott_sys_id'";
                unset($add_set);

              }



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
                  if(isset($add_set)){
                    $sponsored_tag = "1";
                    unset($add_set);
                  }
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


      if($news != ""){
        $addNewsText = $news;
        $my_pottname_mentions = array();
        $my_mentions_cnt = 0;
        preg_match_all("/\B@[a-zA-Z0-9]+/i", $addNewsText, $mentions);
        $mentions = array_map(function($str){ return substr($str, 1); }, $mentions[0]);
        foreach($mentions as $mentionedUser){

          $my_pottname_mentions[$my_mentions_cnt] = $mentionedUser;
          $pott_mentions_tick = 1;
          $my_mentions_cnt++;

            }


            preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $addNewsText, $match);
                $match = $match[0];

                foreach($match as $url){


          $my_url_mentions = $url;
          break;

        }

        //echo "url 111 -- " . $my_url_mentions . "<br>";
        //echo "url_card_tick 111 -- " . $url_card_tick . "<br>";

                        preg_match_all('#\bwww[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $addNewsText, $match);
                            $match = $match[0];
                            foreach($match as $url){

                          $url_card_tick = 1;

                        $urlIshttps =  check_https($my_url_mentions);

                        if($urlIshttps == 1){
                            
                          $my_url_mentions = $url;
                          $full_url = "https://" . $url;
                          break;

                        } else {

                          $my_url_mentions = $url;
                          $full_url = "http://" . $url;
                          break;

                        }
                        unset($urlIshttps);
                //$addNewsText = str_replace($url, $new_str , $addNewsText);
                                  }
        //echo "url 222 -- " . $my_url_mentions . "<br>";
        //echo "url_card_tick 222 -- " . $url_card_tick . "<br>";
                }

                /// END HERE

                if(!isset($pott_mentions_tick)){
                  $pott_mentions_tick = "";
                  $pott_mentions_tick = "";
                }

                if(!isset($url_card_tick) || $url_card_tick == ""){
                  $url_card_tick = "";
          $my_url_mentions = "";
          $url_title = "";
          $url_image = "";
                } else {

          $doc = new DOMDocument();
          @$doc->loadHTMLFile($full_url);
          $xpath = new DOMXPath($doc);
          $url_title =  $xpath->query('//title')->item(0)->nodeValue;  
            //$url="http://assemblynewsgh.com/gallery.php";

              $handle = curl_init($my_url_mentions);
              curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

              $response = curl_exec($handle);

              $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
              if($httpCode == 403) {


                        $url_card_tick = "";
                $my_url_mentions = "";
                $url_title = "";
                $url_image = "";
              curl_close($handle);
              } else {
                  $html = file_get_contents($full_url);

              curl_close($handle);

            ///////////////////////
            

            $doc = new DOMDocument();
            @$doc->loadHTML($html);

            $tags = $doc->getElementsByTagName('img');

            foreach ($tags as $tag) {

                   $url_image = $tag->getAttribute('src');
                   break;
            }


            preg_match_all('#\bwww[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $url_image, $match);
            $match = $match[0];
            foreach($match as $url){

                        $image_url_check = 1;

                    $urlIshttps =  check_https($url);

                    if($urlIshttps == 1){
                        
                        $image_url_check = 1;
                        break;

                    } else {

                        $image_url_check = 1;
                        break;

                    }
                  }
            if(!isset($image_url_check)){
                    $image_url_check = 0;
            }

            if($image_url_check != 1){
                $r = parse_url($full_url);
                $url_image = $r["scheme"] . "://" . $r["host"] . "/" . $url_image;

            }    
          }             
        
        }

        //echo "url 333 -- " . $my_url_mentions . "<br>";
        //echo "url_card_tick 333 -- " . $url_card_tick . "<br>";
        //echo "scheme 333 -- " . $r["scheme"] . "<br>";
        //echo "host 333 -- " . $r["host"] . "<br>";
 
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

                  if($type == "shares4sale") {

                    $type_title = "Shares 4 Sale";

                      $query = "SELECT * FROM shares4sale WHERE shares_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $shares_news_id = $row["shares_news_id"];
                        $parent_shares_id = $row["parent_shares_id"];
                        $sharesOnSale_id = $row["sharesOnSale_id"];
                        $shares4sale_owner_id = $row["shares4sale_owner_id"];
                        $selling_price = $row["selling_price"];
                        $currency = $row["currency"];
                        $num_on_sale = trim($row["num_on_sale"]);
                        $number_sold = $row["number_sold"];
                        $num_on_sale = intval($num_on_sale);
                        $number_sold = intval($number_sold);
                        $num_on_sale = $num_on_sale - $number_sold;
                        if($num_on_sale <= 0){
                          continue;
                        }
                        $convert_amt = floatval($selling_price);
                        $query = "SELECT * FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";
                        $result = $mysqli->query($query);
                        if (mysqli_num_rows($result) != "0") {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $share_name = $row["share_name"];
                          $company_name = $row["parent_company_name"];
                          $yield_per_share = trim($row["yield_per_share"]);
                          $yield_duration = $row["yield_duration"];
                          $value_change_rate = $row["value_change_rate"];
                          $country_origin = trim($row["country_origin"]);
                          $seller_country = "Ghana";
                        $convert_amt = $yield_per_share * $num_on_sale;
                        $shares_conversion = 1;

                        include(ROOT_PATH . 'inc/android_currency_converter.php');
                        unset($shares_conversion);
                        $yield_per_share = $new_amt_user_str;

                        $convert_amt = $selling_price;
                        
                          $shares_logo = trim($row["shares_logo"]);
                          $news_image = $shares_logo;
                          if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                            $news_image = "";
                            
                            } else {

                              $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                            }


                        } else {

                          $skip_add = 1;
                        }  

                      } else {

                          $skip_add = 1;
                        }

                  } elseif ($type == "up4sale") {

                    $type_title = "Up 4 Sale";

                      $query = "SELECT * FROM up4sale WHERE up4sale_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $up4sale_item_name = $row["item_name"];
                        $up4sale_item_location = $row["item_location"];
                        $rest = substr($up4sale_item_location, 0,5);
                        $rest = strtolower($rest);
                        $up4sale_item_delivery = $row["item_delivery"];
                        $currency = $row["currency"];
                        $up4sale_item_price = $row["item_price"];
                        $item_quantity = $row["item_quantity"];
                        $convert_amt = floatval($up4sale_item_price);
                        if(substr($up4sale_item_location, 1,1) == "."){

                            $coor = 1;

                        } else if($rest == "ferry"){
                           
                           include(ROOT_PATH . 'inc/db_connect_ferry.php');

                        $up4sale_item_location = strtolower($up4sale_item_location);

                            $query = "SELECT add_long, add_lat FROM addressofmine WHERE add_id = '$up4sale_item_location' ";
                            $result = $mysqli2->query($query);
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


                  $url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $up4sale_item_location . '&sensor=false&key=' . GOOGLE_MAP_KEY;
                                  $geocode=file_get_contents($url);
                                  $output= json_decode($geocode, true);

                    $up4sale_item_location =  $output["results"][1]["formatted_address"];
                              }
                              unset($coor);
                              if($item_quantity > 1) {

                                $more = "You can buy up to " . $item_quantity . "  pieces of this item";

                              } else {

                                $more = "";
                              }


                      } else {

                          $skip_add = 1;
                          $convert_amt = "na";
                      }

                    
                  } elseif ($type == "fundraiser") {

                    $type_title = "Fundraiser";

                      $query = "SELECT * FROM fundraiser WHERE f_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $fundraiser_name = $row["fundraiser_name"];
                        $fundraiser_start_date = $row["start_date"];
                        $fundraiser_end_date = $row["end_date"];
                        $fundraiser_target_amount = $row["target_amount"];
                        $currency = $row["currency"];
                        $fundraiser_num_of_contributors = $row["num_of_contributors"];
                        $convert_amt = floatval($fundraiser_target_amount);

                      } else {

                          $skip_add = 1;
                      }

                    
                  } elseif ($type == "shared_news") {

                    $type_title = "RePost";

                  $query = "SELECT * FROM newsfeed WHERE news_id = '$news_id_ref' ";
                      $result = $mysqli->query($query);
                if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $sku = $row["sku"];
                      $sn_type = $row["type"];
                      $sn_inputtor_type = $row["inputtor_type"];
                      $sn_inputtor_id = $row["inputtor_id"];

                      $sn_news = $row["news"];
                      $sn_news_id = $row["news_id"];
                      $sn_news_image = $row["news_image"];
                      $sn_news_aud = $row["news_aud"];
                      $sn_news_video = $row["news_video"];

                      if($sn_type == "shares4sale"){

                          $query = "SELECT parent_shares_id FROM shares4sale WHERE shares_news_id = '$news_id_ref' ";
                          $result = $mysqli->query($query);
                          
                          if (mysqli_num_rows($result) != "0") {

                            $row = $result->fetch_array(MYSQLI_ASSOC);

                            $parent_shares_id = $row["parent_shares_id"];

                            $query = "SELECT shares_logo FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";
                              $result = $mysqli->query($query);
                            if (mysqli_num_rows($result) != "0") {

                              $row = $result->fetch_array(MYSQLI_ASSOC);

                              $shares_logo = $row["shares_logo"];
                              $sn_news_image = $shares_logo;
                            }  

                            }

                      }

                        if (trim($sn_news_image) == "" || !file_exists("../user/" . $sn_news_image)) {

                            $sn_news_image = "";
                            } else {

                              $sn_news_image = HTTP_HEAD . "://fishpott.com/user/" . $sn_news_image; 
                            }
                        if (trim($sn_news_aud) == "" || !file_exists("../user/" . $sn_news_aud)) {

                            $sn_news_aud = "";
                            } else {

                              $sn_news_aud = HTTP_HEAD . "://fishpott.com/user/" . $sn_news_aud; 
                            }

                        if (trim($sn_news_video) == "" || !file_exists("../user/" . $sn_news_video)) {

                            $sn_news_video = "";
                            } else {

                              $sn_news_video = HTTP_HEAD . "://fishpott.com/user/" . $sn_news_video; 
                            }

                    $query = "SELECT COUNT(*) FROM newsfeed WHERE news_id_ref = '$news_id_ref'";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_num_of_shares = $row["COUNT(*)"];

                    if(!isset($sn_num_of_shares)  || trim($sn_num_of_shares) == "") {

                    $sn_num_of_shares == "0"; 

                    }


                    $query = "SELECT COUNT(*) FROM adetor WHERE adetor_news_id = '$news_id_ref'";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_num_of_buys = $row["COUNT(*)"];

                    if(!isset($sn_num_of_buys)  || trim($sn_num_of_buys) == "") {

                    $sn_num_of_buys == "0"; 

                    }


                         $query = "SELECT * FROM investor WHERE investor_id = '$sn_inputtor_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $sn_first_name = $row["first_name"];
                          $sn_last_name = $row["last_name"];
                          $sn_pott_name = $row["pot_name"];
                          $sn_verified_tag = $row["verified_tag"];
                          $sn_full_name = $sn_first_name . " " . $sn_last_name;
                          $sn_profile_picture = $row["profile_picture"];
                          if (!file_exists("../pic_upload/" . $sn_profile_picture)) {

                              $sn_profile_picture = "";

                              } else {

                                $sn_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $sn_profile_picture; 
                              }

                        } else {

                              $skip_add = 1;
                              $sn_news_image = "";
                          }

                    $query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$sn_news_id' AND like_type = 1";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_p_num_of_likes = $row["COUNT(*)"];

                    if(!isset($sn_p_num_of_likes)  || $sn_p_num_of_likes == 0) {

                    $sn_p_num_of_likes == " ";          
                    }

                    $query = "SELECT COUNT(*) FROM likes WHERE likes_news_id = '$sn_news_id' AND like_type = 0";   


                    $result = $mysqli->query($query);
                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_p_num_of_dislikes = $row["COUNT(*)"];

                    if(!isset($sn_p_num_of_dislikes) || $sn_p_num_of_dislikes == 0) {

                    $sn_p_num_of_dislikes == " ";          
                    }

                    $query = "SELECT * FROM likes WHERE likes_news_id = '$sn_news_id' AND liker_investor_id = '$investor_id' ";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_db_like_type = $row["like_type"];

                    if($sn_db_like_type == 1) {

                    $sn_set_like_btn_color =  1;

                    } else {

                    $sn_set_dis_like_btn_color =  1;

                    }

                    }


                      } else {

                          $skip_add = 1;
                      }

                    
                  } elseif ($type == "event") {

                      $type_title = "Event";

                      $query = "SELECT * FROM event WHERE event_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $event_name = $row["event_name"];
                        $event_venue = $row["venue"];
                        $event_date = $row["event_date"];
                        $event_time = $row["event_time"];
                        $event_ticket_cost = $row["ticket_cost"];
                        $currency = $row["currency"];
                        $num_of_goers = $row["num_of_goers"];
                        $event_image = $row["image"];
                        $event_verified_tag = $row["verified_tag"];
                        if($event_ticket_cost == "" ||  $event_ticket_cost == 0){

                          $convert_amt = 0;

                        } else {

                          $convert_amt = floatval($event_ticket_cost);

                        }
                      } else {

                          $skip_add = 1;
                      }
                  } elseif ($type == "news") {

                      $type_title = "news";

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

                  //echo "convert_amt : " . $convert_amt . "<br>";
                  //echo "seller_country : " . $seller_country . "<br>";
                  //echo "i_country : " . $i_country . "<br>"; exit;

                        include(ROOT_PATH . 'inc/currency_converter.php');
                  } else {

                          $new_amt_user_str = "FREE";
                          $new_amt_pg = 0;
                  }


                if($type == "up4sale") { 

                        $news_sub = $up4sale_item_name . ". Price :  " . $new_amt_user_str . " at " . $up4sale_item_location . ". Delivery Service: " . $up4sale_item_delivery . ". " . $more;

                } elseif ($type == "shares4sale")  {

$news_sub = $share_name . ". Originating Country : " . $country_origin . ".  Quantity On Sale: " . $num_on_sale . ". Price Per Share : " . $new_amt_user_str . ". Yields " . $yield_per_share . " total for every " . $yield_duration . " days";

                } elseif ($type == "fundraiser")  {

                        $news_sub = $fundraiser_name . ". Target Amount : " . $new_amt_user_str . ". Closing Date : " . $fundraiser_start_date;

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
                $news_sku = $f_Ids["sku"][$i];

              if($skip_add != 1){

                //$news = htmlspecialchars($news);
                //$sn_news = htmlspecialchars($sn_news);
                //$full_name = htmlspecialchars($full_name);
                //$thepot_name = htmlspecialchars($thepot_name);
                //$sn_full_name = htmlspecialchars($sn_full_name);
                //$sn_pott_name = htmlspecialchars($sn_pott_name);

                          $next  = array(
                            'sku' => $news_sku,
                            'news_id' => $news_id,
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $thepot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $type,
                            'news_type_title' => $type_title,
                            'news_maker_verified_status' => $this_inputtor_vtag,
                            'news_main' => $news,
                            'news_sub' => $news_sub,
                            'news_image' => $news_image,
                            'news_video' => $news_video,
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => $news_aud,
                            'news_shared_id' => $news_id_ref,
                            'news_shared_maker_pro_pic' => $sn_profile_picture,
                            'news_shared_maker_full_name' => $sn_full_name,
                            'news_shared_maker_verified_status' => $sn_verified_tag,
                            'news_shared_maker_pottname' => $sn_pott_name, 
                            'news_shared_type' => $sn_type,
                            'news_shared_main' => $sn_news,
                            'news_shared_image' => $sn_news_image,
                            'news_shared_video' => $sn_news_video,
                            'news_shared_audio' => $sn_news_aud,
                            'news_set_like_color' => $set_like_btn_color,
                            'news_likes_num' => $p_num_of_likes,
                            'news_set_dislike_color' => $set_dis_like_btn_color,
                            'news_dislikes_num' => $p_num_of_dislikes,                      
                            'shared_news_set_like_color' => $shared_news_set_like_btn_color,
                            'shared_news_likes_num' => $sn_num_of_shares,
                            'shared_news_set_dislike_color' => $shared_news_set_dis_like_btn_color,
                            'shared_news_dislikes_num' => $sn_num_of_buys,                      
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
                          $i_stop = $i_stop + 1;
                          if($i_stop == 20){

                    break;

                  }
                  $url_card_tick = "";
                  $skip_add = 0;
                }
              ///////////////
              }

              

            } // END OF FOR LOOP



            if(isset($_POST["notify_counts"]) && trim($_POST["notify_counts"]) == "1"){

            $gen_nots = 0;
            $my_transfers = 0;

              $query="SELECT * FROM nkae WHERE wo_id = '$myid' AND badger_status = 0 ORDER BY sku DESC";
                  $result = $mysqli->query($query);

                  while($row=$result->fetch_array()) {

                      $sku = $row["sku"];
                    $gen_nots = $gen_nots + 1;
            $query = "UPDATE nkae SET badger_status = 1 WHERE sku = $sku";
            $result2 = $mysqli->query($query);

                  }
                  $next  = array(
                    'notification_num' => $gen_nots
                    );
                  array_push($newsfeedReturn["general_notification"], $next);



              $query="SELECT * FROM y3n_transfers WHERE receiver_id = '$myid' AND nkae_status = 0 ORDER BY sku DESC";
                  $result = $mysqli->query($query);

                  while($row=$result->fetch_array()) {

                      $sku = $row["sku"];
                    $my_transfers = $my_transfers + 1;
            $query = "UPDATE y3n_transfers SET nkae_status = 1 WHERE sku = $sku";
            $result2 = $mysqli->query($query);

                  }
                  $next  = array(
                    'transfer_num' => $my_transfers
                    );
                  array_push($newsfeedReturn["transfers"], $next);

                  $news_count = count($newsfeedReturn["hits"]);

                  $next  = array(
                    'news_num' => $news_count
                    );
                  array_push($newsfeedReturn["new_news_count"], $next);

      $query = "SELECT * FROM akasakasa_details WHERE investor_id = '$myid'";   
            $result = $mysqli->query($query);
                
              while($row=$result->fetch_array()) {
               
                      $chat_table = trim($row["chat_table"]);
                      $receiver_pottname = trim($row["receiver_pottname"]);
                      $msg_datetime = trim($row["msg_datetime"]);
                      $msg = trim($row["msg"]);

      $query = "SELECT first_name, last_name, profile_picture, verified_tag FROM investor WHERE pot_name = '$receiver_pottname'";

                          $result2 = $mysqli->query($query);

                      if (mysqli_num_rows($result2) != "0") {

                          $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                          $first_name = trim($row2["first_name"]);
                          $last_name = trim($row2["last_name"]);
                          $receiver_full_name = $first_name . " " . $last_name;
                          $verified_tag = trim($row2["verified_tag"]);
                          $profile_picture = trim($row2["profile_picture"]);
                          $strStart = $row["msg_datetime"];

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

            }

            echo json_encode($newsfeedReturn);

          }




          }





    }




}
