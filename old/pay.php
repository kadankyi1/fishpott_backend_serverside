<?php
session_start(); 
require_once("config.php");
include(ROOT_PATH . 'inc/db_connect.php');

$new_news_id = $_GET["nid"];
$type = $_GET["type"];
$fold = $_GET["fold"];
$login = $_GET["login"];
$u_type = $_GET["u_type"];
$i_country = $_GET["i_country"];
$news_id = $new_news_id;
$id_strt = uniqid($news_id, TRUE);
$t = time();
$r_t = date("Y-m-d",$t);
$ext = $r_t . $t;

//$invoice_id = $type . "_" . $id_strt . $ext;
$invoice_id = $type;
//$invoice_id = $type . "_" . $news_id;
$s_index = "a" . uniqid();

$table_name = "investor";

$item_1 = "net_worth";

$column1_name = "investor_id";
$column1_value = $_SESSION["user_sys_id"];
$pam1 = "s";

include(ROOT_PATH . 'inc/select1_where1_prepared_statement.php');
if($done == 1 && $item_1 != "" && $item_1 != "coins_secure_datetime" ) {

  $pc = $item_1;

}
include(ROOT_PATH . 'inc/db_connect.php');
//http://www.localhost/FISHPOT/user/?fold=e0c025f3943da706b49533d63f8e0d2b&e_o=82cfa808b95d08ccb998337cd8c362c8&login=70f40e0d49aa0121f92d8c7b4b12e403&u_type=6172ad9da8095756b63d7c58077f286f
$_SESSION[$s_index][0] = $invoice_id;
$_SESSION[$s_index][1] = $new_news_id;
$_SESSION[$s_index][2] = $type;
$success_url = "https://fishpott.com/user/index.php?fold=" . $fold .  "&e_o=82cfa808b95d08ccb998337cd8c362c8&st=" . $s_index . "&login=" . $login . "&u_type=" . $u_type;
$cancelled_url = "https://fishpott.com/user/index.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$deferred_url = "https://fishpott.com/user/index.php?fold=" . $fold .  "&login=" . $login . "&u_type=" . $u_type;
$merchant_key = "I will get this from the site";

if($type == "up4sale") {
      $table_name = "up4sale";
      $item_1 = "up4sale_news_id";
      $item_2 = "item_name";
      $item_3 = "item_price";
      $item_4 = "item_description";
      $item_5 = "item_location";
      $item_6 = "currency";
      $item_7 = "sale_status";
      $item_8 = "item_quantity";
      $item_9 = "item_delivery";

      $column1_name = "up4sale_news_id";
      $column1_value = $new_news_id;
      $pam1 = "s";

      include(ROOT_PATH . 'inc/select9_where1_prepared_statement.php');

    if($done == 1 && $item_1 != "" && $item_1 != "up4sale_news_id" && $item_7 == 0 && $item_9 == "FishFerry"){ 
        if($item_6 == "GHS") {

          $seller_country = "Ghana";

        } elseif($item_6 == "GBP") {

          $seller_country = "United Kingdom";

        } else {

          $seller_country = "USA";
        }
        $convert_amt = $item_3;
       include(ROOT_PATH . 'inc/currency_converter.php');
    if(isset($new_amt_user_str) && $new_amt_user_str != ""){ 
      $_SESSION[$s_index][3] = $new_amt_user_str;

      if(isset($pc) && $pc != ""){
          if($i_country == "Ghana"){

              $coins2curr = $pc / $coins_GHS;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_GHS);
                $newcoins = intval($newcoins);
              }

          } else if($i_country == "United Kingdom"){

              $coins2curr = $pc / $coins_GBP;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_GBP);
                $newcoins = intval($newcoins);
              }
              
          } else {

              $coins2curr = $pc / $coins_USD;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_USD);
                $newcoins = intval($newcoins);
              }
          }
      }

?>

<div id = "wholebuy_div<?php echo $new_news_id; ?>">
<span onclick="hideBuyDiv(this)" id="close_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" class="w3-hover-text-grey w3-closebtn w3-right">
    <i class="fa fa-remove"></i>
