    <div class="container" >
        <div class="left">

            <div class="top">
                <input type="text" id="get_new_chat_keyword" oninput ="getNewChat()" />
                <a href="javascript:;" class="search" onclick="getNewChat()"></a>
            </div>
            <div style=" width: 100%; height: calc(80% + 23px)">
            <div class="people" id="chatters_div">


<?php $item_1 = "sku"; $i_chats = 0; ?>
<?php $table_name = "linkups"; ?>
<?php include(ROOT_PATH . 'inc/db_connect_autologout.php'); ?>
<?php 

include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php'); 
include(ROOT_PATH . 'inc/db_connect_autologout.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

  $query = "SELECT * FROM linkups WHERE (sender_id = '$investor_id' AND status = 1 AND sku = $i_notif) OR (receiver_id = '$investor_id' AND status = 1 AND sku = $i_notif)";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $status = $row["status"];
      $sender_id = $row["sender_id"];
      $receiver_id = $row["receiver_id"];
      $done = 1;
      if($sender_id == $investor_id){

        $not_investor_id = $receiver_id;
        $akasakasa_id = $investor_id . $not_investor_id;

      } else {

        $not_investor_id = $sender_id;
        $akasakasa_id = $investor_id . $not_investor_id;
      }

    $query = "SELECT * FROM akasakasa WHERE (sender_id = '$investor_id' AND receiver_id = '$not_investor_id') OR (sender_id = '$not_investor_id' AND receiver_id = '$investor_id')";

    $result = $mysqli->query($query);

    if (mysqli_num_rows($result) != "0") {

        $row = $result->fetch_array(MYSQLI_ASSOC);
        $sender_id = $row["sender_id"];
        $receiver_id = $row["receiver_id"];
        $akasakasa_id = $row["akasakasa_id"];
        $latest_kasa_sku = $row["latest_kasa_sku"];
        $strStart = $row["latest_date_time"];

        include(ROOT_PATH . 'inc/time_converter.php');
        $done = 1;

        if($sender_id == $investor_id){

          $not_investor_id = $receiver_id;

        } else {

          $not_investor_id = $sender_id;
          
        }

      } else {

          $query = "INSERT INTO akasakasa (sku, akasakasa_id, sender_id, receiver_id, latest_kasa_sku, latest_date_time) VALUES (NULL, '$akasakasa_id', '$investor_id', '$not_investor_id', 1, '2017-06-07 04:31:42');";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if ($result != "0") {

            $akasa_created = 1;

            } else {

                $done = 0;
                $akasa_created = 0;
              }
          
        }



    } else {

        $done = 0;
        
      }

  if($done == 1 && $sender_id != "" && $not_investor_id != "") {

      $table_name = "investor";

      $item_1 = "first_name";
      $item_2 = "last_name";
      $item_3 = "investor_id";
      $item_4 = "country";
      $item_5 = "profile_picture";

      $column1_name = "investor_id";

      $column1_value = $not_investor_id;

      $pam1 = "s";
      $skip = 1;
      include(ROOT_PATH . 'inc/db_connect_autologout.php');
      include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');
      include(ROOT_PATH . 'inc/db_connect_autologout.php');
      if($done == 1 && $skip == 0) {

        $linker_name = $item_1;

      } else {

        $skip = 1;
      }

  } else {

      $skip = 1;
  }

}

$item_1 = "sku";
$table_name = "akasakasa";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
$chatters = array();
$i_chats = 0;
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php'); 
include(ROOT_PATH . 'inc/db_connect_autologout.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

  $query = "SELECT * FROM akasakasa WHERE (sender_id = '$investor_id' AND sku = $i_notif) OR (receiver_id = '$investor_id' AND sku = $i_notif)";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $sender_id = $row["sender_id"];
      $receiver_id = $row["receiver_id"];
      $akasakasa_id = $row["akasakasa_id"];
      $akasakasa_type = $row["akasakasa_type"];
      $latest_kasa_sku = $row["latest_kasa_sku"];
      $latest_date_time = $row["latest_date_time"];
      if(trim($latest_date_time) != "2017-06-07 04:31:42"){

          $strStart = $row["latest_date_time"];
          include(ROOT_PATH . 'inc/time_converter.php');

      }
      $done = 1;

      if($sender_id == $investor_id){

        $not_investor_id = $receiver_id;

      } else {

        $not_investor_id = $sender_id;
        
      }

    } else {

        $done = 0;
        
      }

  if($done == 1 && $sender_id != "" && $not_investor_id != "") {


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
          //echo "sharer_full_name : " . $sharer_full_name; exit;

    }

  } else {

      $skip = 1;
  }

