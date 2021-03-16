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

    $query = "SELECT password FROM wuramu WHERE id = '$myid'";   

    $result = $mysqli->query($query);
        
    if (mysqli_num_rows($result) != 0) {

          $row = $result->fetch_array(MYSQLI_ASSOC);
          $dbpass = trim($row["password"]);
          $linkUpsReturn["hits"] = array();
          //$next = "daisy";
          //array_push($linkUpsReturn["suggestions"], $next);
          //echo json_encode($linkUpsReturn,JSON_UNESCAPED_SLASHES); exit;


          if($mypass == $dbpass) {

            $query = "SELECT first_name, last_name, pot_name, investor_id, profile_picture FROM investor WHERE investor_id = 'fpsharescommission599a9afc1101a2.33950921'";

            //$numrows = mysql_num_rows($query);
            $result = $mysqli->query($query);

            if (mysqli_num_rows($result) != "0") {

                $row = $result->fetch_array(MYSQLI_ASSOC);
                $investor_id = $row["investor_id"];
                $first_name = $row["first_name"];
                $last_name = $row["last_name"];
                $full_name = $first_name . " " . $last_name;
                $pot_name = $row["pot_name"];
                $profile_picture = $row["profile_picture"];

                if (file_exists($profile_picture)) {


                    $next  = array(
                      'id' => "na", 
                      'pot_name' => "na",
                      'tags' => "na", 
                      'userImageURL' => "na",
                      'status' => "no",
                      'error' => "Something went Awry. Skip this process. There's more excitement after"
                      );
                    array_push($linkUpsReturn["hits"], $next);
                }

              }

              $cnt = 1;
              $picked = array();
              $picked_cnt = 1;
              $picked[0] = "fpsharescommission599a9afc1101a2.33950921";
              for($i=0; $i <= 20; $i++){

                      $query = "SELECT first_name, last_name, pot_name, investor_id, profile_picture FROM investor ORDER BY RAND() LIMIT 1";

                      //$numrows = mysql_num_rows($query);
                      $result = $mysqli->query($query);

                      if (mysqli_num_rows($result) != "0") {

                          $row = $result->fetch_array(MYSQLI_ASSOC);
                          
                          $investor_id = $row["investor_id"];
                          $first_name = $row["first_name"];
                          $last_name = $row["last_name"];
                          $full_name = $first_name . " " . $last_name;
                          $pot_name = $row["pot_name"];
                          $profile_picture = trim($row["profile_picture"]);
                          $addthis = 1;

                          

                          if($investor_id == $myid){
                              $addthis = 0;

                          }

                          for($check_j = 0; $check_j < $picked_cnt; $check_j++) {

                            if(isset($picked[$check_j]) && $picked[$check_j] == $investor_id && trim($investor_id) != ""){

                                $addthis = 0;

                                      
                            }

                           
                            
                              
                          }

                          if($addthis == 1 && $profile_picture != "" && file_exists("../pic_upload/" . $profile_picture) && isset($pot_name)){
                              $profile_picture = HTTP_HEAD . "://fishpott.com/pic_upload/" . $profile_picture;
                              $next  = array(
                                'id' => $investor_id, 
                                'pot_name' => $pot_name,
                                'tags' => $full_name, 
                                'userImageURL' => $profile_picture,
                                'status' => "yes",
                                'error' => "na"

                                );
                              unset($pot_name);
                              
                              $cnt = $cnt + 1;
                              
                               $picked[$picked_cnt] = $investor_id;
                               $picked_cnt = $picked_cnt + 1;
                              array_push($linkUpsReturn["hits"], $next);
                              
                              //array_push($picked, $investor_id;);

                          }


                        }
                  }
                  //echo json_encode($picked);
        //array_push($linkUpsReturn["suggestions"], $picked);
        echo json_encode($linkUpsReturn);
        //echo json_encode($picked,JSON_UNESCAPED_SLASHES);
         exit;
      } else {

              $linkUpsReturn["hits"]  = array(
                'id' => "na", 
                'pot_name' => "na",
                'tags' => "na", 
                'userImageURL' => "na",
                'status' => "no",
                'error' => "Something went Awry. Skip this process. There's more excitement after"
                );
          echo json_encode($linkUpsReturn); exit;

      }


    } else {

        $linkUpsReturn["hits"]  = array(
          'status' => "no",
          'error' => "Something went Awry. Skip this process. There's more excitement after"
          );
    echo json_encode($linkUpsReturn); exit;
    }
}
