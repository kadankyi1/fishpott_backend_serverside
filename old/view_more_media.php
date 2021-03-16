<?php
session_start(); 

require_once("../inc/config.php"); 
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');

  $item_1 = $_GET["ls"];
  $p_investor_id = $_GET["pi"];
  $media_cnt = trim($_GET["media_cnt"]);
  $item_1 = $item_1 - 1;
  $table_name = "photos";
  $pot = 1;
  include(ROOT_PATH . 'inc/db_connect.php');
    if($item_1 != "" && $item_1 != "sku") {
      $chk_cnt = 0;
      for ($i = $item_1; $i > 0; $i--)  {
      include(ROOT_PATH . 'inc/get_photo_set_skip.php');

        if($skip == "no"){ 
          $chk_cnt = $chk_cnt + 1;
  ?>

         <div class="w3-white w3-round w3-third">
         <div style="height: 170px; border:solid 1px #d2d2d2; margin-top: 10px; width: 95%" class="w3-round">
     <a href="../user/<?php echo $p_pic_path; ?>" id="media<?php echo $chk_cnt; ?>" data-lightbox="media"><img src="../user/<?php echo $p_pic_path; ?>" width="100%" height="100%"></a><br>
         </div>  
          </div> 
        <?php } if($chk_cnt == 1) {break;} ?>
      <?php } ?>
    <?php } ?>
<?php if(isset($i)) { ?>
<span id="latest_media_sku<?php echo $media_cnt + 1; ?>" data-sku = "<?php echo $i; ?>" style="display: none;"></span>
<?php } ?>