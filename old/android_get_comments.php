<?php

if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['news_id']) && trim($_POST['news_id']) != "") {
    require_once("config.php");

    include(ROOT_PATH . 'inc/db_connect.php');

    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $news_id = mysqli_real_escape_string($mysqli, $_POST['news_id']);

    $myid = trim($myid);
    $mypass = trim($mypass);
    $news_id = trim($news_id);
    $investor_id = $myid;

    mysqli_set_charset($mysqli, 'utf8mb4');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          $count = 0;
          if($mypass == $dbpass && $flag == 0) {

                $table_name = "comments";
                $order_by = "sku";
              include(ROOT_PATH . 'inc/get_latest_sku.php');

              if($skip == 0){


                for ($i=$latest_sku; $i > 0; $i--) { 

                          $query = "SELECT inputtor_id, date_time, comment FROM comments WHERE sku = $i AND news_id = '$news_id'";

                              //$numrows = mysql_num_rows($query);
                              $result = $mysqli->query($query);

                              if (mysqli_num_rows($result) != "0") {

                                  $row = $result->fetch_array(MYSQLI_ASSOC);

                                    $comment = $row["comment"];
                                    //$date_time = $row["date_time"];
                                    $inputtor_id = $row["inputtor_id"];
								      $strStart = $row["date_time"];

								      include(ROOT_PATH . 'inc/time_converter.php');


$query = "SELECT first_name, last_name, pot_name, verified_tag, profile_picture FROM investor WHERE investor_id = '$inputtor_id'";

                                            //$numrows = mysql_num_rows($query);
                                            $result = $mysqli->query($query);

                                            if (mysqli_num_rows($result) != "0") {

                                                $row = $result->fetch_array(MYSQLI_ASSOC);

                                                $first_name = $row["first_name"];
                                                $last_name = $row["last_name"];
                                                $news_maker_full_name = $first_name . " " . $last_name;
                                                $pot_name = $row["pot_name"];
                                                $verified_tag = $row["verified_tag"];
                                                $profile_picture = $row["profile_picture"];
										          if (!file_exists("../pic_upload/" . $profile_picture)) {

										          		$profile_picture = "";
							                		} else {

							                			$profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture; 
							                		}

							                	$count = $count + 1;


                //$comment = htmlspecialchars($comment);
                //$news_maker_full_name = htmlspecialchars($news_maker_full_name);
                //$pot_name = htmlspecialchars($pot_name);
                                               $next  = array(

                                              'news_maker_full_name' => $news_maker_full_name, 
                                              'news_maker_pro_pic' => $profile_picture,
                                              'news_maker_pottname' => $pot_name,
                                              'news_maker_verified_status' => $verified_tag, 
                                              'news_main' => $comment, 
                                              'news_date' => $date_time, 
                                              'inputtor_id' => $inputtor_id

                                              );
                                              array_push($linkUpsReturn["hits"], $next);  
                                              if($count == 50){
                                              	break;
                                              }  
                                            }

                                   
                              }


                }

        echo json_encode($linkUpsReturn); exit;

              } else {

                  echo json_encode($linkUpsReturn); exit;

                }


          }

        }

    }
