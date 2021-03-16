<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_phone"]) && trim($_POST["log_phone"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["all_contacts_names"]) && 
	isset($_POST["all_contacts_numbers"]) && 
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

	//DECLARING THE ARRAYS AND STRINGS FOR THE QUERY
	$query_values_holder = array();
	$last_five_letters_values_holder = array();
	$input_all_contacts_numbers_array = array();
	$input_all_contacts_names_array = array();
	$query_values_regex = "";
	$query_valuestype_string = "";
	$query_questionmarks_string = " " . USER_BIO_TABLE_NAME .".phone IN( ";
	$query_regex_string = " " . USER_BIO_TABLE_NAME .".phone REGEXP(?)";
	$query_questionmarks_fetchedpotts_string = "";
	$last_phone = "";
	$first_phone = "";


	// GETTING ALL THE CONTACTS NAMES AND NUMBERS INTO AN ARRAY
	if (trim($_POST["all_contacts_numbers"]) != "") {
			$input_all_contacts_names_string = trim($_POST["all_contacts_names"]);
			$input_all_contacts_names_array = explode(" | ", $input_all_contacts_names_string);
			$input_all_contacts_numbers_string = trim($_POST["all_contacts_numbers"]);
			$input_all_contacts_numbers_array = explode(" | ", $input_all_contacts_numbers_string);
			$last_phone = $input_all_contacts_numbers_array[count($input_all_contacts_numbers_array) - 1];
			$first_phone = $input_all_contacts_numbers_array[0];
	}


	if(isset($_POST["all_contacts_numbers"]) && trim($_POST["all_contacts_numbers"]) != "" && count($input_all_contacts_numbers_array) > 1) {
		$contacts["processed_all_contacts_numbers"] = array();

		//MAKING SURE THEY ARE PHONE NUMBERS TO WORK WITH
		// CHECKING IF THE NUMBER BEGINS WITH A "+" OR NOT AND PREPARING IT FOR THE QUERY
		for ($i=0; $i < count($input_all_contacts_numbers_array); $i++) { 
			$current_number = $input_all_contacts_numbers_array[$i];
			$current_number = $validatorObject->removeAllCharactersAndLeaveNumbers($current_number);
            array_push($contacts["processed_all_contacts_numbers"], $current_number);    
			if($current_number === false){		
				continue;
			}
			$last_5_char = substr($current_number,strlen($current_number)-5,strlen($current_number));
			$last_five_letters_values_holder[$last_5_char] = $current_number;
			if(substr($current_number,0,1) != "+"){
				$next = substr($current_number,1,strlen($current_number));
				if(strlen($current_number) < 9 || strlen($current_number) > 15){

					continue;
				}
            	$query_values_regex = $query_values_regex . $next . "|" ;
			} else {
				$next = $current_number;
				if(strlen($current_number) < 9 || strlen($current_number) > 15){
					continue;
				}
            	$query_questionmarks_string = $query_questionmarks_string . "?,";
            	array_push($query_values_holder, $next);    
            	$query_valuestype_string = $query_valuestype_string . "s";
			}
		}

		$query_questionmarks_string = substr($query_questionmarks_string, 0, -1);
		$query_values_regex = substr($query_values_regex, 0, -1);
		$query_questionmarks_string = $query_questionmarks_string . ") ";

        if(trim($query_valuestype_string) == ""){
        	$query_questionmarks_string = "";
        	$or = "";
        } else {
        	$or = " OR ";
        }

        if($query_values_regex == ""){
        	$query_regex_string = "";
        	$or = "";
        } else {
	        array_push($query_values_holder, $query_values_regex);    
	        $query_valuestype_string = $query_valuestype_string . "s";
        }

		//var_dump($contacts["processed_all_contacts_numbers"]);

        if(trim($query_valuestype_string) != ""){
        	$query_values_holder[count($query_values_holder)] =  $input_id;
	        $query_valuestype_string = $query_valuestype_string . "s";

			$contacts_query =  'SELECT '
			. USER_BIO_TABLE_NAME . '.first_name,  '
			. USER_BIO_TABLE_NAME . '.last_name,  ' 
			. USER_BIO_TABLE_NAME . '.pot_name,  ' 
			. USER_BIO_TABLE_NAME . '.net_worth,  ' 
			. USER_BIO_TABLE_NAME . '.pott_value,  ' 
			. USER_BIO_TABLE_NAME . '.profile_picture,  '  
			. USER_BIO_TABLE_NAME . '.verified_tag,  ' 
			. USER_BIO_TABLE_NAME . '.investor_id,  ' 
			. USER_BIO_TABLE_NAME . '.investing_points, '  
			. USER_BIO_TABLE_NAME . '.total_amount_made_onfp FROM '  
			. USER_BIO_TABLE_NAME . ' INNER JOIN '
			. LOGIN_TABLE_NAME . ' ON  '  
			. USER_BIO_TABLE_NAME . '.investor_id='  
			. LOGIN_TABLE_NAME . '.id '
			. ' WHERE ' . $query_questionmarks_string . $or . $query_regex_string . ' AND ' . LOGIN_TABLE_NAME . '.flag = 0  AND ' . USER_BIO_TABLE_NAME . '.investor_id != ? LIMIT 12';

			$sysResponse = fetchlinkups($contacts_query, $query_valuestype_string, $query_values_holder, $input_id, $input_mypottname, $languagesObject, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $validatorObject, true);

		}

}

	// GETTING MORE LINKS IF THE NUMBER RETRIEVED FROM CONTACT LIST IS LOW
	if(count($sysResponse["news_returned"]) < 10){
			$linkups_query =  'SELECT '
			. USER_BIO_TABLE_NAME . '.first_name,  '
			. USER_BIO_TABLE_NAME . '.last_name,  ' 
			. USER_BIO_TABLE_NAME . '.pot_name,  ' 
			. USER_BIO_TABLE_NAME . '.net_worth,  ' 
			. USER_BIO_TABLE_NAME . '.pott_value,  ' 
			. USER_BIO_TABLE_NAME . '.profile_picture,  '  
			. USER_BIO_TABLE_NAME . '.verified_tag,  ' 
			. USER_BIO_TABLE_NAME . '.investor_id,  ' 
			. USER_BIO_TABLE_NAME . '.investing_points, '  
			. USER_BIO_TABLE_NAME . '.total_amount_made_onfp FROM '  
			. USER_BIO_TABLE_NAME . ' INNER JOIN '
			. LOGIN_TABLE_NAME . ' ON  '  
			. USER_BIO_TABLE_NAME . '.investor_id='  
			. LOGIN_TABLE_NAME . '.id '
			. ' WHERE ' . LOGIN_TABLE_NAME . '.flag = 0 AND ' . USER_BIO_TABLE_NAME . '.investor_id != ? ORDER BY RAND() LIMIT 12';

		$sysResponse = fetchlinkups($linkups_query, "s", array($input_id), $input_id, $input_mypottname, $languagesObject, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $validatorObject, false);
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

function fetchlinkups($query, $query_values_type_string, $query_values_array, $input_id, $input_mypottname, $languagesObject, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $validatorObject, $fetched_from_contacts) {

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query, count($query_values_array), $query_values_type_string, $query_values_array);

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd3(3, $languagesObject->getLanguageString("request_failed", $input_language));
	}
	
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".first_name", 
		USER_BIO_TABLE_NAME . ".last_name", 
		USER_BIO_TABLE_NAME . ".pot_name", 
		USER_BIO_TABLE_NAME . ".net_worth", 
		USER_BIO_TABLE_NAME . ".pott_value", 
		USER_BIO_TABLE_NAME . ".profile_picture", 
		USER_BIO_TABLE_NAME . ".verified_tag", 
		USER_BIO_TABLE_NAME . ".investor_id", 
		USER_BIO_TABLE_NAME . ".investing_points",
		USER_BIO_TABLE_NAME . ".total_amount_made_onfp"
	), 10, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($first_name, $last_name, $pot_name, $net_worth, $pott_value, $profile_picture, $verified_tag, $investor_id, $investing_points, $total_amount_made_onfp);

	while($prepared_statement_results_array->fetch()){

		// CHECKING IF YOU ARE ALREADY LINKED TO AN ACCOUNT
		$prepared_statement_3 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT status FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$investor_id));
		if($prepared_statement_3 === false){
			continue;
		}
		$prepared_statement_results_array_3 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_3, array("status"), 1, 1);

		if($prepared_statement_results_array_3 === false || $prepared_statement_results_array_3[0] == 1){
			continue;
		}

		$full_name = $first_name . " " . $last_name;
		if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
			$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
		} else {
			$profile_picture = "";
		}

		// LINKED FROM CONTACTS
		if($fetched_from_contacts){
			$fetch_reason = $languagesObject->getLanguageString("linked_from_your_contact_list", $input_language);
		}
		// POTT HAS A HIGH VALUE
		else if($pott_value >= POTT_VALUE_IS_HIGH_LEVEL_3){
			$fetch_reason = $languagesObject->getLanguageString("pott_has_a_high_value", $input_language);
		} 
		// AMOUNT MADE ON FISHPOTT
		else if($total_amount_made_onfp > TOTAL_AMOUNT_MADE_ON_FP_CONSIDERED_A_LOT){
		   $fetch_reason = $languagesObject->getLanguageString("makes_a_lot_of_income_on_from_their_fishpott", $input_language);
		} 
		// IF POTT IS VERIFIED
		else if($verified_tag  == 1){
		   $fetch_reason = $languagesObject->getLanguageString("this_pott_is_verified", $input_language);
		} 
		else if($verified_tag  == 2){
		   $fetch_reason = $languagesObject->getLanguageString("this_pott_is_popular", $input_language);
		} 
		// USING THE INVESTOR POINTS
		else if($investing_points > BABY_INVESTOR_UPPER_LIMIT && $investing_points <= TODDLER_INVESTOR_UPPER_LIMIT){
	    	$fetch_reason = $languagesObject->getLanguageString("a_toddler_investor", $input_language);
	    } else if($investing_points > TODDLER_INVESTOR_UPPER_LIMIT && $investing_points <= SWIFT_INVESTOR_UPPER_LIMIT){
	    	$fetch_reason = $languagesObject->getLanguageString("a_swift_investor", $input_language);
	    } else if($investing_points > SWIFT_INVESTOR_UPPER_LIMIT && $investing_points <= DEMI_GOD_INVESTOR_UPPER_LIMIT){
	    	$fetch_reason = $languagesObject->getLanguageString("a_demi_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
	    } else if($investing_points > DEMI_GOD_INVESTOR_UPPER_LIMIT && $investing_points <= GOD_INVESTOR_UPPER_LIMIT || $investing_points > GOD_INVESTOR_UPPER_LIMIT  ){
	    	$fetch_reason = $languagesObject->getLanguageString("a_god_investor_this_pott_makes_a_lot_of_shares_trades", $input_language);
	    } else if($net_worth > TOTAL_AMOUNT_OF_PEARLS_ON_FP_CONSIDERED_A_LOT){
			$fetch_reason = $languagesObject->getLanguageString("makes_a_lot_of_pearls", $input_language);
		} else {
			$fetch_reason = $languagesObject->getLanguageString("this_pot_has", $input_language) . " " . $net_worth . " " . $languagesObject->getLanguageString("pearls", $input_language);
	    }
		     
			$next  = array(				
				"0a" => $profile_picture, //int pottpic;
				"1" => $full_name, //int full_name;
				"2" => $pot_name, //String pot_name;
				"3" => $verified_tag, //String verified_tag;
				"4" => $fetch_reason, //String verified_tag;
				"5" => $investor_id //String fetch_reason;
				);
			array_push($sysResponse["news_returned"], $next);

	}

	return $sysResponse;



}
