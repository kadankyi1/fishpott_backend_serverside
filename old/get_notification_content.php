<?php
if(isset($_GET["ajax"]) && $_GET["ajax"] != ""){
  require_once("../inc/config.php");
  $config = "yes";
  include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $investor_id = $_GET["investor_id"];

}

//item_bought
//fundraiser_contributions
//----shared_news-----------------
//----shares_bought
//comments------------------------
//likes---------------------------
//link_ups request received
//link_up_request accepted

$nots = array();
$nots_cnt = 0;

//shared_news//shared_news//shared_news//shared_news//shared_news//shared_news//shared_news//shared_news
$item_1 = "sku";
$table_name = "newsfeed";
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');


for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {
	
	$table_name = "newsfeed";

	$item_1 = "news_id_ref";
	$item_2 = "inputtor_id";
	$item_3 = "news_id";

	$column1_name = "sku";
	$column2_name = "type";
	$column3_name = "news_nkae_status";
	$column4_name = "inputtor_id";

	$column1_value = $i_notif;
	$column2_value = "shared_news";
	$column3_value = 0;
	$column4_value = $investor_id;

	$pam1 = "i";
	$pam2 = "s";
	$pam3 = "i";
	$pam4 = "s";
	include(ROOT_PATH . 'inc/db_connect_autologout.php');

	include(ROOT_PATH . 'inc/select3_where4Not1_prepared_statement.php');

	if($done == 1 && $item_1 != "news_id_ref" && $item_1 != "") {

			$shared_news = $item_1;
			$sharer_id = $item_2;
			$news4rm_Shared_id = $item_3;
			//echo "sharer_id : " . $sharer_id; exit;

			$table_name = "investor";

			$item_1 = "first_name";
			$item_2 = "last_name";
			$item_3 = "investor_id";
			$item_4 = "country";
			$item_5 = "profile_picture";

			$column1_name = "investor_id";

			$column1_value = $sharer_id;

			$pam1 = "s";
			$skip = 0;
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');

			$sharer_full_name = $item_1 . " " . $item_2;
			//echo "sharer_full_name : " . $sharer_full_name; exit;

	} else {

			$skip = 1;
	}

	if($done == 1 && $item_1 != "first_name" && $item_1 != "" && $skip == 0) {
			$table_name = "newsfeed";
			$item_1 = "news_id";
			$item_2 = "type";
			$item_3 = "inputtor_type";
			$item_4 = "inputtor_id";
			$item_5 = "date_time";
			$item_6 = "news";

			$column1_name = "news_id";
			$column1_value = $shared_news;
			$column2_name = "inputtor_id";
			$column2_value = $investor_id;

			$pam1 = "s";
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select6_where2_prepared_statement.php');
			if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

				$nots_text = $sharer_full_name . " shared your post";
				$shared_post_news = $item_6;
				$shared_news_id = $news4rm_Shared_id;

				$nots[$nots_cnt] = array("shared_news",$shared_news_id,$nots_text,$shared_post_news,$sharer_id, "" , "" , "" , "" );
//				$nots[$nots_cnt] = array("not_type" => "shared_news","not_link_id" => $item_1, "nots_text" => $nots_text, "shared_post_news" => $shared_post_news, "actor_id" => $sharer_id,  );
				$nots_cnt = $nots_cnt + 1;

			}



	}

} 


//comments//comments//comments//comments//comments//comments//comments//comments//comments//comments//comments//comments

