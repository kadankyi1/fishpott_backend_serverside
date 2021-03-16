<?php
require_once("config.php");
$cnt = 0;

$table_name = "investor";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		
}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT first_name, last_name, investor_id, profile_picture FROM investor WHERE sku = $i AND first_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $profile_picture = $row["profile_picture"];
		  $first_name = $row["first_name"];
		  $inputtor_id = $row["investor_id"];
		  $last_name = $row["last_name"];
	      $skip = 0;
	      $finds[$finds_cnt]= array($inputtor_id, $first_name, $last_name, $profile_picture);
	      $finds_cnt = $finds_cnt + 1;
	      continue;
		} else {

		    $skip = 1;

		}

		$query = "SELECT  first_name, last_name, investor_id, profile_picture FROM investor WHERE sku = $i AND last_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $profile_picture = $row["profile_picture"];
		  $first_name = $row["first_name"];
		  $inputtor_id = $row["investor_id"];
		  $last_name = $row["last_name"];
	      $finds[$finds_cnt]= array($inputtor_id, $first_name, $last_name, $profile_picture);
		  $finds_cnt = $finds_cnt + 1;
		  $skip = 0;
		  continue;

		} else {

		    $skip = 1;

		}


		$query = "SELECT  first_name, last_name, investor_id, profile_picture FROM investor WHERE sku = $i AND phone LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $profile_picture = $row["profile_picture"];
		  $first_name = $row["first_name"];
		  $inputtor_id = $row["investor_id"];
		  $last_name = $row["last_name"];
	      $finds[$finds_cnt]= array($inputtor_id, $first_name, $last_name, $profile_picture);
		  $finds_cnt = $finds_cnt + 1;
		  $skip = 0;
		  continue;

		} else {

		    $skip = 1;

		}

		$query = "SELECT  first_name, last_name, investor_id, profile_picture FROM investor WHERE sku = $i AND pot_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $profile_picture = $row["profile_picture"];
		  $first_name = $row["first_name"];
		  $inputtor_id = $row["investor_id"];
		  $last_name = $row["last_name"];
	      $finds[$finds_cnt]= array($inputtor_id, $first_name, $last_name, $profile_picture);
	      $finds_cnt = $finds_cnt + 1;
	      $skip = 0;
	      continue;

		} else {

		    $skip = 1;

		}


}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
<div class="w3-row-padding" style="margin-top: 10px;" id="people_sec">
<?php for($i; $i >= 0; $i--) { ?>

        
        <div class="w3-col m12" style="margin-top: 10px; width: 100%;">
        	<div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">
              	<a class="w3-col m12" href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][0]; ?>" style=" text-decoration: none; width: 70%">
				  <?php if ($finds[$i][3] != "") { ?>
			        <img src="../pic_upload/<?php echo $finds[$i][3]; ?>" alt="Avatar" class="w3-left w3-margin-right" style="width:65px">
			      <?php } else { ?>
			        <img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:65px">
			      <?php } ?>
			       <strong style="font-size: 14px"><?php echo $finds[$i][1] . " " . $finds[$i][2]; ?></strong>
       			</a>
        <span class="w3-right w3-opacity">Pott</span><br><br>
<?php $p_investor_id = $finds[$i][0];  include(ROOT_PATH . 'inc/check_linkup_status.php');  ?>

      <?php if($status == 2) { ?>

        <button style="float: right; color: green; font-size: small;" value="<?php echo $p_investor_id; ?>" class="w3-btn w3-grey w3-round"  onclick="sendLinkUp(this)"><span> Send Link Up <span></button>

      <?php } elseif($status == 1) { ?>
        <button style="float: right; color: green; font-size: small;" value="<?php echo $p_investor_id; ?>" class="w3-btn w3-white w3-round"  onclick="sendLinkUp(this)"><i class="fa fa-handshake-o w3-margin-right"></i><span> You Are Linked Up <span></button>

      <?php } elseif($status == 0) { ?>
        <button style="float: right; color: green; font-size: small;" value="<?php echo $p_investor_id; ?>" class="w3-btn w3-white w3-round"  onclick="sendLinkUp(this)"><span><span style="float: right; color: green; font-size: small;"><strong>Link Up Request Sent</strong></span>
<span></button>
      <?php }  ?>
      </div>
      </div>
      </div>

