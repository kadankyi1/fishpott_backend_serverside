<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["victim_pottname"]) && trim($_POST["victim_pottname"]) != "" &&
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
	$input_victim_pottname = trim($_POST["victim_pottname"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);


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

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// GETTING THE NEWS CONTENT
	$news_fetch_query =  "SELECT "  
	. USER_BIO_TABLE_NAME . ".fcm_token,  "  
	. USER_BIO_TABLE_NAME . ".fcm_token_ios,  "  
	. USER_BIO_TABLE_NAME . ".net_worth,  "  
	. USER_BIO_TABLE_NAME . ".language,  "  
	. USER_BIO_TABLE_NAME . ".country,  "  
	. USER_BIO_TABLE_NAME . ".investor_id,  "  
	. USER_BIO_TABLE_NAME . ".shield_date FROM "
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id "
	. " WHERE " . LOGIN_TABLE_NAME . ".flag = 0 AND " . USER_BIO_TABLE_NAME . ".pot_name = ?";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($input_victim_pottname));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".fcm_token", 
		USER_BIO_TABLE_NAME . ".fcm_token_ios",
		USER_BIO_TABLE_NAME . ".net_worth",
		USER_BIO_TABLE_NAME . ".language",
		USER_BIO_TABLE_NAME . ".country",
		USER_BIO_TABLE_NAME . ".investor_id",
		USER_BIO_TABLE_NAME . ".shield_date"
	), 7, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($fcm_token, $fcm_token_ios, $sys_victim_pearls, $language, $country, $investor_id, $shield_date);

	$prepared_statement_results_array->fetch();

	$sys_victim_id = $investor_id;

	if($sys_victim_id == ""){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("pott_not_found", $input_language));
	}

	if(trim($language) == ""){
		$language = $country;
	}

	$sys_victim_pearls = intval($sys_victim_pearls);

	if($sys_victim_pearls <= 0){
		$sys_shield_cost = 20;
	} else {
		$sys_shield_cost = intval($sys_victim_pearls/4);
	}


	$sys_receiver_fcm_token = $fcm_token;
	$sys_receiver_fcm_token_ios = $fcm_token_ios;

	$receiver_keys = array();
	if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
		$receiver_keys[0] = $sys_receiver_fcm_token;
	}

	if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
		$receiver_keys[count($receiver_keys)] = $sys_receiver_fcm_token_ios;
	}

/******************************************************************************************************************************************************/


/******************************************************************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT debit_wallet_usd, withdrawal_wallet_usd, fcm_token, fcm_token_ios, net_worth FROM " . USER_BIO_TABLE_NAME . " WHERE pot_name = ?", 1, "s", array($input_mypottname));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("debit_wallet_usd", "withdrawal_wallet_usd", "fcm_token", "fcm_token_ios", "net_worth"), 5, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if(    trim($prepared_statement_results_array[2]) == "fcm_token"
		|| trim($prepared_statement_results_array[3]) == "fcm_token_ios"
	){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_debit_wallet_usd = floatval($prepared_statement_results_array[0]);
	$sys_withdrawal_wallet_usd = floatval($prepared_statement_results_array[1]);
	$sys_pott_pearls = intval($prepared_statement_results_array[4]);
	$sys_receiver_fcm_token = $prepared_statement_results_array[2];
	$sys_receiver_fcm_token_ios = $prepared_statement_results_array[3];

	$receiver_keys2 = array();
	if(trim($sys_receiver_fcm_token) != "fcm_token" && trim($sys_receiver_fcm_token) != ""){
		$receiver_keys2[0] = $sys_receiver_fcm_token;
	}

	if(trim($sys_receiver_fcm_token_ios) != "fcm_token_ios" && trim($sys_receiver_fcm_token_ios) != ""){
		$receiver_keys2[count($receiver_keys2)] = $sys_receiver_fcm_token_ios;
	}

	if($sys_pott_pearls >= $sys_shield_cost){
		$sys_new_buyer_pearls = $sys_pott_pearls - $sys_shield_cost;
		$sys_shield_days_to_be_added = "+2 day";
		$sys_new_withdrawal_wallet_usd = $sys_withdrawal_wallet_usd;
		$sys_new_debit_wallet_usd = $sys_debit_wallet_usd;
		$pearls_used = 1;
	} else {
		$sys_new_buyer_pearls = $sys_pott_pearls;
		if($sys_debit_wallet_usd >= 1){
			$sys_new_debit_wallet_usd = $sys_debit_wallet_usd - 1;
			$sys_new_withdrawal_wallet_usd = $sys_withdrawal_wallet_usd;
		} else if($sys_withdrawal_wallet_usd >= 1){
			$sys_new_withdrawal_wallet_usd = $sys_withdrawal_wallet_usd - 1;
			$sys_new_debit_wallet_usd = $sys_debit_wallet_usd;
		} else {
			$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("insufficient_funds", $input_language));
		}
		$sys_shield_days_to_be_added = "+7 day";
		$sys_shield_cost = "$1";
		$pearls_used = 0;
	}

	if($shield_date == "0000-00-00"){
		$sys_new_shield_date = $timeObject->getNewDateAfterNumberOfDays(date('Y-m-d'), $sys_shield_days_to_be_added, "Y-m-d");
	} else {
		if(intval($timeObject->getDaysPassed($shield_date, date("Y-m-d"))) <= 0){
			$sys_new_shield_date = $timeObject->getNewDateAfterNumberOfDays($shield_date, $sys_shield_days_to_be_added, "Y-m-d");
		} else {
			$sys_new_shield_date = $timeObject->getNewDateAfterNumberOfDays(date('Y-m-d'), $sys_shield_days_to_be_added, "Y-m-d");
		}
	}


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET debit_wallet_usd = ?, withdrawal_wallet_usd = ?, net_worth = ? WHERE pot_name = ? ", 4, "ddis", array($sys_new_debit_wallet_usd, $sys_new_withdrawal_wallet_usd, $sys_new_buyer_pearls, $input_mypottname));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	} 

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET shield_date = ?, poach_ruled_by = '' WHERE pot_name = ?", 2, "ss", array($sys_new_shield_date, $input_victim_pottname));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}
		$sys_formatted_new_shield_date = date_create($sys_new_shield_date);
		$sys_status_info = $languagesObject->getLanguageString("pott_shielded_until", $language) . " " . date_format($sys_formatted_new_shield_date,"M j, y");
		$sys_status_info2 = $languagesObject->getLanguageString("pott_shielded", $language);

		$miscellaneousObject->sendNotificationToUser(
			FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
			FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
			$receiver_keys, 
			"fp", 
			"normal", 
			"general_notification", 
			"shield", 
			$input_id, 
			$input_mypottname, 
			$sys_status_info2, 
			$sys_status_info, 
			date("F j, Y"), 
			""
		);

	if($pearls_used == 1){
		$sys_return_info = $languagesObject->getLanguageString("pott_shielded_until", $input_language) . " " . date_format($sys_formatted_new_shield_date,"M j, y") . ". " . $sys_shield_cost . " " . $languagesObject->getLanguageString("pearls_used_for_shield", $input_language);
	} else {
		$sys_return_info = $languagesObject->getLanguageString("pott_shielded_until", $input_language) . " " . date_format($sys_formatted_new_shield_date,"M j, y") . ". " . $sys_shield_cost . " " . $languagesObject->getLanguageString("used_for_shield", $input_language);
	}


	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => $sys_return_info,  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on,
		'8' => $sys_new_buyer_pearls,
		'9' => $sys_new_debit_wallet_usd,
		'10' => $sys_new_withdrawal_wallet_usd
		);
	
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
