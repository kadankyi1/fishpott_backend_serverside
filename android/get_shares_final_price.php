<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["share_id"]) && trim($_POST["share_id"]) != "" &&
	isset($_POST["risk_type"]) && trim($_POST["risk_type"]) != "" &&
	isset($_POST["buy_quantity"]) && trim($_POST["buy_quantity"]) != ""  && intval($_POST["buy_quantity"]) > 0 &&
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
	$input_share_id = trim($_POST["share_id"]);
	$input_risk_type = intval($_POST["risk_type"]);
	$input_buy_quantity = intval($_POST["buy_quantity"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	if($input_risk_type != 1 && $input_risk_type != 2 && $input_risk_type != 3){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}


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

	//UPDATING THE LAST SEEN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

	$news_fetch_query =  "SELECT "  
	. SHARES4SALE_TABLE_NAME . ".selling_price,  " 
	. SHARES4SALE_TABLE_NAME . ".currency,  " 
	. SHARES4SALE_TABLE_NAME . ".num_on_sale,  " 
	. SHARES4SALE_TABLE_NAME . ".number_sold,  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares,  " 
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date,  " 
	. SHARES_HOSTED_TABLE_NAME . ".curr_max_price,  " 
	. SHARES_HOSTED_TABLE_NAME . ".yield_per_share, "
	. SHARES_HOSTED_TABLE_NAME . ".yield_duration, "
	. SHARES_HOSTED_TABLE_NAME . ".type, "
	. SHARES_HOSTED_TABLE_NAME . ".share_name, "
	. SHARES_HOSTED_TABLE_NAME . ".last_dividend_pay_date, "
	. SHARES_HOSTED_TABLE_NAME . ".parent_company_name, "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".total_yield FROM "
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " INNER JOIN " 
	. SHARES4SALE_TABLE_NAME . " ON  "  
	. SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".share_id="  
	. SHARES4SALE_TABLE_NAME . ".sharesOnSale_id  INNER JOIN "
	. SHARES_HOSTED_TABLE_NAME . " ON  "  
	. SHARES_HOSTED_TABLE_NAME . ".parent_shares_id="  
	. SHARES4SALE_TABLE_NAME . ".parent_shares_id "
	. " WHERE " . SHARES4SALE_TABLE_NAME . ".flag = 0 AND " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".flag = 0 AND " . SHARES_HOSTED_TABLE_NAME . ".flag_not_tradable = 0 AND " .  SHARES4SALE_TABLE_NAME . ".sharesOnSale_id = ?";

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $news_fetch_query, 1, "s", array($input_share_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		SHARES4SALE_TABLE_NAME . ".selling_price", 
		SHARES4SALE_TABLE_NAME . ".currency", 
		SHARES4SALE_TABLE_NAME . ".num_on_sale",
		SHARES4SALE_TABLE_NAME . ".number_sold",
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".num_of_shares",
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".start_date",
		SHARES_HOSTED_TABLE_NAME . ".curr_max_price",
		SHARES_HOSTED_TABLE_NAME . ".yield_per_share",
		SHARES_HOSTED_TABLE_NAME . ".yield_duration",
		SHARES_HOSTED_TABLE_NAME . ".type",
		SHARES_HOSTED_TABLE_NAME . ".share_name",
		SHARES_HOSTED_TABLE_NAME . ".last_dividend_pay_date",
		SHARES_HOSTED_TABLE_NAME . ".parent_company_name",
		SHARES_OWNED_BY_INVESTOR_TABLE_NAME . ".total_yield"
	), 14, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[1]) == SHARES4SALE_TABLE_NAME . ".currency" || trim($prepared_statement_results_array[1]) == "currency"){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$sys_selling_price = floatval($prepared_statement_results_array[0]);
	$sys_selling_price_currency = $prepared_statement_results_array[1];
	$sys_selling_price_number_on_sale = intval($prepared_statement_results_array[2]);
	$sys_selling_price_number_sold = intval($prepared_statement_results_array[3]);
	$sys_seller_available_shares = intval($prepared_statement_results_array[4]);
	$sys_max_price_dollars = intval($prepared_statement_results_array[6]);
	$sys_yield_per_share_dollar = floatval($prepared_statement_results_array[7]);
	$sys_yield_duration = intval($prepared_statement_results_array[8]);
	$sys_investment_type = trim($prepared_statement_results_array[9]);
	$sys_share_name = trim($prepared_statement_results_array[10]);
	$sys_last_dividend_pay_date = trim($prepared_statement_results_array[11]);
	$sys_parent_company_name = trim($prepared_statement_results_array[12]);
	$sys_db_total_yield = trim($prepared_statement_results_array[13]);

	$sys_selling_quantity_available = $sys_selling_price_number_on_sale;

	if($sys_investment_type == "Treasury Bill"){

		if($sys_db_total_yield <= 0){
			$sys_yield_per_share_dollar = 0;
		} else {
			$sys_yield_per_share_dollar = ($sys_db_total_yield / $sys_seller_available_shares);
		}

	}

	//$sys_selling_quantity_available = $sys_selling_price_number_on_sale - $sys_selling_price_number_sold;

	if($input_buy_quantity <= 0 || $sys_selling_quantity_available < $input_buy_quantity){
		$miscellaneousObject->respondFrontEnd3(5, $sys_selling_quantity_available);
	}
	
	if(($sys_seller_available_shares < $input_buy_quantity) || ($sys_seller_available_shares < $sys_selling_quantity_available)){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("the_seller_does_not_have_enough_shares", $input_language));
	}


	$sys_yield_per_share_my_currency = $miscellaneousObject->convertPriceToNewCurrency("USD", $sys_yield_per_share_dollar * $input_buy_quantity, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

	$sys_selling_price_my_currency = $miscellaneousObject->convertPriceToNewCurrency($sys_selling_price_currency, $sys_selling_price, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);

	$sys_total_selling_price_my_currency = $sys_selling_price_my_currency * $input_buy_quantity;

	if($sys_investment_type == "Treasury Bill"){
		$sys_insurance_fee = 0;
		$total_payout = round(($sys_yield_per_share_my_currency * (1- FISHPOTT_YIELD_PROCESSING_PERCENTAGE))  + $sys_total_selling_price_my_currency,2);
		$sys_insurance_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . "0";
		$yield_per_share_info = "Total payout is " . $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $total_payout . " " . $languagesObject->getLanguageString("in", $input_language) . " " . strval($sys_yield_duration) . $languagesObject->getLanguageString("days", $input_language) . ". This total includes the amount paid, interest of " . $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_yield_per_share_my_currency . " and a deducted " . (FISHPOTT_YIELD_PROCESSING_PERCENTAGE*100) . "% yield processing fee on the interest";

		$sys_processing_fee = $miscellaneousObject->convertPriceToNewCurrency("USD", FISHPOTT_T_BILL_PURCHASE_PROCESSING_FEE_IN_DOLLARS, $sys_selling_price_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
		$sys_processing_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_processing_fee;
		$risk_info = "There is no risk making this investment";
	} else {
		if($input_risk_type == 1){
			$risk_info = $languagesObject->getLanguageString("risk_100_percent_statement", $input_language);
			$sys_insurance_fee = $sys_total_selling_price_my_currency * FULL_PROTECTION_INSURANCE_CHARGE_PERCENTAGE;
			$sys_insurance_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_insurance_fee;
		} else if($input_risk_type == 2){
			$risk_info = $languagesObject->getLanguageString("risk_50_percent_statement", $input_language);
			$sys_insurance_fee = $sys_total_selling_price_my_currency * HALF_PROTECTION_INSURANCE_CHARGE_PERCENTAGE;
			$sys_insurance_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_insurance_fee;
		} else {
			$risk_info = $languagesObject->getLanguageString("risk_no_percent_statement", $input_language);
			$sys_insurance_fee = 0;
			$sys_insurance_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . "0";
		}
		if($miscellaneousObject->convertPriceToNewCurrency($sys_selling_price_currency, $sys_total_selling_price_my_currency, "USD", $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false) < 100){

			$sys_processing_fee = $miscellaneousObject->convertPriceToNewCurrency("USD", FISHPOTT_STOCK_PURCHASE_PROCESSING_FEE_LESS_THAN_100_DOLLARS_IN_DOLLARS, $sys_selling_price_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, false);
			$sys_processing_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_processing_fee;
			
		} else {
			$sys_processing_fee =  $sys_total_selling_price_my_currency * FISHPOTT_STOCK_PURCHASE_PROCESSING_FEE_PERCENTAGE_OVER_100_DOLLARS;
			$sys_processing_fee_user_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_processing_fee;
		}

		$yield_per_share_info = $sys_parent_company_name . " paid " . $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_yield_per_share_my_currency .  " last time they paid dividends for owning the number of shares you are buying. Next payout is " . $timeObject->getNewDateAfterNumberOfDays($sys_last_dividend_pay_date, "+" . strval($sys_yield_duration) . " day", "M j, Y") . ". " . (FISHPOTT_YIELD_PROCESSING_PERCENTAGE*100) . "% yield processing fee on the dividend applies on yield.";

		 //$languagesObject->getLanguageString("you_make", $input_language) . " " . $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_yield_per_share_my_currency . $languagesObject->getLanguageString("every", $input_language) . " " . strval($sys_yield_duration) . $languagesObject->getLanguageString("days", $input_language);
	}
	
	$sys_selling_price_my_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_selling_price_my_currency;

	$sys_total_selling_price = $sys_total_selling_price_my_currency + $sys_insurance_fee + $sys_processing_fee;
	$sys_total_selling_price_my_currency = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $sys_total_selling_price;


	if($sys_selling_price_currency == "Ghc" || $sys_selling_price_currency == "GHS" || $sys_selling_price_currency == "₵"){
		if($input_my_currency == "Ghc" || $input_my_currency == "GHS" || $input_my_currency == "₵"){
			$rate_info = "₵1 = ₵1";
		} else if($input_my_currency == "GBP" || $input_my_currency == "£"){
			$rate_info = "£1 = ₵" . $GBP_GHS . " & ₵1 = £" . $GHS_GBP ;  
		}  else {
			$rate_info = "$1 = ₵" . $USD_GHS . " & ₵1 = $" . $GHS_USD ;  
		} 
	} else if($sys_selling_price_currency == "GBP" || $sys_selling_price_currency == "£"){
		if($input_my_currency == "Ghc" || $input_my_currency == "GHS" || $input_my_currency == "₵"){
			$rate_info = "£1 = ₵" . $GBP_GHS . " & ₵1 = £" . $GHS_GBP ;  
		} else if($input_my_currency == "GBP" || $input_my_currency == "£"){
			$rate_info = "£1 = £1";
		}  else {
			$rate_info = "$1 = £" . $USD_GBP . "& £1 = $" . $GBP_USD ;  
		} 
	} else {
		if($input_my_currency == "Ghc" || $input_my_currency == "GHS" || $input_my_currency == "₵"){
			$rate_info = "$1 = ₵" . $USD_GHS . " & ₵1 = $" . $GHS_USD ;  
		} else if($input_my_currency == "GBP" || $input_my_currency == "£"){
			$rate_info = "$1 = £" . $USD_GBP . "& £1 = $" . $GBP_USD ; 
		}  else {
			$rate_info = "$1 = $1";
		} 
	}
/*

    final String currPricePerItem = o.getString("8");
    final String currQuantity = o.getString("9");
    final String currRate = o.getString("10");
    final String currTotal = o.getString("11");
    final String currYieldInfo = o.getString("12");
    final String currRisk = o.getString("13");

*/
	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "",  
		'3' => $phone_verification_is_on, 
		'4' => CURRENT_HIGHEST_VERSION_CODE,
		'5' => FORCE_UPDATE_STATUS,
		'6' => UPDATE_DATE,
		'7' => $government_id_verification_is_on,
		'8' => $sys_selling_price_my_currency,
		'9' => $input_buy_quantity,
		'10' => $rate_info,
		'11' => $sys_total_selling_price_my_currency,
		'12' => $yield_per_share_info,
		'13' => $risk_info,
		'14' => $sys_processing_fee_user_currency,
		'15' => $sys_insurance_fee_user_currency
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
