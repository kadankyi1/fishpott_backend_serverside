<?php
exit;
	$_POST["type"] = "like";
	$_POST["id_1"] = "5";
	$_POST["id_2"] = "raylight";
	$_POST["id_3"] = "";
	$_POST["not_text"] = "test general_notification";
	$_POST["title"]  = "test";

// MAKING SURE THE REQUEST METHOD IS A POST AND HAS THE EXPECTED PARAMETERS
if( 
	isset($_POST["type"]) && 
	isset($_POST["id_1"]) &&
	isset($_POST["id_2"]) && 
	isset($_POST["id_3"]) && 
	isset($_POST["not_text"]) &&
	isset($_POST["title"]) 
) {

	//CALLING THE CONFIGURATION FILE
	require_once("config.php");
	include_once 'classes/miscellaneous_class.php';
	$miscellaneousObject = new miscellaneousActions();
	$receiver_keys = array("dU236Z7VDs4:APA91bGlRUqyqZ3yx6XQmnCkdZQ0ysG_3RidN25X_njfvcWERRxxHak48yj4W0z2VuI6RCu6g_j-6Sj3lJJxj70byjhyqrxCKBoRlaaTOUzxQzOkhfIgRABtqK-3AaKG_vHTqbqlHT7T");

	echo "STATUS : " . $miscellaneousObject->sendNotificationToUser(
		FIREBASE_NOTIFICATION_SERVER_ADDRESS_LINK, 
		FIREBASE_NOTIFICATION_ACCOUNT_SERVER_KEY, 
		$receiver_keys, 
		"https://www.fishpott.com/pic_upload/uploads/5lv7cjy4GabC6BQhWlxGWQX3HFAbAMY0kENriZAwuIHqzL7ccNw2019-01-091547043378.png", 
		"normal", 
		"general_notification", 
		$_POST["type"], 
		$_POST["id_1"], 
		$_POST["id_2"], 
		$_POST["title"], 
		$_POST["not_text"], 
		date("F j, Y"),
		$_POST["id_3"]
	);


}