</span>
<?php if($item_8 > 1) { ?>
    <p id="quan_sec<?php echo $new_news_id; ?>" style="text-align: center">
            <input id="quan_<?php echo $new_news_id; ?>" style="width : 100%; text-align: center;"  type="number" min="1" max="<?php echo $item_8; ?>" name="quan" placeholder="Quantity ( max : <?php echo $item_8; ?> )" data-newsid="<?php echo $new_news_id; ?>"><br><br>
            <input onclick="setSaleQuantity(this)" data-onecost="<?php echo $new_amt_pg; ?>" data-max="<?php echo $item_8; ?>" id="setq_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" style="width : 30%;" type="submit" value="Continue" class="w3-btn w3-green w3-round">
    </p>
    <p id="da_sec<?php echo $new_news_id; ?>" style="text-align: center; display: none;">
<?php } else { ?>
    <p id="da_sec<?php echo $new_news_id; ?>" style="text-align: center;">
<?php } ?>
    DELIVERY ADDRESS<br><br>
            <button onclick="getLocation(this);" id="geo_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" data-type="<?php echo $type; ?>" data-price="<?php echo $type; ?>" class="w3-btn w3-green w3-round"> Use My Current Location</button>    
            <button onclick="typeManualLoc(this)" data-newsid="<?php echo $new_news_id; ?>" class="w3-btn w3-grey w3-round" id="type_add<?php echo $new_news_id; ?>"> Type My Address / Location</button><br>
            <span style="width : 100%; display: none" id="inp_add<?php echo $new_news_id; ?>">  <br> 
            <input id="my_add_<?php echo $new_news_id; ?>" style="width : 100%; text-align: center;"  type="text" name="address" placeholder="Ferry Address / Street Address" oninput="ManualLocReady(this);" data-newsid="<?php echo $new_news_id; ?>"><br>
            <input id="my_reg_<?php echo $new_news_id; ?>" style="width : 100%; text-align: center;"  type="text" name="address" placeholder="Region/State" oninput="ManualLocReady(this)" data-newsid="<?php echo $new_news_id; ?>"><br>            
            <input id="my_country_<?php echo $new_news_id; ?>" style="width : 100%; text-align: center;"  type="text" name="address" placeholder="Country" oninput="ManualLocReady(this)" data-newsid="<?php echo $new_news_id; ?>"><br>            

            <br>
            <p id="geo_got<?php echo $new_news_id; ?>" data-gotcoor = "0" style="width : 100%; text-align: center;">
                    
            </p><br><br>
            </span>
            <input onclick="delAddressComplete(this)" id="manual_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" style="width : 100%; display: none" type="submit" value="Continue" class="w3-btn w3-green w3-round">

    </p>
    <span id="gotopay<?php echo $new_news_id; ?>" style="display: none;" >
    <div class="w3-container w3-green">
    <p style="text-align: center">ITEM      :      <?php echo $item_2;?><strong id="q_holder_<?php echo $new_news_id; ?>"></strong></p>
    </div>    
    <div class="w3-container w3-grey">
    <p style="text-align: center">PRICE      :      <strong id="t_holder_<?php echo $new_news_id; ?>"><?php echo $new_amt_user_str;?></p>
    </div>    
    <div class="w3-container w3-green">
    <p style="text-align: center">DELIVERY CHARGE      :     <span id="del_char_str<?php echo $new_news_id; ?>">NOT SET. You will be contacted with this cost shortly after payment</span><br><button onclick="setNewDelChar(this)" type="button" data-ip = "<?php echo $new_amt_pg; ?>" data-delcharge = "0" data-ct = "r" data-newsid = "<?php echo $new_news_id ?>" id="delchg<?php echo $new_news_id; ?>" class="w3-btn w3-light-grey w3-round"><img style="display: none" id="delchgload<?php echo $new_news_id; ?>"  src="../img/load.gif" height="20px" width="20px"> <span id="delchgbtninfo<?php echo $new_news_id; ?>">Use Express(24-hours)</span> </button></p>                

    </div>    
    <div class="w3-container w3-grey">
    <p style="text-align: center">TOTAL CHARGE      :      <span id="total_char_str<?php echo $new_news_id; ?>"><?php echo $new_amt_user_str;?></span></p>
    </div>    
        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />
      <?php if($i_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP" id="curr_curr_<?php echo $new_news_id; ?>"  readonly="readonly" />
      <?php } elseif ($i_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS"  id="curr_curr_<?php echo $new_news_id; ?>" readonly="readonly" />
      <?php } elseif ($i_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  id="curr_curr_<?php echo $new_news_id; ?>" readonly="readonly" />
      <?php } ?>
        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input id="totl_char<?php echo $new_news_id; ?>" style="display: none" name=total type=text size=10 value="<?php echo $new_amt_pg; ?>"  readonly="readonly" /><br>
        <p style="width: 100%;text-align: center; align-content: center;">
        <input class="w3-btn w3-green w3-round" type=submit value="PROCEED" />
        <?php if(isset($pc) && $coins2curr > $new_amt_pg) { ?>

        <button type="button" onclick="buyWithCoins(this)" id="coinsbtn_<?php echo $new_news_id; ?>" data-newsid = "<?php echo $new_news_id ?>" data-st = "<?php echo $s_index ?>" data-totalcoinscharge = "<?php echo $newcoins ?>" class="w3-btn w3-grey w3-round"><i class="fa fa-cart-arrow-down"></i> Buy With My Pott Coins</button>        
        <button onclick="pod(this)" type="button" data-newsid = "<?php echo $new_news_id ?>" data-st = "<?php echo $s_index ?>" data-totalcoinscharge = "<?php echo $newcoins ?>"  class="w3-btn w3-red w3-round"><i class="fa fa-cart-truck"></i>Pay On Delivery</button>      

        <?php } else { ?>

        <button type="button" style="display: none;" id="coinsbtn_<?php echo $new_news_id; ?>" data-newsid = "<?php echo $new_news_id ?>" data-st = "<?php echo $s_index ?>" data-currcoins = "<?php echo $newcoins ?>" class="w3-btn w3-grey w3-round"><i class="fa fa-cart-arrow-down"></i> Buy With My Pott Coins</button>        

        <?php } ?>
        </form>    
    </span>
