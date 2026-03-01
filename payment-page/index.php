<?php 
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

require_once '../logger.php';


// die();
session_start();
$is_management = isset($_SESSION["management"]);

$reserve_type = $_SESSION['type'];







$allGetParams = array_merge($_GET, $_POST);

$is_pay_by_cash = isset($allGetParams['cash'])
 // && $allGetParams['cash']=='T'
 && $is_management
 ;

$is_account_unpaid = isset($allGetParams['account_unpaid'])
 && $is_management
 ;

if ($is_pay_by_cash && !$is_management) {
    echo "Only manager can pay by cash";
    die();
}

require_once '../tesing_stage_verification.php';
 ?>
	<meta charset="utf-8">



<?php 


if (count($_GET) == 1) {
    // Escape values for security (prevent XSS)
    foreach ($allGetParams as $key => $value) {
        $allGetParams[$key] = htmlspecialchars($value);
    }
    ?>
    <!DOCTYPE html>
    <html>
    <body>
    <form id="proceed_form" action="./" method="post">
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
} else if ($is_management) {
    
} else if (!isset($allGetParams['auth']) 
    // ||count($allGetParams) != 1
) {
    m_log("payment index.php 您使用本網站的方式不恰當，請逐步使用 ".json_encode($allGetParams));
     ?>

    <script type="text/javascript">
        alert('您使用本網站的方式不恰當，請逐步使用\nThe way you are using this website is inappropriate, please use it step by step');
        setTimeout(function() {
            window.location.href = "../";
        }, 1000);
    </script>
    <?php
    die();
}
$auth = $allGetParams['auth'];

m_log("reach payment index.php $auth");

require_once '../account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 ?>

<?php

  $sql = "SELECT * FROM golf_fairway_booking where auth='$auth'; ";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
  } else {
    m_log("payment index.php 您使用本網站的方式不恰當");
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當\nThe way you are using this website is inappropriate');
        setTimeout(function() {
            window.location.href = "../";
        }, 1000);
    </script>
    <?php
  }

    // 顯示總價
    // echo "總價：$total_price<br>";
?>


<?php 



$sql = "SELECT * FROM `golf-payment-session` where `payment-datetime` is not null and `auth`='".$auth."'; ";
$result = $conn->query($sql);
$booking_arr = array();

if ($result->num_rows > 0) {

    m_log("payment index.php 您不應多次造訪此頁面 $auth");
     ?>
    <script type="text/javascript">
        alert('您不應多次造訪此頁面\n You should not visit this page more than once.');
        setTimeout(function() {
            window.location.href = "../";
        }, 1000);
    </script>
    <?php
    die();
}



?>

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






<link href="bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<style type="text/css">
    h1 {
        font-size: 2.3em;
    }

    html {
        background-image: linear-gradient(to top, 
            #FC9630, 
            #30FCE1,
            #30B1FC
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
    h1 {
        font-size: 2.5em;
    }
    body {
        font-size: 2.2em;
    }

}   
</style>
<!-- <script src="jquery.min.js"></script> -->
<!-- <script src="bootstrap.min.js"></script>
<link rel="stylesheet" href="all.css"> -->






















<!-- 


<div class="container">
<br>
<hr>

<div class="row">
    <aside class="col-sm-6">

 -->





















<hr>
<h1>預訂詳情 <br>Booking Details</h1>

<i><small style="color: grey;font-size: 0.6em;">請不要退出此頁面，否則您的預訂將在 15 分鐘後取消。在該預訂過期之前，您將無法返回目前選擇的相同預訂日期時間和球道。<br>
Please do not exit this page or your reservation will be canceled after 15 minutes. You will not be able to return to the same reservation date time and course you currently selected until that reservation expires.</small></i>

<hr>

<?php 
require_once '../price-calculation.php';

 ?>


<?php 


// Special Period (Especially for VIP rest time)
$sql = "SELECT 
    *, 
    (
            SELECT price from `golf-payment-session` 
            where `golf-payment-session`.`auth`=`golf_fairway_booking`.`auth`
    ) price
FROM `golf_fairway_booking` where `auth`='".$auth."' ; ";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price = (double) $row['price'];
                // 吸收表單送出的資料
                $name = $row['name'];
                $email = $row['email'];
                $booking_date = $row['booking_date'];
                $begin_hour = $row['begin_hour'];
                $end_hour = $row['end_hour'];
                $num_spot = $row['p_selections'];

                $position_display = str_replace( array('[', '"', ']', ' '), '', $row['p_selections'] );

                $items = explode(',', $position_display);

                foreach ($items as &$v) {
                    if (ctype_digit($v)) {              // ensure numeric
                        $num = (int)$v;
                        if ($num >= 100 && $num <= 199) {
                            $v = $num - 99;            // subtract 100
                            $v = "".$v;
                        }
                    }
                }

                $position_display = implode(',', $items);


                // 顯示資料
                echo "姓名 Name：$name<br>";
                echo "電子郵件 Email Address：$email<br>";
                echo "預定日期 Booking Date：$booking_date<br>";
                echo "開始時間 Begin time：".pointToHalfHour($begin_hour)."<br>";
                echo "結束時間 End time：".pointToHalfHour($end_hour)."<br>";
                echo ($reserve_type == 'pickleball'?'球場號碼 Court No.':'球道號碼 Bay No.')."：$position_display<br>";
                
    }
}


 ?>
