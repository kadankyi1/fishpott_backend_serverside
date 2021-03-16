  <?php 
session_start(); 

require_once("../inc/config.php"); 
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');
$p_country = trim($_GET["p_country"]);
$fnd_cnt = trim($_GET["fnd_cnt"]);
if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") { 

    $fold = $_SESSION["e_user"];
    $login = $_SESSION["login_type"];
    $u_type = $_SESSION["user_type"];
    $my_country = trim($_GET["my_country"]);
$success_url = "https://fishpott.com/user/pay/success.php?fold=" . $fold .  "&saletype=" . $login . "&u_type=" . $u_type;
$cancelled_url = "https://fishpott.com/user/pay/deffered.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$deferred_url = "https://fishpott.com/user/pay/cancelled.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$merchant_key = "I will get this from the site";

if($my_country == "Ghana"){

$placeholder = "Amount In GHc";

} elseif($my_country == "United Kingdom"){

$placeholder = "Amount In GBP";

} else {

$placeholder = "Amount In USD";

}


} else {

  $success_url = "https://fishpott.com/user/pay/success.php";
  $cancelled_url = "https://fishpott.com/user/pay/deffered.php";
  $deferred_url = "https://fishpott.com/user/pay/cancelled.php";
  $merchant_key = "I will get this from the site";
  $my_country = $p_country;
  if($p_country == "Ghana"){

  $placeholder = "Amount In GHc";

  } elseif($p_country == "United Kingdom"){

  $placeholder = "Amount In GBP";

  } else {

  $placeholder = "Amount In USD";

  }
}

  $item_1 = $_GET["ls"];
  $p_investor_id = $_GET["pi"];
  $table_name = "fundraiser";
  $item_1 = $item_1 - 1;
  include(ROOT_PATH . 'inc/db_connect.php');
    if($item_1 != "" && $item_1 != "sku") {
      $chk_cnt = 0; $i = $item_1;
      for ($i; $i > 0; $i--)  {
      $table_name = "fundraiser";
      $item_1 = "f_news_id";
      $item_2 = "contributed_amount";
      $item_3 = "fundraiser_name";
      $item_4 = "start_date";
      $item_5 = "target_amount";
      $item_6 = "num_of_contributors";

      $column1_name = "f_starter_id";
      $column1_value = $p_investor_id;
      $pam1 = "s";

      $column2_name = "flag";
      $column2_value = 0;
      $pam2 = "i";

      $column3_name = "sku";
      $column3_value = $i;
      $pam3 = "i";
      include(ROOT_PATH . 'inc/select6_where3_prepared_statement.php');
      include(ROOT_PATH . 'inc/db_connect.php');
        if($done == 1 && $item_1 != "" && $item_1 != "f_news_id"){ 
          $contributed_amount = $item_2;
          $f_news_id = $item_1;
          $fundraiser_name = $item_3;
          $table_name = "newsfeed";
          $item_1 = "news_image";
          $item_2 = "news";

          $column1_name = "news_id";
          $column1_value = $f_news_id;
          $pam1 = "s";

          include(ROOT_PATH . 'inc/select2_where1_prepared_statement.php');
          include(ROOT_PATH . 'inc/db_connect.php');

          if($done == 1 && $item_1 != "" && $item_1 != "news_image"){ 
              $chk_cnt = $chk_cnt + 1;

              $news_id = $f_news_id;
              $id_strt = uniqid($news_id, TRUE);
              $t = time();
              $r_t = date("Y-m-d",$t);
              $ext = $r_t . $t;

              $invoice_id = "fundraiser_" . $id_strt . $ext;
              $seller_country = $p_country;
              $i_country = $my_country;
              //include(ROOT_PATH . 'inc/currency_converter.php');

  ?>
<div class = "w3-white w3-round w3-third" style="margin-top: -10px;">
   <div style="height: 170px; border:solid 1px #d2d2d2; width: 95%" class="w3-round">
     <a href="../user/<?php echo $item_1; ?>" id="<?php echo $item_1; ?>" data-lightbox="<?php echo $item_3; ?>" data-title="<?php if($contributed_amount > 0) { echo 'Total Contribution :  ' . $contributed_amount . '<br>Number Of Contributors : ' . $item_6; } ?>"><img src="../user/<?php echo $item_1; ?>" width="100%" height="100%"></a><br>
   </div>
    <center>
    <p  style="display: inline-block; padding-right: 20px; color: black;">
     <span style="display: inline-block;word-wrap: break-word; width: 85%; height: 30px; text-overflow: ellipsis;font-weight: bold;"><?php echo $item_2; ?></span> <br><hr>
    <?php if(isset($item_6) && $item_6 > 0){ ?><span style="color: green"><?php echo $item_6 . " People Have Contributed "; ?></span><br><hr><?php } ?>

        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />
<?php if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") { ?>
      <?php if($my_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP"  readonly="readonly" />
      <?php } elseif ($my_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS"  readonly="readonly" />
      <?php } elseif ($my_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  readonly="readonly" />
      <?php } ?>
<?php } else { ?>

      <?php if($p_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP"  readonly="readonly" />
      <?php } elseif ($p_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS"  readonly="readonly" />
      <?php } elseif ($p_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  readonly="readonly" />
      <?php } ?>

<?php } ?>

        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input style="width: 75%; display: none" min="1" required="required" id="totl_char<?php echo $f_news_id; ?>" placeholder = "<?php echo $placeholder;?>" name=total type=number size=10  /><br><p></p>

        <input style="display: none" id="sub_<?php echo $f_news_id; ?>" class="w3-btn w3-green w3-round" type=submit value="PROCEED" />
        </form>
    <button id="fnd<?php echo $f_news_id; ?>" data-amtinp = "totl_char<?php echo $f_news_id; ?>"  data-subbtn = "sub_<?php echo $f_news_id; ?>" type="button" onclick="setfndForm(this)" class="w3-btn w3-blue w3-round" style="border:solid 1px #d2d2d2;"><i class="fa fa-users w3-margin-right"></i> Contribute </button>
    </p>
    </center><br>    
    </div>         

    <?php if($chk_cnt == 1) {break;} }  ?>
      <?php } ?>
  <?php } ?>
<?php } ?>

<?php if(isset($i)) { ?>
<span id="latest_fnd_sku<?php echo $fnd_cnt + 1; ?>" data-sku = "<?php echo $i; ?>" style="display: none;"></span>
<?php } ?>