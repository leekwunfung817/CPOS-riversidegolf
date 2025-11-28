<?php

session_start();



if (!isset($_SESSION["management"])) {
     ?>
  <meta charset="UTF-8">
    <script type="text/javascript">
        alert('請使用您的員工帳號重新登錄\nPlease login with your staff account again');
        window.location.href = "./";
    </script>
    <?php
    die();
}












if (isset($_GET['fn'])) {
    $directory = './booking-records/';
    $json_data = read_json_files($directory);
    var_dump($json_data);
    die();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>高爾夫網上預訂記錄表</title>
  
  <style type="text/css">

    table {
      text-align: left;
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      text-align: left;
      border: 1px solid black;
      padding: 5px;
      white-space: nowrap;
      font-size: 15px;
    }

    th:first-child, td:first-child {
      width: 80px;
    }

    label {
      display: block;
    }

  </style>
</head>









<body>
  
<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>

  <form>  

<link href="bootstrap.css" rel="stylesheet"/>
<script src="jquery.js"></script>
<script src="bootstrap.js"></script>

<script type="text/javascript">
  $('[data-toggle="popover"]').popover({
    placement: "auto",
    trigger: "hover"
  })
</script>


<?php 
require_once 'booking-status-json-variable.php';
 ?>












<style>
  .green {
    background-color: green;
  }
  .yellow {
    background-color: yellow;
  }
  .red {
    background-color: red;
  }
  .blue {
    background-color: #5DE2E7;
  }
  .orange {
    background-color: orange;
  }
</style>
<style type="text/css">

span.tooltip {
      position: absolute;
      width: 100px;
      height: 20px;
      line-height: 20px;
      padding: 10px;
      font-size: 14px;
      text-align: center;
      color: rgb(113, 157, 171);
      background: rgb(255, 255, 255);
      border: 4px solid rgb(255, 255, 255);
      border-radius: 5px;
      text-shadow: rgba(0, 0, 0, 0.1) 1px 1px 1px;
      box-shadow: rgba(0, 0, 0, 0.1) 1px 1px 2px 0px;
}

span.tooltip:after {
      content: "";
      position: absolute;
      width: 0;
      height: 0;
      border-width: 10px;
      border-style: solid;
      border-color: #FFFFFF transparent transparent transparent;
      top: 44px;
      left: 50px;
}

b {
  font-size: 18px;
}
td {
  border-style: solid;
  border-width: medium;
  border-width: 1px;
  width: 60px;
  text-align: center;
}
</style>

<h1>高爾夫網上預訂記錄表</h1>


<hr>
<table>
  <tr>
    <th>
        
      <table style="width: 500px">
        <tr><th> 白色（預訂空缺）<br> White (Booking vacancy) </th><th></th></tr>
        <tr><th> 紅色（待付款）<br> Red (Pendding for payment) </th><th class="red"></th></tr>
        <tr><th> 黃色（已付款等待到達）<br> Yellow (Paid and wait for arrival) </th><th class="yellow"></th></tr>
        <tr><th> 橙色（車輛已到達）<br> Orange (Vehicle arrived) </th><th class="orange"></th></tr>
        <tr><th> 藍色（已簽到）<br> Blue (Checked-in) </th><th class="blue"></th></tr>
      </table>

    </th>
    <th>
      <iframe id="receipt_printing_buffer">
      </iframe>
    </th>
  </tr>
</table>

<hr>
  

    <table>
      <tbody>





























<?php

// Function to generate date string for the next 6 days (including today)
function getNextWeekDates() {
  $dates = [];
  for ($i = 0; $i < 8; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days"));
  }
  return $dates;
}

// Get next week dates
$dates = getNextWeekDates();

?>

<?php 
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





$hour_iterator = 1;
if ($half_hour_cluster) {
  $hour_iterator = 0.5;
}

foreach ($dates as $date) {
   ?><tr>
      <th colspan="15">
          <h3><?php echo $date; ?></h3>
      </th>
    </tr><?php 

  echo "<tr>";
  echo "<th>";
  for ($hour = 8; $hour <= 21; $hour=$hour+$hour_iterator) {
    $formattedHour = pointToHalfHour($hour);
    echo '<th>'.date('H:i', strtotime($formattedHour)).'</th>';
  }
  echo "</th>";
  echo "</tr>";

  foreach ($position_list as $key => $i) {
    echo "<tr>";
    echo "<td>$i</td>";
    for ($hour = 8; $hour <= 21; $hour=$hour+$hour_iterator) {
      $formattedHour = pointToHalfHour($hour);
      
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
      if (strlen($cssClass)>0) {
        $cssClass = ' class="'.$cssClass.'" ';
      }
    ?><td<?php echo $cssClass; ?><?php  

      // echo "$value";
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

     ?>><?php 
        // echo "$i";
        if (isset($booking_arr['data_'.$i])) {
          echo $booking_arr['data_'.$i]['id'];
        }
        

     ?></td><?php 
    }
    echo "<td>$i</td>"; ?></tr>
    <!-- </tbody></table></th></tr> -->
    <?php 

      
  } 
    echo "<tr>";
    echo "<th>";
    for ($hour = 8; $hour <= 21; $hour=$hour+$hour_iterator) {
      $formattedHour = pointToHalfHour($hour);
      echo '<th>'.date('H:i', strtotime($formattedHour)).'</th>';
    }
    echo "</th>";
    echo "</tr>";
} ?>

      </tbody>
    </table>

<script type="text/javascript">

setTimeout(function(){
  window.location.reload(1);
}, 30000);


function discount_digit_convert(data) {
  if (data=='S') {
      return 'Student';
  } else if (data=='H') {
      return 'No disount';
  } else if (data=='D') {
      return 'Disabled';
  }
}

function comfirm_and_print(
  auth,
  id,
  name,
  telephone,
  octopus_no,
  check_digit,
  booking_date,
  begin_hour,
  end_hour,
  p_selections,
  discount,
  auth_code,
  req_card_number,
  amount,
  cash
) {

  var msg = '';
  var printing = '<h1>白石高球練習場</h1>';
  printing += '<div style="text-align: right;">Tel: 27771813</div>';
  printing += '<div style="text-align: right;">RIVERSIDE Whitehead Golf Club</div>';
  printing += '<i style="text-align: center;"><hr></i>';

  sourceTxt = p_selections;
  if (sourceTxt.length>1) {
    msg += 'Booking:'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.8em;">Bay: '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = booking_date;
  if (sourceTxt.length>1) {
    msg += 'Date:'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.8em;">Date: '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = begin_hour;
  sourceTxt2 = end_hour;
  if ( sourceTxt.length>0 && sourceTxt2.length>0 ) {
    msg += 'Time:'+sourceTxt+' to '+sourceTxt2+'\n';
    printing += '<b style="text-align: left;font-size: 1.8em;">Time: '+sourceTxt+'-'+sourceTxt2+'</b><br>';
  }
  
  sourceTxt = name;
  if (sourceTxt.length>1) {
    msg += 'Name:'+sourceTxt+'\n';
    printing += '<i style="text-align: right;">Name: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = telephone;
  if (sourceTxt.length>1) {
    msg += 'Tel:'+sourceTxt+'\n';
    printing += '<i style="text-align: right;">Tel: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = auth;
  if (sourceTxt.length>1) {
    msg += 'Auth:'+sourceTxt+'\n';
  }
  
  sourceTxt = id;
  if (sourceTxt.length>1) {
    msg += 'ID:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">ID: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = octopus_no;
  sourceTxt2 = check_digit;
  if ( sourceTxt.length>1 && sourceTxt2.length>1 ) {
    msg += 'Octopus: '+sourceTxt+' ('+sourceTxt2+')'+'\n';
  }
  
  sourceTxt = auth_code;
  if (sourceTxt.length>1) {
    msg += 'Auth Code: '+sourceTxt+'\n';
  }
  
  sourceTxt = req_card_number;
  if (sourceTxt.length>1) {
    msg += 'Card Number: '+sourceTxt+'\n';
  }
  
  sourceTxt = discount_digit_convert(discount);
  if (sourceTxt.length>0) {
    msg += 'Discount:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Discount: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = amount;
  if (sourceTxt.length>1) {
    msg += 'Credit Card Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Credit Card</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;">Amount: '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = cash;
  if (sourceTxt.length>1) {
    msg += 'Cash Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Cash</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;">Amount: '+sourceTxt+'</b><br>';
  }
  if (confirm(msg)) {
    const oIframe = document.getElementById('receipt_printing_buffer');
    oIframe.contentWindow.document.open();
    oIframe.contentWindow.document.write(printing);
    oIframe.contentWindow.document.close();
    oIframe.contentWindow.print();
  }
}
</script>

<?php   

die();

 ?>