<?php 


$sql = "
    update `golf_fairway_booking` 
    set `timestamp`=CURRENT_TIMESTAMP
    where `auth`='".$auth."';
";

try {
   if ($conn->query($sql) === TRUE) {
   } else {
   }
} catch (Exception $e) {
}


 ?>
<?php

  $sql = "SELECT * FROM golf_fairway_booking where auth='$auth'; ";
  $result = $conn->query($sql);
  $booking_arr = array();


  $key3='booking';

  if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
          $booking_arr = $row;
      }
  } else {
      echo "0 results";
  }

    // 顯示總價
    // echo "總價：$total_price<br>";
?>
<?php 
$begin_hour = (double) $booking_arr['begin_hour'];
$end_hour = (double) $booking_arr['end_hour'];
    ?>
<hr>
<?php 

$total_price = price_calculation( array(
    'lan' => 'all',
    'print' => 'Y'
), $booking_arr);

 ?>


<?php 
cyber_source_make_payment($total_price, $auth, $is_pay_by_cash, $is_account_unpaid);

 ?>

<?php 

function cyber_source_make_payment($amount, $auth, $is_pay_by_cash, $is_account_unpaid)
{
 ?>
 <style type="text/css">
    input {
        width: 1000px;
    }
 </style>

    <!-- 
    access_key:<input type="text" name="access_key" value="cf4b773036d53da9b3cc010ce86858da"><br>
    profile_id:<input type="text" name="profile_id" value="4048C58C-89F6-4FFE-A6EE-90C4DCE5C5F1"><br>
    transaction_uuid:<input type="text" name="transaction_uuid" value="<?php echo uniqid(); ?>" readonly><br>
    signed_field_names:<input type="text" name="signed_field_names" value="access_key,profile_id, transaction_unid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type, reference_number,amount,currency"><br>
    unsigned_field_names:<input type="text" name="unsigned_field_names"><br>
    signed_date_time:<input type="text" name="signed_date_time" value="<?php echo gmdate("Y-m-d\TH:i:s\2"); ?>" readonly><br>
    locale:<input type="text" name="locale" value="en" readonly><br>
    transaction_type:<input type="text" name="transaction_type" value="authorization" readonly><br>
    reference_number:<input type="text" name="reference_number" value="<?php 
$digits = 13;
echo rand(pow(10, $digits-1), pow(10, $digits)-1);
     ?>" readonly><br>
    payment_method:<input type="text" name="payment_method" value="Visa" readonly><br>
    amount:<input type="text" name="amount" value="<?php echo "$amount"; ?>.00" readonly><br>
    currency:<input type="text" name="currency" value="HKD" readonly><br>

 -->

<form action="<?php 
    if ($is_account_unpaid) {
        echo "./account_unpaid.php";
    } else if ($is_pay_by_cash) {
        echo "./payment-confirm.php";
    } else {
        echo "../SecureAcceptanceCheckout/";
    }
 ?>" method="post" id="forward_form">

<table style="width: 300px">
<!--   <tr>
    <td>access_key:</td>
    <td><input type="text" name="access_key" value="cf4b773036d53da9b3cc010ce86858da"></td>
  </tr>
  <tr>
    <td>profile_id:</td>
    <td><input type="text" name="profile_id" value="4048C58C-89F6-4FFE-A6EE-90C4DCE5C5F1"></td>
  </tr>
  <tr>
    <td>transaction_uuid:</td>
    <td><input type="text" name="transaction_uuid" value="<?php echo uniqid(); ?>" readonly></td>
  </tr>
  <tr>
    <td>signed_field_names:</td>
    <td><input type="text" name="signed_field_names" value="access_key,profile_id, transaction_unid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type, reference_number,amount,currency"></td>
  </tr>
  <tr>
    <td>signature:</td>
    <td><input type="text" name="signature" value=""></td>
  </tr>
  <tr>
    <td>unsigned_field_names:</td>
    <td><input type="text" name="unsigned_field_names"></td>
  </tr>
  <tr>
    <td>signed_date_time:</td>
    <td><input type="text" name="signed_date_time" value="<?php echo gmdate("Y-m-d\TH:i:s\2"); ?>" readonly></td>
  </tr>
  <tr>
    <td>locale:</td>
    <td><input type="text" name="locale" value="en" readonly></td>
  </tr>
  <tr>
    <td>transaction_type:</td>
    <td><input type="text" name="transaction_type" value="authorization" readonly></td>
  </tr>
  <tr>
    <td>reference_number:</td>
    <td><input type="text" name="reference_number" value="<?php $digits = 13; echo rand(pow(10, $digits-1), pow(10, $digits)-1); ?>" readonly></td>
  </tr>
  <tr>
    <td>payment_method:</td>
    <td><input type="text" name="payment_method" value="Visa" readonly></td>
  </tr> --><!-- 
  <tr>
    <td>reference_number:</td>
    <td><input type="hidden" name="amount" value="<?php echo "$amount"; ?>.00" readonly></td>
  </tr>
  <tr>
    <td>amount:</td>
    <td><input type="hidden" name="amount" value="<?php echo "$amount"; ?>.00" readonly></td>
  </tr>
   --><!--
  <tr>
    <td>currency:</td>
    <td><input type="text" name="currency" value="HKD" readonly></td>
  </tr> -->
</table>
<input type="hidden" name="amount" id="amount" value="<?php echo "$amount"; ?>.00" readonly>
<input type="hidden" name="reference_number" value="<?php echo $auth; ?>" readonly>


<?php if ($is_pay_by_cash) { ?>
<input type="hidden" name="cash" value="T">
<?php } ?>


<?php if ($is_account_unpaid) { ?>
<input type="hidden" name="account_unpaid" value="T">
備註 Remark : <textarea name="remark"></textarea>
<?php } ?>

<?php if ($is_pay_by_cash || $is_account_unpaid) { ?>

<input type="hidden" name="req_reference_number" value="<?php echo $auth; ?>">
<input type="hidden" name="decision" value="ACCEPT">
<input type="hidden" name="req_currency" value="HKD">

<hr>

<div style="border: 1px solid black;">

<b style="color: red;">
管理專用輸入區<br>
Management input area
</b>

<hr>

<table style="width: 80%;">
    <tr>
        <td>費用倍增 <br> Fee Multiplier:
        </td>
        <td>
            <input type="number" name="percentage" id="percentage" style="width: 50%;" value="100"

             min="0" max="10000"

             onchange="updateFinalAmount()"
              onkeyup="updateFinalAmount()"
              >%<br>
            <small style="color: red">
                注意：100%不會增減金額 <br> Notice: 100% will not increase or decrease the amount
            </small>
        </td>
    </tr>
    <tr>
        <td>費用附加 (倍增後) <br> Additional Charge (After multiplied):</td>
        <td><input type="number" name="addition" id="addition" style="width: 50%;" value="0"

             min="-100000" max="100000"

         onchange="updateFinalAmount()"
          onkeyup="updateFinalAmount()"
          ></td>
    </tr>
    <tr>
        <td style="color: red;">
            <hr>
            注意！如果沒有收到現金，請勿點選確認按鈕。
            <br>
            Notice! If you don't receive cash, don't click the confirm button.
        </td>
    </tr>
    <tr>
        <td>
            <hr>
            <b id="cal_result"></b>
        </td>
    </tr>
</table>
<script type="text/javascript">
    original_amount = <?php echo "$amount"; ?>;
    function updateFinalAmount() {
        var percentage_value = document.getElementById('percentage').value;
        if (percentage_value == NaN || percentage_value == null ||percentage_value == '') {
            percentage_value = 100;
            document.getElementById('percentage').value = percentage_value;
        }
        var addition_value = document.getElementById('addition').value;
        if (addition_value == NaN || addition_value == null ||addition_value == '') {
            addition_value = 0;
            document.getElementById('addition').value = addition_value;
        }

        var percentage = parseFloat(percentage_value);
        var addition = parseFloat(addition_value);
        var after_percentage = original_amount * (percentage/100);
        var final_amount = (after_percentage + addition);
        document.getElementById('cal_result').innerHTML = '費用倍增 Fee Multiplier: <br> $'+original_amount+' x '+percentage+'% = $'+after_percentage+'<br>'
        +'費用附加 (倍增後) Additional Charge (After multiplied): <br> $'+after_percentage+' + '+addition+' = $'+final_amount+'<br>'
        +'<h1>最終總額 Final total: $'+final_amount+'</h1>'
        ;
        document.getElementById('amount').value = final_amount;
    }
    updateFinalAmount();
</script>

<hr>

</div>
<br>
<?php } ?>


<?php 

if (!$is_account_unpaid && !$is_pay_by_cash) {
 ?>
<b style="color: red;">
    當信用卡系統長時間顯示載入圖示時，請另外手動開啟手機銀行應用程式確認付款。
    <br>
    When the credit card system displays the loading icon for a long time, please manually open the mobile banking application to confirm the payment.
</b>
<?php 
}
 ?>
    <input type="submit"
style="
    color: black;
    background-image: linear-gradient(to right top, orange, yellow);
    padding: 30px;
    width: 100%;
    border-radius: 30px;
    display: block;
    text-align: center;
    cursor: pointer;
" value="<?php 


if ($is_account_unpaid) {
    m_log("payment index.php chose keep account $auth");
    echo "確認記帳 Confirm to keep account";
} else if ($is_pay_by_cash) {
    m_log("payment index.php chose cash $auth");
    echo "確認收到現金 Confirm receipt of cash";
} else {
    m_log("payment index.php chose credit card $auth");
    echo "確認並以信用卡支付 Confirm and pay by credit card";
}
 ?>">
    <input id="submit_value" type="button" name="submit_value" style="display: none">
</form>


<i><small style="color: grey;font-size: 0.6em;">付款指南：輸入您的名字和姓氏後，選擇信用卡類型或付款方式。然後輸入財務認證資訊並點擊付款按鈕。<br>
Payment Guidance: After enter your First name and last name, choose the type of credit card or payment method. Then input the financial authentication information and click the payment button.</small></i>

<hr>
<i><small style="color: grey;font-size: 0.6em;">注意：此付款程序均不允許洗錢。 Note: Money laundering is not allowed in this payment.</small></i>

<?php 
}
 ?>
<!-- 您的頁面將自動跳到信用卡付款頁面
Your page will automatically jump to the credit card payment page
<b id="counter"></b> 秒 second(s)

<script>
let count = 0;
let target = 5;
let intervalId = setInterval(function() {
    count++;
    document.getElementById('counter').innerHTML = target - count;
    if(count === target) {
        clearInterval(intervalId);
        document.getElementById('forward_form').submit();
    }
}, 1000);

</script>
 -->
<?php 

// Close the connection
$conn->close();
exit();
 ?>

