<?php
session_start(); 

require_once("../inc/config.php"); 
$config = "yes";
include(ROOT_PATH . 'inc/db_connect.php');
$p_country = trim($_GET["p_country"]);
$shares_cnt = trim($_GET["shares_cnt"]);

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
$table1_name = "shares4sale";

  include(ROOT_PATH . 'inc/db_connect.php');
    if($item_1 != "" && $item_1 != "sku") {
      $chk_cnt = 0; $i = $item_1;
      for ($i; $i > 0; $i--)  {
      $table_name = "shares4sale";
      $item_1 = "shares_news_id";
      $item_2 = "sharesOnSale_id";
      $item_3 = "selling_price";
      $item_4 = "num_on_sale";
      $item_5 = "parent_shares_id";
      $item_6 = "num_on_sale";
      $item_7 = "currency";

      $column1_name = "shares4sale_owner_id";
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
        if($done == 1 && $item_1 != "" && $item_1 != "shares_news_id"){ 
          $shares_id = $item_2;
          $shares_news_id = $item_1;
          $shares_price = $item_3;
          $table_name = "newsfeed";
          $item_1 = "inputtor_type";
          $item_2 = "news";

          $column1_name = "news_id";
          $column1_value = $shares_news_id;
          $pam1 = "s";

          include(ROOT_PATH . 'inc/select2_where1_prepared_statement.php');
          include(ROOT_PATH . 'inc/db_connect.php');

          if($done == 1 && $item_1 != "" && $item_1 != "inputtor_type"){ 
                  $news_image = $item_1;
                  $news = $item_2;

                  $table_name = "shares_worso";
                  $item_1 = "parent_company_name";
                  $item_2 = "country_origin";
                  $item_3 = "shares_logo";

                  $column1_name = "parent_shares_id";
                  $column1_value = $item_5;
                  $pam1 = "s";

                  include(ROOT_PATH . 'inc/select3_where1_prepared_statement.php');
                  include(ROOT_PATH . 'inc/db_connect.php');

                if($done == 1 && $item_1 != "" && $item_1 != "company_name"){ 
                    $chk_cnt = $chk_cnt + 1;
                    $company_name = $item_1;

                    $news_id = $shares_news_id;
                    $id_strt = uniqid($news_id, TRUE);
                    $t = time();
                    $r_t = date("Y-m-d",$t);
                    $ext = $r_t . $t;

                    $invoice_id = "shares" . $id_strt . $ext;
                    if($item_7 == "GHS"){

                    $seller_country = "Ghana";

                    } elseif($item_7 == "GBP"){

                    $seller_country = "United Kingdom";

                    } else {

                    $seller_country = "USA";

                    }
                    $convert_amt = $shares_price;
                    $i_country = $my_country;
                    include(ROOT_PATH . 'inc/currency_converter.php');
  ?>

  <div class="w3-white w3-round w3-third">
   <center>
   <div style="height: 170px; border:solid 1px #d2d2d2; margin-top: 10px; width: 95%" class="w3-round">
     <a href="../user/<?php echo $item_3; ?>" id="<?php echo $news; ?>" data-lightbox="<?php echo $news; ?>" data-title="<?php echo htmlspecialchars($news);?>"><img src="../user/<?php echo $item_3; ?>" width="100%" height="100%"></a><br>
     
   </div>
     <p  style="display: inline-block; padding-right: 20px; color: black;">
     <span style="display: inline-block;word-wrap: break-word; width: 85%; height: 30px; text-overflow: ellipsis;font-weight: bold;"><?php echo $item_1 . " : " . $item_2; ?></span> <br><hr>
     <span style="display: inline-block;"><?php echo 'Price Per Shares : ' .   ceil($new_amt_pg) . " " . $currency ; ?></span> <br>
<?php if(isset($_SESSION["user_sys_id"]) && $_SESSION["user_sys_id"] != "") { ?>

      <input id="sn_inp_<?php echo $shares_news_id; ?>" type="number" style="width: 75%;display: none" placeholder="How Many Shares"  max ='<?php echo $item_4; ?>' min='1' name="num_of_shares<?php echo $shares_news_id; ?>"><hr>
        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />

        <input type=hidden name=currency value="<?php echo $currency; ?>"  readonly="readonly" />
        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input style="width: 75%; display: none" min="1" id="totl_char<?php echo $shares_news_id; ?>" name=total type=number size=10  />

        <input style="display: none" id="s4s_sub<?php echo $shares_news_id; ?>" class="w3-btn w3-green w3-round" type=submit value="PROCEED" />
        </form>
    <button style="display: none" id="setsharesnum_<?php echo $shares_news_id; ?>"  data-pps = "<?php echo ceil($new_amt_pg); ?>" data-newsid="<?php echo $shares_news_id; ?>" type="button" class="w3-btn w3-theme-l4 w3-round" style="border:solid 1px #d2d2d2;" onclick = "setsharesTotalCharg(this);"></i> Proceed</button>

    <button id="buyshares_<?php echo $shares_news_id; ?>" data-newsid="<?php echo $shares_news_id; ?>" onclick="setNumInp(this);" type="button" class="w3-btn w3-theme-l4 w3-round" style="border:solid 1px #d2d2d2;"><i class="fa fa-shopping-cart w3-margin-right"></i> Buy Shares</button>
    </p>  
<?php } else { ?>
    <a href="../index.php">
        <button id="eventbtn" type="button" class="w3-btn w3-green w3-round" style="border:solid 1px #d2d2d2;"><i class="fa fa-shopping-cart w3-margin-right"></i> Get Ticket </button>
      </p>
    </a>
  <?php } ?>
          <?php if($chk_cnt == 1) {break;} }  ?>
      <?php } ?>
    <?php } ?>
  <?php } ?>
<?php } ?>
    </div>
<?php if(isset($i)) { ?>
<span id="latest_shares_sku<?php echo $shares_cnt + 1; ?>" data-sku = "<?php echo $i; ?>" style="display: none;"></span>
<?php } ?>