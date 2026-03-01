<?php 

require_once '../logger.php';

date_default_timezone_set('Asia/Hong_Kong');

error_reporting(E_ALL);
ini_set('display_errors', '1');


session_start();

if(isset($_SESSION["paying_session"])) {
	$_POST['req_reference_number'] = $_SESSION["paying_session"];
	$_POST['decision'] = 'ACCEPT';
	unset($_SESSION["paying_session"]);
}

$is_management = isset($_SESSION["management"]);

$allGetParams = array_merge($_GET, $_POST);

$is_pay_by_cash = isset($allGetParams['cash'])
 && $is_management
 ;


require_once '../tesing_stage_verification.php';




require_once '../account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create connection
$cp_conn = new mysqli($servername, $username, $password, $cp_dbname);

// Check connection
if ($cp_conn->connect_error) {
    die("Connection failed: " . $cp_conn->connect_error);
}

 ?>
	<meta charset="utf-8">
<?php 

if (isset($_GET['auth'])) {
	// Get all GET parameters
	$getParameters = $_GET;

	// Escape values for security (prevent XSS)
	foreach ($getParameters as $key => $value) {
	  $getParameters[$key] = htmlspecialchars($value);
	}
?>
	<!DOCTYPE html>
	<html>
	<body>
	  <form id="proceed_form" action="./payment-confirm.php" method="post">

	      <input type="hidden" name="req_reference_number" value="<?php echo $_GET['auth']; ?>">
	    <?php foreach ($getParameters as $key => $value) : ?>
	      <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>">
	    <?php endforeach; ?>
	    <button type="submit" style="display: none;">Submit Data</button>
	  </form>
	</body>
	</html>
<script> document.getElementById('proceed_form').submit();</script>
<?php
	die();
} ?>


<?php 
if (
	//!isset($_GET['auth'])
	!isset($_POST['req_reference_number'])
	|| !isset($_POST['decision'])

) {
	// var_dump($_POST);

	
	// echo $_POST['auth_response'];
	m_log(
		"reach payment-confirm.php 您使用本網站的方式不恰當 "
		.json_encode($_POST)." "
		.json_encode($_GET)
	);
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當\nThe way you are using this website is inappropriate');
	    setTimeout(function() {
	        window.location.href = "../";
	    }, 2000);
    </script>
    <?php
    die();
}

$data = $_POST;
$have_insert_payment = false;

require_once '../lib_complete_payment.php';

if (isset($data['decision'])) {
	if (insert_payment_record($conn, $data)) {
	 	$have_insert_payment = true;
	}
}

$auth = $_POST['req_reference_number'];
$decision = $_POST['decision'];

m_log("reach payment-confirm.php $auth");

// require_once '../booking-status-json-variable.php';

$booking_non_expired = false;

$sql = "SELECT * FROM `golf_fairway_booking` WHERE `auth`='$auth';";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$booking_non_expired = true;
}


$bank_accept = false;

require_once '../cybersource_api/search.php';
?>
<!-- <?php top_up_cybersource($conn, $auth); ?> -->
<?php


$sql = "SELECT * FROM `golf_cybersource` 
WHERE `req_reference_number`='$auth'
	and decision='ACCEPT'
    and `req_transaction_type`='sale'
    and transaction_id<>''
    and `golf_cybersource`.auth_amount > 0
;";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	if ($row['decision']=='ACCEPT') {
		m_log("reach payment-confirm.php BANK ACCEPT $auth");
		$bank_accept = true;
	}
}






$is_syn = false;

$sql = "SELECT `T_BOOK`.`pay_amount` FROM `T_BOOK` WHERE `T_BOOK`.`qr_code`='$auth';";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$pay_amount = (int) $row["pay_amount"];
	if ($pay_amount > 0) {
		$is_syn = true;
	}
	// echo "<small>Syn pay_amount=$pay_amount</small> <br>";
}


if ( $is_pay_by_cash && isset($data['amount']) && isset($data['addition']) && isset($data['percentage']) ) {
	$data['card_type_name'] = 'cash';
	
	// $date = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('UTC'));
    // $date->setTimezone(new DateTimeZone('Asia/Shanghai')); // UTC+8
    // $signed_date_time = $date->format('Y-m-d H:i:s');

    $signed_date_time = date('Y-m-d H:i:s');
	$data['signed_date_time'] = $signed_date_time;

	$amount = floatval($data['amount']);

	$addition = floatval($data['addition']);
	$percentage = floatval($data['percentage']);

	$auth_amount = $amount;
	// ( $amount * ($percentage/100) ) + $addition;
	$data['req_amount'] = $auth_amount;
	$data['auth_amount'] = $auth_amount;

		$sql = "
		INSERT INTO `golf-cash`(
			`auth`, 
			`amount`, 
			`multiplied`, 
			`extra`,
			`timestamp`
		) VALUES (
			'$auth',
			'$auth_amount',
			'$percentage',
			'$addition',
			'$signed_date_time'
		);
		";

	if (!isset($_POST['download'])) {

	try {

		// Execute the statement
		if ($conn->query($sql)) {
			$have_insert_payment = true;
			m_log("reach payment-confirm.php CASH PAYMENT $auth");
		    // echo "New records created successfully";
		} else {
		    echo "Error: ";
			m_log("reach payment-confirm.php CASH PAYMENT ERROR $auth");
		}

	}

	//catch exception
	catch(Exception $e) {
	  // echo 'Message: ' .$e->getMessage();
	  // echo $sql;
	}

	}
} else {


}

