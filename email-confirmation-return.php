<?php 
require_once './logger.php';
t_log('begin[email-confirmation-return.php]');

session_start();
$is_management = isset($_SESSION["management"]);


function stop_by_invalid_booking_2($str)
{

    m_log("return page stop_by_invalid_booking_2 $str");
     ?>
    <script type="text/javascript">
        alert('<?php echo $str; ?>');
        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    die();
}


if (isset($_GET['DEMO_DEBUG'])) {
    stop_by_invalid_booking_2('DEMO_DEBUG');
}


require_once 'tesing_stage_verification.php';
 ?>
    <meta charset="utf-8">



<?php 



function stop_by_invalid_booking($str)
{
    stop_by_invalid_booking_2("抱歉，當您提交預訂請求時，您的預訂在球道上與其他預訂發生衝突\\n Sorry, your booking is conflicted in fairway with another booking when you submit the booking request. $str");
}



$allGetParams = array_merge($_GET, $_POST);

if (count($_GET) == 2) {
    // Escape values for security (prevent XSS)
    foreach ($allGetParams as $key => $value) {
        $allGetParams[$key] = htmlspecialchars($value);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <body>
    <form id="proceed_form" action="./email-confirmation-return.php" method="post">
        <?php foreach ($allGetParams as $key => $value) : ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
        <?php endforeach; ?>
        <button type="submit" style="display: none;">Submit Data</button>
    </form>
    </body>
    </html>
    <script> document.getElementById('proceed_form').submit();</script>
    <?php
    die();
} else if ( !isset($allGetParams['auth']) || !isset($allGetParams['name']) ) {
    stop_by_invalid_booking_2("您使用本網站的方式不恰當，請逐步使用\\nThe way you are using this website is inappropriate, please use it step by step ".json_encode($allGetParams));
}


$auth = $allGetParams['auth'];
$name = $allGetParams['name'];

m_log("reach email return $auth");



 ?>

<!DOCTYPE html>
<html>
<head>
     <meta charset="utf-8">
     <meta http-equiv="x-ua-compatible" content="ie=edge">
     <title>高爾夫球場預訂確認</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <style type="text/css">

    html {
        background-image: linear-gradient(to right top, 
            #FC30CD,
            #30FCC6, 
            #FCF230, 
            #FFAD31,
            #FC3030
        );
        padding: 30px;
    }
    body {
        background-color: white;
        padding: 50px;
        font-size: 2em;
        
        box-shadow: 4px 4px grey;
    }


@media only screen and (max-width: 1000px) {
/*    h1 {
        font-size: 1.3em;
    }
    body {
        font-size: 1em;
    }
*/
    h1 {
        font-size: 1.8em;
    }
    body {
        font-size: 1em;
    }

}   
         /** Google webfonts. Recommended to include the .woff version for cross-client compatibility. */
         @media screen {
             @font-face {
                 font-family: 'Source Sans Pro';
                 font-style: normal;
                 font-weight: 400;
                 src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe035) fors0.
             }
         }
         /* Rest of your CSS styles go here */
     </style>
</head>

<body>

<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//analytics.austreme.com/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '215']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>



<script type="text/javascript">

    // window.addEventListener('beforeunload', (event) => {
    //     event.returnValue = `如果您想繼續預訂，您應該付款，否則您不能離開。 \n If you want to proceed with the booking you should pay otherwise you cannot leave.`;
    // });


</script>
<!-- 

<?php

require_once 'account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
















var_dump($allGetParams);


// var_dump($booking_arr);

// echo json_encode($booking_arr);

// print_r(json_decode(json_encode($booking_arr), true));
// print_r(json_decode(json_encode($booking_arr), true)['p_selections']);

// $booking_position_arr = array();
// foreach ($booking_arr['p_selections'] as $x) {
//     // echo "$x";
//     $booking_position_int = (int) str_replace("position_", "", $x);
//     array_push($booking_position_arr, $booking_position_int);
// }

?>
 -->

<?php 












$sql = "SELECT * FROM golf_fairway_booking where auth='".$auth."'; ";
$result = $conn->query($sql);
$booking_arr = array();


$key3='booking';

$total_buffer_count = 0;

