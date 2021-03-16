<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_id_token"]) && trim($_POST["log_id_token"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["mypottname"]) && trim($_POST["mypottname"]) != "" &&
	isset($_POST["my_currency"]) && trim($_POST["my_currency"]) != "" &&
	isset($_POST["session_id"]) &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE SESSION CLASS FILE
	include_once 'classes/session_class.php';
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

	
	//CALLING THE INPUT VALIDATOR CLASS
	include_once 'classes/input_validation_class.php';
	//CALLING THE MISCELLANOUS CLASS
	include_once 'classes/miscellaneous_class.php';
	//CALLING TO THE DATABASE CLASS
	include_once 'classes/db_class.php';
	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once 'classes/prepared_statement_class.php';
	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once 'classes/languages_class.php';
	//CALLING TO THE COUNTRY CODES CLASS
	include_once 'classes/country_codes_class.php';
	//CALLING TO THE SUPPORTED FILE CLASS
	include_once 'classes/file_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_id = trim($_POST["log_id_token"]);
	$input_pass = trim($_POST["log_pass_token"]);
	$input_mypottname = trim($_POST["mypottname"]);
	$input_my_currency = trim($_POST["my_currency"]);
	$session_id = trim($_POST["session_id"]);
	$input_language = trim($_POST["language"]);
	
	//DEFAULT GOVERNMENT ID VERIFICATION STATUS
	$government_id_verification_is_on = false;

	//DECLARING THE ARRAY FOR THE RESULTS
	$sysResponse["stories_returned"] = array();
	$GLOBALS['all_news_picked'] = array();

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$fileObject = new fileActions();
	
	// CREATING COUNTRY CODES OBJECT
	$countryCodesObject = new countryCodes();

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	// CREATING THE SESSION ACTIONS OBJECT
	$sessionObject = new sessionsActions();

	// STARTING A SESSION FOR THE USER
	if(!isset($session_id) || trim($session_id) == ""){
		$session_id = $miscellaneousObject->getRandomString(40);
	}

	// THE MAXIMUM TIME THE SESSION IS ALLOWED TO BE VALID
	//$max_allowed_session_time = MAXIMUM_SESSION_ALLOWED_TIME; // 3 DAYS
	$max_allowed_session_time = 5000;

	$sessionObject->startSession($session_id, $max_allowed_session_time);

	// CHECKING IF THE SESSION
	if($sessionObject->sessionIsNoLongerValid($max_allowed_session_time) === true){
		//$sessionObject->destroySession();
		$session_id = $miscellaneousObject->getRandomString(40);
		$sessionObject->startSession($session_id, $max_allowed_session_time);
	}

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if(	
		$validatorObject->stringContainsNoTags($input_id) !== true || $validatorObject->stringContainsNoTags($input_mypottname) !== true
	){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, government_id_verified, request_government_id FROM " . LOGIN_TABLE_NAME . " WHERE id = ?", 1, "s", array($input_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "government_id_verified", "request_government_id"), 5, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "password" || trim($prepared_statement_results_array[1]) == "number_verified"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
	if($prepared_statement_results_array[3] == 0 && $prepared_statement_results_array[4] == 1){
		$government_id_verification_is_on = true;
	}

	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){

		//$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	$sysResponse["data_returned"][0]  = array(
		'1' => 3
		);
	echo json_encode($sysResponse);

	}

	//CHECKING IF THE INPUT PASSWORD MATCHES THE DATABASE PASSWORD OTHERWISE WE FAIL THE REQUEST
	if($prepared_statement_results_array[0] != $input_pass){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("session_closed_restart_the_app_and_login_to_start_a_new_session", $input_language));
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
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
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
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("ip_usage_count"), 1, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}
		// IF THE DATABASE QUERY GOT NO RESULTS
		if($prepared_statement_results_array[0] <= 0){
			//INSERTING NEW IP ADDRESS USAGE
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . IP_ADDRESSES_TABLE_NAME . " (ip_id, investor_id, ip_address, ip_usage_count) VALUES (?, ?, ?, ?)" , 4, "sssi", array($ip_address_id, $input_id, $ip_address, 1));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
			}

		} else {
			// UPDATING IP USAGE COUNT
			$new_ip_count = $prepared_statement_results_array[0] + 1;
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . IP_ADDRESSES_TABLE_NAME . " SET ip_usage_count = ? WHERE ip_id = ?", 2, "is", array( $new_ip_count, $ip_address_id));
			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
			}
		}

	} else {
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

/***********************************************************************************************************

							GETTING CURRENCY EXCHANGE RATES

***********************************************************************************************************/

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}

	$GHS_USD = $prepared_statement_results_array[0];
	$USD_GHS = $prepared_statement_results_array[1];
	$GHS_GBP = $prepared_statement_results_array[2];
	$GBP_GHS = $prepared_statement_results_array[3];
	$USD_GBP = $prepared_statement_results_array[4];
	$GBP_USD = $prepared_statement_results_array[5];

	//UPDATING THE LAST SEEN DATE
	//$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));



