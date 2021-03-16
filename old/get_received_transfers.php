<?php

$item_1 = "sku";
$table_name = "y3n_transfers";
$t_cnt = 0;
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "y3n_transfers";

	include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $query = "SELECT * FROM y3n_transfers WHERE (sender_id = '$investor_id' AND nkae_status = 0 AND sku = $i_notif) OR (receiver_id = '$investor_id' AND nkae_status = 0 AND sku = $i_notif)";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $sender_id = $row["sender_id"];
      $receiver_id = $row["receiver_id"];
      $shares_parent_name = $row["shares_parent_name"];
      $num_shares_transfered = $row["num_shares_transfered"];
      $done = 1;
      if($sender_id == $investor_id){

      	$not_investor_id = $receiver_id;

      } else {

      	$not_investor_id = $sender_id;
      	
      }

    } else {

      	$done = 0;
      	
      }
	if($done == 1 && $sender_id != "" && $receiver_id != "" && $sender_id != $investor_id) {

			$transfer_sku = $i_notif;
			$not_type = "transfer";

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

			$transferer_full_name = $item_1 . " " . $item_2;

			$nots_text = "<a href='../pots/index.php?fold=" . $old_fold . "&e_o=" . $e_o['0'] . "&login=" . $old_e_login . "&u_type=" . $old_e_u_type . "&u=" . $not_investor_id . "' style=' text-decoration: none; font-weight : bold; '>" . $transferer_full_name . "</a> has transfered <span style='font-weight : bold;'>" . $num_shares_transfered . " " . $shares_parent_name . "</span> to you";
			$commented_post_news = "";
			$commented_news_id = "shares_transfer";
			$comment_made = $nots_text;

			$transfers[$t_cnt] = array($not_type,$commented_news_id,$nots_text,$commented_post_news,$not_investor_id, $transferer_full_name, $comment_made,  $transfer_sku);
				$t_cnt = $t_cnt + 1;


	} else {

			$skip = 1;
	}

}

if(isset($transfers)){

	$t_cnt = count($transfers);;

} else {

 $t_cnt = 0;
}

