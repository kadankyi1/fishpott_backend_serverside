<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["parentsharesid"]) && trim($_POST["parentsharesid"]) != "" &&
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
	$input_parentsharesid = trim($_POST["parentsharesid"]);
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

	//UPDATING THE LAST SEEN DATE
	//$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

		// GETTING THE NEWS CONTENT
		$news_fetch_query =  "SELECT "  
		. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id,  " 
		. SHARES_HOSTED_TABLE_NAME . ".share_name,  " 
		. SHARES_HOSTED_TABLE_NAME . ".shares_logo,  " 
		. SHARES_HOSTED_TABLE_NAME . ".value_per_share,  " 
		. SHARES_HOSTED_TABLE_NAME . ".yield_duration,  " 
		. SHARES_HOSTED_TABLE_NAME . ".yield_per_share,  " 
		. SHARES_HOSTED_TABLE_NAME . ".company_pottname,  " 
		. SHARES_HOSTED_TABLE_NAME . ".parent_company_name,  " 
		. SHARES_HOSTED_TABLE_NAME . ".company_networth,  " 
		. SHARES_HOSTED_TABLE_NAME . ".country_origin,  "  
		. SHARES_HOSTED_TABLE_NAME . ".company_avg_profit_per_year,  "  
		. SHARES_HOSTED_TABLE_NAME . ".percent_of_all_comp_shares_hosted,  "   
		. SHARES_HOSTED_TABLE_NAME . ".total_number,  " 
		. SHARES_HOSTED_TABLE_NAME . ".c_e_o_name,  " 
		. SHARES_HOSTED_TABLE_NAME . ".type,  " 
		. SHARES_HOSTED_TABLE_NAME . ".share_description FROM " 
		. SHARES_HOSTED_TABLE_NAME . " WHERE " . SHARES_HOSTED_TABLE_NAME . ".parent_shares_id = ? AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0  ORDER BY  " . SHARES_HOSTED_TABLE_NAME . ".sku DESC";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($input_parentsharesid));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		SHARES_HOSTED_TABLE_NAME . ".parent_shares_id", 
		SHARES_HOSTED_TABLE_NAME . ".share_name", 
		SHARES_HOSTED_TABLE_NAME . ".shares_logo", 
		SHARES_HOSTED_TABLE_NAME . ".value_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".yield_duration", 
		SHARES_HOSTED_TABLE_NAME . ".yield_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".company_pottname", 
		SHARES_HOSTED_TABLE_NAME . ".parent_company_name", 
		SHARES_HOSTED_TABLE_NAME . ".company_networth", 
		SHARES_HOSTED_TABLE_NAME . ".country_origin", 
		SHARES_HOSTED_TABLE_NAME . ".company_avg_profit_per_year", 
		SHARES_HOSTED_TABLE_NAME . ".percent_of_all_comp_shares_hosted", 
		SHARES_HOSTED_TABLE_NAME . ".total_number", 
		SHARES_HOSTED_TABLE_NAME . ".c_e_o_name", 
		SHARES_HOSTED_TABLE_NAME . ".type", 
		SHARES_HOSTED_TABLE_NAME . ".share_description"
	), 15, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($parent_shares_id, $share_name, $shares_logo, $value_per_share, $yield_duration, $yield_per_share, $company_pottname, $parent_company_name, $company_networth, $country_origin, $company_avg_profit_per_year, $percent_of_all_comp_shares_hosted, $total_number, $c_e_o_name, $share_type, $share_description);

	
	$prepared_statement_results_array->fetch();

		if(trim($shares_logo) != "" && $validatorObject->fileExists("../../user/" . $shares_logo) !== false){
			$shares_logo = HTTP_HEAD . "://fishpott.com/user/" . $shares_logo;
		} else {
			$shares_logo = "";
		}

		$value_per_share = floatval($value_per_share);
		$yield_per_share = floatval($yield_per_share);
		$company_networth = floatval($company_networth);
		$company_avg_profit_per_year = floatval($company_avg_profit_per_year);
		$total_number = intval($total_number);

		// CONVERTING THE CURRENCY TO USER'S CURRENCY
		$value_per_share_in_users_currency = $sys_currency_symbol .  $miscellaneousObject->convertPriceToNewCurrency("USD", $value_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, true);
		
		$yield_per_share_in_users_currency = $sys_currency_symbol . $miscellaneousObject->convertPriceToNewCurrency("USD", $yield_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
		
		$company_networth = $miscellaneousObject->convertPriceToNewCurrency("USD", $company_networth, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$company_networth_in_users_currency = $sys_currency_symbol . $miscellaneousObject->number_format_short($company_networth, 1);

		$sys_country_origin = $languagesObject->getLanguageString("originating_from", $input_language) . " " . $country_origin;
		
		$parent_company_name = $languagesObject->getLanguageString("offered_by", $input_language) . " " . $parent_company_name;

		$company_avg_profit_per_year = $miscellaneousObject->convertPriceToNewCurrency("USD", $company_avg_profit_per_year, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$company_avg_profit_per_year_in_users_currency = $sys_currency_symbol . $miscellaneousObject->number_format_short($company_avg_profit_per_year, 1);
		
		$company_avg_profit_per_year_in_users_currency_text = $company_avg_profit_per_year_in_users_currency  . " " . $languagesObject->getLanguageString("average_profit_per_year", $input_language);

		$total_number_shares = $miscellaneousObject->number_format_short($total_number, 1);

		if(strtolower($share_type) == "treasury bill"){
			$total_number_text = $total_number_shares . "  " . $languagesObject->getLanguageString("hosted_treasury_bills", $input_language);
		} else {
			$total_number_text = $total_number_shares . "  " . $languagesObject->getLanguageString("hosted_stock", $input_language) . " ( " . $percent_of_all_comp_shares_hosted . "% " . $languagesObject->getLanguageString("of_company", $input_language) . " )";
		}


		$ceo_text = $languagesObject->getLanguageString("ceo", $input_language) . " " . $c_e_o_name;

			$next  = array(				
				"0a" => $parent_shares_id,
				"1" => $share_name,
				"2" => $shares_logo,
				"3" => $value_per_share_in_users_currency, 
				"4" => $yield_per_share_in_users_currency,
				"5" => $parent_company_name, 
				"6" => $company_pottname, 
				"7" => $company_networth_in_users_currency, 
				"8" => $sys_country_origin, 
				"9" => $company_avg_profit_per_year_in_users_currency_text, 
				"10" => $total_number_text, 
				"11" => $ceo_text, 
				"12" => $share_description
				);
			array_push($sysResponse["news_returned"], $next);


		// GETTING SHARES VALUE HISTORY UP TO 30 INPUTS AGO
		$news_fetch_query =  "SELECT "  
		. SHARES_VALUE_HISTORY_TABLE_NAME . ".value_record_date,  " 
		. SHARES_VALUE_HISTORY_TABLE_NAME . ".value_per_share,  " 
		. SHARES_VALUE_HISTORY_TABLE_NAME . ".dividend_per_share,  " 
		. SHARES_VALUE_HISTORY_TABLE_NAME . ".investors_now FROM " 
		. SHARES_VALUE_HISTORY_TABLE_NAME . " WHERE " . SHARES_VALUE_HISTORY_TABLE_NAME . ".parent_id = ?  ORDER BY  " . SHARES_VALUE_HISTORY_TABLE_NAME . ".sku DESC LIMIT 40";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s",array($input_parentsharesid));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		SHARES_HOSTED_TABLE_NAME . ".value_record_date", 
		SHARES_HOSTED_TABLE_NAME . ".value_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".dividend_per_share", 
		SHARES_HOSTED_TABLE_NAME . ".investors_now"
	), 4, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($value_record_date, $value_per_share, $dividend_per_share, $investors_now);

	while($prepared_statement_results_array->fetch()){

		$value_per_share = floatval($value_per_share);
		$dividend_per_share = floatval($dividend_per_share);

		$value_per_share_in_users_currency = $sys_currency_symbol .  $miscellaneousObject->convertPriceToNewCurrency("USD", $value_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, true);
		
		$yield_per_share_in_users_currency = $sys_currency_symbol . $miscellaneousObject->convertPriceToNewCurrency("USD", $dividend_per_share, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

		$investors_now = $miscellaneousObject->number_format_short($investors_now, 1);

		$value_record_date = $timeObject->getTimeElapsedSstring($value_record_date, false);

			$next  = array(				
				"0a" => $value_record_date,
				"1" => $value_per_share_in_users_currency,
				"2" => $yield_per_share_in_users_currency,
				"3" => $investors_now
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
		'7' => $government_id_verification_is_on
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