$is_account_unpaid = false;
$unpaid_amount = 0;
// $unpaid_is_paid = 0;

$sql = " SELECT `amount`,`is_paid` FROM `golf-unpaid-account` where `auth`='$auth' limit 1; ";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$unpaid_amount = ((float) $row['amount']);
	// $unpaid_is_paid = ((int) $row['is_paid']);
	// var_dump($row);
	// echo $real_payment_price;
	$is_account_unpaid = true;
}


if ($is_account_unpaid) {
	m_log("reach payment-confirm.php UNPAID PAID $auth");

	$remark = '';
	$req_reference_number = $auth;

	if (isset($data['remark']) && isset($data['req_reference_number'])) {
		$remark = $data['remark'];
		$req_reference_number = $data['req_reference_number'];

		$sql = "INSERT INTO `golf_remark`(`auth`, `remark`) VALUES ('$req_reference_number',
			(
				select `golf-unpaid-account`.`remark` 
				from `golf-unpaid-account` 
				where `golf-unpaid-account`.`auth`='$req_reference_number'
			)
		);";

		try {
			// Execute the statement
			if ($conn->query($sql)) {
			    // echo "New records created successfully";
			} else {
			    echo "Error: ";
			}
		} catch(Exception $e) {
		  // echo 'Message: ' .$e->getMessage();
		  // echo $sql;
		}
	}


	$sql = "UPDATE `golf-unpaid-account` SET `remark`='$remark',`is_paid`='1' WHERE `auth`='$req_reference_number';";

	try {
		// Execute the statement
		if ($conn->query($sql)) {
			$have_insert_payment = true;
		    // echo "New records created successfully";
		} else {
		    echo "Error: ";
		}
	} catch(Exception $e) {
	  // echo 'Message: ' .$e->getMessage();
	  // echo $sql;
	}

	if (isset($_SESSION['name'])&&isset($_SESSION['name2'])) {
		$admin_name = $_SESSION['name'];
		$sql = "UPDATE `golf_fairway_booking` SET `src`='$admin_name' WHERE `auth`='$req_reference_number';";

		try {
			// Execute the statement
			if ($conn->query($sql)) {
			} else {
			    echo "Error: ";
			}
		} catch(Exception $e) {
		  // echo 'Message: ' .$e->getMessage();
		  // echo $sql;
		}
	}
}

$is_reload = false;

// if (isset($booking_arr_buf['name'])
// && isset($booking_arr_buf['id'])
// && isset($booking_arr_buf['booking_date'])
// && isset($data['card_type_name'])
// && isset($data['signed_date_time'])
// && isset($data['auth_amount'])
// && isset($data['req_reference_number'])
// && isset($booking_arr_buf['octopus_no'])
// && isset($booking_arr_buf['email'])) {


//     $date = new DateTime($data['signed_date_time'], new DateTimeZone('UTC'));
//     $date->setTimezone(new DateTimeZone('Asia/Shanghai')); // UTC+8
//     $signed_date_time = $date->format('Y-m-d H:i:s');

// 	// Prepare the INSERT statement
// 	$sql = "


// INSERT INTO `T_BOOK`(
// 	`car_park_id`,
// 	`member_id`,
// 	`reference`,
// 	`order_time`,
// 	`order_source`,
// 	`pos_no`,
// 	`date_begin`,
// 	`date_end`,
// 	`pay_type`,
// 	`pay_time`,
// 	`pay_amount`,
// 	`qr_code`,
// 	`card_no`,
// 	`email`,
// 	`invalid`
// ) VALUES (
// 	'$car_park_id',
// 	'".$booking_arr_buf['name']."',
// 	'".$booking_arr_buf['id']."',
// 	'".$booking_arr_buf['timestamp']."',
// 	'web',
// 	'".$p_selections."',
// 	'".$booking_arr_buf['booking_date']." ".$begin_hour."',
// 	'".$booking_arr_buf['booking_date']." ".$end_hour."',
// 	'".$data['card_type_name']."',
// 	'".$signed_date_time."',
// 	'".$data['auth_amount']."',
// 	'".$data['req_reference_number']."',
// 	'".$booking_arr_buf['octopus_no']."',
// 	'".$booking_arr_buf['email']."',
// 	'0'
// );


// ";

// 	try {

// 		// Execute the statement
// 		if ($cp_conn->query($sql)) {
// 		    // echo "New records created successfully";
// 		} else {
// 		    // echo "Error: ";
// 		    $is_reload = true;
// 		}

// 	} catch(Exception $e) {
// 	  // echo 'Message: ' .$e->getMessage();
// 		    $is_reload = true;
// 	}


// }









///////////////////////////////////////////////////////////////////////////////////
// Insert everything before this line
///////////////////////////////////////////////////////////////////////////////////


$is_pay_by_cash = false;
$is_cash = false;
$cash_amount = 0;

$sql = " SELECT `amount`, `multiplied`, `extra` FROM `golf-cash` where `auth`='$auth' limit 1; ";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
	$cash_amount = ((float) $row['amount']);
	// $cash_amount = ( ((float) $row['amount']) * ( ((float) $row['multiplied']) / 100 ) ) + ((float) $row['extra']);
	// var_dump($row);
	// echo $real_payment_price;

	$is_cash = true;
	$is_pay_by_cash = true;
}

