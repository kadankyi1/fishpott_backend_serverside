<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_id_token"]) && trim($_POST["log_id_token"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_POST["linkup_id"]) && trim($_POST["linkup_id"]) != "" &&
	isset($_POST["language"]) && trim($_POST["language"]) != "" &&
	isset($_POST["app_version_code"]) && trim($_POST["app_version_code"]) != "" ) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	
	// SETTING DEVELOPMENT MODE IF NEED BE
	$GLOBALS["USAGE_MODE_IS_LIVE"] = true;
	if(isset($_POST["log_id_token"]) && trim($_POST["log_id_token"]) != "" && DEVELOPER_USING_LIVE_MODE !== true){
		$ALL_DEVELOPER_POTTNAMES = explode(",", DEVELOPER_USAGE_ID);
		if (in_array(trim($_POST["log_id_token"]), $ALL_DEVELOPER_POTTNAMES)){
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
	$input_linkup_id = trim($_POST["linkup_id"]);
	$session_id = trim($_POST["session_id"]);
	$input_language = trim($_POST["language"]);

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS
	$government_id_verification_is_on = false;



	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	//MAKING SURE THAT SOME INPUTS CONATINS NO TAGS
	if(	
		$validatorObject->stringContainsNoTags($input_id) !== true 
	){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, full_name, government_id_verified, request_government_id FROM " . LOGIN_TABLE_NAME . " WHERE id = ?", 1, "s", array($input_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "full_name", "government_id_verified", "request_government_id"), 6, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "password" || trim($prepared_statement_results_array[1]) == "number_verified"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
	if($prepared_statement_results_array[2] != 0){
		$miscellaneousObject->respondFrontEnd1("0", $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	}

	//CHECKING IF THE INPUT PASSWORD MATCHES THE DATABASE PASSWORD OTHERWISE WE FAIL THE REQUEST
	if($prepared_statement_results_array[0] != $input_pass){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("session_closed_restart_the_app_and_login_to_start_a_new_session", $input_language));
	}

	//CHECKING IF GOVERNMENT ID VERIFICATION IS REQUIRED
	if($prepared_statement_results_array[5] == 1 && $prepared_statement_results_array[4] == 0){
		$government_id_verification_is_on = true;
	}

	$sys_full_name =  $prepared_statement_results_array[3];

	// CHECKING IF PHONE VERIFICATION IS ON, AND WHEN CHECKING
	// IF USER ACCOUNT IS PENDING SMS VERIFICATION
	if($prepared_statement_results_array[1] == -1){
		$phone_verification_is_on = true;
	} else if($prepared_statement_results_array[1] == -1){
		$phone_verification_is_on = true;
	} else if($prepared_statement_results_array[1] == 0 && LOGIN_PHONE_NUMBER_VERIFICATION_IS_ON === true){
		$phone_verification_is_on = true;
		$reset_code = $miscellaneousObject->getRandomString(9);
/*****************************************************************************************************************
			

		SEND VERIFICATION SMS HERE. MAKE SURE THERE IS NO DATE IN THE DATABASE OR THE DATE IS PAST 24 HOURS

******************************************************************************************************************/
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LOGIN_TABLE_NAME . " SET number_verified = ?, number_verification_code = ?, last_sms_sent_datetime = ? WHERE number_login = ?", 4, "isss", array( -1, $reset_code, date("Y-m-d H:i:s"), $input_phone));
		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("request_failed", $input_language));
		}

	} else {
		$phone_verification_is_on = false;
	}

	//GETTING THE USER'S PROFILE PICTURE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT profile_picture, pot_name FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("profile_picture, pot_name"),2, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "profile_picture" || trim($prepared_statement_results_array[1]) == ""){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}

	$sys_user_profile_picture = trim($prepared_statement_results_array[0]);
	$sys_user_pottname = trim($prepared_statement_results_array[1]);

	if($sys_user_profile_picture != ""){
		$sys_user_profile_picture = "../../pic_upload/" . $sys_user_profile_picture;
		if($validatorObject->fileExists($sys_user_profile_picture) !== false){
			$sys_user_profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . trim($prepared_statement_results_array[0]);
		}
	} else {
		$sys_user_profile_picture =  FISHPOTT_APP_ICON_PICTURE_LINK;
	}


	//UPDATING THE LAST SEENN DATE
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET coins_secure_datetime = ? WHERE investor_id = ?", 2, "ss", array(date("Y-m-d H:i:s"), $input_id));

	//CHECKING IF A LINKUP ALREADY EXISTS
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT status FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$input_linkup_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("status"), 1, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("linkup_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "status"){
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "INSERT INTO " . LINKUPS_TABLE_NAME . " (sender_id,receiver_id,status,date_started) VALUES (?,?,?,?)" , 4, "ssis", array($input_id, $input_linkup_id, 1, date("Y-m-d H:i:s")));
				$action_performed = "2";
	} else {
		// CHECKING IF YOUR ACCOUNT IS SUSPENDED OR NOT
		if($prepared_statement_results_array[0] == 1){
			//IF A CONNECTION EXISTS, THEN WE ASSUME THE USER WANTS TO UN-LINK
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "DELETE FROM " . LINKUPS_TABLE_NAME . " WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$input_linkup_id));
				$action_performed = "0";
		} else {
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . LINKUPS_TABLE_NAME . " SET status = 1 WHERE (sender_id = ? AND receiver_id = ?)", 2, "ss", array($input_id,$input_linkup_id));
			$action_performed = "1";
		}
	}

	//SENDING THE LINKUP NOTIFICATION
	if($action_performed == "1" || $action_performed == "2"){

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT fcm_token, fcm_token_web, fcm_token_ios, language FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($input_linkup_id));

		// CHECKING THAT PREPARED STATEMENT WAS SUCCESSFUL
		if($prepared_statement !== false){

			// GETTING RESULTS OF QUERY INTO AN ARRAY
			$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("fcm_token", "fcm_token_web", "fcm_token_ios", "language"), 4, 1);


			if($prepared_statement_results_array !== false && isset($prepared_statement_results_array[0])){				
				if(trim($prepared_statement_results_array[0]) != "fcm_token"){

					$alert = $sys_full_name . " " .  $languagesObject->getLanguageString("has_linked_up_to_your_pot", $prepared_statement_results_array[3]);
					$receiver_android_key = $prepared_statement_results_array[0];
					$receiver_web_key = $prepared_statement_results_array[1];
					$receiver_ios_key = $prepared_statement_results_array[2];
					$receiver_keys = [$receiver_android_key, $receiver_web_key, $receiver_ios_key];

					$miscellaneousObject->sendNotificationToUser(FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, $receiver_keys, $sys_user_profile_picture, "normal", "general_notification", "linkup", "", $sys_user_pottname, "New Linkup", $alert, date("F j, Y"), "linkup");

				}
			}

		}

	}


	$sysResponse["data_returned"][0]  = array(
		'1' => 1, 
		'2' => $action_performed, 
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

?>