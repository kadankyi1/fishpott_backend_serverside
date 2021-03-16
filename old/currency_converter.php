<?php
if(!isset($config)){

require_once("config.php");

}
include(ROOT_PATH . 'inc/db_connect_autologout.php');
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

	$seller_country = "";
	$rates = 0;
}
if(!isset($sn_converter_sh_toggl) || $sn_converter_sh_toggl == 0){
	if(isset($convert_amt) && $convert_amt != 0){
		if(isset($seller_country) && isset($i_country) && $seller_country != "" && $i_country != "" ) {

			if($seller_country != "na" && $i_country != "na") {

				if(isset($convert_amt) && $convert_amt != "na" && isset($rates) && $rates == 1) {

					if(isset($convert_amt) && $i_country == "USA"){

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_USD;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user;

						} elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt * $GBP_USD;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $convert_amt;
							$new_amt_pg = $new_amt_user; 

						}

					} elseif(isset($convert_amt) && $i_country == "Ghana"){

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GH¢ " . $convert_amt;
							$new_amt_pg = $new_amt_user;

						}  elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt * $GBP_GHS;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GH¢  " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt * $USD_GHS;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GH¢ " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt * $USD_GHS;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GH¢ " . $new_amt_user;
							$new_amt_pg = $new_amt_user;
						}

					}  elseif(isset($convert_amt) && $i_country == "United Kingdom"){

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_GBP;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GBP  " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						}  elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GBP  " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt * $GBP_USD;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt * $USD_GBP;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "GBP " . $convert_amt;
							$new_amt_pg = $new_amt_user;
						}

					} else {

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_USD;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user;

						} elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt * $GBP_USD;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt;
							$new_amt_user = ceil($new_amt_user);
							$new_amt_user_str = "USD " . $convert_amt;
							$new_amt_pg = $new_amt_user; 

						}

					}

				} 
			}

		} 
	} else {

		$new_amt_user_str = "FREE";
		$new_amt_pg = 0; 
	}
} elseif (isset($sn_converter_sh_toggl) && $sn_converter_sh_toggl == 1) {
	if(isset($sn_convert_amt) && $sn_convert_amt != 0){
		if(isset($sn_seller_country) && isset($i_country) && $sn_seller_country != "" && $i_country != "" ) {

			if($sn_seller_country != "na" && $i_country != "na") {

				if(isset($sn_convert_amt) && $sn_convert_amt != "na" && isset($rates) && $rates == 1) {

					if(isset($sn_convert_amt) && $i_country == "USA"){

						if($sn_seller_country == "Ghana"){

							$sn_new_amt_user = $sn_convert_amt * $GHS_USD;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "USD " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user;

						} elseif($sn_seller_country == "United Kingdom"){

							$sn_new_amt_user = $sn_convert_amt * $GBP_USD;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "USD " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user; 

						} elseif($sn_seller_country == "USA"){

							$sn_new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "USD " . $sn_convert_amt;
							$new_amt_pg = $sn_new_amt_user; 

						} else {

							$sn_new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "USD " . $sn_convert_amt;
							$new_amt_pg = $sn_new_amt_user; 

						}

					} elseif(isset($sn_convert_amt) && $i_country == "Ghana"){

						if($sn_seller_country == "Ghana"){

							$sn_new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GH¢ " . $sn_convert_amt;
							$new_amt_pg = $sn_new_amt_user;

						}  elseif($sn_seller_country == "United Kingdom"){

							$sn_new_amt_user = $sn_convert_amt * $GBP_GHS;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GH¢ " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user; 

						} elseif($sn_seller_country == "USA"){
							
							$sn_new_amt_user = $sn_convert_amt * $USD_GHS;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GHS " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user; 

						} else {

							$sn_new_amt_user = $sn_convert_amt * $USD_GHS;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GH¢ " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user;
						}

					}  elseif(isset($sn_convert_amt) && $i_country == "United Kingdom"){

						if($sn_seller_country == "Ghana"){

							$sn_new_amt_user = $sn_convert_amt * $GHS_GBP;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GBP  " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user; 

						}  elseif($sn_seller_country == "United Kingdom"){

							$sn_new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GBP  " . $sn_new_amt_user;
							$new_amt_pg = $sn_new_amt_user; 

						} elseif($sn_seller_country == "USA"){
							
							$sn_new_amt_user = $sn_convert_amt * $USD_GBP;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "USD " . $sn_convert_amt;
							$new_amt_pg = $sn_new_amt_user; 

						}  else {

							$sn_new_amt_user = $sn_convert_amt * $USD_GBP;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$sn_new_amt_user_str = "GBP " . $sn_convert_amt;
							$new_amt_pg = $sn_convert_amt;
						}

					} else {

						if($seller_country == "Ghana"){

							$new_amt_user = $sn_convert_amt * $GHS_USD;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user;

						} elseif($seller_country == "United Kingdom"){

							$new_amt_user = $sn_convert_amt * $GBP_USD;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $sn_convert_amt;
							$sn_new_amt_user = ceil($sn_new_amt_user);
							$new_amt_user_str = "USD " . $convert_amt;
							$new_amt_pg = $new_amt_user; 

						}

					}




				}

			}

		} 
	} else {

							$new_amt_pg = 0;
							$new_amt_user_str = "FREE";
	}
} 

if(isset($sn_converter_sh_toggl)){

	unset($sn_converter_sh_toggl);	
}

if(isset($sn_convert_amt)){

	unset($sn_convert_amt);	
}

if(isset($convert_amt)){

	unset($convert_amt);	
}

	//$new_amt_user_str = "new_amt_user_str : " . $new_amt_user_str .  "i_country : " . $i_country . "<br>  " . "seller_country : " . $seller_country . "<br> " . "<br>convert_amt : " . $convert_amt . "<br> ". "<br>GHS_USD : " . $GHS_USD . "<br>rates : " . $rates;

