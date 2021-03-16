<?php
session_start(); 

require_once("../inc/config.php"); 
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');
$p_country = trim($_GET["p_country"]);
$yardsale_cnt = trim($_GET["yardsale_cnt"]);

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
  $table_name = "up4sale";
  $item_1 = $item_1 - 1;
  include(ROOT_PATH . 'inc/db_connect.php');
    if($item_1 != "" && $item_1 != "sku") {
      $chk_cnt = 0; $i = $item_1;
      for ($i; $i > 0; $i--)  {
      $table_name = "up4sale";
      $item_1 = "up4sale_news_id";
      $item_2 = "item_name";
      $item_3 = "item_price";
      $item_4 = "item_description";
      $item_5 = "item_location";
      $item_6 = "item_delivery";
      $item_7 = "currency";

      $column1_name = "seller_id";
      $column1_value = $p_investor_id;
      $pam1 = "s";

      $column2_name = "sale_status";
      $column2_value = 0;
      $pam2 = "i";

      $column3_name = "sku";
      $column3_value = $i;
      $pam3 = "i";
      include(ROOT_PATH . 'inc/select7_where3_prepared_statement.php');
      include(ROOT_PATH . 'inc/db_connect.php');
        if($done == 1 && $item_1 != "" && $item_1 != "up4sale_news_id"){ 
          $item_name = $item_2;
          $up4sale_news_id = $item_1;
          $item_price = $item_3;
          $table_name = "newsfeed";
          $item_1 = "news_image";
          $item_2 = "news";

          $column1_name = "news_id";
          $column1_value = $up4sale_news_id;
          $pam1 = "s";

          include(ROOT_PATH . 'inc/select2_where1_prepared_statement.php');
          include(ROOT_PATH . 'inc/db_connect.php');

          if($done == 1 && $item_1 != "" && $item_1 != "news_image"){ 
            $chk_cnt = $chk_cnt + 1; 
            $news_id = $up4sale_news_id;
            $id_strt = uniqid($news_id, TRUE);
            $t = time();
            $r_t = date("Y-m-d",$t);
            $ext = $r_t . $t;

            $invoice_id = "yardsale_" . $id_strt . $ext;
            if($item_7 == "GHS"){

            $seller_country = "Ghana";

            } elseif($item_7 == "GBP"){

            $seller_country = "United Kingdom";

            } else {

            $seller_country = "USA";

            }
            $convert_amt = $item_price;
            $i_country = $my_country;
            include(ROOT_PATH . 'inc/currency_converter.php');
        ?>   

  <div class="w3-white w3-round w3-third">
  <center>
   <div style="height: 170px; border:solid 1px #d2d2d2; margin-top: 10px; width: 95%" class="w3-round">
     <a href="../user/<?php echo $item_1; ?>" id="<?php echo $up4sale_news_id; ?>" data-lightbox="yardsale" data-title="<?php echo htmlspecialchars($item_2);?>"><img src="../user/<?php echo $item_1; ?>" width="100%" height="100%"></a><br>
     
   </div>
     <p  style="display: inline-block; padding-right: 20px; color: black;">
     <span id="inf_<?php echo $news_id; ?>" style="display: inline-block;word-wrap: break-word; width: 85%; height: 30px; text-overflow: ellipsis;font-weight: bold;"><?php echo $item_name; ?> <br><hr></span>
     <span id="pri_<?php echo $news_id; ?>" style="display: inline-block;"><?php echo ceil($new_amt_pg) . " " . $currency; ?> <br><hr></span>
<br><button class="w3-btn  w3-round w3-light-green" style="cursor: pointer;" value="<?php echo $news_id; ?>" id="<?php echo "buy_item_btn" . $news_id; ?>" onclick="showPayDiv(this);document.getElementById('inf_<?php echo $news_id; ?>').style.display = 'none';document.getElementById('pri_<?php echo $news_id; ?>').style.display = 'none';document.getElementById('buy_item_btn<?php echo $news_id; ?>').style.display = 'none'; " data-type = "up4sale"><label for="<?php echo "buy_item_btn" . $news_id; ?>" onclick="document.getElementById('commentholder<?php echo $news_id; ?>').style.display = 'block';">


<div class="tooltip"><i style="cursor: pointer;" id="<?php echo "buy_icon" . $news_id; ?>" class="fa fa-shopping-cart" style="color: green;">

            <span class="tooltiptext">Buy Item</span>
</i>
        </div>
</label>
</button>
<div id="<?php echo 'commentholder' . $news_id;?>" style="width: 100%;"> </div>

        <?php if($chk_cnt == 1) {break;} }  ?>
      <?php } ?>
  <?php } ?>
<?php } ?>
<?php if(isset($i)) { ?>
<span id="latest_up4sale_sku<?php echo $yardsale_cnt + 1; ?>" data-sku = "<?php echo $i; ?>" style="display: none;"></span>
<?php } ?>