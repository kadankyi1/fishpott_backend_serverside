<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["first_name"]) && trim($_POST["first_name"]) != "" &&
	isset($_POST["last_name"]) && trim($_POST["last_name"]) != "" &&
	isset($_POST["gender"]) && trim($_POST["gender"]) != "" &&
	isset($_POST["dob"]) && trim($_POST["dob"]) != "" &&
	isset($_POST["country"]) && trim($_POST["country"]) != "" &&
	isset($_POST["pott_name"]) && trim($_POST["pott_name"]) != "" &&
	isset($_POST["referrer_pott_name"]) &&
	isset($_POST["phone_number"]) && trim($_POST["phone_number"]) != "" &&
	isset($_POST["password"]) && trim($_POST["password"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");

	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	
	if(isset($_POST["phone_number"]) && trim($_POST["phone_number"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_PHONES = explode(",", DEVELOPER_USAGE_PHONES);
		if (in_array(trim($_POST["phone_number"]), $ALL_DEVELOPER_PHONES)){
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


	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_first_name = trim($_POST["first_name"]);
	$input_last_name = trim($_POST["last_name"]);
	$input_gender = trim($_POST["gender"]);
	$input_dob = trim($_POST["dob"]);
	$input_country = trim($_POST["country"]);
	$input_pott_name = trim($_POST["pott_name"]);
	$input_pott_name = strtolower($input_pott_name);
	$input_referrer_pott_name = trim($_POST["referrer_pott_name"]);
	$input_referrer_pott_name = strtolower($input_referrer_pott_name);
	$input_phone_number = trim($_POST["phone_number"]);
	$input_password = trim($_POST["password"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();

	//MAKING SURE THE MAXLENGTH FOR INPUTS ARE MET
	if( 
		$validatorObject->stringIsNotMoreThanMaxLength($input_first_name, 15) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_last_name, 15) === false  || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_gender, 6) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_dob, 10) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_country, 45) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_pott_name, 15) === false || 
		($input_referrer_pott_name != "" && $validatorObject->stringIsNotMoreThanMaxLength($input_referrer_pott_name, 15) === false ) || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_phone_number, 15) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_password, 20) === false || 
		$validatorObject->stringIsNotMoreThanMaxLength($input_language, 3) === false
	){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	//MAKING SURE THE NO-HTML-TAGS RULE FOR INPUTS ARE MET
	if( 
		$validatorObject->stringContainsNoTags($input_first_name) !== true || 
		$validatorObject->stringContainsNoTags($input_last_name) !== true || 
		$validatorObject->stringContainsNoTags($input_gender) !== true || 
		$validatorObject->stringContainsNoTags($input_dob) !== true || 
		$validatorObject->stringContainsNoTags($input_country) !== true || 
		$validatorObject->stringContainsNoTags($input_pott_name) !== true || 
		($input_referrer_pott_name != "" && $validatorObject->stringContainsNoTags($input_referrer_pott_name, 15) !== true) || 
		$validatorObject->stringContainsNoTags($input_phone_number) !== true || 
		$validatorObject->stringContainsNoTags($input_language) !== true
		){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($input_phone_number,1,strlen($input_phone_number))) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed_your_phone_number_must_begin_with_your_country_code", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($input_phone_number,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed_your_phone_number_must_begin_with_your_country_code", $input_language));
	}

	//MAKING SURE POTTNAME CONTAINS ONLY LETTERS, NUMBERS AND UNDERSCORE
	$special_characters_array = array('_');
	if( $validatorObject->inputContainsOnlyAlphabetsWithUnderscore($input_pott_name, true, $special_characters_array) ===  false || 
		($input_referrer_pott_name != "" && $validatorObject->inputContainsOnlyAlphabetsWithUnderscore($input_referrer_pott_name, true, $special_characters_array) ===  false)
		){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed_check_the_pott_name_and_the_referrer_pott_name_pott_names_can_only_contain_alphabets_numbers_and_underscore", $input_language));
	}

	if($input_pott_name == "linkups" || $input_referrer_pott_name == "linkups"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("your_pott_name_or_the_referrer_pott_name_cannot_be_linkups_linkups_is_a_reserved_word", $input_language));
	}

	if(($input_gender != "male" && $input_gender != "female") || $validatorObject->inputContainsOnlyAlphabetsWithUnderscore($input_language, false, "") ===  false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT first_name FROM " . USER_BIO_TABLE_NAME . " WHERE phone = ?", 1, "s", array($input_phone_number));

	// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("first_name"), 1, 1);

	// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
	if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
		if(trim($prepared_statement_results_array[0]) != "" && trim($prepared_statement_results_array[0]) != "first_name"){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed_an_account_exists_with_the_phone_number", $input_language));
		}
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT first_name FROM " . USER_BIO_TABLE_NAME . " WHERE pot_name = ?", 1, "s", array($input_pott_name));

	// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("first_name"), 1, 1);

	// CHECKING IF THE REQUEST TO FIND AN ACCOUNT WITH THE PHONE NUMBER MADE A MATCH
	if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){
		if(trim($prepared_statement_results_array[0]) != "" && trim($prepared_statement_results_array[0]) != "first_name"){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed_the_pott_name_is_already_taken", $input_language));
		}
	}

	// HASHING THE PASSWORD
	$input_password_hashed = $validatorObject->hashString($input_password);
	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// GENERATING THE INVESTOR ID
	$sys_investor_id = $input_pott_name . substr($input_phone_number,1,strlen($input_phone_number)) . $miscellaneousObject->getRandomString(40);
	// GETTING USER CURRENCY FOR UI
	$sys_currency = $miscellaneousObject->getCurrencyForUIFromCountry($input_country);
	$sys_currency2 = $miscellaneousObject->getCurrencyForPaymentGatewaysFromCountry($input_country);
	// GETTING USER EMAIL
	$sys_investor_email = $input_phone_number . "@fishpott.com";
	// GETTING USER COIN SECURE DATE
	$sys_coin_secure_date = date("Y-m-d H:i:s");
	// GETTING USER SIGNUP DATE
	$sys_signup_date = date("Y-m-d");
	// USER FULL NAME
	$sys_full_name = $input_first_name . " " . $input_last_name;

	if(PHONE_NUMBER_VERIFICATION_IS_ON === true){
		$phone_verification_is_on = true;
		$verification_status = -1;
		$verification_code = $miscellaneousObject->getRandomString(9);
		$flag = 0;

	} else {
		$phone_verification_is_on = false;
		$verification_status = 0;
		$verification_code = "";
		$flag = 0;
	}


	//INSERTING THE NEW USER DATA INTO THE USER BIO TABLE DATABASE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . USER_BIO_TABLE_NAME . " (first_name, last_name, pot_name, dob, phone, investor_id, sex, country, language, currency, coins_secure_datetime, signup_date, referred_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)" , 13, "sssssssssssss", array($input_first_name, $input_last_name, $input_pott_name, $input_dob, $input_phone_number, $sys_investor_id, $input_gender, $input_country, $input_language, $sys_currency2, $sys_coin_secure_date, $sys_signup_date, $input_referrer_pott_name));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	//INSERTING THE NEW USER DATA INTO THE LOGIN TABLE DATABASE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . LOGIN_TABLE_NAME . " (id, number_login, email_login, password, login_type, full_name, flag, number_verified, number_verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)" , 9, "ssssssiis", array($sys_investor_id, $input_phone_number, $sys_investor_email, $input_password_hashed, "investor", $sys_full_name, $flag, $verification_status, $verification_code));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("registration_failed", $input_language));
	}

	// CREATING THE REFERRER'S POTT WITH PEARLS
	if($input_referrer_pott_name != "" && PHONE_NUMBER_VERIFICATION_IS_ON !== true){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT net_worth, fcm_token, fcm_token_web, fcm_token_ios, language FROM " . USER_BIO_TABLE_NAME . " WHERE pot_name = ?", 1, "s", array($input_referrer_pott_name));

		// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
		if($prepared_statement !== false){

			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("net_worth", "fcm_token", "fcm_token_web", "fcm_token_ios", "language"), 5, 1);


			if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){				
				if(trim($prepared_statement_results_array[0]) != "" && trim($prepared_statement_results_array[0]) != "net_worth"){

					$new_referrer_pott_pearls = intval($prepared_statement_results_array[0]) + REFERRAL_PEARLS;
					$alert = $languagesObject->getLanguageString("you_just_received_pearls_for_doing_a_referral_your_pearls_are_now", $prepared_statement_results_array[4]) . strval($new_referrer_pott_pearls);
					$alert_title = $languagesObject->getLanguageString("referral_bonus_received", $prepared_statement_results_array[4]);
					$receiver_android_key = $prepared_statement_results_array[1];
					$receiver_web_key = $prepared_statement_results_array[2];
					$receiver_ios_key = $prepared_statement_results_array[3];
					$receiver_keys = [$receiver_android_key, $receiver_web_key, $receiver_ios_key];

					// UPDATING THE REFERRER'S POTT PEARLS
					$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET net_worth = ? WHERE pot_name = ?", 2, "is", array($new_referrer_pott_pearls, $input_referrer_pott_name));

						if($prepared_statement !== false){

							$miscellaneousObject->sendNotificationToUser(FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, $receiver_keys, FISHPOTT_APP_ICON_PICTURE_LINK, "normal", "general_notification", "referral", $input_pott_name, $input_pott_name, $alert_title, $alert, date("F j, Y"), "");
						}

				}
			}

		}

	}


	if(PHONE_NUMBER_VERIFICATION_IS_ON === true){
		$receiver_number = substr($input_phone_number,1,strlen($input_phone_number));
		$phone_verification_is_on = true;
		$verification_status = -1;
		$verification_code = $miscellaneousObject->getRandomString(9);
		$flag = 0;
		$reset_text = "FishPott - " . $languagesObject->getLanguageString("your_number_verification_code_is", $input_language) . " " . $verification_code;
		$sms_response = $miscellaneousObject->sendSMS('sendsms', SMS_G_USERNAME, SMS_G_PASS, SENDER_NAME, $receiver_number, $reset_text);
		if($sms_response == 1){
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET number_verified = ?, number_verification_code = ?, last_sms_sent_datetime = ?  WHERE number_login = ?", 4, "isss", array( -1, $verification_code, date("Y-m-d H:i:s"), $input_phone_number));
		}

	} else {
		$phone_verification_is_on = false;
		$verification_status = 0;
		$verification_code = "";
		$flag = 0;
	}

		$signUpReturn["data_returned"][0]  = array(
			'status' => "yes", 
			'message' => "", 
			'phone_verification_is_on' => $phone_verification_is_on, 
			'user_phone' => $input_phone_number, 
			'user_id' => $sys_investor_id, 
			'user_pass' => $input_password_hashed, 
			'user_pott_name' => $input_pott_name,
			'user_full_name' => $sys_full_name,
			'user_country' => $input_country,
			'user_type' => "investor",
			'user_gender' => $input_gender,
			'user_date_of_birth' => $input_dob,
			'user_currency' => $sys_currency,
			'highest_version_code' => CURRENT_HIGHEST_VERSION_CODE,
			'force_update_status' => FORCE_UPDATE_STATUS,
			'update_date' => UPDATE_DATE,
			'8' => MTN,
			'9' => VODAFONE,
			'10' => AIRTELTIGO,
			'11' => MTN_NAME,
			'12' => VODAFONE_NAME,
			'13' => AIRTELTIGO_NAME

			);
		echo json_encode($signUpReturn); exit;

}