<?php	} ?>
      </div>
	</a>
<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;" id="people_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... People Weren't Found</div>
            </div>
        </div>
      </div>

<?php } ?>

<!-- ///////////////////////////////////////// PEOPLE SECTION END //////////////////////////// -->



<?php 

$table_name = "newsfeed";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT news_id, inputtor_id FROM newsfeed WHERE sku = $i AND news LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $news_id = $row["news_id"];
		  $inputtor_id = $row["inputtor_id"];
	      $skip = 0;
	      $finds[$finds_cnt]= array($news_id, $inputtor_id);
	      $finds_cnt = $finds_cnt + 1;
	      continue;
		} else {

		    $skip = 1;

		}

}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="news_sec">
<?php for($i; $i >= 0; $i--) { ?>

    <?php $new_news = 1; $new_news_id = $finds[$i][0]; include(ROOT_PATH . 'inc/get_news_set_skip.php'); ?>
	<?php   include(ROOT_PATH . 'inc/newsfeed_section.php'); ?>
<?php } ?>
	</div>
<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="news_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... We Found No News</div>
            </div>
        </div>
      </div>

<?php } ?>


<!-- ///////////////////////////////////////// NEWSFEED SECTION END //////////////////////////// -->



<?php 

$table_name = "event";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT event_news_id, creater_id FROM event WHERE sku = $i AND event_name LIKE '%$keyword%'";

		//$numrows = mysql_num_rows($query);
		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $event_news_id = $row["event_news_id"];
		  $creater_id = $row["creater_id"];
	      $skip = 0;
	      $finds[$finds_cnt]= array($event_news_id, $creater_id);
	      $finds_cnt = $finds_cnt + 1;
	      continue;
		} else {

		    $skip = 1;

		}



}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="events_sec">
<?php for($i; $i >= 0; $i--) { ?>

    <?php $new_news = 1; $new_news_id = $finds[$i][0]; include(ROOT_PATH . 'inc/get_news_set_skip.php'); ?>
	<?php   include(ROOT_PATH . 'inc/newsfeed_section.php'); ?>
<?php } ?>
	</div>
<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="events_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... We Found No News</div>
            </div>
        </div>
      </div>

<?php } ?>


<!-- ///////////////////////////////////////// EVENTS SECTION END //////////////////////////// -->


<?php 

