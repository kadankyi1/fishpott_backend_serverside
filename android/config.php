<?php
	
	// HTTP BEING USED
	define("HTTP_HEAD","https"); 

	// HTTP BEING USED
	define("HTTP_HEAD_FOR_FISHPOTT","https://fishpott.com"); 

	 // BASE URL OF FILES
	define("BASE_URL","/");

	// COMPANY FISHPOTT POTT NAME & ID
	define("FISHPOT_POTT_NAME","fishpot_inc");
	define("FISHPOT_POTT_ID","030250308659e9029382af83.46926837");

	// COMPANY FISHPOTT POTT NAME & ID
	define("THE_INVESTOR_ID","theinvestor233553663643Rt6nnlDLKvqEYXCYxkpSGEsxITZPuCdSq8QaE0os");

	// ROOT PATH OF FILES
	define("ROOT_PATH",$_SERVER["DOCUMENT_ROOT"] . "/"); 

	// GOOGLE MAP API KEY
	define("GOOGLE_MAP_KEY","AIzaSyBl0hM2SASs3MeH-WOOfHUHEVntV497ZZM");

	// GOOGLE MAP API KEY
	define("FISHPOTT_PHONE_NUMBER","+233207393447");

	// GOOGLE MAP API KEY
	define("SENDING_EMAIL_ANDROID","notifications@fishpott.com");
	define("RECEIVING_EMAIL_ANDROID","fishpottcompany@gmail.com");

	// HIGHEST VERSION CODE AND FORCE UPDATE STATUS
	define("CURRENT_HIGHEST_VERSION_CODE", 14);
	define("MINIMUM_ALLOWED_VERSION_CODE", 6);
	define("FORCE_UPDATE_STATUS", false);
	define("UPDATE_DATE", "18/06/2020");

	// MAXIMUM ALLOWED SESSION TIME
	define("MAXIMUM_SESSION_ALLOWED_TIME", 259200); // 3 DAYS

	//FORCE GOVERNMENT ID
	define("FORCE_GOVERNMENT_STATUS", false);

	//DEVELOPER INFO
	define("DEVELOPER_USING_ADMIN_LIVE_MODE", true);
	define("DEVELOPER_USING_LIVE_MODE", true);
	define("DEVELOPER_USAGE_POTTNAME","raylight,thepkay,theinvestor");
	define("DEVELOPER_USAGE_ID","5,thepkay5a2a997ad77184.64969997,theinvestor233553663643llU9U52dql8PgsHwyf6tmj6IWnThxp8cUt5lsF6W");
	define("DEVELOPER_USAGE_PHONES","+233207393447,+233560012668,+233553663643");


	// REFERRAL PEARLS
	define("REFERRAL_PEARLS", 15);

	// TURN ON PHONE NUMBER VERIFICATIONS FOR SIGNUP
	define("PHONE_NUMBER_VERIFICATION_IS_ON", false); 

	// TURN ON PHONE NUMBER VERIFICATIONS FOR LOGIN
	define("LOGIN_PHONE_NUMBER_VERIFICATION_IS_ON", false); 

	// FISHPOTT APP ICON PICTURE LINK
	define("FISHPOTT_APP_ICON_PICTURE_LINK","https://www.fishpott.com/pic_upload/uploads/2017-12-161513439813.png");

	// DEFAULT NO-PHOTO AVATAR PICTURE LINK
	define("DEFAULT_NO_PHOTO_AVATAR_PICTURE_LINK","https://www.fishpott.com/pic_upload/uploads/2017-12-161513439813.png");

	// DEFINING VARIOUS SIZES
	define('KB', 1024);
	define('MB', 1048576);
	define('GB', 1073741824);
	define('TB', 1099511627776);


	// DEFINING PERCENTAGE CUTS FOR RISK PROTECTION
	define('FULL_PROTECTION', 0.6);
	define('HALF_PROTECTION', 0.8);
	define('NO_PROTECTION', 1);


	define('FULL_PROTECTION_INSURANCE_CHARGE_PERCENTAGE', 0.03);
	define('HALF_PROTECTION_INSURANCE_CHARGE_PERCENTAGE', 0.02);

	// FISHPOTT COMMISSION PER SALE
	define('FISHPOTT_COMMISSION_PERCENTAGE', 0.01);

	// FISHPOTT COMMISSION PER SALE
	define('FISHPOTT_TRANSFER_FEE_IN_DOLLARS', 1);

	// FISHPOTT PROCESSING FEE
	define('FISHPOTT_STOCK_PURCHASE_PROCESSING_FEE_PERCENTAGE_OVER_100_DOLLARS', 0.01);
	define('FISHPOTT_STOCK_PURCHASE_PROCESSING_FEE_LESS_THAN_100_DOLLARS_IN_DOLLARS', 1);

	// FISHPOTT FISHPOTT_STOCK_PURCHASE_PROCESSING_FEE_PERCENTAGE
	define('FISHPOTT_T_BILL_PURCHASE_PROCESSING_FEE_IN_DOLLARS', 1);
	define('FISHPOTT_YIELD_PROCESSING_PERCENTAGE', 0.1);

	// RETURNING SHARES VALUE REDUCTION FACTOR
	define('RETURNING_SHARES_VALUE_REDUCTION_FACTOR', 0.8);

	// MAXIMUM FILE SIZE FOR POTT PICTURE UPLOAD
	define('MAXIMUM_ALLOWED_POTT_IMAGE_UPLOAD_SIZE', 10 * MB);

	// MAXIMUM FILE SIZE FOR NEWS IMAGE UPLOAD
	define('MAXIMUM_ALLOWED_NEWS_IMAGE_UPLOAD_SIZE', 10 * MB);

	// MAXIMUM FILE SIZE FOR NEWS VIDEO UPLOAD
	define('MAXIMUM_ALLOWED_NEWS_VIDEO_UPLOAD_SIZE', 65 * MB);

	// MAXIMUM FILE SIZE FOR POTT PICTURE UPLOAD WITHOUT COMPRESSION
	define('MAXIMUM_ALLOWED_POTT_IMAGE_UPLOAD_SIZE_FOR_NO_COMPRESSION', 150 * KB);

	// MAXIMUM FILE SIZE FOR POTT PICTURE UPLOAD WITHOUT COMPRESSION
	define('MINIMUM_ALLOWED_WITHDRAWAL', 10);

	// ALL USERS TOPIC
	define("ALL_USERS_TOPIC", "FISHPOT_TIPS");

	// NEWS TYPE
	define("NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY",6);
	define("NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY",7);
	define("NEWS_TYPE_3_TO_4_JUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY",8);
	define("NEWS_TYPE_5_TO_6_JUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY",9);
	define("NEWS_TYPE_7_AND_9_JUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY",10);
	define("NEWS_TYPE_8_JUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY",11);
	define("NEWS_TYPE_10_UPFORSALENEWS_VERTICAL_KEY",12);
	define("NEWS_TYPE_12_EVENTNEWS_VERTICAL_KEY",13);
	define("NEWS_TYPE_14_SHARESFORSALENEWS_VERTICAL_KEY",14);
	define("NEWS_TYPE_16_FUNDRAISERNEWS_VERTICAL_KEY",15);
	define("NEWS_TYPE_17_SHARES4SALEWITHVIDEO_VERTICAL_KEY",16);
	define("NEWS_TYPE_1_SPONSOREDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY",17);
	define("NEWS_TYPE_2_SPONSOREDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY",18);
	define("NEWS_TYPE_3_TO_4_SPONSOREDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY",19);
	define("NEWS_TYPE_5_TO_6_SPONSOREDJUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY",20);
	define("NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY",21);
	define("NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY",22);
	define("NEWS_TYPE_3_TO_4_REPOSTEDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY",23);
	define("NEWS_TYPE_5_TO_6_REPOSTEDNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY",24);
	define("NEWS_TYPE_7_AND_9_REPOSTEDJUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY",25);
	define("NEWS_TYPE_8_REPOSTEDJUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY",26);
	define("NEWS_TYPE_10_REPOSTEDUPFORSALENEWS_VERTICAL_KEY",27);
	define("NEWS_TYPE_14_REPOSTEDSHARESFORSALENEWS_VERTICAL_KEY",28);
	define("NEWS_TYPE_17_REPOSTEDSHARES4SALEWITHVIDEO_VERTICAL_KEY",29);

	define("NEWS_TYPE_41_SHARESFORSALE_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY",41);
	define("NEWS_TYPE_42_SHARESFORSALE_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY",42);
	define("NEWS_TYPE_43_SHARESFORSALE_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY",43);
	define("NEWS_TYPE_44_SHARESFORSALE_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY",44);


