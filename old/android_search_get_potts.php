<?php

if(
  isset($_POST['myid']) && trim($_POST['myid']) != "" && 
  isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
  isset($_POST['news_sku']) && trim($_POST['news_sku']) != "" && 
  isset($_POST['gettype']) && trim($_POST['gettype']) != "" && 
  isset($_POST['search_txt']) && trim($_POST['search_txt']) != "" && 
  isset($_POST['i_country']) && trim($_POST['i_country']) != "") {

    require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8mb4');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $news_sku = mysqli_real_escape_string($mysqli, $_POST['news_sku']);
    $gettype = mysqli_real_escape_string($mysqli, $_POST['gettype']);
    $search_txt = mysqli_real_escape_string($mysqli, $_POST['search_txt']);
    $i_country = mysqli_real_escape_string($mysqli, $_POST['i_country']);

    $myid = trim($myid);
    $investor_id = $myid;
    $mypass = trim($mypass);
    $search_txt = trim($search_txt);
    $news_sku = trim($news_sku);
    $news_sku = intval($news_sku);
    $news_sku_real = $news_sku;
    $gettype = trim($gettype);
    $i_country = trim($i_country);

    if($i_country != "Ghana" && $i_country != "United Kingdom" && $i_country != "USA"){

      $i_country = "USA";
      
    }

    if($news_sku == 0 || $news_sku == ""){

        $table_name = "investor";
        $order_by = "sku";
        include(ROOT_PATH . 'inc/get_latest_sku.php');

        if($skip == 1){

          echo json_encode($newsfeedReturn); exit;

        } else {

          $news_sku = $latest_sku + 1;

        }

    }

    $newsfeedReturn["hits"] = array();

    $fetch_cnt = 0;

    $query = "SELECT * FROM investor WHERE (UPPER(first_name) LIKE UPPER('%$search_txt%') OR UPPER(last_name) LIKE UPPER('%$search_txt%') OR UPPER(pot_name) LIKE UPPER('%$search_txt%') OR UPPER(country) LIKE UPPER('%$search_txt%') OR UPPER(phone) LIKE UPPER('%$search_txt%') OR UPPER(email) LIKE UPPER('%$search_txt%')) AND sku < $news_sku ORDER BY sku DESC";   
    $result = $mysqli->query($query);
        
      while($row=$result->fetch_array()) {
       
              $first_name = trim($row["first_name"]);
              $last_name = trim($row["last_name"]);
              $pot_name = trim($row["pot_name"]);
              $verified_tag = trim($row["verified_tag"]);
              $country = trim($row["country"]);
              $net_worth = trim($row["net_worth"]);
              $profile_picture = trim($row["profile_picture"]);
              $sku = trim($row["sku"]);
              $full_name = $first_name . " " . $last_name;

              if (!file_exists("../pic_upload/" . $profile_picture)) {

                  $profile_picture = "";

                } else {

                  $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 

                }



              if($first_name != "" && $pot_name != ""){

                $fetch_cnt = $fetch_cnt + 1;

    $pot_name = htmlspecialchars($pot_name);
    $full_name = htmlspecialchars($full_name);
    $profile_picture = htmlspecialchars($profile_picture);

                    $next  = array(
                      'sku' => $sku,
                      'news_id' => $pot_name,
                      'news_maker_pro_pic' => $profile_picture,
                      'news_maker_pottname' => $pot_name, 
                      'news_date' => $country, 
                      'news_maker_full_name' => $full_name,
                      'news_type' => "pott",
                      'news_type_title' => $net_worth,
                      'news_maker_verified_status' => $verified_tag,
                      'news_main' => "",
                      'news_sub' => $net_worth,
                      'news_image' => "",
                      'news_video' => "",
                      'news_or_shared_cover_video_image' => $profile_picture,
                      'news_audio' => "",
                      'news_shared_id' => "",
                      'news_shared_maker_pro_pic' => $profile_picture,
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
                      'news_set_buy_color' => "1",
                      'news_buy_num' => "",
                      'news_url_web_address' => "",
                      'news_url_title' =>  "",
                      'news_url_image' =>  "",
                      'news_set_url_card' => "",
                      'news_sponsored_tag' => ""
                      );
                    array_push($newsfeedReturn["hits"], $next);                    
                    $fetch_cnt = $fetch_cnt + 1;

                    if($fetch_cnt == 50){

                      break;

                    }

              }



        } // WHILE LOOP END


        echo json_encode($newsfeedReturn);




}
