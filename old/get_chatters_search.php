
<?php
session_start();
$investor_id = $_SESSION["user_sys_id"];
//echo $investor_id;
$config = 1;
require_once("../inc/config.php");
include(ROOT_PATH . 'inc/db_connect_autologout.php');
$new_news = 1;
$keyword = $_GET["kw"];

$table_name = "investor";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');

if(isset($latest_sku) && $latest_sku != "") {


		$latest_sku_investor_tb = $latest_sku;

}


$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

		$query = "SELECT * FROM investor WHERE sku = $i AND first_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		    $row = $result->fetch_array(MYSQLI_ASSOC);
		    $chatter_investor_id = $row["investor_id"];
		    $first_name = $row["first_name"];
		    $last_name = $row["last_name"];
		    $profile_picture = $row["profile_picture"];
		    $skip = 0;
		    $x = 0;
		    if(isset($finds) && count($finds) != 0){

			    foreach ($finds as list($chk_id)) {
		    	
		    		if($chk_id == $chatter_investor_id) {
		    				$x = 1;
		    		}
		    	
		    	}
	    	
		    } elseif(!isset($finds) || count($finds) == 0) {
						    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
						    $finds_cnt = $finds_cnt + 1;

		    } else if($x == 0){

			    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
			    $finds_cnt = $finds_cnt + 1;
		    }


		} else {

		    $skip = 1;

		}

		$query = "SELECT * FROM investor WHERE sku = $i AND last_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		    $chatter_investor_id = $row["investor_id"];
		    $first_name = $row["first_name"];
		    $last_name = $row["last_name"];
		    $profile_picture = $row["profile_picture"];
		    $skip = 0;
		    $x = 0;
		    if(isset($finds) && count($finds) != 0){

			    foreach ($finds as list($chk_id)) {
		    	
		    		if($chk_id == $chatter_investor_id) {
		    				$x = 1;
		    		}
		    	
		    	}
	    	
		    } elseif(!isset($finds) || count($finds) == 0) {
						    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
						    $finds_cnt = $finds_cnt + 1;

		    } else if($x == 0){

			    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
			    $finds_cnt = $finds_cnt + 1;
		    }


		} else {

		    $skip = 1;

		}

		$query = "SELECT * FROM investor WHERE sku = $i AND phone LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		    $chatter_investor_id = $row["investor_id"];
		    $first_name = $row["first_name"];
		    $last_name = $row["last_name"];
		    $profile_picture = $row["profile_picture"];
		    $skip = 0;
		    $x = 0;
		    if(isset($finds) && count($finds) != 0){

			    foreach ($finds as list($chk_id)) {
		    	
		    		if($chk_id == $chatter_investor_id) {
		    				$x = 1;
		    		}
		    	
		    	}
	    	
		    } elseif(!isset($finds) || count($finds) == 0) {
						    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
						    $finds_cnt = $finds_cnt + 1;

		    } else if($x == 0){

			    $finds[$finds_cnt]= array($chatter_investor_id, $first_name, $last_name, $profile_picture);
			    $finds_cnt = $finds_cnt + 1;
		    }


		} else {

		    $skip = 1;

		}


	}

if(!isset($finds) || count($finds) == 0) {

	$finds[0]= array("na", "na", "na", "na");
}

if(isset($finds) && count($finds) != 0 && $finds[0][0] != "na") {


$i = count($finds) - 1;
$j = 0;

 for($j; $j <= $i; $j++) {

 	if($j == 5) {

 		break;
 	}
	if($finds[$j][0] != $investor_id) {

 		$akasakasa_id_1 = $investor_id . $finds[$j][0];
 		$akasakasa_id_2 = $finds[$j][0] . $investor_id;
 		$not_investor_id = $finds[$j][0];
		$query = "SELECT * FROM akasakasa WHERE akasakasa_id = '$akasakasa_id_1' OR akasakasa_id = '$akasakasa_id_2'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		    $akasakasa_id = $row["akasakasa_id"];
		    $latest_kasa_sku = $row["latest_kasa_sku"];
		    $latest_date_time = $row["latest_date_time"];
			    if(trim($latest_date_time) != "2017-06-07 04:31:42"){

			        $strStart = $row["latest_date_time"];

			        include(ROOT_PATH . 'inc/time_converter.php');
			    }
  			include(ROOT_PATH . 'inc/cnt_unread_msg_specific.php');
  		} else {


          $query = "INSERT INTO akasakasa (sku, akasakasa_id, akasakasa_type, sender_id, receiver_id, latest_kasa_sku, latest_date_time) VALUES (NULL, '$akasakasa_id_1', 'auto', '$investor_id', '$not_investor_id', 1, '2017-06-07 04:31:42');";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

  		}

	  }
	} 

}

