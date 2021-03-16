<?php

    $query = "SELECT * FROM newsfeed WHERE sku = $i AND inputtor_id = '$p_investor_id'";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $sku = $row["sku"];
      $type = $row["type"];
      $inputtor_type = $row["inputtor_type"];
      $inputtor_id = $row["inputtor_id"];

      $strStart = $row["date_time"];

      include(ROOT_PATH . 'inc/time_converter.php');

/*      
      echo "Year : " . $yr_diff . "<br>";
      echo "Month : " . $mth_diff. "<br>";
      echo "Day : " . $day_diff. "<br>";
      echo "Hour : " . $hr_diff. "<br>";
      echo "Minute : " . $min_diff. "<br>";
      echo "Second : " . $sec_diff; exit;
*/  
      //$dteDiff  = $dteStart->diff($dteEnd);
      //echo $dteDiff->format("%Y:%M:%D %H:%I:%S"); exit;

      $news = $row["news"];
      $news_id = $row["news_id"];
      $news_image = $row["news_image"];
      $news_aud = $row["news_aud"];
      $news_video = $row["news_video"];
      $news_id_ref = $row["news_id_ref"];
      $skip = "no";

      $query = "SELECT * FROM investor WHERE investor_id = '$inputtor_id' ";
      $result = $mysqli->query($query);
      if (mysqli_num_rows($result) != "0") {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $first_name = $row["first_name"];
          $last_name = $row["last_name"];
          $full_name = $first_name . " " . $last_name;
          $profile_picture = $row["profile_picture"];
          $this_inputtor_vtag = $row["verified_tag"];
        } else {

              $seller_country = "na";
              $skip = "yes";
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

                } else {

                    $set_dis_like_btn_color =  1;

                }

            }

      if($type == "shares4sale") {

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

            } else {

              $skip = "yes";
            }  

          } else {

              $skip = "yes";
            }

      } elseif ($type == "up4sale") {

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
          } else {

              $skip = "yes";
              $convert_amt = "na";
          }

        
      } elseif ($type == "fundraiser") {

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

              $skip = "yes";
          }

        
      } elseif ($type == "event") {

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

              $skip = "yes";
          }
      }

    if(isset($convert_amt) && $convert_amt != ""){

            if($currency == "GHS") {

              $seller_country = "Ghana";

            } elseif($currency == "GBP") {

              $seller_country = "United Kingdom";

            } else {

              $seller_country = "USA";
            }
            
            include(ROOT_PATH . 'inc/currency_converter.php');
      } else {

              $new_amt_user_str = "FREE";
              $new_amt_pg = 0;
      }

    } else {

      $skip = "yes";
    }