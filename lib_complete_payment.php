<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

$import_filepath__ = 'logger.php';
if (file_exists("./$import_filepath__")) {
    $import_filepath__ = "./$import_filepath__";
} else if (file_exists("../$import_filepath__")) {
    $import_filepath__ = "../$import_filepath__";
}
require_once $import_filepath__;


function mail_payment_record($booking_arr_buf,$mail_data)
{

    // $_POST['confirmation_code'];
    // Set these parameters
    $subject = '白石高爾夫球練習場 - 付款成功! | White Head Golf - Payment Confirmed'; // Subject of the email
    $emailadd = 'support@cpospay.com'; // Your email address (where the form information will be sent)
    $req = '0'; // Set to '1' to make all fields required, '0' to allow empty fields

    // Initialize variables
    $text = "親愛的高爾夫球場客戶，

感謝您支付我們設施的高爾夫球場預訂費用 ".$booking_arr_buf['req_currency']." $".$booking_arr_buf['payment_amount']."，我們衷心感謝您的惠顧。 
您可以在以下時間和地點透過以下身分驗證造訪我們的設施。


日期：".$booking_arr_buf['booking_date']."
時間：";

$text .= $mail_data['begin_hour'];
$text .= " - "; 
$text .= $mail_data['end_hour'];

$text .= "
球場名稱：白石高爾夫球練習場
打球位置：".$booking_arr_buf['p_selections']."

請下載此二維碼作為進入高爾夫球場的門卡
".$mail_data['full_url']."/GolfBooking/payment-page/payment-confirm.php?auth=".$mail_data['auth']."&decision=".$mail_data['decision']."&download=true


歡迎您來到我們的高爾夫練習場！

此致
白石高爾夫球場 團隊

____________________________________________________________________________________________________________________


Dear Golf Course Customers,

Thank you for paying ".$booking_arr_buf['req_currency']." $".$booking_arr_buf['payment_amount']." for your golf course reservation at our facility. We sincerely thank you for your business and look forward to providing you with an exceptional golf experience.
You can visit our facilities at the following times and locations with the following authentication.


Date：".$booking_arr_buf['booking_date']."
Time：";

$text .= $mail_data['begin_hour'];
$text .= " - "; 
$text .= $mail_data['end_hour'];

$text .= "
Golf Court：White Head Club Golf Driving Range
Spot：".$mail_data['p_selections']."


Please download this QR code as your keycard to enter the golf course
https://cpospay.com/GolfBooking/payment-page/payment-confirm.php?auth=".$mail_data['auth']."&decision=".$mail_data['decision']."&download=true



Welcome to our driving range!

Best Regards
White Head Golf


".$mail_data['initialize_report'];

// email-confirmation
// Send the email
	m_log("reach lib_complete_payment.php TRY SEND MAIL ".$mail_data['auth']);
	mail($booking_arr_buf['email'], $subject, $text, 'From: ' . $emailadd);

}


function mail_payment_record_by_auth($auth, $initialize_report)
{
	$sql = "
SELECT 
	`id`, 
	`name`, 
	`email`, 
	`telephone`, 
	`octopus_no`, 
	`check_digit`, 
	`booking_date`, 
	`begin_hour`, 
	`end_hour`, 
	`discount`, 
	`p_selections`, 
	`auth`, 
	`timestamp`, 
	`src`, 
	`school-name` ,
	`golf_cybersource`.*
FROM `golf_fairway_booking`,`golf_cybersource`
WHERE `auth`='$auth' 
and `golf_cybersource`.`req_reference_number`=`golf_fairway_booking`.`auth`
limit 1
	";

	$account_variable_filepath = "account_variable.php";
	if (file_exists("./$account_variable_filepath")) {
		$account_variable_filepath = "./$account_variable_filepath";
	} else if (file_exists("../$account_variable_filepath")) {
		$account_variable_filepath = "../$account_variable_filepath";
	}
	require $account_variable_filepath;
	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	$result = $conn->query($sql);
	while ($row = $result->fetch_assoc()) {
		$mail_data = array();
		$mail_data['begin_hour'] = $row['begin_hour'];
		$mail_data['end_hour'] = $row['end_hour'];

		$full_url = "http://";
		if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
		    $full_url = "https://";
		}
		$full_url .= $_SERVER["SERVER_NAME"];
		$mail_data['full_url'] = $full_url;
		
		$mail_data['auth'] = $auth;
		$mail_data['decision'] = 'ACCEPT';


		$p_selections = str_replace( array('[', '"', ']', ' '), '', $row['p_selections'] );
		$mail_data['p_selections'] = $p_selections;
		$mail_data['initialize_report'] = $initialize_report;


		$booking_arr_buf = $row;
		$booking_arr_buf['payment_amount'] = $booking_arr_buf['auth_amount'];
		$booking_arr_buf['p_selections'] = $p_selections;
		mail_payment_record($booking_arr_buf, $mail_data);
		
	}

	$conn->close();

}

