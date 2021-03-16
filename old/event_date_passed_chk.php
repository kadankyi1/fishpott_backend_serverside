<?php

$today_num = date('z');


include(ROOT_PATH . 'inc/time_converter.php'); 

// you can pass any date to the strtotime function
$evt_day_num = date('z', strtotime($strStart));

if($news_yr > $now_yr) {

	$evt_coming = 1;

} elseif($news_yr == $now_yr) {

	if($evt_day_num >= $today_num) {

			$evt_coming = 1;

	} else {

			$evt_coming = 0;

	}
} else {

	$evt_coming = 0;

}
