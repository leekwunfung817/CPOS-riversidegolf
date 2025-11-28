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

if (!isset($_GET['exact_date'])) {
  exit();
}

require_once 'booking-status-json-variable.php';
require_once 'common-function.php';



$date = $_GET['exact_date'];

 ?>
<h3><?php echo $date; ?></h3>
<?php 

 ?>
<table id="fair_way_table">
  <tbody>
<?php

// Function to generate date string for the next 6 days (including today)
// function getNextWeekDates() {
//   $dates = [];
//   // for ($i = 0; $i < 7; $i++) {
//   //   $dates[] = date('Y-m-d', strtotime("+$i days"));
//   // }
//   $dates[] = date('Y-m-d');
//   return $dates;
// }

// // Get next week dates
// $dates = getNextWeekDates();

require_once 'setting-admin.php';

function discount_func($discount_code)
{
  if ($discount_code == 'S') {
      return "Student";
  } else if ($discount_code == 'D') {
      return "Disabled";
  } else if ($discount_code == 'H') {
      return "No discount";
  }
  return $discount_code;
}

function hour_num_to_hour_display($hour_num)
{
    $cursor_hour = ((float) $hour_num);
    $hour_int = ((int) $hour_num);
    $is_half_hour = $cursor_hour != $hour_int;
    $half_hour_mark = ($is_half_hour ? ':30' : ':00');
    return str_pad($hour_int, 2, "0", STR_PAD_LEFT) . $half_hour_mark;
}


$half_hour_cluster = isset($_GET['half_hour']);
// half_hour_cluster
// echo "hhc:($half_hour_cluster)";

$hour_iterator = 1;
if ($half_hour_cluster) {
  $hour_iterator = 0.5;
  // echo "((half_hour_cluster))";
  // echo "<textarea>";
  // echo json_encode($complexArray);
  // echo "</textarea>";
}




