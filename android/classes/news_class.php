<?php
class newsActions {

	function getNewsType($news_type_in_db, $news_text, $news_video_link, $news_image_link, $news_text_detected_url, $news_text_detected_url_image, $news_text_detected_url_video, $return_type_as_number, $advert_icon, $advert_text_title, $advert_text_title2, $advert_button_text, $reposterPottName) {

			$news_type_in_db = trim($news_type_in_db);
			$news_text = trim($news_text);
			$news_video_link = trim($news_video_link);
			$news_image_link = trim($news_image_link);
			$news_text_detected_url = trim($news_text_detected_url);
			$news_text_detected_url_image = trim($news_text_detected_url_image);
			$news_text_detected_url_video = trim($news_text_detected_url_video);
			$advert_icon = trim($advert_icon);
			$advert_text_title = trim($advert_text_title);
			$advert_text_title2 = trim($advert_text_title2);
			$advert_button_text = trim($advert_button_text);
			$reposterPottName = trim($reposterPottName);

			$NEWS_TYPE_28_SHARES4SALE_STORY_HORIZONTAL_KEY = 1;
			$NEWS_TYPE_28_UP4SALE_STORY_HORIZONTAL_KEY = 2;
			$NEWS_TYPE_28_EVENT_STORY_HORIZONTAL_KEY = 3;
			$NEWS_TYPE_28_FUNDRAISER_STORY_HORIZONTAL_KEY = 4;
			$NEWS_TYPE_28_JUSTNEWS_STORY_HORIZONTAL_KEY = 5;

			//VERTICAL NEWS KEYS
			$NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 6;
			$NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 7;
			$NEWS_TYPE_3_TO_4_JUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 8;
			$NEWS_TYPE_5_TO_6_JUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 9;
			$NEWS_TYPE_7_AND_9_JUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 10;
			$NEWS_TYPE_8_JUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 11;
			$NEWS_TYPE_10_UPFORSALENEWS_VERTICAL_KEY = 12;
			$NEWS_TYPE_12_EVENTNEWS_VERTICAL_KEY = 13;
			$NEWS_TYPE_14_SHARESFORSALENEWS_VERTICAL_KEY = 14;
			$NEWS_TYPE_16_FUNDRAISERNEWS_VERTICAL_KEY = 15;
			$NEWS_TYPE_17_SHARES4SALEWITHVIDEO_VERTICAL_KEY = 16;
			$NEWS_TYPE_1_SPONSOREDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 17;
			$NEWS_TYPE_2_SPONSOREDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 18;
			$NEWS_TYPE_3_TO_4_SPONSOREDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 19;
			$NEWS_TYPE_5_TO_6_SPONSOREDJUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 20;
			$NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 21;
			$NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 22;
			$NEWS_TYPE_3_TO_4_REPOSTEDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 23;
			$NEWS_TYPE_5_TO_6_REPOSTEDNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 24;
			$NEWS_TYPE_7_AND_9_REPOSTEDJUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 25;
			$NEWS_TYPE_8_REPOSTEDJUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 26;
			$NEWS_TYPE_10_REPOSTEDUPFORSALENEWS_VERTICAL_KEY = 27;
			$NEWS_TYPE_14_REPOSTEDSHARESFORSALENEWS_VERTICAL_KEY = 28;
			$NEWS_TYPE_17_REPOSTEDSHARES4SALEWITHVIDEO_VERTICAL_KEY = 29;


			// NEWS ORIENTATION KEYS
			$VERTICAL_NEWS_KEY = 110;
			$HORIZONTAL_NEWS_TYPE_28_KEY = 111;
			$HORIZONTAL_NEWS_TYPE_15_KEY = 112;
			$HORIZONTAL_NEWS_TYPE_26_KEY = 113;
			$HORIZONTAL_NEWS_TYPE_11_KEY = 114;
		$news_type_in_db = strtolower($news_type_in_db);

		// Just News With Text Less Than 160
		if (strlen($news_text) <= 200 && ($news_type_in_db == "news" || $news_type_in_db == "") && $news_image_link == "" && $news_video_link == "" && $news_text_detected_url == ""){

			if($advert_icon == "" && $advert_text_title == "" && $advert_text_title2 == "" && $advert_button_text == "" && $reposterPottName == ""){
				if($return_type_as_number){
					return $NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY;
				} else {
					return "Just News With Text Less Than 160";
				}
			} else if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY;
				} else {
					return "Reposted Just News With Text Less Than 160";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_1_SPONSOREDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY;
				} else {
					return "Sponsored Just News With Text Less Than 160";
				}
			}

		} 

		// Just News With Text Less Than 500
		else if (strlen($news_text) > 200 && ($news_type_in_db == "news" || $news_type_in_db == "") && $news_image_link == "" && $news_video_link == "" && $news_text_detected_url == ""){

			if($advert_icon == "" && $advert_text_title == "" && $advert_text_title2 == "" && $advert_button_text == "" && $reposterPottName == ""){
				if($return_type_as_number){
					return $NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY;
				} else {
					return "Just News With Text 500 or more";
				}
			} else if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY;
				} else {
					return "Reposted Just News With Text 500 or more";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_2_SPONSOREDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY;
				} else {
					return "Sponsored Just News With Text 500 or more";
				}
			}


		} 

		// News With Image
		else if (($news_type_in_db == "news" || $news_type_in_db == "") && $news_image_link != "" && $news_video_link == ""){
			if($advert_icon == "" && $advert_text_title == "" && $advert_text_title2 == "" && $advert_button_text == "" && $reposterPottName == ""){
				if($return_type_as_number){
					return $NEWS_TYPE_3_TO_4_JUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "News With Image";
				}
			} else if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_3_TO_4_REPOSTEDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Reposted News With Image";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_3_TO_4_SPONSOREDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Sponsored News With Image";
				}
			}

		} 

		// News With Video
		else if (($news_type_in_db == "news" || $news_type_in_db == "") && $news_video_link != ""){
			if($advert_icon == "" && $advert_text_title == "" && $advert_text_title2 == "" && $advert_button_text == "" && $reposterPottName == ""){
				if($return_type_as_number){
					return $NEWS_TYPE_5_TO_6_JUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "News With Video";
				}
			} else if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_5_TO_6_REPOSTEDNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Reposted News With Image";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_5_TO_6_SPONSOREDJUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Sponsored News With Video";
				}
			}

		} 

		// News With Url With Url Image
		else if (
			($news_type_in_db == "news" || $news_type_in_db == "") && $news_video_link == "" && 
 			$news_image_link == "" && $news_text_detected_url != "" && $news_text_detected_url_image != ""  && 
 			$news_text_detected_url_video == ""
		){
			if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_7_AND_9_REPOSTEDJUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Reposted News With Url With Url Image";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_7_AND_9_JUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "News With Url With Url Image";
				}
			}

		} 

		// News With Url With Url Video
		else if (
			($news_type_in_db == "news" || $news_type_in_db == "") && $news_video_link == "" && 
 			$news_image_link == "" && $news_text_detected_url != "" && $news_text_detected_url_video != ""
		){
			if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_8_REPOSTEDJUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "Reposted News With Url With Url Image";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_8_JUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY;
				} else {
					return "News With Url With Url Video";
				}
			}

		} 

		// up 4 sale
		else if ($news_type_in_db == "up4sale"){
			if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_10_REPOSTEDUPFORSALENEWS_VERTICAL_KEY;
				} else {
					return "Reposted Yard Sale";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_10_UPFORSALENEWS_VERTICAL_KEY;
				} else {
					return "Yard Sale";
				}
			}
		}
	
		else if ($news_type_in_db == "shares4sale" && $news_image_link != "" && $news_video_link == ""){
			if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_14_REPOSTEDSHARESFORSALENEWS_VERTICAL_KEY;
				} else {
					return "Reposted Shares For Sale With Image";
				}
			} else {
				if($return_type_as_number){
						return $NEWS_TYPE_14_SHARESFORSALENEWS_VERTICAL_KEY;
					} else {
						return "Shares For Sale With Image";
					}
				}
		}
	
		else if ($news_type_in_db == "shares4sale" && $news_video_link != ""){
			if($reposterPottName != ""){
				if($return_type_as_number){
					return $NEWS_TYPE_17_REPOSTEDSHARES4SALEWITHVIDEO_VERTICAL_KEY;
				} else {
					return "Reposted Shares For Sale With Video";
				}
			} else {
				if($return_type_as_number){
					return $NEWS_TYPE_17_SHARES4SALEWITHVIDEO_VERTICAL_KEY;
				} else {
					return "Shares For Sale With Video";
				}
			}
		} else {
			return "";
		}


	
	} // END OF getNewsType

	function getNewsBackGroundColor($news_type){

			$NEWS_TYPE_28_SHARES4SALE_STORY_HORIZONTAL_KEY = 1;
			$NEWS_TYPE_28_UP4SALE_STORY_HORIZONTAL_KEY = 2;
			$NEWS_TYPE_28_EVENT_STORY_HORIZONTAL_KEY = 3;
			$NEWS_TYPE_28_FUNDRAISER_STORY_HORIZONTAL_KEY = 4;
			$NEWS_TYPE_28_JUSTNEWS_STORY_HORIZONTAL_KEY = 5;

			//VERTICAL NEWS KEYS
			$NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 6;
			$NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 7;
			$NEWS_TYPE_3_TO_4_JUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 8;
			$NEWS_TYPE_5_TO_6_JUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 9;
			$NEWS_TYPE_7_AND_9_JUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 10;
			$NEWS_TYPE_8_JUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 11;
			$NEWS_TYPE_10_UPFORSALENEWS_VERTICAL_KEY = 12;
			$NEWS_TYPE_12_EVENTNEWS_VERTICAL_KEY = 13;
			$NEWS_TYPE_14_SHARESFORSALENEWS_VERTICAL_KEY = 14;
			$NEWS_TYPE_16_FUNDRAISERNEWS_VERTICAL_KEY = 15;
			$NEWS_TYPE_17_SHARES4SALEWITHVIDEO_VERTICAL_KEY = 16;
			$NEWS_TYPE_1_SPONSOREDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 17;
			$NEWS_TYPE_2_SPONSOREDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 18;
			$NEWS_TYPE_3_TO_4_SPONSOREDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 19;
			$NEWS_TYPE_5_TO_6_SPONSOREDJUSTNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 20;
			$NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY = 21;
			$NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY = 22;
			$NEWS_TYPE_3_TO_4_REPOSTEDJUSTNEWSWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 23;
			$NEWS_TYPE_5_TO_6_REPOSTEDNEWSWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 24;
			$NEWS_TYPE_7_AND_9_REPOSTEDJUSTNEWSWITHURLWITHIMAGEANDMAYBETEXT_VERTICAL_KEY = 25;
			$NEWS_TYPE_8_REPOSTEDJUSTNEWSWITHURLWITHVIDEOANDMAYBETEXT_VERTICAL_KEY = 26;
			$NEWS_TYPE_10_REPOSTEDUPFORSALENEWS_VERTICAL_KEY = 27;
			$NEWS_TYPE_14_REPOSTEDSHARESFORSALENEWS_VERTICAL_KEY = 28;
			$NEWS_TYPE_17_REPOSTEDSHARES4SALEWITHVIDEO_VERTICAL_KEY = 29;


			// NEWS ORIENTATION KEYS
			$VERTICAL_NEWS_KEY = 110;
			$HORIZONTAL_NEWS_TYPE_28_KEY = 111;
			$HORIZONTAL_NEWS_TYPE_15_KEY = 112;
			$HORIZONTAL_NEWS_TYPE_26_KEY = 113;
			$HORIZONTAL_NEWS_TYPE_11_KEY = 114;
		if(
			$news_type == $NEWS_TYPE_1_JUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY
			|| $news_type ==  $NEWS_TYPE_2_JUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY
			|| $news_type ==  $NEWS_TYPE_1_SPONSOREDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY
			|| $news_type ==  $NEWS_TYPE_2_SPONSOREDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY
			|| $news_type ==  $NEWS_TYPE_1_REPOSTEDJUSTNEWSTEXTLESSTHAN160_VERTICAL_KEY
			|| $news_type ==  $NEWS_TYPE_2_REPOSTEDJUSTNEWSTEXT500ORMOREWITHREADMORE_VERTICAL_KEY
		){
            // 0 = black, 1 = red, 2 = yellow, 3 = green, 4 = orange, 5 = blue
			return rand(0,5);
		} else {
			return -1;
		}
	}


}	
