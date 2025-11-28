<?php 
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// die();
session_start();
$is_management = isset($_SESSION["management"]);

require_once '../tesing_stage_verification.php';
 ?>
	<meta charset="utf-8">



<?php 


$allGetParams = array_merge($_GET, $_POST);
$is_pay_by_cash = $is_management && isset($allGetParams['cash']) && $allGetParams['cash']=='T';

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
// } else if ($is_management) {
    
} else if (!isset($allGetParams['auth'])||count($allGetParams) != 1) {
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當，請逐步使用\nThe way you are using this website is inappropriate, please use it step by step');
        window.location.href = "../";
    </script>
    <?php
    die();
}
$auth = $allGetParams['auth'];


require_once '../account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

 ?>

<?php

  $sql = "SELECT * FROM golf_fairway_booking where auth='".$allGetParams['auth']."'; ";
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
  } else {
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當\nThe way you are using this website is inappropriate');
        window.location.href = "../";
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
     ?>
    <script type="text/javascript">
        alert('您不應多次造訪此頁面\n You should not visit this page more than once.');
        window.location.href = "../";
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
function pointToHalfHour($pointHour)
{
  $hour_int = ((int)$pointHour);
  if ($pointHour == $hour_int) {
    $half_hour_mark = ':00';
  } else {
    $half_hour_mark = ':30';
  }
  return $hour_int . $half_hour_mark;
}
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
				// 顯示資料
				echo "姓名 Name：$name<br>";
				echo "電子郵件 Email Address：$email<br>";
				echo "預定日期 Booking Date：$booking_date<br>";
				echo "開始時間 Begin time：".pointToHalfHour($begin_hour)."<br>";
				echo "結束時間 End time：".pointToHalfHour($end_hour)."<br>";
				echo "球道號碼 Bay No.：$position_display<br>";
				
    }
}


 ?>

<?php

  $sql = "SELECT * FROM golf_fairway_booking where auth='".$allGetParams['auth']."'; ";
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


require_once '../price-calculation.php';

$total_price = price_calculation( array(
    'lan' => 'all',
    'print' => 'Y'
), $booking_arr);

 ?>


<?php 
cyber_source_make_payment($total_price, $auth);
 ?>



<?php 
// cyber_source_make_payment($total_price);
 ?>


















<?php 

$action_target = "payment-confirm.php";

 ?>
<?php 