$table_name = "shares_owned";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT parent_shares_id, share_name, shares_logo FROM shares_worso WHERE sku = $i AND share_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {
		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $parent_shares_id = $row["parent_shares_id"];
		  $share_name = $row["share_name"];
		  $shares_logo = $row["shares_logo"];

			$query = "SELECT shares4sale_owner_id, sharesOnSale_id, selling_price, num_on_sale, sale_status, currency, shares_news_id FROM shares4sale WHERE parent_shares_id = '$parent_shares_id'";

			$result = $mysqli->query($query);

			if (mysqli_num_rows($result) != "0") {

			  $row = $result->fetch_array(MYSQLI_ASSOC);
			  $shares4sale_owner_id = $row["shares4sale_owner_id"];
			  $sharesOnSale_id = $row["sharesOnSale_id"];
			  $selling_price = $row["selling_price"];
			  $num_on_sale = $row["num_on_sale"];
			  $sale_status = $row["sale_status"];
			  $currency = $row["currency"];
			  $shares_news_id = $row["shares_news_id"];

			  if($sale_status == 0){
				$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$shares4sale_owner_id'";

				$result = $mysqli->query($query);

				if (mysqli_num_rows($result) != "0") {

				  $row = $result->fetch_array(MYSQLI_ASSOC);
				  $first_name = $row["first_name"];
				  $last_name = $row["last_name"];
				  $profile_picture = $row["profile_picture"];

$finds[$finds_cnt]= array($parent_shares_id, $share_name, $shares_logo, $shares4sale_owner_id, $sharesOnSale_id, $selling_price, $num_on_sale, $first_name, $last_name, $profile_picture, $currency, $shares_news_id);
				  $finds_cnt = $finds_cnt + 1;
				  continue;

				}
			  }
			}
		}

}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
   <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="shares4sale_sec">
	<?php for($i; $i >= 0; $i--) { ?>

        <div class="w3-col m12" style="margin-top: 10px; width: 100%;">
        	<div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">
              	<a class="w3-col m12" href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][11]; ?>" style=" text-decoration: none; width: 50%; text-align: center;">
				  <?php if ($finds[$i][2] != "") { ?>
			        <img src="../user/<?php echo $finds[$i][2]; ?>" alt="Avatar" class="w3-left w3-margin-right" style="width: 100%; ">
			      <?php } else { ?>
			        <img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:65px">
			      <?php } ?>
			      </a>

			       <strong style="font-size: 14px; width: 100%; ">
              	<a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][11]; ?>" style=" text-decoration: none; color: #ff751a">
              	<?php echo $finds[$i][1]; ?></a><br><?php echo $finds[$i][6]; ?><span style="color: grey; font-size: x-small;"> available</span><br><?php echo $finds[$i][5] . " " . $finds[$i][10]; ?> per share <br><a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][3]; ?>" style=" text-decoration: none;"><?php echo $finds[$i][7] . " " . $finds[$i][8]; ?><span style="color: grey; font-size: x-small;"> seller</span></a></strong>
       			
       			<br>
              </div>
      </div>
      </div>
<?php	} ?>

      </div>

<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="shares4sale_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... No Pott Was Found</div>
            </div>
        </div>
      </div>
<?php } ?>

<!-- ///////////////////////////////////////// SHARES4SALE SECTION END //////////////////////////// -->

<?php 

$table_name = "up4sale";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT up4sale_news_id, seller_id, item_name, item_price, currency, item_location, item_delivery, sale_status, item_description FROM up4sale WHERE sku = $i AND item_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $up4sale_news_id = $row["up4sale_news_id"];
		  $item_name = $row["item_name"];
		  $item_price = $row["item_price"];
		  $currency = $row["currency"];
		  $item_location = $row["item_location"];
		  $item_delivery = $row["item_delivery"];
		  $sale_status = $row["sale_status"];
		  $item_description = $row["item_description"];
		  $seller_id = $row["seller_id"];

			  if($sale_status == 0){

				$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$seller_id'";

				if (mysqli_num_rows($result) != "0") {

				  $result = $mysqli->query($query);
				  $row = $result->fetch_array(MYSQLI_ASSOC);
				  $first_name = $row["first_name"];
				  $last_name = $row["last_name"];
				  $profile_picture = $row["profile_picture"];
				  
						$query = "SELECT news_image, news_aud, news_video FROM newsfeed WHERE news_id = '$up4sale_news_id'";

						$result = $mysqli->query($query);

						if (mysqli_num_rows($result) != "0") {

						  $row = $result->fetch_array(MYSQLI_ASSOC);
						  $news_image = $row["news_image"];
						  $news_aud = $row["news_aud"];
						  $news_video = $row["news_video"];

$finds[$finds_cnt]= array($up4sale_news_id, $item_name, $item_price, $currency, $item_location, $item_delivery, $item_delivery, $sale_status, $item_description, $seller_id, $first_name, $last_name, $profile_picture, $news_image, $news_aud, $news_video);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
					  }



			  }
			}
		}

