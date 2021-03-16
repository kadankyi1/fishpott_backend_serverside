<?php

	if(isset($convert_amt) && $convert_amt != 0){
		if(isset($seller_country) && isset($i_country) && $seller_country != "" && $i_country != "" ) {

			if($seller_country != "na" && $i_country != "na") {

				if(isset($convert_amt) && $convert_amt != "na" && isset($rates) && $rates == 1) {

					if(isset($convert_amt) && $i_country == "USA"){

						$new_amt_user_currency = "USD";

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_USD;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user;

						} elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt * $GBP_USD;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $convert_amt;
							$new_amt_pg = $new_amt_user; 

						}

					} elseif(isset($convert_amt) && $i_country == "Ghana"){

						$new_amt_user_currency = "GHS";

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
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

						$new_amt_user_currency = "GBP";

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_GBP;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "GBP  " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						}  elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "GBP  " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt * $GBP_USD;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt * $USD_GBP;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "GBP " . $convert_amt;
							$new_amt_pg = $new_amt_user;
						}

					} else {

						$new_amt_user_currency = "USD";

						if($seller_country == "Ghana"){

							$new_amt_user = $convert_amt * $GHS_USD;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user;

						} elseif($seller_country == "United Kingdom"){

							$new_amt_user = $convert_amt * $GBP_USD;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} elseif($seller_country == "USA"){


							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $new_amt_user;
							$new_amt_pg = $new_amt_user; 

						} else {

							$new_amt_user = $convert_amt;
							if(!isset($shares_conversion)){

								$new_amt_user = ceil($new_amt_user);
							}
							$new_amt_user_str = "USD " . $convert_amt;
							$new_amt_pg = $new_amt_user; 

						}

					}

				} 
			}

		} 
	} else {

		$new_amt_user_currency = "USD";
		$new_amt_user_str = "FREE";
		$new_amt_pg = 0; 
	}
