<?php

if(
isset($_POST['myid']) && trim($_POST['myid']) != "" 
&& isset($_POST['mypass']) && trim($_POST['mypass']) != "" 
&& isset($_POST['adetor_news_id']) && trim($_POST['adetor_news_id']) != "" 
&& isset($_POST['adetor_receiver_name']) && trim($_POST['adetor_receiver_name']) != "" 
&& isset($_POST['adetor_receiver_phone']) && trim($_POST['adetor_receiver_phone']) != "" 
&& isset($_POST['adetor_delivery_address']) && trim($_POST['adetor_delivery_address']) != ""
&& isset($_POST['adetor_delivery_type']) && trim($_POST['adetor_delivery_type']) != "" 
&& isset($_POST['item_quantity']) && trim($_POST['item_quantity']) != "" 
&& isset($_POST['adetor_currency']) && trim($_POST['adetor_currency']) != "" 
&& isset($_POST['adetor_price_per_item']) && trim($_POST['adetor_price_per_item']) != "" 
&& isset($_POST['delivery_charge_num']) && trim($_POST['delivery_charge_num']) != "" 
&& isset($_POST['total_charge_num']) && trim($_POST['total_charge_num']) != "" 
&& isset($_POST['adetor_status_code']) && trim($_POST['adetor_status_code']) != "" 
&& isset($_POST['adetor_pay_type']) && trim($_POST['adetor_pay_type']) != "" 
&& isset($_POST['adetor_status_message']) && trim($_POST['adetor_status_message']) != ""
&& isset($_POST['mycountry']) && trim($_POST['mycountry']) != "") {
require_once("config.php");

include(ROOT_PATH . 'inc/db_connect.php');

$myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
$mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
$adetor_news_id = mysqli_real_escape_string($mysqli, $_POST['adetor_news_id']);
$adetor_receiver_name = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_name']);
$adetor_receiver_phone = mysqli_real_escape_string($mysqli, $_POST['adetor_receiver_phone']);
$adetor_delivery_address = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_address']);
$adetor_delivery_type = mysqli_real_escape_string($mysqli, $_POST['adetor_delivery_type']);
$delivery_type = $adetor_delivery_type;
$item_quantity = mysqli_real_escape_string($mysqli, $_POST['item_quantity']);
$adetor_currency = mysqli_real_escape_string($mysqli, $_POST['adetor_currency']);
$adetor_price_per_item = mysqli_real_escape_string($mysqli, $_POST['adetor_price_per_item']);
$delivery_charge_num = mysqli_real_escape_string($mysqli, $_POST['delivery_charge_num']);
$total_charge_num = mysqli_real_escape_string($mysqli, $_POST['total_charge_num']);
$adetor_status_code = mysqli_real_escape_string($mysqli, $_POST['adetor_status_code']);
$adetor_pay_type = mysqli_real_escape_string($mysqli, $_POST['adetor_pay_type']);
$adetor_status_message = mysqli_real_escape_string($mysqli, $_POST['adetor_status_message']);
$mycountry = mysqli_real_escape_string($mysqli, $_POST['mycountry']);
$mycountry = trim($mycountry);
$adetor_type = "up4sale";

if($mycountry != "Ghana" && $mycountry != "United Kingdom" && $mycountry != "USA"){

  $mycountry = "USA";
  
}


    $myid = trim($myid);
    $mypass = trim($mypass);
    $adetor_news_id = trim($adetor_news_id);
    $adetor_receiver_name = trim($adetor_receiver_name);
    $adetor_receiver_phone = trim($adetor_receiver_phone);
    $adetor_delivery_address = trim($adetor_delivery_address);
    $adetor_delivery_address = strtolower($adetor_delivery_address);
    $adetor_delivery_type = trim($adetor_delivery_type);
    $item_quantity = trim($item_quantity);
    $item_quantity = intval($item_quantity);


    $today = date("F j, Y");
    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag, full_name FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $dbflag = trim($row["flag"]);
          $dbfull_name = trim($row["full_name"]);

          if($mypass == $dbpass && $dbflag == 0) {

		  		$newsfeedReturn["hits"] = array();
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
                      $GHS_coins = $row["GHS_coins"];
                      $USD_coins = $row["USD_coins"];
                      $GBP_coins = $row["GBP_coins"];
                      $rates = 1;

                    } else {
                        $next  = array(
                                  'hit_status' => "0"
                                  );
                        array_push($newsfeedReturn["hits"], $next); 
                        echo json_encode($newsfeedReturn); exit;
                    }

/////////////// START OF ADDRESS CALCULATIONS ///////////////////////

              $query = "SELECT item_quantity,seller_id,item_weight_type, sale_status, item_location,item_price, currency, number_sold FROM up4sale WHERE up4sale_news_id = '$adetor_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {


                $row = $result->fetch_array(MYSQLI_ASSOC);
                $up4sale_sale_status = $row["sale_status"];
                $fp_seller_id = trim($row["seller_id"]);
                if($fp_seller_id != "030250308659e9029382af83.46926837"){

                    $next  = array(
                              'hit_status' => "2"
                              );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;
                }
                $seller_id = $fp_seller_id;
                $up4sale_item_location = trim($row["item_location"]);
                $up4sale_item_location = strtolower($up4sale_item_location);
                $up4sale_item_quantity = $row["item_quantity"];
                $up4sale_item_price = trim($row["item_price"]);
                $up4sale_seller_currency = $row["currency"];
                $up4sale_number_sold = trim($row["number_sold"]);
                $up4sale_number_sold = intval($up4sale_number_sold);
                $up4sale_item_weight_type = trim($row["item_weight_type"]);

                if($up4sale_sale_status == "1"){

                    $next  = array(
                              'hit_status' => "3"
                              );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                } else if ($item_quantity > intval($up4sale_item_quantity - $up4sale_number_sold)){

                    $next  = array(
                              'hit_status' => "4"
                              );
                    array_push($newsfeedReturn["hits"], $next); 
                    echo json_encode($newsfeedReturn); exit;

                }

                //echo "up4sale_item_price : " . $up4sale_item_price . " \n";
                //echo "up4sale_item_location : " . $up4sale_seller_currency . " \n";
                //echo "adetor_delivery_address : " . $adetor_delivery_address . " \n"; exit;

    include(ROOT_PATH . 'inc/db_connect_ferry.php');
$query = "SELECT add_long, add_lat, add_name FROM addressofmine WHERE add_id = '$up4sale_item_location'";

                                    $result = $mysqli2->query($query);

                                    if (mysqli_num_rows($result) != "0") {


                                        $row = $result->fetch_array(MYSQLI_ASSOC);
                                        $lon1 = trim($row["add_long"]);
                                        $lat1 = trim($row["add_lat"]);
                                        $pickup_name = trim($row["add_name"]);
										$pickup_latlng = $lat1 . "," . $lon1;

                                    } else {

                                        $next  = array(
                                                  'hit_status' => "5"
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

    $adetor_currency = "pearls";
    $adetor_price_per_item = floatval($up4sale_item_price);



if(substr($adetor_delivery_address,0,5) == "ferry"){

    include(ROOT_PATH . 'inc/db_connect_ferry.php');
		$query = "SELECT add_long, add_lat, add_name, verified_status, investor_id FROM addressofmine WHERE add_id = '$adetor_delivery_address'";

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
				$url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $pickup_latlng . '&sensor=false&key=' . GOOGLE_MAP_KEY;

				//$url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=6.673159,-1.565402&sensor=false&key=' . GOOGLE_MAP_KEY;
				                $geocode=file_get_contents($url);
				                $output= json_decode($geocode, true);

    			$pickup_country =  trim($output["results"][0]["address_components"][3]["long_name"]);
    			$pickup_country = str_replace(" ", "", $pickup_country);
    			$pickup_region =  trim($output["results"][0]["address_components"][2]["long_name"]);

			$pickup_country2 =  trim($output["results"][0]["address_components"][4]["long_name"]);
			$pickup_country2 = str_replace(" ", "", $pickup_country2);
			$pickup_region2 =  trim($output["results"][0]["address_components"][3]["long_name"]);

				$url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $destination_latlng . '&sensor=false&key=' . GOOGLE_MAP_KEY;
				                $geocode=file_get_contents($url);
				                $output= json_decode($geocode, true);

		$destination_country =  trim($output["results"][0]["address_components"][3]["long_name"]);
    			$destination_country = str_replace(" ", "", $destination_country);
		$destination_region =  trim($output["results"][0]["address_components"][2]["long_name"]);

		$destination_country2 =  trim($output["results"][0]["address_components"][4]["long_name"]);
    			$destination_country2 = str_replace(" ", "", $destination_country2);
		$destination_region2 =  trim($output["results"][0]["address_components"][3]["long_name"]);

              $delivery_charge_table_id = $pickup_country . "_" . $destination_country;
              $delivery_charge_table_id = strtolower($delivery_charge_table_id);

              $delivery_charge_table_id2 = $pickup_country . "_" . $destination_country2;
              $delivery_charge_table_id2 = strtolower($delivery_charge_table_id2);

              $delivery_charge_table_id3 = $pickup_country2 . "_" . $destination_country;
              $delivery_charge_table_id3 = strtolower($delivery_charge_table_id3);

              $delivery_charge_table_id4 = $pickup_country2 . "_" . $destination_country2;
              $delivery_charge_table_id4 = strtolower($delivery_charge_table_id4);

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
              echo "FOUR delivery_charge_table_id : " . $delivery_charge_table_id4 . "\n"; exit;
*/
              // IDS FOR NEWS DELIVERIES TO COUNTRIES CONSIST OF
              // THE GOOGLE RETURNED NAME OF THE PICKUP COUNTRY AND DESTINATION COUNTRY WITH
              // ALL WHITE SPACES REMOVED AND ALL CAPS TO LOWER CASE


  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id'";
                    $result = $mysqli->query($query);
                    if (mysqli_num_rows($result) != "0") {

                      $row = $result->fetch_array(MYSQLI_ASSOC);
                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                	  $pickup_country_real = $pickup_country;
                	  $pickup_region_real = $pickup_region;
                	  $destination_country_real = $destination_country;
                	  $destination_region_real = $destination_region;

                    } else {

						  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id2'";
			                    $result = $mysqli->query($query);
			                    if (mysqli_num_rows($result) != "0") {

			                      $row = $result->fetch_array(MYSQLI_ASSOC);
			                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
			                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                	  $pickup_country_real = $pickup_country;
                	  $pickup_region_real = $pickup_region;
                	  $destination_country_real = $destination_country2;
                	  $destination_region_real = $destination_region2;

			                    } else {
								  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id3'";
					                    $result = $mysqli->query($query);
					                    if (mysqli_num_rows($result) != "0") {

					                      $row = $result->fetch_array(MYSQLI_ASSOC);
					                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
				                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                	  $pickup_country_real = $pickup_country2;
                	  $pickup_region_real = $pickup_region2;
                	  $destination_country_real = $destination_country;
                	  $destination_region_real = $destination_region;


					                    } else {
										  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id4'";
							                    $result = $mysqli->query($query);
							                    if (mysqli_num_rows($result) != "0") {

							                      $row = $result->fetch_array(MYSQLI_ASSOC);
							                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
							                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

                	  $pickup_country_real = $pickup_country2;
                	  $pickup_region_real = $pickup_region2;
                	  $destination_country_real = $destination_country2;
                	  $destination_region_real = $destination_region2;


							                    } else {
								              		$next  = array(
										                      'hit_status' => "5"
										                      );
											        array_push($newsfeedReturn["hits"], $next);	
								    				echo json_encode($newsfeedReturn); exit;
							                    }
					                    }
			                    }
                    }
    if($pickup_country_real == $pickup_country_real){
		$destination_region_real_table = str_replace(" ", "", $destination_region_real);
		$pickup_region_real_table = str_replace(" ", "", $pickup_region_real);
		$delivery_region_table_id = $pickup_region_real_table . "_" . $destination_region_real_table;
		$delivery_region_table_id = strtolower($delivery_region_table_id);
		//echo "delivery_region_table_id : " . $delivery_region_table_id;
		$query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_region_table_id'";
		                    $result = $mysqli->query($query);
		                    if (mysqli_num_rows($result) != "0") {

		                      $row = $result->fetch_array(MYSQLI_ASSOC);
		                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];

		                    } else {
			              		$next  = array(
					                      'hit_status' => "5a"
					                      );
						        array_push($newsfeedReturn["hits"], $next);	
			    				echo json_encode($newsfeedReturn); exit;
		                    }
    }
/*
              echo "pickup_country_real : " . $pickup_country_real . "\n";
              echo "pickup_region_real : " . $pickup_region_real . "\n";
              echo "destination_country_real : " . $destination_country_real . "\n";
              echo "destination_region_real : " . $destination_region_real . "\n";
*/

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

							//echo "1 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "1 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis * 2;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "1 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}


				} else if($delivery_type == "Economy Delivery"){

						//echo "Economy Delivery" . "\n";

					if($pickup_country_real == $destination_country_real){

					// SAME COUNTRY (INTRA) DELIVERY HERE
						if($pickup_region_real != $destination_region_real){

							//echo "1 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "1 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "1 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}

				}  else if($delivery_type == "Tortoise Delivery"){

						//echo "Tortoise Delivery" . "\n";

					if($pickup_country_real == $destination_country_real){

					// SAME COUNTRY (INTRA) DELIVERY HERE
						if($pickup_region_real != $destination_region_real){

							//echo "1 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "1 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis * 0.7;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "1 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}


				}  else {

	              		$next  = array(
			                      'hit_status' => "6"
			                      );
				        array_push($newsfeedReturn["hits"], $next);	
	    				echo json_encode($newsfeedReturn); exit;

				} 

$delivery_charge_num_cedis = ceil($delivery_charge_num_cedis);

$convert_amt = ($adetor_price_per_item * $item_quantity);
$i_country = "Ghana";
$seller_country =  "Ghana";

include(ROOT_PATH . 'inc/android_currency_converter.php');
$item_total_cost_cedis = $new_amt_user;
$item_total_cost_cedis_str = $new_amt_user_str;
$item_total_cost_cedis_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);

if($item_total_cost_cedis != "" && $delivery_charge_num_cedis != ""){

	$total_charge_in_coins = ($item_total_cost_cedis + $delivery_charge_num_cedis) * $GHS_coins;
	$total_charge_in_coins = ceil($total_charge_in_coins);
    $adetor_price_per_item_pearls = $adetor_price_per_item * $GHS_coins;
    $delivery_charge_pearls = $delivery_charge_num_cedis * $GHS_coins;

	  $query = "SELECT net_worth FROM investor WHERE investor_id = '$myid'";
	        $result = $mysqli->query($query);
	        if (mysqli_num_rows($result) != "0") {

	          $row = $result->fetch_array(MYSQLI_ASSOC);

	          $buyer_pott_pearls = $row["net_worth"];
	          $buyer_pott_pearls = intval($buyer_pott_pearls);

	        } else {
	            $next  = array(
	                      'hit_status' => "5b"
	                      );
	            array_push($newsfeedReturn["hits"], $next); 
	            echo json_encode($newsfeedReturn); exit;
	        }



	if($buyer_pott_pearls > $total_charge_in_coins){

		$new_buyer_pott_pearls = $buyer_pott_pearls - $total_charge_in_coins;

$query = "UPDATE investor SET net_worth = $new_buyer_pott_pearls WHERE investor_id = '$myid'";
	$result = $mysqli->query($query);

	if($result == true){

		$bought = 1;


	} else {

	    $next  = array(
	              'hit_status' => "0"
	              );
	    array_push($newsfeedReturn["hits"], $next); 
	    echo json_encode($newsfeedReturn); exit;


	}


	} else {
	            $next  = array(
	                      'hit_status' => "11"
	                      );
	            array_push($newsfeedReturn["hits"], $next); 
	            echo json_encode($newsfeedReturn); exit;


	}


// MAKE SUBS

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


          		$delivery_address = urlencode($adetor_delivery_address);
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

				$url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $pickup_latlng . '&sensor=false&key=' . GOOGLE_MAP_KEY;
				                $geocode=file_get_contents($url);
				                $output= json_decode($geocode, true);

    			$pickup_country =  trim($output["results"][0]["address_components"][3]["long_name"]);
    			$pickup_country = str_replace(" ", "", $pickup_country);
    			$pickup_region =  trim($output["results"][0]["address_components"][2]["long_name"]);

			$pickup_country2 =  trim($output["results"][0]["address_components"][4]["long_name"]);
			$pickup_country2 = str_replace(" ", "", $pickup_country2);
			$pickup_region2 =  trim($output["results"][0]["address_components"][3]["long_name"]);


				$url= 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $destination_latlng . '&sensor=false&key=' . GOOGLE_MAP_KEY;
				                $geocode=file_get_contents($url);
				                $output= json_decode($geocode, true);

		$destination_country =  trim($output["results"][0]["address_components"][3]["long_name"]);
    			$destination_country = str_replace(" ", "", $destination_country);
		$destination_region =  trim($output["results"][0]["address_components"][2]["long_name"]);

		$destination_country2 =  trim($output["results"][0]["address_components"][5]["long_name"]);
    			$destination_country2 = str_replace(" ", "", $destination_country2);
		$destination_region2 =  trim($output["results"][0]["address_components"][4]["long_name"]);

              $delivery_charge_table_id = $pickup_country . "_" . $destination_country;
              $delivery_charge_table_id = strtolower($delivery_charge_table_id);

              $delivery_charge_table_id2 = $pickup_country . "_" . $destination_country2;
              $delivery_charge_table_id2 = strtolower($delivery_charge_table_id2);

              $delivery_charge_table_id3 = $pickup_country2 . "_" . $destination_country;
              $delivery_charge_table_id3 = strtolower($delivery_charge_table_id3);

              $delivery_charge_table_id4 = $pickup_country2 . "_" . $destination_country2;
              $delivery_charge_table_id4 = strtolower($delivery_charge_table_id4);



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
                    $result = $mysqli->query($query);
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

						  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id2'";
			                    $result = $mysqli->query($query);
			                    if (mysqli_num_rows($result) != "0") {

			                      $row = $result->fetch_array(MYSQLI_ASSOC);
			                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
			                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);
//echo " 2 \n";
                	  $pickup_country_real = $pickup_country;
                	  $pickup_region_real = $pickup_region;
                	  $destination_country_real = $destination_country2;
                	  $destination_region_real = $destination_region2;

			                    } else {
								  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id3'";
					                    $result = $mysqli->query($query);
					                    if (mysqli_num_rows($result) != "0") {

					                      $row = $result->fetch_array(MYSQLI_ASSOC);
					                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
				                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);

//echo " 3 \n";
                	  $pickup_country_real = $pickup_country2;
                	  $pickup_region_real = $pickup_region2;
                	  $destination_country_real = $destination_country;
                	  $destination_region_real = $destination_region;


					                    } else {
										  $query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_charge_table_id4'";
							                    $result = $mysqli->query($query);
							                    if (mysqli_num_rows($result) != "0") {

							                      $row = $result->fetch_array(MYSQLI_ASSOC);
							                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];
							                	  $charge_per_km_in_cedis = floatval($charge_per_km_in_cedis);
//echo " 4 \n";
							                	  
                	  $pickup_country_real = $pickup_country2;
                	  $pickup_region_real = $pickup_region2;
                	  $destination_country_real = $destination_country2;
                	  $destination_region_real = $destination_region2;


							                    } else {
								              		$next  = array(
										                      'hit_status' => "5"
										                      );
											        array_push($newsfeedReturn["hits"], $next);	
								    				echo json_encode($newsfeedReturn); exit;
							                    }
					                    }
			                    }
                    }

    if($pickup_country_real == $destination_country_real){

		$destination_region_real_table = str_replace(" ", "", $destination_region_real);
		$pickup_region_real_table = str_replace(" ", "", $pickup_region_real);
		$delivery_region_table_id = $pickup_region_real_table . "_" . $destination_region_real_table;
		$delivery_region_table_id = strtolower($delivery_region_table_id);

		//echo "delivery_region_table_id : " . $delivery_region_table_id;
		$query = "SELECT charge_per_km_in_cedis FROM delivery_akatua WHERE sender_receiver_country = '$delivery_region_table_id'";
		                    $result = $mysqli->query($query);
		                    if (mysqli_num_rows($result) != "0") {

		                      $row = $result->fetch_array(MYSQLI_ASSOC);
		                      $charge_per_km_in_cedis = $row["charge_per_km_in_cedis"];

		                    } else {
			              		$next  = array(
					                      'hit_status' => "5"
					                      );
						        array_push($newsfeedReturn["hits"], $next);	
			    				echo json_encode($newsfeedReturn); exit;
		                    }
    }

/*
              echo "pickup_country_real : " . $pickup_country_real . "\n";
              echo "pickup_region_real : " . $pickup_region_real . "\n";
              echo "destination_country_real : " . $destination_country_real . "\n";
              echo "destination_region_real : " . $destination_region_real . "\n";
*/
              //echo "2 distance : " . $calc_distance . " km \n";

				if($delivery_type == "Express Delivery"){

						//echo "2 Express Delivery" . "\n";

					if($pickup_country_real == $destination_country_real){

					// SAME COUNTRY (INTRA) DELIVERY HERE
						if($pickup_region_real != $destination_region_real){

							//echo "2 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "2 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis * 2;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "2 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}


				} else if($delivery_type == "Economy Delivery"){

						//echo "2 Economy Delivery" . "\n";

					if($pickup_country_real == $destination_country_real){

					// SAME COUNTRY (INTRA) DELIVERY HERE
						if($pickup_region_real != $destination_region_real){

							//echo "2 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "2 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "2 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}

				}  else if($delivery_type == "Tortoise Delivery"){

						//echo "2 Tortoise Delivery" . "\n";

					if($pickup_country_real == $destination_country_real){

					// SAME COUNTRY (INTRA) DELIVERY HERE
						if($pickup_region_real != $destination_region_real){

							//echo "2 CROSS-REGION Delivery" . "\n";

						} else {

							//echo "2 INTRA-REGION Delivery" . "\n";
							$delivery_charge_num_cedis = ($calc_distance + $item_order_quantity + $item_weight_type + 15) * $charge_per_km_in_cedis * 0.7;

							$delivery_charge_num_cedis = floatval($delivery_charge_num_cedis);
							
							if($delivery_charge_num_cedis < 15){

								$delivery_charge_num_cedis = 15;

							}


						}


					} else {

						//echo "2 INTERNATIONAL Delivery" . "\n";
						$delivery_charge_num_cedis = $calc_distance * $item_order_quantity * $item_weight_type * $charge_per_km_in_cedis * 5;

					}


				}  else {

	              		$next  = array(
			                      'hit_status' => "6"
			                      );
				        array_push($newsfeedReturn["hits"], $next);	
	    				echo json_encode($newsfeedReturn); exit;

				} 


$delivery_charge_num_cedis = ceil($delivery_charge_num_cedis);

$convert_amt = ($adetor_price_per_item * $item_quantity);
$i_country = "Ghana";
$seller_country =  "Ghana";

include(ROOT_PATH . 'inc/android_currency_converter.php');
$item_total_cost_cedis = $new_amt_user;
$item_total_cost_cedis_str = $new_amt_user_str;
$item_total_cost_cedis_curr = $new_amt_user_currency;

unset($new_amt_user);
unset($new_amt_user_currency);
unset($new_amt_user_str);

if($item_total_cost_cedis != "" && $delivery_charge_num_cedis != ""){

	$total_charge_in_coins = ($item_total_cost_cedis + $delivery_charge_num_cedis) * $GHS_coins;
	$total_charge_in_coins = ceil($total_charge_in_coins);
    $adetor_price_per_item_pearls = $adetor_price_per_item * $GHS_coins;
    $delivery_charge_pearls = $delivery_charge_num_cedis * $GHS_coins;

	  $query = "SELECT net_worth FROM investor WHERE investor_id = '$myid'";
	        $result = $mysqli->query($query);
	        if (mysqli_num_rows($result) != "0") {

	          $row = $result->fetch_array(MYSQLI_ASSOC);

	          $buyer_pott_pearls = $row["net_worth"];
	          $buyer_pott_pearls = intval($buyer_pott_pearls);

	        } else {
	            $next  = array(
	                      'hit_status' => "0"
	                      );
	            array_push($newsfeedReturn["hits"], $next); 
	            echo json_encode($newsfeedReturn); exit;
	        }


	if($buyer_pott_pearls > $total_charge_in_coins){

		$new_buyer_pott_pearls = $buyer_pott_pearls - $total_charge_in_coins;

$query = "UPDATE investor SET net_worth = $new_buyer_pott_pearls WHERE investor_id = '$myid'";
	$result = $mysqli->query($query);

	if($result == true){

		$bought = 1;


	} else {

	    $next  = array(
	              'hit_status' => "0"
	              );
	    array_push($newsfeedReturn["hits"], $next); 
	    echo json_encode($newsfeedReturn); exit;


	}


	} else {
	            $next  = array(
	                      'hit_status' => "11"
	                      );
	            array_push($newsfeedReturn["hits"], $next); 
	            echo json_encode($newsfeedReturn); exit;


	}


// MAKE SUBS

} else {

	$next  = array(
              'hit_status' => "7"
              );
    array_push($newsfeedReturn["hits"], $next);	
	echo json_encode($newsfeedReturn); exit;


}



/********************* END CALCULATE DELIVERY CHARGE HERE ******************/		


						     
						} else{

		                	$next  = array(
				                      'hit_status' => "9"
				                      );
					        array_push($newsfeedReturn["hits"], $next);	
		    				echo json_encode($newsfeedReturn); exit;
    	
					}
				} 
      }

if(!isset($bought) || $bought != 1){

    	$next  = array(
                  'hit_status' => "0"
                  );
        array_push($newsfeedReturn["hits"], $next);	
		echo json_encode($newsfeedReturn); exit;

}

////////////// END OF CHARGE CALCULATIONS ///////////////////////////            

			$query = "SELECT first_name, last_name, pot_name, profile_picture FROM investor WHERE investor_id = '$myid'";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {
				$row = $result->fetch_array(MYSQLI_ASSOC);
				$first_name = trim($row["first_name"]);
                $last_name = trim($row["last_name"]);
                $pot_name = trim($row["pot_name"]);
                $my_full_name = $first_name . " " . $last_name;
				$not_text = $my_full_name . " bought an item from your pott";
              $my_profile_picture = trim($row["profile_picture"]);
              
	          if (!file_exists("../pic_upload/" . $my_profile_picture)) {

	          		$my_profile_picture = "";
        		} else {

					$my_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $my_profile_picture; 
        		}
			} else {
                $my_full_name = "Someone";
				$not_text = "Someone bought an item from your pott";
	          	$my_profile_picture = "";
	          	$pot_name = "";
			}

          $query = "SELECT inputtor_id, type FROM newsfeed WHERE news_id = '$adetor_news_id' ";
              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $seller_id = trim($row["inputtor_id"]);
				if(isset($_POST["adetor_type"]) && trim($_POST["adetor_type"]) != ""){

					$adetor_type = mysqli_real_escape_string($mysqli, $_POST['adetor_type']);
					$adetor_type = trim($adetor_type);

				}else {

                	$adetor_type = trim($row["type"]);

				}

                if($adetor_type == "up4sale"){

                $query = "SELECT item_quantity, number_sold FROM up4sale WHERE up4sale_news_id = '$adetor_news_id' ";

              $result = $mysqli->query($query);
              if (mysqli_num_rows($result) != "0") {

                        $row = $result->fetch_array(MYSQLI_ASSOC);
                        $db_item_quantity = trim($row["item_quantity"]);
                        $db_item_quantity = intval($db_item_quantity);
                        $db_number_sold = trim($row["number_sold"]);
                        $db_number_sold = intval($db_number_sold) + $item_quantity;
                        $db_item_quantity = $db_item_quantity - $item_quantity;
                        if($db_item_quantity <= 0){

                            $sale_status = 1;

                        } else {

                            $sale_status = 0;

                        }

        $query = "UPDATE up4sale SET number_sold = $db_number_sold,  sale_status = $sale_status WHERE up4sale_news_id = '$adetor_news_id'";
        $result = $mysqli->query($query);
            }

            } 

            } else {

                	$adetor_type = "unknown";
                	$seller_id = "unknown";
            }


            $short_id = uniqid();
            
            $date_time = date("Y-m-d H:i:s");
            $date_time = trim($date_time);

            $table_name = "adetor";

            $column1_name = "adetor_news_id";
            $column2_name = "adetor_id_short";
            $column3_name = "adetor_type";
            $column4_name = "seller_id";
            $column5_name = "buyer_id";
            $column6_name = "transaction_currency";
            $column7_name = "price_per_item_num";
            $column8_name = "item_quantity";
            $column9_name = "receiver_name";
            $column10_name = "receiver_phone";
            $column11_name = "delivery_address";
            $column12_name = "delivery_type";
            $column13_name = "delivery_charge_num";
            $column14_name = "total_charge_num";
            $column15_name = "adetor_pay_type";
            $column16_name = "date_time";
            $column17_name = "adetor_status_code";
            $column18_name = "adetor_status_message";

            $column1_value = $adetor_news_id;
            $column2_value = $short_id;
            $column3_value = $adetor_type;
            $column4_value = $seller_id;
            $column5_value = $myid;
            $column6_value = $adetor_currency;
            $column7_value = $adetor_price_per_item_pearls;
            $column8_value = $item_quantity;
            $column9_value = $adetor_receiver_name;
            $column10_value = $adetor_receiver_phone;
            $column11_value = $adetor_delivery_address;
            $column12_value = $adetor_delivery_type;
            $column13_value = $delivery_charge_pearls;
            $column14_value = $total_charge_in_coins;
            $column15_value = "pearls";
            $column16_value = $date_time;
            $column17_value = $adetor_status_code;
            $column18_value = $adetor_status_message;

            $pam1 = "s";
            $pam2 = "s";
            $pam3 = "s";
            $pam4 = "s";
            $pam5 = "s";
            $pam6 = "s";
            $pam7 = "s";
            $pam8 = "i";
            $pam9 = "s";
            $pam10 = "s";
            $pam11 = "s";
            $pam12 = "s";
            $pam13 = "d";
            $pam14 = "d";
            $pam15 = "s";
            $pam16 = "s";
            $pam17 = "i";
            $pam18 = "s";

            include(ROOT_PATH . 'inc/insert18_prepared_statement.php');

                        if($done == 1){
		              		$next  = array(
				                      'hit_status' => "1"
				                      );
					        array_push($newsfeedReturn["hits"], $next);	
		    				echo json_encode($newsfeedReturn);
                        } else {
		              		$next  = array(
				                      'hit_status' => "10"
				                      );
					        array_push($newsfeedReturn["hits"], $next);	
		    				echo json_encode($newsfeedReturn); exit;
                        }
                  include(ROOT_PATH . 'inc/db_connect.php');




		    $query = "SELECT inputtor_id FROM newsfeed WHERE news_id = '$adetor_news_id'";

			    $result = $mysqli->query($query);
				
				if (mysqli_num_rows($result) != "0") {
					$row = $result->fetch_array(MYSQLI_ASSOC);
					$inputtor_id = trim($row["inputtor_id"]);

		$subject = "New PURCHASE ON FISHPOTT WITH PEARLS (" . $adetor_status_message . ")";
		$message = "Buyer ID : " . $myid . "\n Buyer Name : " . $dbfull_name . "\n Item News ID : " . $adetor_news_id . "\n Seller ID : " . $inputtor_id;

                        
			  $headers = "From: <info@fishpott.com>FishPott App";
			  mail("info@fishpott.com",$subject,$message
			  	,  $headers);

              if($adetor_type == "up4sale"){

                $not_text = $my_full_name . " bought an item from your pott's yardsale";

              } else if ($adetor_type == "shares4sale"){

                $not_text = $my_full_name . " bought shares from your pott's stock sales";

              } else if ($adetor_type == "fundraiser"){

                $not_text = $my_full_name . " contributed to your fundraiser";

              } else if ($adetor_type == "event"){

                $not_text = $my_full_name . " bought a ticket for an event on your pott";

              } else if ($adetor_type == "advert"){
                
                $not_text = "An advert has been placed for your news";

              } else {

                $not_text = "There's been a transaction relating to your pott";

              }


  					//if($inputtor_id != $myid){

			            include(ROOT_PATH . 'inc/db_connect.php');
			            $table_name = "nkae";

			            $column1_name = "wo_id";
			            $column2_name = "orno_id";
			            $column3_name = "type";
			            $column4_name = "info_1";
			            $column5_name = "asem_id";

			            $noti_type = "purchase";

			            $column1_value = $inputtor_id;
			            $column2_value = $myid;
			            $column3_value = $noti_type;
			            $column4_value = $not_text;
			            $column5_value = $adetor_news_id;

			            $pam1 = "s";
			            $pam2 = "s";
			            $pam3 = "s";
			            $pam4 = "s";
			            $pam5 = "s";

			            include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
			            include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios, pot_name, first_name, last_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$inputtor_id'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                    $key = trim($row["fcm_token"]);
                    $fcm_token_web = trim($row["fcm_token_web"]);
                    $fcm_token_ios = trim($row["fcm_token_ios"]);
                    $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                    $key = $key . $fcm_token_ios . $fcm_token_web;

                  	$seller_full_name = trim($row["first_name"]) . " " . trim($row["last_name"]);
                	$seller_pot_name = trim($row["pot_name"]);
              		$seller_profile_picture = trim($row["profile_picture"]);
              
	          if (!file_exists("../pic_upload/" . $seller_profile_picture)) {

	          		$seller_profile_picture = "";
        		} else {

					$seller_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $seller_profile_picture; 
        		}


              if($adetor_type == "up4sale"){

                $not_text = "Your yardsale purchase from " . $seller_full_name . "'s pott is complete and FishPott will have your item delivered. Your transaction code is " . $short_id;

              } else if ($adetor_type == "shares4sale"){

                $not_text = "Your shares purchase from " . $seller_full_name . " is complete. Your transaction code is " . $short_id;

              } else if ($adetor_type == "fundraiser"){

                $not_text = "Your fundraiser contribution to " . $seller_full_name . " is complete. Your transaction code is " . $short_id;

              } else if ($adetor_type == "event"){
                    if(isset($not_text_event)){

                    $not_text = $not_text_event;

                    } else {

                $not_text = "Your event ticket purchase from " . $seller_full_name . "'s pott is complete. Your " . $db_event_name . " event ticket code is " . $short_id;

                    }

              } else if ($adetor == "advert"){
                
                $not_text = "Your advert placement purchase is complete and under review.";

              } else {

                $not_text = "There's been a transaction relating to your pott";

              }


//////////////////////    FCM  START      /////////////////////////

			  $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

			  $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

			  $headers = array(
			    'Authorization:key=' . $server_key, 
			    'Content-Type:application/json');

			  $title = "FishPott";

			  $myalert = $not_text;

			  $fields = array(
                "registration_ids" => $all_keys,
                "priority" => "normal",
			          'data' => array(
			          	'notification_type' => "general_notification",
			          	'not_type_real' => "purchase",
			          	'not_pic' => $seller_profile_picture,
			          	'not_title' => $title,
			          	'not_message' => $not_text,
			          	'not_image' => "",
			          	'not_video' => "",
			          	'not_text' => $not_text, 
			          	'not_pott_or_newsid' => $adetor_news_id, 
			          	'pott_name' => $seller_pot_name, 
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

//////////////////////    FCM  END      /////////////////////////  


                    //if($inputtor_id != $myid){

                        include(ROOT_PATH . 'inc/db_connect.php');
                        $table_name = "nkae";

                        $column1_name = "wo_id";
                        $column2_name = "orno_id";
                        $column3_name = "type";
                        $column4_name = "info_1";
                        $column5_name = "asem_id";

                        $noti_type = "purchase";

                        $column1_value = $myid;
                        $column2_value = $inputtor_id;
                        $column3_value = $noti_type;
                        $column4_value = $not_text;
                        $column5_value = $adetor_news_id;

                        $pam1 = "s";
                        $pam2 = "s";
                        $pam3 = "s";
                        $pam4 = "s";
                        $pam5 = "s";

                        include(ROOT_PATH . 'inc/insert5_prepared_statement.php');
                        include(ROOT_PATH . 'inc/db_connect.php');

                $query = "SELECT fcm_token, fcm_token_web, fcm_token_ios FROM investor WHERE investor_id = '$myid'";   

                $result = $mysqli->query($query);
                    
                if (mysqli_num_rows($result) != 0) {

                      $row = $result->fetch_array(MYSQLI_ASSOC);

                      $key = trim($row["fcm_token"]);
                      $fcm_token_web = trim($row["fcm_token_web"]);
                      $fcm_token_ios = trim($row["fcm_token_ios"]);
                      $all_keys = [$key, $fcm_token_ios, $fcm_token_web];
                      $key = $key . $fcm_token_ios . $fcm_token_web;

//////////////////////    FCM  START      /////////////////////////

              $path_to_fcm = "https://fcm.googleapis.com/fcm/send";

              $server_key = "AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf";

              $headers = array(
                'Authorization:key=' . $server_key, 
                'Content-Type:application/json');

              $title = "FishPott";

              $myalert = $not_text;

			  $fields = array(
                "registration_ids" => $all_keys,
                "priority" => "normal",
			          'data' => array(
			          	'notification_type' => "general_notification",
			          	'not_type_real' => "purchase",
			          	'not_pic' => $my_profile_picture,
			          	'not_title' => $title,
			          	'not_message' => $not_text,
			          	'not_image' => "",
			          	'not_video' => "",
			          	'not_text' => $not_text, 
			          	'not_pott_or_newsid' => $adetor_news_id, 
			          	'pott_name' => $pot_name, 
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

//////////////////////    FCM  END   SECOND   /////////////////////////  

                  } // TOKEN FETCH END SECOND



        } // TOKEN FETCH END


//////////////////////////
			    }

          } // END OF PASSWORD CHECK

        }

    }