<p id="bwc<?php echo $new_news_id; ?>" style="text-align: center;display: none;"><img  src="../img/loadfp.gif" height="60px" width="60px"></p>
</div>
<?php } else {?>

<div id = "wholebuy_div<?php echo $new_news_id; ?>">
   <span onclick="hideBuyDiv(this)" id="buyspan<?php echo $new_news_id; ?>" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
  <span id="get_add_section<?php echo $new_news_id; ?>">
    <p style="text-align: center">FAILED</p>
    </span>
    </p>
</div>
    <?php } ?>
<?php } elseif($item_7 == 0 && $item_9 != "FishFerry"){ ?>
<div id = "wholebuy_div<?php echo $new_news_id; ?>">
   <span onclick="this.parentElement.style.display='none'" id="buyspan<?php echo $new_news_id; ?>" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
  <span id="get_add_section<?php echo $new_news_id; ?>">
      <p style=" width: 100%; text-align: center;"><img src="../img/tick.png"  style="width: 5%; height: 5%;"><br><br><strong>You Must Meet With The Seller To Buy This Item<br><span style="color: grey; font-size: x-small;">Please ensure you meet in a public place</span></strong></p>
    </span>
    </p>
</div>
<?php } else { ?>
<div id = "wholebuy_div<?php echo $new_news_id; ?>">
   <span onclick="this.parentElement.style.display='none'" id="buyspan<?php echo $new_news_id; ?>" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
  <span id="get_add_section<?php echo $new_news_id; ?>">
      <p style=" width: 100%; text-align: center;"><img src="../img/cross.png"  style="width: 5%; height: 5%;"><br><br><strong>This item has been sold</strong></p>
    </span>
    </p>
</div>
<?php } ?>
<?php } elseif($type == "fundraiser"){?>
<div>
   <span onclick="this.parentElement.style.display='none'" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>

    <div class="w3-container" id="set_amt_div<?php echo $new_news_id; ?>">    
    <p style="text-align: center; width: 100%">How Much Do You Want To Help With?</p>
    <p style="text-align: center; width: 100%">
        <input style="width: 50%; text-align: center;" id="totl_contr<?php echo $new_news_id; ?>" placeholder="Contribution Amount" name=total type=number min="1" size=10  /><br>
    </p>
    <p style="text-align: center; width: 100%">
      <input onclick="setContriAmt(this)" id="manual_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" style="width : 100%;" type="submit" value="Continue" class="w3-btn w3-green w3-round">
    </p>
    </div>
        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />
      <?php if($i_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP"  readonly="readonly" />
      <?php } elseif ($i_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS"  readonly="readonly" />
      <?php } elseif ($i_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  readonly="readonly" />
      <?php } ?>
        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input style="width: 50%;display: none" id="totl_char<?php echo $new_news_id; ?>" name=total type=text size=10  />
    <br>
    <p></p><br>
    <div class="w3-container w3-grey" style="display: none" id="con_amt_div<?php echo $new_news_id; ?>">
    <p style="text-align: center">Contribution      :      <span id="con_amt_str<?php echo $new_news_id; ?>">  <?php ech ?></span></p>
    </div>
    <div class="w3-container" style="display: none" id="subm_fund<?php echo $new_news_id; ?>">
        <p style="width: 100%;text-align: center; align-content: center;">
        <input  class="w3-btn w3-green w3-round" type=submit value="PROCEED" />
        </p>
    </div>
        </form>
</div>    
<?php } elseif($type == "shares4sale"){?>
<?php 
      $table_name = "shares4sale";
      $item_1 = "shares4sale_owner_id";
      $item_2 = "parent_shares_id";
      $item_3 = "sale_status";
      $item_4 = "sharesOnSale_id";
      $item_5 = "selling_price";
      $item_6 = "currency";
      $item_7 = "num_on_sale";

      $column1_name = "shares_news_id";
      $column1_value = $new_news_id;
      $pam1 = "s";

      include(ROOT_PATH . 'inc/select7_where1_prepared_statement.php');

    if($done == 1 && $item_1 != "" && $item_1 != "shares_news_id" && $item_3 == 0 && $item_7 != 0){ 
      
      include(ROOT_PATH . 'inc/db_connect.php');

      $query = "SELECT num_of_shares FROM shares_owned WHERE share_id = '$item_4'";   
      $result = $mysqli->query($query);

      if (mysqli_num_rows($result) != 0) {

            $row = $result->fetch_array(MYSQLI_ASSOC);
            if($item_7 > $row["num_of_shares"]) {

                $item_7 = $row["num_of_shares"];

            }
        }


        if($item_6 == "GHS") {

          $seller_country = "Ghana";

        } elseif($item_6 == "GBP") {

          $seller_country = "United Kingdom";

        } else {

          $seller_country = "USA";
        }
        $convert_amt = $item_5;
       include(ROOT_PATH . 'inc/currency_converter.php');


?>

<div>
   <span onclick="this.parentElement.style.display='none'" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>

    <div class="w3-container" id="set_amt_div<?php echo $new_news_id; ?>">    
    <p style="text-align: center; width: 100%" id="info_shares<?php echo $new_news_id; ?>">How Many Shares Do You Want To Buy?</p>
    <p style="text-align: center; width: 100%">
        <input style="width: 50%; text-align: center;" id="totl_buyingshares<?php echo $new_news_id; ?>" placeholder="Number Of Shares(Max : <?php echo $item_7; ?> )" name=total type=number min="1" max = "<?php echo $item_7; ?>" data-costpershare="<?php echo $new_amt_pg; ?>"  size=10  /><br>
    </p>
    <p style="text-align: center; width: 100%">
      <input onclick="setTotalSharesAmt(this)" id="manual_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" style="width : 100%;" type="submit" value="Continue" class="w3-btn w3-green w3-round">
    </p>
    </div>
        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />
      <?php if($i_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP"  readonly="readonly" />
      <?php } elseif ($i_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS"  readonly="readonly" />
      <?php } elseif ($i_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  readonly="readonly" />
      <?php } ?>
        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input style="width: 50%;display: none" id="totl_char<?php echo $new_news_id; ?>" name=total type=text size=10  />
    <br>
    <p></p><br>
    <div class="w3-container w3-grey" style="display: none" id="con_amt_div<?php echo $new_news_id; ?>">
    <p style="text-align: center">TOTAL SHARES PRICE      :      <span id="con_amt_str<?php echo $new_news_id; ?>">  <?php ech ?></span></p>
    </div>
    <div class="w3-container" style="display: none" id="subm_fund<?php echo $new_news_id; ?>">
        <p style="width: 100%;text-align: center; align-content: center;">
        <input  class="w3-btn w3-green w3-round" type=submit value="PROCEED" />
        </p>
    </div>
        </form>
</div>
<?php } else if ($item_3 == 1 ||  $item_7 == 0) { ?>    
<div id = "sharesold<?php echo $new_news_id; ?>">
   <span onclick="this.parentElement.style.display='none'" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
  <span id="get_add_section<?php echo $new_news_id; ?>">
    <p style="text-align: center; color: red">Ooops....THESE SHARES ARE SOLD OUT</p>
    </span>
    </p>
</div>
<?php } ?>
<?php } elseif($type == "event"){?>
<?php 
      $table_name = "event";
      $item_1 = "event_news_id";
      $item_2 = "event_id";
      $item_3 = "ticket_cost";
      $item_4 = "verified_tag";
      $item_5 = "num_of_goers";
      $item_6 = "currency";

      $column1_name = "event_news_id";
      $column1_value = $new_news_id;
      $pam1 = "s";

      include(ROOT_PATH . 'inc/select6_where1_prepared_statement.php');

    if($done == 1 && $item_1 != "" && $item_1 != "event_news_id"){ 

        if($item_6 == "GHS") {

          $seller_country = "Ghana";

        } elseif($item_6 == "GBP") {

          $seller_country = "United Kingdom";

        } else {

          $seller_country = "USA";
        }
        $convert_amt = $item_3;
       include(ROOT_PATH . 'inc/currency_converter.php');
      if(isset($pc) && $pc != ""){
      $_SESSION[$s_index][3] = $new_amt_user_str;
          if($i_country == "Ghana"){

              $coins2curr = $pc / $coins_GHS;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_GHS);
                $newcoins = intval($newcoins);
              }

          } else if($i_country == "United Kingdom"){

              $coins2curr = $pc / $coins_GBP;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_GBP);
                $newcoins = intval($newcoins);
              }

          } else {

              $coins2curr = $pc / $coins_USD;
              if($coins2curr > $new_amt_pg) {

                $newcoins = $pc - ($new_amt_pg * $coins_USD);
                $newcoins = intval($newcoins);
              }
          }
      }