function insert_payment_record($conn, $data)
{
		$sql = '
INSERT INTO golf_cybersource (
	utf8,
	auth_cv_result,
	req_card_number,
	req_locale,
	signature,
	req_card_type_selection_indicator,
	auth_trans_ref_no,
	req_bill_to_surname,
	req_card_expiry_date,
	auth_cavv_result,
	merchant_advice_code,
	card_type_name,
	reason_code,
	auth_amount,
	auth_response,
	req_bill_to_forename,
	req_payment_method,
	request_token,
	auth_cavv_result_raw,
	auth_time,
	req_amount,
	transaction_id,
	req_currency,
	req_card_type,
	decision,
	req_override_custom_receipt_page,
	message,
	signed_field_names,
	req_transaction_uuid,
	auth_avs_code,
	auth_code,
	req_transaction_type,
	req_access_key,
	auth_cv_result_raw,
	req_profile_id,
	req_reference_number,
	auth_reconciliation_reference_number,
	signed_date_time
		) VALUES ('
	.'\''.(isset($data['utf8'])?$data['utf8']:'')
	.'\',\''.(isset($data['auth_cv_result'])?$data['auth_cv_result']:'')
	.'\',\''.(isset($data['req_card_number'])?$data['req_card_number']:'')
	.'\',\''.(isset($data['req_locale'])?$data['req_locale']:'')
	.'\',\''.(isset($data['signature'])?$data['signature']:'')
	.'\',\''.(isset($data['req_card_type_selection_indicator'])?$data['req_card_type_selection_indicator']:'')
	.'\',\''.(isset($data['auth_trans_ref_no'])?$data['auth_trans_ref_no']:'')
	.'\',\''.(isset($data['req_bill_to_surname'])?$data['req_bill_to_surname']:'')
	.'\',\''.(isset($data['req_card_expiry_date'])?$data['req_card_expiry_date']:'')
	.'\',\''.(isset($data['auth_cavv_result'])?$data['auth_cavv_result']:'')
	.'\',\''.(isset($data['merchant_advice_code'])?$data['merchant_advice_code']:'')
	.'\',\''.(isset($data['card_type_name'])?$data['card_type_name']:'')
	.'\',\''.(isset($data['reason_code'])?$data['reason_code']:'')
	.'\',\''.(isset($data['auth_amount'])?$data['auth_amount']:'')
	.'\',\''.(isset($data['auth_response'])?$data['auth_response']:'')
	.'\',\''.(isset($data['req_bill_to_forename'])?$data['req_bill_to_forename']:'')
	.'\',\''.(isset($data['req_payment_method'])?$data['req_payment_method']:'')
	.'\',\''.(isset($data['request_token'])?$data['request_token']:'')
	.'\',\''.(isset($data['auth_cavv_result_raw'])?$data['auth_cavv_result_raw']:'')
	.'\',\''.(isset($data['auth_time'])?$data['auth_time']:'')
	.'\',\''.(isset($data['req_amount'])?$data['req_amount']:'')
	.'\',\''.(isset($data['transaction_id'])?$data['transaction_id']:'')
	.'\',\''.(isset($data['req_currency'])?$data['req_currency']:'')
	.'\',\''.(isset($data['req_card_type'])?$data['req_card_type']:'')
	.'\',\''.(isset($data['decision'])?$data['decision']:'')
	.'\',\''.(isset($data['req_override_custom_receipt_page'])?$data['req_override_custom_receipt_page']:'')
	.'\',\''.(isset($data['message'])?$data['message']:'')
	.'\',\''.(isset($data['signed_field_names'])?$data['signed_field_names']:'')
	.'\',\''.(isset($data['req_transaction_uuid'])?$data['req_transaction_uuid']:'')
	.'\',\''.(isset($data['auth_avs_code'])?$data['auth_avs_code']:'')
	.'\',\''.(isset($data['auth_code'])?$data['auth_code']:'')
	.'\',\''.(isset($data['req_transaction_type'])?$data['req_transaction_type']:'')
	.'\',\''.(isset($data['req_access_key'])?$data['req_access_key']:'')
	.'\',\''.(isset($data['auth_cv_result_raw'])?$data['auth_cv_result_raw']:'')
	.'\',\''.(isset($data['req_profile_id'])?$data['req_profile_id']:'')
	.'\',\''.(isset($data['req_reference_number'])?$data['req_reference_number']:'')
	.'\',\''.(isset($data['auth_reconciliation_reference_number'])?$data['auth_reconciliation_reference_number']:'')
	.'\',\''.(isset($data['signed_date_time'])?$data['signed_date_time']:'').'\''.');';

	if (!isset($data['download'])) {
		try {
			// Execute the statement
			if ($conn->query($sql)) {
				m_log("reach lib_complete_payment.php SUCCESS INSERT CREDIT CARD RECORD $sql");
				return true;
			} else {
				m_log("reach lib_complete_payment.php FAILED INSERT CREDIT CARD RECORD $sql");
			    echo "Error: ";
			    return false;
			}
		} catch(Exception $e) {
		  // echo 'Message: ' .$e->getMessage();
		  // echo $sql;
			return false;
		}
	}
}


// mail_payment_record_by_auth('0190415b992551ecfa7c86f97b2e3aeb','Test Mail');


















// $insert_data = array();
// $insert_data['decision'] = 'ACCEPT';
// $insert_data['transaction_id'] = "1234567890";

// $insert_data['req_transaction_type'] = "sale";
// $insert_data['req_reference_number'] = "0190415b992551ecfa7c86f97b2e3aeb";
// $insert_data['req_amount'] = "0.00";
// $insert_data['req_currency'] = "HKD";
// $insert_data['req_locale'] = "en-us";
// $insert_data['req_payment_method'] = "card";
// $insert_data['req_bill_to_forename'] = "";
// $insert_data['req_bill_to_surname'] = "";

// $insert_data['req_card_number'] = "";
// $insert_data['req_card_type'] = "";
// $insert_data['req_card_type_selection_indicator'] = "";
// $insert_data['card_type_name'] = "";
// $insert_data['reason_code'] = "100";
// $insert_data['auth_amount'] = "0.00";
// $insert_data['auth_code'] = "1234";
// $insert_data['auth_trans_ref_no'] = "1234567890";
// $insert_data['auth_reconciliation_reference_number'] = "12345";


// $insert_data['signed_date_time'] = "2024-08-06 09:30:19";


// $account_variable_filepath = "account_variable.php";
// if (file_exists("./$account_variable_filepath")) {
// 	$account_variable_filepath = "./$account_variable_filepath";
// } else if (file_exists("../$account_variable_filepath")) {
// 	$account_variable_filepath = "../$account_variable_filepath";
// }
// require $account_variable_filepath;


// $conn = new mysqli($servername, $username, $password, $dbname);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// if (insert_payment_record($conn, $insert_data)) {
//     mail_payment_record_by_auth($insert_data['req_reference_number'], "Cybersource API processing");
// }

// $conn->close();

 ?>