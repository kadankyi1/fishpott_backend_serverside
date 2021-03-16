<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['this_currency']) && trim($_POST['this_currency']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $this_currency = mysqli_real_escape_string($mysqli, $_POST['this_currency']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $this_currency = trim($this_currency);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0) {

                $table_name = "shares_owned";
                $order_by = "sku";

              include(ROOT_PATH . 'inc/get_latest_sku.php');
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
                      $skip = 1;
                    }

              $query = "SELECT country FROM investor WHERE investor_id = '$myid'";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $i_country = $row["country"];

                    } else {
                      $skip = 1;
                    }

              if($skip == 0){


                for ($i=$latest_sku; $i > 0; $i--) { 

                          $query = "SELECT share_id, parent_shares_id, share_name, owner_id, cost_price_per_share , num_of_shares, start_date, yield_date FROM shares_owned WHERE sku = $i AND owner_id = '$investor_id'";

                              //$numrows = mysql_num_rows($query);
                              $result = $mysqli->query($query);

                              if (mysqli_num_rows($result) != "0") {

                                  $row = $result->fetch_array(MYSQLI_ASSOC);

                                    $share_id = $row["share_id"];
                                    $parent_shares_id = $row["parent_shares_id"];
                                    $share_name = $row["share_name"];
                                    $owner_id = $row["owner_id"];
                                    $cost_price_per_share = $row["cost_price_per_share"];
                                    $num_of_shares = $row["num_of_shares"];
                                    $start_date = $row["start_date"];
                                    $yield_date = trim($row["yield_date"]);

                                    $now = time(); // or your date as well
                                    $your_date = strtotime($yield_date);
                                    $datediff = $your_date - $now;

                                    $diff = (floor($datediff / (60 * 60 * 24)) + 1);

                                    if($diff <= 1){
                                      $diff = 1;
                            $subject = "SHARES PAYMENT IS DUE ( Purchase " . $share_id . ")";
                                            $message = "THESE SHARES HAVE NOT BEEN PAID. " . 
                                            "\n Buyer ID : " . $myid . 
                                            "\n SHARE ID : " . $share_id . 
                                            "\n START DATE : " . $start_date . 
                                            "\n YIELD DATE : " . $yield_date . 
                                            "\n NUMBER OF SHARES : " . $num_of_shares .
                                            "\n PARENT SHARES ID : " . $parent_shares_id;

                                    $headers = "From: <info@fishpott.com>FishPott App";
                                    mail("info@fishpott.com",$subject,$message
                                      ,  $headers);

                                    }


                                    if($num_of_shares > 0){

                                       $query = "SELECT curr_max_price, yield_per_share, yield_duration FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";

                                            //$numrows = mysql_num_rows($query);
                                            $result = $mysqli->query($query);

                                            if (mysqli_num_rows($result) != "0") {

                                                $row = $result->fetch_array(MYSQLI_ASSOC);

                                                $curr_max_price = $row["curr_max_price"];
                              $yield_duration = trim($row["yield_duration"]);
                              $yield_per_share = trim($row["yield_per_share"]);
                              $yield_per_share_real = trim($row["yield_per_share"]);



                        $seller_country = "Ghana";
                        $convert_amt = $yield_per_share_real;
                        $shares_conversion = 1;

                        include(ROOT_PATH . 'inc/android_currency_converter.php');
                        unset($shares_conversion);
                        $yield_per_share_real = $new_amt_user;


                        $seller_country = "Ghana";
                        $convert_amt = $yield_per_share * $num_of_shares;
                        $shares_conversion = 1;

                        include(ROOT_PATH . 'inc/android_currency_converter.php');
                        unset($shares_conversion);
                        $yield_per_share = $new_amt_user_str;

                                                if($this_currency == "Ghc"){

                                                    $curr_max_price = $curr_max_price;

                                                } else if ($this_currency == "GBP"){

                                                  $curr_max_price = $curr_max_price * $GHS_GBP;

                                                } else {

                                                  $curr_max_price = $curr_max_price * $GHS_USD;

                                                }

					$share_name = $share_name . " ( Yields " . $yield_per_share . "  in " . $diff . " days )";

                                               $next  = array(

                                              'share_id' => $share_id, 
                                              'parent_shares_id' => $share_id,
                                              'yield_per_share' => $yield_per_share_real,
                                              'yield_duration' => $yield_duration,
                                              'curr_max_price' => $curr_max_price,
                                              'share_name' => $share_name, 
                                              'cost_price_per_share' => $cost_price_per_share, 
                                              'num_of_shares' => $num_of_shares, 
                                              'owner_id' => $owner_id

                                              );
                                              array_push($linkUpsReturn["hits"], $next);    
                                            }
                                    }

                                   
                              }


                }

        echo json_encode($linkUpsReturn); exit;

              } else {

                  echo json_encode($linkUpsReturn); exit;

                }


          }

        }

    }
