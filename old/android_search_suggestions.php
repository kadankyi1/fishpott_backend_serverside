<?php
if(isset($_POST['myid']) && trim($_POST['myid']) != "" && isset($_POST['mypass']) && trim($_POST['mypass']) != "" && isset($_POST['search_txt']) && trim($_POST['search_txt']) != "") {

	require_once("config.php");
    include(ROOT_PATH . 'inc/db_connect.php');
    mysqli_set_charset($mysqli, 'utf8');
    $myid = mysqli_real_escape_string($mysqli, $_POST['myid']);
    $mypass = mysqli_real_escape_string($mysqli, $_POST['mypass']);
    $search_txt = mysqli_real_escape_string($mysqli, $_POST['search_txt']);


    $myid = trim($myid);
    $mypass = trim($mypass);
    $search_txt = trim($search_txt);


    $investor_id = $myid;
    mysqli_set_charset($mysqli, 'utf8');

    $query = "SELECT password, flag FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $flag = trim($row["flag"]);
          $linkUpsReturn["hits"] = array();
          if($mypass == $dbpass && $flag == 0) {

           // $query="SELECT type,id,display_text,img_src FROM heihw3_paa WHERE search_text LIKE '%$search_txt%' ORDER BY count";
            $query="SELECT sku, type,id,display_text,img_src FROM heihw3_paa WHERE search_text LIKE '%$search_txt%' ORDER BY count DESC";
            $result = $mysqli->query($query);

            //$row = $result->fetch_row();

            while($row=$result->fetch_array()) {
                   
                $sku = $row["sku"];
                $type = $row["type"];
                $id = $row["id"];
                $display_text = $row["display_text"];
                $img_src = $row["img_src"];

                if($type != "investor"){

                    if (trim($img_src) == "" || !file_exists("../user/" . $img_src)) {

                        $img_src = "";

                        } else {

                          $img_src = "http://fishpott.com/user/" . $img_src; 
                        }

                } else {

                      if (!file_exists("../pic_upload/" . $img_src)) {

                          $img_src = "";
                          } else {

                            $img_src = "http://fishpott.com/pic_upload/" . $img_src; 
                          }


                }

                $next  = array(

                'sku' => $sku, 
                'type' => $type,
                'id' => $id,
                'display_text' => $display_text, 
                'img_src' => $img_src

                );
                array_push($linkUpsReturn["hits"], $next);    

           }
                  echo json_encode($linkUpsReturn); exit;

          } else {

                  echo json_encode($linkUpsReturn); exit;

                }

        } else {

                  echo json_encode($linkUpsReturn); exit;

                }

    }