$is_pay_by_credit_card = (!$is_pay_by_cash) && (!$is_account_unpaid);













// echo "<small>($bank_accept) ($decision) ($is_pay_by_credit_card) ($is_pay_by_cash) ($is_account_unpaid)</small>";

if ($is_pay_by_credit_card && ( $decision != 'ACCEPT' || !$bank_accept) ) {
	m_log("reach payment-confirm.php 您的付款被銀行付款程序拒絕，請重新處理。 $auth ".json_encode($_POST));
 ?>
<script type="text/javascript">
	alert('您的付款被銀行付款程序拒絕，請重新處理。 Your payment was refused by the bank\'s payment procedure, please process again.');
    setTimeout(function() {
        window.location.href = "../";
    }, 2000);
</script>
<?php
	if (!isset($_POST['download'])) {
 ?>
<?php
		$json = json_encode($_POST);
		// echo "$json";
		// file_put_contents($auth.'.json', $json);
	}
	echo "decision: $decision <br>";
	echo "bank_accept: $bank_accept <br>";
	echo "Bank Refused";
	die();
}




if (!$booking_non_expired) {
	m_log("reach payment-confirm.php 由於15分鐘已過，預訂已過期，如果您確定已付款，請申請退款。 $auth");
	?>
	<script type="text/javascript">
		alert('由於15分鐘已過，預訂已過期，如果您確定已付款，請申請退款。 Because 15 minutes are past,The booking expired, request the refund if you sure that you paid.');
	    setTimeout(function() {
	        window.location.href = "../";
	    }, 2000);
	</script>
	<?php
		// $json = json_encode($_POST);
		// echo "$json";
		// file_put_contents($auth.'.json', $json);
	die();
}






?>

<!DOCTYPE html>
<html>
<head>
     <meta charset="utf-8">
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

<style type="text/css">
	html {
		background-image: linear-gradient(to right top, #d16ba5, #c777b9, #ba83ca, #aa8fd8, #9a9ae1, #8aa7ec, #79b3f4, #69bff8, #52cffe, #41dfff, #46eefa, #5ffbf1);
		padding: 30px;
	}
	body {
		background-color: white;
		padding: 50px;
		font-size: 2em;
		box-shadow: 4px 4px grey;
	}
	td {
		vertical-align: text-top;
	}



@media only screen and (max-width: 1000px) {
	h1 {
		font-size: 1.5em;
	}
	body {
		font-size: 3em;
	}

}	
</style>
<a href="../">返回首頁 Return to home page</a>


<?php

// echo "point 1";

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// echo "point 2";



// echo "point 3";

function booking_record_sql($table_name,$auth)
{
	$sql = "
	SELECT 
		`$table_name`.`id`, 
		`$table_name`.`name`, 
		`$table_name`.`octopus_no`, 
		`$table_name`.`check_digit`, 
		`$table_name`.`booking_date`, 
		`$table_name`.`begin_hour`, 
		`$table_name`.`end_hour`, 
		`$table_name`.`p_selections`, 
		`$table_name`.`email`,
		`$table_name`.`timestamp` , 
		`$table_name`.`discount` , 
		`$table_name`.`auth`,
		`$table_name`.`telephone`,

		`golf_cybersource`.req_amount credit_card_req_amount,
		`golf_cybersource`.auth_amount credit_card_amount,
		`golf_cybersource`.`req_currency` req_currency,
		
		IFNULL(`golf-unpaid-account`.`is_paid`,-1) unpaid_is_paid,

		( 
			IFNULL(`golf-cash`.`amount`,0) 
			+ IFNULL(`golf_cybersource`.auth_amount,0) 
			+ IFNULL(`golf-unpaid-account`.amount,0) 
		) payment_amount,
		`golf-cash`.`amount` cash_amount,
		`golf-unpaid-account`.`amount` unpaid_amount,

		`golf-cash`.`currency` cash_currency,
		`golf-unpaid-account`.`currency` unpaid_currency,

		( IFNULL(`golf-cash`.`multiplied`,0) + IFNULL(`golf-unpaid-account`.`multiplied`,0) ) `multiplied`, 
		( IFNULL(`golf-cash`.`extra`,0) + IFNULL(`golf-unpaid-account`.`extra`,0) ) `extra`

	FROM `$table_name`

	left join `golf_cybersource` on `golf_cybersource`.`req_reference_number`=`$table_name`.`auth`
	left join `golf-cash` on `golf-cash`.`auth`=`$table_name`.`auth`
	left join `golf-unpaid-account` on `golf-unpaid-account`.`auth`=`$table_name`.`auth`

	WHERE `$table_name`.`auth`='$auth'

	; ";

	return $sql;
}

require_once '../price-calculation.php';

// try {


	$p_selections = '';
	$booking_arr_buf = null;
	$begin_hour = "";
	$end_hour = "";

	$sql = booking_record_sql('golf_fairway_booking',$auth);
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    while ($booking_arr = $result->fetch_assoc()) {
	    	$booking_arr_buf = $booking_arr;
	    }
	} else {

		$sql = booking_record_sql('golf_fairway_booking_history',$auth);
		$result_1 = $conn->query($sql);

		if ($result_1->num_rows > 0) {
		    while ($booking_arr_1 = $result_1->fetch_assoc()) {
		    	$booking_arr_buf = $booking_arr_1;
		    }
		}


		$total_price = price_calculation( array(
		    'lan' => 'zn',
		    'print' => 'Y'
		), $booking_arr_buf);

		echo "客戶欠款 $ $total_price Client Owe $ $total_price";
		// echo $sql;
		die();
	}


