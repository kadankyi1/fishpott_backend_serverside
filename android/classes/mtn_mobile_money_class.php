<?php
class mtnMomoActions {

function mtnCreateApiUser($UUID, $mtn_key, $api_url, $response_handler_url){
	$postdata_array = array(
	    "providerCallbackHost" => $response_handler_url
	);
	$postdata_json = json_encode($postdata_array);

	$header_ref_id = 'X-Reference-Id: ' . $UUID;
	$header_mtn_key = 'Ocp-Apim-Subscription-Key: ' . $mtn_key;
	$header_content_type = 'Content-Type: application/json';
	$header_content_length = 'Content-Length: ' . strlen($postdata_json);

	$headers = array(
	    $header_ref_id,
	    $header_mtn_key,
	    $header_content_type,
	    $header_content_length
	);	

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	//curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($httpcode ==  "201"){

		$result = rtrim($result);
		$response_array_exploded = explode("\n",$result);
		$api_user_array_exploded = explode(":",$response_array_exploded[2]);

		if(count($api_user_array_exploded) >= 2 && strlen(trim($api_user_array_exploded[2])) == 36){
			return trim($api_user_array_exploded[2]);
		} else {
			return "0";
		}
		 
	} else {
		return "0";
	}

}// END

function mtnGetApiKey($mtn_api_user_UUID, $mtn_key, $api_url){
	$header_ref_id = 'X-Reference-Id: ' . $mtn_api_user_UUID;
	$header_mtn_key = 'Ocp-Apim-Subscription-Key: ' . $mtn_key;

	$headers = array(
	    $header_ref_id,
	    $header_mtn_key
	);	

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	//curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($httpcode ==  "201"){

		$result = rtrim($result);
		$response_array_exploded = explode("\n",$result);

		$result_json_obj = json_decode($response_array_exploded[6]);
		
		if($result_json_obj == null){
			return "0";
		}
		return $result_json_obj->apiKey;		 
	} else {
		return "0";
	}

}// END

function mtnGetApiToken($mtn_api_user_UUID, $mtn_api_key, $mtn_key, $api_url){
	$header_autorization_id = 'Authorization: Basic ' . base64_encode("$mtn_api_user_UUID:$mtn_api_key");
	$header_mtn_key = 'Ocp-Apim-Subscription-Key: ' . $mtn_key;

	$headers = array(
	    $header_autorization_id,
	    $header_mtn_key
	);	

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	//curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($httpcode ==  "200"){

		$result = rtrim($result);
		$response_array_exploded = explode("\n",$result);

		$result_json_obj = json_decode($response_array_exploded[8]);
		
		if($result_json_obj == null){
			return "0";
		}
		return $result_json_obj->access_token;		 
	} else {
		return "0";
	}

}// END

function mtnSendMomoCollectionRequest($mtn_api_token, $call_back_url, $target_environment, $mtn_api_user_UUID, $mtn_key,  $api_url, $amount, $currency, $external_id, $payer_type, $payer_id, $payer_message){

	$postdata_array = array(
	    "amount" => $amount,
	    "currency" => $currency,
	    "externalId"=> $external_id,
	    "payer" => array (
	        "partyIdType" => $payer_type,
	        "partyId" => $payer_id
	    ),
	    "payerMessage" => $payer_message,
	    "payeeNote"=> $payer_message
	);
	$postdata_json = json_encode($postdata_array);

	//echo "\n\n postdata_json \n\n";
	//var_dump($postdata_json);

	$header_autorization_id = 'Authorization: Bearer ' . $mtn_api_token;
	$header_callbackurl = 'X-Callback-Url: ' . $call_back_url;
	$header_target_environment = 'X-Target-Environment: ' . $target_environment;
	$header_ref_id = 'X-Reference-Id: ' . $mtn_api_user_UUID;
	$header_content_type = 'Content-Type: application/json';
	$header_mtn_key = 'Ocp-Apim-Subscription-Key: ' . $mtn_key;
	$header_content_length = 'Content-Length: ' . strlen($postdata_json);

	if(trim($callBackUrl) == ""){
		$headers = array(
		    $header_autorization_id,
		    $header_target_environment,
		    $header_ref_id,
		    $header_mtn_key,
		    $header_content_type,
		    $header_content_length
		);	
	} else {
		$headers = array(
		    $header_autorization_id,
		    $header_callbackurl,
		    $header_target_environment,
		    $header_ref_id,
		    $header_mtn_key,
		    $header_content_type,
		    $header_content_length
		);	
	}

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	//curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	//echo "\n\n result \n\n";
	//var_dump($result);

	if($httpcode ==  "202"){
		return "1";		 
	} else {
		return "0";
	}

}// END

function mtnSendMomoTransferRequest($mtn_api_token, $call_back_url, $target_environment, $mtn_api_user_UUID, $mtn_key,  $api_url, $amount, $currency, $external_id, $payer_type, $payer_id, $payer_message){

	$postdata_array = array(
	    "amount" => $amount,
	    "currency" => $currency,
	    "externalId"=> $external_id,
	    "payee" => array (
	        "partyIdType" => $payer_type,
	        "partyId" => $payer_id
	    ),
	    "payerMessage" => $payer_message,
	    "payeeNote"=> $payer_message
	);
	$postdata_json = json_encode($postdata_array);

	//echo "\n\n postdata_json \n\n";
	//var_dump($postdata_json);

	$header_autorization_id = 'Authorization: Bearer ' . $mtn_api_token;
	$header_callbackurl = 'X-Callback-Url: ' . $call_back_url;
	$header_target_environment = 'X-Target-Environment: ' . $target_environment;
	$header_ref_id = 'X-Reference-Id: ' . $mtn_api_user_UUID;
	$header_content_type = 'Content-Type: application/json';
	$header_mtn_key = 'Ocp-Apim-Subscription-Key: ' . $mtn_key;
	$header_content_length = 'Content-Length: ' . strlen($postdata_json);

	if(trim($callBackUrl) == ""){
		$headers = array(
		    $header_autorization_id,
		    $header_target_environment,
		    $header_ref_id,
		    $header_mtn_key,
		    $header_content_type,
		    $header_content_length
		);	
	} else {
		$headers = array(
		    $header_autorization_id,
		    $header_callbackurl,
		    $header_target_environment,
		    $header_ref_id,
		    $header_mtn_key,
		    $header_content_type,
		    $header_content_length
		);	
	}

	$ch = curl_init($api_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	//curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
	//curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	//echo "\n\n result \n\n";
	//var_dump($result);

	if($httpcode ==  "202"){
		return "1";		 
	} else {
		return "0";
	}

}// END


}	