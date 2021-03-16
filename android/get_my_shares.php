<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
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
	$input_mypottname = trim($_POST["mypottname"]);
	$input_my_currency = trim($_POST["my_currency"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	//DECLARING THE ARRAY FOR THE RESULTS
	$sysResponse["news_returned"] = array();

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

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_mypottname) !== true){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("login_failed_if_this_continues_uninstall_your_app_reinstall_and_login_again", $input_language));
	}

	// GETTING THE CURRENCY SYMBOL OF THE USER
	$sys_currency_symbol = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency);

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

	$transfer_fee = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $miscellaneousObject->convertPriceToNewCurrency("USD", FISHPOTT_TRANSFER_FEE_IN_DOLLARS, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

	//UPDATING THE LAST SEEN DATE
	//$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));



	// GETTING THE NEWS CONTENT
	$news_fetch_query =  "SELECT "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_name,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".cost_price_per_share,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".yield_date,  " 
	. SHARES_HOSTED_TABLE_NAME . ".curr_max_price,  " 
	. SHARES_HOSTED_TABLE_NAME . ".yield_per_share, "
	. SHARES_HOSTED_TABLE_NAME . ".parent_company_name, "
	. SHARES_HOSTED_TABLE_NAME . ".yield_duration, "
	. SHARES_HOSTED_TABLE_NAME . ".value_per_share, "
	. SHARES_HOSTED_TABLE_NAME . ".type, "
	. SHARES_HOSTED_TABLE_NAME . ".last_dividend_pay_date FROM "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id="  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id "
	. " WHERE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".owner_id = ? AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".flag = 0 AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0  ORDER BY  " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".sku DESC";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($input_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".parent_shares_id", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_name", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".cost_price_per_share", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date", 
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".yield_date", 
		SHARES_HOSTED_TABLE_NAME . ".curr_max_price", 
		SHARES_HOSTED_TABLE_NAME . ".yield_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".parent_company_name", 
		SHARES_HOSTED_TABLE_NAME . ".yield_duration", 
		SHARES_HOSTED_TABLE_NAME . ".value_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".type", 
		SHARES_HOSTED_TABLE_NAME . ".last_dividend_pay_date"
	), 14, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($share_id, $parent_shares_id, $share_name, $cost_price_per_share, $num_of_shares, $start_date, $yield_date, $curr_max_price, $yield_per_share, $parent_company_name, $yield_duration, $value_per_share, $share_type, $last_dividend_pay_date);

	while($prepared_statement_results_array->fetch()){

		if($num_of_shares <= 0){
			continue;
		}

		$num_of_shares = strval($num_of_shares);

		$cost_price_per_share = floatval($cost_price_per_share);
		$curr_max_price = floatval($curr_max_price);
		$yield_per_share = floatval($yield_per_share);
		$yield_duration = strval($yield_duration);

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$cost_price_per_share_in_users_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $cost_price_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
		
		$curr_max_price_in_users_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $curr_max_price, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
		
		$yield_per_share_in_users_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $yield_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$value_per_share_in_users_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $value_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$yield_per_share_in_users_currency_total = $yield_per_share_in_users_currency * intval($num_of_shares);


		$yield_date = $timeObject->reformatDate("M j, Y", $yield_date);

		$sys_db_yield_date = $timeObject->getNewDateAfterNumberOfDays($last_dividend_pay_date, "+" . strval($yield_duration) . " day", "M j, Y");

/*
	    mine1.setSharesDividendPerShare(k.getString("7")); //Total of $14 dividends by 2 Sep, 2019. Dividends are paid every 365 days
	    mine1.setProfitOrLoss(k.getString("8")); // PROFIT MADE / LOSS MADE
*/
	    if($value_per_share_in_users_currency > $cost_price_per_share_in_users_currency){
	    	$value_statement = "1";
	    } else if($value_per_share_in_users_currency < $cost_price_per_share_in_users_currency){
	    	$value_statement = "-1";
	    } else {
	    	$value_statement = "0";
	    }

	    if($input_language == "zh"){
	    	if($share_type == "Treasury Bill"){
			    $sys_dividend_info = 
			    $languagesObject->getLanguageString("continuation_1_dividends_info", $input_language)
			    . $yield_date . " " 
			    . $languagesObject->getLanguageString("continuation_2_dividends_info", $input_language) 
			    . $sys_currency_symbol . $yield_per_share_in_users_currency_total . " " 
			    . $languagesObject->getLanguageString("continuation_3_dividends_info", $input_language) . " "
			    . $share_name . " " 
			    . $languagesObject->getLanguageString("pays_dividends_every", $input_language) . " " 
			    . $yield_duration . " " 
			    . $languagesObject->getLanguageString("days", $input_language);
	    	} else {
			    $sys_dividend_info = $parent_company_name . " paid " . $sys_currency_symbol . $yield_per_share_in_users_currency_total . " to shareholders owning " . $num_of_shares . " of its shares. The next payout is likely to be " . $sys_db_yield_date . ".";
	    	}
	    } else {
	    	if($share_type == "Treasury Bill"){
			    $sys_dividend_info = 
			    $sys_currency_symbol . $yield_per_share_in_users_currency_total . " " 
			    . $languagesObject->getLanguageString("will_be_paid_to_you_as_dividends_on", $input_language) . " " 
			    . $yield_date . ". " 
			    . $share_name . " " 
			    . $languagesObject->getLanguageString("pays_dividends_every", $input_language) . " " 
			    . $yield_duration . " " 
			    . $languagesObject->getLanguageString("days", $input_language);
			} else {
			    $sys_dividend_info = $parent_company_name . " paid " . $sys_currency_symbol . $yield_per_share_in_users_currency_total . " to shareholders owning " . $num_of_shares . " of its shares. The next payout is likely to be " . $sys_db_yield_date . ".";
			}
	    }

			$next  = array(				
				"0a" => $share_name, //int pottpic;
				"1" => $share_id, //int shares_logo;
				"2" => $parent_shares_id, //String added_item_short_note;
				"3" => $num_of_shares, //String added_item_selling_price;
				"4" => $cost_price_per_share_in_users_currency, //String num_on_sale_final;
				"5" => $curr_max_price_in_users_currency, //String shares_news_id;
				"6" => $yield_per_share_in_users_currency, //String shares_news_id;
				"7" => $sys_dividend_info, //String sys_dividend_info;
				"8" => $value_statement, //String sys_dividend_info;
				"9" => $value_per_share_in_users_currency //String sys_dividend_info;
				);
			array_push($sysResponse["news_returned"], $next);
	}

	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "",  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on,
		'8' => $transfer_fee
		);

	 //var_dump($json_response);
	 //echo "here 999 \n";
	 echo safe_json_encode($sysResponse);

	// CLOSE DATABASE CONNECTION
	if($prepared_statement !== false){
		$dbObject->closeDatabaseConnection($prepared_statement);
	}
	exit;
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