$query = "SELECT up4sale_news_id, seller_id, item_name, item_price, currency, item_location, item_delivery, sale_status, item_description FROM up4sale WHERE sku = $i AND item_description LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $up4sale_news_id = $row["up4sale_news_id"];
		  $item_name = $row["item_name"];
		  $item_price = $row["item_price"];
		  $currency = $row["currency"];
		  $item_location = $row["item_location"];
		  $item_delivery = $row["item_delivery"];
		  $sale_status = $row["sale_status"];
		  $item_description = $row["item_description"];
		  $seller_id = $row["seller_id"];

			  if($sale_status == 0){

				$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$seller_id'";

				if (mysqli_num_rows($result) != "0") {

				  $result = $mysqli->query($query);
				  $row = $result->fetch_array(MYSQLI_ASSOC);
				  $first_name = $row["first_name"];
				  $last_name = $row["last_name"];
				  $profile_picture = $row["profile_picture"];
				  
						$query = "SELECT news_image, news_aud, news_video FROM newsfeed WHERE news_id = '$up4sale_news_id'";

						$result = $mysqli->query($query);

						if (mysqli_num_rows($result) != "0") {

						  $row = $result->fetch_array(MYSQLI_ASSOC);
						  $news_image = $row["news_image"];
						  $news_aud = $row["news_aud"];
						  $news_video = $row["news_video"];

$finds[$finds_cnt]= array($up4sale_news_id, $item_name, $item_price, $currency, $item_location, $item_delivery, $item_delivery, $sale_status, $item_description, $seller_id, $first_name, $last_name, $profile_picture, $news_image, $news_aud, $news_video);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
					  }



			  }
			}
		}
}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
   <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="up4sale_sec">
	<?php for($i; $i >= 0; $i--) { ?>

        <div class="w3-col m12" style="margin-top: 10px; width: 100%;">
        	<div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">
              	<a class="w3-col m12" href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][0]; ?>" style=" text-decoration: none; width: 30%; text-align: center; margin-right: 10px;">
				  <?php if ($finds[$i][13] != "") { ?>
			        <img src="../user/<?php echo $finds[$i][13]; ?>" alt="Avatar" class="w3-left w3-margin-right" style="width: 100%; ">
			      <?php } elseif ($finds[$i][13] == "" && $finds[$i][15] != "") { ?>
			          <video width="640px" height="267px" controls style=" width:100%;">
			            <source src="<?php echo $finds[$i][15]; ?>" type="video/mp4">
			            <source src="<?php echo $finds[$i][15]; ?>" type="video/ogg">
			            Your browser does not support HTML5 video.
			          </video>
			      <?php } elseif ($finds[$i][13] == "" && $finds[$i][15] == "" && $finds[$i][14] != "") { ?>
			          <audio controls>
			            <source src="<?php echo $finds[$i][14]; ?>" type="audio/ogg">
			            <source src="<?php echo $finds[$i][14]; ?>" type="audio/mpeg">
			            Your browser does not support the audio tag.
			          </audio>        
			      <?php } else { ?>
			        <img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:65px">
			      <?php } ?>
			      </a>

			       <strong style="font-size: 14px; width: 100%; ">
              	<a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][0]; ?>" style=" text-decoration: none; color: #4CAF50">
              	<?php echo $finds[$i][1]; ?></a><br><?php echo $finds[$i][2] . " " . $finds[$i][3]; ?> <br><a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][9]; ?>" style=" text-decoration: none;"><?php echo $finds[$i][10] . " " . $finds[$i][11]; ?><span style="color: grey; font-size: x-small;"> seller</span></a><br><?php echo $finds[$i][4]; ?><span style="color: grey; font-size: x-small;"> description</span><br></strong>
       			
       			<br>
              </div>
      </div>
      </div>
<?php	} ?>

      </div>

<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="up4sale_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... No Pott Was Found</div>
            </div>
        </div>
      </div>
<?php } ?>

<!-- ///////////////////////////////////////// UP4SALE SECTION END //////////////////////////// -->

<?php 