$credit_card_amount = $booking_arr_buf['credit_card_amount'];

$unpaid_is_paid = $booking_arr_buf['unpaid_is_paid'];

if ($unpaid_is_paid==1) {
	$is_account_unpaid = true;
}







$p_selections = str_replace( array('[', '"', ']', ' '), '', $booking_arr_buf['p_selections'] );



if (strpos($booking_arr_buf['begin_hour'], '.5') !== false) {
    $begin_hour = str_replace('.5', ':30', $booking_arr_buf['begin_hour']);
} else {
    $begin_hour = $booking_arr_buf['begin_hour'].':00';
}
if (strpos($booking_arr_buf['end_hour'], '.5') !== false) {
    $end_hour = str_replace('.5', ':30', $booking_arr_buf['end_hour']);
} else {
    $end_hour = $booking_arr_buf['end_hour'].':00';
}







$payment_amount = $booking_arr_buf['payment_amount'];


// // Construct the SQL query
// $sql = "DELETE FROM `golf-payment-session` where `auth`='$auth';";
// try {
//     if ($conn->query($sql) === TRUE) {
//     }
// } catch (Exception $e) {
    
// }

if (!$is_account_unpaid && !$is_pay_by_cash && !$bank_accept) {
	
}

// Construct the SQL query
$sql = "INSERT INTO `golf-payment-session`(`auth`, `price`, `payment-datetime`) 
	VALUES ('$auth','$payment_amount',CURRENT_TIMESTAMP);";
try {
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
    	$have_insert_payment = true;
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
    }

} catch (Exception $e) {
    
}

// Construct the SQL query
$sql = "UPDATE `golf-payment-session` SET `payment-datetime`=CURRENT_TIMESTAMP WHERE `auth`='$auth';";
try {
    
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "<h1>付款收據 <br> Payment receipt</h1>";
    } else {
        // echo "Error: " . $sql . "<br>" . $conn->error;
    }

} catch (Exception $e) {
    
}

$is_download = isset($_POST['download']);

$is_resend = isset($_POST['resend']);

$is_payment_initialize = ( ( (!$is_download && !$is_reload) || $is_resend ) && $booking_non_expired );



if ($is_payment_initialize) {
	$initialize_report = "<small style=\"font-size: 0.3em\">(Payment initialize:".($is_payment_initialize?'T':'F').") (Download:".($is_download?'T':'F').") (Reload:".($is_reload?'T':'F').") (Is Resend:".($is_resend?'T':'F').") </small>";
	mail_payment_record_by_auth($auth, $initialize_report);
}





















			 ?>

<script type="text/javascript">


    function addBorderToBase64Image(base64Image, borderWidth, borderColor, callback) {
        // Create an image element
        const img = new Image();
        img.src = base64Image;

        img.onload = function() {
            // Create a canvas element
            const canvas = document.getElementById('canvas');
            const ctx = canvas.getContext('2d');

            // Set canvas dimensions
            const width = img.width + 2 * borderWidth;
            const height = img.height + 2 * borderWidth;
            canvas.width = width;
            canvas.height = height;

            // Draw the border
            ctx.fillStyle = borderColor;
            ctx.fillRect(0, 0, width, height);

            // Draw the image on top of the border
            ctx.drawImage(img, borderWidth, borderWidth);

            // Get the new Base64 image
            const newBase64Image = canvas.toDataURL();

            callback(newBase64Image);
            // Display the new image
            // document.getElementById('borderedImage').src = newBase64Image;
        }
    }

	function download_image() {
		const collection = document.getElementsByTagName("img");
		var base64_str = collection[0].src;

		if (true) {
			addBorderToBase64Image(base64_str, 30, 'white', function (newBase64Image) {
				addBorderToBase64Image(newBase64Image, 30, 'black', function (newBase64Image2) {
					var a = document.createElement("a"); //Create <a>
					// a.href = base64_str;
					a.href = newBase64Image2;
					
					// "data:image/png;base64," + ImageBase64; //Image Base64 Goes here
					a.download = "QRCodePass.png"; //File name Here
					a.click(); //Downloaded file
				});
			});
		} else {

			var a = document.createElement("a"); //Create <a>
			a.href = base64_str;
			// a.href = newBase64Image;
			
			// "data:image/png;base64," + ImageBase64; //Image Base64 Goes here
			a.download = "QRCodePass.png"; //File name Here
			a.click(); //Downloaded file
		}



	}

</script>
			<p style="color: grey;">
<?php 	

if (!$is_management) {

 ?>
					請於到場後出示以下QR碼登記簽到，謝謝！
					<br>
					Please show the QR code below for check-in. Thank you!
<?php 	


} else {
	// echo "這個二維碼是為堅持要自己掃描二維碼的顧客準備的。<br>This QR code is for customers who insist on scanning the QR code themselves.";
}


 ?>
			</p>
<table>
	<tr>	
		<td>
<div style="text-align: center;padding: 1em;">

<div id="qrcode"></div>


<br>
<a onclick="download_image()" 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
"
>
	二維碼下載 QR code Download
</a>
<br>

