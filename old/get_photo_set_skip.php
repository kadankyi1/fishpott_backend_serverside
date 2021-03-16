<?php
  if(isset($pot) && $pot == 1){

  $query = "SELECT * FROM photos WHERE sku = $i AND p_owner_id = '$p_investor_id'";

  } else {
  $query = "SELECT * FROM photos WHERE sku = $i AND p_owner_id = '$investor_id'";

  }

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      $skip = "no";
      $p_owner_id = $row["p_owner_id"];
      $p_pic_path = $row["p_pic_path"];

      if($p_owner_id == "" || $p_pic_path == "") {

          $skip = "yes";

      }

    } else {

      $skip = "yes";
    }
