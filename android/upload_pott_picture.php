<?php
// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( $_SERVER["REQUEST_METHOD"] == "POST" &&
	isset($_POST["log_id_token"]) && trim($_POST["log_id_token"]) != "" &&
	isset($_POST["log_pass_token"]) && trim($_POST["log_pass_token"]) != "" &&
	isset($_FILES["pott_pic"]) && trim($_FILES["pott_pic"]["name"]) != "" &&
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
	//CALLING TO THE SUPPORTED FILE CLASS
	include_once 'classes/file_class.php';

	// INITIALIZING VARIABLES TO HOLD THE INPUTS
	$input_id = trim($_POST["log_id_token"]);
	$input_pass = trim($_POST["log_pass_token"]);
	$input_pott_pic = $_FILES["pott_pic"];
	$input_language = trim($_POST["language"]);

	//DEFAULT GOVERNMENT ID VERIFICATION STATUS
	$government_id_verification_is_on = false;


	// CREATING A VALIDATOR OBJECT TO BE USED FOR VALIDATIONS
	$validatorObject = new inputValidator();

	// CREATING A LANGUAGES OBJECT TO BE USED TO RETRIEVE STRINGS NEEDED FOR RESPONSES
	$languagesObject = new languagesActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$miscellaneousObject = new miscellaneousActions();
	
	// CREATING FRONT-END RESPONDER OBJECT
	$fileObject = new fileActions();

	// CREATING DATABASE CONNECTION OBJECT
	$dbObject = new dbConnect();

	if($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}

	// CREATING PREPARED STATEMENT QUERY OBJECT
	$preparedStatementObject = new preparedStatement();
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "SELECT password, number_verified, flag, government_id_verified, request_government_id FROM " . LOGIN_TABLE_NAME . " WHERE id = ?", 1, "s", array($input_id));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}
	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("password", "number_verified", "flag", "government_id_verified", "request_government_id"), 5, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if(trim($prepared_statement_results_array[0]) == "password" || trim($prepared_statement_results_array[1]) == "number_verified"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
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
	if($prepared_statement_results_array[3] == 0 && $prepared_statement_results_array[4] == 1){
		$government_id_verification_is_on = true;
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
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
		}

	} else {
		$phone_verification_is_on = false;
	}


	// HANDLING THE IMAGE UPLOAD

	// GETTING THE BASE NAME OF THE INPUT FILE
	$sys_input_file_type = $input_pott_pic["type"];
	$sys_input_file_basename = basename($input_pott_pic["name"]);
	$sys_input_file_extension = strtolower(pathinfo($sys_input_file_basename,PATHINFO_EXTENSION));


	// CHECKING THE UPLOAD FILE TYPE TO BE SURE IF NOT WE FAIL THE PROCESS
	if($input_pott_pic["type"] != "image/jpeg" && $input_pott_pic["type"] != "image/png"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}

	if($sys_input_file_extension != "jpg" && $sys_input_file_extension != "png" && $sys_input_file_extension != "jpeg"){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}


	if($fileObject->fileSizeIsNotLargerThanMaxSize($input_pott_pic, MAXIMUM_ALLOWED_POTT_IMAGE_UPLOAD_SIZE) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}

	$sys_image_name = $input_id . $miscellaneousObject->getRandomString(50) . $miscellaneousObject->getRandomStringFromDateTime() . "." . $sys_input_file_extension;

	$sys_image_name_for_upload = "../../pic_upload/uploads/" . $sys_image_name;
	$sys_image_name_for_database = "uploads/" . $sys_image_name;
	$sys_image_name_to_be_returned = HTTP_HEAD_FOR_FISHPOTT . "/pic_upload/uploads/" . $sys_image_name;


/*

	// IF THE IMAGE IS MORE THEN 100 KB, THEN WE SHOULD COMPRESS IT TO BE THAT SIZE OR LESS
	if($fileObject->fileSizeIsNotLargerThanMaxSize($input_pott_pic, MAXIMUM_ALLOWED_POTT_IMAGE_UPLOAD_SIZE_FOR_NO_COMPRESSION) === false){

		$sys_compressed_input_file = $fileObject->compress($input_pott_pic["tmp_name"], $sys_image_name_for_upload, $fileObject->calculateCompressionQuality($input_pott_pic,MAXIMUM_ALLOWED_POTT_IMAGE_UPLOAD_SIZE_FOR_NO_COMPRESSION), false);
	} else {

		// UPLOADING THE FILE AND CHECKING IF IT'S SUCCESSFUL OR NOT
		if($fileObject->moveFile($input_pott_pic["tmp_name"], $sys_image_name_for_upload) === false){
			$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
		}
	}
*/

	// UPLOADING THE FILE AND CHECKING IF IT'S SUCCESSFUL OR NOT
	// REMOVE THIS WHEN YOU INCOMENT THE CODE ABOVE
	if($fileObject->moveFile($input_pott_pic["tmp_name"], $sys_image_name_for_upload) === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}

	// UPDATING THE RESET DATE, THE PASSWORD
	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, $GLOBALS["USAGE_MODE_IS_LIVE"]), "UPDATE " . USER_BIO_TABLE_NAME . " SET profile_picture = ? WHERE investor_id = ?", 2, "ss", array($sys_image_name_for_database, $input_id));

	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd1($languagesObject->getLanguageString("error", $input_language), $languagesObject->getLanguageString("upload_failed", $input_language));
	}

	// CLOSE DATABASE CONNECTION
	$dbObject->closeDatabaseConnection($prepared_statement);

	$signUpReturn["data_returned"][0]  = array(
		'status' => "yes", 
		'message' => "UPLOAD COMPLETE", 
		'pott_pic_path' => $sys_image_name_to_be_returned, 
		'phone_verification_is_on' => $phone_verification_is_on, 
		'highest_version_code' => CURRENT_HIGHEST_VERSION_CODE,
		'force_update_status' => FORCE_UPDATE_STATUS,
		'update_date' => UPDATE_DATE,
		'9' => $government_id_verification_is_on

		);
	echo json_encode($signUpReturn); exit;


	// CHECK IMAGE SIZE AND COMPRESS TO LESS THAN 
	// UPLOAD IMAGE
	// UPDATE LOGIN DATE
}

?>