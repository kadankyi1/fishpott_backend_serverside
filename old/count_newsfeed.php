<?php

  $sql = "SELECT COUNT(*) FROM newsfeed";
  $result = $mysqli->query($sql);

  $row = $result->fetch_array(MYSQLI_ASSOC);


  $newsrows = $row["COUNT(*)"];

