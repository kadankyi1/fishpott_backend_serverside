<?php
session_start();
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');
$investor_id = $_SESSION["user_sys_id"];

if(isset($_GET["akasakasa_id"]) && $_GET["akasakasa_id"] != ""){

	$cur_akasa_id = $_GET["akasakasa_id"];

} else {

	$cur_akasa_id = "na";
}

$query = "SELECT sku FROM kasa WHERE akasakasa_id = '$cur_akasa_id' ORDER BY sku DESC";

//$numrows = mysql_num_rows($query);
$result = $mysqli->query($query);

if (mysqli_num_rows($result) != "0") {

  $row = $result->fetch_array(MYSQLI_ASSOC);
  $latest_unread_msg_sku = $row["sku"];
  $skip = 0;
} else {

  $skip = 1;

}


$query = "SELECT sku FROM kasa WHERE akasakasa_id = '$cur_akasa_id' AND kaka_nake_status = 0 ORDER BY sku ASC";

//$numrows = mysql_num_rows($query);
$result = $mysqli->query($query);

if (mysqli_num_rows($result) != "0") {

  $row = $result->fetch_array(MYSQLI_ASSOC);
  $earliest_unread_msg_sku = $row["sku"];
  $skip = 0;
} else {

  $skip = 1;

}

	//echo "cur_akasa_id : " .  $cur_akasa_id . "\n";
	//echo "latest_unread_msg_sku : " .  $latest_unread_msg_sku . "\n";
	//echo "earliest_unread_msg_sku : " .  $earliest_unread_msg_sku . "\n";
	//echo "skip : " .  $skip . "\n";

$msg_cnt = 0;
if($skip == 0){
	for ($latest_unread_msg_sku; $latest_unread_msg_sku >= $earliest_unread_msg_sku; $latest_unread_msg_sku--) {

	        $table_name = "kasa";
	        $item_1 = "kasa";
	        $item_2 = "kasa_date_time";
	        $item_3 = "status";
	        $item_4 = "sender_id";
	        $item_5 = "kasa_pic";
	        $item_6 = "kasa_vid";
	        $item_7 = "kasa_aud";
	        $item_8 = "kaka_nake_status";

	        $column1_name = "sku";
	        $column1_value = $latest_unread_msg_sku;

	        $column2_name = "akasakasa_id";
	        $column2_value = $cur_akasa_id;

	        $pam1 = "i";
	        $pam2 = "s";
	        $skip = 1;
	        include(ROOT_PATH . 'inc/db_connect_autologout.php');
	        include(ROOT_PATH . 'inc/select8_where2_prepared_statement.php');
	        include(ROOT_PATH . 'inc/db_connect_autologout.php');

	//echo "item_4 : " .  $item_4 . "\n";

	        if($done == 1 && $item_1 != "kasa" && $item_1 != "" && $skip == 0 && $item_4 != $investor_id){

			      $bubble_type = "you";


			      $strStart = $item_2;

			      include(ROOT_PATH . 'inc/time_converter.php');

			       $msgs[$msg_cnt]["bubble_type"] = $bubble_type;
			       $msgs[$msg_cnt]["this_msg"] = $item_1;
			       $msgs[$msg_cnt]["pic"] = $item_5;
			       $msgs[$msg_cnt]["vid"] = $item_6;
			       $msgs[$msg_cnt]["aud"] = $item_7;
			       $msgs[$msg_cnt]["status"] = $item_3;
			       $msgs[$msg_cnt]["sku"] = $latest_unread_msg_sku;

			      if(isset($twelve_hr_date) && $twelve_hr_date != "") {

			             $msgs[$msg_cnt]["date"] = $twelve_hr_date; 

			           } else { 

			            $msgs[$msg_cnt]["date"] = $date_time;

			          }                 
			            $msg_cnt = $msg_cnt + 1;
			} 
	}


	if(isset($msgs)){
		$max_msg = count($msgs);
		$max_msg =  $max_msg - 1;
	} else {

			echo 0; exit;
	}
	$showed_msgs = 0;
	$not_showed_msgs = $max_msg;

	//echo "cur_akasa_id : " .  $cur_akasa_id . "\n";
	//echo "latest_unread_msg_sku : " .  $latest_unread_msg_sku . "\n";
	//echo "earliest_unread_msg_sku : " .  $earliest_unread_msg_sku . "\n";
	//echo "messages \n"; var_dump($msgs); 
	//echo "max_msg : " .  $max_msg . "\n";
	//exit;

	for($max_msg; $max_msg >= 0; $max_msg--) {

	    $bubble_type =  $msgs[$max_msg]["bubble_type"];
	    $item_1  =  $msgs[$max_msg]["this_msg"];
	    $item_5  = $msgs[$max_msg]["pic"];
	    $item_6  = $msgs[$max_msg]["vid"];
	    $item_7  = $msgs[$max_msg]["aud"];
	    $item_3  = $msgs[$max_msg]["status"];
	    $sku  = $msgs[$max_msg]["sku"];
	    $showed_msgs = $showed_msgs +1;
	?>
	          <?php echo $item_1; ?><br>

	          <?php if(trim($item_5) != "") { ?>
	            <a href="<?php echo $item_5; ?>" data-lightbox="<?php echo 'msg_image' . $msg_cnt ?>" data-title="<?php echo $item_1; ?>"><img style="width: 130px; height: 130px" src="<?php echo $item_5; ?>"></a>
	          <?php } ?>
	          <?php if(isset($item_6) && trim($item_6) != "") { ?>
	            <video width="640px" height="267px" controls style=" width:100%;">
	              <source src="<?php echo $item_6; ?>" type="video/mp4">
	              <source src="<?php echo $item_6; ?>" type="video/ogg">
	              Your browser does not support HTML5 video.
	            </video>
	          <?php } ?><br>
	          <?php if(isset($item_7) && trim($item_7) != "") { ?>
	            <audio controls>
	              <source src="<?php echo $item_7; ?>" type="audio/ogg">
	              <source src="<?php echo $item_7; ?>" type="audio/mpeg">
	            Your browser does not support the audio element.
	            </audio>                      
	          <?php } ?><br>
	          <?php if(isset($item_3) && $item_3 == 1000) { ?>
	          <i class="fa fa-check" id="<?php echo $cur_akasa_id . '_' . $i_msg;?>" aria-hidden="true" style="height: 20px; font-size: x-small; float: right; 
	          color: 
	          <?php if($item_3 == 0) {  echo "#000000"; } 
	          elseif($item_3 == 1) {  echo "#208000"; } 
	          else {  echo "#4d94ff"; } ?>">
	            
	          </i>
	          <?php } ?>
	          <span style="height: 20px; float: left; color: #000000; font-size: x-small;"><?php if(isset($twelve_hr_date) && $twelve_hr_date != "") { echo $twelve_hr_date; } else { echo $date_time;} ?></span><br>
<?php

	}
}

?>