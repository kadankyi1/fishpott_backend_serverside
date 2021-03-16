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
	isset($_POST['search_txt']) && trim($_POST['search_txt']) != "" && 
	isset($_POST['i_country']) && trim($_POST['i_country']) != ""
) {
require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $search_txt = mysqli_real_escape_string($mysqli, $_POST['search_txt']);
    $i_country = mysqli_real_escape_string($mysqli, $_POST['i_country']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $search_txt = trim($search_txt);
    $i_country = trim($i_country);

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

                                 YARD SALES FETCH START

            ************************************************************************/

                if(isset($yardsale_sku) && $yardsale_sku > 0){

			          $query="SELECT * FROM up4sale WHERE sku < $yardsale_sku AND (UPPER(item_name) LIKE UPPER('%$search_txt%') OR UPPER(item_description) LIKE UPPER('%$search_txt%') OR UPPER(item_location) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
			            $result = $mysqli->query($query);
                      $real_skip = 0;

                } else {


                  $table_name = "up4sale";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
			          $query="SELECT * FROM up4sale WHERE sku < $latest_sku AND (UPPER(item_name) LIKE UPPER('%$search_txt%') OR UPPER(item_description) LIKE UPPER('%$search_txt%') OR UPPER(item_location) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
			            $result = $mysqli->query($query);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

            while($row=$result->fetch_array()) {
                   
                              $sku = $row["sku"];
                              $up4sale_news_id = $row["up4sale_news_id"];
                              $item_name = $row["item_name"];
                              $item_price = $row["item_price"];
                              $currency = $row["currency"];
                              $item_quantity = $row["item_quantity"];
                              $number_sold = $row["number_sold"];
                              $item_description = $row["item_description"];
                              $item_location = $row["item_location"];
                              $sale_status = $row["sale_status"];
                              $seller_id = $row["seller_id"];
                              $flag = $row["flag"];
                              $verified_tag = $row["verified_tag"];
                              $convert_amt = $item_price;

                              $item_quantity = $item_quantity - $number_sold;

                              if($item_quantity > 0 && $flag == 0){
                                $add_this = 1;
                              } else {

                                continue;
                              }
///

                      $query = "SELECT pot_name FROM investor WHERE investor_id = '$seller_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              //'news_maker_pottname' => $maker_pott_name,
                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $maker_pott_name = trim($row2["pot_name"]);

                            } else {
                              continue;
                            }

///
$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$up4sale_news_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0" && $add_this == 1) {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $news_image = $row2["news_image"];
                              $news_video = $row2["news_video"];
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

                                } else if ($this_currency == "GBP"){

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

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "up4sale", 
                                      'up4sale_news_id' => $up4sale_news_id, 
                                      'news_maker_pottname' => $maker_pott_name,
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

                                }



           }


            /************************************************************************

                                 SHARES SALES FETCH START

            ************************************************************************/

                if(isset($sharessale_sku) && $sharessale_sku > 0){

				        $query="SELECT parent_shares_id FROM shares_worso WHERE sku < $sharessale_sku  AND ( UPPER(share_name) LIKE UPPER('%$search_txt%') OR UPPER(parent_company_name) LIKE UPPER('%$search_txt%') OR UPPER(country_origin) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
				            $result = $mysqli->query($query);

                        $real_skip = 0;

                } else {


                  $table_name = "shares4sale";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
                      $query = "SELECT parent_shares_id FROM shares_worso WHERE sku < $latest_sku  AND ( UPPER(share_name) LIKE UPPER('%$search_txt%') OR UPPER(parent_company_name) LIKE UPPER('%$search_txt%') OR UPPER(country_origin) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
                      $result = $mysqli->query($query);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

    
            while($row=$result->fetch_array()) {
                   
                $parent_shares_id = $row["parent_shares_id"];

        $query="SELECT * FROM shares4sale WHERE UPPER(parent_shares_id) LIKE UPPER('%$parent_shares_id%') ORDER BY sku DESC";
                    $result2 = $mysqli->query($query);

                    while($row2=$result2->fetch_array()) {
                           
                        $parent_shares_id = $row2["parent_shares_id"];
                              $sku = $row2["sku"];
                              $shares_news_id = $row2["shares_news_id"];
                              $parent_shares_id = $row2["parent_shares_id"];
                              $sharesOnSale_id = $row2["sharesOnSale_id"];
                              $selling_price = $row2["selling_price"];
                              $currency = $row2["currency"];
                              $num_on_sale = $row2["num_on_sale"];
                              $number_sold = $row2["number_sold"];
                              $shares4sale_owner_id  = trim($row2["shares4sale_owner_id"]);
                              $flag = $row2["flag"];
                              $verified_tag = $row2["verified_tag"];

                      $query = "SELECT pot_name FROM investor WHERE investor_id = '$shares4sale_owner_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              //'news_maker_pottname' => $maker_pott_name,
                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $maker_pott_name = trim($row2["pot_name"]);

                            } else {
                              continue;
                            }

$query = "SELECT num_of_shares FROM shares_owned WHERE owner_id = '$shares4sale_owner_id'";

                          $result3 = $mysqli->query($query);

                          if (mysqli_num_rows($result3) != "0" && $flag == 0) {

                              $row3 = $result3->fetch_array(MYSQLI_ASSOC);
                              $num_of_shares = $row3["num_of_shares"];
                              $add_this = 1;
                            } else {

                                continue;
                            }
$query = "SELECT shares_logo, share_name FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";

                          $result4 = $mysqli->query($query);

                          if (mysqli_num_rows($result4) != "0" && $add_this == 1) {

                              $row4 = $result4->fetch_array(MYSQLI_ASSOC);
                              $shares_logo = $row4["shares_logo"];
                              $share_name = $row4["share_name"];

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

                          $result5 = $mysqli->query($query);

                          if (mysqli_num_rows($result5) != "0" && $add_this == 1) {

                              $row5 = $result5->fetch_array(MYSQLI_ASSOC);
                              $news = $row5["news"];
     
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

                                } else if ($this_currency == "GBP"){

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

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "shares4sale", 
                                      'share_name' => $share_name, 
                                      'shares_news_id' => $shares_news_id, 
                                      'news_maker_pottname' => $maker_pott_name,
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

                                }




                   }

           }

            /************************************************************************

                                 EVENTS FETCH START

            ************************************************************************/


                if(isset($events_sku) && $events_sku > 0){

				        $query="SELECT * FROM event WHERE  sku < $events_sku AND (UPPER(event_name) LIKE UPPER('%$search_txt%') OR UPPER(venue) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
				            $result = $mysqli->query($query);

                      $real_skip = 0;

                } else {


                  $table_name = "event";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                        $latest_sku = $latest_sku + 1;
				        $query="SELECT * FROM event WHERE  sku < $latest_sku AND (UPPER(event_name) LIKE UPPER('%$search_txt%') OR UPPER(venue) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
				            $result = $mysqli->query($query);
                    	$real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

            while($row=$result->fetch_array()) {
                   
                  $sku = $row["sku"];
                  $event_news_id = $row["event_news_id"];
                  $event_id = $row["event_id"];
                  $event_name = $row["event_name"];
                  $creater_id = $row["creater_id"];
                  $image = $row["image"];
                  $venue = $row["venue"];
                  $event_date = $row["event_date"];
                  $event_time = $row["event_time"];
                  $ticket_cost = $row["ticket_cost"];
                  $currency = $row["currency"];
                  $available_tics = $row["available_tics"];
                  $num_of_goers = $row["num_of_goers"];
                  $verified_tag = $row["verified_tag"];
                  $flag = $row["flag"];

          $query = "SELECT pot_name FROM investor WHERE investor_id = '$creater_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              //'news_maker_pottname' => $maker_pott_name,
                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $maker_pott_name = trim($row2["pot_name"]);

                            } else {
                              continue;
                            }


$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$event_news_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0" && $flag == 0) {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $news_image = $row2["news_image"];
                              $news_video = $row2["news_video"];
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

                                } else if ($this_currency == "GBP"){

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

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "event", 
                                      'event_news_id' => $event_news_id, 
                                      'news_maker_pottname' => $maker_pott_name,
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

                                }
   

           }



                $table_name = "event";
                $order_by = "sku";
                include(ROOT_PATH . 'inc/get_latest_sku.php');
                if($skip == 0){

                    for ($latest_sku; $latest_sku > 0; $latest_sku--) { 

                        $query = "SELECT * FROM event WHERE sku = $latest_sku AND creater_id = '$db_investor_id'";

                          $result = $mysqli->query($query);

                          if (mysqli_num_rows($result) != "0") {

   
                          }

                      }

                }

            /************************************************************************

                                 FUNDRAISER FETCH START

            ************************************************************************/

                if(isset($fundraiser_sku) && $fundraiser_sku > 0){

			        	$query="SELECT * FROM fundraiser WHERE sku < $fundraiser_sku AND (UPPER(fundraiser_name) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
			            $result = $mysqli->query($query);

                      $real_skip = 0;

                } else {


                  $table_name = "fundraiser";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $latest_sku = $latest_sku + 1;
        			$query="SELECT * FROM fundraiser WHERE sku < $latest_sku AND UPPER(fundraiser_name) LIKE UPPER('%$search_txt%') ORDER BY sku DESC";
			            $result = $mysqli->query($query);
                    $real_skip = 0;

                  } else {
                    $real_skip = 1;
                  }

                }

            //$row = $result->fetch_row();

            while($row=$result->fetch_array()) {
                   
                $sku = $row["sku"];
                $f_news_id = $row["f_news_id"];
                $fundraiser_id = $row["fundraiser_id"];
                $fundraiser_name = $row["fundraiser_name"];
                $f_starter_id = $row["f_starter_id"];
                $start_date = $row["start_date"];
                $end_date = $row["end_date"];
                $target_amount = $row["target_amount"];
                $currency = $row["currency"];
                $target_amount = $row["target_amount"];
                $available_tics = $row["available_tics"];
                $num_of_contributors = $row["num_of_contributors"];
                $contributed_amount = $row["contributed_amount"];
                $flag = $row["flag"];
                $verified_tag = $row["verified_tag"];

          $query = "SELECT pot_name FROM investor WHERE investor_id = '$f_starter_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0") {

                              //'news_maker_pottname' => $maker_pott_name,
                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $maker_pott_name = trim($row2["pot_name"]);

                            } else {
                              continue;
                            }

