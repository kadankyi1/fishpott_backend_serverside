<?php
  require_once("config.php");

function getCountryOrRegion($latlng, $array_index_name, $array_index_name2) {
  $return_array = array();
  $this_country = "";
  $this_region = "";
  $url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $latlng . '&sensor=false&key=' . GOOGLE_MAP_KEY;
  $geocode=file_get_contents($url);
  //$output= json_decode($geocode);
  $output= json_decode($geocode, true);

  for($i = 0; $i < count($output["results"][0]["address_components"]); $i++){

    if(isset($output["results"][0]["address_components"][$i]["long_name"]) && $output["results"][0]["address_components"][$i]["long_name"] != ""){

      if($output["results"][0]["address_components"][$i]["types"][0] == $array_index_name){
        $this_country = $output["results"][0]["address_components"][$i]["long_name"];
      }

      if($output["results"][0]["address_components"][$i]["types"][0] == $array_index_name2){
        $this_region = $output["results"][0]["address_components"][$i]["long_name"];
      }


    }        


  }

  $return_array[0] =  $this_country;
  $return_array[1] =  $this_region;

  return $return_array;
}


if(
	isset($_POST['myid']) && trim($_POST['myid']) != "" && 
	isset($_POST['mypass']) && trim($_POST['mypass']) != "" && 
	isset($_POST['generic_item_news_id']) && trim($_POST['generic_item_news_id']) != "" && 
	isset($_POST['item_order_quantity']) && trim($_POST['item_order_quantity']) != "" && 
	isset($_POST['delivery_address']) && trim($_POST['delivery_address']) != "" && 
	isset($_POST['adetor_delivery_type']) && trim($_POST['adetor_delivery_type']) != "" && 
	isset($_POST['mycountry'])) {
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $generic_item_news_id = mysqli_real_escape_string($mysqli, $_POST['generic_item_news_id']);
    $item_order_quantity = mysqli_real_escape_string($mysqli, $_POST['item_order_quantity']);
    $delivery_address = mysqli_real_escape_string($mysqli, $_POST['delivery_address']);
    $delivery_type = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_type']);
    $mycountry = mysqli_real_escape_string($mysqli, $_POST['mycountry']);

    $myid = trim($myid);
    $investor_id = $myid;
    $mypass = trim($mypass);
    $delivery_address = trim($delivery_address);
    $delivery_address = strtolower($delivery_address);
    $generic_item_news_id = trim($generic_item_news_id);
    $item_order_quantity = trim($item_order_quantity);
    $item_order_quantity = intval($item_order_quantity);
    $mycountry = trim($mycountry);
	if($mycountry != "Ghana" && $mycountry != "United Kingdom" && $mycountry != "USA"){

      $mycountry = "USA";
      
    }

    $query = "SELECT password, flag, login_type FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dblogin_type = trim($row["login_type"]);

          if($mypass == $dbpass && $dbflag == 0) {
		  		$newsfeedReturn["hits"] = array();

              $query = "SELECT item_quantity,item_weight_type, sale_status, item_location,item_price, currency FROM up4sale WHERE up4sale_news_id = '$generic_item_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $sale_status = $row["sale_status"];
                $item_location = trim($row["item_location"]);
                $item_location = strtolower($item_location);
                $item_quantity = $row["item_quantity"];
                $item_price = $row["item_price"];
        		$total_convert_amt = floatval($item_price) * $item_order_quantity;
        		$convert_amt = $total_convert_amt;
                $seller_currency = $row["currency"];
                $item_weight_type = trim($row["item_weight_type"]);
                $item_weight_type = floatval($item_weight_type);

                if($sale_status == "1"){

                	$next  = array(
		                      'hit_status' => "2"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

                } else if ($item_order_quantity > intval($item_quantity)){

                	$next  = array(
		                      'hit_status' => "3"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

                }

    include(ROOT_PATH . 'inc/db_connect_ferry.php');
$query = "SELECT add_long, add_lat, add_name FROM addressofmine WHERE UPPER(add_id) = UPPER('$item_location')";

									$result = $mysqli2->query($query);

									if (mysqli_num_rows($result) != "0") {


										$row = $result->fetch_array(MYSQLI_ASSOC);
										$lon1 = trim($row["add_long"]);
										$lat1 = trim($row["add_lat"]);
										$pickup_name = trim($row["add_name"]);
										$pickup_latlng = $lat1 . "," . $lon1;

									} else {

					                	$next  = array(
							                      'hit_status' => "8"
							                      );
								        array_push($newsfeedReturn["hits"], $next);	
					    				echo json_encode($newsfeedReturn); exit;
							
									}



              } else {

              	$next  = array(
		                      'hit_status' => "0"
		                      );
			        array_push($newsfeedReturn["hits"], $next);	
    				echo json_encode($newsfeedReturn); exit;

              }


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
	              		$next  = array(
			                      'hit_status' => "0"
			                      );
				        array_push($newsfeedReturn["hits"], $next);	
	    				echo json_encode($newsfeedReturn); exit;
                    }


              if($seller_currency == "GHS" || $seller_currency == "Ghc") {

                $seller_country = "Ghana";

              } elseif($seller_currency == "GBP") {

                $seller_country = "United Kingdom";

              } else {

                $seller_country = "USA";
              }
              $i_country = "Ghana";
          	  
          	  include(ROOT_PATH . 'inc/android_currency_converter.php');

              $new_amt_cedis = $new_amt_user;
              $new_amt_cedis_str = $new_amt_user_str;
              $new_amt_cedis_str_curr = $new_amt_user_currency;

			unset($new_amt_user);
			unset($new_amt_user_currency);
			unset($new_amt_user_str);


if(substr($delivery_address,0,5) == "ferry"){

    include(ROOT_PATH . 'inc/db_connect_ferry.php');
		$query = "SELECT add_long, add_lat, add_name, verified_status, investor_id FROM addressofmine WHERE UPPER(add_id) = UPPER('$delivery_address')";

									$result = $mysqli2->query($query);

									if (mysqli_num_rows($result) != "0") {

										$row = $result->fetch_array(MYSQLI_ASSOC);
										$lon2 = trim($row["add_long"]);
										$lat2 = trim($row["add_lat"]);
										$destination_latlng = $lat2 . "," . $lon2;
										$destination_name = trim($row["add_name"]);
										$address_investor_id = trim($row["investor_id"]);
										$verified_status = trim($row["verified_status"]);

										if($verified_status != "1"){

$query = "SELECT first_name, last_name FROM investor WHERE investor_id = '$address_investor_id'";

									$result = $mysqli2->query($query);

									if (mysqli_num_rows($result) != "0") {


										$row = $result->fetch_array(MYSQLI_ASSOC);
										$address_last_name = trim($row["last_name"]);
										$address_first_name = trim($row["first_name"]);
$destination_name = $destination_name . " (Ferry Address of " . $address_first_name . " " . $address_last_name . ")";

									} else {


					                	$next  = array(
							                      'hit_status' => "9"
							                      );
								        array_push($newsfeedReturn["hits"], $next);	
					    				echo json_encode($newsfeedReturn); exit;
							

									}


								}

/********************* START CALCULATE DELIVERY CHARGE HERE ******************/	

      $this_return_array = getCountryOrRegion($pickup_latlng, "country", "administrative_area_level_1");

      $pickup_country = $this_return_array[0];
      $pickup_country = str_replace(" ", "", $pickup_country);

      $pickup_region = $this_return_array[1];
      $pickup_region = str_replace(" ", "", $pickup_region);


      $this_return_array = getCountryOrRegion($destination_latlng, "country", "administrative_area_level_1");

      $destination_country = $this_return_array[0];
      $destination_country = str_replace(" ", "", $destination_country);

      $destination_region = $this_return_array[1];
      $destination_region = str_replace(" ", "", $destination_region);

      if($pickup_country == $destination_country){
        
      $delivery_charge_table_id = $pickup_region . "_" . $destination_region;
      $delivery_charge_table_id = strtolower($delivery_charge_table_id);

      } else {

      $delivery_charge_table_id = $pickup_country . "_" . $destination_country;
      $delivery_charge_table_id = strtolower($delivery_charge_table_id);

      }

      //$delivery_charge_table_id = $pickup_country . "_" . $destination_country;
      //$delivery_charge_table_id = strtolower($delivery_charge_table_id);

              /*
              echo "pickup_region : " . $pickup_region . "\n";
              echo "pickup_country : " . $pickup_country . "\n";
              
              echo "destination_region : " . $destination_region . "\n";
              echo "destination_country : " . $destination_country . "\n";

              echo "delivery_charge_table_id : " . $delivery_charge_table_id . "\n";
              */

              // IDS FOR NEWS DELIVERIES TO COUNTRIES CONSIST OF
              // THE GOOGLE RETURNED NAME OF THE PICKUP COUNTRY AND DESTINATION COUNTRY WITH
              // ALL WHITE SPACES REMOVED AND ALL CAPS TO LOWER CASE


  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id'";
                    $result = $mysqli2->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
                  	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                  	  $pickup_country_real = $pickup_country;
                  	  $pickup_region_real = $pickup_region;
                  	  $destination_country_real = $destination_country;
                  	  $destination_region_real = $destination_region;

                    } else {

                      $next  = array(

                              'hit_status' => "5",
                              'pickup_region' => $pickup_region,
                              'pickup_country' => $pickup_country,
                              'destination_country' => $destination_country,
                              'destination_region' => $destination_region,
                              'delivery_charge_table_id' => $delivery_charge_table_id

                              );
                      array_push($newsfeedReturn["hits"], $next); 
                      echo json_encode($newsfeedReturn); exit;

                    }

				$theta = $lon1 - $lon2;
				$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
				$dist = acos($dist);
				$dist = rad2deg($dist);
				$miles = $dist * 60 * 1.1515;
				$unit = strtoupper($unit);
				$distance = $miles * 1.609344;

				$calc_distance = ceil($distance);
				$calc_distance = intval($calc_distance);
				
              //echo "distance : " . $calc_distance . " km \n";

        if($delivery_type == "Express Delivery"){

            //echo "Express Delivery" . "\n";

          if($pickup_country_real == $destination_country_real){

          // SAME COUNTRY (INTRA) DELIVERY HERE
            if($pickup_region_real != $destination_region_real){

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil(1.5 * $charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";


                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = 1.5 * ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = 1.5 * (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {


            //echo "1 INTERNATIONAL Delivery" . "\n";

            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil(1.5 * ($charge_per_km_in_cedis + $total_weight_constant));

          }


        } else if($delivery_type == "Economy Delivery"){

          if($pickup_country_real == $destination_country_real){

          // SAME COUNTRY (INTRA) DELIVERY HERE
            if($pickup_region_real != $destination_region_real){

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil(1.3 * $charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";
                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = 1.3 * ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = 1.3 * (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {


            //echo "1 INTERNATIONAL Delivery" . "\n";

            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil(1.3 * ($charge_per_km_in_cedis + $total_weight_constant));

          }

        }  else if($delivery_type == "Tortoise Delivery"){

            //echo "Tortoise Delivery" . "\n";

          if($pickup_country_real == $destination_country_real){

              // SAME COUNTRY (INTRA) DELIVERY HERE
                if($pickup_region_real != $destination_region_real){

                  //echo "1 CROSS-REGION Delivery" . "\n";

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil($charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";
                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {


            //echo "1 INTERNATIONAL Delivery" . "\n";

            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil(($charge_per_km_in_cedis + $total_weight_constant));

          }


        }  else {

                    $next  = array(
                            'hit_status' => "6"
                            );
                array_push($newsfeedReturn["hits"], $next); 
              echo json_encode($newsfeedReturn); exit;

        } 

$delivery_charge_num_cedis = ceil($delivery_charge_num_cedis);
$convert_amt = $delivery_charge_num_cedis;

$i_country = $mycountry;
include(ROOT_PATH . 'inc/android_currency_converter.php');
$delivery_charge_mycurrency = $new_amt_user;
$delivery_charge_mycurrency_str = $new_amt_user_str;
$delivery_charge_mycurrency_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);


$total_amount_cedis = $new_amt_cedis + $delivery_charge_num_cedis;
$i_country = $mycountry;
$convert_amt = $total_amount_cedis;
include(ROOT_PATH . 'inc/android_currency_converter.php');
$total_charge_mycurrency = $new_amt_user;
$total_charge_mycurrency_str = $new_amt_user_str;
$total_charge_mycurrency_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);

if($total_charge_mycurrency != "" && $delivery_charge_mycurrency != ""){

	$next  = array(
              'hit_status' => "1",
              'item_location_name' => $pickup_name,
              'destination_location_name' => $destination_name,
              'currency' => $total_charge_mycurrency_curr,
              'delivery_charge_num' => $delivery_charge_mycurrency,
              'delivery_charge_str' => $delivery_charge_mycurrency_str,
              'total_charge_str' => $total_charge_mycurrency_str,
              'total_charge_num' => $total_charge_mycurrency,
    		      'total_charge_num_cedis' => $total_amount_cedis
              );
    array_push($newsfeedReturn["hits"], $next);	
	echo json_encode($newsfeedReturn); exit;


} else {

	$next  = array(
              'hit_status' => "7"
              );
    array_push($newsfeedReturn["hits"], $next);	
	echo json_encode($newsfeedReturn); exit;


}



/********************* END CALCULATE DELIVERY CHARGE HERE ******************/		

					} else {

	                	$next  = array(
			                      'hit_status' => "0"
			                      );
				        array_push($newsfeedReturn["hits"], $next);	
	    				echo json_encode($newsfeedReturn); exit;
					}

          	} else {


          		$delivery_address = urlencode($delivery_address);
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . $delivery_address . '&sensor=false&key=' . GOOGLE_MAP_KEY;
    			$resp_json = file_get_contents($url);
    			$resp = json_decode($resp_json, true);
				if($resp['status']=='OK'){

				// get the important data
				$lati = $resp['results'][0]['geometry']['location']['lat'];
				$longi = $resp['results'][0]['geometry']['location']['lng'];
				$formatted_address = $resp['results'][0]['formatted_address'];
				$destination_name = $formatted_address;
						if($lati && $longi && $formatted_address){
				             $lat2 = strval($lati);
				             $lon2 = strval($longi);
				$destination_latlng = $lat2 . "," . $lon2;
                //$item_price
                //$seller_currency
				//$delivery_type
/********************* START CALCULATE DELIVERY CHARGE HERE ******************/	
/********************* CONSIDER ITEM PRICE, CURRENCY AND DELIVERY TYPE ******************/                
				$theta = $lon1 - $lon2;
				$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
				$dist = acos($dist);
				$dist = rad2deg($dist);
				$miles = $dist * 60 * 1.1515;
				$unit = strtoupper($unit);

				$distance = $miles * 1.609344;

				$calc_distance = ceil($distance);
				$calc_distance = intval($calc_distance);


      $this_return_array = getCountryOrRegion($pickup_latlng, "country", "administrative_area_level_1");

      $pickup_country = $this_return_array[0];
      $pickup_country = str_replace(" ", "", $pickup_country);

      $pickup_region = $this_return_array[1];
      $pickup_region = str_replace(" ", "", $pickup_region);


      $this_return_array = getCountryOrRegion($destination_latlng, "country", "administrative_area_level_1");

      $destination_country = $this_return_array[0];
      $destination_country = str_replace(" ", "", $destination_country);

      $destination_region = $this_return_array[1];
      $destination_region = str_replace(" ", "", $destination_region);

      if($pickup_country == $destination_country){
        
      $delivery_charge_table_id = $pickup_region . "_" . $destination_region;
      $delivery_charge_table_id = strtolower($delivery_charge_table_id);

      } else {

      $delivery_charge_table_id = $pickup_country . "_" . $destination_country;
      $delivery_charge_table_id = strtolower($delivery_charge_table_id);

      }

/*
              echo "pickup_region : " . $pickup_region . "\n";
              echo "destination_region : " . $destination_region . "\n";
              echo "pickup_country : " . $pickup_country . "\n";
              echo "destination_country : " . $destination_country . "\n";

              echo "TWO pickup_region : " . $pickup_region2 . "\n";
              echo "TWO destination_region : " . $destination_region2 . "\n";
              echo "TWO pickup_country : " . $pickup_country2 . "\n";
              echo "TWO destination_country : " . $destination_country2 . "\n";

              echo "delivery_charge_table_id : " . $delivery_charge_table_id . "\n";
              echo "TWO delivery_charge_table_id : " . $delivery_charge_table_id2 . "\n";
              echo "THREE delivery_charge_table_id : " . $delivery_charge_table_id3 . "\n";
              echo "FOUR delivery_charge_table_id : " . $delivery_charge_table_id4 . "\n";

*/

  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id'";
                    $result = $mysqli2->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                	  $pickup_country_real = $pickup_country;
                	  $pickup_region_real = $pickup_region;
                	  $destination_country_real = $destination_country;
                	  $destination_region_real = $destination_region;

//echo " 1 \n";
                    } else {

                      /*
                      if($pickup_country == $destination_country && $pickup_region == $destination_region){

                          $charge_per_km_in_cedis = floatval(1);

                          $pickup_country_real = $pickup_country;
                          $pickup_region_real = $pickup_region;
                          $destination_country_real = $destination_country;
                          $destination_region_real = $destination_region;

                      } else {
                        $next  = array(
                                'hit_status' => "5",
                                'pickup_region' => $pickup_region,
                                'pickup_country' => $pickup_country,
                                'destination_region' => $destination_region,
                                'destination_country' => $destination_country,
                                'delivery_charge_table_id' => $delivery_charge_table_id
                                );
                        array_push($newsfeedReturn["hits"], $next); 
                        echo json_encode($newsfeedReturn); exit;
                      }
                      */
                        $next  = array(
                                'hit_status' => "5",
                                'pickup_region' => $pickup_region,
                                'pickup_country' => $pickup_country,
                                'destination_region' => $destination_region,
                                'destination_country' => $destination_country,
                                'delivery_charge_table_id' => $delivery_charge_table_id
                                );
                        array_push($newsfeedReturn["hits"], $next); 
                        echo json_encode($newsfeedReturn); exit;

                    }


/*
              echo "pickup_country_real : " . $pickup_country_real . "\n";
              echo "pickup_region_real : " . $pickup_region_real . "\n";
              echo "destination_country_real : " . $destination_country_real . "\n";
              echo "destination_region_real : " . $destination_region_real . "\n";
*/
              //echo "2 distance : " . $calc_distance . " km \n";

        if($delivery_type == "Express Delivery"){

            //echo "Express Delivery" . "\n";

          if($pickup_country_real == $destination_country_real){

          // SAME COUNTRY (INTRA) DELIVERY HERE
            if($pickup_region_real != $destination_region_real){

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil(1.5 * $charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";


                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = 1.5 * ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = 1.5 * (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {

            //echo "1 INTERNATIONAL Delivery" . "\n";
            
            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil(1.5 * ($charge_per_km_in_cedis + $total_weight_constant));

          }


        } else if($delivery_type == "Economy Delivery"){


          if($pickup_country_real == $destination_country_real){

          // SAME COUNTRY (INTRA) DELIVERY HERE
            if($pickup_region_real != $destination_region_real){

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil(1.3 * $charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";
                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = 1.3 * ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = 1.3 * (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {

            //echo "1 INTERNATIONAL Delivery" . "\n";

            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil(1.3 * ($charge_per_km_in_cedis + $total_weight_constant));

            //$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

          }

        }  else if($delivery_type == "Tortoise Delivery"){

            //echo "Tortoise Delivery" . "\n";

          if($pickup_country_real == $destination_country_real){

              // SAME COUNTRY (INTRA) DELIVERY HERE
                if($pickup_region_real != $destination_region_real){

                  //echo "1 CROSS-REGION Delivery" . "\n";

                if($item_weight_type <= 10){

                  $total_weight_constant = (5 + ($item_weight_type * 1)) * $item_weight_type;

                } else {

                  $total_weight_constant = (5 * $item_weight_type) * 1.2;
                  
                }


                $delivery_charge_num_cedis = ceil($charge_per_km_in_cedis + $total_weight_constant);

            } else {

              //echo "1 INTRA-REGION Delivery" . "\n";
                if($calc_distance <= 16){

                      if($item_weight_type <= 10){

                        $total_weight_constant = 1;

                      } else {

                        $total_weight_constant = 1.3;
                        
                      }

                      $delivery_charge_num_cedis = ( 1.3 * ((($calc_distance * 0.4) + 13) * $total_weight_constant));

                } else {

                    if($item_weight_type <= 10){

                      $total_weight_constant = 1;

                    } else {

                      $total_weight_constant = 1.3;

                    }

                    $delivery_charge_num_cedis = (($calc_distance * 0.6) + 14) * $total_weight_constant;


                }

            }


          } else {

            //echo "1 INTERNATIONAL Delivery" . "\n";

            if($item_weight_type <= 10){

              $total_weight_constant = (27 + ($item_weight_type * 1)) * $item_weight_type;

            } else {

              $total_weight_constant = (26 * $item_weight_type) * 3.2;
              
            }


            $delivery_charge_num_cedis = ceil($charge_per_km_in_cedis + $total_weight_constant);

            //$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

          }


        }  else {

                    $next  = array(
                            'hit_status' => "6"
                            );
                array_push($newsfeedReturn["hits"], $next); 
              echo json_encode($newsfeedReturn); exit;

        } 

$delivery_charge_num_cedis = ceil($delivery_charge_num_cedis);
$convert_amt = $delivery_charge_num_cedis;

$i_country = $mycountry;
include(ROOT_PATH . 'inc/android_currency_converter.php');
$delivery_charge_mycurrency = $new_amt_user;
$delivery_charge_mycurrency_str = $new_amt_user_str;
$delivery_charge_mycurrency_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);


$total_amount_cedis = $new_amt_cedis + $delivery_charge_num_cedis;
$i_country = $mycountry;
$convert_amt = $total_amount_cedis;
include(ROOT_PATH . 'inc/android_currency_converter.php');
$total_charge_mycurrency = $new_amt_user;
$total_charge_mycurrency_str = $new_amt_user_str;
$total_charge_mycurrency_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);

if($total_charge_mycurrency != "" && $delivery_charge_mycurrency != ""){

//
	
	$next  = array(
              'hit_status' => "1",
              'item_location_name' => $pickup_name,
              'destination_location_name' => $destination_name,
              'currency' => $total_charge_mycurrency_curr,
              'delivery_charge_num' => $delivery_charge_mycurrency,
              'delivery_charge_str' => $delivery_charge_mycurrency_str,
              'total_charge_str' => $total_charge_mycurrency_str,
              'total_charge_num' => $total_charge_mycurrency,
    		      'total_charge_num_cedis' => $total_amount_cedis
              );
    array_push($newsfeedReturn["hits"], $next);	
	echo json_encode($newsfeedReturn); exit;


} else {

	$next  = array(
              'hit_status' => "7"
              );
    array_push($newsfeedReturn["hits"], $next);	
	echo json_encode($newsfeedReturn); exit;


}



/********************* END CALCULATE DELIVERY CHARGE HERE ******************/		


						     
						}else{

		                	$next  = array(
				                      'hit_status' => "4"
				                      );
					        array_push($newsfeedReturn["hits"], $next);	
		    				echo json_encode($newsfeedReturn); exit;
    	
						}
				} 
          	}

    				echo json_encode($newsfeedReturn); exit;

          } // END OF PASSWORD CHECK
    }
}