/*****************************************************************************************************************
				SMS API CREDENTIAL START
******************************************************************************************************************/
	// SMS API CREDENTIAL
	define("PRODUCTTOKEN", "TOKEN GOES HERE");
	define("SENDER_NAME", "FishPott");
	define("SMS_G_USERNAME", "pavqxdnk");
	define("SMS_G_PASS", "kBQ99Fvs");



/*****************************************************************************************************************
				MOBILE MONEY
******************************************************************************************************************/

	// COLLECTION REQUESTS
	define("MTN_CREATE_API_USER_URL", "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser");
	define("MTN_CURRENT_TARGET_ENVIRONMENT", "sandbox");

	define("MTN_MOMO_COLLECTION_API_KEY_1", "0a978b188f9442a4bfe821b4c01a99a7");
	define("MTN_MOMO_COLLECTION_API_KEY_2", "face30e5c5804956adb315b6c7524f17");
	define("MTN_CREATE_COLLECTION_TOKEN_URL", "https://sandbox.momodeveloper.mtn.com/collection/token/");
	define("MTN_CREATE_COLLECTION_REQUESTTOPAY_URL", "https://sandbox.momodeveloper.mtn.com/collection/v1_0/requesttopay");


	define("MTN_MOMO_DISBURSEMENT_API_KEY_1", "3e8a0798b86444768264c04cf3f8d7ee");
	define("MTN_MOMO_DISBURSEMENT_API_KEY_2", "6d325258b3304f29ae56d9957d9e93eb");
	define("MTN_CREATE_DISBURSEMENT_TOKEN_URL", "https://sandbox.momodeveloper.mtn.com/disbursement/token/");
	define("MTN_CREATE_DISBURSEMENT_SEND_TRANSFER_URL", "https://sandbox.momodeveloper.mtn.com/disbursement/v1_0/transfer");


	define("MTN", "0553663643");
	define("MTN_NAME", "Dankyi Anno Kwaku");
	define("VODAFONE", "");
	define("VODAFONE_NAME", "");
	define("AIRTELTIGO", "");
	define("AIRTELTIGO_NAME", "");