$table_name = "fundraiser";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT f_news_id, f_starter_id, fundraiser_id, fundraiser_name, start_date, target_amount, num_of_contributors, contributed_amount, currency FROM fundraiser WHERE sku = $i AND fundraiser_name LIKE '%$keyword%'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $f_news_id = $row["f_news_id"];
		  $f_starter_id = $row["f_starter_id"];
		  $fundraiser_id = $row["fundraiser_id"];
		  $fundraiser_name = $row["fundraiser_name"];
		  $start_date = $row["start_date"];
		  $target_amount = $row["target_amount"];
		  $num_of_contributors = $row["num_of_contributors"];
		  $contributed_amount = $row["contributed_amount"];
		  $currency = $row["currency"];

				$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$f_starter_id'";

				if (mysqli_num_rows($result) != "0") {

				  $result = $mysqli->query($query);
				  $row = $result->fetch_array(MYSQLI_ASSOC);
				  $first_name = $row["first_name"];
				  $last_name = $row["last_name"];
				  $profile_picture = $row["profile_picture"];
				  
						$query = "SELECT news_image, news_aud, news_video FROM newsfeed WHERE news_id = '$f_news_id'";

						$result = $mysqli->query($query);

						if (mysqli_num_rows($result) != "0") {

						  $row = $result->fetch_array(MYSQLI_ASSOC);
						  $news_image = $row["news_image"];
						  $news_aud = $row["news_aud"];
						  $news_video = $row["news_video"];

$finds[$finds_cnt]= array($f_news_id, $fundraiser_name, $contributed_amount, $currency, $num_of_contributors, $f_starter_id, $start_date, $target_amount, $first_name, $last_name, $profile_picture, $news_image, $news_aud, $news_video);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
					  }
			}
		}


}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
   <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="fundraiser_sec">
	<?php for($i; $i >= 0; $i--) { ?>

        <div class="w3-col m12" style="margin-top: 10px; width: 100%;">
        	<div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">
              	<a class="w3-col m12" href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][0]; ?>" style=" text-decoration: none; width: 30%; height: 100%; text-align: center; margin-right: 10px;">
				  <?php if ($finds[$i][11] != "") { ?>
			        <img src="../user/<?php echo $finds[$i][11]; ?>" alt="Avatar" class="w3-left w3-margin-right" style="width: 100%; height: 100%; ">
			      <?php } elseif ($finds[$i][11] == "" && $finds[$i][13] != "") { ?>
			          <video width="640px" height="267px" controls style=" width:100%;">
			            <source src="<?php echo $finds[$i][13]; ?>" type="video/mp4">
			            <source src="<?php echo $finds[$i][13]; ?>" type="video/ogg">
			            Your browser does not support HTML5 video.
			          </video>
			      <?php } elseif ($finds[$i][11] == "" && $finds[$i][13] == "" && $finds[$i][12] != "") { ?>
			          <audio controls>
			            <source src="<?php echo $finds[$i][12]; ?>" type="audio/ogg">
			            <source src="<?php echo $finds[$i][12]; ?>" type="audio/mpeg">
			            Your browser does not support the audio tag.
			          </audio>        
			      <?php } else { ?>
			        <img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:65px">
			      <?php } ?>
			      </a>

			       <strong style="font-size: 14px; width: 100%; ">
              	<a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][0]; ?>" style=" text-decoration: none; color: #2196F3">
              	<?php echo $finds[$i][1]; ?></a><br>
              	<?php echo $finds[$i][2] . " " . $finds[$i][3]; ?><br>
<a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $finds[$i][9]; ?>" style=" text-decoration: none;">
              	<?php echo $finds[$i][8] . " " . $finds[$i][9]; ?><span style="color: grey; font-size: x-small;"> fundraiser starter</span>
</a>
              	<?php if($finds[$i][4] > 0) { ?><br><?php echo $finds[$i][2] . " " . $finds[$i][3]; ?><span style="color: grey; font-size: x-small;"> contributions made</span><br><?php echo $finds[$i][4]; ?><span style="color: grey; font-size: x-small;"> people have contributed</span><?php } ?><br></strong>
       			
       			<br>
              </div>
      </div>
      </div>
