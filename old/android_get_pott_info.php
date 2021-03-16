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
  isset($_POST['pott_name']) && trim($_POST['pott_name']) != "" && 
  isset($_POST['my_country']) && trim($_POST['my_country']) != ""
) {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');
    $share_news_id = trim($_POST['news_id']);
    $addNewsText = trim($_POST['myshare_addition']);

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $pott_name = mysqli_real_escape_string($mysqli, $_POST['pott_name']);
    $i_country = mysqli_real_escape_string($mysqli, $_POST['my_country']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $pott_name = trim($pott_name);
    $i_country = trim($i_country);


    if($i_country != "Ghana" && $i_country != "United Kingdom" && $i_country != "USA"){

      $i_country = "USA";
      
    }

    if(
      isset($_POST['yardsale_sku']) && 
      trim($_POST['yardsale_sku']) != "" && 
      intval($_POST['yardsale_sku']) > 0
    ){

        $yardsale_sku = mysqli_real_escape_string($mysqli, $_POST['yardsale_sku']);
        $yardsale_sku = intval($yardsale_sku);

    }

    if(
      isset($_POST['sharessale_sku']) && 
      trim($_POST['sharessale_sku']) != "" && 
      intval($_POST['sharessale_sku']) > 0
    ){

        $sharessale_sku = mysqli_real_escape_string($mysqli, $_POST['sharessale_sku']);
        $sharessale_sku = intval($sharessale_sku);

    }

    if(
      isset($_POST['events_sku']) && 
      trim($_POST['events_sku']) != "" && 
      intval($_POST['events_sku']) > 0
    ){

        $events_sku = mysqli_real_escape_string($mysqli, $_POST['events_sku']);
        $events_sku = intval($events_sku);

    }

    if(
      isset($_POST['fundraiser_sku']) && 
      trim($_POST['fundraiser_sku']) != "" && 
      intval($_POST['fundraiser_sku']) > 0
    ){

        $fundraiser_sku = mysqli_real_escape_string($mysqli, $_POST['fundraiser_sku']);
        $fundraiser_sku = intval($fundraiser_sku);

    }

    if(
      isset($_POST['videos_sku']) && 
      trim($_POST['videos_sku']) != "" && 
      intval($_POST['videos_sku']) > 0
    ){

        $videos_sku = mysqli_real_escape_string($mysqli, $_POST['videos_sku']);
        $videos_sku = intval($videos_sku);

    }

    if(
      isset($_POST['news_sku']) && 
      trim($_POST['news_sku']) != "" && 
      intval($_POST['news_sku']) > 0
    ){

        $news_sku = mysqli_real_escape_string($mysqli, $_POST['news_sku']);
        $news_sku = intval($news_sku);

    }


    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);

          if($mypass == $dbpass && $dbflag == 0) {

            $newsfeedReturn["pott_info"] = array();
            $newsfeedReturn["yardsale"] = array();
            $newsfeedReturn["shares4sale"] = array();
            $newsfeedReturn["events"] = array();
            $newsfeedReturn["fundraisers"] = array();
            $newsfeedReturn["news"] = array();

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

                                   $next  = array(

                                  'full_name' => $db_full_name, 
                                  'dob' => $db_dob,
                                  'country' => $db_country,
                                  'net_worth' => $db_net_worth,
                                  'investor_level' => $investor_level,
                                  'profile_picture' => $db_profile_picture, 
                                  'status' => $db_status, 
                                  'our_link' => $our_link, 
                                  'verified_tag' => $db_verified_tag,
                                  'mylinkups' => $linkups,
                                  'linkstome' => $links, 
                                  'investor_id' => $db_investor_id

                                  );
                                  array_push($newsfeedReturn["pott_info"], $next);    
                                } else {

                                    echo json_encode($newsfeedReturn); exit;
                                }


            /************************************************************************

                                 YARD SALES FETCH START

            ************************************************************************/
                $fetch_counts = 0;
              
                if(isset($yardsale_sku) && $yardsale_sku > 0){

                      $query9 = "SELECT * FROM up4sale WHERE sku < $yardsale_sku AND seller_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                      $real_skip = 0;

                } else {


                  $table_name = "up4sale";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
                      $query9 = "SELECT * FROM up4sale WHERE sku < $latest_sku AND  seller_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

                if($real_skip == 0){

                    while($row9=$result9->fetch_array()) {

                          if ($real_skip == 0) {

                              $sku = $row9["sku"];
                              $up4sale_news_id = $row9["up4sale_news_id"];
                              $item_name = $row9["item_name"];
                              $item_price = $row9["item_price"];
                              $currency = $row9["currency"];
                              $item_quantity = $row9["item_quantity"];
                              $number_sold = $row9["number_sold"];
                              $item_description = $row9["item_description"];
                              $item_location = $row9["item_location"];
                              $sale_status = $row9["sale_status"];
                              $flag = $row9["flag"];
                              $verified_tag = $row9["verified_tag"];
                              $convert_amt = $item_price;

                              $item_quantity = $item_quantity - $number_sold;

                              if($item_quantity > 0 && $flag == 0){
                                $add_this = 1;
                              } else {

                                continue;
                              }

$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$up4sale_news_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $add_this == 1) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $news = $row["news"];
                              $news_image = $row["news_image"];
                              $news_video = $row["news_video"];
                              if (trim($news_video) == "" || !file_exists("../user/" . $news_video)) {

                                    $news_video = "";

                                } else {

                                    $news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video; 
                                }

                                if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                                      $news_image = "";
                                      $add_this = 0;
                                  } else {

                                      $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                                  }

                            } else {
                                $add_this = 0;
                            }


                                if($currency == "Ghc"){

                                    $seller_country = "Ghana";

                                } else if ($currency == "GBP"){

                                    $seller_country = "United Kingdom";

                                } else {

                                    $seller_country = "USA";

                                }

                                include(ROOT_PATH . 'inc/android_currency_converter.php');
                    if($add_this == 0 || !isset($new_amt_user) ||  !isset($new_amt_user_str)){
                                $add_this = 1;
                    }

                    if(trim($news_image) == ""){
                        $add_this = 0;
                    }

                    $info2 = "Avail: " . $item_quantity;
                    $info3 = "Sold: " . $number_sold;
                                if($add_this == 1){
                                	$fetch_counts = $fetch_counts + 1;
                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "up4sale", 
                                      'up4sale_news_id' => $up4sale_news_id, 
                                      'news' => $news, 
                                      'item_name' => $item_name, 
                                      'item_price' => $new_amt_user,
                                      'item_price_string' => $new_amt_user_str,
                                      'news_image' => $news_image,
                                      'news_video' => $news_video,
                                      'item_quantity' => $item_quantity,
                                      'info2' => $info2,
                                      'info3' => $info3,
                                      'number_sold' => $number_sold, 
                                      'item_description' => $item_description, 
                                      'verified_tag' => $verified_tag, 
                                      'item_location' => $item_location

                                      );
                                      array_push($newsfeedReturn["yardsale"], $next);    
                                unset($new_amt_user);
                                unset($new_amt_user_str);
                                unset($add_this);
                                	if($fetch_counts == 10){
                                		break;
                                	}
                                }

                          }

                      }

                }

            /************************************************************************

                                 SHARES SALES FETCH START

            ************************************************************************/

                $fetch_counts = 0;
                if(isset($sharessale_sku) && $sharessale_sku > 0){

                      $query9 = "SELECT * FROM shares4sale WHERE sku < $sharessale_sku  AND shares4sale_owner_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                      $real_skip = 0;

                } else {


                  $table_name = "shares4sale";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
                      $query9 = "SELECT * FROM shares4sale WHERE sku < $latest_sku  AND shares4sale_owner_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

                if($real_skip == 0){

                    while($row9=$result9->fetch_array()) {

                          if ($real_skip == 0) {

                              $sku = $row9["sku"];
                              $shares_news_id = $row9["shares_news_id"];
                              $parent_shares_id = $row9["parent_shares_id"];
                              $sharesOnSale_id = $row9["sharesOnSale_id"];
                              $selling_price = $row9["selling_price"];
                              $currency = $row9["currency"];
                              $num_on_sale = $row9["num_on_sale"];
                              $number_sold = $row9["number_sold"];
                              $flag = $row9["flag"];
                              $verified_tag = $row9["verified_tag"];


$query = "SELECT num_of_shares FROM shares_owned WHERE owner_id = '$db_investor_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $flag == 0) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $num_of_shares = $row["num_of_shares"];
                                $add_this = 1;
                            } else {

                                continue;
                            }

$query = "SELECT shares_logo, share_name FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $add_this == 1) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $shares_logo = $row["shares_logo"];
                              $share_name = $row["share_name"];

                              if (trim($shares_logo) == "" || !file_exists("../user/" . $shares_logo)) {

                                    $shares_logo = "";
                                    $add_this = 0;
                                } else {

                                    $shares_logo = HTTP_HEAD . "://fishpott.com/user/" . $shares_logo; 
                                }


                            } else {
                                continue;
                            }


$query = "SELECT news FROM newsfeed WHERE news_id = '$shares_news_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $add_this == 1) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $news = $row["news"];
     
                            }


                              $convert_amt = $selling_price;

                              $num_on_sale = $num_on_sale - $number_sold;

                  if($num_on_sale > 0 && $flag == 0 && $num_on_sale <= $num_of_shares){
                                $add_this = 1;
                              } else {

                                $add_this = 0;
                              }

                                if($currency == "Ghc"){

                                    $seller_country = "Ghana";

                                } else if ($currency == "GBP"){

                                    $seller_country = "United Kingdom";

                                } else {

                                    $seller_country = "USA";

                                }

                                include(ROOT_PATH . 'inc/android_currency_converter.php');
                    if($add_this == 0 || !isset($new_amt_user) ||  !isset($new_amt_user_str)){
                                $add_this = 0;
                    } else {
                      $new_amt_user_str = $new_amt_user_str . " per share";
                    }

                    if(trim($shares_logo) == ""){
                        $add_this = 0;
                    }

                    $info2 = "Avail: " . $num_on_sale;
                    $info3 = "Sold: " . $number_sold;

                                if($add_this == 1){	
                                	$fetch_counts = $fetch_counts + 1;
                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "shares4sale", 
                                      'share_name' => $share_name, 
                                      'shares_news_id' => $shares_news_id, 
                                      'parent_shares_id' => $parent_shares_id, 
                                      'news' => $news, 
                                      'shares_logo' => $shares_logo,
                                      'sharesOnSale_id' => $sharesOnSale_id,
                                      'selling_price_string' => $new_amt_user_str,
                                      'selling_price_num' => $new_amt_user,
                                      'info2' => $info2,
                                      'info3' => $info3,
                                      'verified_tag' => $verified_tag,
                                      'num_on_sale' => $num_on_sale,
                                      'number_sold' => $number_sold

                                      );
                                      array_push($newsfeedReturn["shares4sale"], $next);    
                                unset($new_amt_user);
                                unset($new_amt_user_str);
                                unset($add_this);
                                	if($fetch_counts == 10){
                                		break;
                                	}

                                }

                          }

                      }

                }


            /************************************************************************

                                 EVENTS FETCH START

            ************************************************************************/

                $fetch_counts = 0;

                if(isset($events_sku) && $events_sku > 0){

                      $query9 = "SELECT * FROM event WHERE sku < $events_sku AND creater_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                      $real_skip = 0;

                } else {


                  $table_name = "event";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
                      $query9 = "SELECT * FROM event WHERE sku < $latest_sku AND creater_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

                if($real_skip == 0){

                    while($row9=$result9->fetch_array()) {

                          if ($real_skip == 0) {
                              $add_this = 1;
                              $sku = $row9["sku"];
                              $event_news_id = trim($row9["event_news_id"]);
                              $event_id = $row9["event_id"];
                              $event_name = $row9["event_name"];
                              $image = $row9["image"];
                              $venue = $row9["venue"];
                              $event_date = $row9["event_date"];
                              $event_time = $row9["event_time"];
                              $ticket_cost = $row9["ticket_cost"];
                              $currency = $row9["currency"];
                              $available_tics = $row9["available_tics"];
                              $num_of_goers = $row9["num_of_goers"];
                              $verified_tag = $row9["verified_tag"];
                              $flag = intval($row9["flag"]);


$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$event_news_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $flag == 0) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $news = $row["news"];
                              $news_image = $row["news_image"];
                              $news_video = $row["news_video"];
                              if (trim($news_video) == "" || !file_exists("../user/" . $news_video)) {

                                    $news_video = "";

                                } else {

                                    $news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video; 
                                }

                              if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                                    $news_image = "";
                                    $add_this = 0;
                                } else {

                                    $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                                }

                            } else {
                                continue;
                            }




                              $convert_amt = $ticket_cost;

                              $available_tics = $available_tics - $num_of_goers;

                  if($available_tics > 0 && $flag == 0){

                                $add_this = 1;

                              } else {

                                $add_this = 0;
                              }

                                $strStart = $event_date;
                                include(ROOT_PATH . 'inc/event_date_passed_chk.php');
                                if($evt_coming != 1) {
                                    $add_this = 0;
                                }

                                if($currency == "Ghc"){

                                    $seller_country = "Ghana";

                                } else if ($currency == "GBP"){

                                    $seller_country = "United Kingdom";

                                } else {

                                    $seller_country = "USA";

                                }

                                include(ROOT_PATH . 'inc/android_currency_converter.php');
                    if($add_this == 0 || !isset($new_amt_user) ||  !isset($new_amt_user_str)){
                                $add_this = 0;
                    }


                    if(trim($news_image) == ""){
                        $add_this = 0;
                    }


                    $info2 = "Tic. Avail: " . $available_tics;
                    $info3 = "Tic. Sold: " . $num_of_goers;


                    if($add_this == 1){
                                	$fetch_counts = $fetch_counts + 1;

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "event", 
                                      'event_news_id' => $event_news_id, 
                                      'event_id' => $event_id, 
                                      'event_name' => $event_name,
                                      'venue' => $venue,
                                      'news' => $news, 
                                      'news_image' => $news_image,
                                      'news_video' => $news_video,
                                      'event_date' => $event_date,
                                      'event_time' => $event_time,
                                      'ticket_cost_string' => $new_amt_user_str,
                                      'ticket_cost_num' => $new_amt_user,
                                      'available_tics' => $available_tics,
                                      'info2' => $info2,
                                      'info3' => $info3,
                                      'verified_tag' => $verified_tag,
                                      'num_of_goers' => $num_of_goers

                                      );
                                      array_push($newsfeedReturn["events"], $next);    
                                unset($new_amt_user);
                                unset($new_amt_user_str);
                                unset($add_this);

                                	if($fetch_counts == 10){
                                		break;
                                	}

                                }

                          }

                      }

                }

            /************************************************************************

                                 FUNDRAISER FETCH START

            ************************************************************************/
                $fetch_counts = 0;

                if(isset($fundraiser_sku) && $fundraiser_sku > 0){

                      $query9 = "SELECT * FROM fundraiser WHERE sku < $fundraiser_sku AND f_starter_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                      $real_skip = 0;

                } else {


                  $table_name = "fundraiser";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
                      $query9 = "SELECT * FROM fundraiser WHERE sku < $latest_sku AND f_starter_id = '$db_investor_id' ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

                if($real_skip == 0){

                    while($row9=$result9->fetch_array()) {

                          if ($real_skip == 0) {

                              $sku = $row9["sku"];
                              $f_news_id = $row9["f_news_id"];
                              $fundraiser_id = $row9["fundraiser_id"];
                              $fundraiser_name = $row9["fundraiser_name"];
                              $start_date = $row9["start_date"];
                              $end_date = $row9["end_date"];
                              $target_amount = $row9["target_amount"];
                              $currency = $row9["currency"];
                              $target_amount = $row9["target_amount"];
                              $available_tics = $row9["available_tics"];
                              $num_of_contributors = $row9["num_of_contributors"];
                              $contributed_amount = $row9["contributed_amount"];
                              $flag = $row9["flag"];
                              $verified_tag = $row9["verified_tag"];



$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$f_news_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0" && $flag == 0) {

                              $row = $result->fetch_array(MYSQLI_ASSOC);
                              $news = $row["news"];
                              $news_image = $row["news_image"];
                              $news_video = $row["news_video"];
                              if (trim($news_video) == "" || !file_exists("../user/" . $news_video)) {

                                    $news_video = "";

                                          if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                                                $news_image = "";
                                                $add_this = 0;
                                            } else {

                                                $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                                            }


                                } else {

                                    $news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video; 
                                }

                            } else {
                                continue;
                            }




                              $convert_amt = $target_amount;

                              if($flag == 0){

                                $add_this = 1;

                              } else {

                                $add_this = 0;
                              }

                                $strStart = $end_date;
                                include(ROOT_PATH . 'inc/event_date_passed_chk.php');
                                if($evt_coming != 1) {
                                    $add_this = 0;
                                }

                                if($currency == "Ghc"){

                                    $seller_country = "Ghana";

                                } else if ($currency == "GBP"){

                                    $seller_country = "United Kingdom";

                                } else {

                                    $seller_country = "USA";

                                }

                                include(ROOT_PATH . 'inc/android_currency_converter.php');
                    if($add_this == 0 || !isset($new_amt_user) ||  !isset($new_amt_user_str)){
                                $add_this = 0;
                    } else {
                        $target_amount_string = $new_amt_user_str;
                        $target_amount_num = $new_amt_user;
                        unset($new_amt_user);
                        unset($new_amt_user_str);
                    }

                    $convert_amt = $contributed_amount;

                    include(ROOT_PATH . 'inc/android_currency_converter.php');
                    if($add_this == 0 || !isset($new_amt_user) ||  !isset($new_amt_user_str)){
                                $new_amt_user = "0";
                                $new_amt_user_str = "none";
                    }

                    if(trim($news_image) == ""){
                        $add_this = 0;
                    }


                    $info2 = "Target: " . $target_amount_string;
                    $info3 = "Contributions: " . $new_amt_user_str;


                                if($add_this == 1){
                                	$fetch_counts = $fetch_counts + 1;

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "fundraiser", 
                                      'event_news_id' => $f_news_id, 
                                      'event_id' => $fundraiser_id, 
                                      'event_name' => $fundraiser_name,
                                      'venue' => $start_date,
                                      'news' => $news, 
                                      'news_image' => $news_image,
                                      'news_video' => $news_video,
                                      'event_date' => $end_date,
                                      'event_time' => $event_time,
                                      'ticket_cost_string' => $target_amount_string,
                                      'ticket_cost_num' => $target_amount_num,
                                      'contributed_amount_string' => $new_amt_user_str,
                                      'available_tics' => $new_amt_user,
                                      'info2' => $info2,
                                      'info3' => $info3,
                                      'verified_tag' => $verified_tag,
                                      'num_of_goers' => $num_of_goers

                                      );
                                      array_push($newsfeedReturn["events"], $next);    
                                unset($new_amt_user);
                                unset($new_amt_user_str);
                                unset($add_this);

                                	if($fetch_counts == 10){
                                		break;
                                	}


                                }

                          }

                      }

                }



            /************************************************************************

                                 NEWS FETCH START

            ************************************************************************/
            	$fetch_counts = 0;

                if(isset($news_sku) && $news_sku > 0){

                      $query9 = "SELECT * FROM newsfeed WHERE sku < $news_sku AND inputtor_id = '$db_investor_id' AND flag = 0 ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);
                      $real_skip = 0;

                } else {


                  $table_name = "newsfeed";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $real_skip = 0;
                      $latest_sku = $latest_sku + 1;
                      $query9 = "SELECT * FROM newsfeed WHERE sku < $latest_sku AND inputtor_id = '$db_investor_id' AND flag = 0 ORDER BY sku DESC";
                      $result9 = $mysqli->query($query9);

                  } else {
                    $real_skip = 1;
                  }

                }

                if($real_skip == 0){

                    while($row9=$result9->fetch_array()) {

                          if ($real_skip == 0) {

						      $sku = $row9["sku"];
						      $type = $row9["type"];
						      $inputtor_type = $row9["inputtor_type"];
						      $inputtor_id = $row9["inputtor_id"];
                  $news_views = intval($row9["news_views"]);
                  $new_news_views = $news_views + 1;

						      $strStart = $row9["date_time"];

						      include(ROOT_PATH . 'inc/time_converter.php');

						      $news = trim($row9["news"]);
						      $sponsored_tag = trim($row9["sponsored_tag"]);
						      $news_id = trim($row9["news_id"]);
						      $news_image = trim($row9["news_image"]);
						      $news_aud = trim($row9["news_aud"]);
						      $news_video = trim($row9["news_video"]);
						      $news_id_ref = trim($row9["news_id_ref"]);
						      $flag = trim($row9["flag"]);
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
								      $shared_news_sku = $row["sku"];
								      $sn_type = $row["type"];
								      $sn_inputtor_type = $row["inputtor_type"];
								      $sn_inputtor_id = $row["inputtor_id"];
                      $sn_news = $row["news"];
                      $sn_news_views = intval($row["news_views"]);
                      $new_sn_news_views = $sn_news_views + 1;

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

							if($skip != 1){

    $query = "UPDATE newsfeed SET news_views = $new_news_views WHERE news_id = '$news_id'";
    $result = $mysqli->query($query);

  $query = "UPDATE newsfeed SET news_views = $new_sn_news_views WHERE news_id = '$news_id_ref'";
    $result = $mysqli->query($query);
    
                //$news = htmlspecialchars($news);
                //$sn_news = htmlspecialchars($sn_news);
                //$full_name = htmlspecialchars($full_name);
                //$thepot_name = htmlspecialchars($thepot_name);
                //$sn_full_name = htmlspecialchars($sn_full_name);
                //$sn_pott_name = htmlspecialchars($sn_pott_name);

    if($type == "shared_news"){

        $cover_image = $sn_news_image;

    } else {

      $type = "news";
      $cover_image = $news_image;

    }


    if(trim($cover_image) == ""){

      $cover_image = "https://fishpott.com/inc/no_image.jpg";

    }

      if(trim($cover_image) == ""){

        $cover_image = "https://fishpott.com/inc/no_image.jpg";

      }


                                	$fetch_counts = $fetch_counts + 1;

			                    $next  = array(
			                      'sku' => $sku,
			                      'news_id' => $news_id,
                            'type' => "news", 
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
                            'news_views' => $news_views,   
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
			                    if($i_stop <= 20){

			                    		$i_stop = $i_stop + 1;
                          				array_push($newsfeedReturn["news"], $next);

								  }
                                	if($fetch_counts == 15){
                                		break;
                                	}

                  if(!isset($videos_sku)){
                      $videos_sku = $sku;
                  }
			                    if($sku <= $videos_sku && $news_video != "" || $sn_news_video != ""){

			                    		$videos_stop = $videos_stop + 1;
                          				array_push($newsfeedReturn["fundraisers"], $next);
                          				unset($news_video);

								  }
								  $url_card_tick = "";
								  $skip = 0;
							  }
						  ///////////////
						  }

            } // END OF FOR LOOP


                }




               /************************************************************************

                                 ENDING NEWS FETCH

            ************************************************************************/
                    echo json_encode($newsfeedReturn); exit;

                              

          }

        }

    }
