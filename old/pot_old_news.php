<?php
session_start();
$investor_id = $_SESSION["user_sys_id"];

$config = 1;
require_once("../inc/config.php");

if(isset($_GET["oldestsku"]) && $_GET["oldestsku"] != "" && isset($_GET["p_investor_id"]) && $_GET["p_investor_id"] != "" && isset($_GET["i_country"]) && $_GET["i_country"] != ""){


include(ROOT_PATH . 'inc/db_connect.php');
  $table_name = "newsfeed";
  $order_by = "sku";
  include(ROOT_PATH . 'inc/get_latest_sku.php');
  if($skip == 1) {

    exit;
      
  } 
  $oldestsku = $_GET["oldestsku"];
  $i = $latest_sku - $oldestsku;
  $old_news_cnt = $i - 100;
  $k = $oldestsku;
  $p_inputtor_id = $_GET["p_investor_id"];
  $i_country = $_GET["i_country"];

  //echo "\noldestsku : " . $oldestsku;
  //echo "\nlatest_sku : " . $latest_sku;
  //echo "\np_inputtor_id : " . $p_inputtor_id;
  //echo "\nold_news_cnt : " . $old_news_cnt;

} else {


    exit;

}

for($i; $i >= 0; $i--){

include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_news_set_skip.php');

//$date_time = date('g:ia');    //5:53pm on Monday 6th March 2017
if(isset($_SESSION['inputtor_type']) && $_SESSION['inputtor_type'] != "") {

    $inputtor_type = $_SESSION['inputtor_type'];
    $show_comments = 1;

} else {

    $show_comments = 0;
}
if($skip != "yes" && $skip == "no"){
$k =  $k + 1;
?>
<div class="w3-container w3-card-2 w3-white w3-round w3-margin" id="<?php echo 'oldNewsDivAjx' . $k; ?>" data-newsku = "<?php echo $sku; ?>"  style=" margin: 0px;" >
<br>
        <?php if ($profile_picture != "") { ?>
        <a href="index.php?fold=<?php echo $_SESSION['e_user']; ?>&e_o=<?php echo $_SESSION['user_profile'];?>&login=<?php echo $_SESSION['login_type']; ?>&u_type=<?php echo $_SESSION['user_type']; ?>&u=<?php echo $investor_id; ?>" style=" text-decoration: none; "><img src="../pic_upload/<?php echo $profile_picture; ?>" alt="Avatar" class="w3-left w3-margin-right" style="width:40px"></a>
      <?php } else { ?>
        <a href="index.php?fold=<?php echo $_SESSION['e_user']; ?>&e_o=<?php echo $_SESSION['user_profile'];?>&login=<?php echo $_SESSION['login_type']; ?>&u_type=<?php echo $_SESSION['user_type']; ?>&u=<?php echo $investor_id; ?>" style=" text-decoration: none; "><img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:40px"></a>
      <?php } ?>
        <span class="w3-right w3-opacity"><?php echo $date_time; ?></span>
        <a href="index.php?fold=<?php echo $_SESSION['e_user']; ?>&e_o=<?php echo $_SESSION['user_profile'];?>&login=<?php echo $_SESSION['login_type']; ?>&u_type=<?php echo $_SESSION['user_type']; ?>&u=<?php echo $investor_id; ?>" style=" text-decoration: none; "><strong style="font-size: 14px"><?php echo $full_name; ?></strong></a>

        <h6 class="post_heading w3-center w3-hide-small">
        <?php if($type == "up4sale") { ?>
          <span style="color: #006600; ">Up 4 Sale</span>
        <?php } elseif ($type == "shares4sale")  {?>
          <span style="color: #ff9900;">Shares 4 Sale</span>
        <?php } elseif ($type == "fundraiser")  {?>
          <span style="color: #008ae6;">Fundraiser</span>
        <?php } elseif ($type == "event")  {?>
          <span style="color: #cccc00;">Event</span>
        <?php } ?>

        </h6><br>

        <h6 class="post_heading w3-center w3-hide-medium w3-hide-large" style="display: inline-block;">
        <?php if($type == "up4sale") { ?>
          <br><span style="color: #006600; ">Up 4 Sale</span>
        <?php } elseif ($type == "shares4sale")  {?>
          <br><span style="color: #ff9900;">Shares 4 Sale</span>
        <?php } elseif ($type == "fundraiser")  {?>
          <span style="color: #008ae6;">Fundraiser</span>
        <?php } elseif ($type == "event")  {?>
          <span style="color: #cccc00;">Event</span>
        <?php } ?>

        </h6><br>
        <hr class="w3-clear">
        <p><?php if($type == "shared_news") {echo $news;} ?></p>
        <?php if($type == "shares4sale") { ?>
        <center><p style="font-weight: bold;"><span style="color: #ff9900"><?php echo $share_name; ?><br></span><span style="color: #ff9900"><?php echo $country_origin; ?><br></span>Price per Share : <span style="color: #ff9900"> <?php echo $new_amt_user_str; ?></span></p></center>
        <?php } elseif($type == "fundraiser") { ?>
        <p style="font-weight: bold;"><span style="color: #008ae6"><?php echo $fundraiser_name; ?></span><br>Start Date : <span style="color: #008ae6"><?php echo $fundraiser_start_date; ?></span><br>End Date : <span style="color: #008ae6"><?php echo $fundraiser_end_date; ?></span><br>Target Amount : <span style="color: #008ae6"><?php echo $new_amt_user_str; ?></span></p>
        <?php } elseif($type == "up4sale") { ?>
        <p style="font-weight: bold;"><span style="color: green"><?php echo $up4sale_item_name; ?><br></span><span style="color: green"><?php echo  $new_amt_user_str; ?><br></span><span style="color: green"><?php echo $up4sale_item_location; ?></span><br>Delivery : <span style="color: green"><?php echo $up4sale_item_delivery; ?></span></p>

        <?php } elseif($type == "event") { ?>
        <p style="font-weight: bold;"><span style="color: #cccc00"><?php echo $event_name; ?><br></span><span style="color: #cccc00"><?php echo $event_venue; ?></span><br><span style="color: #cccc00"><?php echo $event_date; ?><br></span><span style="color: #cccc00"><?php echo $event_time; ?><br></span><span style="color: #cccc00"><?php echo $new_amt_user_str; ?></span></p>

        <?php } elseif($type == "shared_news") { ?>
        <?php $new_news_id  = $news_id_ref; include(ROOT_PATH . 'inc/get_shared_news.php'); ?>
        <?php $news_addition = $news; ?>
        <div class="w3-container w3-card-2 w3-white w3-round w3-center" style = "width : 100%; border-style : outset;">
        <br>
        <strong style="font-size: 16px; opacity: 0.5">
          <a href="ipage.php?fold=<?php echo $_SESSION['e_user']; ?>&e_o=<?php echo $_SESSION['user_profile'];?>&login=<?php echo $_SESSION['login_type']; ?>&u_type=<?php echo $_SESSION['user_type']; ?>&u=<?php echo $sn_inputtor_id; ?>" style=" text-decoration: none;" ><?php if($full_name != $sn_full_name) {echo $sn_full_name;} ?>
        </a>
        </strong>
        <p></p>
        <hr class="w3-clear">
        <h6 class="w3-center" style="font">
        <?php if($sn_type == "up4sale") { ?>
          <strong><span style="color: #006600; ">Up 4 Sale</span></strong>
        <?php } elseif ($sn_type == "shares4sale")  {?>
          <strong><span style="color: #ff9900;">Shares 4 Sale</span></strong>
        <?php } elseif ($sn_type == "fundraiser")  {?>
          <strong><span style="color: #008ae6;">Fundraiser</span></strong>
        <?php } elseif ($sn_type == "event")  {?>
          <strong><span style="color: #cccc00;">Event</span></strong>
        <?php } ?>
        </h6>
        <p><?php if($type == "shared_news") {echo $sn_news;} ?></p>
        <?php if($sn_type == "shares4sale") { ?>
        <p style="font-weight: bold;"><span style="color: #ff9900"><?php echo $sn_share_name; ?><br></span><span style="color: #ff9900"><?php echo $sn_country_origin; ?><br></span>Price per Share : <span style="color: #ff9900"><?php echo $sn_new_amt_user_str; ?></span></p>
        <?php } elseif($sn_type == "fundraiser") { ?>
        <p style="font-weight: bold;">Fundraiser : <span style="color: #008ae6"><?php echo $sn_fundraiser_name; ?><br></span>Start Date : <span style="color: #008ae6"><?php echo $sn_fundraiser_start_date; ?><br></span>End Date : <span style="color: #008ae6"><?php echo $sn_fundraiser_end_date; ?><br></span>Target Amount : <span style="color: #008ae6"><?php echo  $sn_new_amt_user_str; ?></span></p>
        <?php } elseif($sn_type == "up4sale") { ?>
        <p style="font-weight: bold;"><span style="color: green"><?php echo $sn_up4sale_item_name; ?><br></span><span style="color: green"><?php echo $sn_new_amt_user_str; ?><br></span>Item Location : <span style="color: green"><?php echo $sn_up4sale_item_location; ?><br></span>Delivery : <span style="color: green"><?php echo $sn_up4sale_item_delivery; ?></span></p>

        <?php } elseif($sn_type == "event") { ?>
        <p style="font-weight: bold;"><span style="color: #cccc00"><?php echo $sn_event_name; ?><br></span>Venue : <span style="color: #cccc00"><?php echo $sn_event_venue; ?><br></span><span style="color: #cccc00"><?php echo $sn_event_date; ?><br></span><span style="color: #cccc00"><?php echo $sn_event_time; ?><br></span><span style="color: #cccc00"><?php echo $sn_new_amt_user_str; ?></span></p>

        <?php } ?>
            <!--//SHARED NEWS COMES HERE-->



        <?php if(isset($sn_news_image) && $sn_news_image != "") { ?>
        <div style="text-align: center;">
          <a href="<?php echo $sn_news_image; ?>" data-lightbox="../user/<?php echo 'shared_image' . $k ?>" data-title="<?php echo $news_addition; ?>"><img src="../user/<?php echo $sn_news_image; ?>"  style=" width:50%;"  class="w3-margin-bottom"></a>
        </div>
          <br>
        <?php } ?>
        <?php if(isset($sn_news_aud) && $sn_news_aud != "") { ?>
        <div style="text-align: center;">
          <audio controls>
            <source src="../user/<?php echo $sn_news_aud; ?>" type="audio/ogg">
            <source src="../user/<?php echo $sn_news_aud; ?>" type="audio/mpeg">
            Your browser does not support the audio tag.
          </audio>        
        </div>
          <br>
        <?php } ?>
        <?php if(isset($sn_news_video) && $sn_news_video != "") { ?>
          <video width="640px" height="267px" controls style=" width:100%;">
            <source src="../user/<?php echo $sn_news_video; ?>" type="video/mp4">
            <source src="../user/<?php echo $sn_news_video; ?>" type="video/ogg">
            Your browser does not support HTML5 video.
          </video>
          <br>            
        <?php } ?>
        <button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" onclick="likeThis(this)" value="<?php echo $investor_id; ?>" id="<?php echo $news_id_ref . '1'; ?>" data-id = "<?php echo $news_id_ref; ?>" data-like-type = "1"><label for="<?php echo $news_id_ref . '1'; ?>"><i class="fa fa-thumbs-up fa-1x" id="<?php echo $investor_id . $news_id_ref . '1'; ?>" style="cursor: pointer;color: <?php if(isset($set_like_btn_color) && $set_like_btn_color == 1) { echo '#ff6300'; $set_like_btn_color = 0; } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_likes; ?></i></label></button>
        <button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" onclick="likeThis(this)" value="<?php echo $investor_id; ?>" id="<?php echo $news_id_ref . '0'; ?>" data-id = "<?php echo $news_id_ref; ?>" data-like-type = "0"><label for="<?php echo $news_id_ref . '0'; ?>"><i class="fa fa-thumbs-down fa-1x" id="<?php echo $investor_id . $news_id_ref . '0'; ?>" style="cursor: pointer;color: <?php if(isset($set_dis_like_btn_color) && $set_dis_like_btn_color == 1) { echo 'red'; $set_dis_like_btn_color = 0;  } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_dislikes; ?></i></label></button>

        <button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "share_newsbtn" . $news_id_ref; ?>" onclick="showShare2ThisNewsDiv(this)">
          
        <label for="<?php echo "share_newsbtn" . $news_id_ref; ?>">
                <div class="tooltip"><i class="fa fa-share" style="cursor: pointer;color:#6489d4;"></i>
                    <span class="tooltiptext">RePost</span>
                </div>
        </label>
        </button>

        <?php if($sn_type == "up4sale") { ?>
<button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_item_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNews(this)" data-type = "up4sale">
<label for="<?php echo "buy_item_btn" . $news_id_ref; ?>">

        <div class="tooltip"><i class="fa fa-shopping-cart" style="cursor: pointer;color: green;">
        </i>
            <span class="tooltiptext">Buy Item</span>
        </div>
</label>
</button>
        <?php } elseif($sn_type == "shares4sale") { ?>
<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_shares_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNews(this)" data-type = "shares4sale"><label for="<?php echo "buy_shares_btn" . $news_id_ref; ?>">
        <div class="tooltip"><i class="fa fa-shopping-cart" style="cursor: pointer; color: green">

        </i>
            <span class="tooltiptext">Buy Shares</span>
</div>
</label>           
</button>
        <?php } elseif($sn_type == "fundraiser") { ?>

<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "contribute_fundraiser_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNews(this)" data-type = "fundraiser"><label for="<?php echo "contribute_fundraiser_btn" . $news_id_ref; ?>">        
  <div class="tooltip"><i class="fa fa-money" style="cursor: pointer; color: green">
</i>
            <span class="tooltiptext">Contribute</span>
        </div>
</label>  
</button>
        <?php } elseif($sn_type == "event") { ?>
<button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_ticket_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNews(this)" data-type = "event">
<label for="<?php echo "buy_ticket_btn" . $news_id_ref; ?>">       
        <div class="tooltip"><i class="fa fa-shopping-cart" style="color: green;">
        </i>
            <span class="tooltiptext">Buy Ticket</span>
        </div>
</label>
</button>
        <?php } ?>        

  <p></p>
<div id="<?php echo 'shared_commentholder' . $news_id_ref;?>" style="width: 100%; "></div>
        </div>
        <?php } ?>
        <p style="word-wrap: break-word;"><?php if($type != "shared_news") {echo $news;} ?></p>
        <?php if(isset($news_image) && $news_image != "") { ?>
        <div style="text-align: center;">
          <a href="../user/<?php echo $news_image; ?>" data-lightbox="<?php echo 'image' . $k ?>" data-title="<?php echo $news; ?>" ><img src="../user/<?php echo $news_image; ?>" style=" width:50%;"  class="w3-margin-bottom"></a>
          </div>
        <?php } ?>
        <?php if(isset($news_aud) && $news_aud != "") { ?>
        <div style="text-align: center;">
          <audio controls style="width: 100%">
            <source src="../user/<?php echo $news_aud; ?>" type="audio/ogg">
            <source src="../user/<?php echo $news_aud; ?>" type="audio/mpeg">
            Your browser does not support the audio tag.
          </audio> 
        </div>
          <br>
        <?php } ?>
        <?php if(isset($news_video) && $news_video != "") { ?>
          <video width="640px" height="267px" controls style=" width:100%;">
            <source src="../user/<?php echo $news_video; ?>" type="video/mp4">
            <source src="../user/<?php echo $news_video; ?>" type="video/ogg">
            Your browser does not support HTML5 video.
          </video>
          <br>
        <?php } ?>
        <button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" onclick="likeThis(this)" value="<?php echo $investor_id; ?>" id="<?php echo $news_id . '1'; ?>" data-id = "<?php echo $news_id; ?>" data-like-type = "1"><label for="<?php echo $news_id . '1'; ?>"><i class="fa fa-thumbs-up fa-1x" id="<?php echo $investor_id . $news_id . '1'; ?>" style="cursor: pointer;color: <?php if(isset($set_like_btn_color) && $set_like_btn_color == 1) { echo '#ff6300'; $set_like_btn_color = 0; } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_likes; ?></i></label></button>
        <button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" onclick="likeThis(this)" value="<?php echo $investor_id; ?>" id="<?php echo $news_id . '0'; ?>" data-id = "<?php echo $news_id; ?>" data-like-type = "0"><label for="<?php echo $news_id . '0'; ?>"><i class="fa fa-thumbs-down fa-1x" id="<?php echo $investor_id . $news_id . '0'; ?>" style="cursor: pointer;color: <?php if(isset($set_dis_like_btn_color) && $set_dis_like_btn_color == 1) { echo 'red'; $set_dis_like_btn_color = 0;  } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_dislikes; ?></i></label></button>

<button class="w3-btn w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "commentbtn" . $news_id; ?>" onclick="getCommentSection(this)">

<div class="tooltip">

<?php $table_name = "comments"; $item_1 = "sku"; $done = 0; ?>
<?php   
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');
if($done == 1 && $item_1 != "sku"){


  $latest_sku_comments = $item_1;

}

include(ROOT_PATH . 'inc/db_connect.php'); 

$table_name = "comments"; $item_1 = "sku"; $done = 0; 
include(ROOT_PATH . 'inc/get_num_of_comments.php'); 
include(ROOT_PATH . 'inc/db_connect_autologout.php'); 

$table_name = "comments";
$item_1 = "sku";
$item_2 = "news_id";
$column1_name = "inputtor_id";
$column1_value = $investor_id;
$column2_name = "news_id";
$column2_value = $news_id;
$pam1 = "s";
$pam2 = "s";

include(ROOT_PATH . 'inc/select2_where2_prepared_statement.php'); 

if ($item_1 != "sku" && $item_1 != "" && $item_2 != "news_id" && $item_2 != ""){

  $user_commentted = 1;
  $item_1 = "sku";
  $item_2 = "news_id";


} else {

  $user_commentted = 0;
  $item_1 = "sku";
  $item_2 = "news_id";

}
include(ROOT_PATH . 'inc/db_connect_autologout.php'); 

?>  
<label for="<?php echo "commentbtn" . $news_id; ?>">
<i class="fa fa-comment dropbtn" style="cursor: pointer; color : <?php if(isset($user_commentted) && $count_comments != 0){echo "#5b2d00";} elseif(!isset($user_commentted) || $user_commentted == 0) {echo "#bfbfbf";} ?>;" > <span id="<?php echo 'numOfComments' . $news_id; ?>"><?php if(isset($count_comments) && $count_comments != 0) { echo $count_comments;} ?></span></i>
<?php $count_comments = 0; ?>
            <span class="tooltiptext">Comment</span>
        </div>
</label>
</button>

<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "share_newsbtn" . $news_id; ?>" onclick="showShareThisNewsDiv(this)">
<label for="<?php echo "share_newsbtn" . $news_id; ?>">
        <div class="tooltip"><i class="fa fa-share" style="color:#6489d4;"></i>
            <span class="tooltiptext">RePost</span>
        </div>
</label>
</button>
        <?php if($type == "up4sale") { ?>

<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_item_btn" . $news_id; ?>" onclick="showPayDiv(this); " data-type = "up4sale"><label for="<?php echo "buy_item_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">

<div class="tooltip"><i class="fa fa-shopping-cart" style="cursor: pointer; color: green;">

            <span class="tooltiptext">Buy Item</span>
</i>
        </div>
</label>
</button>
        <?php } elseif($type == "shares4sale") { ?>
<button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_shares_btn" . $news_id; ?>" onclick="showPayDiv(this)" data-type = "shares4sale"><label for="<?php echo "buy_shares_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">
        <div class="tooltip"><i class="fa fa-shopping-cart" style="cursor: pointer; color: green">

            <span class="tooltiptext">Buy Shares</span>
</i>
</div>
</label>           
</button>
        <?php } elseif($type == "fundraiser") { ?>
<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "contribute_fundraiser_btn" . $news_id; ?>" onclick="showPayDiv(this)" data-type = "fundraiser"><label for="<?php echo "contribute_fundraiser_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">        
<div class="tooltip"><i class="fa fa-money" style="cursor: pointer; color: green">

</i>
            <span class="tooltiptext">Contribute</span>
        </div>
</label> 
</button>

        <?php } elseif($type == "event") { ?>
<button  class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_ticket_btn" . $news_id; ?>" onclick="showPayDiv(this)" data-type = "event"><label for="<?php echo "buy_ticket_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">        
        <div class="tooltip"><i class="fa fa-shopping-cart" style="cursor: pointer; color: green;">

</i>
            <span class="tooltiptext">Buy Ticket</span>
        </div>
</label>
</button>
        <?php } ?>
<div id="<?php echo 'commentholder' . $news_id;?>" style="width: 100%; "></div>
        <p></p>
      </div>
<p id="<?php echo 'oldNewsLoaderAjx' . $k ?>" style="text-align: center; display: none;"><img  src="../img/load.gif" height="20px" width="20px"></p>
</div>    
  <?php  } ?>
<?php } ?>