/*****************************************************************************************************************
				SMS API CREDENTIAL END
******************************************************************************************************************/

/*****************************************************************************************************************
				FIREBASE NOTIFICATION API CREDENTIALS START
******************************************************************************************************************/
	
	// FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK
	define("FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK","https://fcm.googleapis.com/fcm/send");

	// FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY
	define("FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY","AAAAyNozJtc:APA91bHf8IpIE_vM52ZhLTP7Vi1QDS-EK3urQwX_-0cj5aSlT7TaYU3eKftPv5-d4K3aOqFKqiFN6pTWGB7nhzqV5eF6sFqOmXX9rj5qCPdYp-I-IpbcybJuE5w4S4Zp4tVIuHb4qwDf");

/*****************************************************************************************************************
				FIREBASE NOTIFICATION API CREDENTIALS END
******************************************************************************************************************/


/*****************************************************************************************************************
				TABLE NAMES START
******************************************************************************************************************/
	// TABLE NAME - USER BIOS 
	define("USER_BIO_TABLE_NAME","investor");

	// TABLE NAME - LOGIN
	define("LOGIN_TABLE_NAME","wuramu");

	// TABLE NAME - LINKUPS
	define("LINKUPS_TABLE_NAME","linkups");

	// TABLE NAME - COLLECTED_CONTACTS
	define("PHONE_CONTACTS_TABLE_NAME","nipa_contacts");

	// TABLE NAME - IP ADDRESSES 
	define("IP_ADDRESSES_TABLE_NAME","nipa_ip_addresses");

	// TABLE NAME - NEWS TABLE 
	define("NEWS_TABLE_NAME","newsfeed");

	// TABLE NAME - NEWS IMAGES TABLE 
	define("NEWS_IMAGES_TABLE_NAME","news_mfoni");

	// TABLE NAME - NEWS LINKS TABLE 
	define("NEWS_LINKS_TABLE_NAME","news_links_nyinaa");

	// TABLE NAME - NEWS LIKES TABLE 
	define("LIKES_TABLE_NAME","likes");

	// TABLE NAME - NEWS COMMENTS TABLE 
	define("COMMENTS_TABLE_NAME","comments");

	// TABLE NAME - VIEWS TABLE 
	define("VIEWS_TABLE_NAME","news_ahwehwer_views");

	// TABLE NAME - PURCHASES TABLE 
	define("PURCHASES_TABLE_NAME","adetor");

	// TABLE NAME - SHARES4SALE TABLE 
	define("SHARES4SALE_TABLE_NAME","shares4sale");

	// TABLE NAME - UP4SALE TABLE 
	define("UP4SALE_TABLE_NAME","up4sale");

	// TABLE NAME - EVENT TABLE 
	define("EVENT_TABLE_NAME","event");

	// TABLE NAME - FUNDRAISER TABLE 
	define("FUNDRAISER_TABLE_NAME","fundraiser");

	// TABLE NAME - EXCHANGE RATES TABLE 
	define("EXCHANGE_RATES_TABLE_NAME","nsesa");

	// TABLE NAME - SHARES ON FISHPOTT TABLE 
	define("SHARES_HOSTED_TABLE_NAME","shares_worso");

	// TABLE NAME - SHARES-OWNED BY INVESTORS ON FISHPOTT TABLE 
	define("SHARES_OWNED_BY_INVESTOR_TABLE_NAME","shares_owned");

	// TABLE NAME - SHARES VALUE HISTORY TABLE 
	define("SHARES_VALUE_HISTORY_TABLE_NAME","shares_nseminfo_history");

	// TABLE NAME - SHARES TRANSFER TABLE 
	define("SHARES_TRANSFER_TABLE_NAME","y3n_transfers");

	// TABLE NAME - CHAT MESSAGES TABLE 
	define("CHAT_MESSAGES_TABLE_NAME","akasakasa_nkomor");

	// TABLE NAME - WALLET CREDIT COUPON TABLE 
	define("WALLET_CREDIT_COUPON_TABLE_NAME","sika_credit_coupon");

	// TABLE NAME - SHARES CREDIT COUPON TABLE 
	define("SHARES_CREDIT_COUPON_TABLE_NAME","shares_erko_coupon");

	// TABLE NAME - WITHDRAWAL TABLE 
	define("WITHDRAWAL_TABLE_NAME","sika_withdrawal_requests");

	// TABLE NAME - CREDIT_REQUEST TABLE 
	define("MONEY_CREDIT_TABLE_NAME","sika_credit_requests");

	// TABLE NAME - BLOCKED POTTS TABLE 
	define("BLOCKED_POTTS_TABLE_NAME","mern_ha_me_fuo");

	// TABLE NAME - CHAT MESSAGES TABLE 
	define("POACH_TABLE_NAME","awiawia");