?>
<div>
   <span onclick="this.parentElement.style.display='none'" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
    <p id="quan_sec<?php echo $new_news_id; ?>" style="text-align: center">
            <input id="quan_<?php echo $new_news_id; ?>" style="width : 100%; text-align: center;"  type="number" min="1" max="1000000" name="quan" placeholder="Number Of Tickets" data-newsid="<?php echo $new_news_id; ?>"><br><br>
            <input onclick="setESaleQuantity(this)" data-onecost="<?php echo $new_amt_pg; ?>" data-max="1000000" id="setq_<?php echo $new_news_id; ?>" data-newsid="<?php echo $new_news_id; ?>" style="width : 30%;" type="submit" value="Continue" class="w3-btn w3-green w3-round">
    </p>

        <form method=POST action="https://community.ipaygh.com/gateway">
        <input type=hidden name=merchant_key value="4d2ca63e-4d11-11e7-8a33-f23c9170642f"  readonly="readonly" />
      <?php if($i_country == "United Kingdom") { ?>
        <input type=hidden name=currency value="GBP"  id="curr_curr_<?php echo $new_news_id; ?>" readonly="readonly" />
      <?php } elseif ($i_country == "Ghana")  { ?>
        <input type=hidden name=currency value="GHS" id="curr_curr_<?php echo $new_news_id; ?>"  readonly="readonly" />
      <?php } elseif ($i_country != "Ghana" && $i_country != "United Kingdom")  { ?>
        <input type=hidden name=currency value="USD"  id="curr_curr_<?php echo $new_news_id; ?>" readonly="readonly" />
      <?php } ?>
        <input type=hidden name=success_url value="<?php echo $success_url; ?>"  readonly="readonly" />
        <input type=hidden name=cancelled_url value="<?php echo $cancelled_url; ?>"  readonly="readonly" />
        <input type=hidden name=deferred_url value="<?php echo $deferred_url; ?>"  readonly="readonly" />
        <input style="display: none" type=text name=invoice_id value="<?php echo $invoice_id; ?>"  readonly="readonly" />
        <input style="width: 50%;display: none" id="totl_char<?php echo $new_news_id; ?>" value="<?php echo $new_amt_pg; ?>" name=total type=text size=10  />
    <br>
    <p></p><br>
    <span style="display: none;" id="tic_amt_span<?php echo $new_news_id; ?>">
    <div class="w3-container w3-grey" id="tic_amt_div<?php echo $new_news_id; ?>">
    <p style="text-align: center">TICKET(s) PRICE      :      <span id="evt_amt_str<?php echo $new_news_id; ?>">  <?php echo $new_amt_user_str; ?></span></p>
    </div>
    <div class="w3-container" id="subm_fund<?php echo $new_news_id; ?>">
        <p style="width: 100%;text-align: center; align-content: center;">
        <input  class="w3-btn w3-green w3-round" type=submit value="PROCEED TO PAYMENT & GET TICKET CODE"   />
        <?php if(isset($pc) && $coins2curr > $new_amt_pg) {  ?>

        <button type="button" onclick="buyWithCoinsEvt(this)" id="coinsbtn_<?php echo $new_news_id; ?>" data-newsid = "<?php echo $new_news_id ?>" data-st = "<?php echo $s_index ?>" data-totalcoinscharge = "<?php echo $newcoins ?>" class="w3-btn w3-grey w3-round"><i class="fa fa-cart-arrow-down"></i> Buy With My Pott Coins</button>     
           
        <?php } else { ?>
        <button type="button" style="display: none" id="coinsbtn_<?php echo $new_news_id; ?>" data-newsid = "<?php echo $new_news_id ?>" data-st = "<?php echo $s_index ?>" data-currcoins = "<?php echo $newcoins ?>" data-iquan = "1" class="w3-btn w3-grey w3-round"><i class="fa fa-cart-arrow-down"></i> Buy With My Pott Coins</button>        
        <?php } ?>
        </p>
    </div>
    </span>
        </form>
<p id="bwc<?php echo $new_news_id; ?>" style="text-align: center;display: none;"><img  src="../img/loadfp.gif" height="60px" width="60px"></p>
</div> 

<?php } else { ?>
<div id = "wholeevt_div<?php echo $new_news_id; ?>">
   <span onclick="hideBuyDiv(this)" id="evtspan<?php echo $new_news_id; ?>" class="w3-hover-text-grey w3-closebtn">
    <i class="fa fa-remove"></i>
  </span>
  <span id="get_add_section<?php echo $new_news_id; ?>">
    <p style="text-align: center">FAILED</p>
    </span>
    </p>
</div>
<?php } ?>

<?php } ?>