<?php	} ?>

      </div>

<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="fundraiser_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... No Pott Was Found</div>
            </div>
        </div>
      </div>
<?php } ?>

<?php 

$table_name = "adetor";
$order_by = "sku";
include(ROOT_PATH . 'inc/get_latest_sku.php');
if($skip == 1) {

	$latest_sku = -1;
		

}

$finds = array();
$finds_cnt = 0;
for($i = $latest_sku; $i > 0; $i--){

$query = "SELECT adetor_news_id, adetor_type, buyer_id, adetor_code_used, item_quantity FROM adetor WHERE sku = $i AND adetor_id_short = '$keyword'";

		$result = $mysqli->query($query);

		if (mysqli_num_rows($result) != "0") {

		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $adetor_news_id = $row["adetor_news_id"];
		  $this_buyer_id = $row["buyer_id"];
		  $item_quantity = $row["item_quantity"];
		  $adetor_type = trim($row["adetor_type"]);
		  $adetor_code_used = trim($row["adetor_code_used"]);
 

		$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$this_buyer_id'";

		if (mysqli_num_rows($result) != "0") {

		  $result = $mysqli->query($query);
		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $first_name = $row["first_name"];
		  $last_name = $row["last_name"];
		  $profile_picture = $row["profile_picture"];

		  if($adetor_type == "event"){

	$query = "SELECT event_name, event_date, creater_id FROM event WHERE event_news_id = '$adetor_news_id'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {

	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $event_name = $row["event_name"];
	  $event_date = $row["event_date"];
	  $creater_id = $row["creater_id"];

	$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$this_buyer_id'";

	if (mysqli_num_rows($result) != "0") {

	  $result = $mysqli->query($query);
	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $c_first_name = $row["first_name"];
	  $c_last_name = $row["last_name"];
	  $c_profile_picture = $row["profile_picture"];

$finds[$finds_cnt]= array($adetor_type, $adetor_news_id, $event_name, $event_date, $c_first_name, $c_last_name, $c_profile_picture, $first_name, $last_name, $profile_picture, $this_buyer_id, $creater_id, $adetor_code_used, $item_quantity);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
				}
			}

		  } elseif($adetor_type == "up4sale"){


	$query = "SELECT item_name, item_description, seller_id FROM up4sale WHERE up4sale_news_id = '$adetor_news_id'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {

	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $item_name = $row["item_name"];
	  $item_description = $row["item_description"];
	  $this_seller_id = $row["seller_id"];

	$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$this_seller_id'";

	if (mysqli_num_rows($result) != "0") {

	  $result = $mysqli->query($query);
	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $c_first_name = $row["first_name"];
	  $c_last_name = $row["last_name"];
	  $c_profile_picture = $row["profile_picture"];

$finds[$finds_cnt]= array($adetor_type, $adetor_news_id, $item_name, $item_description, $c_first_name, $c_last_name, $c_profile_picture, $first_name, $last_name, $profile_picture, $this_buyer_id, $this_seller_id, $adetor_code_used, $item_quantity);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
				}
			}


		  } elseif($adetor_type == "shares4sale"){


	$query = "SELECT num_on_sale, parent_shares_id, shares4sale_owner_id FROM shares4sale WHERE shares_news_id = '$adetor_news_id'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {

	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $num_on_sale = $row["num_on_sale"];
	  $parent_shares_id = $row["parent_shares_id"];
	  $this_shares4sale_owner_id = $row["shares4sale_owner_id"];

	$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$this_shares4sale_owner_id'";

	if (mysqli_num_rows($result) != "0") {

	  $result = $mysqli->query($query);
	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $c_first_name = $row["first_name"];
	  $c_last_name = $row["last_name"];
	  $c_profile_picture = $row["profile_picture"];

	$query = "SELECT share_name, shares_logo, country_origin FROM shares_worso WHERE parent_shares_id = '$parent_shares_id'";

	if (mysqli_num_rows($result) != "0") {

	  $result = $mysqli->query($query);
	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $share_name = $row["share_name"];
	  $shares_logo = $row["shares_logo"];

$finds[$finds_cnt]= array($adetor_type, $adetor_news_id, $share_name, $num_on_sale, $c_first_name, $c_last_name, $c_profile_picture, $first_name, $last_name, $profile_picture, $this_buyer_id, $this_shares4sale_owner_id, $adetor_code_used, $item_quantity, $shares_logo);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
				}
			}
		   }

		  } elseif($adetor_type == "fundraiser"){

	$query = "SELECT fundraiser_name, start_date, f_starter_id FROM fundraiser WHERE f_news_id = '$adetor_news_id'";

	$result = $mysqli->query($query);

	if (mysqli_num_rows($result) != "0") {

	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $fundraiser_name = $row["fundraiser_name"];
	  $start_date = $row["start_date"];
	  $this_f_starter_id = $row["f_starter_id"];

	$query = "SELECT first_name, last_name, profile_picture FROM investor WHERE investor_id = '$this_f_starter_id'";

	if (mysqli_num_rows($result) != "0") {

	  $result = $mysqli->query($query);
	  $row = $result->fetch_array(MYSQLI_ASSOC);
	  $c_first_name = $row["first_name"];
	  $c_last_name = $row["last_name"];
	  $c_profile_picture = $row["profile_picture"];

$finds[$finds_cnt]= array($adetor_type, $adetor_news_id, $fundraiser_name, $start_date, $c_first_name, $c_last_name, $c_profile_picture, $first_name, $last_name, $profile_picture, $this_buyer_id, $this_f_starter_id, $adetor_code_used, $item_quantity);
				  $finds_cnt = $finds_cnt + 1;
				  continue;
				}
			}



		  } 
		}
	}


}