$query = "SELECT news_image, news_video, news FROM newsfeed WHERE news_id = '$f_news_id'";

                          $result2 = $mysqli->query($query);

                          if (mysqli_num_rows($result2) != "0" && $flag == 0) {

                              $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                              $news = $row2["news"];
                              $news_image = $row2["news_image"];
                              $news_video = $row2["news_video"];
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

                                } else if ($this_currency == "GBP"){

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

                                       $next  = array(

                                      'sku' => $sku, 
                                      'type' => "fundraiser", 
                                      'event_news_id' => $f_news_id, 
                                      'event_id' => $fundraiser_id, 
                                      'news_maker_pottname' => $maker_pott_name,
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

                                }
           }


            /************************************************************************

                                 NEWS FETCH START

            ************************************************************************/
    
                if(isset($news_sku) && $news_sku > 0){

						$query="SELECT sku, first_name, last_name, pot_name, profile_picture,net_worth ,verified_tag FROM investor WHERE sku < $news_sku AND (UPPER(first_name) LIKE UPPER('%$search_txt%') OR UPPER(last_name) LIKE UPPER('%$search_txt%') OR UPPER(pot_name) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
						$result = $mysqli->query($query);
                        $real_skip = 0;

                } else {


                  $table_name = "newsfeed";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $real_skip = 0;
                      $latest_sku = $latest_sku + 1;
						$query="SELECT sku,first_name, last_name, pot_name, profile_picture,net_worth ,verified_tag FROM investor WHERE sku < $latest_sku AND (UPPER(first_name) LIKE UPPER('%$search_txt%') OR UPPER(last_name) LIKE UPPER('%$search_txt%') OR UPPER(pot_name) LIKE UPPER('%$search_txt%')) ORDER BY sku DESC";
						$result = $mysqli->query($query);

                  } else {
                    $real_skip = 1;
                  }

                }


            //$row = $result->fetch_row();

            while($row=$result->fetch_array()) {
                   
                $sku = $row["sku"];
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $full_name = $first_name . " " . $last_name;
                $pot_name = $row["pot_name"];
                $net_worth = $row["net_worth"];
                $net_worth = $net_worth . " pott pearls";
                $img_src = $row["profile_picture"];
                $verified_tag = $row["verified_tag"];

                      if (!file_exists("../pic_upload/" . $img_src)) {

                          $img_src = "";
                          } else {

                            $img_src = HTTP_HEAD . "://fishpott.com/pic_upload/" . $img_src; 
                          }


                $next  = array(

                'sku' => $sku, 
                'news_id' => $pot_name, 
                'news_maker_pottname' => $pot_name,
                'type' => "pott",
                'news_main' => $full_name,
                'news_image' => $img_src, 
                'verified_tag' => $verified_tag, 
                'info2' => $net_worth, 
                'info3' => ""

                );
                array_push($newsfeedReturn["news"], $next);    

           }



               /************************************************************************

                                 ENDING NEWS FETCH

            ************************************************************************/

            /************************************************************************

                                 NEWS FETCH START

            ************************************************************************/

                if(isset($videos_sku) && $videos_sku > 0){

			        $query="SELECT * FROM newsfeed WHERE sku < $videos_sku AND UPPER(news) LIKE UPPER('%$search_txt%') ORDER BY sku DESC";

			            $result2 = $mysqli->query($query);

                      $real_skip = 0;

                } else {


                  $table_name = "newsfeed";
                  $order_by = "sku";
                  include(ROOT_PATH . 'inc/get_latest_sku.php');
                  if($skip == 0){

                      $real_skip = 0;
                      $latest_sku = $latest_sku + 1;
			        $query="SELECT * FROM newsfeed WHERE sku < $latest_sku AND UPPER(news) LIKE UPPER('%$search_txt%') ORDER BY sku DESC";

			            $result2 = $mysqli->query($query);

                  } else {
                    $real_skip = 1;
                  }

                }

            while($row2=$result2->fetch_array()) {


              if ($real_skip == 0) {
                  $sku_real = $row2["sku"];
                  $type = $row2["type"];
                  $inputtor_type = $row2["inputtor_type"];
                  $inputtor_id = $row2["inputtor_id"];
			      $news_views = intval($row2["news_views"]);
			      $new_news_views = $news_views + 1;

                  $strStart = $row2["date_time"];

                  include(ROOT_PATH . 'inc/time_converter.php');

                  $news = trim($row2["news"]);
                  $sponsored_tag = trim($row2["sponsored_tag"]);
                  $news_id = trim($row2["news_id"]);
                  $news_image = trim($row2["news_image"]);
                  $news_aud = trim($row2["news_aud"]);
                  $news_video = trim($row2["news_video"]);
                  $news_id_ref = trim($row2["news_id_ref"]);
                  $flag = trim($row2["flag"]);
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

              if($skip != 1){
              	
    $query = "UPDATE newsfeed SET news_views = $new_news_views WHERE news_id = '$news_id'";
    $result = $mysqli->query($query);

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

                          $next  = array(
                            'sku' => $sku_real,
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
                          if($videos_stop <= 20 && ($news_video != "" || $sn_news_video != "")){

                              $videos_stop = $videos_stop + 1;
                                  array_push($newsfeedReturn["fundraisers"], $next);
                                  unset($news_video);

                  }
                  $url_card_tick = "";
                  $skip = 0;
                }

            } // END OF FOR LOOP


                }

                    echo json_encode($newsfeedReturn); exit;

                              

          }

        }

    }