/*****************************************************************************************************************
			
		FETCHING ALL CONTACTS OF USER TO USE FOR
		GETTING STORIES FROM PEOPLE ON THEIR CONTACT LIST

******************************************************************************************************************/

	$processed_all_contacts_numbers = $miscellaneousObject->getServerValueWithKey(USER_CONTACTS_SEPARATED_BY_VERTICAL_SLASH_FOR_REGEX_QUERY, "2");

	if($processed_all_contacts_numbers == ""){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT this_number FROM  " . PHONE_CONTACTS_TABLE_NAME . " WHERE owner_id = ?", 1, "s", array($input_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}


		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("this_number"), 1, 2);
		//BINDING THE RESULTS TO VARIABLES
		$prepared_statement_results_array->bind_result($this_number);

		// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
	    while($prepared_statement_results_array->fetch()){

	    	if(trim($this_number) != ""){
	    		$this_number = substr($this_number, 1);
		    	$processed_all_contacts_numbers .= trim($this_number);
		    	$processed_all_contacts_numbers .= "|" ;
	    	}

	    } // end of while
		$processed_all_contacts_numbers = substr($processed_all_contacts_numbers, 0, -1);

	}

/*****************************************************************************************************************
			
		FETCHING ALL IDS OF CONTACTS PICKED

******************************************************************************************************************/

	if($processed_all_contacts_numbers != ""){
		
		$query = "SELECT investor_id FROM " . USER_BIO_TABLE_NAME . " WHERE phone REGEXP(?)";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query, 1, "s", array($processed_all_contacts_numbers));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}


		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("investor_id"), 1, 2);
		//BINDING THE RESULTS TO VARIABLES
		$prepared_statement_results_array->bind_result($this_investor_id);

		// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
	    while($prepared_statement_results_array->fetch()){

	    	//&& $this_investor_id  != $input_id
	    	if($this_investor_id != ""){
	    		$this_investor_id = str_replace("+", "", $this_investor_id);
	    	$processed_all_contacts_numbers_investor_ids .= $this_investor_id;
		    	$processed_all_contacts_numbers_investor_ids .= "|" ;
	    	}

	    } // end of while
		$processed_all_contacts_numbers_investor_ids = substr($processed_all_contacts_numbers_investor_ids, 0, -1);


	}

/*****************************************************************************************************************
			
		FETCHING ALL NEWS OF CONTACTS THAT IS NOT MORE THAN 48 HOURS OLD

******************************************************************************************************************/
	if($processed_all_contacts_numbers_investor_ids != ""){

		$sysResponse = generateContent(1, $processed_all_contacts_numbers_investor_ids, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject);

	}

/*****************************************************************************************************************
			
	WE FETCH NEWS FROM LINKUPS WHILE MAKING SURE THERE IS NO REPEAT AND MAKE SURE THE 15 LIMIT IS NO REACHED

******************************************************************************************************************/

	if(count($sysResponse["stories_returned"]) < 15){

		$query = "SELECT receiver_id FROM " . LINKUPS_TABLE_NAME . " WHERE sender_id = ? ORDER BY linkup_interaction_rate DESC";

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query, 1, "s", array($processed_all_contacts_numbers_investor_ids));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}


		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("receiver_id"), 1, 2);

		//BINDING THE RESULTS TO VARIABLES
		$prepared_statement_results_array->bind_result($receiver_id);

		// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
	    while($prepared_statement_results_array->fetch()){
	    	$receiver_id = $prepared_statement_results_array[0];
	    	//&& $this_investor_id  != $input_id
	    	if($receiver_id != ""){
	    		$receiver_id = str_replace("+", "", $receiver_id);
	    		$processed_all_linkups_investor_ids .= $receiver_id;
		    	$processed_all_linkups_investor_ids .= "|" ;
	    	}

	    } // end of while

		$processed_all_linkups_investor_ids = substr($processed_all_linkups_investor_ids, 0, -1);


		if(trim($processed_all_linkups_investor_ids) != ""){

			$sysResponse = generateContent(1, $processed_all_linkups_investor_ids, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject);

	  	}

	}