if(isset($finds) && count($finds) > 0) { $i = count($finds) - 1 ; ?>
   <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="fish_code_sec">
	<?php for($i; $i >= 0; $i--) { ?>

        <div class="w3-col m12" style="margin-top: 10px; width: 100%;">
        	<div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">
              <?php if($finds[$i][12] == 0) { ?>
              <p style=" width: 100%; text-align: center;"><img src="../img/tick.png"  style="width: 10%; height: 10%;"></p>
              <?php } else { ?>
              <p style=" width: 100%; text-align: center;"><img src="../img/cross.png"  style="width: 10%; height: 10%;"></p>
              <?php } ?>
              <p style=" width: 100%; text-align: center;"><strong><?php echo $finds[$i][2]; ?></strong><br></p>
              	<?php if($finds[$i][0] == "shares4sale") { ?>
              	<img src="../user/<?php echo $finds[$i][12]; ?>" alt="Avatar" class="w3-left w3-margin-right"  style="width:65px"><br>
              	<?php } ?> 
              <?php if($finds[$i][11] == $investor_id && $finds[$i][12] == 0 && ($finds[$i][0] == "event" || $finds[$i][0] == "up4sale") ) { ?>
              <p style=" width: 100%; text-align: right;"><button onclick="chkCodeUsed(this)" data-shtcode = "<?php echo $keyword; ?>" type="button"  class="w3-btn w3-red w3-round"><i class="fa fa-check"></i> Check As Used</button></p>
              <?php } ?>
              </div>
      </div>
      </div>
<?php	} ?>

      </div>

<?php  } else { ?>

      <div class="w3-row-padding" style="margin-top: 10px;display: none;" id="fish_code_sec">
        <div class="w3-col m12" style="margin-top: 10px; width: 100%; text-align: center;">
            <div class="w3-card-2 w3-round w3-white">
              <div class="w3-container w3-padding">OOoopps... We Couldn't Find Your Code</div>
            </div>
        </div>
      </div>
<?php } ?>
<!-- ///////////////////////////////////////// UP4SALE SECTION END //////////////////////////// -->