/*****************************************************************************************************************
				TABLE NAMES END
******************************************************************************************************************/


/*****************************************************************************************************************
				INVESTOR POINTS LIMITS AND POTT VALUE
******************************************************************************************************************/
	// SMS API CREDENTIAL
	define("TOTAL_AMOUNT_MADE_ON_FP_CONSIDERED_A_LOT", 1000);
	define("TOTAL_AMOUNT_OF_PEARLS_ON_FP_CONSIDERED_A_LOT", 100);
	define("BABY_INVESTOR_UPPER_LIMIT", 0);
	define("TODDLER_INVESTOR_UPPER_LIMIT", 1);
	define("SWIFT_INVESTOR_UPPER_LIMIT", 50);
	define("DEMI_GOD_INVESTOR_UPPER_LIMIT", 199);
	define("GOD_INVESTOR_UPPER_LIMIT", 100000000000000000000000000);

	define("POTT_VALUE_IS_HIGH_LEVEL_1", 50);
	define("POTT_VALUE_IS_HIGH_LEVEL_2", 100);
	define("POTT_VALUE_IS_HIGH_LEVEL_3", 500);
	define("POTT_VALUE_IS_HIGH_LEVEL_4", 1000);
/*****************************************************************************************************************
				INVESTOR POINTS LIMITS  END
******************************************************************************************************************/

/*****************************************************************************************************************
				USER SESSION VALUES INDEXES
******************************************************************************************************************/

	define("USER_CONTACTS_SEPARATED_BY_COMMAS", "USER_CONTACTS_SEPARATED_BY_COMMAS");
	define("USER_CONTACTS_SEPARATED_BY_VERTICAL_SLASH_FOR_REGEX_QUERY", "USER_CONTACTS_SEPARATED_BY_VERTICAL_SLASH_FOR_REGEX_QUERY");

/*****************************************************************************************************************
				INVESTOR POINTS LIMITS  END
******************************************************************************************************************/


/*****************************************************************************************************************
				
							**********************************************
							* 			ADMIN CONSTANTS START 			 *
							* 			ADMIN CONSTANTS START 			 *
							* 			ADMIN CONSTANTS START 			 *
							* 			ADMIN CONSTANTS START 			 *
							**********************************************

******************************************************************************************************************/


// TARGET ONLINE USERS
define("TARGET_ONLINE_USERS", 100); 


/*****************************************************************************************************************
				 SUPER_ADMIN_LEVEL CAN PERFORM ALL ACTIVITIES
******************************************************************************************************************/

define("SUPER_ADMIN_LEVEL", 1); //

/*****************************************************************************************************************
				ADMIN_LEVEL_2 CAN PERFORM ALL ACTIVITIES BELOW
				 - FLAG/UNFLAG NEWS
				 - CHECK MESSAGES
				 - SEND MESSAGES OR RESPOND TO MESSAGES FROM THE FISHPOT_INC ACCOUNT
				 - UPDATE SHARES VALUE
******************************************************************************************************************/
define("ADMIN_LEVEL_2", 2); 
// CAN DO ALL

/*****************************************************************************************************************
				ADMIN TABLE NAMES
******************************************************************************************************************/

// TABLE NAME - ADMIN LOGIN RATES TABLE 
define("ADMIN_BIO_LOGIN_TABLE_NAME","abanfo_nhyinaa");

