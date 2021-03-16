<?php

function check_https($url){
$ch = curl_init ('https://'.$url);

curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, 'HEAD'); //its a  HEAD
curl_setopt ($ch, CURLOPT_NOBODY, true);          // no body

curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);  // in case of redirects
curl_setopt ($ch, CURLOPT_VERBOSE,        0); //turn on if debugging
curl_setopt ($ch, CURLOPT_HEADER,         1);     //head only wanted

curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 10);    // we dont want to wait forever

curl_exec ( $ch ) ;

$header = curl_getinfo($ch,CURLINFO_HTTP_CODE);
//var_dump ($header);

if($header===0){//no ssl
return false;
}else{//maybe you want to check for 200
return true;
}

}


					//$full_url = "https://www.cbc.ca/amp/1.4790394";

					//$full_url = "http://www.ferryglobal.com";

					//$full_url = "https://www.myjoyonline.com/news/2018/august-18th/former-un-secretary-general-kofi-annan-has-died.php";

					$full_url = "https://www.myjoyonline.com/news/2018/august-17th/fatal-friday-dawn-road-crash-claims-life-of-young-lady.php";

					$doc = new DOMDocument();
					@$doc->loadHTMLFile($full_url);
					$xpath = new DOMXPath($doc);
					$url_title =  $xpath->query('//title')->item(0)->nodeValue;  
						//$url="http://assemblynewsgh.com/gallery.php";

							$handle = curl_init($my_url_mentions);
							curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

							$response = curl_exec($handle);

							$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
							if($httpCode == 403) {


			                	$url_card_tick = "";
								$my_url_mentions = "";
								$url_title = "";
								$url_image = "";

							curl_close($handle);
							} else {

							$html = file_get_contents($full_url);
							//echo "<br>\nHTML : " .  htmlspecialchars($html);
							curl_close($handle);

						///////////////////////
						

						$doc = new DOMDocument();
						@$doc->loadHTML($html);

						$tags = $doc->getElementsByTagName('img');

					$size_array = array(); // create an new empty array
					$src_array = array(); // create an new empty array

					$count = 0;
					foreach ($tags as $tag) {	

						   $image_src = $tag ->getAttribute('src');
						   $image_src = trim($image_src);
						if($image_src != ""){

						   if(substr($image_src, 0, 7) != "http://" && substr($image_src, 0, 8) != "https://"){


							   $urlIshttps =  check_https($image_src);

								$r = parse_url($full_url);
								$image_src = $r["scheme"] . "://" . $r["host"] . "/" . $image_src;

						   }

						   $size_img = getimagesize($image_src);


					       $size_array[$count] = $size_img[0];
					       $src_array[$count] = $image_src;
					       $count++;

					       //assign size as key and path as value to the newly created array


						}
					}

					if(count($size_array) > 0){

						$max_size = max($size_array); // get max size from keys array
						$key = array_search($max_size, $size_array);
						$url_image = $src_array[$key];

					} else {


			                	$url_card_tick = "";
								$my_url_mentions = "";
								$url_title = "";
								$url_image = "";

					}
			}
		       echo "\n<br> url_image : " . $url_image;
