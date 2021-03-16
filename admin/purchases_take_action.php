<?php
session_start();
$error_page = "../../abanfo/in/examples/_1purchases_view_purchases.php";

	//CALLING THE CONFIGURATION FILE
	require_once("../android/config.php");

	//CALLING THE INPUT VALIDATOR CLASS
	include_once '../android/classes/input_validation_class.php';
	$validatorObject = new inputValidator();

	//CALLING THE MISCELLANOUS CLASS
	include_once '../android/classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();
	//CALLING TO THE DATABASE CLASS
	include_once '../android/classes/db_class.php';
	$dbObject = new dbConnect();

	//CALLING TO THE PREPARED STATEMENT QUERY CLASS
	include_once '../android/classes/prepared_statement_class.php';
	$preparedStatementObject = new preparedStatement();

	//CALLING TO THE SUPPORTED LANGUAGES CLASS
	include_once '../android/classes/languages_class.php';
	$languagesObject = new languagesActions();


	if(isset($_GET["i"]) && intval($_GET["i"]) > 0){
		$var_sku_purchases = intval($_GET["i"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_GET["i2"]) && intval($_GET["i2"]) > 0){
		$var_sku_ptransfers = intval($_GET["i2"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(isset($_GET["t"]) && (intval($_GET["t"]) == 1 || intval($_GET["t"]) == 0)){
		$var_action_type = intval($_GET["t"]);
	} else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}


	$var_phone = trim($_SESSION["admin_phone"]);
	$input_password_hashed = trim($_SESSION["admin_pass"]);


	//MAKING SURE THE PHONE AND PASSWORD MAXLENGTH IS MET
	if($validatorObject->stringIsNotMoreThanMaxLength($var_phone, 15) === false){

		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THAT PHONE NUMBER CONATINS NO TAGS
	if($validatorObject->stringContainsNoTags($var_phone) !== true){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}
	
	// MAKING SURE PHONE NUMBER AFTER + CONTAINS ONLY NUMBERs
	if($validatorObject->inputContainsOnlyNumbers(substr($var_phone,1,strlen($var_phone))) !== true){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//MAKING SURE THE FIRST LETTER IS '+'
	if(substr($var_phone,0,1) != "+"){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	if($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE) === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT flag, admin_password, admin_id, admin_name, admin_country, admin_profile_pic, admin_level FROM " . ADMIN_BIO_LOGIN_TABLE_NAME . " WHERE admin_phone = ?", 1, "s", array($var_phone));
	if($prepared_statement === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("flag", "admin_password", "admin_id", "admin_name", "admin_country", "admin_profile_pic", "admin_level"), 7, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	if($input_password_hashed === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("login_failed", $input_language));
	}

	//CHECKING THAT THE PASSWORD MATCHES AND THE ACCOUNT IS NOT FLAGGED
	if($input_password_hashed == $prepared_statement_results_array[1] && $prepared_statement_results_array[0] == 0){

		// ASSIGNING THE FETCHED LOGIN DETAILS FROM DB INTO VARIABLES
		$var_admin_pass = $prepared_statement_results_array[1];
		$var_admin_id = $prepared_statement_results_array[2];
		$var_admin_level = $prepared_statement_results_array[6];
		$input_language = $prepared_statement_results_array[4];

		if($var_admin_level > SUPER_ADMIN_LEVEL){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- You do not have the clearance level to perform this action. Inform Super Admin");
		}

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT GHS_USD, USD_GHS, GHS_GBP, GBP_GHS, USD_GBP, GBP_USD FROM " . EXCHANGE_RATES_TABLE_NAME . " ORDER BY sku DESC", 0, "", array());

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("GHS_USD", "USD_GHS", "GHS_GBP", "GBP_GHS", "USD_GBP", "GBP_USD"), 6, 1);

	if($prepared_statement_results_array === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	// IF THE DATABASE QUERY GOT NO RESULTS
	if($prepared_statement_results_array[0] <= 0 || $prepared_statement_results_array[1] <= 0){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$GHS_USD = $prepared_statement_results_array[0];
	$USD_GHS = $prepared_statement_results_array[1];
	$GHS_GBP = $prepared_statement_results_array[2];
	$GBP_GHS = $prepared_statement_results_array[3];
	$USD_GBP = $prepared_statement_results_array[4];
	$GBP_USD = $prepared_statement_results_array[5];


/***********************************************************************************************************************
	
												START PERFORMING NEEDED TASK				

***********************************************************************************************************************/

	$query =  "SELECT "  
	. USER_BIO_TABLE_NAME . ".fcm_token,  "  
	. USER_BIO_TABLE_NAME . ".fcm_token_ios,  "  
	. USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd,  "  
	. USER_BIO_TABLE_NAME . ".language,  "  
	. USER_BIO_TABLE_NAME . ".country,  "  
	. SHARES_TRANSFER_TABLE_NAME . ".sender_id ,  "  
	. SHARES_TRANSFER_TABLE_NAME . ".share_id,  "  
	. SHARES_TRANSFER_TABLE_NAME . ".receiver_id ,  "  
	. SHARES_TRANSFER_TABLE_NAME . ".receiver_share_id,  "    
    . PURCHASES_TABLE_NAME . ".item_quantity,  "   
    . PURCHASES_TABLE_NAME . ".transaction_currency,  "   
    . PURCHASES_TABLE_NAME . ".sale_real_amt_credited_to_seller_acc,  "   
	. SHARES_TRANSFER_TABLE_NAME . ".shares_parent_name,  "   
	. PURCHASES_TABLE_NAME . ".fp_commission,  "   
	. SHARES_TRANSFER_TABLE_NAME . ".sku FROM "
	. USER_BIO_TABLE_NAME . " INNER JOIN " 
	. SHARES_TRANSFER_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. SHARES_TRANSFER_TABLE_NAME . ".receiver_id INNER JOIN " 
	. LOGIN_TABLE_NAME . " ON  "  
	. USER_BIO_TABLE_NAME . ".investor_id="  
	. LOGIN_TABLE_NAME . ".id  INNER JOIN " 
	. PURCHASES_TABLE_NAME . " ON  "  
	. SHARES_TRANSFER_TABLE_NAME . ".share_id= "  
	. PURCHASES_TABLE_NAME . ".adetor_item_id "
	. " WHERE " . SHARES_TRANSFER_TABLE_NAME . ".admin_review_status = 0 AND " . SHARES_TRANSFER_TABLE_NAME . ".admin_id = '' AND " . LOGIN_TABLE_NAME . ".flag = 0 AND " . PURCHASES_TABLE_NAME . ".sku = ? AND " . SHARES_TRANSFER_TABLE_NAME . ".sku = ?";


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), $query, 2, "ii",array($var_sku_purchases, $var_sku_ptransfers));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array(
		USER_BIO_TABLE_NAME . ".fcm_token", 
		USER_BIO_TABLE_NAME . ".fcm_token_ios",
		USER_BIO_TABLE_NAME . ".withdrawal_wallet_usd",
		USER_BIO_TABLE_NAME . ".language",
		USER_BIO_TABLE_NAME . ".country",
		SHARES_TRANSFER_TABLE_NAME . ".sender_id",
		SHARES_TRANSFER_TABLE_NAME . ".share_id",
		SHARES_TRANSFER_TABLE_NAME . ".receiver_id",
		SHARES_TRANSFER_TABLE_NAME . ".receiver_share_id",
		PURCHASES_TABLE_NAME . ".item_quantity",
		PURCHASES_TABLE_NAME . ".transaction_currency",
		PURCHASES_TABLE_NAME . ".sale_real_amt_credited_to_seller_acc",
		SHARES_TRANSFER_TABLE_NAME . ".shares_parent_name",
		PURCHASES_TABLE_NAME . ".fp_commission",
		SHARES_TRANSFER_TABLE_NAME . ".sku"
	), 14, 2);

	//BINDING THE RESULTS TO VARIABLES
	$prepared_statement_results_array->bind_result($fcm_token, $fcm_token_ios, $withdrawal_wallet_usd, $user_language, $country, $sender_id, $share_id, $receiver_id, $receiver_share_id, $item_quantity, $transaction_currency, $sale_real_amt_credited_to_seller_acc,  $shares_parent_name, $fp_commission, $transfer_sku);

	$prepared_statement_results_array->fetch();

	if(trim($sender_id) == "" || trim($share_id) == "" || trim($receiver_id) == "" || trim($receiver_share_id) == "" || $item_quantity <= 0 || $sale_real_amt_credited_to_seller_acc <= 0){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	if(trim($user_language) == ""){
		$user_language = $country;
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


    $prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT shares_parent_id FROM " . SHARES_TRANSFER_TABLE_NAME . " WHERE share_id = ? AND sku > ?", 2, "si", array($share_id, $transfer_sku));

    if($prepared_statement_2 !== false){
        $prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("shares_parent_id"), 1, 1);

        if($prepared_statement_results_array_2 !== false && $prepared_statement_results_array_2[0] != "shares_parent_id"){
	    	$new_transfer_exists = true;
        } else {
	    	$new_transfer_exists = false;
        }
    } else {
		$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed operation.");
    }


	if($var_action_type == 1){

		if(!$new_transfer_exists){
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET flag = 0, admin_review_status = 1 WHERE share_id = ?", 1, "s", array($receiver_share_id));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to unflag shares for buyer. If this continues, inform Super Admin.");
			}
		}

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_TRANSFER_TABLE_NAME . " SET admin_review_status = 1, admin_id = ?, review_datetime = ? WHERE sku = ?", 3, "ssi", array($var_admin_id, date("Y-m-d H:i:s"), $var_sku_ptransfers));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update transfer review status. Inform Super Admin");
		}
		
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . USER_BIO_TABLE_NAME . " SET withdrawal_wallet_usd = withdrawal_wallet_usd + $sale_real_amt_credited_to_seller_acc WHERE investor_id = ?", 1, "s", array($sender_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to credit seller's money from sale. Inform Super Admin");
		}

		

		$sys_status_info = $languagesObject->getLanguageString("purchased_confirmed", $user_language);
		$sys_status_info2 = $item_quantity . " " . $shares_parent_name . " " . $languagesObject->getLanguageString("shares_purchased_confirmed", $user_language);

		$miscellaneousObject->sendNotificationToUser(
			FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
			FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
			$receiver_keys, 
			"fp", 
			"normal", 
			"general_notification", 
			"purchase", 
			FISHPOT_POTT_ID, 
			FISHPOT_POTT_NAME, 
			$sys_status_info, 
			$sys_status_info2, 
			date("F j, Y"), 
			$share_id
		);

	} else {

	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT total_yield, num_of_shares FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE share_id = ?", 1, "s", array($share_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("total_yield", "num_of_shares"), 2, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	$sender_shares_total_yield = $prepared_statement_results_array[0];
	$sender_num_of_shares = $prepared_statement_results_array[1];

	if($sender_shares_total_yield <= 0 || $sender_num_of_shares <= 0){
		$new_sender_shares_total_yield = 0;
	} else {
		$new_sender_shares_total_yield = $sender_shares_total_yield + (($sender_shares_total_yield/$sender_num_of_shares) * $item_quantity);
	}


	$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT total_yield, num_of_shares FROM " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " WHERE share_id = ?", 1, "s", array($receiver_share_id));

	if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// GETTING RESULTS OF QUERY INTO AN ARRAY
	$prepared_statement_results_array = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement, array("total_yield", "num_of_shares"), 2, 1);

	if($prepared_statement_results_array === false){
		$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}

	// IF THE DATABASE QUERY GOT NO RESULTS
	$receiver_shares_total_yield = $prepared_statement_results_array[0];
	$receiver_num_of_shares = $prepared_statement_results_array[1];

	if($receiver_shares_total_yield <= 0 || $receiver_num_of_shares <= 0){
		$new_receiver_shares_total_yield = 0;
	} else {
		$new_receiver_shares_total_yield = $receiver_shares_total_yield - (($receiver_shares_total_yield/$receiver_num_of_shares) * $item_quantity);
	}

		if($new_transfer_exists){
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET num_of_shares = num_of_shares - $item_quantity, total_yield = ? WHERE share_id = ?", 2, "ds", array($new_receiver_shares_total_yield, $receiver_share_id));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to remove shares from buyer. If this continues, inform Super Admin");
			}
		} else {
			$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET flag = 0, num_of_shares = num_of_shares - $item_quantity, admin_review_status = 1, total_yield = ?  WHERE share_id = ?", 2, "ds", array($new_receiver_shares_total_yield, $receiver_share_id));

			if($prepared_statement === false){
				$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to remove shares from buyer. If this continues, inform Super Admin");
			}
		}


		$new_debit_wallet_usd = $sale_real_amt_credited_to_seller_acc + $fp_commission;
		
		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . USER_BIO_TABLE_NAME . " SET debit_wallet_usd = debit_wallet_usd + $new_debit_wallet_usd WHERE investor_id = ?", 1, "s", array($receiver_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to return buyer's money. Inform Super Admin");
		}

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_TRANSFER_TABLE_NAME . " SET admin_review_status = 2, admin_id = ?, review_datetime = ? WHERE sku = ?", 3, "ssi", array($var_admin_id, date("Y-m-d H:i:s"), $var_sku_ptransfers));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to update transfer review status. Inform Super Admin");
		}

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES_OWNED_BY_INVESTOR_TABLE_NAME . " SET num_of_shares = num_of_shares + $item_quantity, total_yield = ? WHERE share_id = ?", 2, "ds", array($new_sender_shares_total_yield, $share_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to add shares to seller. Inform Super Admin");
		}

		$sys_status_info = $languagesObject->getLanguageString("purchased_failed", $user_language);
		$sys_status_info2 = $item_quantity . " " . $shares_parent_name . " " . $languagesObject->getLanguageString("shares_purchase_failed_to_confirm", $user_language);


		$miscellaneousObject->sendNotificationToUser(
			FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
			FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
			$receiver_keys, 
			"fp", 
			"normal", 
			"general_notification", 
			"purchase", 
			FISHPOT_POTT_ID, 
			FISHPOT_POTT_NAME, 
			$sys_status_info, 
			$sys_status_info2, 
			date("F j, Y"), 
			$share_id
		);

		$prepared_statement = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "UPDATE " . SHARES4SALE_TABLE_NAME . " SET num_on_sale = num_on_sale + $item_quantity, number_sold = number_sold - $item_quantity WHERE sharesOnSale_id = ?", 1, "s", array($share_id));

		if($prepared_statement === false){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT]- Failed to re-add shares to shares for sale table. Inform Super Admin");
		}

	}



	    $prepared_statement_2 = $preparedStatementObject->prepareAndExecuteStatement($dbObject->connectToDatabase(0, DEVELOPER_USING_ADMIN_LIVE_MODE), "SELECT fcm_token, fcm_token_ios, language, country FROM " . USER_BIO_TABLE_NAME . " WHERE investor_id = ?", 1, "s", array($sender_id));

	    if($prepared_statement_2 !== false){
	        $prepared_statement_results_array_2 = $preparedStatementObject->getPreparedStatementQueryResults($prepared_statement_2, array("fcm_token", "fcm_token_ios", "language", "country"), 4, 1);

	        if($prepared_statement_results_array_2 !== false){

	            $receiver_pottname = $prepared_statement_results_array_2[0];

	            $user_language = $prepared_statement_results_array_2[2];

				if(trim($user_language) == ""){
					$user_language = $country;
				}

				$sys_sender_fcm_token = $prepared_statement_results_array_2[0];
				$sys_sender_fcm_token_ios = $prepared_statement_results_array_2[1];


				$sender_keys2 = array();
				if(trim($sys_sender_fcm_token) != "fcm_token" && trim($sys_sender_fcm_token) != ""){
					$sender_keys2[0] = $sys_sender_fcm_token;
				}

				if(trim($sys_sender_fcm_token_ios) != "fcm_token_ios" && trim($sys_sender_fcm_token_ios) != ""){
					$sender_keys2[count($sender_keys2)] = $sys_sender_fcm_token_ios;
				}

				$fp_real_commission = FISHPOTT_COMMISSION_PERCENTAGE * 100;

				if($var_action_type == 1){
					$sys_status_info = $languagesObject->getLanguageString("purchased_confirmed", $user_language);
					$sys_status_info2 = $item_quantity . " " . $shares_parent_name . " " . $languagesObject->getLanguageString("shares_purchased_confirmed", $user_language) . " " . $fp_real_commission . "% " . $languagesObject->getLanguageString("transaction_fee_applied", $user_language);

				} else {
					$sys_status_info = $languagesObject->getLanguageString("purchased_failed", $user_language);
					$sys_status_info2 = $item_quantity . " " . $shares_parent_name . " " . $languagesObject->getLanguageString("shares_purchase_failed_to_confirm", $user_language);

				}

				$miscellaneousObject->sendNotificationToUser(
					FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
					FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
					$sender_keys2, 
					"fp", 
					"normal", 
					"general_notification", 
					"transfer", 
					FISHPOT_POTT_ID, 
					FISHPOT_POTT_NAME, 
					$sys_status_info, 
					$sys_status_info2, 
					date("F j, Y"), 
					$share_id
				);

	        }
	    }


	$miscellaneousObject->respondFrontEnd2("black", $error_page, "[NT] - Operation successful");

/***********************************************************************************************************************
	
												END PERFORMING NEEDED TASK				

***********************************************************************************************************************/

	} else if($input_password_hashed != $prepared_statement_results_array[1]){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("incorrect_phone_number_or_password", $input_language));
	} else if($prepared_statement_results_array[0] != 0){
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("your_account_has_been_suspended", $input_language));
	} else {
			$miscellaneousObject->respondFrontEnd2("black", $error_page, $languagesObject->getLanguageString("something_went_wrong", $input_language));
	}
	
