<?php

class timeOperator {

	private $news_sec = -1;

	public function startTimeCounter() {
		//$startTime = microtime(true);
		$strStart = date("Y-m-d H:i:s");
      	$dteStart = new DateTime($strStart); 
      	$news_sec = $dteStart->format('s');

	} // END OF startTimeCounter

	public function endTimeCounter(){
		if($news_sec != -1){
	      	$strEnd = date("Y-m-d H:i:s");
	      	$dteEnd   = new DateTime($strEnd);
	      	$now_sec = $dteEnd->format('s');
	      	$sec_diff = ($now_sec - $news_sec)/10; 
			return $sec_diff;
		} else {
			return -1;
		}

	} // END OF endTimeCounter

	public function getDateDifference($start_datetime, $end_datetime) {

		if(trim($start_datetime) == "" || trim($end_datetime) == ""){
			return false;
		}
			$dteStart = new DateTime($start_datetime); 
			$news_yr = $dteStart->format('Y');
			$news_mth = $dteStart->format('m');
			$news_day = $dteStart->format('d');
			$news_hr = $dteStart->format('H');
			$news_min = $dteStart->format('i');
			$news_sec = $dteStart->format('s');


			$dteEnd   = new DateTime($end_datetime);

			$now_yr = $dteEnd->format('Y');
			$now_mth = $dteEnd->format('m');
			$now_day = $dteEnd->format('d');
			$now_hr = $dteEnd->format('H');
			$now_min = $dteEnd->format('i');
			$now_sec = $dteEnd->format('s');

			$yr_diff = $now_yr - $news_yr;
			$mth_diff = $now_mth - $news_mth;
			$day_diff = $now_day - $news_day; 
			$hr_diff = $now_hr - $news_hr; 
			$min_diff = $now_min - $news_min; 
			$sec_diff = $now_sec - $news_sec; 

			$date_difference_array = array('year_difference' => $yr_diff,'month_difference' => $mth_diff, 
												'day_difference' => $day_diff,'hour_difference' => $hr_diff,
												'minute_difference' => $min_diff,'second_difference' => $sec_diff);

			return $date_difference_array;

	} // END OF getDateDifference

	public function getDateBeforeOrAfterGivenNumberOfTime($number_of_time_unit, $time_unit, $date_format){

		$date_skip_string = $number_of_time_unit . " " . $time_unit;

		$date = new DateTime($date_skip_string);
		$date = $date->format($date_format);

		return $date;
	} // END OF getDateOfTimeBehindGivenDate

	public function getTimeElapsedSstring($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'year',
	        'm' => 'month',
	        'w' => 'week',
	        'd' => 'day',
	        'h' => 'hour',
	        'i' => 'minute',
	        's' => 'second',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	public function getDaysPassed($start_datetime, $end_datetime) {

		$now = strtotime($end_datetime);
		$your_date = strtotime($start_datetime);
		$datediff = $now - $your_date;

		return round($datediff / (60 * 60 * 24));
	} // END

	public function reformatDate($date_format, $input_date){

		return date($date_format, strtotime($input_date));
	}

	public function getNewDateAfterNumberOfDays($date, $number_of_days, $return_date_format) {

		$date = strtotime($date);
		//$date = strtotime("+7 day", $date);
		$date = strtotime($number_of_days, $date);
		//return date('M d, Y', $date);
		return date($return_date_format, $date);

	} // END

}	