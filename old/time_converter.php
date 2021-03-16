<?php
      $strEnd = date("Y-m-d H:i:s");

      $strStart = trim($strStart);
      $strEnd = trim($strEnd);
      $date_y = date_create($strStart);
      //echo $strStart . "<br>";
      //echo $strEnd; exit;

      $dteStart = new DateTime($strStart); 
      $news_yr = $dteStart->format('Y');
      $news_mth = $dteStart->format('m');
      $news_day = $dteStart->format('d');
      $news_hr = $dteStart->format('H');
      $news_min = $dteStart->format('i');
      $news_sec = $dteStart->format('s');


      $dteEnd   = new DateTime($strEnd);

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



      if($yr_diff > 0) {

        $date_time = date_format($date_y, 'jS F Y');
        //echo date_format($date_y, 'g:ia \o\n l jS F Y'); exit;    //5:53pm on Monday 6th March 2017
      }

      if ($yr_diff == 0 && $mth_diff > 0) {

        $date_time = date_format($date_y, 'jS F');

      }

      if ($yr_diff == 0 && $mth_diff == 0 && $day_diff == 1) {

        $date_time = "Yesterday at " . date_format($date_y, 'g:ia');
        $twelve_hr_date = date_format($date_y, 'g:ia');


      }
       if ($yr_diff == 0 && $mth_diff == 0 && $day_diff == 2) {

        $date_time = "2 days";
        $twelve_hr_date = date_format($date_y, 'jS g:ia');

      }
       if ($yr_diff == 0 && $mth_diff == 0 && $day_diff == 3) {

        $date_time = "3 days";
        $twelve_hr_date = date_format($date_y, 'jS g:ia');
      }
      if ($yr_diff == 0 && $mth_diff == 0 && $day_diff > 3) {

        $date_time = date_format($date_y, 'jS F');
        $twelve_hr_date = date_format($date_y, 'jS g:ia');
      }


      if ($yr_diff == 0 && $mth_diff == 0 && $day_diff == 0) {

          if($hr_diff == 1 && $min_diff < 0) {

            $date_time = (60+$min_diff) . " mins";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 1 && $min_diff > 0) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff > 1 && $min_diff > 0 && ($sec_diff <= 0 || $sec_diff >= 0 )) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff > 1 && $min_diff == 0) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff > 12 && $min_diff > 0) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff > 12 && $min_diff < 0) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff > 1 && $min_diff < 0) {

            $date_time = date_format($date_y, 'g:ia');
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff < 0) {

            $date_time =  $min_diff . " mins";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff == 0 && $sec_diff > 0) {

            $date_time = $sec_diff . "s";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff > 0 && $min_diff <= 1 && $sec_diff < 0) {

            $date_time = (60+$sec_diff) . "s";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff > 1 && $sec_diff < 0) {

            $date_time = ($min_diff - 1) . "mins";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff == 1 && $sec_diff > 0) {

            $date_time = (60 - $sec_diff) . "s";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff <= 0 && $sec_diff < 0) {

            $date_time = (60+$sec_diff) . "s";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff == 0 && $sec_diff == 0) {

            $date_time = "just now";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff >= 1 && $sec_diff > 1) {

            $date_time = $min_diff . "mins";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } elseif ($hr_diff == 0 && $min_diff > 1 && $sec_diff > 1) {

            $date_time = $min_diff . "mins";
            $twelve_hr_date = date_format($date_y, 'g:ia');

          } 

      }

      if(!isset($date_time) || trim($date_time) == ""){
        $date_time = "a while ago";
      }