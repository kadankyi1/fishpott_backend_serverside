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
  isset($_POST['generic_id']) && trim($_POST['generic_id']) != "" && 
  isset($_POST['generic_item_type']) && trim($_POST['generic_item_type']) != ""
) {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $generic_id = mysqli_real_escape_string($mysqli, $_POST['generic_id']);
    $generic_item_type = mysqli_real_escape_string($mysqli, $_POST['generic_item_type']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $generic_id = trim($generic_id);
    $generic_item_type = trim($generic_item_type);


    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);
          $newsfeedReturn["hits"] = array();

          if($mypass == $dbpass && $dbflag == 0) {


    if($generic_item_type == "external_url"){
      $generic_item_type = "comment";
    }


    $newsfeedReturn["hits"] = array();
    $investor_id = "t3e";
    mysqli_set_charset($mysqli, 'utf8mb4');


              $query = "SELECT * FROM newsfeed WHERE news_id = '$generic_id' ";

                $result = $mysqli->query($query);

              if (mysqli_num_rows($result) != "0") {

                  $row = $result->fetch_array(MYSQLI_ASSOC);
                  $sku = $row["sku"];
                  $type = $row["type"];
                  $inputtor_type = $row["inputtor_type"];
                  $inputtor_id = $row["inputtor_id"];

                  $strStart = $row["date_time"];

                  include(ROOT_PATH . 'inc/time_converter.php');

                  $news_views = trim($row["news_views"]);
                  $news = trim($row["news"]);
                  $sponsored_tag = trim($row["sponsored_tag"]);
                  $news_id = trim($row["news_id"]);
                  $news_image = trim($row["news_image"]);
                  $news_aud = trim($row["news_aud"]);
                  $news_video = trim($row["news_video"]);
                  $news_id_ref = trim($row["news_id_ref"]);
                  $flag = trim($row["flag"]);
                  $skip = 0;

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

              /* Get the HTML or whatever is linked in $url. */
              $response = curl_exec($handle);

              /* Check for 404 (file not found). */
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
                          $skip = 1;
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
                        $num_on_sale = $row["num_on_sale"];
                        $convert_amt = floatval($selling_price);
                        $query = "SELECT * FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";
                        $result = $mysqli->query($query);
                        if (mysqli_num_rows($result) != "0") {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          $share_name = $row["share_name"];
                          $company_name = $row["parent_company_name"];
                          $value_change_rate = $row["value_change_rate"];
                          $country_origin = $row["country_origin"];
                          $shares_logo = $row["shares_logo"];
                          $news_image = $shares_logo;
                          if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                            $news_image = "";
                            
                            } else {

                              $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                            }


                        } else {

                          $skip = 1;
                        }  

                      } else {

                          $skip = 1;
                        }

                  } elseif ($type == "up4sale") {

                    $type_title = "Up 4 Sale";

                      $query = "SELECT * FROM up4sale WHERE up4sale_news_id = '$news_id' ";
                      $result = $mysqli->query($query);
                      if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $up4sale_item_name = $row["item_name"];
                        $up4sale_item_location = $row["item_location"];
                        $rest = substr($up4sale_item_location, 0,1);
                        $up4sale_item_delivery = $row["item_delivery"];
                        $currency = $row["currency"];
                        $up4sale_item_price = $row["item_price"];
                        $item_quantity = $row["item_quantity"];
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


                      } else {

                          $skip = 1;
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

                          $skip = 1;
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

                      $sn_news_views = $row["news_views"];
                      $sn_news = $row["news"];
                      $sn_news_id = $row["news_id"];
                      $sn_news_image = $row["news_image"];
                      $sn_news_aud = $row["news_aud"];
                      $sn_news_video = $row["news_video"];

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

                              $skip = 1;
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

                          $skip = 1;
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

                          $skip = 1;
                      }
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

                        if($currency == "GHS") {

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

                        $news_sub = $up4sale_item_name . " " . $new_amt_user_str . " " . $up4sale_item_location . " Delivery : " . $up4sale_item_delivery . $more;

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

               // } else {

                //  $skip = "yes";
               // }


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
                  $skip = 1;
                }
              }
                //$news = htmlspecialchars($news);
                //$sn_news = htmlspecialchars($sn_news);
                //$full_name = htmlspecialchars($full_name);
                //$thepot_name = htmlspecialchars($thepot_name);
                //$sn_full_name = htmlspecialchars($sn_full_name);
                //$sn_pott_name = htmlspecialchars($sn_pott_name);

              if($skip != 1){
                $new_news_views = intval($news_views) + 1;
    $query = "UPDATE newsfeed SET news_views = $new_news_views WHERE news_id = '$news_id'";
    $result = $mysqli->query($query);
                $new_sn_news_views = intval($sn_news_views) + 1;
  $query = "UPDATE newsfeed SET news_views = $new_sn_news_views WHERE news_id = '$news_id_ref'";
    $result = $mysqli->query($query);

    if($type == "shared_news"){

        $cover_image = $sn_news_image;

    } else {

      $cover_image = $news_image;

    }
      if(trim($cover_image) == ""){

        $cover_image = "https://fishpott.com/inc/no_image.jpg";

      }
/****** APP CHALLENGE CHANGE START*************/

/*
	if($type != "shared_news" && $type != "news" && $type != "shares4sale"){

		if($news_id != ""){

        	continue;

		}

    }
*/
/****** APP CHALLENGE CHANGE END*************/

                          $next  = array(
                            'status' => "1",
                            'type' => "news",
                            'news_id' => $news_id,
                            'news_views' => $news_views,
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
                            'news_or_shared_cover_video_image' => $cover_image,
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
                      }

              } else {


                  $next  = array(
                    'status' => "0"
                    );
                  array_push($newsfeedReturn["hits"], $next);
                  echo json_encode($newsfeedReturn); exit;

              }

    if($generic_item_type == "like" || $generic_item_type == "dislike"){

        $query="SELECT * FROM likes WHERE likes_news_id = '$generic_id'";
            $result = $mysqli->query($query);

            //$row = $result->fetch_row();
            $hit_count = 0;
            while($row=$result->fetch_array()) {
                   
                        $sku = $row["sku"];
                        $liker_investor_id = $row["liker_investor_id"];
                        $like_type = trim($row["like_type"]);
                        $date_time = trim($row["date_time"]);
                        $strStart = trim($row["date_time"]);
                        include(ROOT_PATH . 'inc/time_converter.php');

                        $query = "SELECT first_name, last_name, pot_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$liker_investor_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $first_name = trim($row2["first_name"]);
                              $last_name = trim($row2["last_name"]);
                              $full_name = $last_name . " " . $first_name;
                              $verified_tag = trim($row2["verified_tag"]);
                              $profile_picture = trim($row2["profile_picture"]);
                              $pot_name = trim($row2["pot_name"]);

                          if (!file_exists("../pic_upload/" . $profile_picture)) {

                              $profile_picture = "";

                              } else {

                                $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                              }
                $full_name = htmlspecialchars($full_name);
                $pot_name = htmlspecialchars($pot_name);

                          $next  = array(
                            'sku' => $sku,
                            'status' => "2",
                            'type' => "like",
                            'news_id' => "",
                            'news_views' => "",
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $pot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $like_type,
                            'news_type_title' => $like_type,
                            'news_maker_verified_status' => $verified_tag,
                            'news_main' => "",
                            'news_sub' => "",
                            'news_image' => "",
                            'news_video' => "",
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => "",
                            'news_shared_id' => "",
                            'news_shared_maker_pro_pic' => "",
                            'news_shared_maker_full_name' => "",
                            'news_shared_maker_verified_status' => "",
                            'news_shared_maker_pottname' => "", 
                            'news_shared_type' => "",
                            'news_shared_main' => "",
                            'news_shared_image' => "",
                            'news_shared_video' => "",
                            'news_shared_audio' => "",
                            'news_set_like_color' => "",
                            'news_likes_num' => "",
                            'news_set_dislike_color' => "",
                            'news_dislikes_num' => "",                      
                            'shared_news_set_like_color' => "",
                            'shared_news_likes_num' => "",
                            'shared_news_set_dislike_color' => "",
                            'shared_news_dislikes_num' => "",                      
                            'news_set_comment_color' => "",
                            'news_comment_num' => "",
                            'news_share_num' => "",
                            'news_set_buy_color' => "",
                            'news_buy_num' => "",
                            'news_url_web_address' => "",
                            'news_url_title' =>  "",
                            'news_url_image' =>  "",
                            'news_set_url_card' => "",
                            'news_sponsored_tag' => ""
                            );
                          array_push($newsfeedReturn["hits"], $next);

                              if($hit_count >= 150){
                                break;
                              }
                          }

                      } // WHILE LOOP END



                          }  else if ($generic_item_type == "comment"){



        //echo "type : " . $generic_item_type;
        $query="SELECT * FROM comments WHERE news_id = '$generic_id'";
            $result = $mysqli->query($query);

            //$row = $result->fetch_row();
            $hit_count = 0;
            while($row=$result->fetch_array()) {
                   
                        $sku = $row["sku"];
                        $commentor_inputtor_id = $row["inputtor_id"];
                        $comment = trim($row["comment"]);
                        $date_time = trim($row["date_time"]);
                        $strStart = trim($row["date_time"]);
                        include(ROOT_PATH . 'inc/time_converter.php');

                        $query = "SELECT first_name, last_name, pot_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$commentor_inputtor_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $first_name = trim($row2["first_name"]);
                              $last_name = trim($row2["last_name"]);
                              $full_name = $last_name . " " . $first_name;
                              $verified_tag = trim($row2["verified_tag"]);
                              $profile_picture = trim($row2["profile_picture"]);
                              $pot_name = trim($row2["pot_name"]);

                          if (!file_exists("../pic_upload/" . $profile_picture)) {

                              $profile_picture = "";

                              } else {

                                $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                              }

                $full_name = htmlspecialchars($full_name);
                $pot_name = htmlspecialchars($pot_name);
                $comment = htmlspecialchars($comment);

                          $next  = array(
                            'sku' => $sku,
                            'status' => "3",
                            'type' => "comment",
                            'news_id' => "",
                            'news_views' => "",
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $pot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $comment,
                            'news_type_title' => $comment,
                            'news_maker_verified_status' => $verified_tag,
                            'news_main' => "",
                            'news_sub' => "",
                            'news_image' => "",
                            'news_video' => "",
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => "",
                            'news_shared_id' => "",
                            'news_shared_maker_pro_pic' => "",
                            'news_shared_maker_full_name' => "",
                            'news_shared_maker_verified_status' => "",
                            'news_shared_maker_pottname' => "", 
                            'news_shared_type' => "",
                            'news_shared_main' => "",
                            'news_shared_image' => "",
                            'news_shared_video' => "",
                            'news_shared_audio' => "",
                            'news_set_like_color' => "",
                            'news_likes_num' => "",
                            'news_set_dislike_color' => "",
                            'news_dislikes_num' => "",                      
                            'shared_news_set_like_color' => "",
                            'shared_news_likes_num' => "",
                            'shared_news_set_dislike_color' => "",
                            'shared_news_dislikes_num' => "",                      
                            'news_set_comment_color' => "",
                            'news_comment_num' => "",
                            'news_share_num' => "",
                            'news_set_buy_color' => "",
                            'news_buy_num' => "",
                            'news_url_web_address' => "",
                            'news_url_title' =>  "",
                            'news_url_image' =>  "",
                            'news_set_url_card' => "",
                            'news_sponsored_tag' => ""
                            );
                          array_push($newsfeedReturn["hits"], $next);

                              if($hit_count >= 150){
                                break;
                              }
                          }

                      } // WHILE LOOP END


                          } else if ($generic_item_type == "share"){



        $query="SELECT * FROM newsfeed WHERE news_id_ref = '$generic_id'";
            $result = $mysqli->query($query);

            //$row = $result->fetch_row();
            $hit_count = 0;
            while($row=$result->fetch_array()) {
                   
                        $sku = $row["sku"];
                        $sharer_inputtor_id = $row["inputtor_id"];
                        $share_added_news = trim($row["news"]);
                        $date_time = trim($row["date_time"]);
                        $strStart = trim($row["date_time"]);
                        include(ROOT_PATH . 'inc/time_converter.php');

                        $query = "SELECT first_name, last_name, pot_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$sharer_inputtor_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $first_name = trim($row2["first_name"]);
                              $last_name = trim($row2["last_name"]);
                              $full_name = $last_name . " " . $first_name;
                              $verified_tag = trim($row2["verified_tag"]);
                              $profile_picture = trim($row2["profile_picture"]);
                              $pot_name = trim($row2["pot_name"]);

                          if (!file_exists("../pic_upload/" . $profile_picture)) {

                              $profile_picture = "";

                              } else {

                                $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                              }


                $full_name = htmlspecialchars($full_name);
                $pot_name = htmlspecialchars($pot_name);
                $share_added_news = htmlspecialchars($share_added_news);


                          $next  = array(
                            'sku' => $sku,
                            'status' => "4",
                            'type' => "share",
                            'news_id' => "",
                            'news_views' => "",
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $pot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $share_added_news,
                            'news_type_title' => $share_added_news,
                            'news_maker_verified_status' => $verified_tag,
                            'news_main' => "",
                            'news_sub' => "",
                            'news_image' => "",
                            'news_video' => "",
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => "",
                            'news_shared_id' => "",
                            'news_shared_maker_pro_pic' => "",
                            'news_shared_maker_full_name' => "",
                            'news_shared_maker_verified_status' => "",
                            'news_shared_maker_pottname' => "", 
                            'news_shared_type' => "",
                            'news_shared_main' => "",
                            'news_shared_image' => "",
                            'news_shared_video' => "",
                            'news_shared_audio' => "",
                            'news_set_like_color' => "",
                            'news_likes_num' => "",
                            'news_set_dislike_color' => "",
                            'news_dislikes_num' => "",                      
                            'shared_news_set_like_color' => "",
                            'shared_news_likes_num' => "",
                            'shared_news_set_dislike_color' => "",
                            'shared_news_dislikes_num' => "",                      
                            'news_set_comment_color' => "",
                            'news_comment_num' => "",
                            'news_share_num' => "",
                            'news_set_buy_color' => "",
                            'news_buy_num' => "",
                            'news_url_web_address' => "",
                            'news_url_title' =>  "",
                            'news_url_image' =>  "",
                            'news_set_url_card' => "",
                            'news_sponsored_tag' => ""
                            );
                          array_push($newsfeedReturn["hits"], $next);

                              if($hit_count >= 150){
                                break;
                              }
                          }

                      } // WHILE LOOP END



                          } else if ($generic_item_type == "purchase"){


        $query="SELECT * FROM adetor WHERE adetor_news_id = '$generic_id'";
            $result = $mysqli->query($query);

            //$row = $result->fetch_row();
            $hit_count = 0;
            while($row=$result->fetch_array()) {
                   
                        $sku = $row["sku"];
                        $buyer_id = trim($row["buyer_id"]);
                        $item_quantity = trim($row["item_quantity"]);
                        $adetor_delivery_charge = trim($row["adetor_delivery_charge"]);
                        $date_time = trim($row["date_time"]);
                        $strStart = trim($row["date_time"]);
                        include(ROOT_PATH . 'inc/time_converter.php');

                        $query = "SELECT first_name, last_name, pot_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$buyer_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $first_name = trim($row2["first_name"]);
                              $last_name = trim($row2["last_name"]);
                              $full_name = $last_name . " " . $first_name;
                              $verified_tag = trim($row2["verified_tag"]);
                              $profile_picture = trim($row2["profile_picture"]);
                              $pot_name = trim($row2["pot_name"]);

                          if (!file_exists("../pic_upload/" . $profile_picture)) {

                              $profile_picture = "";

                              } else {

                                $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                              }

                $full_name = htmlspecialchars($full_name);
                $pot_name = htmlspecialchars($pot_name);


                          $next  = array(
                            'sku' => $sku,
                            'status' => "5",
                            'type' => "purchase",
                            'news_id' => "",
                            'news_views' => "",
                            'news_maker_pro_pic' => $profile_picture,
                            'news_maker_pottname' => $pot_name, 
                            'news_date' => $date_time, 
                            'news_maker_full_name' => $full_name,
                            'news_type' => $item_quantity,
                            'news_type_title' => $item_quantity,
                            'news_maker_verified_status' => $verified_tag,
                            'news_main' => "",
                            'news_sub' => "",
                            'news_image' => "",
                            'news_video' => "",
                            'news_or_shared_cover_video_image' => $profile_picture,
                            'news_audio' => "",
                            'news_shared_id' => "",
                            'news_shared_maker_pro_pic' => "",
                            'news_shared_maker_full_name' => "",
                            'news_shared_maker_verified_status' => "",
                            'news_shared_maker_pottname' => "", 
                            'news_shared_type' => "",
                            'news_shared_main' => "",
                            'news_shared_image' => "",
                            'news_shared_video' => "",
                            'news_shared_audio' => "",
                            'news_set_like_color' => "",
                            'news_likes_num' => "",
                            'news_set_dislike_color' => "",
                            'news_dislikes_num' => "",                      
                            'shared_news_set_like_color' => "",
                            'shared_news_likes_num' => "",
                            'shared_news_set_dislike_color' => "",
                            'shared_news_dislikes_num' => "",                      
                            'news_set_comment_color' => "",
                            'news_comment_num' => "",
                            'news_share_num' => "",
                            'news_set_buy_color' => "",
                            'news_buy_num' => "",
                            'news_url_web_address' => "",
                            'news_url_title' =>  "",
                            'news_url_image' =>  "",
                            'news_set_url_card' => "",
                            'news_sponsored_tag' => ""
                            );
                          array_push($newsfeedReturn["hits"], $next);

                              if($hit_count >= 150){
                                break;
                              }
                          }

                      } // WHILE LOOP END

                    }
                  
                  echo json_encode($newsfeedReturn); exit;

    }
  }
}
