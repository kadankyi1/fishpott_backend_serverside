<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["added_item_id"]) && trim($_POST["added_item_id"]) != "" &&
	isset($_POST["added_item_quantity"]) && trim($_POST["added_item_quantity"]) != "" &&
	isset($_POST["myrawpass"]) &&  trim($_POST["myrawpass"]) != "" &&
	isset($_POST["receiver_pottname"]) && trim($_POST["receiver_pottname"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {
                    
	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_POTTNAME);
		if (in_array(trim($_POST["mypottname"]), $ALL_DEVELOPER_POTTNAMES)){
			$GLOBALS["USAGE_MODE_IS_LIVE"] = DEVELOPER_USING_LIVE_MODE;
		}
	}

	

	//CALLING THE INPUT VALIDATOR CLASS & CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	include_once 'classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();

	//CALLING TO THE DATABASE CLASS & CREATING DATABASE CONNECTION OBJECT
	include_once 'classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS && CREATING PREPARED STATEMENT QUERY OBJECT
	include_once 'classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS & CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	include_once 'classes/languages_class.php';
	$languagesObject = new languagesActions();

	//CALLING TO THE COUNTRY CODES CLASS & CREATING COUNTRY CODES OBJECT
	include_once 'classes/country_codes_class.php';
	$countryCodesObject = new countryCodes();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/file_class.php';
	$fileObject = new fileActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/news_class.php';
	$newsObject = new newsActions();

	//CALLING TO THE SUPPORTED FILE CLASS & CREATING FRONT-END RESPONDER OBJECT
	include_once 'classes/time_class.php';
	$timeObject = new timeOperator();

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["log_phone"]);
	$input_pass = trim($_POST["log_pass_token"]);
	$input_added_item_id = trim($_POST["added_item_id"]);
	$input_added_item_quantity = intval($_POST["added_item_quantity"]);
	$input_myrawpass = trim($_POST["myrawpass"]);
	$input_receiver_pottname = trim($_POST["receiver_pottname"]);
	$input_mypottname = trim($_POST["mypottname"]);
	$input_my_currency = trim($_POST["my_currency"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	//DECLARING THE ARRAY FOR THE RESULTS
	$input_my_currency = $miscellaneousObject->getCurrencyAbreviationsFromSymbols($input_my_currency);

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS IS SET TO FALSE
	$government_id_verification_is_on = false;

	// MAKING SURE THE APP THE PERSON IS USING IS AN ALLOWED VERSION
	if($input_app_version_code < MINIMUM_ALLOWED_VERSION_CODE){
		$miscellaneousObject->respondFrontEnd3(2, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THE PHONE MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("login_failed_if_this_continues_uninstall_your_app_reinstall_and_login_again", $input_language));
	}

	if($input_myrawpass == "" || $validatorObject->stringIsNotMoreThanMaxLength($input_myrawpass, 20) === false){
			$miscellaneousObject->respondFrontEnd3(5, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($input_mypottname == $input_receiver_pottname){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if($input_receiver_pottname == "..."){
		if($validatorObject->stringContainsNoTags($input_mypottname) !== true){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
	} else {
		if($validatorObject->stringContainsNoTags($input_mypottname) !== true || $validatorObject->stringContainsNoTags($input_receiver_pottname) !== true){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
	}

	// CHECKING IF THE DATABASE CONNECTION WAS SUCCESSFUL
	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, government_id_verified, request_government_id, id FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "government_id_verified", "request_government_id", "id"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if(    trim($prepared_statement_results_array[0]) == "password"
		|| trim($prepared_statement_results_array[5]) == "id" 
	){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// SETTING USER ID
	$input_id = trim($prepared_statement_results_array[5]);

	//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
	if(FORCE_GOVERNMENT_STATUS || ($prepared_statement_results_array[3] == 0 && $prepared_statement_results_array[4] == 1)){
		$government_id_verification_is_on = true;
	} else {
		$government_id_verification_is_on = false;
	}

	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){
		$miscellaneousObject->respondFrontEnd3(4, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	}

	//CHECKING IF THE INPUT PASSWORD MATCHES THE DATABASE PASSWORD OTHERWISE WE FAIL THE REQUEST
	if($prepared_statement_results_array[0] != $input_pass){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("session_closed_restart_the_app_and_login_to_start_a_new_session", $input_language));
	}

	$sys_dbpass = $prepared_statement_results_array[0];

	// CHECKING IF PHONE VERIFICATION IS ON, AND WHEN CHECKING
	// IF USER ACCOUNT IS PENDING SMS VERIFICATION
	if($prepared_statement_results_array[1] == -1){
		$phone_verification_is_on = true;
	} else if($prepared_statement_results_array[1] == 0 && LOGIN_PHONE_NUMBER_VERIFICATION_IS_ON === true){
		$phone_verification_is_on = true;
		$reset_code = $miscellaneousObject->getRandomString(9);
/*****************************************************************************************************************
			

		SEND VERIFICATION SMS HERE. MAKE SURE THERE IS NO DATE IN THE DATABASE OR THE DATE IS PAST 24 HOURS


******************************************************************************************************************/
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET number_verified = ?, number_verification_code = ?, last_sms_sent_datetime = ?  WHERE number_login = ?", 4, "isss", array( -1, $reset_code, date("Y-m-d H:i:s"), $input_phone));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

	} else {
		$phone_verification_is_on = false;
	}

/*****************************************************************************************************************
			

			SAVING USER'S IP ADDRESS COUNT


******************************************************************************************************************/
	// GETTING USER'S IP ADDRESS
	$ip_address = $miscellaneousObject->getRealIpAddr();
	$ip_address_id = $miscellaneousObject->generateIPaddressID($ip_address, $input_id);


	if($ip_address != ""){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT ip_usage_count FROM " . IP_ADDRESSES_TABLE_NAME . " WHERE ip_id = ?", 1, "s", array($ip_address_id));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("ip_usage_count"), 1, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// IF THE DATABASE QUERY GOT NO RESULTS
		if($prepared_statement_results_array[0] <= 0){
			//INSERTING NEW IP ADDRESS USAGE
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . IP_ADDRESSES_TABLE_NAME . " (ip_id, investor_id, ip_address, ip_usage_count) VALUES (?, ?, ?, ?)" , 4, "sssi", array($ip_address_id, $input_id, $ip_address, 1));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}

		} else {
			// UPDATING IP USAGE COUNT
			$new_ip_count = $prepared_statement_results_array[0] + 1;
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . IP_ADDRESSES_TABLE_NAME . " SET ip_usage_count = ? WHERE ip_id = ?", 2, "is", array( $new_ip_count, $ip_address_id));
			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}
		}

	} else {
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

/***********************************************************************************************************

							GETTING CURRENCY EXCHANGE RATES

***********************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$GHS_USD = $prepared_statement_results_array[0];
	$USD_GHS = $prepared_statement_results_array[1];
	$GHS_GBP = $prepared_statement_results_array[2];
	$GBP_GHS = $prepared_statement_results_array[3];
	$USD_GBP = $prepared_statement_results_array[4];
	$GBP_USD = $prepared_statement_results_array[5];

	//UPDATING THE LAST SEEN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

/***********************************************************************************************************

							GETTING USER'S PROFILE PICTURE

***********************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT profile_picture, debit_wallet_usd, withdrawal_wallet_usd FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("profile_picture"), 3, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] == "profile_picture" ){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_profile_picture = $prepared_statement_results_array[0];
	$sys_debit_wallet_usd = floatval($prepared_statement_results_array[1]);
	$sys_withdrawal_wallet_usd = floatval($prepared_statement_results_array[2]);

	if(($sys_debit_wallet_usd + $sys_withdrawal_wallet_usd) < FISHPOTT_TRANSFER_FEE_IN_DOLLARS){
		$miscellaneousObject->respondFrontEnd3(3, "Insufficient wallet balance. Request Failed.");
	}

	if(FISHPOTT_TRANSFER_FEE_IN_DOLLARS <= $sys_debit_wallet_usd){
		$sys_new_debit_wallet_usd = $sys_debit_wallet_usd - FISHPOTT_TRANSFER_FEE_IN_DOLLARS;
		$sys_new_withdrawal_wallet_usd = $sys_withdrawal_wallet_usd;
	} else {
		$sys_total_wallet = $sys_debit_wallet_usd + $sys_withdrawal_wallet_usd;
		if($sys_total_wallet >= FISHPOTT_TRANSFER_FEE_IN_DOLLARS){
			$sys_total_selling_price_in_dollars = FISHPOTT_TRANSFER_FEE_IN_DOLLARS - $sys_debit_wallet_usd;
			$sys_new_debit_wallet_usd = 0;
			$sys_new_withdrawal_wallet_usd = $sys_withdrawal_wallet_usd - $sys_total_selling_price_in_dollars;
		} else {
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}
	}

	if(trim($sys_profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $sys_profile_picture) !== false){
		$sys_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $sys_profile_picture;
	} else {
		$sys_profile_picture = "";
	}


	//ADDING ADDED ITEM -- AS SHARES FOR SALE IF IT APPLIES
	if($input_added_item_id != "" && $input_added_item_quantity > 0){

		if($sys_dbpass != $validatorObject->hashString($input_myrawpass)){		
			$miscellaneousObject->respondFrontEnd3(5, $languagesObject->getLanguageString("incorrect_password", $input_language));
		}


		// MAKING SURE THE PERSON POSTING OWNS THE SHARES AND THE NUMBER HE IS SELLING
		$news_fetch_query =  "SELECT "  
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares, "
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".yield_date, "
		. SHARES_HOSTED_TABLE_NAME . ".type, "
		. SHARES_HOSTED_TABLE_NAME . ".share_name, "
		. SHARES_HOSTED_TABLE_NAME . ".company_pottname, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".cost_price_per_share, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".risk_protection, "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".total_yield FROM "
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
		. SHARES_HOSTED_TABLE_NAME . " ON  "  
		. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id="  
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id "
		. " WHERE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ? AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".flag = 0 AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0 AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares >= ?  AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id = ?";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 3, "sis", array($input_id, $input_added_item_quantity, $input_added_item_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("num_of_shares", "parent_shares_id", "start_date", "yield_date", "type", "share_name", "company_pottname", "cost_price_per_share", "risk_protection", "total_yield"), 10, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		// IF THE DATABASE QUERY GOT NO RESULTS
		if(intval($prepared_statement_results_array[0]) <= 0 || trim($prepared_statement_results_array[1]) == "" || trim($prepared_statement_results_array[2]) == "" || trim($prepared_statement_results_array[3]) == ""){
			$miscellaneousObject->respondFrontEnd3(6, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		$sys_db_num_of_shares = $prepared_statement_results_array[0];
		$sys_parent_shares_id = $prepared_statement_results_array[1];
		$sys_db_start_date = $prepared_statement_results_array[2];
		$sys_db_yield_date = $prepared_statement_results_array[3];
		$sys_db_type = $prepared_statement_results_array[4];
		$sys_db_share_name = $prepared_statement_results_array[5];
		if($input_receiver_pottname == "..."){
			$input_receiver_pottname = $prepared_statement_results_array[6];
			if($input_mypottname == $input_receiver_pottname){
					$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}
			$sys_shares_returned = true;
		} else {
			$sys_shares_returned = false;
		}

		$total_days_elapsed = $timeObject->getDaysPassed($sys_db_start_date, date("Y-m-d H:i:s"));
		if($total_days_elapsed < 7){
			$miscellaneousObject->respondFrontEnd3(9, $languagesObject->getLanguageString("request_failed", $input_language));
		}

		$sys_db_cost_price_per_share = $prepared_statement_results_array[7];
		$sys_db_risk_protection = $prepared_statement_results_array[8];
		$sys_db_total_yield = $prepared_statement_results_array[9];

		if($sys_db_total_yield <= 0){
			$sys_yield_for_shares_being_transferred = 0;
			$sys_news_yield_for_tranferrer = 0;
		} else {
			$sys_yield_for_shares_being_transferred = ($sys_db_total_yield / $sys_db_num_of_shares) * $input_added_item_quantity;
			$sys_news_yield_for_tranferrer = $sys_db_total_yield - $sys_yield_for_shares_being_transferred;
		}

		// CHECKING IF RECEIVER EXISTS AND IS FLAGGED OR NOT 
			$news_fetch_query =  "SELECT "  
			. USER_BIO_TABLE_NAME . ".pot_name, "
			. USER_BIO_TABLE_NAME . ".fcm_token, "
			. USER_BIO_TABLE_NAME . ".fcm_token_ios, "
			. USER_BIO_TABLE_NAME . ".profile_picture, "
			. USER_BIO_TABLE_NAME . ".investor_id FROM "
			. USER_BIO_TABLE_NAME . " INNER JOIN " 
			. LOGIN_TABLE_NAME . " ON  "  
			. USER_BIO_TABLE_NAME . ".investor_id="  
			. LOGIN_TABLE_NAME . ".id "
			. " WHERE " . USER_BIO_TABLE_NAME . ".pot_name = ? AND " . LOGIN_TABLE_NAME . ".flag = 0 ORDER BY " .  USER_BIO_TABLE_NAME . ".sku DESC";

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s", array($input_receiver_pottname));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(7, $languagesObject->getLanguageString("request_failed", $input_language));
			}

			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
				USER_BIO_TABLE_NAME . ".pot_name", 
				USER_BIO_TABLE_NAME . ".fcm_token", 
				USER_BIO_TABLE_NAME . ".fcm_token_ios",
				USER_BIO_TABLE_NAME . ".profile_picture",
				USER_BIO_TABLE_NAME . ".investor_id"
			), 5, 1);

			if($prepared_statement_results_array === false){
				$miscellaneousObject->respondFrontEnd3(7, $languagesObject->getLanguageString("request_failed", $input_language));
			}

			// IF THE DATABASE QUERY GOT NO RESULTS
			if(trim($prepared_statement_results_array[0]) == USER_BIO_TABLE_NAME . ".pot_name"){
				$miscellaneousObject->respondFrontEnd3(7, $languagesObject->getLanguageString("request_failed", $input_language));
			}

			$sys_receiver_fcm_token = $prepared_statement_results_array[1];
			$sys_receiver_fcm_token_ios = $prepared_statement_results_array[2];
			$sys_receiver_profile_picture = $prepared_statement_results_array[3];
			$sys_receiver_id = $prepared_statement_results_array[4];

			$receiver_keys = array();
			if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
				$receiver_keys[0] = $sys_receiver_fcm_token;
				//$receiver_keys[0] = "dRJcShgXTB4:APA91bEiUig4tUEGOFri-v8SeqhplsLD2GXIFv6XdkxYaBCLYoJpBMPgobxMdH1AQUT82M8n0bBdcXC6R_gb0HaUSDBHmDJTpYfOfMMb-5SfFajDUVHUosbAxmfH90TW2MXzCBU8w9S3";
			}
			if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
				$receiver_keys[count($receiver_keys)] = $sys_receiver_fcm_token_ios;
			}

			if(trim($sys_receiver_profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $sys_receiver_profile_picture) !== false){
				$sys_receiver_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $sys_receiver_profile_picture;
			} else {
				$sys_receiver_profile_picture = "";
			}

			$sys_sender_new_num_of_shares = $sys_db_num_of_shares - $input_added_item_quantity;

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET num_of_shares = ?, total_yield = ? WHERE owner_id = ? AND share_id = ?", 4, "idss", array($sys_sender_new_num_of_shares, $sys_news_yield_for_tranferrer, $input_id, $input_added_item_id));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
			}

			if($sys_shares_returned){
				$sys_this_share_id = $sys_parent_shares_id . "_" . $input_receiver_pottname;
			} else {
				$sys_this_share_id = $sys_parent_shares_id . "_" . $input_receiver_pottname . "_" . date("Y-m-d") . "_" . $sys_db_yield_date . "_r_" . strval($sys_db_risk_protection);
			}

			$news_fetch_query =  "SELECT "  
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares, "
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date, "
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".yield_date, "
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".risk_protection, "
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".total_yield FROM "
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
			. SHARES_HOSTED_TABLE_NAME . " ON  "  
			. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id="  
			. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id "
			. " WHERE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id = ? AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ? AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0";

			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 2, "ss", array($sys_this_share_id, $sys_receiver_id));

			$transfer_is_complete = false;

			if($prepared_statement !== false){
				$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
					"num_of_shares", 
					"start_date", 
					"yield_date", 
					"risk_protection", 
					"total_yield"
				), 5, 1);

				if($prepared_statement_results_array !== false){
					if($prepared_statement_results_array[1] == "start_date"){
							$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET debit_wallet_usd = ?, withdrawal_wallet_usd = ? WHERE investor_id = ? ", 3, "dds", array($sys_new_debit_wallet_usd, $sys_new_withdrawal_wallet_usd, $input_id));

							if($prepared_statement === false){
								$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
							} 
						$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " (share_id, shares_type, parent_shares_id, share_name, owner_id, cost_price_per_share, num_of_shares, start_date, yield_date, flag, risk_protection, total_yield) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)" , 11, "sssssdissid", array($sys_this_share_id, $sys_db_type, $sys_parent_shares_id, $sys_db_share_name, $sys_receiver_id, floatval($sys_db_cost_price_per_share), $input_added_item_quantity, $sys_db_start_date, $sys_db_yield_date, 0, $sys_yield_for_shares_being_transferred));

						if($prepared_statement !== false){
								$transfer_is_complete = true;
						}

					} else {
						// IF THE DATABASE QUERY GOT NO RESULTS
						$sys_yield_for_shares_being_transferred = $prepared_statement_results_array[4] + $sys_yield_for_shares_being_transferred;
						$sys_new_shares = intval($prepared_statement_results_array[0]) + $input_added_item_quantity;
						$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET debit_wallet_usd = ?, withdrawal_wallet_usd = ? WHERE investor_id = ? ", 3, "dds", array($sys_new_debit_wallet_usd, $sys_new_withdrawal_wallet_usd, $input_id));

						if($prepared_statement === false){
							$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
						} 
						$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET num_of_shares = ?, flag = 1, total_yield = ? WHERE share_id = ?  AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ? ", 4, "idss", array($sys_new_shares, $sys_yield_for_shares_being_transferred, $sys_this_share_id, $sys_receiver_id));
						if($prepared_statement !== false){
								$transfer_is_complete = true;
						} 
					}
				}
			}

			if($transfer_is_complete){

				$message = "\n SHARE NAME : " . $sys_db_share_name;
				$message = $message .  "\n PARENT SHARE ID : " . $sys_parent_shares_id;
				$message = $message .  "\n SHARE ID : " . $sys_this_share_id;
				$message = $message .  "\n SHARE NAME : " . $sys_db_share_name;
				$message = $message .  "\n TRANSFER QUANTITY : " . strval($input_added_item_quantity);
				$message = $message .  "\n DATE & TIME : " . date("Y-m-d H:i:s");

				// SETTING WALLET CHANGES IF SHARES WAS RETURNED
				if($sys_shares_returned){
					$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT withdrawal_wallet_usd FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_id));
					if($prepared_statement !== false){
						// GETTING RESULTS OF QUERY INTO AN ARRAY
						$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("withdrawal_wallet_usd"), 1, 1);
						if($prepared_statement_results_array !== false){
							$sys_withdrawal_wallet = floatval($sys_db_cost_price_per_share * RETURNING_SHARES_VALUE_REDUCTION_FACTOR) + floatval($prepared_statement_results_array[0]);
							$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET withdrawal_wallet_usd = ? WHERE investor_id = ?", 2, "ds", array($sys_withdrawal_wallet, $input_id));
							if($prepared_statement === false){
								$message = $message .  "\n WITHDRAWAL WALLET OLD AMOUNT : " . strval($prepared_statement_results_array[0]);
								$message = $message .  "\n WITHDRAWAL WALLET ADDITION AMOUNT : " . strval($sys_db_cost_price_per_share * RETURNING_SHARES_VALUE_REDUCTION_FACTOR);
								$message = $message .  "\n WITHDRAWAL WALLET NEW AMOUNT : " . strval($sys_withdrawal_wallet);
								$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF SENDER", $message);
							}
						} else {
							$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF SENDER", $message);
						}
					} else {
						$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF SENDER", $message);
					}

					//DEBIT THE WALLET OF THE COMPANY SELLING THE SHARES
					$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT withdrawal_wallet_usd FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($sys_receiver_id));
					if($prepared_statement !== false){
						// GETTING RESULTS OF QUERY INTO AN ARRAY
						$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("withdrawal_wallet_usd"), 1, 1);
						if($prepared_statement_results_array !== false){
							$sys_withdrawal_wallet = floatval($prepared_statement_results_array[0]) - floatval($sys_db_cost_price_per_share * RETURNING_SHARES_VALUE_REDUCTION_FACTOR);
							$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET withdrawal_wallet_usd = ? WHERE investor_id = ?", 2, "ds", array($sys_withdrawal_wallet, $sys_receiver_id));
							if($prepared_statement === false){
								$message = $message .  "\n WITHDRAWAL WALLET OLD AMOUNT : " . strval($prepared_statement_results_array[0]);
								$message = $message .  "\n WITHDRAWAL WALLET ADDITION AMOUNT : " . strval($sys_db_cost_price_per_share * RETURNING_SHARES_VALUE_REDUCTION_FACTOR);
								$message = $message .  "\n WITHDRAWAL WALLET NEW AMOUNT : " . strval($sys_withdrawal_wallet);
								$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF PARENT COMPANY OF PARENT COMPANY", $message);
							}
						} else {
							$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF PARENT COMPANY", $message);
						}
					} else {
						$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED UPDATE OF WITHDRAWAL WALLET OF PARENT COMPANY", $message);
					}
				}


				// RECORDING TRANSFER
				$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . SHARES_TRANSFER_TABLE_NAME . " (sender_id, receiver_id, shares_parent_id, share_id, shares_parent_name, num_shares_transfered, date_time, sender_old_quantity, receiver_share_id, transfer_fee, sender_old_deb_balance, sender_old_withdra_balance) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" , 12, "sssssisisddd", array($input_id, $sys_receiver_id, $sys_parent_shares_id, $input_added_item_id, $sys_db_share_name, $input_added_item_quantity, date("Y-m-d H:i:s"), $sys_db_num_of_shares, $sys_this_share_id, FISHPOTT_TRANSFER_FEE_IN_DOLLARS, $sys_debit_wallet_usd, $sys_withdrawal_wallet_usd));


				if($prepared_statement === false){
						$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED RECORD OF SHARES TRANSFER", $message);
				} 

				//NOTIFYING RECEIVER
				$miscellaneousObject->sendNotificationToUser(
					FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
					FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
					$receiver_keys, 
					$sys_profile_picture, 
					"normal", 
					"general_notification", 
					"shares_transfer", 
					$sys_parent_shares_id, 
					$input_mypottname, 
					$languagesObject->getLanguageString("shares_received", $input_language) . ". " . $languagesObject->getLanguageString("transaction_pending_verification", $input_language), 
					"@" . $input_mypottname . " " . $languagesObject->getLanguageString("has_sent_you", $input_language) . " " . strval($input_added_item_quantity) . " " . $sys_db_share_name . ". " . $languagesObject->getLanguageString("transaction_pending_verification", $input_language), 
					date("F j, Y"), 
					""
				);

				//SAVING NOTIFICATION TO NOTIFICATION TABLE

				$sysResponse["data_returned"][0]  = array(
					'1' => 1, 
					'2' => "",  
					'3' => $phone_verification_is_on, 
					'4' => CURRENT_HIGHEST_VERSION_CODE,
					'5' => FORCE_UPDATE_STATUS,
					'6' => UPDATE_DATE,
					'7' => $government_id_verification_is_on
					);

				$miscellaneousObject->sendEmail(SENDING_EMAIL_ANDROID, "FishPott Android App", RECEIVING_EMAIL_ANDROID, "SHARES TRANSFER CONFIRMATION NEEDEDED", "SHARES TRANSFER CONFIRMATION NEEDEDED");				

				 echo safe_json_encode($sysResponse);

				// CLOSE DATABASE CONNECTION
				if($prepared_statement !== false){
					$dbObject->closeDatabaseConnection($prepared_statement);
				}
				exit;
			} else {
					$message = "\n SHARE NAME : " . $sys_db_share_name;
					$message = $message .  "\n PARENT SHARE ID : " . $sys_parent_shares_id;
					$message = $message .  "\n SHARE ID : " . $sys_this_share_id;
					$message = $message .  "\n SHARE NAME : " . $sys_db_share_name;
					$message = $message .  "\n TRANSFER QUANTITY : " . strval($input_added_item_quantity);
					$message = $message .  "\n DATE & TIME : " . date("Y-m-d H:i:s");
					$miscellaneousObject->sendEmail("info@fishpott.com", "FishPott App", RECEIVING_EMAIL_ANDROID, "FAILED SHARES TRANSFER AFTER REMOVAL FROM SENDER", $message);
					$miscellaneousObject->respondFrontEnd3(8, $sys_this_share_id);
			}

	} else {
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

}


function safe_json_encode($value){
	if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
	    $encoded = json_encode($value, JSON_PRETTY_PRINT);
	} else {
	    $encoded = json_encode($value);
	}
	switch (json_last_error()) {
	    case JSON_ERROR_NONE:
	        return $encoded;
	    case JSON_ERROR_DEPTH:
	        return 'Maximum stack depth exceeded'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_STATE_MISMATCH:
	        return 'Underflow or the modes mismatch'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_CTRL_CHAR:
	        return 'Unexpected control character found';
	    case JSON_ERROR_SYNTAX:
	        return 'Syntax error, malformed JSON'; // or trigger_error() or throw new Exception()
	    case JSON_ERROR_UTF8:
	        $clean = utf8ize($value);
	        return safe_json_encode($clean);
	    default:
	        return 'Unknown error'; // or trigger_error() or throw new 
	Exception();
	}
}

function utf8ize($mixed) {
	if (is_array($mixed)) {
	    foreach ($mixed as $key => $value) {
	        $mixed[$key] = utf8ize($value);
	    }
	} else if (is_string ($mixed)) {
	    return utf8_encode($mixed);
	}
	return $mixed;
}