$id = null;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $booking_arr = $row;

        $id = $row['id'];
        $key1 = $row['booking_date'];
        $begin_hour = (int) $row['begin_hour'];
        $end_hour = (int) $row['end_hour'];

        for ($cursor_hour=$begin_hour; $cursor_hour <= $end_hour; $cursor_hour=$cursor_hour+0.5) {
            $hour_int = ((int) $cursor_hour);
            $is_half_hour = $cursor_hour != $hour_int;
            $half_hour_mark = ($is_half_hour ? ':30' : ':00');
            $key2=$hour_int . $half_hour_mark;

            foreach (json_decode($row['p_selections']) as $key => $position) {
                $total_buffer_count += 1;


                $sql = "
                INSERT INTO `golf_booking_buffer` (`date`, `hour`, `position`, `src`) 
                select '$key1','$key2','$key4','$id'
                where (
                    select count(*) 
                    from `golf_booking_buffer` 
                    where `date`='$key1' and `hour`='$key2' and `position`='$key4'
                )=0
                ;
                ";
                try {
                    if ($conn->query($sql) === TRUE) {
                        if (isset($_SESSION["management"])) {
                            // echo "insert '$key1','$key2','$key4','$id' success<br>";
                        }
                    } else {
                    }
                } catch (Exception $e) {
                    echo $e;
                }
            }
        }
    }
}





$_GET['exact_date'] = $booking_arr['booking_date'];


if (sizeof($booking_arr)==0) {
    // var_dump($booking_arr);
    stop_by_invalid_booking_2("(1) 您的預訂不可再用，請申請新的預訂 \\n Your booking is no longer available, please apply for a new booking $auth");
}

require_once './checker_duplicate_bay.php';

if (is_booking_duplicate_by_auth($booking_arr['booking_date'], $auth)) {
    stop_by_invalid_booking_2("Bay Duplicated $auth");
}


if (check_duplicate_by_booking(
    $booking_arr['booking_date'], 
    $booking_arr['begin_hour'], 
    $booking_arr['end_hour'], 
    $booking_arr['p_selections'])
) {
    // stop_by_invalid_booking(" Bay Duplicated ");
    // echo " Bay Duplicated ";
} else {
    stop_by_invalid_booking_2("(2) 您的預訂不可再用，請申請新的預訂 \\n Your booking is no longer available, please apply for a new booking $auth");
}




{
    $sql = "
SELECT `date`, `hour`, `position`, `src`
FROM `golf_booking_buffer`
where '$id'=`golf_booking_buffer`.`src`
;

    ";

    $result = $conn->query($sql);


    if (($id==null) || $result->num_rows!=$total_buffer_count) {
        if (isset($_SESSION["management"])) {
            // echo " $result->num_rows!=$total_buffer_count ";
            // stop_by_invalid_booking(" $result->num_rows!=$total_buffer_count ");
        }
    }


}















































        $sql = "SELECT `price` FROM `golf-payment-session` WHERE `auth`='$auth' AND `payment-datetime` IS NOT NULL; ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $price = (double) $row['price'];

    m_log("return page stop_by_invalid_booking_2 directly to go payment-confirm.php $auth");
                 ?>

<script type="text/javascript">
    window.location.href = "./payment-page/payment-confirm.php?auth=<?php echo $auth; ?>";
</script>
<?php
            }
        }


 ?>


     <div style="width: 100%;">
        <h1>預訂資料</h1>



<i><small style="color: red;font-size: 0.6em;">



    請不要退出此頁面，並請在15分鐘內完成付款，否則您的預訂將在 15 分鐘後取消。
在這預訂過期之前，您將無法返回目前選擇的相同預訂日期時間和球道。<br>
Please do not exit this page and please complete payment within 15 minutes, otherwise your reservation will be canceled after 15 minutes.
You will not be able to return to the same booking date, time and bay(s) currently selected until this booking expires.
</small></i><br>

        <small style="color: red;font-size: 0.5em;">
<a href="./process_expire.php?id=<?php echo $id; ?>&auth=<?php echo $auth; ?>">取消預訂 Cancel booking</a><br>
</small>

        
        <p><?php echo $name; ?> 你好</p>
        <p>感謝您預訂我們的高爾夫球場。 </p>
        <p>目前未付款, 以下是您的預訂詳情：  </p>
<?php 





$date_display = $booking_arr['booking_date'].' ('.date('l', strtotime($booking_arr['booking_date'])).')'; // Get the day name from the date
$time_display = "";

