<?php

session_start();

if (!isset($_SESSION["management"])) {
     ?>
  <meta charset="UTF-8">
    <script type="text/javascript">
        alert('您不是管理人員，您無法存取此頁面\nyou are not management, you cannot access this page');
        window.location.href = "./";
    </script>
    <?php
    die();
}

require_once './account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


























$auth = $_GET['auth'];


$sql = "SELECT * FROM `golf_fairway_booking` where `golf_fairway_booking`.`auth`='$auth';";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {



        $key1 = $row['booking_date'];
        $begin_hour = (int) $row['begin_hour'];
        $end_hour = (int) $row['end_hour'];

        for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
          $hour_int = ((int) $cursor_hour);
          $is_half_hour = $cursor_hour != $hour_int;
          $half_hour_mark = ($is_half_hour ? ':30' : ':00');
          $key2=$hour_int . $half_hour_mark;

          foreach (json_decode($row['p_selections']) as $key => $position) {
            $key4=str_replace("position_", "", $position);


              $sql = "DELETE FROM `golf_booking_buffer` WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';";
              try {
                  if ($conn->query($sql) === TRUE) {
                  } else {
                    echo "Error: $sql";
                    die();
                  }
              } catch (Exception $e) {
                    echo "Error: $sql $e";
                    die();
              }

          }

        }


    }
}















$sql = "DELETE FROM `golf_fairway_booking` 
WHERE `golf_fairway_booking`.`auth`='$auth' 
and (select count(*) from `golf-unpaid-account` where `golf-unpaid-account`.`auth`=`golf_fairway_booking`.`auth`)>0
;";

try {
    // Execute the statement
    if ($conn->query($sql)) {
        // echo "New records created successfully";


    } else {
        echo "Error: $sql";
        die();
    }
} catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
  echo $sql;
        die();
}







$sql = "DELETE FROM `golf_remark`
WHERE `golf_remark`.`auth`='$auth' 
and (select count(*) from `golf-unpaid-account` where `golf-unpaid-account`.`auth`=`golf_remark`.`auth`)>0
;";

try {
    // Execute the statement
    if ($conn->query($sql)) {


    } else {
        echo "Error: $sql";
        die();
    }
} catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
  echo $sql;
        die();
}






$sql = "DELETE FROM `golf-unpaid-account` where `golf-unpaid-account`.`auth`='$auth';";

try {
    // Execute the statement
    if ($conn->query($sql)) {
        // echo "New records created successfully";
    } else {
        echo "Error: $sql";
die();
    }
} catch(Exception $e) {
  echo 'Message: ' .$e->getMessage();
  echo $sql;
die();
}





$conn->close();









?>
<script type="text/javascript">
    window.location.href = "./searching_data_future_booking.php";
</script>