</div>

			
		</td>
		<td>
			
<script type="text/javascript">



async function fetchData(url, callback) {
    try {
        const response = await fetch(url);
        if (response.ok) {
            // console.log(response);
            const json = await response.json();
            // Now you can use the 'json' variable containing your data
            // console.log(json);
            callback(json);
        } else {
            console.error('Error fetching data:', response.status);
        }
    } catch (error) {
        console.error('An error occurred:', error);
    }
}

const apiUrl = '../booking-status-json-variable.php?future_booking'; // Replace with your API endpoint
function reflesh() {
    fetchData(apiUrl, function (data) {
        // jsonData = data;
    });
}
reflesh();

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
  unpaid_amount,
  unpaid_paid,
  amount,
  cash,
  from_button
) {
	console.log(
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
  unpaid_amount,
  unpaid_paid,
  amount,
  cash,
  from_button
  );
	setTimeout(function () {
		var printout_1 = comfirm_and_print_o(
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
  			unpaid_amount,
  			unpaid_paid,
			amount,
			cash,
			'Staff',
  			from_button
		);
		var printout_2 = comfirm_and_print_o(
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
  			unpaid_amount,
  			unpaid_paid,
			amount,
			cash,
			'Customer',
  			from_button
		);

		var printing = `

  <style type="text/css">
    @media print {
      /* Avoid breaking inside elements */
      .no-break {
        page-break-inside: avoid;
      }

      /* Force a page break before or after specific elements */
      .page-break-before {
        page-break-before: always;
      }

      .page-break-after {
        page-break-after: always;
      }
    }
  </style>

		`
		+printout_1
		+`

  <div class="page-break-before"></div>

		`
		+printout_2
		+`

		`;

		const oIframe = document.getElementById('receipt_printing_buffer');
		oIframe.contentWindow.document.open();
		oIframe.contentWindow.document.write(printing);
		oIframe.contentWindow.document.close();
		<?php 
		    if ( $is_management && !isset($data['recurring']) ) {
		 ?>
		 console.log('To print');
				oIframe.contentWindow.print();
		<?php
		    } else {
		 ?>
		 if (from_button == 1) {
		 	console.log('To print');
			oIframe.contentWindow.print();
		 } else {
		 	console.log('Not to print');
		 }
		<?php
		    }
		 ?>


	}, 1000);

}


function transformCsvIfInPickleballRange(csv) {
	const normalizedCsv = String(csv).replace(/\s+/g, "");
	const parts = normalizedCsv.split(",");

  // Convert to numbers and ensure all are valid integers
  const nums = parts.map(v => Number(v));
  if (nums.some(n => !Number.isInteger(n))) {
    return null; // invalid input
  }

  // Check all are between 100 and 199 (inclusive)
  const allInRange = nums.every(n => n >= 100 && n <= 199);
  if (!allInRange) {
    return null; // condition not met
  }

  // Subtract 99 and return as new CSV
  return nums.map(n => n - 99).join(",");
}

// Example:
// console.log(transformCsvIfInRange("100,101,199")); // "1,2,100"
// console.log(transformCsvIfInRange("100,99,150"));  // null


