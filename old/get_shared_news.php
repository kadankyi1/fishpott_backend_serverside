<?php
  //include(ROOT_PATH . 'inc/db_connect.php');

  $query = "SELECT * FROM newsfeed WHERE news_id = '$new_news_id' ";


  //$numrows = mysql_num_rows($query);
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
      $sn_skip = "no";

      $query = "SELECT * FROM investor WHERE investor_id = '$sn_inputtor_id' ";
      $result = $mysqli->query($query);
      if (mysqli_num_rows($result) != "0") {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $sn_first_name = $row["first_name"];
          $sn_last_name = $row["last_name"];
          $sn_full_name = $sn_first_name . " " . $sn_last_name;
          $sn_profile_picture = $row["profile_picture"];
        } else {

              $sn_skip = "yes";
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

      if($sn_type == "shares4sale") {

          $query = "SELECT * FROM shares4sale WHERE shares_news_id = '$sn_news_id' ";
          $result = $mysqli->query($query);
          
          if (mysqli_num_rows($result) != "0") {

            $row = $result->fetch_array(MYSQLI_ASSOC);
            $sn_shares_news_id = $row["shares_news_id"];
            $sn_sharesOnSale_id = $row["sharesOnSale_id"];
            $parent_shares_id = $row["parent_shares_id"];
            $sn_shares4sale_owner_id = $row["shares4sale_owner_id"];
            $sn_selling_price = $row["selling_price"];
            $sn_currency = $row["currency"];
            $num_on_sale = $row["num_on_sale"];
            $sale_status = $row["sale_status"];
            if($num_on_sale == 0 || $sale_status == 1){

              $skip = "yes";

            }
            $sn_convert_amt = floatval($sn_selling_price);
            $query = "SELECT * FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";
            $result = $mysqli->query($query);
            if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $sn_share_name = $row["share_name"];
              $sn_company_name = $row["parent_company_name"];
              $sn_value_change_rate = $row["value_change_rate"];
              $sn_country_origin = $row["country_origin"];
              $sn_shares_logo = $row["shares_logo"];
              $sn_news_image = $sn_shares_logo;
              $sn_converter_sh_toggl = 1;
            } else {

              $skip = "yes";
            }  



          } else {

              $sn_skip = "yes";
            }

      } elseif ($sn_type == "up4sale") {

          $query = "SELECT * FROM up4sale WHERE up4sale_news_id = '$sn_news_id' ";
          $result = $mysqli->query($query);
          if (mysqli_num_rows($result) != "0") {

            $row = $result->fetch_array(MYSQLI_ASSOC);
            $sn_up4sale_item_name = $row["item_name"];
            $sn_up4sale_item_location = $row["item_location"];
            $sn_up4sale_item_delivery = $row["item_delivery"];
            $sn_up4sale_item_price = $row["item_price"];
            $sn_currency = $row["currency"];
            $sn_item_quantity = $row["item_quantity"];
            $sn_convert_amt = floatval($sn_up4sale_item_price);
            $sn_converter_sh_toggl = 1;
            if(substr($sn_up4sale_item_location, 1,1) == "."){

                $sn_coor = 1;

            } else if(substr($sn_up4sale_item_location, 0,5) == "ferry"){

                $query = "SELECT add_long, add_lat FROM addressofmine WHERE add_id = '$sn_up4sale_item_location' ";
                $result = $mysqli->query($query);
                if (mysqli_num_rows($result) != "0") {

                    $row = $result->fetch_array(MYSQLI_ASSOC);
                    $sn_add_long = $row["add_long"];
                    $sn_add_lat = $row["add_lat"];
                    $sn_up4sale_item_location = $sn_add_lat . "," . $sn_add_long;
                    $sn_coor = 1;
                } else {

                    $up4sale_item_location = "";

                }

            }
          } else {

              $skip = "yes";
          }

        
      } elseif ($sn_type == "fundraiser") {

          $query = "SELECT * FROM fundraiser WHERE f_news_id = '$sn_news_id' ";
          $result = $mysqli->query($query);
          if (mysqli_num_rows($result) != "0") {

            $row = $result->fetch_array(MYSQLI_ASSOC);
            $sn_fundraiser_name = $row["fundraiser_name"];
            $sn_fundraiser_start_date = $row["start_date"];
            $sn_fundraiser_end_date = $row["end_date"];
            $sn_fundraiser_target_amount = $row["target_amount"];
            $sn_currency = $row["currency"];
            $sn_fundraiser_num_of_contributors = $row["num_of_contributors"];
            $sn_convert_amt = floatval($sn_fundraiser_target_amount);
            $sn_converter_sh_toggl = 1;

          } else {

              $sn_skip = "yes";
          }

        
      } elseif ($sn_type == "event") {

          $query = "SELECT * FROM event WHERE event_news_id = '$sn_news_id' ";
          $result = $mysqli->query($query);
          if (mysqli_num_rows($result) != "0") {

            $row = $result->fetch_array(MYSQLI_ASSOC);
            $sn_event_name = $row["event_name"];
            $sn_event_venue = $row["venue"];
            $sn_event_date = $row["event_date"];
            $sn_event_time = $row["event_time"];
            $sn_event_ticket_cost = $row["ticket_cost"];
            $sn_currency = $row["currency"];
            $sn_num_of_goers = $row["num_of_goers"];
            $sn_event_image = $row["image"];
            $sn_event_verified_tag = $row["verified_tag"];
            $sn_convert_amt = floatval($sn_event_ticket_cost);
            $sn_converter_sh_toggl = 1;
            if($sn_event_ticket_cost == "" ||  $sn_event_ticket_cost == 0){

              $sn_convert_amt = 0;

            } else {

              $sn_convert_amt = floatval($sn_event_ticket_cost);

            }


          } else {

              $sn_skip = "yes";
          }
      }


if(isset($sn_convert_amt) && $sn_convert_amt != ""){

            if($sn_currency == "GHS") {

              $sn_seller_country = "Ghana";

            } elseif($sn_currency == "GBP") {

              $sn_seller_country = "United Kingdom";

            } else {

              $sn_seller_country = "USA";
            }
            /// STARTING

            
            include(ROOT_PATH . 'inc/currency_converter.php');

            ///ENDING
      }

    } else {

      $sn_skip = "yes";
    }