if($done == 1 && $skip == 0 && $akasakasa_type != "auto"){
  $chatters[$i_chats] = array($not_investor_id,$akasakasa_id, $chatter_name, $latest_kasa_sku);
  $i_chats = $i_chats + 1;


  include(ROOT_PATH . 'inc/cnt_unread_msg_specific.php');
?>
<script type="text/javascript">

          akasakasa_old_id[akasa_cnt] = '<?php echo $akasakasa_id; ?>';
          akasakasa_old_sku[akasa_cnt] = '<?php echo $latest_kasa_sku; ?>';
          akasakasa_newmsg_set[akasa_cnt] = '0';


          var akasa_cnt = akasa_cnt + 1;
</script>


                <p class="person" onclick="setMsgRceiverId(this);scrollDown(this);getLatestMsgs(this);" data-chat="person<?php echo $i_chats; ?>"  data-akid = "<?php echo $akasakasa_id; ?>" data-latestkasasku = "<?php echo $latest_kasa_sku; ?>"  data-chatter-id="<?php echo $not_investor_id; ?>" id = "receiver_id_<?php echo $not_investor_id; ?>"  style="padding: 12px 10% 16px;margin:0;border:0;font-size:100%;font:inherit;vertical-align:baseline; cursor: pointer;line-height:1;height: 65px">
                <?php if ($item_5 != "") { ?>
                    <img src="../pic_upload/<?php echo $item_5; ?>" class="w3-left w3-margin-right" style="width: 40px;" alt="<?php echo $chatter_name; ?>">
                <?php } else { ?>    
                    <img src="../img/buddy_sample.png" class="w3-left w3-margin-right" class="w3-left w3-margin-right" style="width: 40px;" alt="<?php echo $chatter_name; ?>">
                <?php } ?>
                    <span class="name"><?php echo $chatter_name; ?></span>
                    <span class="time"><?php if(isset($date_time) && $date_time != "") { echo $date_time;} ?></span>
                    <span class="preview"><?php if(!isset($date_time) || $date_time == "") { echo "...";} ?></span>
                    
                    <span id="unread_msg_spc_<?php echo $akasakasa_id; ?>" data-num = "<?php echo $unread_msg_spf; ?>" class="time w3-badge  w3-small w3-green" style="padding-top: 5px; margin-top: 20px;<?php if($unread_msg_spf == '' || $unread_msg_spf == 0) { echo ' display: none'; }?>">
                        <strong style="font-size: x-small; color: white; margin-right: 5px;" >
                          <?php echo $unread_msg_spf; ?>                          
                        </strong>
                    </span>

                </p>
  <?php $date_time = ""; if($i_chats == 7) { break;} } ?>
