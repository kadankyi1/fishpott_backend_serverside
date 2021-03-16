<?php

if(isset($_GET['getEventAjaxCheck']) && $_GET['getEventAjaxCheck'] == 1) {
session_start();
require_once("config.php");
if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") { 


  include(ROOT_PATH . 'inc/get_fold.php');

  include(ROOT_PATH . 'inc/set_check_login_type.php');

  include(ROOT_PATH . 'inc/id_unfold.php');
  include(ROOT_PATH . 'inc/db_connect.php');
  include(ROOT_PATH . 'inc/get_user_info.php');
} else {

  include(ROOT_PATH . 'inc/db_connect.php');
}
}



  $query = "SELECT * FROM event ORDER BY RAND() LIMIT 1";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $event_id = $row["event_id"];
      $event_news_id = $row["event_news_id"];
      $event_name = $row["event_name"];
      $event_date = $row["event_date"];
      $event_time = $row["event_time"];
      $creater_id = $row["creater_id"];
      $num_of_goers = $row["num_of_goers"];
      $e_verified_tag = $row["verified_tag"];
      $venue = $row["venue"];
      $ticket_cost = $row["ticket_cost"];
      $currency = $row["currency"];
      $event_image = $row["image"];
      $sku = $row["sku"];
      $skip = "no";

      $strStart = $event_date;
      include(ROOT_PATH . 'inc/event_date_passed_chk.php');
      if($evt_coming == 1) { 
        $id_strt = uniqid($event_news_id, TRUE);
        $t = time();
        $r_t = date("Y-m-d",$t);
        $ext = $r_t . $t;
 if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") {

        $fold = $old_fold;
        $login = $old_e_login;
        $u_type = $old_e_u_type;
        $success_url = "https://fishpott.com/user/pay/success.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
        $cancelled_url = "https://fishpott.com/user/pay/deffered.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
        $deferred_url = "https://fishpott.com/user/pay/cancelled.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
 } else {

        $success_url = "https://fishpott.com/user/pay/success.php";
        $cancelled_url = "https://fishpott.com/user/pay/deffered.php";
        $deferred_url = "https://fishpott.com/user/pay/cancelled.php";

 }
        $invoice_id = "event_" . $id_strt . $ext;
        $new_news_id = $event_news_id;
        $type = "event";
        $news_id = $new_news_id;


        if($currency == "GHS") {

            $seller_country = "Ghana";

        } elseif($currency == "GBP") {

            $seller_country = "United Kingdom";

        } else {

            $seller_country = "USA";

        }
if(!isset($_SESSION["user_sys_id"]) || $_SESSION["user_sys_id"] == "") { 

        $i_country = $seller_country;
        
} else {

        $i_country = $i_country;
}

        $convert_amt = $ticket_cost;
        include(ROOT_PATH . 'inc/currency_converter.php');
      if(isset($_GET['getEventAjaxCheck']) && $_GET['getEventAjaxCheck'] == 1) {

        $getNewEventStatus  = array(

          'ajax_getEvent_status' => 1,
          'ajax_event_news_id' => $event_news_id,
          'ajax_event_id' => $event_id,
          'ajax_event_name' => $event_name,
          'ajax_event_date' => $event_date,
          'ajax_event_time' => $event_time,
          'ajax_event_creater_id' => $creater_id,
          'ajax_event_num_of_goers' => $num_of_goers,
          'ajax_event_verified_tag' => $e_verified_tag,
          'ajax_event_venue' => $venue,
          'ajax_event_ticket_cost_pg' => $new_amt_pg,
          'ajax_event_ticket_cost_usr' => $new_amt_user_str,
          'ajax_event_success_url' => $success_url,
          'ajax_event_cancelled_url' => $cancelled_url,
          'ajax_event_deferred_url' => $deferred_url,
          'ajax_event_invoice_id' => $invoice_id,
          'ajax_event_currency' => $currency,
          'ajax_event_event_image' => $event_image
          );

        echo json_encode($getNewEventStatus,JSON_UNESCAPED_SLASHES); //exit;

      }

  } else {

      $skip = "yes";

      if(isset($_GET['getEventAjaxCheck']) && $_GET['getEventAjaxCheck'] == 1) {

        $getNewEventStatus  = array(
          'ajax_getEvent_status' => 0
          );
        echo json_encode($getNewEventStatus,JSON_UNESCAPED_SLASHES); //exit;


      }
  }
} else {

      $skip = "yes";

      if(isset($_GET['getEventAjaxCheck']) && $_GET['getEventAjaxCheck'] == 1) {

        $getNewEventStatus  = array(
          'ajax_getEvent_status' => 0
          );
        echo json_encode($getNewEventStatus,JSON_UNESCAPED_SLASHES); //exit;


      }
  }

