if (strpos($booking_arr['begin_hour'], '.5') !== false) {
    $time_display .= str_replace('.5', ':30', $booking_arr['begin_hour']);
} else {
    $time_display .= $booking_arr['begin_hour'].':00';
}
$time_display .= " - ";
if (strpos($booking_arr['end_hour'], '.5') !== false) {
    $time_display .= str_replace('.5', ':30', $booking_arr['end_hour']);
} else {
    $time_display .= $booking_arr['end_hour'].':00';
}

$position_display = str_replace( array('[', '"', ']', ' '), '', $booking_arr['p_selections'] );
// $position_display = $booking_arr['p_selections'];

 ?>
        <ul>
            <li>編號：<?php echo $id; ?></li>
            <li>日期：<?php echo $date_display; ?></li>
            <li>時間：<?php echo $time_display; ?></li>
         <li>球場名稱：白石高爾夫球練習場</li>
         <li>球道號碼：<?php echo $position_display; ?> </li>
         <li>優惠 : <?php 
if ($booking_arr['discount'] == 'S') {
    echo "學生";
} else if ($booking_arr['discount'] == 'D') {
    echo "傷健人士";
} else if ($booking_arr['discount'] == 'H') {
    echo "沒有優惠";
}
          ?> </li>
          <li>八達通卡 : <?php 
if ($booking_arr['octopus_no'] == null || $booking_arr['octopus_no'] == '') {
    echo "不需要 (不會開車駛入)";
} else {
    echo $booking_arr['octopus_no'].'('.$booking_arr['check_digit'].')';
}
          ?> </li>
         <!-- 其他預訂細節 -->
        </ul>

<?php 

// require_once 'price-calculation.php';

// $total_price = price_calculation( array(
//     'lan' => 'zn',
//     'print' => 'N'
// ), $booking_arr);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

 ?>







<?php 

$payment_page_url = "./payment-page/?auth=".$auth;
$payment_page_url_cash = $payment_page_url."&cash=T"; 
$payment_page_url_unpaid = $payment_page_url."&account_unpaid=T"; 



 ?>
<h2>請選擇以下付款方式</h2>
<ul>

<li>
<h3><a href="<?php echo $payment_page_url; ?>" target="_blank"><b>信用卡</b></a></h3>
</li>

<?php 
if ($is_management) {
    ?>
<li>
<h3><a href="<?php echo $payment_page_url_cash; ?>" >
<b>現金</b> (僅管理人員可見)
</a></h3>
</li>


<li>
<h3><a href="<?php echo $payment_page_url_unpaid; ?>" >
<b>記帳 - 轉帳或支票 </b> (僅管理人員可見)
</a></h3>
</li>

<?php
}
 ?>

</ul>


         <p>如果您有任何問題或需要進一步的協助，請隨時與我們聯絡。 </p>
         <p>再次感謝您選擇我們的高爾夫球場！ </p>
         <p>祝您有個愉快的高爾夫體驗。 </p>
         <p>此致，</p>
         <p>白石高爾夫球練習場 團隊</p>

<hr>
         <!-- <h1>White Head Club - Golf Court reservation confirmed</h1> -->
         <p>Dear <?php echo $name; ?></p>

         <p>Thank you for booking our golf course. No payment has been made yet, here are your booking details:</p>
         <ul>
             <li>ID：<?php echo $id; ?></li>
             <li>Date：<?php echo $date_display; ?></li>
             <li>Time：<?php echo $time_display; ?></li>
             <li>Location：Riverside Whitehead Golf Club</li>
             <li>Bay No.：<?php echo $position_display; ?> </li>
             <li>Discount : <?php 
if ($booking_arr['discount'] == 'S') {
    echo "Student";
} else if ($booking_arr['discount'] == 'D') {
    echo "Disabled";
} else if ($booking_arr['discount'] == 'H') {
    echo "None";
}
          ?> </li>
          <li>Octopus Card : <?php 
if ($booking_arr['octopus_no'] == null || $booking_arr['octopus_no'] == '') {
    echo "No needed (Will not drive in)";
} else {
    echo $booking_arr['octopus_no'].'('.$booking_arr['check_digit'].')';
}
          ?> </li>
             <!-- 其他預訂細節 -->
         </ul>
<?php 

// $total_price = price_calculation( array(
//     'lan' => 'en',
//     'print' => 'N'
// ), $booking_arr);


 ?>











<h2>Please choose the following payment method</h2>
<ul>

<li>
<h3><a href="<?php echo $payment_page_url; ?>" target="_blank"><b>Credit Card</b></a></h3>
</li>