<?php } ?>

           <p style="display: none; border: transparent;" id="preview_img_msg">
                <span><a style="float: right; color: black; margin-right: 10px; font-size: medium; border: transparent;" href="#" id="1" onclick="hidepreview(this)">&times;</a></span><img id="msg_imageReader" height="250px" width="250px" />
                <span id='msg_aud_holder' style="display : none"></span>
                </p>
                <p style="display : none; border: transparent;" id="emos_holder">
                <a style="float: right; color: black; margin-right: 10px; font-size: medium; border: transparent;" href="#" id="1" onclick="hidepreview(this)">&times;</a>

                <?php   include(ROOT_PATH . 'inc/emos.php'); ?>

                
                </p>
            </div>
            </div>
        </div>
        <div class="right" >
      <span style="display: none" id="chatzone">
        <div class="top"><span><span class="name" id="chatter_name_holder"></span></span></div>


        <?php 
        $i_chats = 0;
         foreach ($chatters as list($chatter_id, $cur_akasa_id)) {
                    $i_chats = $i_chats + 1;

        ?>
        <div class="chat" data-chat="person<?php echo $i_chats; ?>" id="chat<?php echo $cur_akasa_id; ?>">
          <span id="chatholder<?php echo $cur_akasa_id; ?>" class="chatholder" style='overflow: scroll; height:  500px; margin-right: -42px; margin-bottom: -45px; display: none'>
        <?php
            include(ROOT_PATH . 'inc/db_connect_autologout.php');
            //LATEST SKU
          $query = "SELECT sku FROM kasa WHERE akasakasa_id = '$cur_akasa_id' ORDER BY sku ASC";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $latest_msg_sku = $row["sku"];
              $skip = 0;
            } else {

              $skip = 1;

            }

          $query = "SELECT sku FROM kasa WHERE akasakasa_id = '$cur_akasa_id' ORDER BY sku DESC";

          //$numrows = mysql_num_rows($query);
          $result = $mysqli->query($query);

          if (mysqli_num_rows($result) != "0") {

              $row = $result->fetch_array(MYSQLI_ASSOC);
              $newest_msg_sku = $row["sku"];
              $skip = 0;
            } else {

              $skip = 1;

            }


          $msgs = array();
 
          if(isset($latest_msg_sku) && $latest_msg_sku != "" && $latest_msg_sku != 0 && $skip == 0){
            $msg_cnt = 0;
            for ($i_msg = $newest_msg_sku; $i_msg >= $latest_msg_sku; $i_msg--) {

                    $table_name = "kasa";
                    $item_1 = "kasa";
                    $item_2 = "kasa_date_time";
                    $item_3 = "status";
                    $item_4 = "sender_id";
                    $item_5 = "kasa_pic";
                    $item_6 = "kasa_vid";
                    $item_7 = "kasa_aud";

                    $column1_name = "sku";
                    $column1_value = $i_msg;

                    $column2_name = "akasakasa_id";
                    $column2_value = $cur_akasa_id;

                    $pam1 = "i";
                    $pam2 = "s";
                    $skip = 1;
                    include(ROOT_PATH . 'inc/db_connect_autologout.php');
                    include(ROOT_PATH . 'inc/select7_where2_prepared_statement.php');
                    include(ROOT_PATH . 'inc/db_connect_autologout.php');


                    if($done == 1 && $item_1 != "kasa" && $item_1 != "" && $skip == 0){
                        if($item_4 == $investor_id){

                          $bubble_type = "me";

                        } else {

                          $bubble_type = "you";

                        }
                  $strStart = $item_2;

                  include(ROOT_PATH . 'inc/time_converter.php');

                   $msgs[$msg_cnt]["bubble_type"] = $bubble_type;
                   $msgs[$msg_cnt]["this_msg"] = $item_1;
                   $msgs[$msg_cnt]["pic"] = $item_5;
                   $msgs[$msg_cnt]["vid"] = $item_6;
                   $msgs[$msg_cnt]["aud"] = $item_7;
                   $msgs[$msg_cnt]["status"] = $item_3;
                   $msgs[$msg_cnt]["sku"] = $i_msg;

                  if(isset($twelve_hr_date) && $twelve_hr_date != "") {

                         $msgs[$msg_cnt]["date"] = $twelve_hr_date; 

                       } else { 

                        $msgs[$msg_cnt]["date"] = $date_time;

                      }                 
                        $msg_cnt = $msg_cnt + 1;
              if($msg_cnt == 10){$msg_cnt = 0; break;}
            } 
          }


          $max_msg = count($msgs);
          $max_msg =  $max_msg - 1;

          $showed_msgs = 0;
          $not_showed_msgs = $max_msg;

          for($max_msg; $max_msg >= 0; $max_msg--) {

                $bubble_type =  $msgs[$max_msg]["bubble_type"];
                $item_1  =  $msgs[$max_msg]["this_msg"];
                $item_5  = $msgs[$max_msg]["pic"];
                $item_6  = $msgs[$max_msg]["vid"];
                $item_7  = $msgs[$max_msg]["aud"];
                $item_3  = $msgs[$max_msg]["status"];
                $sku  = $msgs[$max_msg]["sku"];
                $showed_msgs = $showed_msgs +1;
          ?>

                  <!--<p class="conversation-start">
                      <span>Today, 6:48 AM</span>
                  </p>
                  -->
                  <?php if($showed_msgs == $not_showed_msgs) { ?>
                  <script type="text/javascript">
                          var the_chatholder_id = "chatholder<?php echo $cur_akasa_id; ?>";
                          the_chatholder = document.getElementById(the_chatholder_id);
                          the_chatholder.setAttribute("data-msgcnt", "<?php echo $showed_msgs; ?>");
                  </script>
                  <?php } ?>
                  <p  class="bubble <?php echo $bubble_type; ?>"  style="font-size: x-small; width: 70%;">
                      <?php echo $item_1; ?><br>

                      <?php if(trim($item_5) != "") { ?>
                        <a href="<?php echo $item_5; ?>" data-lightbox="<?php echo 'msg_image' . $msg_cnt ?>" data-title="<?php echo $item_1; ?>"><img style="width: 130px; height: 130px" src="<?php echo $item_5; ?>"></a>
                      <?php } ?>
                      <?php if(isset($item_6) && trim($item_6) != "") { ?>
                        <video width="640px" height="267px" controls style=" width:100%;">
                          <source src="<?php echo $item_6; ?>" type="video/mp4">
                          <source src="<?php echo $item_6; ?>" type="video/ogg">
                          Your browser does not support HTML5 video.
                        </video>
                      <?php } ?><br>
                      <?php if(isset($item_7) && trim($item_7) != "") { ?>
                        <audio controls>
                          <source src="<?php echo $item_7; ?>" type="audio/ogg">
                          <source src="<?php echo $item_7; ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                        </audio>                      
                      <?php } ?><br>
                      <?php if(isset($item_3) && $item_3 == 1000) { ?>
                      <i class="fa fa-check" id="<?php echo $cur_akasa_id . '_' . $i_msg;?>" aria-hidden="true" style="height: 20px; font-size: x-small; float: right; 
                      color: 
                      <?php if($item_3 == 0) {  echo "#000000"; } 
                      elseif($item_3 == 1) {  echo "#208000"; } 
                      else {  echo "#4d94ff"; } ?>">
                        
                      </i>
                      <?php } ?>
                      <span style="height: 20px; float: left; color: #000000; font-size: x-small;"><?php if(isset($twelve_hr_date) && $twelve_hr_date != "") { echo $twelve_hr_date; } else { echo $date_time;} ?></span><br>
                  </p>
          <?php } ?>

      <p class="bubble me" id="new_msg_before_this<?php echo $akasakasa_id; ?>" style=" color: white; background-color: white;"></p>
        <?php } ?>

              </span>

              </div>
        <?php } ?>
      </span>

            <div class="write" style="height: 55px;  width: calc(100% - 31px); background-color: white; border: none; display: none" id="send_msg_form_btns">
          <p id="load_msg" style="text-align: center;display: none"><img  src="../img/load.gif" height="20px" width="20px">
          </p>
                <form id="messenger_form" method="post" enctype="multipart/form-data">
                <div id="ta_msg" class="write" style="font-size: x-small; color: black; width: calc(100% + -1px); height: 54px;margin-left: -27px; margin-bottom: -29px; border: none;" maxlength = "140" contentEditable="true" data-id = 'ta_first' hidefocus="true" onkeypress="return (this.innerText.length <= 260)" onclick = "if(this.getAttribute('data-id') == 'ta_first'){this.innerHTML = ''; this.setAttribute('data-id', 'ta_notfirst'); }">Type message</div>
                <br>
                <br>
                <p id="curr_msg_holder" style="display: none"></p>
                <span id="msg_btn_holders">
