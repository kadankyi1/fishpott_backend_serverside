<?php

  $sql = "SELECT COUNT(*) FROM share";
  $result = $mysqli->query($sql);

  $row = $result->fetch_array(MYSQLI_ASSOC);


  $num_of_shares = $row["COUNT(*)"];

