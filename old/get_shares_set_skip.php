<?php
  $query = "SELECT * FROM shares_owned WHERE sku = $i AND owner_id = '$investor_id' ";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
          $i_owner_id = $row["owner_id"];
          $i_share_id = $row["share_id"];
          $i_share_name = $row["share_name"];
          $parent_shares_id = $row["parent_shares_id"];
          $cost_price = $row["cost_price_per_share"];
          $i_number_of_shares = $row["num_of_shares"];

          $query = "SELECT * FROM shares_worso WHERE parent_shares_id = '$parent_shares_id' ";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $curr_max_price = $row["curr_max_price"];
              $country_origin = $row["country_origin"];
              $convert_amt = $curr_max_price;
              if($country_origin == "Ghana") {

                  $currency = "GHS";

              } elseif($country_origin == "United Kingdom"){

                  $currency = "GBP";

              } else {

                  $currency = "USD";

              }
              if(isset($convert_amt) && $convert_amt != ""){

                if($currency == "GHS") {

                  $seller_country = "Ghana";

                } elseif($currency == "GBP") {

                  $seller_country = "United Kingdom";

                } else {

                  $seller_country = "USA";
              }
              /// STARTING

            
            include(ROOT_PATH . 'inc/currency_converter.php');
            ///ENDING
      }
        } else {

          $skip = "yes";
        }         

      if($i_owner_id == $investor_id){
      
        $skip = "no";

      } else {
        $skip = "yes";

      }

  } else {

      $skip = "yes";
  }

  //$country_origin = "";