$item_1 = "sku";
$table_name = "adetor";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "adetor";

	include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $query = "SELECT seller_id, buyer_id, adetor_id_short, adetor_type, adetor_news_id FROM adetor WHERE (seller_id = '$investor_id' AND adetor_nkae_status_seller = 0 AND sku = $i_notif) OR (buyer_id = '$investor_id' AND adetor_nkae_status_buyer = 0 AND sku = $i_notif)";

  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $seller_id = $row["seller_id"];
      $adetor_type = $row["adetor_type"];
      $buyer_id = $row["buyer_id"];
      $adetor_id_short = $row["adetor_id_short"];
      $adetor_news_id = $row["adetor_news_id"];
      $done = 1;
      if($seller_id == $investor_id && $buyer_id != $investor_id){
      	$my_type = "seller";
      	$not_investor_id = $buyer_id;

      } elseif($buyer_id == $investor_id && $seller_id != $investor_id) {
      	$my_type = "buyer";
      	$not_investor_id = $seller_id;
      	
      } elseif($buyer_id == $investor_id && $seller_id == $investor_id) {

      	$my_type = "buyer";
      	$not_investor_id = $seller_id;
      	
      }

      if($adetor_type == "event") {

		$query = "SELECT event_name FROM event WHERE event_news_id = '$adetor_news_id'";

		if (mysqli_num_rows($result) != "0") {

		  $result = $mysqli->query($query);
		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $event_name = $row["event_name"];

		} else {

			$done = 0;
		}

      } elseif($adetor_type == "up4sale") {

		$query = "SELECT item_name FROM up4sale WHERE up4sale_news_id = '$adetor_news_id'";

		if (mysqli_num_rows($result) != "0") {

		  $result = $mysqli->query($query);
		  $row = $result->fetch_array(MYSQLI_ASSOC);
		  $item_name = $row["item_name"];

		} else {

			$done = 0;
		}
      }


			$adetor_sku = $i_notif;

			$table_name = "investor";

			$item_1 = "first_name";
			$item_2 = "last_name";
			$item_3 = "investor_id";
			$item_4 = "country";
			$item_5 = "profile_picture";

			$column1_name = "investor_id";
			if($my_type == "seller"){

			$column1_value = $not_investor_id;
			} else {

			$column1_value = $not_investor_id;
			}

			$pam1 = "s";
			$skip = 0;
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			//echo "seller_id : " . $seller_id . "<br>";
			//echo "not_investor_id : " . $not_investor_id; exit;
			include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');
			$this_full_name = $item_1 . " " . $item_2;
			if($my_type == "seller") {
			      if($adetor_type == "event") {
					$not_type = "seller";
					$commented_news_id = "event_ticket_purchase";
						$nots_text = "<a href='../pots/index.php?fold=" . $old_fold . "&e_o=" . $e_o['0'] . "&login=" . $old_e_login . "&u_type=" . $old_e_u_type . "&u=" . $not_investor_id . "' style=' text-decoration: none; font-weight : bold; '>" . $this_full_name . "</a> has bought a ticket to your event <span style='font-weight : bold;'>" . $event_name . "</span>";

					} elseif($adetor_type == "up4sale") {
					$not_type = "seller";
					$commented_news_id = "up4sale_purchase";
						$nots_text = "<a href='../pots/index.php?fold=" . $old_fold . "&e_o=" . $e_o['0'] . "&login=" . $old_e_login . "&u_type=" . $old_e_u_type . "&u=" . $not_investor_id . "' style=' text-decoration: none; font-weight : bold; '>" . $this_full_name . "</a> has bought your item <span style='font-weight : bold;'>" . $item_name .  "</span>";

					}
				} elseif($my_type == "omni") {
			      if($adetor_type == "event") {
					$not_type = "omni";
					$commented_news_id = "event_ticket_purchase";
						$nots_text = "<a href='../pots/index.php?fold=" . $old_fold . "&e_o=" . $e_o['0'] . "&login=" . $old_e_login . "&u_type=" . $old_e_u_type . "&u=" . $not_investor_id . "' style=' text-decoration: none; font-weight : bold; '>" . $this_full_name . "</a> has bought a ticket to your event <span style='font-weight : bold;'>" . $event_name . "</span>";

					} elseif($adetor_type == "up4sale") {
					$not_type = "omni";
					$commented_news_id = "up4sale_purchase";
						$nots_text = "<a href='../pots/index.php?fold=" . $old_fold . "&e_o=" . $e_o['0'] . "&login=" . $old_e_login . "&u_type=" . $old_e_u_type . "&u=" . $not_investor_id . "' style=' text-decoration: none; font-weight : bold; '>" . $this_full_name . "</a> has bought your item <span style='font-weight : bold;'>" . $item_name .  "</span>";

					}
				}else {

			      if($adetor_type == "event") {
					$not_type = "buyer";
					$commented_news_id = "event_ticket_purchase";
						$nots_text ="<span style='font-weight : bold;'>" .  $event_name . "</span> event ticket bought <span style='font-weight : bold;'> Fish Code is " . $adetor_id_short . "</span>";

					} elseif($adetor_type == "up4sale") {
					$not_type = "buyer";
					$commented_news_id = "up4sale_purchase";
						$nots_text ="<span style='font-weight : bold;'>" .  $item_name . "</span> item bought <span style='font-weight : bold;'> Fish Code is " . $adetor_id_short . "</span>";

					}

				}			
			$commented_post_news = "";
			$comment_made = $nots_text;

			$transfers[$t_cnt] = array($not_type,$commented_news_id,$nots_text,$my_type,$not_investor_id, $this_full_name, $comment_made,  $adetor_sku);
				$t_cnt = $t_cnt + 1;


	} else {

			$skip = 1;
	}

}

if(isset($transfers)){

	$transfers_cnt = count($transfers) - 1;;

} else {

	$transfers_cnt = -1;
}
//var_dump($transfers);
include(ROOT_PATH . 'inc/db_connect_autologout.php');
