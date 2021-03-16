<?php

  $query = "SELECT * FROM videos WHERE sku = $i AND v_owner_id = '$investor_id'";

  //$numrows = mysql_num_rows($query);
  $result = $mysqli->query($query);

  if (mysqli_num_rows($result) != "0") {

      $row = $result->fetch_array(MYSQLI_ASSOC);
      
      $v_owner_id = $row["v_owner_id"];
      $v_pic_path = $row["v_pic_path"];
      $skip = "no";

    } else {

      $skip = "yes";
    }
