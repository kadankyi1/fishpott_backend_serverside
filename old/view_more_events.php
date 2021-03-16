<?php
session_start(); 

require_once("../inc/config.php"); 
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');
$p_country = trim($_GET["p_country"]);
$evt_cnt = trim($_GET["evt_cnt"]);
if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") { 

    $fold = $_SESSION["e_user"];
    $login = $_SESSION["login_type"];
    $u_type = $_SESSION["user_type"];
    $my_country = trim($_GET["my_country"]);
$success_url = "https://fishpott.com/user/pay/success.php?fold=" . $fold .  "&saletype=" . $login . "&u_type=" . $u_type;
$cancelled_url = "https://fishpott.com/user/pay/deffered.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$deferred_url = "https://fishpott.com/user/pay/cancelled.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$merchant_key = "I will get this from the site";



} else {

  $success_url = "https://fishpott.com/user/pay/success.php";
  $cancelled_url = "https://fishpott.com/user/pay/deffered.php";
  $deferred_url = "https://fishpott.com/user/pay/cancelled.php";
  $merchant_key = "I will get this from the site";
  $my_country = $p_country;
}

if($my_country == "Ghana"){

    $currency = "GHS";

} elseif($my_country == "United Kingdom"){

    $currency = "GBP";

} else {

    $currency = "USD";

}


  $item_1 = $_GET["ls"];
  $p_investor_id = $_GET["pi"];
 $item_1 = $item_1 - 1;
  $table_name = "event";

  include(ROOT_PATH . 'inc/db_connect.php');
    if($item_1 != "" && $item_1 != "sku") {
      $chk_cnt = 0; $i = $item_1;
      for ($i; $i > 0; $i--)  {

      $table_name = "event";
      $item_1 = "event_news_id";
      $item_2 = "currency";
      $item_3 = "event_name";
      $item_4 = "image";
      $item_5 = "venue";
      $item_6 = "event_date";
      $item_7 = "event_time";
      $item_8 = "ticket_cost";
      $item_9 = "num_of_goers";
      $item_10 = "verified_tag";

      $column1_name = "creater_id";
      $column1_value = $p_investor_id;
      $pam1 = "s";

      $column2_name = "sku";
      $column2_value = $i;
      $pam2 = "i";

      include(ROOT_PATH . 'inc/select10_where2_prepared_statement.php');
      include(ROOT_PATH . 'inc/db_connect.php');
        if($done == 1 && $item_1 != "" && $item_1 != "event_news_id"){
            $strStart = $item_6;
            include(ROOT_PATH . 'inc/event_date_passed_chk.php');
            if($evt_coming == 1) { $chk_cnt = $chk_cnt + 1;

              $news_id = $item_1;
              $id_strt = uniqid($news_id, TRUE);
              $t = time();
              $r_t = date("Y-m-d",$t);
              $ext = $r_t . $t;

              $invoice_id = "fundraiser_" . $id_strt . $ext;

              if($item_2 == "GHS"){

              $seller_country = "Ghana";

              } elseif($item_2 == "GBP"){

              $seller_country = "United Kingdom";

              } else {

              $seller_country = "USA";

              }

              $i_country = $my_country;
              $convert_amt = $item_8;
              include(ROOT_PATH . 'inc/currency_converter.php');
      ?>   
  <div class="w3-white w3-round w3-third">
      <center>
   <div style="height: 170px; border:solid 1px #d2d2d2; margin-top: 10px; width: 95%" class="w3-round">
     <a href="../user/<?php echo $item_4; ?>" id="<?php echo $item_1; ?>" data-lightbox="<?php echo $i_full_name; ?>" data-title="<?php echo $item_3 . '<br>' . ceil($new_amt_pg) . " " . $currency; ?>"><img src="../user/<?php echo $item_4; ?>" width="100%" height="100%"></a>
   </div>
  <p  style="display: inline-block; padding-right: 20px; color: black;">
     <span id="inf_<?php echo $item_1; ?>" style="display: inline-block;word-wrap :auto; width: 85%; height: 30px; text-overflow: ellipsis; font-weight: bold;"><?php echo $item_3; ?> <br><hr></span>
     <?php if($new_amt_pg <= 0) { ?>
     <span id="pri_<?php echo $item_1; ?>" style="display: inline-block;">FREE ENTRY<br><hr></span>
     <?php } else { ?>
     <span id="pri_<?php echo $item_1; ?>" style="display: inline-block;"><?php echo ceil($new_amt_pg) . " " . $currency; ?> <br><hr></span>

<br><button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $item_1; ?>" id="<?php echo "buy_ticket_btn" . $item_1; ?>" onclick="showPayDiv(this);document.getElementById('inf_<?php echo $item_1; ?>').style.display = 'none';document.getElementById('pri_<?php echo $item_1; ?>').style.display = 'none';document.getElementById('buy_ticket_btn<?php echo $item_1; ?>').style.display = 'none';" data-type = "event">
<label for="<?php echo "buy_ticket_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $item_1; ?>').style.display = 'block';">        
        <div class="tooltip"><i style="cursor: pointer;" id="<?php echo "buy_icon" . $item_1; ?>" class="fa fa-shopping-cart" style="color: green;">

</i>
            <span class="tooltiptext">Buy Ticket</span>
        </div>
</label>
</button>
     <?php } ?>
<div id="<?php echo 'commentholder' . $item_1;?>" style="width: 100%;"> </div>
    </center><br> 
    </div> 

                  <?php if($chk_cnt == 1) {break;} } ?>
      <?php } ?>
    <?php }?>    
    <?php }?>
<?php if(isset($i)) { ?>
<span id="latest_evt_sku<?php echo $evt_cnt + 1; ?>" data-sku = "<?php echo $i; ?>" style="display: none;"></span>
<?php } ?>