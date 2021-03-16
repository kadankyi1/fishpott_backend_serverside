<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["phone"]) && trim($_POST["phone"]) != "" &&
	isset($_POST["password"]) && trim($_POST["password"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_POST["phone"]) && trim($_POST["phone"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_PHONES);
		if (in_array(trim($_POST["phone"]), $ALL_DEVELOPER_POTTNAMES)){
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

	//CALLING TO THE TIME OPERATOR CLASS
	include_once 'classes/time_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_phone = trim($_POST["phone"]);
	$input_password = trim($_POST["password"]);
	$input_language = trim($_POST["language"]);
	$input_app_version_code = intval($_POST["app_version_code"]);

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS
	$government_id_verification_is_on = false;

	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();

	$timeOperatorObject = new timeOperator();

	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($input_phone, 15) === false || $validatorObject->stringIsNotMoreThanMaxLength($input_password, 20) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($input_phone) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($input_phone,1,strlen($input_phone))) !== true){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($input_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT flag, password, login_type, id, number_verified, government_id_verified, request_government_id, number_verifcation_date, last_sms_sent_datetime, number_verification_code, media_poster FROM " . LOGIN_TABLE_NAME . " WHERE number_login = ?", 1, "s", array($input_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("flag", "password", "login_type", "id", "number_verified", "government_id_verified", "request_government_id", "number_verifcation_date", "last_sms_sent_datetime", "number_verification_code", "media_poster"), 11, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	$input_password_hashed = $validatorObject->hashString($input_password);

	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//CHECKING THAT THE PASSWORD MATCHES AND THE ACCOUNT IS NOT FLAGGED
	if($input_password_hashed == $prepared_statement_results_array[1] && $prepared_statement_results_array[0] == 0){

		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$db_hashed_pass = $prepared_statement_results_array[1];
		$db_user_type = $prepared_statement_results_array[2];
		$db_user_id = $prepared_statement_results_array[3];
		$db_numer_verify_status = intval($prepared_statement_results_array[4]);
		$db_num_verify_date = $prepared_statement_results_array[7];
		$db_num_verify_sms_sent_date = $prepared_statement_results_array[8];
		$db_num_verify_code = $prepared_statement_results_array[9];
		$db_media_allowed = $prepared_statement_results_array[10];

		$time_difference = $timeOperatorObject->getDateDifference($db_num_verify_sms_sent_date, date("Y-m-d H:i:s"));

		if($time_difference === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
		}

		if($time_difference["year_difference"] >= 1 || $time_difference["month_difference"] >= 1 || $time_difference["day_difference"] >= 1){
			$time_difference = 1;
		} else {
			$time_difference = 0;
		}

		$receiver_number = substr($input_phone,1,strlen($input_phone));

		//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
		if($prepared_statement_results_array[5] == 0 && $prepared_statement_results_array[6] == 1){
			$government_id_verification_is_on = true;
		}


		// IF USER ACCOUNT IS PENDING SMS VERIFICATION
		if($prepared_statement_results_array[4] == -1 || ($prepared_statement_results_array[4] != 1 && LOGIN_PHONE_NUMBER_VERIFICATION_IS_ON === true)){
			$phone_verification_is_on = true;
			$reset_code = $miscellaneousObject->getRandomString(9);
			if($time_difference == 1){
				$reset_text = "FishPott - " . $languagesObject->getLanguageString("your_number_verification_code_is", $input_language) . " " . $reset_code;
				$sms_response = $miscellaneousObject->sendSMS('sendsms', SMS_G_USERNAME, SMS_G_PASS, SENDER_NAME, $receiver_number, $reset_text);
				if($sms_response == 1){
					$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET number_verified = ?, number_verification_code = ?, last_sms_sent_datetime = ?  WHERE number_login = ?", 4, "isss", array( -1, $reset_code, date("Y-m-d H:i:s"), $input_phone));
				}
			}
		} else {
			$phone_verification_is_on = false;
		}

		//GETTING USER PROFILES DETAILS
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT first_name, last_name, dob, sex, country, verified_tag, profile_picture, currency, pot_name, nkurofuo_fetch_date FROM	" . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($db_user_id));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
		}

		// GETTING RESULTS OF QUERY INTO AN ARRAY
		$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("first_name", "last_name", "dob", "sex", "country", "verified_tag", "profile_picture", "currency", "pot_name", "nkurofuo_fetch_date"), 10, 1);

		if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("login_failed", $input_language));
		}

		// CLOSE DATABASE CONNECTION
		$dbObject->closeDatabaseConnection($prepared_statement);


		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$db_first_name = $prepared_statement_results_array[0];
		$db_last_name = $prepared_statement_results_array[1];
		$db_full_name = $db_first_name . " " . $db_last_name;
		$db_date_of_birth = $prepared_statement_results_array[2];
		$db_gender = $prepared_statement_results_array[3];
		$db_country = $prepared_statement_results_array[4];
		$db_verified_status = $prepared_statement_results_array[5];
		$db_profile_picture = "../../pic_upload/" . $prepared_statement_results_array[6];
		$db_currency = $prepared_statement_results_array[7];
		$db_pott_name = $prepared_statement_results_array[8];
		$db_last_version_code_contacts_fetch_date = $prepared_statement_results_array[9];

		if(trim($db_currency) == ""){
			if(strtolower($prepared_statement_results_array[4]) == "ghana"){
				$db_currency = "₵";
			} else if(strtolower($prepared_statement_results_array[4]) == "united kingdom"){
				$db_currency = "£";
			} else {
				$db_currency = "$";
			}
		}

		if($prepared_statement_results_array[6] != "" && $validatorObject->fileExists($db_profile_picture) !== false){
			$db_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $prepared_statement_results_array[6];
		} else {
			$db_profile_picture = "";
		}

		$signUpReturn["data_returned"][0]  = array(
			'status' => "yes", 
			'message' => "", 
			'phone_verification_is_on' => $phone_verification_is_on, 
			'user_phone' => $input_phone, 
			'user_id' => $db_user_id, 
			'user_pass' => $db_hashed_pass, 
			'user_pott_name' => $db_pott_name,
			'user_full_name' => $db_full_name,
			'user_profile_picture' => $db_profile_picture,
			'user_country' => $db_country,
			'user_verified_status' => $db_verified_status,
			'user_type' => $db_user_type,
			'user_gender' => $db_gender,
			'user_date_of_birth' => $db_date_of_birth,
			'user_currency' => $db_currency,
			'highest_version_code' => CURRENT_HIGHEST_VERSION_CODE,
			'force_update_status' => FORCE_UPDATE_STATUS,
			'update_date' => UPDATE_DATE,
			'media_allowed' => $db_media_allowed,
			'9' => $government_id_verification_is_on,
			'8' => MTN,
			'7' => VODAFONE,
			'10' => AIRTELTIGO,
			'11' => MTN_NAME,
			'12' => VODAFONE_NAME,
			'13' => AIRTELTIGO_NAME

			);
		echo json_encode($signUpReturn); exit;
	} else if($input_password_hashed != $prepared_statement_results_array[1]){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));
	} else if($prepared_statement_results_array[0] != 0){
			$miscellaneousObject->respondFrontEnd1("0", $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	} else {
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

/*
	while ($stmt->fetch()) {
     // Because $name and $countryCode are passed by reference, their value
     // changes on every iteration to reflect the current row
     echo "<pre>";
     echo "name: $name\n";
     echo "countryCode: $countryCode\n";
     echo "</pre>";
   }
*/


	
}