function comfirm_and_print_o(
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
	unpaid_amount,
	unpaid_paid,
  amount,
  cash,
  target,
  from_button
) {

  var msg = '';
  var printing = '';
  <?php 
	  if (isset($_SESSION['name'])&&isset($_SESSION['name2'])) {
   ?>
  printing += '<i style="text-align: left;">On-Duty: <?php echo $_SESSION['name'].' - '.$_SESSION['name2']; ?> </i><br>';
  <?php 
	  }
   ?>
  
  var is_pickleball = false;
  sourceTxt = p_selections.replace(/,/g, ", ");
  if (sourceTxt.length>0) {
    msg += 'Booking:'+sourceTxt+'\n';
	var pickelball_csv = transformCsvIfInPickleballRange(sourceTxt);
	if (pickelball_csv) {
		is_pickleball = true;
		var printing_header = '';
		printing_header += '<h1>白石匹克球練習場</h1>';
		printing_header += '<div style="text-align: right;">Tel: 27771813</div>';
		printing_header += '<div style="text-align: right;">RIVERSIDE Whitehead Pickleball</div>';
		printing_header += '<i style="text-align: center;"><hr></i>';
		printing += printing_header;

		printing += '<b style="text-align: left;font-size: 1.8em;">Court: '+pickelball_csv+'</b><br>';
    	printing += 'System No.: '+sourceTxt+'<br>';
	} else {
		var printing_header = '';
		printing_header += '<h1>白石高球練習場</h1>';
		printing_header += '<div style="text-align: right;">Tel: 27771813</div>';
		printing_header += '<div style="text-align: right;">RIVERSIDE Whitehead Golf Club</div>';
		printing_header += '<i style="text-align: center;"><hr></i>';
		printing += printing_header;

    	printing += '<b style="text-align: left;font-size: 1.8em;">Bay: '+sourceTxt+'</b><br>';
	}
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
  if (sourceTxt.length>0) {
    msg += 'ID:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">ID: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = octopus_no;
  sourceTxt2 = check_digit;
  if ( sourceTxt.length>1 && sourceTxt2.length>1 && !is_pickleball) {
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
  if (sourceTxt.length>0 && !is_pickleball) {
    msg += 'Discount:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Discount: '+sourceTxt+'</i><br>';
  }
  

  sourceTxt = unpaid_amount;
  if (sourceTxt.length>0 && parseFloat(sourceTxt)>0) {
    msg += 'Credit Card Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Bank Transfer or Cheque</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;white-space: nowrap;">Amount: HKD $ '+sourceTxt+'</b><br>';

	  sourceTxt = unpaid_paid;
	  if (sourceTxt.length>0 && parseFloat(sourceTxt)>0) {
	    msg += 'Payment Status:'+' Paid '+'\n';
	    printing += '<i style="text-align: left;">Payment Status: Paid</i><br>';
	  } else {
	    msg += 'Payment Status:'+' Unpaid '+'\n';
	    printing += '<i style="text-align: left;">Payment Status: Unpaid</i><br>';
	  }
	  

  }


  sourceTxt = amount;
  if (sourceTxt.length>0 && parseFloat(sourceTxt)>0) {
    msg += 'Credit Card Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Credit Card</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;white-space: nowrap;">Amount: HKD $ '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = cash;
  if (sourceTxt.length>0 && parseFloat(sourceTxt)>0) {
    msg += 'Cash Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Cash</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;white-space: nowrap;">Amount: HKD $ '+sourceTxt+'</b><br>';
  }

  printing += '<i style="text-align: left;">For '+target+'</i><br>';
  return printing;

}	
</script>

<iframe id="receipt_printing_buffer" style="width: 300px;height: 400px;">
</iframe>

<a onclick="
	initial_print(1)
" 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
">
	列印收據 Print Receipt
</a>

<script type="text/javascript">
	console.log('end of page 2');
	function initial_print(from_button) {
		
	<?php 

function hour_num_to_hour_display($hour_num)
{
    $cursor_hour = ((float) $hour_num);
    $hour_int = ((int) $hour_num);
    $is_half_hour = $cursor_hour != $hour_int;
    $half_hour_mark = ($is_half_hour ? ':30' : ':00');
    return str_pad($hour_int, 2, "0", STR_PAD_LEFT) . $half_hour_mark;
}
            echo '   comfirm_and_print('
            .'\''.(isset($booking_arr_buf['auth']) ? $booking_arr_buf['auth'] : "").'\''
            .',\''.(isset($booking_arr_buf['id']) ? $booking_arr_buf['id'] : "").'\''
            .',\''.(isset($booking_arr_buf['name']) ? $booking_arr_buf['name'] : "").'\''
            .',\''.(isset($booking_arr_buf['telephone']) ? $booking_arr_buf['telephone'] : "").'\''
            .',\''.(isset($booking_arr_buf['octopus_no']) ? $booking_arr_buf['octopus_no'] : "").'\''
            .',\''.(isset($booking_arr_buf['check_digit']) ? $booking_arr_buf['check_digit'] : "").'\''
            .',\''.(isset($booking_arr_buf['booking_date']) ? $booking_arr_buf['booking_date'] : "").'\''
            .',\''.(isset($booking_arr_buf['begin_hour']) ? hour_num_to_hour_display($booking_arr_buf['begin_hour']) : "").'\''
            .',\''.(isset($booking_arr_buf['end_hour']) ? hour_num_to_hour_display($booking_arr_buf['end_hour']) : "").'\''
            .',\''.(isset($booking_arr_buf['p_selections']) ? 
              str_replace('[', '', 
              str_replace(']', '', 
              str_replace('"', '', 
                $booking_arr_buf['p_selections']
              )
              )
              )
               : "").'\''
            .',\''.(isset($booking_arr_buf['discount']) ? $booking_arr_buf['discount'] : "").'\''
            .',\''.(isset($data['auth_code']) ? $data['auth_code'] : "").'\''
            .',\''.(isset($data['req_card_number']) ? $data['req_card_number'] : "").'\''

            .',\''.($unpaid_amount).'\''
            .',\''.($unpaid_is_paid).'\''

            .',\''.($credit_card_amount).'\''
            .',\''.($cash_amount).'\''


            .',from_button); ';
	 ?>

	}
	initial_print(0);
	console.log('end of page');
</script>

		</td>
	</tr>
</table>


<script type="text/javascript" src="qrcode.min.js"></script>
<script type="text/javascript">
	
    const width = window.innerWidth;
    const height = window.innerHeight;

    side_length=0
    if (height > width) {
        side_length = width;
        console.log('use width '+side_length);
    } else {
        side_length = height;
        console.log('use height '+side_length);
    }
    side_length = side_length-150;
    side_length = side_length*0.6;

    
    new QRCode(document.getElementById("qrcode"), {
      text: "<?php echo $auth; ?>",
      width: side_length,
      height: side_length,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });

	<?php if (isset($_POST['download'])) {
		// echo "const myTimeout = setTimeout(download_image, 3000);";
	} ?>
</script>

















<?php
function transformCsvIfInPickleballRange($csv) {
    $normalizedCsv = preg_replace('/\s+/', '', (string)$csv);
    if ($normalizedCsv === '') {
        return null;
    }

    $parts = explode(',', $normalizedCsv);
    $result = [];

    foreach ($parts as $part) {
        if ($part === '' || !ctype_digit($part)) {
            return null; // invalid input
        }

        $num = (int)$part;
        if ($num < 100 || $num > 199) {
            return null; // out of required range
        }

        $result[] = (string)($num - 99);
    }

    return implode(',', $result);
}

// Examples:
// echo transformCsvIfInPickleballRange("100,101,199"); // 1,2,100
// var_dump(transformCsvIfInPickleballRange("100, 99,150")); // null
$pickleball_selections = transformCsvIfInPickleballRange($p_selections);

// echo ($pickleball_selections ? "匹克球場編號: $pickleball_selections" : $p_selections);

?>


			<p>尊敬的 <?php echo $booking_arr_buf['name']; ?>,</p>

			<p>感謝預訂<?php echo ($pickleball_selections ? "白石匹克球練習場" : "白石高球練習場"); ?>，閣下之預約資料如下：</p>
			<ul>
				<li>預約編號：<?php echo $booking_arr_buf['id']; ?></li>
				<li>日期：<?php echo $booking_arr_buf['booking_date']; ?> 
				<!-- <a href="?change=booking_date"><small>申請變更</small></a></li> -->
				<li>時間：<?php 

				if (strpos($booking_arr_buf['begin_hour'], '.5') !== false) {
				    echo str_replace('.5', ':30', $booking_arr_buf['begin_hour']);
				} else {
				    echo $booking_arr_buf['begin_hour'].':00';
				}

				?> - <?php 

				if (strpos($booking_arr_buf['end_hour'], '.5') !== false) {
				    echo str_replace('.5', ':30', $booking_arr_buf['end_hour']);
				} else {
				    echo $booking_arr_buf['end_hour'].':00';
				}

				?> 
				<!-- <a href="?change=hour"><small>申請變更</small></a> </li> -->
				<li>球場名稱：<?php echo ($pickleball_selections ? "白石匹克球練習場" : "白石高球練習場"); ?></li>
				<li><?php echo ($pickleball_selections ? "匹克球場編號: $pickleball_selections" : "球道號碼：$p_selections"); ?> </li>
				<!-- <a href="?change=p_selections"><small>申請變更</small></a> </li> -->
<?php 

					$credit_card_req_amount = $booking_arr_buf['credit_card_req_amount'];
					$is_credit_card = false;
					if (((float) $credit_card_amount) > 0) {
						$is_credit_card = true;
					}

 ?>
<?php
if ($pickleball_selections) {
	// echo "這是匹克球的預訂。沒有優惠和八達通資料。<br>This is a pickleball booking. No discount and octopus information.";
} else {
 ?>
         		<li>優惠 : <?php 
if ($booking_arr_buf['discount'] == 'S') {
    echo "學生";
} else if ($booking_arr_buf['discount'] == 'D') {
    echo "傷健人士";
} else if ($booking_arr_buf['discount'] == 'H') {
    echo "沒有優惠";
}
         		 ?> 
				 </li>
<?php
}
?>
         		 <!-- <a href="?change=discount"><small>申請變更</small></a> </li> -->

<?php 
if ($booking_arr_buf['octopus_no'] == null || $booking_arr_buf['octopus_no'] == '') {
    // echo "不需要 (不會開車駛入)";
} else {
         		 ?> 
          		<li>八達通卡 : 
				 <?php 
    echo $booking_arr_buf['octopus_no'].' ('.$booking_arr_buf['check_digit'].')';
         		 ?> 
				  </li>
				 <?php 
}
         		 ?> 
         		 <!-- <a href="?change=octopus_no"><small>申請變更</small></a>  -->


				<li>交易方式：<?php 
					if ($is_account_unpaid) {
						echo "銀行或支票轉帳";
					} else if ($is_credit_card) {
						echo '信用卡';
					} else if ($is_cash) {
						echo '現金';
					} else {
						echo '不明';
					}
				 ?></li>
				<li>交易金額：<?php 
					if ($is_account_unpaid) {
						// $booking_arr_buf['unpaid_amount'];
						$real_payment_price = $unpaid_amount;
						echo $booking_arr_buf['unpaid_currency'].' $'.$unpaid_amount
						.($unpaid_is_paid?'':' 未付')
						// .'.'
						;
					} else if ($is_credit_card) {
						$real_payment_price = $credit_card_amount;
						echo $booking_arr_buf['req_currency'].' $'.$credit_card_amount
						// .'..'
						;
					} else if ($is_cash) {
						$real_payment_price = $cash_amount;
						echo $booking_arr_buf['cash_currency'].' $'.$cash_amount
						// .'...'
						;
					} else {
						echo '不明';
					}
				 ?></li>
				 <?php 

$total_price = price_calculation( array(
    'lan' => 'zn',
    'print' => 'N'
), $booking_arr_buf);


					if ($is_credit_card || $is_account_unpaid) {
						if ($total_price > $real_payment_price) {
							echo "<li>原價 : 折扣自 $".$total_price."</li>";
						} else if ($total_price < $real_payment_price) {
							echo "<li>原價 : 加價自 $".$total_price."</li>";
						}
					}
				  ?>

				<!-- 其他預訂細節 -->
			</ul>

<?php 


$total_price = price_calculation( array(
    'lan' => 'zn',
    'print' => 'Y'
), $booking_arr_buf);

// if ($real_payment_price != $total_price) {
// 	echo '<b style="color: red;">付款後因價格變動而產生的價差。對於由此造成的不便，我們深表歉意。</b>';
// }
 ?>

			<hr>
			<p>Dear <?php echo $booking_arr_buf['name']; ?>,</p>

			<p>Thank you for booking Riverside Whitehead <?php echo ($pickleball_selections ? "Pickleball Court" : "Golf Court"); ?>. Here are your booking details:</p>

			<ul>
				<li>Date：<?php echo $booking_arr_buf['booking_date']; ?>
					<!-- <a href="?change=booking_date"><small>Apply for change</small></a> -->
				</li>
				<li>Time：<?php 

				if (strpos($booking_arr_buf['begin_hour'], '.5') !== false) {
				    echo str_replace('.5', ':30', $booking_arr_buf['begin_hour']);
				} else {
				    echo $booking_arr_buf['begin_hour'].':00';
				}

				?> - <?php 

				if (strpos($booking_arr_buf['end_hour'], '.5') !== false) {
				    echo str_replace('.5', ':30', $booking_arr_buf['end_hour']);
				} else {
				    echo $booking_arr_buf['end_hour'].':00';
				}

				?>
					<!-- <a href="?change=hour"><small>Apply for change</small></a> -->
				</li>
				<li>Location：Riverside Whitehead <?php echo ($pickleball_selections ? "Pickleball" : "Golf"); ?> Club</li>
				<!-- <li>Bay No.：<?php echo $p_selections; ?> <a href="?change=hour"><small>Apply for change</small></a> </li> -->
 
				<li><?php echo ($pickleball_selections ? "Pickleball Court No.: $pickleball_selections" : "Bay No.: $p_selections"); ?> </li>



<?php
if ($pickleball_selections) {
	// echo "這是匹克球的預訂。沒有優惠和八達通資料。<br>This is a pickleball booking. No discount and octopus information.";
} else {
 ?>
				<li>Discount : <?php 
					if ($booking_arr_buf['discount'] == 'S') {
						echo "Student";
					} else if ($booking_arr_buf['discount'] == 'D') {
						echo "Disabled";
					} else if ($booking_arr_buf['discount'] == 'H') {
						echo "None";
					}
				// ?> 
					<!-- <a href="?change=hour"><small>Apply for change</small></a>  -->
				</li>
<?php
}
?><?php 
					if ($booking_arr_buf['octopus_no'] == null || $booking_arr_buf['octopus_no'] == '') {
						// echo "No needed (Will not drive in)";
					} else {
						?><li>Octopus Card : <?php echo $booking_arr_buf['octopus_no']; ?></li><?php
					}
				// ?> 
<!-- <a href="?change=octopus_no"><small>Apply for change</small></a>  -->
				<li>Payment Method：<?php
					if ($is_account_unpaid) {
						echo "Bank Transfer or pay check";
					} else if ($is_credit_card) {
						echo 'Credit Card';
					} else if ($is_cash) {
						echo 'Cash';
					} else {
						echo 'Unknown';
					}
				 ?></li>
				<li>Transaction Amount：<?php 
					if ($is_account_unpaid) {
						$real_payment_price = $unpaid_amount;
						echo $booking_arr_buf['unpaid_currency'].' $'.$unpaid_amount
						.($unpaid_is_paid?'':' Unpaid');
					} else if ($is_credit_card) {
						$real_payment_price = $credit_card_amount;
						echo $booking_arr_buf['req_currency'].' $'.$credit_card_amount;
					} else if ($is_cash) {
						$real_payment_price = $cash_amount;
						echo $booking_arr_buf['cash_currency'].' $'.$cash_amount;
					} else {
						echo 'Unknown';
					}
				 ?></li>

				 <?php 
					if ($is_credit_card || $is_account_unpaid) {
						if ($total_price > $real_payment_price) {
							echo "<li>Original price : Discounted from $".$total_price."</li>";
						} else if ($total_price < $real_payment_price) {
							echo "<li>Original price : Markup from $".$total_price."</li>";
						}
					}
				  ?>

				<!-- 其他預訂細節 -->
			</ul>
<?php 	
	if ($have_insert_payment && !isset($_POST['recurring'])  && !isset($_GET['recurring']) ) {
 ?>
	<form id="recurring_form" action="?" method="get">
		<input type="hidden" name="auth" value="<?php echo $auth; ?>">
		<input type="hidden" name="decision" value="ACCEPT">
		<input type="hidden" name="download" value="true">
		<input type="hidden" name="recurring" value="true">
	</form>
 	<script type="text/javascript">
 		console.log('Have insert payment');
 		setTimeout(function () {
 			document.getElementById("recurring_form").submit();
 		},5000);
 	</script>
<?php
	} else {
 ?>
 	<script type="text/javascript">
 		console.log('Not insert payment');
 	</script>
<?php
	}
 ?>



<?php 

require_once '../price-calculation.php';

$total_price = price_calculation( array(
    'lan' => 'en',
    'print' => 'Y'
), $booking_arr_buf);

// if ($real_payment_price != $total_price) {
// 	echo '<b style="color: red;">The price difference due to price changes after payment. We apologize for any inconvenience caused.</b>';
// }
 ?>

			<?php 



	if (!isset($_POST['download']) && !$is_reload) {
	?>
	<script type="text/javascript">
		alert('您已經付款成功! Your payment is successful!');
	</script>
	<?php
	}


m_log("reach payment-confirm.php Show Invoicee SUCCESSFULLY $auth");
$conn->close();
 ?>
<hr>

<small style="color: grey;">Reference number: <?php echo "$auth"; ?></small>
<br>
<canvas id="canvas" style="display: none;"></canvas>