/*****************************************************************************************************************
			
	WE FETCH NEWS GENERALLY WHILE MAKING SURE THERE IS NO REPEAT AND MAKE SURE THE 15 LIMIT IS NO REACHED

******************************************************************************************************************/

	if(count($sysResponse["stories_returned"]) < 15){

		$sysResponse = generateContent(2, "", $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject);

	}


	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => "1", 
		'3' => "sys_contacts_saved_to_session",  
		'4' => $session_id, 
		'5' => $phone_verification_is_on, 
		'6' => CURRENT_HIGHEST_VERSION_CODE,
		'7' => FORCE_UPDATE_STATUS,
		'8' => UPDATE_DATE,
		'9' => $government_id_verification_is_on

		);
	echo json_encode($sysResponse);


// CLOSE DATABASE CONNECTION
if($prepared_statement !== false){
	$dbObject->closeDatabaseConnection($prepared_statement);
}
 exit;
}

function generateContent($type, $processed_all_contacts_numbers_investor_ids, $preparedStatementObject, $miscellaneousObject, $dbObject, $sysResponse, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, $validatorObject){

	if($type == 1){
		$query = "SELECT news_id, type, news_image, news, news_video, inputtor_id, shares4sale_id FROM " . NEWS_TABLE_NAME . " WHERE flag = 0 AND  inputtor_id REGEXP(?) AND type = 'shares4sale' ORDER BY sku DESC LIMIT 50";
		//$query = "SELECT news_id, type, news_image, news, news_video, inputtor_id FROM " . NEWS_TABLE_NAME . " WHERE flag = 0 AND  inputtor_id REGEXP(?) AND (type = 'shares4sale' OR (type = 'news' AND (news_video != '' OR news_image != ''))) ORDER BY sku DESC LIMIT 50";
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query, 1, "s", array($processed_all_contacts_numbers_investor_ids));
	} else {

		$query = "SELECT news_id, type, news_image, news, news_video, inputtor_id, shares4sale_id FROM " . NEWS_TABLE_NAME . " WHERE flag = 0 AND type = 'shares4sale' ORDER BY sku DESC LIMIT 50";
		//$query = "SELECT news_id, type, news_image, news, news_video, inputtor_id FROM " . NEWS_TABLE_NAME . " WHERE flag = 0 AND (type = 'shares4sale' OR (type = 'news' AND (news_video != '' OR news_image != ''))) ORDER BY sku DESC LIMIT 50";
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), $query, 0, "", array());
	}

	//$query = "SELECT news_id, type, news_image, news, news_video, inputtor_id FROM " . NEWS_TABLE_NAME . " WHERE flag = 0 AND  inputtor_id REGEXP(?) ORDER BY sku DESC";


	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
	}


	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("news_id", "type", "news_image", "news", "news_video", "inputtor_id", "shares4sale_id"), 7, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($news_id, $type, $news_image, $news, $news_video, $inputtor_id, $shares4sale_id);

	// GETTING THE QUERY RESULTS INTO THE RESPONSE ARRAY
	$news_id = "";
	$inputtor_id = "";
	$pot_name = "";
	$profile_picture = "";
	$news_image = "";
	$news_video = "";
	$selling_price = "";
	$parent_shares_id = "";
	$news_item_name = "";

    while($prepared_statement_results_array->fetch()){
			if(count($sysResponse["stories_returned"]) >= 15){
				return $sysResponse;
			}
    		if(isset($GLOBALS['all_news_picked'][$shares4sale_id]) && $GLOBALS['all_news_picked'][$shares4sale_id] == 1){
    			continue;
    		}

    		/*
    		if($type == "event"){
    			continue;
    				$type_num = 3;

			        // GETTING THE EVENT INFO TO CHECK PRICE AND DATE IF PASSED
					$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT event_date, currency, ticket_cost, available_tics, num_of_goers  FROM " . EVENT_TABLE_NAME . " WHERE event_news_id = ? AND flag = 0", 1, "s", array($news_id));

					if($prepared_statement_2 === false){
						continue;
					}

					$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("event_date", "currency", "ticket_cost", "available_tics", "num_of_goers"), 5, 1);

					if($prepared_statement_results_array_2 === false){
						continue;
					}

					if(trim($prepared_statement_results_array_2[0]) == ""){
						continue;
					}

					$event_date = trim($prepared_statement_results_array_2[0]);
					$currency = trim($prepared_statement_results_array_2[1]);
					$selling_price = $prepared_statement_results_array_2[2];

					$available_tics = $prepared_statement_results_array_2[3];
					$num_of_goers = $prepared_statement_results_array_2[4];

					if(($available_tics - $num_of_goers) < 1){
						continue;
					}

    		} else

    		*/ if ($type == "shares4sale"){
    				$type_num = 1;

			        // GETTING THE SHARE4ALE INFO TO CHECK PRICE AND AMOUUNT IF SOLD
					$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT selling_price, currency, num_on_sale, number_sold, parent_shares_id FROM " . SHARES4SALE_TABLE_NAME . " WHERE sharesOnSale_id = ? AND flag = 0", 1, "s", array($shares4sale_id));

					if($prepared_statement_2 === false){
						continue;
					}

					$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("selling_price", "currency", "num_on_sale", "number_sold", "parent_shares_id"), 5, 1);

					if($prepared_statement_results_array_2 === false){
						continue;
					}
					
					if(trim($prepared_statement_results_array_2[0]) == ""){
						continue;
					}
					
					$selling_price = trim($prepared_statement_results_array_2[0]);
					$currency = trim($prepared_statement_results_array_2[1]);
					$num_on_sale = $prepared_statement_results_array_2[2];
					$number_sold = $prepared_statement_results_array_2[3];
					//$number_available = $prepared_statement_results_array_2[2] - $prepared_statement_results_array_2[3];
					$number_available = $prepared_statement_results_array_2[2];
					$parent_shares_id = $prepared_statement_results_array_2[4];

					if($number_available <= 0){
						continue;
					}
					
					$number_available = strval($number_available);

			        // GETTING THE SHARE4ALE INFO TO CHECK PRICE AND AMOUUNT IF SOLD
			        if(isset($GLOBALS[$parent_shares_id]) && trim($GLOBALS[$parent_shares_id]) != ""){
					
						$news_image = $GLOBALS[$parent_shares_id];

			        } else {
						$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT shares_logo, share_name FROM " . SHARES_HOSTED_TABLE_NAME . " WHERE parent_shares_id = ?", 1, "s", array($parent_shares_id));
					
						if($prepared_statement_2 === false){
							continue;
						}
					
						$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("parent_shares_id", "share_name"), 2, 1);
					
						if($prepared_statement_results_array_2 === false){
							continue;
						}
					
						if(trim($prepared_statement_results_array_2[0]) == ""){
							continue;
						}
					
						$news_image = $prepared_statement_results_array_2[0];
						$news_item_name = $prepared_statement_results_array_2[1];
						$GLOBALS[$parent_shares_id] = $prepared_statement_results_array_2[0];
			        }

    		}
    		/*
    		 else if ($type == "news"){
    				$type_num = 5;
    		}
    		*/
    		 else {
    			continue;
    		}

    		// CONVERTING THE CURRENCY TO USER'S CURRENCY
    		$selling_price = $miscellaneousObject->convertPriceToNewCurrency($currency, $selling_price, $input_my_currency, $GHS_USD, $USD_GHS, $GHS_GBP, $GBP_GHS, $USD_GBP, $GBP_USD, true);

    		if($selling_price <= 0){
    			continue;
    		}

    		$selling_price = $miscellaneousObject->getCurrencyForUIFromCurrency($input_my_currency) . $selling_price;

    		if(strlen($selling_price) > 7){
    			continue;
    		}

	        // GETTING THE INPUT PROFILE PICTURE AND POTTNAME
			$prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT pot_name, profile_picture FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($inputtor_id));

			if($prepared_statement_2 === false){
				continue;
			}

			$prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("pot_name", "profile_picture"), 2, 1);

			if($prepared_statement_results_array_2 === false){
				continue;
			}

			if(trim($prepared_statement_results_array_2[0]) == ""){
				continue;
			}

			$pot_name = trim($prepared_statement_results_array_2[0]);
			$profile_picture = trim($prepared_statement_results_array_2[1]);

			if(trim($profile_picture) != "" && $validatorObject->fileExists("../../pic_upload/" . $profile_picture) !== false){
				$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
			} else {
				$profile_picture = "";
			}

			if(trim($news_image) != "" && $validatorObject->fileExists("../../user/" . $news_image) !== false){
				$news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image;
			} else {
				$news_image = "";
			}

			if(trim($news_video) != "" && $validatorObject->fileExists("../../user/" . $news_video) !== false){
				$news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video;
			} else {
				$news_video = "";
			}

			$GLOBALS['all_news_picked'][$shares4sale_id] = 1;

			$next  = array(
				'1' => $type_num, 
				'2' => $news_id, 
				'3' => $inputtor_id,  
				'4' => $pot_name, 
				'5' => $profile_picture, 
				'6' => $news_image, 
				'7' => $news_video, 
				'8' => $selling_price, 
				'9' => $parent_shares_id, 
				'10' => $news_item_name, 
				'11' => $number_available, 
				'12' => $shares4sale_id
				);
	        array_push($sysResponse["stories_returned"], $next); 


    } // end of while

    return $sysResponse;


}


?>