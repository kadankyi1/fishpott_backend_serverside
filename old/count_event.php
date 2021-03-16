<?php

  $sql = "SELECT COUNT(*) FROM event";
  $result = $mysqli->query($sql);

  $row = $result->fetch_array(MYSQLI_ASSOC);


  $num_of_event = $row["COUNT(*)"];