<!--  id="addemoji" -->
                <label style="color: black" onclick="showEmos(this)"><i class="fa fa-smile-o" aria-hidden="true" style="height: 20px; font-size: small;margin-top: -30px"></i></label>
                <label for="msg_pic" style="color: black"><i class="fa fa-picture-o" id="msg_pic_icon" aria-hidden="true" style="height: 20px; margin-top: 15px; font-size: small;"></i></label>
                <input type="file" id='msg_pic' name="msg_pic" style="display: none">
                <label for="msg_aud" style="color: black"><i class="fa fa-music" id="msg_aud_icon" aria-hidden="true" style="height: 20px; margin-top: 15px; font-size: small;"></i></label>           
                <input type="file" id='msg_aud' name="msg_aud" style="display: none">
                <label for="msg_vid" style="color: black"><i class="fa fa-video-camera" id="msg_vid-icon" aria-hidden="true" style="height: 20px; margin-top: 15px; font-size: small;"></i></label>           
                <input type="file" id='msg_vid' name="msg_vid" style="display: none">

                <input type="text" id='last_sku' name="last_sku" value="" style="display: none">
                <input type="text" id='kasa' name="kasa" style="display: none">
                <input type="text" id='akasakasa_id' value="" name="akasakasa_id" style="display: none">
                <input type="text" id='receiver_id' name="receiver_id" value="" style="display: none">

                <button type="submit" id="msg_sub_btn" onclick="fillCurrMsgHolder()" value="Send" style="height: 20px; margin-top: 20px; font-size: x-small;">Send</button>
                </span>
                </form>
            </div>
        </div>
    </div>
</div>
</div>