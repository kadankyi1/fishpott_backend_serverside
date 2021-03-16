<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          $count = 0;
          if($mypass == $dbpass && $flag == 0) {



        $query="SELECT * FROM nkae WHERE wo_id = '$myid' AND badger_status = 0 ORDER BY sku DESC";
            $result = $mysqli->query($query);



              while($row=$result->fetch_array()) {

                  $orno_id = $row["orno_id"];
                  $type = $row["type"];
                  $info_1 = $row["info_1"];
                  $asem_id = $row["asem_id"];


                $query = "SELECT * FROM investor WHERE investor_id = '$orno_id' ";
                  $result2 = $mysqli->query($query);
                  if (mysqli_num_rows($result2) != "0") {

                      $row2 = $result2->fetch_array(MYSQLI_ASSOC);
                      $first_name = $row2["first_name"];
                      $last_name = $row2["last_name"];
                      $thepot_name = $row2["pot_name"];
                      $full_name = $first_name . " " . $last_name;
                      $profile_picture = $row2["profile_picture"];
                      if (!file_exists("../pic_upload/" . $profile_picture)) {

                          $profile_picture = "";
                          } else {

                            $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
                          }

                      $this_inputtor_vtag = $row2["verified_tag"];
                      $add_this = 1;

                    } else {
                      $add_this = 0;
                    }



                    if(trim($info_1) == ""){
                          if($type == "like"){

                            $info_1 = $full_name . " likes your post";

                          } else if ($type == "dislike"){

                            $info_1 = $full_name . " disliked your post";

                          } else if ($type == "comment"){

                            $info_1 = $full_name . " commented on your post";

                          } else if ($type == "linkup"){

                            $info_1 = $full_name . " wants to linkup with you.";

                          }  else if ($type == "linkup_accepted"){

                            $info_1 = $full_name . " is now part of your links.";

                          }  else if ($type == "share"){

                            $info_1 = $full_name . " shared your post";

                          } else if ($type == "purchase"){

                            $info_1 = $full_name . " bought an item from your pott";

                          } else if ($type == "poach"){

                            $info_1 = "You've been poached";

                          }
                      }

if($type == "like" || $type == "dislike" || $type == "comment" || $type == "share" || $type == "purchase") {

                  $query = "SELECT * FROM newsfeed WHERE news_id = '$asem_id' ";
                  $result3 = $mysqli->query($query);
                  if (mysqli_num_rows($result3) != "0") {

                      $row3 = $result3->fetch_array(MYSQLI_ASSOC);
                      $news = trim($row3["news"]);
                      $sponsored_tag = trim($row3["sponsored_tag"]);
                      $news_id = trim($row3["news_id"]);
                      $news_image = trim($row3["news_image"]);
                      $news_aud = trim($row3["news_aud"]);
                      $news_video = trim($row3["news_video"]);
                    if (trim($news_image) == "" || !file_exists("../user/" . $news_image)) {

                        $news_image = "";
                        } else {

                          $news_image = HTTP_HEAD . "://fishpott.com/user/" . $news_image; 
                        }
                    if (trim($news_video) == "" || !file_exists("../user/" . $news_video)) {

                        $news_video = "";
                        } else {

                          $news_video = HTTP_HEAD . "://fishpott.com/user/" . $news_video; 
                        }
                      if(isset($add_this) && $add_this != 0){

                          $add_this = 1;

                      }

                    } else {
                      $add_this = 0;
                    }

  } else {
                        $news_video = "";
                        $news_image = "";
                        $news = "";
  }

                      if($add_this == 1){

                        $count = $count + 1;

                        $news = htmlspecialchars($news);

                         $next  = array(

                        'type' => $type, 
                        'profile_pic' => $profile_picture, 
                        'notification_text' => $info_1,
                        'news_image' => $news_image,
                        'news_video' => $news_video,
                        'news_text' => $news, 
                        'pott_or_news_id' => $orno_id,
                        'pott_name' => $thepot_name

                        );
                        array_push($linkUpsReturn["hits"], $next); 
                          
                      }

          }

              $query="SELECT * FROM y3n_transfers WHERE receiver_id = '$myid' AND nkae_status = 0 ORDER BY sku DESC";
                  $result = $mysqli->query($query);

                  while($row=$result->fetch_array()) {

                      $sku = $row["sku"];
                    $my_transfers = $my_transfers + 1;

                  }
                    echo count($linkUpsReturn["hits"]) + $my_transfers; 
                    //echo "my_transfers : " . $my_transfers . "<br>";
                    //echo "general : " . count($linkUpsReturn["hits"])  . "<br>";
                    exit;

                    //echo json_encode($linkUpsReturn);
        }////// END OF PASSWORD CHECK

    }
  }