$item_1 = "sku";
$table_name = "likes";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "likes";

	$item_1 = "likes_news_id";
	$item_2 = "liker_investor_id";
	$item_3 = "like_type";

	$column1_name = "sku";
	$column1_value = $i_notif;

	$column2_name = "likes_nkae_status";
	$column2_value = 0;

	$column3_name = "liker_investor_id";
	$column3_value = $investor_id;

	$pam1 = "i";
	$pam2 = "i";
	$pam3 = "s";
	include(ROOT_PATH . 'inc/db_connect_autologout.php');

	include(ROOT_PATH . 'inc/select3_where3NOT1_prepared_statement.php');

	if($done == 1 && $item_1 != "likes_news_id" && $item_1 != "") {

			$liked_news_id = $item_1;
			$liker_id = $item_2;
			if($item_3 == 1) {
			
				$like_type = "like";

			} elseif($item_3 == 0) {

				$like_type = "dislike";

			}
			//echo "sharer_id : " . $sharer_id; exit;

			$table_name = "investor";

			$item_1 = "first_name";
			$item_2 = "last_name";
			$item_3 = "investor_id";
			$item_4 = "country";
			$item_5 = "profile_picture";

			$column1_name = "investor_id";

			$column1_value = $liker_id;

			$pam1 = "s";
			$skip = 0;
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');

			$liker_full_name = $item_1 . " " . $item_2;
			//echo "sharer_full_name : " . $sharer_full_name; exit;

	} else {

			$skip = 1;
	}

	if($done == 1 && $item_1 != "first_name" && $item_1 != "likes_news_id" && $item_1 != "" && $skip == 0) {

			$table_name = "newsfeed";
			$item_1 = "news_id";
			$item_2 = "type";
			$item_3 = "inputtor_type";
			$item_4 = "inputtor_id";
			$item_5 = "date_time";
			$item_6 = "news";

			$column1_name = "news_id";
			$column1_value = $liked_news_id;
			$column2_name = "inputtor_id";
			$column2_value = $investor_id;

			$pam1 = "s";
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select6_where2_prepared_statement.php');
			if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

				$nots_text = $liker_full_name . " likes your post";
				$liked_post_news = "";
				$liked_news_id = $item_1;

				$nots[$nots_cnt] = array($like_type,$liked_news_id,$nots_text,$liked_post_news,$liker_id, "" , "" , "" );
//				$nots[$nots_cnt] = array("not_type" => "shared_news","not_link_id" => $item_1, "nots_text" => $nots_text, "shared_post_news" => $shared_post_news, "actor_id" => $sharer_id,  );
				$nots_cnt = $nots_cnt + 1;

			}



	}
}

$item_1 = "sku";
$table_name = "comments";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "comments";

	$item_1 = "news_id";
	$item_2 = "inputtor_id";
	$item_3 = "comment";

	$column1_name = "sku";
	$column1_value = $i_notif;

	$column2_name = "comment_nkae_status";
	$column2_value = 0;

	$column3_name = "inputtor_id";
	$column3_value = $investor_id;

	$pam1 = "i";
	$pam2 = "i";
	$pam3 = "s";
	include(ROOT_PATH . 'inc/db_connect_autologout.php');

	include(ROOT_PATH . 'inc/select3_where3NOT1_prepared_statement.php');

	if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

			$comment_sku = $i_notif;
			$comment_news_id = $item_1;
			$commentor_id = $item_2;
			$comment_made = $item_3;
			$not_type = "comment";

			$table_name = "investor";

			$item_1 = "first_name";
			$item_2 = "last_name";
			$item_3 = "investor_id";
			$item_4 = "country";
			$item_5 = "profile_picture";

			$column1_name = "investor_id";

			$column1_value = $commentor_id;

			$pam1 = "s";
			$skip = 0;
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');

			$commentor_full_name = $item_1 . " " . $item_2;
			//echo "sharer_full_name : " . $sharer_full_name; exit;

	} else {

			$skip = 1;
	}


	if($done == 1 && $item_1 != "first_name" && $item_1 != "" && $skip == 0) {

			$table_name = "newsfeed";
			$item_1 = "news_id";
			$item_2 = "type";
			$item_3 = "inputtor_type";
			$item_4 = "inputtor_id";
			$item_5 = "date_time";
			$item_6 = "news";

			$column1_name = "news_id";
			$column1_value = $comment_news_id;
			$column2_name = "inputtor_id";
			$column2_value = $investor_id;

			$pam1 = "s";
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select6_where2_prepared_statement.php');
			if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

				$nots_text = $commentor_full_name . " said something about your post";
				$commented_post_news = "";
				$commented_news_id = $item_1;

				$nots[$nots_cnt] = array($not_type,$commented_news_id,$nots_text,$commented_post_news,$commentor_id, $commentor_full_name, $comment_made,  $comment_sku);
					$nots_cnt = $nots_cnt + 1;

			}



	}
}

