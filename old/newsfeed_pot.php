      <div <?php //if(isset($type_of_view) && $type_of_view == "mobile") { echo "style = 'margin-right : 0px; margin-left : 0px; ";}  ?> class="w3-container w3-card-2 w3-white w3-round w3-margin" <?php if($type_of_view != "desktop") { ?> style="width: 100%; margin-left: -15px; margin-bottom: 10px;"<?php } ?>
  <?php if(isset($old_news_id_count)) { ?> id="<?php echo 'oldNewsDiv' . $old_news_id_count ?>" <?php if($old_news_id_count == 1 || $old_news_id_count == 19){ ?> data-newsku = "<?php echo $sku; ?>" <?php } ?> <?php } else { ?> id="<?php echo 'oldNewsDiv' . $news_id ?>" <?php } ?> ><br>
        <?php if ($profile_picture != "") { ?>
        <a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $inputtor_id; ?>" style=" text-decoration: none; "><img src="../pic_upload/<?php echo $profile_picture; ?>" alt="Avatar" class="w3-left w3-margin-right w3-round" style="width:40px"></a>
      <?php } else { ?>
        <a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $inputtor_id; ?>" style=" text-decoration: none; "><img src="../img/buddy_sample.png" alt="Avatar" class="w3-left w3-margin-right" style="width:40px"></a>
      <?php } ?>
        <span class="w3-right w3-opacity" style="font-size: 10px; margin-right: 5px;"><?php echo $date_time; ?></span>
        <a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['0']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $inputtor_id; ?>" style=" text-decoration: none; line-height: 20px; "><strong style="font-size: 12px"><?php echo $full_name; ?><?php if($type == "shared_news") {?> <span style="color: grey; font-size: 9px;">shared a post</span>  <?php } ?></strong><?php if($this_inputtor_vtag == 1){ ?><br><span class="tooltip"><span class="tooltiptext">Verified</span><i style="color: green" class="fa fa-check-square" aria-hidden="true"></i></span><br> <?php } ?></a>
        
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

        </h6>
        <hr class="w3-clear">
        <p><?php if($type == "shared_news") {echo $news;} ?></p>
        <?php if($type == "shares4sale") { ?>
        <center><p style="font-weight: bold;"><span style="color: #ff9900"><?php echo $share_name; ?></span><span style="color: #ff9900"><br><?php echo $country_origin; ?><br></span>Number Of Shares On Sale : <span style="color: #ff9900"><?php echo $num_on_sale; ?><br></span>Price per Share : <span style="color: #ff9900"><?php echo $new_amt_user_str; ?></span></p></center>
        <?php } elseif($type == "fundraiser") { ?>
        <p style="font-weight: bold;"><span style="color: #008ae6"><?php echo $fundraiser_name; ?></span><br>Start Date : <span style="color: #008ae6"><?php echo $fundraiser_start_date; ?></span><br>End Date : <span style="color: #008ae6"><?php echo $fundraiser_end_date; ?></span><br>Target Amount :  <span style="color: #008ae6"><?php echo $new_amt_user_str; ?></span></p>
        <?php } elseif($type == "up4sale") { ?>
        <p style="font-weight: bold;"><span style="color: black"><?php echo $up4sale_item_name; ?></span><br><span style="color: black"><?php echo $new_amt_user_str; ?></span><br>
        <span style="color: green">
        <?php if(isset($coor) && $coor == 1){
$url= 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $up4sale_item_location . '&sensor=false';
                $geocode=file_get_contents($url);
                $output= json_decode($geocode);

                for($j=0;$j<count($output->results[0]->address_components);$j++){
                    if($j == 0 && $output->results[0]->address_components[$j]->long_name != ""){
                          echo 'Street Name/Number :  '. $output->results[0]->address_components[$j]->long_name.'<br/>';

                    } else {

                      echo $output->results[0]->address_components[$j]->long_name.'<br/>';
                    }

                }
            } else {
                  echo "<br>" . $up4sale_item_location; 
            }
            unset($coor);
        ?></span>
        Delivery Service : <span style="color: green"><?php echo $up4sale_item_delivery; ?></span><?php if($item_quantity > 1) { ?><br><span style="color: grey; font-size: x-small;">You can buy up to <?php echo $item_quantity; ?> pieces of this item</span><?php } ?></p>

        <?php } elseif($type == "event") { ?>
        <p style="font-weight: bold;"><span><?php echo $event_name; ?><br></span><span style="color: #cccc00"><?php echo $event_venue; ?><br></span><span style="color: #cccc00"><?php echo $event_date; ?><br></span><span style="color: #cccc00"><?php echo $event_time; ?><br></span><span> <?php echo $new_amt_user_str; ?></span></p>

        <?php } elseif($type == "shared_news") { ?>
        <?php $new_news_id  = $news_id_ref; include(ROOT_PATH . 'inc/get_shared_news.php'); ?>
        <?php $news_addition = $news; ?>
        <div class="w3-container w3-card-2 w3-white w3-round w3-center" style = "width : 100%; border-style : outset;">
        <br>
        <strong style="font-size: 16px; opacity: 0.5">
          <a href="../pots/index.php?fold=<?php echo $old_fold; ?>&e_o=<?php echo $e_o['11']; ?>&login=<?php echo $old_e_login; ?>&u_type=<?php echo $old_e_u_type; ?>&u=<?php echo $sn_inputtor_id; ?>" style=" text-decoration: none;" ><?php if($full_name != $sn_full_name) {echo $sn_full_name;} ?>
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
        <p style="font-weight: bold;"><span style="color: #ff9900"><?php echo $sn_share_name; ?></span><br><span style="color: #ff9900"><?php echo $sn_country_origin; ?><br></span>Number Of Shares On Sale : <span style="color: #ff9900"><?php echo $num_on_sale; ?><br></span>Price per Share : <span style="color: #ff9900"><?php echo $sn_new_amt_user_str; ?></span></p>
        <?php } elseif($sn_type == "fundraiser") { ?>
        <p style="font-weight: bold;"><span style="color: #008ae6"><?php echo $sn_fundraiser_name; ?></span><br>Start Date : <span style="color: #008ae6"><?php echo $sn_fundraiser_start_date; ?></span><br>End Date : <span style="color: #008ae6"><?php echo $sn_fundraiser_end_date; ?></span><br>Target Amount :  <span style="color: #008ae6"><?php echo $sn_new_amt_user_str; ?></span></p>
        <?php } elseif($sn_type == "up4sale") { ?>
        <p style="font-weight: bold;">Up4Sale : <span><?php echo $sn_up4sale_item_name; ?></span><br>Price : <span><?php echo $sn_new_amt_user_str; ?></span><br><span style="color: green">
        <?php if(isset($sn_coor) && $sn_coor == 1){
$url= 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $sn_up4sale_item_location . '&sensor=false';
                $geocode=file_get_contents($url);
                $output= json_decode($geocode);

                for($j=0;$j<count($output->results[0]->address_components);$j++){
                    if($j == 0 && $output->results[0]->address_components[$j]->long_name != ""){
                          echo 'Street Name/Number :  '. $output->results[0]->address_components[$j]->long_name.'<br/>';

                    } else {

                      echo $output->results[0]->address_components[$j]->long_name .'<br/>';
                    }

                }
            } else {
                  echo "<br>" . $sn_up4sale_item_location; 
            }
            unset($sn_coor);
          ?>

        </span>Delivery Service : <span style="color: green"><?php echo $sn_up4sale_item_delivery; ?></span><?php if($sn_item_quantity > 1) { ?><br><span style="color: grey; font-size: x-small;">You can buy up to <?php echo $sn_item_quantity; ?> pieces of this item</span><?php } ?></p>

        <?php } elseif($sn_type == "event") { ?>
        <p style="font-weight: bold;"><span ><?php echo $sn_event_name; ?></span><br><span style="color: #cccc00"><?php echo $sn_event_venue; ?></span><br><span style="color: #cccc00"><?php echo $sn_event_date; ?></span><br><span style="color: #cccc00"><?php echo $sn_event_time; ?></span><br><spann> <?php echo $sn_new_amt_user_str; ?></span></p>

        <?php } ?>
            <!--//SHARED NEWS COMES HERE-->



        <?php if(isset($sn_news_image) && $sn_news_image != "") { ?>
        <div style="text-align: center;">
          <a href="../user/<?php echo $sn_news_image; ?>" data-lightbox="news" data-title="<?php echo htmlspecialchars($news_addition); ?>"><img src="../user/<?php echo $sn_news_image; ?>"  style=" width:50%;"  class="w3-margin-bottom"></a>
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
        <hr>
         <button class="w3-btn w3-round w3-light-grey"  style="cursor: pointer;"  onclick="likeThis(this)" data-inputtorid="<?php echo $sn_inputtor_id; ?>" value="<?php echo $investor_id; ?>" id="<?php echo $news_id_ref . '1'; ?>" data-id = "<?php echo $news_id_ref; ?>" data-like-type = "1"><label for="<?php echo $news_id_ref . '1'; ?>"><i class="fa fa-thumbs-up fa-1x" id="<?php echo $investor_id . $news_id_ref . '1'; ?>" style="cursor: pointer;padding-right: 20px;  color: <?php if(isset($set_like_btn_color) && $set_like_btn_color == 1) { echo '#ff6300'; $set_like_btn_color = 0; } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_likes; ?></i></label></button>
         <button class="w3-btn w3-round w3-light-grey"  style="cursor: pointer;" onclick="likeThis(this)" data-inputtorid="<?php echo $sn_inputtor_id; ?>" value="<?php echo $investor_id; ?>" id="<?php echo $news_id_ref . '0'; ?>" data-id = "<?php echo $news_id_ref; ?>" data-like-type = "0"><label for="<?php echo $news_id_ref . '0'; ?>"><i class="fa fa-thumbs-down fa-1x" id="<?php echo $investor_id . $news_id_ref . '0'; ?>" style="cursor: pointer;color: <?php if(isset($set_dis_like_btn_color) && $set_dis_like_btn_color == 1) { echo 'red'; $set_dis_like_btn_color = 0;  } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_dislikes; ?></i></label></button>
<?php if($old_e_o == $_SESSION["newsfeed"]) { ?>
        <button class="w3-btn w3-light-grey w3-round" value="<?php echo $news_id_ref; ?>" id="<?php echo "share_newsbtn" . $news_id_ref; ?>" onclick="showShare2ThisNewsDiv(this)" style="cursor: pointer;">
          
            <label for="<?php echo "share_newsbtn" . $news_id_ref; ?>">
              <?php 
              $item_1 = 0;
              $table1_name = "newsfeed"; 

              $row_chk1_tb_column = "news_id_ref";  
              $row_chk1_tb_value = $sn_news_id;  

              $pam1 = "s";  

              include(ROOT_PATH . 'inc/count_where1_prepared_statement.php');
              if($done == 1) {

                $num_of_shares = $item_1;
              }
              ?>
              <?php  ?>
              <?php   include(ROOT_PATH . 'inc/db_connect_autologout.php'); ?>
            <div class="tooltip"><i  id="<?php echo "share_newsicon" . $news_id_ref; ?>" class="fa fa-share" style="cursor: pointer;color:<?php if($num_of_shares != 0)  {echo "#6489d4";} else { echo "#bfbfbf"; } ?>"></i>
                <span  style="cursor: pointer;" class="tooltiptext">RePost</span> <?php if($num_of_shares != 0)  {echo $num_of_shares;} ?>
            </div>
            </label>


        </button>
<?php } ?>

        <?php if($sn_type == "up4sale") { ?>
<button class="w3-btn w3-light-green w3-round" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_item_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNewsNewsfeed(this)" data-type = "up4sale">
<label  style="cursor: pointer;" for="<?php echo "buy_item_btn" . $news_id_ref; ?>">

        <div class="tooltip"><i id="<?php echo "buy_icon" . $news_id_ref; ?>" class="fa fa-shopping-cart" style="color: black;">

        </i>
            <span class="tooltiptext">Buy Item</span>
        </div>
</label>
</button>
        <?php } elseif($sn_type == "shares4sale") { ?>
<button class="w3-btn w3-light-green w3-round" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_shares_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNewsNewsfeed(this)" data-type = "shares4sale"><label for="<?php echo "buy_shares_btn" . $news_id_ref; ?>">
        <div class="tooltip"><i id="<?php echo "buy_icon" . $news_id_ref; ?>" class="fa fa-shopping-cart" style="cursor: pointer;color: black">

        </i>
            <span class="tooltiptext">Buy Shares</span>
</div>
</label>           
</button>
        <?php } elseif($sn_type == "fundraiser") { ?>
<button class="w3-btn w3-light-green w3-round" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "contribute_fundraiser_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNewsNewsfeed(this)" data-type = "fundraiser"><label for="<?php echo "contribute_fundraiser_btn" . $news_id_ref; ?>">        
<div class="tooltip"><i  id="<?php echo "buy_icon" . $news_id_ref; ?>" class="fa fa-money" style="cursor: pointer;color: black">

</i>
            <span class="tooltiptext">Contribute</span>
        </div>
</label>
</button>

        <?php } elseif($sn_type == "event") { ?>
<button class="w3-btn w3-light-green w3-round" style="cursor: pointer;" value="<?php echo $news_id_ref; ?>" id="<?php echo "buy_ticket_btn" . $news_id_ref; ?>" onclick="showPayDiv4SharedNewsNewsfeed(this)" data-type = "event"><label for="<?php echo "buy_ticket_btn" . $news_id_ref; ?>">       

        <div class="tooltip"><i id="<?php echo "buy_icon" . $news_id_ref; ?>" class="fa fa-shopping-cart" style="cursor: pointer;color: black;">
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
          <a href="../user/<?php echo $news_image; ?>" data-lightbox="news" data-title="<?php echo htmlspecialchars($news); ?>" ><img src="../user/<?php echo $news_image; ?>" style=" width:50%;"  class="w3-margin-bottom"></a>
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

        <button class="w3-btn w3-round w3-light-grey"  style="cursor: pointer;" onclick="likeThis(this)" data-inputtorid="<?php echo $inputtor_id; ?>" value="<?php echo $investor_id; ?>" id="<?php echo $news_id . '1'; ?>" data-id = "<?php echo $news_id; ?>" data-like-type = "1"><label for="<?php echo $news_id . '1'; ?>"><i class="fa fa-thumbs-up fa-1x" id="<?php echo $investor_id . $news_id . '1'; ?>" style="cursor: pointer;color: <?php if(isset($set_like_btn_color) && $set_like_btn_color == 1) { echo '#ff6300'; $set_like_btn_color = 0; } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_likes; ?></i></label></button>
        <button class="w3-btn w3-round w3-light-grey"  style="cursor: pointer;" onclick="likeThis(this)" data-inputtorid="<?php echo $inputtor_id; ?>" value="<?php echo $investor_id; ?>" id="<?php echo $news_id . '0'; ?>" data-id = "<?php echo $news_id; ?>" data-like-type = "0"><label for="<?php echo $news_id . '0'; ?>"><i class="fa fa-thumbs-down fa-1x" id="<?php echo $investor_id . $news_id . '0'; ?>" style="cursor: pointer;color: <?php if(isset($set_dis_like_btn_color) && $set_dis_like_btn_color == 1) { echo 'red'; $set_dis_like_btn_color = 0;  } else { echo '#bfbfbf'; }?>"> <?php echo $p_num_of_dislikes; ?></i></label></button>
        

<button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "commentbtn" . $news_id; ?>" onclick="getCommentSection(this)">

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
<label for = "<?php echo "commentbtn" . $news_id; ?>">
<i class="fa fa-comment dropbtn" id="<?php echo "commenticon" . $news_id; ?>" style="cursor: pointer; color : <?php if(isset($user_commentted) && $count_comments != 0){echo "#5b2d00";} elseif(!isset($user_commentted) || $user_commentted == 0) {echo "#bfbfbf";} ?>;" > <span id="<?php echo 'numOfComments' . $news_id; ?>"><?php if(isset($count_comments) && $count_comments != 0) { echo $count_comments;} ?></span></i>
<?php $count_comments = 0; ?>
            <span class="tooltiptext">Comment</span>
        </div>
</label>
</button>
<?php if($old_e_o == $_SESSION["newsfeed"]) { ?>
<button class="w3-btn  w3-round w3-light-grey" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "share_newsbtn" . $news_id; ?>" onclick="showShareThisNewsDiv(this)">
<label for="<?php echo "share_newsbtn" . $news_id; ?>">
<?php 
$item_1 = 0;
$table1_name = "newsfeed"; 

$row_chk1_tb_column = "news_id_ref";  
$row_chk1_tb_value = $news_id;  

$pam1 = "s";  

include(ROOT_PATH . 'inc/count_where1_prepared_statement.php');
if($done == 1) {

  $num_of_shares = $item_1;
}
?>
<?php  ?>
<?php   include(ROOT_PATH . 'inc/db_connect_autologout.php'); ?>

        <div class="tooltip"><i class="fa fa-share" id="<?php echo "share_newsicon" . $news_id; ?>" style="cursor: pointer;color:<?php if($num_of_shares != 0)  {echo "#6489d4";} else { echo "#bfbfbf"; } ?>"></i>
            <span class="tooltiptext">RePost</span> <?php if($num_of_shares != 0)  {echo $num_of_shares;} ?>
        </div>
</label>
</button>
<?php } ?>
        <?php if($type == "up4sale") { ?>
<button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_item_btn" . $news_id; ?>" onclick="showPayDivNewsfeed(this); " data-type = "up4sale"><label for="<?php echo "buy_item_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">

<div class="tooltip"><i style="cursor: pointer;" id="<?php echo "buy_icon" . $news_id; ?>" class="fa fa-shopping-cart" style="color: black;">

            <span class="tooltiptext">Buy Item</span>
</i>
        </div>
</label>
</button>

        <?php } elseif($type == "shares4sale") { ?>
<button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_shares_btn" . $news_id; ?>" onclick="showPayDivNewsfeed(this)" data-type = "shares4sale">

<label for="<?php echo "buy_shares_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">
        <div class="tooltip"><i id="<?php echo "buy_icon" . $news_id; ?>" class="fa fa-shopping-cart" style="color: black">

            <span class="tooltiptext">Buy Shares</span>
</i>
</div>
</label> 
</button>          

        <?php } elseif($type == "fundraiser") { ?>
<button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "contribute_fundraiser_btn" . $news_id; ?>" onclick="showPayDivNewsfeed(this)" data-type = "fundraiser"><label for="<?php echo "contribute_fundraiser_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">        
<div class="tooltip"><i style="cursor: pointer;" id="<?php echo "buy_icon" . $news_id; ?>" class="fa fa-money" style="color: black">

</i>
            <span class="tooltiptext">Contribute</span>
        </div>
</label>
</button>            
        <?php } elseif($type == "event" && $new_amt_user_str != "FREE") { ?>
<button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_ticket_btn" . $news_id; ?>" onclick="showPayDivNewsfeed(this)" data-type = "event">
<label for="<?php echo "buy_ticket_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">        
        <div class="tooltip"><i style="cursor: pointer;" id="<?php echo "buy_icon" . $news_id; ?>" class="fa fa-shopping-cart" style="color: black;">

</i>
            <span class="tooltiptext">Buy Ticket</span>
        </div>
</label>
</button>
        <?php } ?>
<?php if($inputtor_id == $investor_id){ ?>
<i style="cursor: pointer;color: red; float: right;" onclick="delthisNews(this)" data-newsid = "<?php echo $news_id; ?>" data-newstype = "<?php echo $type; ?>" id="<?php echo "del_icon" . $news_id; ?>" class="fa fa-trash-o" <?php if(isset($old_news_id_count)) { ?> data-divid="<?php echo
      'oldNewsDiv' . $old_news_id_count ?>" <?php if($old_news_id_count == 1 || $old_news_id_count == 19){ ?> data-newsku = "<?php echo $sku; ?>" <?php } ?> <?php } else { ?> data-divid="<?php echo
      'oldNewsDiv' . $news_id ?>" <?php } ?>>
<?php } ?>
</i><div id="<?php echo 'commentholder' . $news_id;?>" style="width: 100%; "></div>
        <p></p>
      </div>
<?php if(isset($new_amt_user_str)){

  unset($new_amt_user_str);  
}
?>