// Title Bar
?><tr><th></th><?php
for ($hour = 8; $hour < 22; $hour=$hour+$hour_iterator) {
  $formattedHour = pointToHalfHour($hour);
 ?><th style="position: sticky;top: 0;background-color: white;border-style: solid;border-width: 5px;">
  <?php  // echo date('h:i A', strtotime($formattedHour)).($hour==22?'':' ~').'<br>'; ?>  
  
  <?php  echo date('H:i', strtotime($formattedHour)).($hour==22?'':' ~'); ?>  
  </th><?php
}
?></tr><?php


















  foreach ($position_list as $key => $i) {
    // Loop position row
   ?>
<tr class="time_row">
<?php
// Row first cell
   ?>
  <th><?php  echo $i; ?></th>
<?php
// Record cell
    for ($hour = 8; $hour < 22; $hour=$hour+$hour_iterator) {
      $formattedHour = pointToHalfHour($hour);
      $formattedHour__ = pointToHalfHour($hour);

      if (!$half_hour_cluster) {
        if ($complexArray[$date][$formattedHour]['booking'][$i] == 0) {
          $formattedHour_Half = pointToHalfHour($hour+0.5);
          if ($complexArray[$date][$formattedHour_Half]['booking'][$i] != 0) {
            $formattedHour = $formattedHour_Half;
          }
        }
      }

      $booking_arr = $complexArray[$date][$formattedHour]['booking'];
      $booked = $booking_arr[$i];
      // Assuming your PHP variable is named $value
      $value = $booked; // Your logic to determine the value
      if ($value == 5) {
        $cssClass = "grey";
      } else if ($value == 4) {
        $cssClass = "blue";
      } else if ($value == 3) {
        $cssClass = "orange";
      } else if ($value == 2) {
        $cssClass = "yellow";
      } else if ($value == 1) {
        $cssClass = "red";
      } else if ($value == 0) {
        $cssClass = "";
      }

    ?><td class="<?php echo $hour.' '.$i.' '.$cssClass; ?> <?php echo ( (strlen($cssClass)>0) ? 'booked':''); ?>" <?php  

      if (strlen($cssClass)>0) {
          // $filename = $booking_arr['fn_'.$i];
          // $data = $booking_arr['data_'.$i];
        if (isset($booking_arr['data_'.$i])) {
          
          $auth = $booking_arr['data_'.$i]['auth'];

          if ($management) {
            echo '   onclick="comfirm_and_print('
            .'\''.(isset($booking_arr['data_'.$i]['auth']) ? $booking_arr['data_'.$i]['auth'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['id']) ? $booking_arr['data_'.$i]['id'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['name']) ? $booking_arr['data_'.$i]['name'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['telephone']) ? $booking_arr['data_'.$i]['telephone'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['octopus_no']) ? $booking_arr['data_'.$i]['octopus_no'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['check_digit']) ? $booking_arr['data_'.$i]['check_digit'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['booking_date']) ? $booking_arr['data_'.$i]['booking_date'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['begin_hour']) ? hour_num_to_hour_display($booking_arr['data_'.$i]['begin_hour']) : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['end_hour']) ? hour_num_to_hour_display($booking_arr['data_'.$i]['end_hour']) : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['p_selections']) ? 
              str_replace('[', '', 
              str_replace(']', '', 
              str_replace('"', '', 
                $booking_arr['data_'.$i]['p_selections']
              )
              )
              )
               : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['discount']) ? $booking_arr['data_'.$i]['discount'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['auth_code']) ? $booking_arr['data_'.$i]['auth_code'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['req_card_number']) ? $booking_arr['data_'.$i]['req_card_number'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['amount']) ? $booking_arr['data_'.$i]['amount'] : "").'\''
            .',\''.(isset($booking_arr['data_'.$i]['cash']) ? $booking_arr['data_'.$i]['cash'] : "").'\''
            .')" ';
          
          }
        }
      }

     ?> ><?php 
     // if ($date == '2024-11-28' && $i==1) {
     //   echo "$formattedHour ".isset($booking_arr['data_'.$i]);
     // }
      if (strlen($cssClass)>0) {
        if (isset($booking_arr['data_'.$i])) {
          if (isset($booking_arr['data_'.$i]['id'])) {
            echo (isset($booking_arr['data_'.$i]['id']) ? $booking_arr['data_'.$i]['id'] : "");
            if ($booking_arr['data_'.$i]['id'] == '91499') {

              $pointHour = $hour;
              $hour_int = ((int)$pointHour);
              if ($pointHour == $hour_int) {
                $half_hour_mark = ':00';
              } else {
                $half_hour_mark = ':30';
              }
              $formattedHour___ = $hour_int . $half_hour_mark;

              // echo " ((($hour - $formattedHour - $formattedHour__ - $formattedHour___))) ";
            }
          }
        }
      }
        // echo "$i";
   ?></td><?php 
    } ?>
    </tr>

<?php 
  } 
















// Title bar
?><tr><th></th><?php
for ($hour = 8; $hour < 22; $hour=$hour+$hour_iterator) {
  $formattedHour = pointToHalfHour($hour);
 ?><th style="position: sticky;bottom: 0;background-color: white;border-style: solid;border-width: 5px;">
  <?php // echo date('h:i A', strtotime($formattedHour)).($hour==22?'':' ~').'<br>'; ?>  
  
  <?php  echo date('H:i', strtotime($formattedHour)).($hour==22?'':' ~'); ?>  
  </th><?php
}
?></tr><?php


















?>
  </tbody>
</table>
Duplicate:
<?php 
echo json_encode($duplicate_list,JSON_PRETTY_PRINT);

// echo json_encode($duplicate_array,JSON_PRETTY_PRINT);

 ?>
 <br>
<?php 

echo sizeof($arr);

 ?> Record(s)
<!-- 
<?php 
// echo json_encode($arr,JSON_PRETTY_PRINT);
echo json_encode($complexArray, true);
 ?>

 -->

<?php 
die();
 ?>