$item_1 = "sku";
$table_name = "linkups";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "linkups";

	include(ROOT_PATH . 'inc/db_connect_autologout.php');

  $query = "SELECT * FROM linkups WHERE (sender_id = '$investor_id' AND status = 0 AND sku = $i_notif AND linkups_nkae_status = 0) OR (receiver_id = '$investor_id' AND status = 0 AND sku = $i_notif AND linkups_nkae_status = 0)";

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

      } else {

      	$not_investor_id = $sender_id;
      	
      }

    } else {

      	$done = 0;
      	
      }
	if($done == 1 && $sender_id != "" && $sender_id != $investor_id && $receiver_id != "") {

			$link_sku = $i_notif;
			$not_type = "linkup";

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

			$linker_full_name = $item_1 . " " . $item_2;
			//echo "sharer_full_name : " . $sharer_full_name; exit;
			$nots_text = $linker_full_name . " wants to link up with you";
			$commented_post_news = "";
			$commented_news_id = "linkup_request";
			$comment_made = $nots_text;

			$nots[$nots_cnt] = array($not_type,$commented_news_id,$nots_text,$commented_post_news,$not_investor_id, $linker_full_name, $comment_made,  $link_sku);
				$nots_cnt = $nots_cnt + 1;


	} else {

			$skip = 1;
	}

}


$item_1 = "sku";
$table_name = "mafrewo";
include(ROOT_PATH . 'inc/db_connect_autologout.php');
include(ROOT_PATH . 'inc/get_latest_sku_prepared_statement.php');

for ($i_notif = $item_1; $i_notif > 0; $i_notif--) {

	$table_name = "mafrewo";

	$item_1 = "news_id";
	$item_2 = "tagger_id";

	$column1_name = "sku";
	$column1_value = $i_notif;

	$column2_name = "nkae_status";
	$column2_value = 0;

	$column3_name = "i_tagged_id";
	$column3_value = $investor_id;

	$pam1 = "i";
	$pam2 = "i";
	$pam3 = "s";
	include(ROOT_PATH . 'inc/db_connect_autologout.php');

	include(ROOT_PATH . 'inc/select2_where3_prepared_statement.php');

	if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

			$tag_news_id = $item_1;
			$tagger_id = $item_2;
			$table_name = "investor";

			$item_1 = "first_name";
			$item_2 = "last_name";
			$item_3 = "investor_id";
			$item_4 = "country";
			$item_5 = "profile_picture";

			$column1_name = "investor_id";

			$column1_value = $tagger_id;

			$pam1 = "s";
			$skip = 0;
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select5_where1_prepared_statement.php');

			$tagger_full_name = $item_1 . " " . $item_2;


	} else {

			$skip = 1;
	}

	if($done == 1 && $item_1 != "first_name" && $item_1 != "first_name" && $item_1 != "" && $skip == 0) {

			$table_name = "newsfeed";
			$item_1 = "news_id";
			$item_2 = "type";
			$item_3 = "inputtor_type";
			$item_4 = "inputtor_id";
			$item_5 = "date_time";
			$item_6 = "news";

			$column1_name = "news_id";
			$column1_value = $tag_news_id;
			$column2_name = "inputtor_id";
			$column2_value = $tagger_id;

			$pam1 = "s";
			include(ROOT_PATH . 'inc/db_connect_autologout.php');
			include(ROOT_PATH . 'inc/select6_where2_prepared_statement.php');
			if($done == 1 && $item_1 != "news_id" && $item_1 != "") {

				$nots_text = $tagger_full_name . " anchored you in a post";
				$liked_post_news = "";
				$liked_news_id = $item_1;
				$not_type = "tag";
				$nots[$nots_cnt] = array($not_type,$item_1,$nots_text,$liked_post_news,$tagger_id, "" , "", $i_notif  );
//				$nots[$nots_cnt] = array("not_type" => "shared_news","not_link_id" => $item_1, "nots_text" => $nots_text, "shared_post_news" => $shared_post_news, "actor_id" => $sharer_id,  );
				$nots_cnt = $nots_cnt + 1;

			}



	}
}
//var_dump($nots); exit;

$cnt = count($nots);

if(isset($_GET["ajax"]) && $_GET["ajax"] != "" && $_GET["ajax"]== 1){

	if($cnt != 0){

        echo json_encode($nots,JSON_UNESCAPED_SLASHES);

	} else {

			$nots  = array();
			echo json_encode($nots,JSON_UNESCAPED_SLASHES); //exit;


	}
        


}