<?php 
if ($is_management) {
    ?>
<li>
<h3><a href="<?php echo $payment_page_url_cash; ?>" >
<b>Cash</b> (Management only)
</a></h3>
</li>


<li>
<h3><a href="<?php echo $payment_page_url_unpaid; ?>" >
<b>Keep accounts - Bank transfer or pay check </b> (Management only)
</a></h3>
</li>

<?php
}
 ?>

</ul>







         <p>If you have any questions or need further assistance, please feel free to contact us. </p>
          <p>Thank you again for choosing our golf course! </p>
          <p>Wish you have a pleasant golf experience. </p>
          <p>Sincerely,</p>
          <p>White Rock Driving Range Team</p>
     </div>

<?php 




// function check_buffer_count($conn,$data)
// {

//     $id = $data['id'];
//     $key1 = $data['booking_date'];
//     $begin_hour = (int) $data['begin_hour'];
//     $end_hour = (int) $data['end_hour'];
//     $p_selections = $data['p_selections'];

//     $buffer_count = 0;

//     for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
//         $hour_int = ((int) $cursor_hour);
//         $is_half_hour = $cursor_hour != $hour_int;
//         $half_hour_mark = ($is_half_hour ? ':30' : ':00');
//         $key2=$hour_int . $half_hour_mark;
//         foreach (json_decode($p_selections) as $key => $position) {
//             // echo $position.'<br>';
//           $buffer_count += 1;

//                  $key4=str_replace("position_", "", $position);
//                  $sql_1 = "
//                  INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
//                  VALUES 
//                  ('$key1','$key2','$key4','$id');
//                  ";
//                    try {
//                        // Execute the query
//                        if ($conn->query($sql_1) === TRUE) {
//                            // echo "Data inserted successfully!";
//                        } else {
//                        //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
//                        // echo "SQL error 222 $sql";
//                        }
//                    } catch (Exception $e) {
//                        // echo $e;
//                        // echo "Exception 222 $sql_1";
//                    }


//                  $sql_1 = "
//                  UPDATE `golf_booking_buffer` SET `src`='$id'
//                  WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';
//                  ";
//                    try {
//                        // Execute the query
//                        if ($conn->query($sql_1) === TRUE) {
//                            // echo "Data inserted successfully!";
//                        } else {
//                        //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
//                        // echo "SQL error 222 $sql";
//                        }
//                    } catch (Exception $e) {
//                        // echo $e;
//                        // echo "Exception 222 $sql_1";
//                    }





//         }
//     }
//     return $buffer_count;

// }

// $sql = "
// SELECT * FROM `golf_fairway_booking`;
// ";

// $result = $conn->query($sql);
// if ($result->num_rows > 0) {
//   while ($row = $result->fetch_assoc()) {
//     $count = check_buffer_count($conn,$row);
//     // echo $count;
//     // echo "<br>";
//     // var_dump($row);
//     // echo "<br>";
//   }
// }



 ?>

<script type="text/javascript">
// Set a timeout to close the page after 15 minutes (900,000 milliseconds)
setTimeout(() => {
    window.close();
}, 15*60*1000); // 900,000 milliseconds = 15 minutes

</script>

</body>
</html>


















<?php



// if (!$is_management) {

$sql = "
SELECT * FROM golf_confirmed_booking
where `golf_confirmed_booking`.`auth`='$auth' 
and (
    select count(*) c 
    from `golf_fairway_booking`
    where `golf_fairway_booking`.`auth`='$auth'
)>0
;
";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // stop_by_invalid_booking_2("您使用本網站的方式不恰當，您不應多次造訪此頁面\\nThe way you are using this website is inappropriate, You should not visit this page more than once.");
// } else {
    // echo "0 results";
} else {


// Construct the SQL query
$sql = "INSERT INTO golf_confirmed_booking (auth)
        VALUES ('$auth')";
try {
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // echo "Data inserted successfully!";
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
    }

} catch (Exception $e) {
    
}



}

// }









// // Construct the SQL query
// $sql = "INSERT INTO `golf-payment-session`(`auth`, `price`) VALUES ('$auth','$total_price');";
// try {
    
//     // Execute the query
//     if ($conn->query($sql) === TRUE) {
//         // echo "Data inserted successfully!";
//     } else {
//         // echo "Error: " . $sql . "<br>" . $conn->error;
//     }

// } catch (Exception $e) {
    
// }




















































// require_once './clear_record.php';


t_log('end[email-confirmation-return.php]');

?>


