$i_chats = 0;
foreach ($finds as list($chk_id)) {

	if($chk_id != $investor_id) {


	 		$akasakasa_id_1 = $investor_id . $chk_id;
	 		$akasakasa_id_2 = $chk_id . $investor_id;
	 		$not_investor_id = $chk_id;
			$query = "SELECT * FROM akasakasa WHERE akasakasa_id = '$akasakasa_id_1' OR akasakasa_id = '$akasakasa_id_2'";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {

				$i_chats = $i_chats + 1;
			  	$row = $result->fetch_array(MYSQLI_ASSOC);
			    $akasakasa_id = $row["akasakasa_id"];
			    $latest_kasa_sku = $row["latest_kasa_sku"];
			    $latest_date_time = $row["latest_date_time"];
			    if(trim($latest_date_time) != "2017-06-07 04:31:42"){

			        $strStart = $row["latest_date_time"];

			        include(ROOT_PATH . 'inc/time_converter.php');
			    }
	  			include(ROOT_PATH . 'inc/cnt_unread_msg_specific.php');
		      $table_name = "kasa";

		      $item_1 = "kasa";
		      $item_2 = "sku";

		      $column1_name = "akasakasa_id";

		      $column1_value = $akasakasa_id;

		      $pam1 = "s";
		      $skip = 0;
		      include(ROOT_PATH . 'inc/db_connect_autologout.php');
		      include(ROOT_PATH . 'inc/select2_where1_DESC_prepared_statement.php');
		      include(ROOT_PATH . 'inc/db_connect_autologout.php');

		      if($done == 1 && $skip == 0){

		        $kasa = $item_1;
		        $kasa_sku = $item_2;

		    } else {

		        $kasa = "...";
		        $kasa_sku = $item_2;

		    }
		          $table_name = "investor";

		          $item_1 = "first_name";
		          $item_2 = "last_name";
		          $item_3 = "investor_id";
		          $item_4 = "country";
		          $item_5 = "profile_picture";

		          $column1_name = "investor_id";

		          $column1_value = $not_investor_id;

		          $pam1 = "s";
		          $skip = 0;
		          include(ROOT_PATH . 'inc/db_connect_autologout.php');
		          include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');
		          include(ROOT_PATH . 'inc/db_connect_autologout.php');

		          $chatter_name = $item_1;
					if($done == 1 && $skip == 0){
	  			?>

                <p class="person" onclick="setMsgRceiverId(this);getChatZone(this);" data-chat="personsearch<?php echo $i_chats; ?>"  data-akid = "<?php echo $akasakasa_id; ?>" data-latestkasasku = "<?php echo $latest_kasa_sku; ?>"  data-chatter-id="<?php echo $not_investor_id; ?>" id = "receiver_id_<?php echo $not_investor_id; ?>"  style="padding: 12px 10% 16px;margin:0;border:0;font-size:100%;font:inherit;vertical-align:baseline; cursor: pointer;line-height:1;height: 65px">
                <?php if ($item_5 != "") { ?>
                    <img src="../pic_upload/<?php echo $item_5; ?>" class="w3-left w3-margin-right" style="width: 40px;" alt="<?php echo $chatter_name; ?>">
                <?php } else { ?>    
                    <img src="../img/buddy_sample.png" class="w3-left w3-margin-right" class="w3-left w3-margin-right" style="width: 40px;" alt="<?php echo $chatter_name; ?>">
                <?php } ?>
                    <span class="name"><?php echo $chatter_name; ?></span>
                    <span class="time"><?php if(isset($date_time) && $date_time != "") { echo $date_time;} ?></span>
                    <span class="preview"><?php if(!isset($date_time) || $date_time == "") { echo "...";} ?></span>
                    
                    <span id="unread_msg_spc_<?php echo $akasakasa_id; ?>" data-num = "<?php echo $unread_msg_spf; ?>" class="time w3-badge  w3-small w3-green" style=" margin-top: 20px;<?php if($unread_msg_spf == '' || $unread_msg_spf == 0) { echo ' display: none'; }?>">
                        <strong style="font-size: xx-small; color: white; margin-right: 5px;" >
                          <?php echo $unread_msg_spf; ?>                          
                        </strong>
                    </span>

                </p>
              <?php
          		$date_time = "";}
	  		}				
		}


}
?>