function cyber_source_make_payment($amount, $auth)
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
    if ($is_pay_by_cash) {
        echo "../SecureAcceptanceCheckout/";
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
<input type="hidden" name="amount" value="<?php echo "$amount"; ?>.00" readonly>
<input type="hidden" name="reference_number" value="<?php echo $auth; ?>" readonly>
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
" value="確認並支付Confirm and Pay">
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
exit();
 ?>











































<article class="card">
</article> <!-- card.// -->


	</aside> <!-- col.// -->
	<aside class="col-sm-6">
<!-- <p>Paymetn form2</p> -->

<article class="card">





<?php 

function self_make_payment()
{
	
 ?>
<div class="card-body p-5">










    <script>
        // Function to hide all divs
        function hideAllDivs() {
            // const divs = document.querySelectorAll('p'); // Select all div elements
            // divs.forEach(div => {
            //     div.style.display = 'none'; // Hide each div
            // });


				  const boxes = document.getElementsByClassName('tab-content');

				  for (const box of boxes) {
				    // 👇️ hides element
				    box.style.display = 'none';

				    // 👇️ removes element from DOM
				    // box.style.display = 'none';
				  }
        }

        // Function to show a specific div by its ID
        function showDivById(divId) {
        		hideAllDivs()
            const targetDiv = document.getElementById(divId);
            if (targetDiv) {
                targetDiv.style.display = 'block'; // Show the specified div
            } else {
                console.error(`Div with ID "${divId}" not found.`);
            }
        }
    </script>
<!--     <button onclick="hideAllDivs()">Hide All Divs</button>
    <button onclick="showDivById('p1')">Show Div 1</button>
    <button onclick="showDivById('p2')">Show Div 2</button>

    <p id="p1">This is Div 1</p>
    <p id="p2" style="display: none;">This is Div 2</p>

 -->










<style type="text/css">
	.payment-icon {
		width: 80px;
	}
	.nav-link {
		margin: 10px;
	}
</style>







<ul class="nav bg-light nav-pills rounded nav-fill mb-3" role="tablist">
	<li class="nav-item">

		<a class="nav-link active" href="#nav-tab-card" onclick="showDivById('Visa')" >
		<i class="fa fa-credit-card"></i> <img src="1700086610327.png" class="payment-icon"> <br> Visa</a></li>
	<li class="nav-item">
		<a class="nav-link active" href="#nav-tab-card" onclick="showDivById('Mastercard')" >
		<i class="fa fa-credit-card"></i> <img src="1700086608399.png" class="payment-icon"> <br> Mastercard</a></li>
	<li class="nav-item">
		<a class="nav-link active" href="#nav-tab-card"  onclick="showDivById('JBC')" >
		<i class="fa fa-credit-card"></i> <img src="1700086610054.png" class="payment-icon"> <br> JCB</a></li>
	<li class="nav-item">
		<a class="nav-link active" href="#nav-tab-bank"  onclick="showDivById('AmericanExpress')" >
		<i class="fa fa-university"></i> <img src="1700086654963.png" class="payment-icon"> <br> American Express</a></li>
	<li class="nav-item">
		<a class="nav-link active" href="#nav-tab-card"  onclick="showDivById('UnionPay')" >
		<i class="fa fa-credit-card"></i>  <img src="1700086600881.png" class="payment-icon"> <br> UnionPay</a></li>
</ul>

<div class="tab-content" id="UnionPay" style="display: none;">
<img src="1700086600881.png" class="payment-icon"> 
	<div class="tab-pane fade show active" id="nav-tab-card">
		<form role="form" action="<?php echo $action_target; ?>">
			<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<div class="form-group">
			<label for="username">Full name (on the card)</label>
			<input type="text" class="form-control" name="username" placeholder="" required="">
		</div> <!-- form-group.// -->

		<div class="form-group">
			<label for="cardNumber">Card number</label>
			<div class="input-group">
				<input type="text" class="form-control" name="cardNumber" placeholder="">
				<div class="input-group-append">
					<span class="input-group-text text-muted">
						<i class="fab fa-cc-visa"></i>   <i class="fab fa-cc-amex"></i>   
						<i class="fab fa-cc-mastercard"></i> 
					</span>
				</div>
			</div>
		</div> <!-- form-group.// -->

		<div class="row">
		    <div class="col-sm-8">
		        <div class="form-group">
		            <label><span class="hidden-xs">Expiration</span> </label>
		        	<div class="input-group">
		        		<input type="number" class="form-control" placeholder="MM" name="">
			            <input type="number" class="form-control" placeholder="YY" name="">
		        	</div>
		        </div>
		    </div>
		    <div class="col-sm-4">
		        <div class="form-group">
		            <label data-toggle="tooltip" title="" data-original-title="3 digits code on back side of the card">CVV <i class="fa fa-question-circle"></i></label>
		            <input type="number" class="form-control" required="">
		        </div> <!-- form-group.// -->
		    </div>
		</div> <!-- row.// -->
		<input type="submit" class="subscribe btn btn-primary btn-block" value="Confirm">
	</form>
	</div> <!-- tab-pane.// -->
</div> <!-- tab-content .// -->



<div class="tab-content" id="Mastercard">
<img src="1700086608399.png" class="payment-icon">
	<div class="tab-pane fade show active" id="nav-tab-card">
		<form role="form" action="<?php echo $action_target; ?>">
			<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<div class="form-group">
			<label for="username">Full name (on the card)</label>
			<input type="text" class="form-control" name="username" placeholder="" required="">
		</div> <!-- form-group.// -->

		<div class="form-group">
			<label for="cardNumber">Card number</label>
			<div class="input-group">
				<input type="text" class="form-control" name="cardNumber" placeholder="">
				<div class="input-group-append">
					<span class="input-group-text text-muted">
						<i class="fab fa-cc-visa"></i>   <i class="fab fa-cc-amex"></i>   
						<i class="fab fa-cc-mastercard"></i> 
					</span>
				</div>
			</div>
		</div> <!-- form-group.// -->

		<div class="row">
		    <div class="col-sm-8">
		        <div class="form-group">
		            <label><span class="hidden-xs">Expiration</span> </label>
		        	<div class="input-group">
		        		<input type="number" class="form-control" placeholder="MM" name="">
			            <input type="number" class="form-control" placeholder="YY" name="">
		        	</div>
		        </div>
		    </div>
		    <div class="col-sm-4">
		        <div class="form-group">
		            <label data-toggle="tooltip" title="" data-original-title="3 digits code on back side of the card">CVV <i class="fa fa-question-circle"></i></label>
		            <input type="number" class="form-control" required="">
		        </div> <!-- form-group.// -->
		    </div>
		</div> <!-- row.// -->
		<input type="submit" class="subscribe btn btn-primary btn-block" value="Confirm">
	</form>
	</div> <!-- tab-pane.// -->
</div> <!-- tab-content .// -->




<div class="tab-content" id="AmericanExpress" style="display: none;">
	<img src="1700086654963.png" class="payment-icon">
	<div class="tab-pane fade show active" id="nav-tab-card">
		<form role="form" action="<?php echo $action_target; ?>">
			<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<div class="form-group">
			<label for="username"> - Full name (on the card)</label>
			<input type="text" class="form-control" name="username" placeholder="" required="">
		</div> <!-- form-group.// -->
		<div class="form-group">
			<label for="cardNumber">Card number</label>
			<div class="input-group">
				<input type="text" class="form-control" name="cardNumber" placeholder="">
				<div class="input-group-append">
					<span class="input-group-text text-muted">
						<i class="fab fa-cc-visa"></i>   <i class="fab fa-cc-amex"></i>   
						<i class="fab fa-cc-mastercard"></i> 
					</span>
				</div>
			</div>
		</div> <!-- form-group.// -->
		<div class="row">
		    <div class="col-sm-8">
		        <div class="form-group">
		            <label><span class="hidden-xs">Expiration</span> </label>
		        	<div class="input-group">
		        		<input type="number" class="form-control" placeholder="MM" name="">
			            <input type="number" class="form-control" placeholder="YY" name="">
		        	</div>
		        </div>
		    </div>
		    <div class="col-sm-4">
		        <div class="form-group">
		            <label data-toggle="tooltip" title="" data-original-title="3 digits code on back side of the card">CVV <i class="fa fa-question-circle"></i></label>
		            <input type="number" class="form-control" required="">
		        </div> <!-- form-group.// -->
		    </div>
		</div> <!-- row.// -->
		<input type="submit" class="subscribe btn btn-primary btn-block" value="Confirm">
	</form>
	</div> <!-- tab-pane.// -->
</div>

<div class="tab-content" id="Visa" style="display: none;">
	<img src="1700086610327.png" class="payment-icon">
	<div class="tab-pane fade show active" id="nav-tab-card">
		<form role="form" action="<?php echo $action_target; ?>">
			<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<div class="form-group">
			<label for="username"> - Full name (on the card)</label>
			<input type="text" class="form-control" name="username" placeholder="" required="">
		</div> <!-- form-group.// -->
		<div class="form-group">
			<label for="cardNumber">Card number</label>
			<div class="input-group">
				<input type="text" class="form-control" name="cardNumber" placeholder="">
				<div class="input-group-append">
					<span class="input-group-text text-muted">
						<i class="fab fa-cc-visa"></i>   <i class="fab fa-cc-amex"></i>   
						<i class="fab fa-cc-mastercard"></i> 
					</span>
				</div>
			</div>
		</div> <!-- form-group.// -->
		<div class="row">
		    <div class="col-sm-8">
		        <div class="form-group">
		            <label><span class="hidden-xs">Expiration</span> </label>
		        	<div class="input-group">
		        		<input type="number" class="form-control" placeholder="MM" name="">
			            <input type="number" class="form-control" placeholder="YY" name="">
		        	</div>
		        </div>
		    </div>
		    <div class="col-sm-4">
		        <div class="form-group">
		            <label data-toggle="tooltip" title="" data-original-title="3 digits code on back side of the card">CVV <i class="fa fa-question-circle"></i></label>
		            <input type="number" class="form-control" required="">
		        </div> <!-- form-group.// -->
		    </div>
		</div> <!-- row.// -->
		<input type="submit" class="subscribe btn btn-primary btn-block" value="Confirm">
	</form>
	</div> <!-- tab-pane.// -->
</div>


<div class="tab-content" id="JBC" style="display: none;">
	<img src="1700086610054.png" class="payment-icon">
	<div class="tab-pane fade show active" id="nav-tab-card">
		<form role="form" action="<?php echo $action_target; ?>">
			<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<div class="form-group">
			<label for="username"> - Full name (on the card)</label>
			<input type="text" class="form-control" name="username" placeholder="" required="">
		</div> <!-- form-group.// -->
		<div class="form-group">
			<label for="cardNumber">Card number</label>
			<div class="input-group">
				<input type="text" class="form-control" name="cardNumber" placeholder="">
				<div class="input-group-append">
					<span class="input-group-text text-muted">
						<i class="fab fa-cc-visa"></i>   <i class="fab fa-cc-amex"></i>   
						<i class="fab fa-cc-mastercard"></i> 
					</span>
				</div>
			</div>
		</div> <!-- form-group.// -->
		<div class="row">
		    <div class="col-sm-8">
		        <div class="form-group">
		            <label><span class="hidden-xs">Expiration</span> </label>
		        	<div class="input-group">
		        		<input type="number" class="form-control" placeholder="MM" name="">
			            <input type="number" class="form-control" placeholder="YY" name="">
		        	</div>
		        </div>
		    </div>
		    <div class="col-sm-4">
		        <div class="form-group">
		            <label data-toggle="tooltip" title="" data-original-title="3 digits code on back side of the card">CVV <i class="fa fa-question-circle"></i></label>
		            <input type="number" class="form-control" required="">
		        </div> <!-- form-group.// -->
		    </div>
		</div> <!-- row.// -->
		<input type="submit" class="subscribe btn btn-primary btn-block" value="Confirm">
	</form>
	</div> <!-- tab-pane.// -->
</div>



















</div> <!-- card-body.// -->

<?php 

}

 ?>



</article> <!-- card.// -->


	</aside> <!-- col.// -->
</div> <!-- row.// -->

</div> 
<!--container end.//-->

<br><br>

