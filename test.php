
<script type="text/javascript">
    alert('您的付款被銀行付款程序拒絕，請重新處理。 Your payment was refused by the bank\'s payment procedure, please process again.');
    window.location.href = "../";
</script>
<?php 
exit();
 ?>

<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');


if (true) {

    // push forward to insert the expired payment


    require_once 'account_variable.php';

    // echo "3-";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // echo "2-";
    // Check connection
    if ($conn->connect_error) {
    echo "con err";
        die("Connection failed: " . $conn->connect_error);
    }




    require_once './cybersource_api/search.php';

    // report_cybersource_by_id($conn,19640);

    // report_cybersource_by_id($conn,19645);
    // report_cybersource_by_id($conn,19630);
    // report_cybersource_by_id($conn,19557);
    // report_cybersource_by_id($conn,19552);
    // report_cybersource_by_id($conn,19661);
    // report_cybersource_by_id($conn,19710);
    // report_cybersource_by_id($conn,18936);
//     $sql = "

// SELECT 

//         `golf_fairway_booking_history`.`id` AS `id`, 
//         `golf_cybersource`.`transaction_id` AS `transaction_id`, 
//         `golf_cybersource`.`auth_code` AS `auth_code`, 
//         `golf_cybersource`.`auth_amount` AS `auth_amount`, 

//         `golf_fairway_booking_history`.`timestamp` AS `timestamp`, 
//         `golf_cybersource`.`signed_date_time` AS `signed_date_time`, 
//         `golf-payment-session`.`payment-datetime`,

//         `golf_fairway_booking_history`.`name` AS `name`, 
//         `golf_cybersource`.`req_bill_to_forename` AS `req_bill_to_forename`, 
//         `golf_cybersource`.`req_bill_to_surname` AS `req_bill_to_surname`, 
//         `golf_fairway_booking_history`.`email` AS `email`, 
//         `golf_fairway_booking_history`.`telephone` AS `telephone`, 

//         `golf_fairway_booking_history`.`booking_date` AS `booking_date`, 
//         `golf_fairway_booking_history`.`auth` AS `auth`, 
//         `golf_cybersource`.`card_type_name` AS `card_type_name`

//     FROM (`golf_fairway_booking_history` join `golf_cybersource` join `golf-payment-session`) 
//     WHERE `golf_fairway_booking_history`.`auth` = `golf_cybersource`.`req_reference_number` 
//     AND `golf_cybersource`.`decision` = 'ACCEPT' 
//     AND `golf_cybersource`.`transaction_id` is not null 
//     AND `golf_cybersource`.`transaction_id` <> '' 
//     and reason_code=100
//     and auth_amount>0
//     and `golf-payment-session`.auth=`golf_fairway_booking_history`.`auth`
//     and `golf-payment-session`.`payment-datetime` between '2024-09-18 20:00:00' and '2024-09-19 20:00:00'
//     ORDER BY `golf_fairway_booking_history`.`timestamp` DESC;
    
    
//     ";


    // $sql = "

    // SELECT 
    //     `golf_fairway_booking_history`.`id`,
    //     `golf_fairway_booking_history`.`auth`,
    //     `golf_cybersource`.`auth_amount`
    // FROM (`golf_fairway_booking_history` join `golf_cybersource` join `golf-payment-session`) 
    // WHERE `golf_fairway_booking_history`.`auth` = `golf_cybersource`.`req_reference_number` 
    // AND `golf_cybersource`.`decision` = 'ACCEPT' 
    // AND `golf_cybersource`.`transaction_id` is not null 
    // AND `golf_cybersource`.`transaction_id` <> '' 
    // and reason_code=100
    // and auth_amount>0
    // and `golf-payment-session`.auth=`golf_fairway_booking_history`.`auth`
    // ORDER BY `golf_fairway_booking_history`.`timestamp` DESC ;

    // ";


    $sql = "

    SELECT 
        `golf_fairway_booking_history`.`id`
    FROM `golf_fairway_booking_history`
    where `golf_fairway_booking_history`.`timestamp` between '2024-10-01 20:00:00' and '2024-10-02 20:00:00'
    ";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['id']."<br>";
            top_up_cybersource_by_id($conn,$row['id']);
            // return;
        }
    }

    // function report_data($raw)
    // {
    //     $count = 0;
    //     $uncount = 0;
    //     $total_amount = 0;
    //     $trade_record = json_decode($raw);
    //     if (
    //         property_exists($trade_record, '_embedded')
    //     ) {
    //         // var_dump($trade_record->_embedded->transactionSummaries);
                
    //         foreach ($trade_record->_embedded->transactionSummaries as $record_index => $record_object) {
    //             $data = $record_object;
    //             $uncount += 1;
    //             if (
    //                 true
    //                 && property_exists($data->orderInformation->amountDetails, 'totalAmount')
    //                 && property_exists($data->processorInformation, 'approvalCode')
    //             ) {
    //                 $id = $data->id;
    //                 $submitTimeUtc = $data->submitTimeUtc;
    //                 $merchantId = $data->merchantId;
    //                 $reasonCode = $data->applicationInformation->reasonCode;
    //                 $rCode = $data->applicationInformation->rCode;
    //                 $rFlag = $data->applicationInformation->rFlag;
    //                 $code = $data->clientReferenceInformation->code;
    //                 $applicationName = $data->clientReferenceInformation->applicationName;
    //                 $transactionId = $data->consumerAuthenticationInformation->transactionId;
    //                 $ipAddress = $data->deviceInformation->ipAddress;
    //                 $resellerId = $data->merchantInformation->resellerId;
    //                 $address1 = $data->orderInformation->billTo->address1;
    //                 $state = $data->orderInformation->billTo->state;
    //                 $city = $data->orderInformation->billTo->city;
    //                 $country = $data->orderInformation->billTo->country;
    //                 $postalCode = $data->orderInformation->billTo->postalCode;
    //                 $email = $data->orderInformation->billTo->email;
    //                 $firstName = $data->orderInformation->billTo->firstName;
    //                 $lastName = $data->orderInformation->billTo->lastName;
    //                 $totalAmount = $data->orderInformation->amountDetails->totalAmount;
    //                 $currency = $data->orderInformation->amountDetails->currency;
    //                 $paymentType = $data->paymentInformation->paymentType->type;
    //                 $method = $data->paymentInformation->paymentType->method;
    //                 $suffix = $data->paymentInformation->card->suffix;
    //                 $prefix = $data->paymentInformation->card->prefix;
    //                 $type = $data->paymentInformation->card->type;
    //                 $commerceIndicator = $data->processingInformation->commerceIndicator;
    //                 $commerceIndicatorLabel = $data->processingInformation->commerceIndicatorLabel;
    //                 $processorName = $data->processorInformation->processor->name;
    //                 $approvalCode = $data->processorInformation->approvalCode;
    //                 $eventStatus = $data->processorInformation->eventStatus;
    //                 $retrievalReferenceNumber = $data->processorInformation->retrievalReferenceNumber;
    //                 $transactionDetailHref = $data->_links->transactionDetail->href;
    //                 $transactionDetailMethod = $data->_links->transactionDetail->method;
    //                 if ($reasonCode==100 && strlen($id)>0 && $totalAmount>0) {
    //                     $count += 1;
    //                     $total_amount += $totalAmount;
    //                 }
    //             }
    //         }
    //     } else {
    //         echo "No record<br>";
    //         var_dump($trade_record);
    //     }
    //     echo "uncount:".$uncount."<br>";
    //     echo "count:".$count."<br>";
    //     echo "total_amount:".$total_amount."<br>";

    // }



    // $raw = cybersource_api_date_range('2024-10-01 20:00:00','2024-10-02 20:00:00','0');
    // report_data($raw);
    
    // $raw = cybersource_api_date_range('2024-10-01 20:00:00','2024-10-02 20:00:00','1');
    // report_data($raw);

    // $raw = cybersource_api_query('submitTimeUtc:[NOW/DAY-2DAY TO NOW/DAY]');


    // report_cybersource_by_id($conn,7673);
    // report_cybersource_by_id($conn,2507);
    // report_cybersource_by_id($conn,2513);

// 28db0f34f5429c7db73194ff61bf4360

    top_up_cybersource($conn, '28db0f34f5429c7db73194ff61bf4360');
    // top_up_cybersource($conn, '8215301a2a66bf4f398798635d95b3b5');

    // $order = 'asc';
    // $sql = "SELECT `id`,`auth`,`timestamp` FROM `golf_fairway_booking_history` where (select count(*) from golf_cybersource where `req_reference_number`=`auth`)=0 "
    // ." and `id`>".file_get_contents('last_asc_id.txt')
    // // ." and `id`<".file_get_contents('last_desc_id.txt')
    // ." order by `timestamp` $order;";
    // $result = $conn->query($sql);
    // if ($result->num_rows > 0) {
    //     while ($row = $result->fetch_assoc()) {
    //         $auth=$row['auth'];
    //         echo "id:".$row['id'].'<br>';
    //         top_up_cybersource($conn, $auth);
    //         file_put_contents("last_"."$order"."_id.txt", "".$row['id']);
    //     }
    // }

    die();

}

if (false) {
// {
//   "save": "false",
//   "name": "MRN",
//   "timezone": "America/Chicago",
//   "query": "clientReferenceInformation.code:TC50171_3 AND submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}",
//   "offset": 0,
//   "limit": 100,
//   "sort": "id:asc,submitTimeUtc:asc"
// }


$domain = 'api.cybersource.com';
$profile_id = '73B281D2-B93B-4CAA-95B4-8D945E5A4C4F';
$key_id = '1f376db974fe39a38c56c8996af542fe';
$secret_key = 'c3028e36e6d8483bafd567cc73e17161805651d0aa484c629293ab71e63a6a9d89e3e8a417964b47bec9a7c007735e10085187857cea4c93a59d3ed7ba162ca486cafdd18be248ec85f4c69e95bfa3152dcaf7e5ced84919a0001a217a8c45cd9da011f2672c4e5f9572801b0328e1d517125b5ffdef43e9ba1a81f1c0086507';
$data = array(
    'save' => 'false',
    'name' => 'MRN',
    // 'query' => 'clientReferenceInformation.code:fa57b9f8af3b82b17d89cae0d2e68bf8',
    'query' => 'clientReferenceInformation.code:TC50171_3 AND submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}',
    'timezone' => 'Asia/Hong_Kong',
    // 'timezone' => "America/Chicago",
    'offset' => '0',
    'limit' => '100',
    "sort" => "id:asc,submitTimeUtc:asc"
);


$domain = 'apitest.cybersource.com';
$profile_id = 'b9e5fab9-2d75-4ddb-92f1-ac8639347648';
$data = array(
    "save" => "false",
    "name" => "MRN",
    "timezone" => "America/Chicago",
    "query" => "clientReferenceInformation.code:TC50171_3 AND submitTimeUtc:[NOW/DAY-7DAYS TO NOW/DAY+1DAY}",
    "offset" => 0,
    "limit" => 100,
    "sort" => "id:asc,submitTimeUtc:asc"
);

$request_target = "$domain/tss/v2/searches";
$url = "https://$request_target";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");



$json = json_encode($data);
$v_c_date = gmdate('D, d M Y H:i:s T');
$payload = json_encode($json);
$digest = 'SHA-256=' . base64_encode(hash('sha256', $payload, true));



$merchant_id = $profile_id;
$host = $domain;

$headers = [
    'host' => $host,
    'date' => $v_c_date,
    '(request-target)' => "post $request_target",
    'v-c-merchant-id' => $merchant_id,
    'digest' => $digest
];

$header_string = '';
foreach ($headers as $key => $value) {
    $header_string .= strtolower($key) . ': ' . $value . "\n";
}

$signature_string = base64_encode(hash_hmac('sha256', $header_string, base64_decode($secret_key), true));

$signature_header = sprintf(
    'keyid="%s", algorithm="HmacSHA256", headers="%s", signature="%s"',
    $key_id,
    implode(' ', array_keys($headers)),
    $signature_string
);

$final_header = array(
    'Content-Type: application/json',
    'Content-Length: ' . (strlen($json)),
    'host: '.$domain,
    'signature: keyid="'.$profile_id.'", algorithm="HmacSHA256", headers="host v-c-date request-target digest v-c-merchant-id", signature="'.$signature_string.'"',
    'digest: '.$digest,
    'v-c-merchant-id: testrest',
    'v-c-date: '.$v_c_date
);
echo $url;
var_dump($data);
var_dump($final_header);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, $final_header);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    echo $response;
}
curl_close($ch);


die();
}
 ?>


<?php
if (false) {


$content = "Oct 01 2024 01:31:12 PM HKT	
7277606723816671903240
0ed15cf105f8ff316ae119a9d51e8aa0	0570	
Success Card Payments - Authorization
Success Payer Authentication Validation
Success Card Payments - Settlement
XIAO FANG	YANG	427405369633	
7277606723816671903240
400.00	HKD	null@cybersource.com	vbv			Visa							vdcwhb			AAkBBnSCYAAAAJxANEJ1dQAAAAA=	DhNsa1o8P0RChT3V74M0		751629			1295 Charleston Rd	Mountain View	CA	94043		45.64.241.138	US										Secure Acceptance Web/Mobile";


$content = "Oct 01 2024 11:39:07 AM HKT	
7277539477756249603048
c2e8695aaa99d2e73deb6e6051f89d8e	5682	
Success Card Payments - Settlement
Success Card Payments - Authorization
Success Payer Authentication Enrollment
PAN	LIQI	427403136114	
7277539477756249603048
100.00	HKD	null@cybersource.com	spa			MasterCard							vdcwhb				36tjk7bgywdSntgAdhH0		743345			1295 Charleston Rd	Mountain View	CA	94043		218.103.201.143	US										Secure Acceptance Web/Mobile";

// Split the content by tab characters
$data = preg_split('/\t|\n/', $content);

// Assign each substring to a variable (assuming the number of variables matches the number of substrings)
list(
    $date,
    $dummy1,
    $requestId,
    $merchantReferenceNumber,
    $accountSuffix,
    $dummy2,
    $dummy3,
    $dummy4,
    $applications,
    $lastName,
    $firstName,
    $retrievalReferenceNumber,
    $dummy5,
    $transactionReferenceNumber,
    $amount,
    $currency,
    $email,
    $commerceIndicator,
    $installmentIdentifier,
    $tokenId,
    $paymentMethod,
    $paymentSolution,
    $authorizationIndicator,
    $partnerOriginalTransactionId,
    $partnerSolutionId,
    $deviceId,
    $terminalSerialNumber,
    $processor,
    $businessApplicationId,
    $terminalId,
    $xid,
    $paTransactionId,
    $salesSlipNumber,
    $authorizationCode,
    $jccaTerminalId,
    $acquirerAccountId,
    $billingAddress1,
    $billingCity,
    $billingState,
    $billingPostalCode,
    $billingPhoneNumber,
    $ipAddress,
    $billingCountry,
    $shippingFirstName,
    $shippingLastName,
    $shippingAddress1,
    $shippingCity,
    $shippingState,
    $shippingPostalCode,
    $shippingCountry,
    $shippingPhoneNumber,
    $customerId,
    $clientApplication
) = $data;

// HTML form to submit the variables
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Data</title>
</head>
<body>

<hr>



    <form action="./payment-page/payment-confirm.php" method="post">
        <br>signed_date_time:<input type="text" name="signed_date_time" value="0000-00-00 00:00:00">
        <br>transaction_id:<input type="text" name="transaction_id" value="<?php echo htmlspecialchars($requestId); ?>">
        <br>req_reference_number:<input type="text" name="req_reference_number" value="<?php echo htmlspecialchars($merchantReferenceNumber); ?>">
        <br>req_card_number:<input type="text" name="req_card_number" value="<?php echo htmlspecialchars($accountSuffix); ?>">
        <br>auth_amount:<input type="text" name="auth_amount" value="<?php echo htmlspecialchars($amount); ?>">
        <br>req_amount:<input type="text" name="req_amount" value="<?php echo htmlspecialchars($amount); ?>">
        <br>req_currency:<input type="text" name="req_currency" value="<?php echo htmlspecialchars($currency); ?>">
        <br>auth_reconciliation_reference_number:<input type="text" name="auth_reconciliation_reference_number" value="<?php echo htmlspecialchars($retrievalReferenceNumber); ?>">

        <br>auth_trans_ref_no:<input type="text" name="auth_trans_ref_no" value="<?php echo htmlspecialchars($requestId); ?>">


        <br>req_profile_id:<input type="text" name="req_profile_id" value="Empty">
        <br>auth_cv_result_raw:<input type="text" name="auth_cv_result_raw" value="Empty">
        <br>req_access_key:<input type="text" name="req_access_key" value="Empty">
        <br>auth_avs_code:<input type="text" name="auth_avs_code" value="Empty">
        <br>req_transaction_uuid:<input type="text" name="req_transaction_uuid" value="Empty">
        <br>signed_field_names:<input type="text" name="signed_field_names" value="Empty">
        <br>message:<input type="text" name="message" value="Empty">
        <br>req_override_custom_receipt_page:<input type="text" name="req_override_custom_receipt_page" value="Empty">
        <br>utf8:<input type="text" name="utf8" value="Empty">
        <br>auth_cv_result:<input type="text" name="auth_cv_result" value="Empty">
        <br>auth_cavv_result_raw:<input type="text" name="auth_cavv_result_raw" value="Empty">
        <br>auth_cavv_result:<input type="text" name="auth_cavv_result" value="Empty">
        <br>merchant_advice_code:<input type="text" name="merchant_advice_code" value="Empty">
        <br>request_token:<input type="text" name="request_token" value="Empty">
        <br>auth_time:<input type="text" name="auth_time" value="Empty">
        <br>req_card_expiry_date:<input type="text" name="req_card_expiry_date" value="">
        <br>auth_response:<input type="text" name="auth_response" value="00">
        <br>req_card_type_selection_indicator:<input type="text" name="req_card_type_selection_indicator" value="1">


        <br>req_locale:<input type="text" name="req_locale" value="en-us">

        <br>req_transaction_type:<input type="text" name="req_transaction_type" value="sale">
        <br>req_payment_method:<input type="text" name="req_payment_method" value="card">
        <br>decision:<input type="text" name="decision" value="ACCEPT">
        <br>reason_code:<input type="text" name="reason_code" value="100">
        <br>auth_code:<input type="text" name="auth_code" value="<?php echo htmlspecialchars($authorizationCode); ?>">
        <br>req_card_type:<input type="text" name="req_card_type" value="<?php echo htmlspecialchars($paymentMethod); ?>">
        <br>card_type_name:<input type="text" name="card_type_name" value="<?php echo htmlspecialchars($paymentMethod); ?>">
        <br>signature:<input type="text" name="signature" value="<?php echo htmlspecialchars($xid); ?>">

        <br>req_bill_to_surname:<input type="text" name="req_bill_to_surname" value="<?php echo htmlspecialchars($lastName); ?>">
        <br>req_bill_to_forename:<input type="text" name="req_bill_to_forename" value="<?php echo htmlspecialchars($firstName); ?>">






        <br>applications:<input type="text" name="applications" value="<?php echo htmlspecialchars($applications); ?>">
        <br>transactionReferenceNumber:<input type="text" name="transactionReferenceNumber" value="<?php echo htmlspecialchars($transactionReferenceNumber); ?>">
        <br>email:<input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <br>commerceIndicator:<input type="text" name="commerceIndicator" value="<?php echo htmlspecialchars($commerceIndicator); ?>">
        <br>installmentIdentifier:<input type="text" name="installmentIdentifier" value="<?php echo htmlspecialchars($installmentIdentifier); ?>">
        <br>tokenId:<input type="text" name="tokenId" value="<?php echo htmlspecialchars($tokenId); ?>">
        <br>paymentSolution:<input type="text" name="paymentSolution" value="<?php echo htmlspecialchars($paymentSolution); ?>">
        <br>authorizationIndicator:<input type="text" name="authorizationIndicator" value="<?php echo htmlspecialchars($authorizationIndicator); ?>">
        <br>partnerOriginalTransactionId:<input type="text" name="partnerOriginalTransactionId" value="<?php echo htmlspecialchars($partnerOriginalTransactionId); ?>">
        <br>partnerSolutionId:<input type="text" name="partnerSolutionId" value="<?php echo htmlspecialchars($partnerSolutionId); ?>">
        <br>deviceId:<input type="text" name="deviceId" value="<?php echo htmlspecialchars($deviceId); ?>">
        <br>terminalSerialNumber:<input type="text" name="terminalSerialNumber" value="<?php echo htmlspecialchars($terminalSerialNumber); ?>">
        <br>processor:<input type="text" name="processor" value="<?php echo htmlspecialchars($processor); ?>">
        <br>businessApplicationId:<input type="text" name="businessApplicationId" value="<?php echo htmlspecialchars($businessApplicationId); ?>">
        <br>terminalId:<input type="text" name="terminalId" value="<?php echo htmlspecialchars($terminalId); ?>">
        <br>paTransactionId:<input type="text" name="paTransactionId" value="<?php echo htmlspecialchars($paTransactionId); ?>">
        <br>salesSlipNumber:<input type="text" name="salesSlipNumber" value="<?php echo htmlspecialchars($salesSlipNumber); ?>">
        <br>jccaTerminalId:<input type="text" name="jccaTerminalId" value="<?php echo htmlspecialchars($jccaTerminalId); ?>">
        <br>acquirerAccountId:<input type="text" name="acquirerAccountId" value="<?php echo htmlspecialchars($acquirerAccountId); ?>">
        <br>billingAddress1:<input type="text" name="billingAddress1" value="<?php echo htmlspecialchars($billingAddress1); ?>">
        <br>billingCity:<input type="text" name="billingCity" value="<?php echo htmlspecialchars($billingCity); ?>">
        <br>billingState:<input type="text" name="billingState" value="<?php echo htmlspecialchars($billingState); ?>">
        <br>billingPostalCode:<input type="text" name="billingPostalCode" value="<?php echo htmlspecialchars($billingPostalCode); ?>">
        <br>billingPhoneNumber:<input type="text" name="billingPhoneNumber" value="<?php echo htmlspecialchars($billingPhoneNumber); ?>">
        <br>ipAddress:<input type="text" name="ipAddress" value="<?php echo htmlspecialchars($ipAddress); ?>">
        <br>billingCountry:<input type="text" name="billingCountry" value="<?php echo htmlspecialchars($billingCountry); ?>">
        <br>shippingFirstName:<input type="text" name="shippingFirstName" value="<?php echo htmlspecialchars($shippingFirstName); ?>">
        <br>shippingLastName:<input type="text" name="shippingLastName" value="<?php echo htmlspecialchars($shippingLastName); ?>">
        <br>shippingAddress1:<input type="text" name="shippingAddress1" value="<?php echo htmlspecialchars($shippingAddress1); ?>">
        <br>shippingCity:<input type="text" name="shippingCity" value="<?php echo htmlspecialchars($shippingCity); ?>">
        <br>shippingState:<input type="text" name="shippingState" value="<?php echo htmlspecialchars($shippingState); ?>">
        <br>shippingPostalCode:<input type="text" name="shippingPostalCode" value="<?php echo htmlspecialchars($shippingPostalCode); ?>">
        <br>shippingCountry:<input type="text" name="shippingCountry" value="<?php echo htmlspecialchars($shippingCountry); ?>">
        <br>shippingPhoneNumber:<input type="text" name="shippingPhoneNumber" value="<?php echo htmlspecialchars($shippingPhoneNumber); ?>">
        <br>customerId:<input type="text" name="customerId" value="<?php echo htmlspecialchars($customerId); ?>">
        <br>clientApplication:<input type="text" name="clientApplication" value="<?php echo htmlspecialchars($clientApplication); ?>">
        <button type="submit">Submit</button>
    </form>
</body>
</html>


<?php 

die();

	
}

 ?>
<!-- <form action="https://api.cybersource.com/tss/v2/searches" method="post">

	<br>save:<input type="text" name="save" value="save">
	<br>name:<input type="text" name="name" value="Search by Code">
	<br>query:<input type="text" name="query" value="clientReferenceInformation.code:fa57b9f8af3b82b17d89cae0d2e68bf8">
	<br>timezone:<input type="text" name="timezone" value="Asia/Hong_Kong">
	<br>offset:<input type="text" name="offset" value="0">
	<br>limit:<input type="text" name="limit" value="100">
	<br>sort:<input type="text" name="sort" value="submitTimeUtc:desc">

	<input type="submit" value="Search">
</form>
 -->
<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');













require_once 'account_variable.php';

// echo "3-";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// echo "2-";
// Check connection
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}

// Create connection
$conn_1 = new mysqli($servername, $username, $password, $dbname);

// echo "2-";
// Check connection
if ($conn_1->connect_error) {
echo "con err";
    die("Connection failed: " . $conn_1->connect_error);
}


function show_record($conn,$sql)
{
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	  while ($row = $result->fetch_assoc()) {
	  	// var_dump($row);
	  }
	} else {
    echo "For $sql <br>";
		echo "No record <br>";
	}
}



// show_record($conn,"
// 	update `golf_fairway_booking` 
// 	set `timestamp`=CURRENT_TIMESTAMP
// 	where `auth`='4411';
// 	");


if (false) {

echo '2024-09-07 to 2024-09-08<br>';

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b7cb6f32a83df4830954018a6028b918'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='615aa6e187af514a7bda807e572c2f96'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='102032b76a3d32a365908dedd9a82054'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='847eb3c2d828501a85bfbbc86a0ed17f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='57f4cb5d1055b39d3515a65cc13f5409'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='531b9353898f4cc7c5c21ef81dd974c9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='51bc2cddfb3fa56bdef3ffe440977664'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='82e1c35285ed3516a9dd74bb708bd2f2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d1ee22e1d12c292f03fabb8f3a72fc7a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='12580acad8a880c92b06ed6f15fa9eae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5d8bbb1342b3ed90c5b3b661fc7932d5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c224c691ef510e49251c3cec48bf80bd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d0edb678926c524f0c4d2743c57ac13c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='39ac246288b69356f859c4d8143a4c36'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ef91c3460f96795f0f2c6823ddcd6c52'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d53f5d4473aefcaf9d4ed01d796c9fb9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fd77fd2187fa7dbd4985922bd691b5ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98643b28df93ff7d7dfcfb39908c1eb8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e63014d20bf57f927363792711fb2619'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b358edfca59dc06b6a6dfc3c876e00e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17a5548f81ec6a3a61fab2606ee5553b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='23f0bca3dced46f839e5ec8a45641f2e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c52955b74e4d686ca36c9f6473e7bb64'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='69a214cc6efc49406cb1fc35375dd36a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8aff08e3e0b207df473c47b026df9d97'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='33df804ab741e4f4fa8f444a77f06c60'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='02392547272766b94080451a90a87609'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0f805bdd7db46f3f4ab425620670c0ba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='642c4c4d76710d44178862daa46922b4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5545e0d27e664c3bf0a4aca3563f9b05'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fcc581fb80ae3a411a773cccf20659d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='19a343204ff2dde32c51a35f8d091f09'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='48757b50af9630b2cab1a8a1e07c68b1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='478b7b60fb7394a35aacb81677b741bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8ccc044ed36a63f3e5593dc3190f5c1d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='be5ff18d8f4aac233e2aca117a8ef4bd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0c508a146c3f3c16a4869a3414dea475'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a45ab6065efb65cb55a4045bdb3999ff'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7b6ecc5873506073729c26b5cebded21'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='459ead05bfc8207e74211b0846690e0f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='13a648d7b1c864b46671b85121b00c08'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='43a9eefb1d6a3c4362f6750471a3cc3d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='32b373c0cfd375b60a8eab45ca87f9db'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='58f929b01f77746fed614cbcbee1de5b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='64c851dd3dfda0bea24d02c234b392d2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c5cc63be6cf297af57c4546adfa30033'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0725ac5e3096bbc09f2df390997e87d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a55847f3037986f96db5d854f7ba91db'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cbe4414d7a93305a46b3cfa45b567db5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='272bfdf0678edf4fe2abd4704372c98b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aa3cd544dbc8f051644f1a228274ca28'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='504459a4225c44f0970d1ff611ee9464'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='95977dd15fd8bbaf11a0aaa99ca731fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1b180d9d9f8d1173f7fe4d1104619087'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f8d4de81cac006f2cf72d206c1b00e03'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='536b659691fcc5a39848b0dbf8af13ca'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a31840bf65664029874de365fd1af87f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8f37e21fb18c5f8cac3cde66bb9d21b2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98d0c759e7f031f73d0f885d6fe186b9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f59c4a17ea68ff1606862cb967017077'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ec2149e9b8069dc449e29ce846693561'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6c1843ca01cc94c745f86e789de71955'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='de56e6d85e2117729b2210988bd6b5d2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='002b4b8e4000a3ab7ab95757c816c5ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='259cf9ccb7c2e374aeba35a8c9f96d5f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='666bcd0da72e4b2c41167673f23451b1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98ae71245ff81c78f9ac8bf295094821'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='62ab5cc400269f483aa69e6214ceba67'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4f8e8c0d1246afa843eaffe92733df3c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e508c67dc8bee7ce524aca96f596e942'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='37bb49a55de0dd1566159d64a3d4e59a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='87b80344735b219ff4e244f8d8b8ef55'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3b50891ab98a3fe27af64c5fd633847c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1c07d674ba1ee7c2eab6f669377ad29a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0b24774b3f7c7f422e704a640c02e90b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eabcbe016cd1ad7ea5999a377688277b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='111e848245b025f95a732107e758eb36'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='84a8cfce33d563fa229857b8d248e50d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2d87c6682395788c32bbefbec2f8b0e6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='def4b1a6bf77e6d826cc571aea54a64d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4f266774d6ecd44d5635c0e838cb50a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='11fe524279888cfd875db6b3128d3eaf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b190fd72ba2c8ec452d250c6da04c44f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='be102cb03deb0a1f9a8710a588da99cf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e80ad98ef1dc3589feb5ef04117ea4c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b92d7ae507003053b9518ea961646b3e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d753cb9820ebef15e24b361014e98bee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f8b60d9fa1f3c139d98b5a4a2e60c675'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5bf1696790f9eb0d304d9b6fdd11e877'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1fc6b5550bcf7ad7ecd2d1ed43f9d182'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0dc77739434e10e0555bbb03c06e979a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7196d01dc1cc22a8bf550d2fd812b07f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5b7235ad718e0e002e3d8263090661f7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='48762dbb921f7472f13d0560b928c1e7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='65314fec78974d03c32aa9cad72aa838'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='c7755ce90d268cf4a5f661e5c9e7d007'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='72764adfc1f14c2aefa9467a6e3e743c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c69c95629b56f722c203d77c232f8647'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='94f4a73346cab6520796d6c5cd28c082'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c293c1a6b1f319374ab512776ec6cc4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='13bd0e41203e6f46edc4e1eee194e297'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3caa4c2135dbe943004cf83ebd99e699'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4d1f385bc6a8e7611f0c5b9071fd9891'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d8560ec945d5cf146efa680f94de57fc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a67e407b556cda4092f77cac2ad0f333'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d756cd4d733e9b1e29f4fd2868a370a4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd4c585691353e747239f664483f6835'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8337c2fc572ca3f1c6a2b23508d72978'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2260106f11e67d73840d5d4f1d06825e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0d1eadac90f8bfa8a8e54488bda90eb4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='109827b57e9cc1bc643a57b580738ebc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4b06b9edfe8ebd22761bf245d47b7d36'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c9e8504b595a148648b74a053e3ca15d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='167724455c245abe544a74576bfa78c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='705859d925a310ca94a899d4d303a3e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d8ab5b0fb4e998c7c5a45da6fde415c2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='554d27d2780382751f148d3af37aba4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e3db9a91fca91c1e825a8f1560407e98'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd4c051fda6d87bee4f875233f6dab40'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8fcf18f8b9166a0d68ba8476ad52dd74'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='70c878adb076d98bfcd33fdcfb33e805'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df95e44a31cc2715ee7e28c6be553f84'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c00cbb60e82527aee03a1d78c4b86d90'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8b453db5edaaf6ddfc741b08a46d57fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e292a40bf327a0f12899d36c59d3d87d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5b1b52c6917e6925ab9548f4e18503d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='27e73043ee52df27b006d79d40c95702'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6b11470f97be5831b1af74c27378db89'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='27345f7f549f3c83669ee9858855210e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d8e922f6029d4b92e6bac1161a0c475d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7964c6dafb7b58f2e7dde9700f69c972'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c61dbfdaf52f7a6f5f19c6d578455ce7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c6d9fdaf2fc83128f1622b50ed8a1dac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c301cf66801112060beb447828a0ebd0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='26c42145e6c0c960f5ac7fede1a81f73'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='16d0f6309cd271fac45de6333f52b4d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='675ac693f9ddb105294bfb5da595dd79'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9b6385ab532a347ae188d01424eddf2a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fbfd703efc24ec64395bb64335c3f1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='09b181b535d178d3c2e03a25fd6fd17a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2a9589aaa5f1aa6deae7ba77533a421c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3a49eb235af1c188606c477edb276904'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cc1d0db2b0a8c39db25d2639bede3f64'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dd9bbf2c39b27d3b1a59c7cd0457efe4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8009f610415ad2da0420cd4cc52d6678'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='80d71f7d7ae721252db7bd162cb31ea9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='31f663c0e37b441d3038569e5fb2189a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c9a132a4a3b26b2aabd4b261195f3a06'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='acea67b41073cd61b0856913fdb2e81c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a8b2efa25166a2f068c3b6206680c74'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='68125f63b139359119672d7ac0919f6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='12e8b8156e37a92c79012ee0d1de2ee4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c44778ac0676c1207e43ae346221b35f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d9b3d04284ae575273811e6545c30079'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b5696dcc7f4b160e624c8e472de5b984'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a79e4d1544b7d0eeeb8a375cc075f9fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='785354ce5f543a926c4b6de9dd7498e2'; ");

}

if (false) {
	
echo '2024-09-08 to 2024-09-09<br>';

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8b3d1dd693908b2f2348f1240b687669'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ae32fa15c61bc63a5d14575a14f7a4e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bb09d098d196b54b0a2f67beead74b8d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6d0a27c0c7f1a30af58619529642eca3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='64729358d9d7620da70b5e99c3613822'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a3e14c21079713115c8b2b435afcc2f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='244eae544893c26b42892959d27b97ef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc19ebb811ae4e681d04935be534259f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='00576a4965e5d4926336089c3992d07c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb5ab0505853a21f74d5332b04de2856'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='689d93b0a64cc61943b03a24c5924919'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7fa54baa1d73806579d6e5ddf9be7c6d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='26e684c0725ce78ed67a95bb6f9e3ae3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9aae1759f436b802e609ff9917f96056'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e426afeca4035c53f43163a4b7f2d9fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='44bae70708621a66a35d8bb8ee2cec4d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9b77b5f6973ffd0c775f5acd21579df2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='95cf72565a806cd9e6ad0fb7b9ba2370'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='edb738a2b7e1cbe15762c79ce9a93168'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ffae37d583a386372880585aa3161e6c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='70b90984ad398842a252143da29999c9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='195b585ee732837cd9f856deb110afbd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2e0460d5c2c6701cf93e321f1f721bd7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4009d01e03ff20ab17db1d110994da28'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='18d3fce66b9b6e7512a8357eda0fe796'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9797fd53ad79c9d75cc82ba077f26133'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb2748486ba78de05d54ccb95ede0ef8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='47771bdb744753ab9f362048b37f9f21'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a3c0bb345e5bbdf977adf1ff78ab881b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ce3c24ae4f62e242ec8bb6295b4c34f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5880eafd7c6c426810818404df4fa242'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8d7cf3f44f3dbfd49be2b42447a6873f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a638dc106b254909a1c94b63b405168c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='69cee51c204701b97337c364169eac8c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5330561bea165b988e75d984d7999e45'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9261ffe6f41b49089d81b27570eed2e7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='790b91c98089af79b6304bb7ac639906'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b5cb452c0b58761b613d5a06a2b35c4f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3ea8e99deebc511a0ad03e5f477061e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b8487168de4c4bcb0af542c08b686add'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aaeef6b3b2f11b72ad196519844aadd6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='26b54990e30372ec0b5c7f75c8a6cedb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='55b14a1080222655e9e215f74ef1625b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5593c8098614fad8267d380cec2f4ac6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d00916aa625d20ecdb283bcf9998babb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='623e9f6a11c967536a90f01d5a5544a8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0caae1aaca889976859047bd46f34c26'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='847cc1b81eace6463713b53eacf46cdf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='74c601f15cc18e0166cc73a553d4c0fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bdbffc57fda0f50b65e2f3722b4b0c7e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4af6c185b74f63e15263e18aa444aebc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b0b3cc6cada307da051e9be48f922f71'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c43ce7fa7a9bef51665eab3c88ae6840'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a639802260804c9b07691a5d47a7fdac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dbe635e3c348dc1915d31f35a6cbfdc5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fd605c5ef870c73fb7aea5c97cf4d992'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='35890b316cde8d77a84c557b2cb0bdb0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a50c09998cf75ad31a15b8470a3113aa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5b5b0796c76294dd616413533498bcd3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bba9d4cbd0d00b5eda2683b9ba945878'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2691ed94911da92e29c3a52e63983f99'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='70e15ff90d818d7c2d368e74400566c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5a12b62e1e44406057d148dd8f974b2a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='76e01030b8fbe37fa5658a2cb1259c67'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='73aaa4a83fd74d93e27bc2d4bd1085a5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='62724256a6101b7bbddb3e3a1b0e1d95'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6aeb0fce25f2d1a0c91fe9804deccf49'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cf3a3bb51459cf41fe486ae6bd70739d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa9edb78f630f07719908c7ada54b92c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17cadf91eb264dde5974695f1fe3b7d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f348aca1f04922979f40bd53f15d0623'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b4284ec0fc62ffee94ff8a210b9f84da'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ddf324987d8230aac17c89d217e4b542'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a4dd434b7df7d2d97dfd02edfbaed6e8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c459a4be6141a74a5d12f6c3cea92ad9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3a4bf07d11b8ad26d820fb5660c87924'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3a0fd18328a065bd749a851ccb1af2ac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8a62337e21d9bc7ed31e51354838dcc5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='107d7dbd98c1bda1f2f3d32c00ef7d1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05a6ae1f53b59fcf6747e7b413cfb227'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b421df871a077fdf5fb6b8ba467fd7af'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='52868350edbc5e00c1ab6580bdd97d86'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1229d8a5fb40b20ed3c199e666742f97'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7f2c4c0814a2dbeda89e1fc491f6a904'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2e5487931d80c6e259139c3a15cf4645'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5f940915273a85adc6878559e19a8ac5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='38a97b3cd52562fca1643e1f8972398f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='81d25fce148adf712426e61a975e92e1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='46f4225f5b09bbff5abe4febc19dc0fc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='1124138cf9918b268e8cde186e0134bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='89dfb911809ab9c2c73bc7890215350b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b794ea9e57c867c84ebf8e57c88b75ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='753eb374c0f55ebc09528fee4db3e355'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc1055d88415419f4aa15c7c8df05e8c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2915016ec6187be2f0d540e1c9955e7e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ac3ab8b34cf957c06db62d44013e0b53'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2f8965c15fad11673d0c16b585b7158a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='57d33c4e26065db8c23a0cac7bbae4d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b9473f996842340055e6b2abf496ed3d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b3d0fa7f44a66f722ad63e9bfbdb4618'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5096705806478ed1009f3ee56ac71107'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='31ba9c6b489ad3ebbf7567e3d0a3ec79'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e22f683bbd2cf8f8961d855f580bf7e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='923587c34e228efb1e9d1a5f4f433f93'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='34d2d0581e4d2ad61afb21847697b2da'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b8295bb3e88cf8daee919b99db4203b6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8e0796a7656628ec539e3910e904e307'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1f903f3515d33d0149b1494a2e6f3811'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5f688ab94a23b5274882f7266aa43cc2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f7d866355b8bb6ac52bae311c9fe9bb2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92838ab5e9c4ec0bdc6d2ca8b8bf5bf9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ab87b891d32f265563501ff40780dd1f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5b501a7eb62a1810147e730c717c2ed3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b46f130ff1a1f0b4a45c3eb29a0db2d5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='73d7f5fed4a4669685775127307e6b55'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a48e1eb148574d0218d2470578a8362'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3d09d08349bc04c29280772031eb92c3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='50058a0f70cd543948e6742b3680ded6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8c79bea991c9fa7933088d1d87fc44c2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='303492c5eff3b01698baeabdf7d756ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5567f3c389129596266f215f27cc1381'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b412ebfb5ec3c5dc5cb1c6900a0ce80b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='01ebca751c5bf92575d4b4a836bcad6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f174aff9fb38b0029d9ac09747fdedf2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='725606738f6b5bc0e4d5984eee0d2163'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='300b02573c7c5f98c25e78ba2d0b63cc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a47e5f2a0406bcff21f39dd55d6d7dc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05739021b7367d00765b37472f498d6c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='04e6ae1391fc84618f1b30c0c5a4917c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7ce06f82fddb7c79268258ace4e85cc5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='93f6513406d1feaf47a01c259c97cb5d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='824db88ced9897d3a640b21191fad38c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='48870c0513a4181924f559a4ee638371'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='478b3bda6620f01b0c0a8d0d96edacdf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c3ae7e0a50dc77f8d755a5defd220e61'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4ef964cb7fb7373213d7a94a839ec6b5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7099ec437f485bf71e82b50e3e1de97b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc1bed687e833e0423e9770c09ca9689'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c7a96cd3b1a8910a9d466482a38aa431'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9655dde19371cc8feb5da894e721b4d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb8b2fbeb9108577a0b5630d61b8ccfa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='97b76e86943960845aab244eaf2966b7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a8dd8fa3b442c3869f71712189f78b0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='ff3d39c9c98fc9a2d3d5fd2eecdd955a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7a08d68c505ffb11165e01f51e2001e6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='711089e8499ed0a6f79bc61368f5fbcc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='281d11b6542259230c1e3755e2e86ba8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='881e81371b489f8947ba7084c515f7e1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='fe29f7317e0d26b08908e557ecbb5d11'; ");


}


if (false) {

show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='c7755ce90d268cf4a5f661e5c9e7d007'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='705859d925a310ca94a899d4d303a3e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='195b585ee732837cd9f856deb110afbd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='46f4225f5b09bbff5abe4febc19dc0fc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='1124138cf9918b268e8cde186e0134bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='ff3d39c9c98fc9a2d3d5fd2eecdd955a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking_history` WHERE auth='fe29f7317e0d26b08908e557ecbb5d11'; ");

}

if (false) {

echo '2024-09-02<br>';

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='18ea04a5fcc276c68d642bb4a8430107'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='963d2d888e545683353ead91b6df5768'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dd928affdb81fb11cfe2f38927c39ce2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9f6118d91320061bf074268fea9d254d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79fef6bba52e2ffce2266c02f294003f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='85b5531ae37ffc4b34acecc63e1e5323'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='164e89fcf87d172d6648432e47673e39'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd3115866de859d780f26e4f4f8fb67b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='922a6dad29e6ca4470c3ecdceb218816'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e34a78811fd277a2a7723b389fb28e76'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a4cacf4c8b5a86500164c49a7769761'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='14456bdd38a002c08f637012eaf6824e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='45b27534c19dd0796f7c49d38f614dd9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='210284e7ba878409effcf7630a782792'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f1dcc095e4ce29bba69e6ccc73cb3803'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e19e2540ee91322b846127a87340fe17'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6bb9d169083e7f25201e55d8aa93f420'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3db03168f267ed12b9c381dd42a1984a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dafae8f4fec2712b4e350684c0f274da'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e4a8cb1e014e601f868d50d40354f5c8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9eaaede72283fff7981af87cf2287126'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1657cc6de7ce775a7749d10d17d4faaa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='090b325a6cc98f312bb3de84c117a566'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b3d2861821cfdec620a05b2a95e16ea5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8cd463810740676e9cac2385d24ad665'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b311a71a755e809d31168b4409d61391'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0c9ba5874d4f60a846ea47e1a9cb58a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='70eca5eae0a270dabca9a9e79d41e428'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='818491dcf98c797a76f5e978788b0f30'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='15fecd723b07d301a72840490a6b22db'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='655e197a2ed44c6a87cebdff9a085c4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='46723851d67a8c9f87a1c8bd51943a6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eade98a7f8306dc0cf29d37c63c504e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7ef0cd20fa8a7930d7067d201746f8cc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c62775fb81e392922ac38f37e7533842'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b6f67afda472c8f76175807ffff4aa24'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='91205ce39a223b0afe5beddbf56b498a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ddf66de7778f6d098a92b1ce15c9f239'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='36a9201f3ab2f6ee82ce83a2d45879b5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5e6018f3586dbbb2b708b2357bfcf1fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='72d658703ae15e10028d6eddd08719ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='893d6fc1f4a2e602a1668d4588c99565'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6df886aefc8f16481c815b61635faac9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6e1f3de7ea5f3dba260551025ba0eb63'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1042418fd879aa3b9b6608e8afb415dd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8d8580c3fca3c9362b9c50bf603e321c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5527c904da1bef734d0e16dabc53888a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='60ed21089b27717f2d65a7c693c1cc2c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb6eaf6578cfd782ff1b7000cdfc02ef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fe25b392f9b834320f74e1719c538d6b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cab3c10405e8bd792d346face229d975'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e262134111dcbdcb35936856abdf9bfc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='430e6e50d61f774a88ff004aeeadfea7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0aa479b2243ce66bc1fd08b5e3d600a6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='65d83d6c28f170e3e4ca9e1fd8d95dc2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05462194576f0fe0c0d359917453485b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5f4b8fb076c34dd619ddf34a7aea0c83'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df0c35b8994598f326f172483514fbd8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='07b7c470c2d3c485bc084c1eb0ca23cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='887ba2ae0f2f8d84dba4127d56f90fbd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df6b63f39be2130f03ac293e4e59c3ec'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ab0085092188250c03cd62d01c7608d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c4bdd013c4ac7306a25e6b1eb44bfbc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0c3ac17755b569c2c7fdd5ca8744988c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='776dc60f2efaf66ebea43b58a3a060cb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3728013290ea4424494e593841b5f0d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f7ce6545a8d8d88bbba6e65ff6678013'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1311f75157f355aae657a51b211faaca'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c1fb5ac23f234ec598e55021faa92d89'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fef1bb66205c4bf497bc3c168d7dc73d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='06012ec3cd7d7b9886273794aca58852'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='39c8869f506e0097e68a4f7ec5b33df7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6eb25da55f7fa65e7920f58546fdb63d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3388d23419d930f9a2b3a3a390ab6c6c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c867efb933da273cb5b3fe52997298a5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7d6383a35198a71be4f09d260736feaf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='49fca2a1f4434f841159697f3be1d39c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e3695172c1a1c695e6db0d56b8b5c64b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6f4f57b52d096bc8969776e96916e453'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eeaadf2020e4276929480b76f7157475'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3ccad22242959f312b3963d25b1aab70'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3f8a25cfd6b8c34cd99da00245180933'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d78306acc7015ea63730fc6f0bfbd9e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='84b23a978eebaf6bef0e5c528425ae32'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7dbbd1cdc9bae5ef4aa5c72cfe614d74'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aadaeb859d89cad21449ad8226d2c318'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='87f7f2f2baf9fa1730af9ee6c48c92de'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='987a0d0b96199ebbba96cacf83e49af5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='285fa8ea19351461349d4c479d6f2341'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ff194ca95b24c753aaee074b737393dc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1bf938d4b63ab32eb1c7c3748068b22a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='47584284383af4f58a72b9a6329e9ebc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='be6c9e596e9ccd8e80a11828722e56f0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='934ab541dfa64debe2b76d98e2b90f7d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e91c25ca116694f8febe200ed518d771'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bb3dc93f61f7f31b77c4db0dddd223ef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d6d2c74aefc1dfa1a271430c1d4b58cb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9718e03e2aa97e060fca1c266173c224'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f91a08c1746537b9e26926bf6195d5dd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6f84bb2248ba38aa3788effa59893e51'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e8e494dfb24a2d6796342b1cc585431f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7be98866dccbeecff58303c2f855ed14'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='48c2046afc26124b0430b4e7d43a92a9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8b0fe54a07f393ac1d6d3f407dfd26f8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad5b47fa53ef922168291c8082638c4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='61daaf919fe7de313576af8fe183674e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b7760c7e3c9e298b891dfe0a7efcf56c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f1efd98c628a2c7ae753beb63a6e827f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d4153cdaf59ed9fb68b13059eeabd621'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b139a93ac72fb83becd9867e8ddc13ce'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a2821d9d373b4ad2ee3a4c9b9708c60'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='19c4aa4886ff4a4877ca5a948aaa3584'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ffd77bf6574a681b383dcdee3965634c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb6448696159c39ce4f4e87fe9ce6892'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c4fbc4bfaf690398fb1f1877876f9340'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b54fea4816986e305e18e54e39ee3ba0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b562f858704d63bda53f3db22794ccb8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a94cb5524948385ffce62d4c5c05c2e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ce6644e76ffc5a0a376421297b3f940'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='62f5e7fcd459c6b142b94915e9d74113'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4dcd9ff73ed5494d3c49047343c0ce6e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b8c8d330c52c3de9657e9c581ac1bada'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1fdaff6f8dd2ee043962b0b1a0d697b5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0252a308047301cbe9e6c08ffed6cd77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='59bbfc8e689d0a52f038c57b839ceb3a'; ");

}

if (false) {



echo '2024-09-01 After 12 <br>';

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a0787b8d10ee4bf2554b9f8dcc1c5feb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79b3e4651c7568ec0ef7c19c5a905fa6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='75a1cf0389c4882ec237e8f801a84d75'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a9a735d7ed21e79a12983ab5384987e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='38007ff2a4c8fe1c949cb9c6bff31f1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='259ecbec036b0dd96f5e385d7460deb9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='25ee8c5323bcb09e09bb0d92b61e1365'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4bab4b827732a9511c6b69ef84ac9041'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bba52122c759cc0510550fd0e9872466'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='507008e4adf7d9d389b3736bf5bf52f6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d93a77449c245f48300990a90bd876b7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='53edf8a4610a274a0f3451427d01129c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='52e64c0a8f7cc372238012c8067f069e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='55b8468ea2261563fe966f120932c2c9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7de51037b529bf180cb21196ee6f788b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92c8556817d6d6866a2ca95eda0ab80d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0da7b5d8fcad90f2512cd56eb226a971'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='804cb7367c772c7b2a135e3740c1f850'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='712ec18094b683a64ab14287eacf7781'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7f9a676ef2924c7f6f8a272876c5a444'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1f5f353f4beb85c2a139c1a93af77c5c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='90f8da90ba12c17926d5bc2a6732b9c1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f55a3768c05d4d5b96deea36c4d183b8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0d29dbe4015edc3df76cfca7723cd483'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='240fe4ba3700667f103fd65021a0dec0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4216f77c221c23a7a5344e9238526481'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='895abe818384e5b1c8ac8b01dc733d4d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2e8ee7519e838c3a653a25acdd085ad7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='af5a4cdf6d995e5041e361a8d330524a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='be31b90313ce03a9f5989fdd1d0574d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fb3ccb3cbe6c55634e512e71373065d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='71a1060dcdfe4cb2078f81bd002f4760'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='155074be98fc16237fbe2b5940f153fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d62a7083bd0ce1e70d42ce24e78d3c32'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1f2220e2a0fdbfe323b6abafac33e8d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f7e1d16ccc0d303e6758591949e5d895'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6f829b2c12995f91635bf832d3a276e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0e4460a7360308211be45ae024f5f6ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8cc737741993a5831cd68da36e2b43cf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='813d04b8af8776c26f36a5e57e943671'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='10cc5b69bea8b8ec8029afec6ae7bd77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2d446e6d911dd53689ff4d2e26dfe647'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d5b3b3339ba096e2b1f79988e8b2d055'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5e157ea29ee9e3a1920c4397ac2dfeb4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='61aaa883543df64c5be95eb64ba91c1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8569991f9cc6428b111330d688480fad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7036ac5eba54b47050f7514dae51754e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='65616abd6a86572c5314404d0a806a19'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='77ddde0dec76f720898a25cb5c307ae9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bdf02211bc7231a0e711d06002de3b2a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='929b5b2cc1943bfad308afcb0d8b9087'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='85fb387ae1ad16910e778c128fa99d77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b3f11cc9a67bc333b42c5b732a34d4e0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cff3c9707bd2fe87220fe141f4b0dd99'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='14a11a3ac4fd2c82803927d44fc8e330'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e0defabc0e6d8de25734325ac6e7a01d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2c3a64f9a0917ff28b9684c513898436'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5dda5102b41ba227419d091259a05c91'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2a1d8bb436380b83efeda93170c7083b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d76acb251449dbb8a27e16c6eef79f2d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='97ee45c97947fd84a3a5381b629f8391'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bc8a3adf9f8fbd8451b4827d98510167'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='327dff1b8df6278954ac2a406de9360f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f2dc2b0d56ad4597b1468d3b2adbe5a6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='925303d3a6850d9b2234f27942a0403a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5bf3afe96490b4adce31bd217490ed2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4b466b47c8d598c372546c30a81275fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='43919f474492dc2cd399f30b9e7b18f0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2a2193236f2494a3ba5f5b61ec587598'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='da2fbce68960d7b0ba44d533db0ad239'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='574b655674f7f75493e346f339c0c7fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='39f01c245a2ee8193a43b6ca7ef93615'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b698215565903c324bf4de0aafaf8bd4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6e91f1d71dfff9758133176e2857cb65'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='842a6813d30b050bf003af958eea1dba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='50b2defcbbb18d371d350a17309ccaee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='504e35931c2e191396828d974091b385'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e0424f7800b4d38662b09172355b17ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='46cf603bd360e651aa4b2ab710aa30a9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8c7c108e21c0f92df35cf80ff2ea3170'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7f5328e511321d0e4be6c6b15f1af093'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='43039f16a0b0594322e84569583cb642'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='07c02ee7dc78085ac278951e3b6eaf91'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6d9912ac70328b1721bd135abd54d863'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='81a693940d3fb63fd1ffbe16134d1abb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e448dd93790216a6d9e8020b372d057a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='67ef28c531bf3961389eecd7ec485b13'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad075be7e40fabf841e9503a6d351413'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6aefd1109a075b79aa20577afb4754b8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='68ab69d61dcc0947dbda79823d0e008e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a0ce7b71040ed5e8e23f8b97caa935c4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='350cfebb624ab2b346dc341f3ac810be'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b576106fb94d6653701a3f75571418db'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ac6b2130ed8ab305e9544ecc6e4fd594'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='51f38a957fec6a071d06eb69445855a1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad98162abfec4ca8f198b9d0af988fa5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2fb3da64ba11f1694b7177f3d86685f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bc795e8d30fd5e78645bfd29bb0503da'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d750c3317cae99dea0d6538d16cd658f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0f834decfd875878d7433106b112c518'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5ec31837a565f2df74b209c6428df6ef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='efce9b7b111f9fba45d3c8f3309fc061'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8b14a51b60b30c8c16c64038193fece5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2ff5804a3514973fdf3dee61274f2023'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='16471e65e376b8006d93c24ca3bd7d9f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='295fd9a1916c933c10eaa43601cdc68a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='62ff832bf259a6a8bac929f54bcd04fc'; ");


}

if (false) {
echo '2024-09-14 <br>';
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c7b75d62f58a960e7bc3750fb99b8779'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a30889a077e8172e100757e561dd05ae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='37980cffaf4405708f8f6204d031437a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a81eae64730ed134cb8cab25f30cbb3c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3e934202c9e297a119b7aece98e960d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='32d099c8ca087ebd6702c02f7ccc4678'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='556d472a9227e3938379f57861eb7f19'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='542840d667ada64debf20366a1beac59'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c77a2220bc61842ed65c0f0ae35f38a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9e9afb25ef05d7a79351ca2e97b416c5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b3bc9c3f50e2d8431cd24a251310c6ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='108a82d755eff3071acf77cc998f0771'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a301968e43a23a15b2f399c7e2aa5427'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='44d0ba6d0cd7b411ffba28dd75f5cf6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a972b4deff8c9c66bd99511d210ca691'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a940e8e1912dfb7a2fe8ba7675847784'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='57f83992fce66f61a633ca53f789c5af'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cefb3029ced02fd561e6fe30fcbab90d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3bf7dae5670d65b64ec2b3f27c2b4152'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='062654e4ad50c74352ecc61687366a66'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='de1ad38ba5fb9046a1796721832595dd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='68169c09fffa31565e68ffbdec143961'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0c99479160f35e049246429be794ecb7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2fccb041a7b3e12ad919941c7175c563'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3e540ab4dcbfe6091b4b35182b7a326d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8c0a24c66dd7eea22a9f70c62d21245a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e9cec284a0f3d3f85f1bbf7d7bb1e01e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='846e090185e3303222f60d02f446427b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ca96cd84267165afef26f98ca3887e11'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='16599a1935dabea6ff163d6fae0d5da7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c498807c6586c5073a3fa3b3c57233b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='11910a50f840155f0afd5bd9d3e43f9f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9e59cd9cfd7877d06ce764cca7145284'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fdc02b5931dd1d50661f86865aa0e89'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d021660c259ce187b24feb38a013e894'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='77f032cdd421a1080f36949ffc8fd933'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7c9c578a51e7e79cc529e1218131e7dc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a880b9dfceb1753612c73347bb30e9f4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8848e1d3a6b7ef2c6c98aaa95bbc2336'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05dbad58c2f97db6f04aef48b43abd28'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86a5bacb637df33a1549b289182359d7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a9d59f8f8607703a6c167d375607d423'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a1a61a66b23dea561e5a2828f64e631'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a398c23ab31e7e6352edfb7bf1ccb151'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aea0b74900412d8d341d96725dc1a88f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2cfc9b21181815ec3785ef17f3c86f7d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='326dd6cfb7830157a595f548f086a5ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='716cea5ab9a3bc646f9f73edbdb881c4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b15ec4e3c6866abf6d422db653122ab5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a2044b9f1d75a1310b0d4224cc28aea5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0d921386a2d2ff92b51ed82de1ff616c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c58c643ca9674f5470d8e7ef63a0e8df'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='be68677ac80e51112fd679601f34fbe3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='943afff82fbc85e5c52c5a8f9135a01a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7972e6e8b00cc70a6250571fb58dba45'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f4fead45bfbb25e57a57ff9c4b33ee0d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a74b95381f5595abb42065bdad3d59b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd8bcabbf7fa1981df7f2cb96a725e65'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6ba7490cdc26df6438c5b4affe02606e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c4e429aa603f65e114620c4a5e97b3ca'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f77989956c76e612c95ecd33eddd23dc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='966afaaccc614c2445d02f51cc93b0b9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f985da948aebd449c86d99971a222868'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='94d7a772fbe7b2b02662bca3e6ed76e0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e628edd79cc72162f9f0eb8ed88f8ec7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79608572cde3c7991607e87501a84782'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='19e5207a010255068a88e332659253ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e879bcbb3d38a2f89a94b37600037112'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='453457309f9439792f742172ef5b4c1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0fc78891a822b6fdf5a48baa4e3cda92'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='14981e7c5e487902f60272471f85ba6d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7eb3fbf0c9524d875384d194c0df86e7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7d9ca95ea42de1ecddc3ae8eb4bb9536'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb03581f05106a2310574485ddbdde85'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a8c280d56322fc2465b8a1a8549832ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f8ff2b741d6b9965aae19e4b2b8d5fcf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='13c400ae2b20d9f191380b063ca604ec'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='32b4527806cc1913d69ebbe2e46fb23b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='22453e1282e7cda56bbc33b72076b00b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='909b8b61ec5e84708057a724c9e7aaba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cf60915a9ca2607d2b36887917c26897'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2162a767b2a441d90a644e581c6ce049'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f36a06bea5e86606297fba0ddca2b53d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3cf0eb4bae202227c48e0027b12d1457'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ff633d8dccc2941d0e8b55f90f5f6cd8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='60e8f59048cf868942f2ca6a3bf61ab8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2187eab757feaf2ae6f96494806e5e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='633575112edc64a4c6ee205b2fcb4686'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b759629a3d5d3d2fd00e65c90e5a0bef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1aebdf1688f2ba49e26d2da625997759'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='68a474d2937c4814aad18dc9cab3ed03'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5bde5657ea09f47a8fe5be4dd6633389'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='40b9cc15f30dd250d423a2ff02eea0a2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9bc1dd267e6b2dd07e1ec42162aabaec'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a1b263aecda865802181cb45c0f8cc56'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2a82069c8691cbb420942b937c3b9722'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cfa0f18929386820359183c29de7b336'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bd24a7e91736dd5a8ce7128cea7eee9a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8d27b30b04fdc3d9cb5e5ca9de8ab0e1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='af23e9dfb2ca109dbcf9c9dfd6fba446'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a97472b2582eede4fb94b7c1bd5b78ba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1ab78a6a3cd995ee2d40faf19eda4c73'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ed55bafcc494d07ed81a242f1de996c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d1f04ff3ccde821fc3d6be078d8d2fe5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df2de2c54d6060c64e97b1909beff5a4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c24f93e61a6e20aad786e080a140f300'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cbd3582f7143586a4ed49a72e7ac1694'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17af17a29ef38ad3a143f1d0cfc34554'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='594eb78d9e9edc03f2c829379297c102'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='27ffa624ac4c61e3ad20b771f8884274'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='21b247e43f379f459763184c741da134'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c8c1b2ef5002e5c4c8d45c6b9f3707d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='526e264d7bb9c735ea61b3b80e53ca56'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='82d5a76635d5054f8164e95550523f32'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bdd318203ce1e1fc3108f02bc0b0241b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5f3fdf0bc67c7cfa64674b9b185334f7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0eac57b31178ca6a49dccfe1ef94f34b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79fa32b2e3f65627827125544732a829'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8a2fc2b4b1d7d09610d253be3dfa136c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='13e75a7ec884e62f025183edfb91907e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='de51cbda1710db9d7a7771c9a4f7b404'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7632de1584aa59e78d6bdbc5b27a2d34'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4176a86e2e3785d6648685e8185516ba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='96e4dde4b657896da433199975a5a333'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='73fd2edb48fd86a26cb43d4b0364f66e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ebb04cd51843c1af3ece895d4f9345f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='557b035d59086845bd7d4fc63f6279df'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92896e22adcd8a16e3c217d3ee80ff1b'; ");

}

if (false) {
echo '2024-09-15 <br>';
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c03d82c7b67aee9953f5e24e4e764ece'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9c96ded059a0259d56c949f5a9740198'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f34f629ddce73a55b5e3c13d74b4d901'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f263afee21683d2ff84ee4437b407222'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8c3c7c393df2e9f6c6f597fe22e024e9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05f3976807664a88a67a470ee5d5f212'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df25d7e06ec05a98adcf7b6888461989'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='829fc7629dbb7cf682817d029e1d5a79'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d859125d861241d4582a9b89f317366b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4e2096f62b50bf9314f6893ba7a4a278'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ebd5553f21118ae21c3cfd1b2e71f71a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='58244d93c33d6d612f644466bc30581f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f9424e43b31cf4daf43ee7bc838ba9fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9c50486c55e7139db3a64b1bf0c9ad50'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9e988718786e633a368f73a726d3da03'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4d8b1ab03762608f92fdaa9f9596624b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='216aa8d334cbcc5aa3563805dbd4ff86'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='634cff5052742b217fd983f0fab9c575'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b2e5360b3fe862832cc7d2047bdca97d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ed232204892abfefd9746cf607cdfd8d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='186f0de8ad14c8ffcbe8c1c499d67be1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3ce02268570bf9a68f22bc1aa6451960'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c16c1cf11ab4051f36eb628bcd219243'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b5ed66db0580c768255258a6be92e859'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fb9f2b5ff77a8e0bdc5a25ebd4c88eef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dec183fd64b7fff6e5cc9d82c2d0e87a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bff72c497aaad6f996e656f54103fd3b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e634dc2fca97b5df7d7e1792c9606380'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7ade896b92d1a761fbb29e5ce382f1b2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c4436adff94d4557d11897cce9830f0b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7090b82417f702b541f9f8831d948bfc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17f5855e387abd1fbc26630ab121cceb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='91d45ecbb64a29d229b89af8e2559590'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='99ad3013c6fbbdea0fc6a2cb2e919337'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a9b923055a5130da1fd25e6d2b19af94'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f46f176d249d785addc9805ee5fcaf5e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a5fdd0be6ee1fc2174f5b2bcc0849436'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bc76ffe50eedabf6a026417b6058ab71'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ca84a5a8cabae5c1c5ab9e8986538163'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='357f549c442cceaef5b83a1e7aff75fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5fa16ea3d17780c52bbd043d3adb58e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c6679feab7aff7ffa04fc58c166093e9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='74fb558ba0693178a30161b1f4e279cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='19e34a897ceb8d18e378ab807896b403'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5860863178f700a7d653aa2121536219'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cfccecaf9ee9851089bf86f62371b116'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='07f267d06f8279642e64dcfee32fd6d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='96ff344d06e38d00d93b93ebafa58808'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='047efe12b0524a2eb7a04d3cffc27162'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f68c12a0259a77d3470b9adf67654e4e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6e8f4bbb02b8a3166755d3f72ac23489'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8dc49a22bdb1ac790be8e7acb86784bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fc012758c7c2892f3af3d51c9adb5ca4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='827f87e48c7920178f6ac25f297e69ed'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc346f47031fa516a4ccad3fc7568da3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='33b0f640077385639cf110704594c1cf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fafbae74bd9116dce83132402e356847'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2cf7303639133661d02d8c74cdae49ab'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='150f46c605dd6aa641c64dac0e912325'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d0285b1f9ad8e4f31285130a023df2bb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7a04b8e2ddc31f7dbe569e4d1f9a6607'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5ae8ade0147bbcc9e6a0ce074a9b2780'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b2fc4f32933cb445569f40199266a7cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5be98f9543f009345d6853df286bbe23'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d5505be10738e74f9ef7e96edcc53724'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1479941089d94c702fe8b34527f1d097'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b0bdae43937aee9cc3ad95cc7025d08c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='675ec4632a2d1381987eea8c8a56d739'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='084945b279cf639df308fba7fa9bfa40'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fb51b99ae1f1757414c81a02dcecd775'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2486bc29c1ecc3410c16eb9de1047f5b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7eb9218a20973e2f8b6850f2ee165317'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='db5014040244161f6ccef87dd47f6492'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9604654201d76c251564c81eb1e64c8e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a2fc5362b78a0234bd4dce356756ec77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bbb2e5be70fe60cc633b66d642c21d2c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='077cb2124a945e5180e6ebd86e106338'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='59d661aa31c0fafaf7729250bd38657b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='627ae3100eb87408052bb29bd362bf3e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a905d6f7a23c4cba8053ffa7df2978e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa9908c4476af2a998b6b4c4538422ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='25aa453ebcb7e73dd00edb2a2ba19f5a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9fd6121f55ea6fb37eba90246161e631'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3eeb06d9ea9b5b5580e7880e9f2d651e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='64efd892cb38aac4f58255c0f6ca2dfd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e491fe2d20222600c75406385346f1d3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a418642fe4cf49d639aceccd9c97d083'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c068dc111f0f3ccdbd688ca29c771c6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4d33a07577207df45dfbdb9ab4669b8a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5653bacc4277cffcc4c8cdedf36d2b8b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fd0fc20e83b916af74ef69e8e2d1831'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='90761bf1e2cae3985d5e3ebc4f2aefd0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2a1b4c887296e3c591ae24b94a9a46f8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98d36182983c0e28e5a145498696a13e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9555ca0b85abbacf603b69e801093031'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc6b4a451b9493f092be86e9dfe3da86'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2ef409050668fea7666505425c38d70e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='37f72f76bbad12d7df9a372b2664c180'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3390a4e0569e4948d40ab0cc6f411c7c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='96b33bfe8d194af759f1200bc3507cf9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='82eb6ca0c546303c170329953301a644'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2764ba4ae8618f87f4f05c1a7d81bf08'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4db24143fd810424017efe52e68b361c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='14f014f64752e9e9ecdb0086ee1b8805'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c9cd381f5a5a77095287532bc4564a0d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b5dbc25b9bdb6604b4332a98d47ff37a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='00c53f2784939e5be049bf562f818c40'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7b7f6a0a777d959cf31d14188f87ffea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bcc2c796d10df69a98d61db809d6ad99'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d893072910561dcaf23c7c87346670b0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9cbd6da13fa3682bc65ae65a31c7726b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb3674274f620873e09f97bf1682f7c8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='28a2ffaf4507c726f077ca5bd81649c3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6ac97c83a1ab1e3b00c19f69e141435a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bfb572b0071990105e3d604c2757ba36'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='26f3ea7945775387341292e99f205fbe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='861272197ec2e707c065870b90917f67'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='058d35fd3b5005000791fda5c2427b5f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a49bd90b25920d898e6c672571003c5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aac3cf1f8c59e0df92cb8e364ac78bf5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2db9b6a4d1f3ffbee288ef93467a571b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='49d9d80056773ca7c1d19dd42109b386'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='faa2ce8638dfa82d1773a1221fa80209'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a85449296f4b169bac8a5a4058845d60'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0bc2fef62b862798276a2a4dfe199032'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e2b62c0ce8240a52d191a8aaef6a39aa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='944d15502edf30ac221f0049784e81f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c5e312a7680f6831b5231bab6e2c21d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4df9c3d27edccfe859e686bf36bca98b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='de3c2286c7de8a2f67b08f4de5a6d4d5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd89466613a3342834d63258d5eb8686'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aba93b35405258326a341ee0f3026ab3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ef00ba22b2c2040be374b6030d8d2dc1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bf4380a9ae02f2591f18f001f360ae00'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='529e9f13f1aaa551c5297a5033682662'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e1b1445e9c54c9b8c457a30371f649fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6dd9f39f66444956d0f0265f84b9a7d4'; ");

}

if (false) {

echo '2024-09-16 <br>';
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8022e84694d294a2ab909c23fd0c027c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='deb54f1173138cad171325a68a4be81c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f87949274238dc75aae431127bd79421'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='536c3c1efae18af06adf7e031324b3f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ecd688a16e0bf6e51622dd117e25cb7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17899d22e7070a5a77b880ba6460fdf3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b2e123c65e0a5791c3580f3853078725'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0b4502111285ef6ba6034f1fbc44a1b0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b146288ae318ab4243d2557da786901a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2014094f554b549897ac464d2a1787e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='73539a1575ec4bd0fddbd8b46dbd485d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ccd7d6a44ce011639735a0dd167ac029'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='64ab1aea494e174d7e2fc7f9c0b27a15'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1be751be1e684962541afeccf0998c83'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='84fad31a7d7f91a25c7dd7e1a3ddccb3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='59d4a6c395a51d83429e88946e6cd37e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='305d67b6c5ee8c64554012cef5b00d6d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3fc08dde8febf56bba1683ca3c3b4658'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9c40031f41b3ff23290c5432237434eb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='78528d1767a0a4c3b4811211715424b8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2385ead309529ebbaebeb54232af39d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c5fb084c46ca17a06f7efa310ea9b420'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2c2e656f7c73a7d0f584fb2f8f9a4e0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ba9c514c92770838ae835a6d2eb1e908'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5bc14b74dc5441eccfa423767fda2d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3255427e7cc71872c9064e84fb2f3b5c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='45fd0a01503fd97845fcb17627bf4301'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c626bf4af09615993bc7f388098670ba'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d00494953a02f3f4c2bfde01537b4944'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c62abc31e612457e027b993a58e48aa2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86e0d147958c4bba58226dc4e5fc7caa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3188bd3468bbc9ab9045bcb106f4b7cc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c8202f51a890634bcd47eb602de65dd9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1d844058b9b010d57ff6ef3898515d46'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='352f9e4b0078294f32195fb3460085a1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='22b22858fecdaa5f67c52e542a33ba42'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='23679f8b585b897976199b38d96965c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98ebc5adc121480d60927740241e815a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b54c758615b1130172b39d6ecba4508e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='596fb756e78aca61927e2ce90a04fed4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='128f052fa6e22d05004b66497fcc196b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='957a48c10a264552268b5aae12206385'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='82886b8a59e942004587644da24c3109'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cae25e20bb21cb9d5b50f97f7d14d78e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='30a4b261f3c0facedcc862bf72c01161'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1163d92579d1adbde8919b36d7fa5829'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='760217274cc06da6db62795aade9a6a4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f23dcd003091f21ea38b7ed6e01f0370'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='871376827f59386cdb1a6dd70e4e7a7c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f67623b6763986136b3c1666a72544bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='81f3c9f30c4c26f60f1b26099efee2e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='085857c1dc8f3b9d0eb51cf1b737fe1f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a7dc3edb1ed1efb8ab021a26a6c009e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5654b3e55c5666faa8850f124255c277'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='abdd149e5cb6da0237084d3bb77a2458'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='019c0a73c8d18b579845ea9eac3d1f66'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='076ab6dd0743604e9863a3cd3666a08e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a1d1c1ea124f8dfab3ac07b54b6a50a8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5e2e15bf69e588034a2e2c6c48e1dab1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='364f10ab022b4764fe9b732f291dbbf5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='78e829a30992d4881d957ca11aba04fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='15688cb2ed6f98156cc4f946c3947110'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='43e030a08015b72f2d6ede07df7829f7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='542ed4e10397103b6c47aef8f43a755c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a104c4543773fe3bbbe1a425b26f91f8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='53d4ec1aa9012bb22fb88d844be9f0ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='104ba225251cddd26640f8c0071054ac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a5d56c4cf763fd09f130cae9b44fc2a3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1ea52300fa51b415f03d555a192bc1fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ed836cba9790d014b81bd8c7dac0ecae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='95f1d0bf56a17e5609feb4a482f53caa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e24bb8348905f1301cc4379dac01b72a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7ad76f3355ffff8b5a0c49a87b6b7a3d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f29ada117519006f42230a0e7467959e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3ab2954ec5d9b3c8fab8c664a8c2da9f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cba0ce77f413ae70087bebedbfdfc19b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f6549cdac9a5d340b60818af3deb9250'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ea21dd3de6a1ffb02c66af43cccc650c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='70d0c0a09a6d88027f53f65632446934'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a875427aa2118d2bb79ff889299038d2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a9494a4f6d0ada46925eaff135cac592'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='74f370cdf6b3e0217f0451bf8dab9a89'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e899fca9f8745de4e5245bf1e6afd783'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a81907cb429da12620852782a300e0f9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c00845089d613c87eeccfabfa4f10346'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bd385adb0395bd779662e5075dd89fc1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='348d8cebf41d0ce3efb190cea5d8cbc0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ed6fa7982f5783cb8466814dd014666d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6044b38f4f60ee64720a7181160cbfc4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c5ef90c830282726376ffee3b226dc94'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7c9b398b2b109fc1628662744bfb497b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86cb2e7146a9df50987195b1ef646d4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e81f1b86cb718fc8b45c7735d0ff1a6e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='93f2c899f848459d84db7b5625a987aa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f9632b22a6ef0927557c079b387ee210'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6e542c6e442e4bbc4d50cd4d7252d6e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5a3e1b558db1b1cf6306f1ff431c83a3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8a6c3e0680ed56a92e531b4cb9d5725a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c8dc1b904bd2bd944be5d32258f71380'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1961608ccd53af59fd8fe42bd4f04b9c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0a4395cad12cb1f7e8bca5b60dc42dfe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cbac9a6d75effa121c13ea907956f4b2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='58c4483d7f497950398b9da850899d21'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a1b9525ab5eaa544af9f89c78bdc8742'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e9d3f15df4add73f881a198b08d33f5e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c33c8114ba883e81e0f148b82bde8c8f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3184bb155cef5981baa5887ccd4e22bb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a21e7078425f945f07803cfee318f21'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4645ebd81761c28d613d30b96c8c4f97'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4f6fd712cbe7cb90cfb6de1c658f7875'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f948e88029ee600e48a90773dd3e31cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cc18839efa5becd1d1195d6e108f6acb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e38b6fec4bb106d1d46dc8b5ce135fa0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0b865623b2e085afcfe2b487a09db1e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d8b8d81ff66689f9b2b9d2e62472a4f6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9d680a20896ec293f80a24d4fd44b610'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='96bb80a9e7e1eb1630ff4ef1f4bff906'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98921b01b361e370aac97aa5eee8135f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c309ba1cf82ca6b70f2bf5a1ced3f246'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='55b3527b2ac407b3bbbcbc0a9d47ed96'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='41326f8ee4cff220c3640379eadd5459'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f22a861beab1fb84d5b8dc48203828a8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e7a08f399d3080faf721a09a45dd7635'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='979796eb69b826d4cdf8eb2a063f14c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dad0e5555a7fc1d2ea3b72a5604d570e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='32b27ed79b18dcee573a624f46b5c429'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dccaa1d0840514a15783e5939b6f1546'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3cf6f466d5ffbc4a8d4e7e6d2c38214b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3914bb002a58691d97a2f0f1361c978c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f6903c7a46d082d24eb93685bb636119'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2dde457ea6e86acf0d09fa8683a6b9bd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2eea49c6510e0bf0744a6918c34ed598'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='da4d16273f03672afad0a8be5b06f9a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='18473c9e6c4bd87149ca912bc00baf41'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a1104be40538a5cb55ce354aef69ce6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fee633576b9a5d1e919bc007ff960cab'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='97975bc11791fad4b11490fa8dd049d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7527411f6c0a2b06e6c11f1978e06737'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dfc18100e90c4a383cb56b7e5c48eec2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d3c4b65499de0e381126d3212220544f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fe7c8c1e6f92bfb45434e140bc26b0c0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2fd7536a243124aef206e24aea0daa1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='324eb0603ab2bcca9faf5d160a0ec40b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fa703e44121b07c7fe964fa7cbfc950'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e4249c40055cd621be2cce16ff6d3860'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='23c009a777d2d99a45de97d5afb23ac8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1dee7d6f7300c884633bca4333ab11df'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='310f733d7cc7fe2855cb8a0c39ac54c4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8fcbe5b3906bbe6851a09cc811c9ff47'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2a217c279968a1e8ab87bc8dd1a6eaa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='01e903adc36e2e6cdb6f0f5d5253103e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9bd9b5b10c186fed45c318f79b2a06ac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5e51c2a0c2521ff2e53a5e4582b10b2f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='96f3296e8c48252d5c5503f219b80c84'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4d65067bc549ce1f58ddaca5c40407f3'; ");


}

if (false) {

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e7d96deb163911322c8304484ce08d1d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3d80d77f0f68d7aba5d93bb626f280cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dce68417a4249a86e18b3ed5216047d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='39826155bd9258a0675e54ca5bed846b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='918cab19767545f352f19beffd98fca5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8c30b6b8a1ba9c5b7597f73e7010ce1b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c95455be75d763fd647afb090b487ff4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1497c8d9821632506d1ce398c588951a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f730da1d823066e07eac36b733970753'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='68287fb0b4f715b912be1e3f086fc083'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dde6e6adf318cefe05dcc2f7ae1b8d1a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7c96476abedbf064054b69e5017abb54'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ae30b17cf10c0e016756d5f50541d98e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='616c0e9a19b5fb4a44a60963e2f7f3a4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bfe6dc1e77feee2fb5a47b4961e59300'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6bb06b09ddbba9bfae91d3aab12820ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ca5965a2e9a871e8096dfa62da42110d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98499a9c02f39ce1de7d1c559590bada'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='88e1d9cbee55edce8fe7f18a2a091c4f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='737647067141b7c12592564e8ef1b04b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9916907a50b9f613fda2664003c6a339'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ebd1ec26a7fcf146bc498ae9352f3cfa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1c0b95709dd30c48eef6ccc6855c9064'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2d3c3bdce2109469f92f6601fd41d3a5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='535fe5c9ecd8f22909f4b6a9ae0eb3aa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0bd76f50285eb298f19397d8dd4d80a9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9cda6b3e0da8958aafc104714d8b3f9e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f584c292415141d74abd9abd8801a0db'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a66e798a8abae483a8b2beddf6b283d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='56b6f03596fc39c1799a8917407fd203'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b14488855259ab7442c5b7787013c26f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d2966d4017929e957885a552f2f5141a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a9454742b1e3df87bd82e7d9c2dbd15e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4db766056ddb5c21554d2877279c36a5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1faaa05aeac53013dec5e9b7849935e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a9f6066544528bda3ef631dbd3d514a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='af4c577c31f0bacb41663ee8fe9ed48d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='83b29991495f6952f0c3c3196209c3e6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eeb75580857d0890ab02d997374f87ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='98d489227aff12035ca3e8a08a0d4fa4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0f96b3d41849c87d6ec2ddc9c3cd06a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='43395cb2af367d9a4b2e8cd138208cb0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2f119355204551095d21ed6b9071c155'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='17eb40b7cd00c7781d1d8154ba9a4ec1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ab801f56b3b756f9db03bacad55e559f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='971fe42ff82bdfa7448881ada9af9f32'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79c1df0515d8cf24568efd6291a7631f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3f3d338f15874fb9ff92ba329b25a6ae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3c7a46df3b6c61fbebc0af38c342a33f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ff8095d19f47b6c74bedbe49858d1307'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b8826a5bce8efb0dca0997ab02d4ed9c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cd496aac82cfeeb82399407e649a6508'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='db88ee05f6304aa9a51993b0157f4fa8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a9f194de4a572f1504844f153fbef97'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='27f4aaef1bc56f6eb42cb523220f4ea8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e7463bc9db01c04a7420c649267ea90c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='51dee2df27533cddbcdedf0a58eb7453'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92bd7b3108ee596d66d4951c894fe2c0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d0b0d3bb85de987b94591d4610506260'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ea992d1abebc503fcffb0c563e82030f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='97a0939ca1fdfac61c1a840936d416d8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa9af45e033cf2625a496866564ebdc1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9124164023cf9614daafa196badff5a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1231aef20d1546765b1ca770fb2ed664'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='334afdbbe253e1cb191f14db939982e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f40f24ef6167549f5d8a59cd733d6fac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad771031f29ca420ce95b3aa49f810c4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d22c38e8b9337c8be80f64c3c90a38e6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e6368a9c24c1547ebecc900b0c644e75'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b1a09f092aa936c7655c8cfeb1842d77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='64342f828717d5b8d3a871535aa8cad4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a025401207d9cd0fcba331d51cfa67b1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c4b750b780da6758273e42bc62670bc5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='28b00603a6fc29af1f454f5d80eee09c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a221590b86ab2af637c9ef481a2d737'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8965809bb3219758fa419d3e1a745cb8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1220754887494642dbe2a653df0eb31a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0f5ea19665a332cbea2e1cee7ef7b43c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3f86ac69048afd71942adcab20292e4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2d099af8b383af86186d017867927ebf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='035b8c358c89925ad3698a912e8f6c9b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8d38059a643c5a27bc1cf0ce7bb502e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='605482434a45d24f812966c22f65cade'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b6d3649eeb7f58870b6b3877b93e088a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fe4f34490a740cca9ba109053e80ca54'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2674f2088825136e37180d68573778bf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ba53896993a6e054a73e40386ad3065a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a99b25ad24d903934d7cd9c6ec5607f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f2141e0880ead69629db135b0122f0ed'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='102c848d206c55183778b84be0db938a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d413a2ece22c56593733625c1cf668e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d57b6fcac7dbdda6bc88a28a20fc531d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1a3cf72fa276c939ae6f21378be50b4c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='afed705084a674625efeceaa92359af7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9c224d89efac47338c53d2e7e75b16dc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a2e69bfd5c8e13af3c3e76c23131ed2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7cc0649e8057af446b3b78bdf2d98e75'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7d3ace9a7de9478adc125bb7444999e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='feea29b447693172556bdfd2bdbfb854'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='341fbe7d76516bfa2495016ac860f60d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a19c13079ae1bba31b62a8e5f25099be'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3098de1280bdb2b3f25be3ed6338872a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='656fbe79aae65f7285264b405c0f77df'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c796559f6a5672fab7c07fbcb9871804'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fe13231da3ead8445dc5e1a06f8f5e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e2c07309f43547834f1be8b762cb5815'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0e03cc616b8dbc11e214f8f6c7b751f5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e379bdb7e1f73b347b64890da6464338'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cbaa91f08a489efbd2551522ae35592f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='699359fcfbf44d704caba917f3e717b4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f332ab17b5cdf477962835914fa68380'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0186be3944ff2cf97a2a1f995b602fd5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='48dffd33b43d5ef7e9f76cd30dbb0636'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4fda6a1de7e97749b5d2f964035815d6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2529ae9d237f88d74fb6f5586168bd3b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92c48b6ba65f89e4b3a1da8867410dd2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4fa3885d6ea4a30c7a7fb73aaf8af957'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='766e0f04b4a6bd233cde55d27083a6d9'; ");


}

if (false) {

// 2024-10-02 20

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='988a27448d1684cde716260967c30bd3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f500a8b0f875b73c149062fc46ff9f0c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b4f5c4ea2008df8a67f92251c2c3f360'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b63959c684ddd9cc8780527c2a58b316'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9e46eb400b8d20f49d0ec2196b039f05'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6439cc61cf589c9713acf134e6c12fbf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8d046a542fc0123e29b358441d47cb63'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='13ec2bd5122cc7de36a1e4f0286eb2d4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='765df3b5bb00e006dd8bf8652ba0f85d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6d5eb44f98b4997e9c09a0db69528f07'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c2b8967d06caa0392f777ddc65042cb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d586f81b0d989e072ddf448bbdbc7ccc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eac132357e7c6d5ce8c70f6bdeeef2e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='85e420f8b2ba66684ba243ce086f1dd6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5edc3df9509422515ee1225fbd53e38'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fd3bc7b124d7b89d94042ee6d4dd57c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9897f75607a06f849caa6d6f2372e5f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d32c26226d89a614efa927bd8ab0f477'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5ab0602cc7fc2b7fabe69a2a50f0a526'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d0cc4d44e91402d5479cfd0bbaf2d081'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9564761d7b5b6803ded66ee47a445d2e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a880b7f13bb2a3a2acddd457cb4eb67'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7663201ff2c54e21ecac76215a5171ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1b6927c4b0ce4d7c9e94fd01a41eab7d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5c77cd12609503e5a6d7717797de23fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='42a120d552a013e059ded0d138cef741'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3dab5f45a25a8e616df6b56c57bc5799'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb6d78d9df1a941340fd4bfa288261ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e06666754091dcc8d73b50d9a4a2099d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e75bc835ae04a071f7fa07f957747b4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5d5349b9797cfbb0772bc164fadb2a2e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='90b13c5e4419ae540976e154a73edf33'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4dce687abce27eead3a413856a5aae52'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a62e886bd0fdcb2fb9a7876606018082'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dd9e3c253aa4bf260f2a86c3ed44b19e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7587c03929590dd7fa6a6fb0a92b9bdd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5d2d01978bf0d33065aabf645e976377'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a23a05cc6a40d8fa571c7bd30d01ddd4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='55904511abe61f3841de271c2dae215c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='464460095d939e1f33005ce175ffe793'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f214f294f87f4a65704f348d9475b9e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e2223e386b17628c11535cf4fc46195c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ebfee2e41ab8493975745529fb80d476'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='076bd24b25e07e7f091333cb17ce4eeb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6a17b02db284b3ffc34bf80701ffaa6a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7580e9e17d760766dbf236e7644a129c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5368a229be93ca534b0458ff7e1328fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='959eb0acbb614d8f3cda93b555865dd9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e675625706e378af7cfbc2a68d88026b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86a5f7cbd08a21803aee8a0b3a3d556c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f45987cb1cdb21fb094d9e56abbeaee9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dfc9f5c00222ade7e8fc15b20da4d3c0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='99bc18a1b9fd74ab149a3321488ab74c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e470a1c42fdbd78044cef204c2f469ff'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0a5a9f490360e180acda4648fdf375cc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='638e4a4dfa4c48ff87b09f3646f8b1df'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cf1f9cf69c6447ebffd0cd50514da8bc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='252c9f61b78dbfffbf28fbcc8922f651'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1864eb0357feb44d5ccbb6ed257233ed'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='95ed3da18d613ead6aa47dbe80f64e55'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='616e8c62b8bf51307a9272fcc0bc18b9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dbbeffb755f9d7e6b3b932d8d14cdba8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='57d06c7cbc806dc5f320de729a1c0e91'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='52cb0c16d3595885797a829bd1aa8068'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0666f2ae9fdc59650fe94fd093a8de16'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7eebabddc46942f5a34bf80d6ed5addd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5278d52d0e1a93abf5cf27da7eed9e9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7a119250dadfdeedf6a195541edd7ca6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='439bd46887f576284a8aa3724b0103ac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5a7934ecfc3c669570feea584ade30f5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b87cbf529511cd334ea114030131038f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4faef823d9fd1b440bfe459f364222b8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='976c4cbc0c5072fe8ec7ebd21cc0ee12'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9c07facf0229bf4533512af7c40731e9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='88b30ee8acd858c708155ea241680803'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5ed9ec363d5c3369b142fc16b512f581'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3103f0f29ac99ec1f9f5c4f58d41b1c2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ae8d74614f253927b8405eaebcfee867'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='69196e374987ba00a99ae92eb901102e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b58de1ff1b1dec09e42a94567d4afa10'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e28384f6b35d8dc556b4171e25d72428'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='00fe561a2d65f9a00bdb4b7a3b066f4f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='085c345ccc0aef739b989678e70808a7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='814319d931a610ccc824b748c4a1bfe2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a2e5dac38076089b82115344494d995b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='37ccaae8cd138ad420911f9467344668'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3a2fad4331a6b31edceaace44302bb91'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fb4929284c6b5a8e904ed3e40500cb6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0bbb34ae48b254a93d1d5116c400d737'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8cafdad9582b5b135c3a56fc5a35350f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1e34011ccf947c0faa55977c6efecb5a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c0e48f3fe90e4494f0b0f792d66e9f6d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2153fb4eec0392aba43269e43e9e71d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='34573e28424c8931d0fd195e05a0592b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6d58c7fa1e31a9d140605a15ad031bf1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ceb9954711d488c2afe6b8d562c93b30'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='abb626f9cfe7b2144e98b0eca726f07d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b3df7af9d692b8f654edd167e13e3745'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='289ac6e1c45cc18027fa77bbce34cb3a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='21b0563339d5668b5d0133093d501521'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='05bdc1c1261467c57fffcae5b74e4fdb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='da781c09f7651e778fac743919e2ccaa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f1040f254040d21ea709213d20d73538'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='73cb7170f0868f1aa8b58f5d4d2dbeb2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='81820c1e248c3cc86a40bf0f17a3848c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='421267cc87d88082725d1b156bce517b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0e9c513bfedd1f6744dd7ff575b0daae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aff1fc5a39eb790f2dad4d9cf77b5c8c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='704d06ae980b53e3c2c3f69d78e67252'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb224b79c83540f8f1d564247e6ad632'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='78957b8bad1c88785f2b485fb4e03ff9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dc58a6292629a3ac6a3db98a8cde9ccf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e046b5a018c7dafef0ef582e63f0349c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='72026fb3716c3a6afda66425b18ddd10'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c41d8b22e852f8086dcf168ef8cb6319'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='62beb41763e771f7caefbc6db6b1ad3c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='db618ab78702499c299f1841701b4dae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7c3eb17558b336fef74887e80749c483'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='402759237bb6f1bcd2f747c69e875b68'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5c172340be2b71bd7d9900af61b8e48d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='119d066fecc343ef728f1c639a42f319'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4fae6579a2418aefe37d59f19eda2599'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='21694083da51d4be180ee6cd7aec45f4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='db556cfad607d4656a6d3d33742f0d8a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c29e9ca0ed63d68b5e14cd474d5eb82b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d5322562f5a9b978bffd6f6011369a53'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ce02e2ebe04d68992bfe457d70b91dbf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='63c70e214224d7dec64fafdb28ff2c82'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='42e4d63b84f40895a22b59a33bc51b2e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='06e803ac8243d032b6afdbec2d75335c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4b185057712026bf94bacc53b51fb0ac'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a98b87af34785a4d0789d20cf33e1b7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0eb3bd34270928b1e58adabe745e71ea'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3dd799260b94b918d923fc30ff849dd0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cbb407f3572f288eed72ba72172eedc4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='231b68390fdc884df91c7098b33509c2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ecf45526fff8c856154a2d8d86ae6289'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c3b1c16be6b1fa6c00352964eab1ba31'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='00db46873748a9896db9c449081a95e4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='50f35731188a5bbae6c968fdced446fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9b86644b76ac4de0bbbda986af92cd01'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2334dd4c605d7d4fa17571299ec3530c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0a3df4a8eb6abdd8e197e238704e49ec'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c37ac6ec0a30e523d3a56e1d38d1786c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ec140af4a32dbd7d02f2d109f7de555e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0f69bb5558ef02d70cb6e9262f0506f9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3d746018ffd80b1cfe55c53f10a73f92'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d3f5d2c7b6726a85d26ed8a53be8a1fb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='94b906bfead1b9dcc5b951435b00e7f3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9708292c841114ecbaa80e9b116f792a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='18d2af8620caebc0230d3d647260d1d6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2b2461e8d3d04b832022c9f2d7d255e0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='efd47f55dfba016ab6db69a3ea313856'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='97f35eeebbce6467625967250ea6bdae'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4c2babc996953540412e797e876fb92e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='872b5838a0e6186a53dda05b6cca9e9c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='420b6047ec0d25061f9c232bf53c9857'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad5ae8abc6929971f2d9a4ca16e7be0c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e8b32d7e87190343322a602efad5319d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5402ccbcd6cbe1f80d7c78be5b4d8fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='21f85f55a50f32289073cdc9f71067a9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2be9247fafda2a4ca2eedc314ccf220e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='12fe03ef7ecf5d07d945fc66fcef953d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb80c2c00fad97d3ca57abe7253a199e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='35e30e0d5d5460253016a5c4ab104546'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f0dd193b1ab4c1f1bcdb75c7f59d7633'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2bf3913f53a5836d53727f3808e9c469'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4a7bc2cdd68cfdb53fb14ae25ab53c1a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bc30a5bff2b5d6d3427d936e615fe9fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0cd8a6042968c88608eaaef602c6bb81'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='794ca7c75fb0b716b2bcc2ea944437fe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='738021e2b8b8a25b328c6843980b3fbc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9919fcaba9d90c91ccc761b630c30b0e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='db04ef87840e43955375754d069f65e2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a16e71925d17f2bdd7d5ddf5460da28e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='335fd592799094ae6d5ec5ce68f3e335'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a0ad174d69bd3c394c8238a4402b841a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='938e727347937fe0c70db24db9b1eb36'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='401ecaf49b10b16fdd5688e8d6a7fd4e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c304ce55f163c96e5bd509ddb21d7c8f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='58b9744e27c32375fc2273a8eae9c7f0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7356cfad58752e14fabaeb2ebbdd1493'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='164c5c8c2ea8379c993e8864ec8c7446'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1a419024f9d772402213932491bed4f7'; ");

}

if (true) {
    echo "2024-10-01 20 pm";

show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c6611af5e19987140bd7c4fccdcebf69'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e6f36247bc8a75a287de82bed781679c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5c7d2708c9d3934931c97cb9b6c5a9ed'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c394eb2c6129004c0684bdd28fca878e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b25e1a58a13b0165c3ae6114431c9a56'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5063799e62a6b2bf3434bcd582c20618'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c398891b9c27c2d5024eecef647ff83c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='89ea11f1d6e64b452dc3e8a95ff47476'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4cd86f7ad57d1e3c607a06f10d2b0772'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='de942afb86cbaf945c74801702e8b0a0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='618483a584953d5bda2ffdf35804a739'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8a6d6ac7d513738a4b59b8f69f44aa80'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='04b5a218bf7f802ce988d95ba0d82d7d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='936be78c463a0178747f1db769d4e1de'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b764a2d8845cfe4682d96f1dedcb8354'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='add7a1afd865a496860debadff6badef'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='35f350f30f4e8fbff0c28db90e40c3fd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4199e6ee0dc5fd7ccec901cc8be64bb0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='01602bd0ee7301c5cd2fb1ee3ef9bab2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d431a8d492164efbfddf8b3bc710ae49'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='da4dd3938f6449092bdc9d1e1f95db05'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5e7ec12b6a3264b1d536c837f2510f1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='172e21b11c30a7204eb335dd4a87a886'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a0a87cd175fef0dfe314e4cbeac7016e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='719a6a5ebbbb4ed36587e74b2c9628ee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d17cc0b3d0cddbeb276260fbf2986316'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='937ff4739d5171f5607115ca475a665e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c6a7cbaf5ee00d2391fe8b1dc0481093'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='773cdc836db63aaf7bf3f8bde1ee1e83'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='dff142d31aa8602222187407bfaca434'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6fdfcd3cc0f7161f2dbfde5dc39bacb6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='290345d50a9f7254dd4ea02b067aa24f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7e4737459443ad2c4852252c31127fbf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='26b4d343befae350bc8ec68e9cc9cd38'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c76cfe77481ea779eb8d017a99620e3b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='179ae202108609ccb19a8ed3d97c493d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cdbb73487c229b0fff73e1b2ffbf1947'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fe94e593082e34dcadf897516d51689b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d24043dca843efee7557dc50f2dd92ad'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d2a26fc8e27d898b7582b83d702c76d5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='50189f477f387bb7f58fd32340990000'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c463a223d55067b2931f903cbeb8116c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='116bcf99099c7c798d31b236986c8fc2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='400c15389fd82c6942588cbce89aceee'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6e2e49008dde60e5e697bb7d848c575c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='03b82b593807d48dfdd8caa0cd9f3ddb'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b90fdef20e655eca91b283aa10d32d53'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2c3495765a57078893be5d82bc090d1e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c8a64b0bd466953d5da9468105a23ac5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a41edc2d402728b0f361a93e4bec3ad4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c9088d42ebd79b457f85aef7314da9bf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7387356ffdabf27855ad4dcc2daab869'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9a539c89fed2f040446709afbe370030'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5bf51eb368326526e8ae553d084fdbf8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4b7231dcd554c2ff422582113ca0f115'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b7823cc0303ff8d9fdf4b938718b3226'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d7c22f16f491151acf1285d3a5308ec9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7f4be6af53daef5ade3cc2f9e5523e27'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='167adc34bee3344847e670b7ac6e58e5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86e9c10a25ed249ba004b0aa1bf6b33b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6380c7c2e58cf6d89026ac847daa569a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='92e1c6b465996cdc7290a017155b3448'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c34a06f220d23cca4ed7fa6f4dd6ff0a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8cfb78a2820e73c44451b68475e1cbed'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='cb4f382aebfabb61a9ca86e0edb9a9c5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='12c2f6682f1afeb9f4386eb39bffa098'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2e140aa404bb4b4186d770ce154d402'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5259b6014747fd729bc362c9c9ced171'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='63a8f73dc31acf2fa60c21556b4dfbe6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6b84fd1b6679bc9a107bcc78ff15afff'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa315d4b0bbcfc99f66d6772aa061f49'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='a3836f5192fcd3385c35116b96a440bf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4784dd2a16291e958fdfb064aaaa1afd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c4382bb97894b9c8a3914b600294ded5'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='59f3d9e60b6af9c2afcaf11a83c948e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1aa065eadc26d9d0b2544915b18d118b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ef2fc6eeba2ec3465777286679122ce1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e12c8e053e45caf2ebb481cefc7aaf33'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='faa0f8c590518a69f3efcba94b39f693'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='60af8d2571e1b23012edebb5ed29036d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c9c030633eaef721c3c7ddfa79c3b4c6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eceaa32059e6f93e7dd74c47a93e7e90'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0e765cdbcd17618950fc8d4b0de09c83'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='89a5f3ffb04580a224f93e0a9b00c063'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f220e6c6ba2f3b4a275b8ba56e723072'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ce87981b6990b4a087850419a9658f03'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f5673cf0d4483ed296c7dbb62edb02c7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa57b9f8af3b82b17d89cae0d2e68bf8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2fcca950fc6da06c1245fe4c0cfe22d0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d9efe319b7ec3302d7cca95d5c64cb7a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='63d6d544eba93500c13f29bbaa6f6385'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ff7c490010ae415a8c1a077261f9fa58'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aaa1bfe02707072899b92bae5624926e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='28db0f34f5429c7db73194ff61bf4360'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='94b724998a45141c5f4eea2c773802f3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c2e8695aaa99d2e73deb6e6051f89d8e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7dd564ca36ec2fa2a6f5337e68773f56'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fdb72bcfba4d7a20dea861ff72558768'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ffed545c8b91e91d44b1637542c56b30'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b9d3b3ac02237bc0ab864e1cf58b72bd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b8b22f9cca91453dfa52fe4f8af1ec45'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='2f235896ba4c0d93000730730dd5f116'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d3f363c0c6a0b5ad8beb6cd7a3f2dee7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='06fed7a0438c62af6189086dc294cefe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='04e0d2d38d5827e7955d87809d5f087e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5090226b245c564bbc19a3e1b2254c4a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f3f36326a2f4d81c6652c2db7eee1701'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3a254f94171e920e81e8124d32e6738a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='df7625c24b5da002d9c6b5547ad56491'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='86a3ef2777a464096b85956a6bdcbd5e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='eb54b2627d235c34be60a26c92516f86'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7392c319b41fb1cc3d7ff33eab00731e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='228012ae338f874a4076e572c80aaafd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fa8d5c0801cd65e5f99a70c7b5779c63'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='58486ef0a2ff025ece20a93839528cd9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8e08e144434db872c4a356b90cb2722b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e2b69bfa6fa98e15e0ec28e2323eba37'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1c2a31d629b5a48bf0e2b91e9c1f3d76'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c8ceedecca08707cbd19a54d994e0eaf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0ed15cf105f8ff316ae119a9d51e8aa0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='42ce12d0110e25111f29a65dc37d8983'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5d14cab846a3cebb1e0cecee24bc632b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fd7ca5946998a936677aa2e0faeb1104'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='5e44f425c7358cd460b8bed007115f87'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='90656e4f6f8b4f0da4f2956f85e66ad0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1e7a9a25bd1cc8c9dd4ad72ad59af8cc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='af940c6edae27a85f69116b2022e4f1d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d825453caa931e1711d72e0e72dcf889'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b29f4a8c584777a0415d275946450e43'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c0313a58c2930aeed8bfc3a0a45c26e3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='612bb30240211a39b960d3d89936add1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='ad6c73c36ba20b33ab55572cf6c3f3e7'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='578a213b0a528ce55f5798d01a8584cd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9cd47617fe88241041aad969578165cf'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9246bd4548157cb31a9282628d2b630f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='740dbaebd642246992013d5787c3b6a2'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='3cf8f9ad34b56d967a914744ccdd6203'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='00dcb6d40bb144d927f40a7da98e3574'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='34df4235543b33e4487ef144be290585'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='84d65ebbbaaa52714915c0d0e941902f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d6d0a2e279892d89c4cdea6810a5372f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='82c25718da5884b8f864cb0a3c6835b9'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d990fea597970e0d79ea50267bdf749f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1ce317411510e3954498f6ae2b2887d1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4290876d6a03febd2e16ec7cb473a9f6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d0e9a19f477c83dd81cc42654cc1a5fc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9ced8d5a3edc8aff07dc2e0fde1306b1'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='942fab1f64d8d1266e90cec21126fbfe'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='52dc354aafcfd38ca1832ffdf59dda77'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='75c42d3698a52d8d19feb0307e12bcc4'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='521809e073eb15a3f75755f2c9147b5f'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8605ee25df758494d143b79c8466d362'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='6302c448f616bd085ba5daf6cfe15efd'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8fc78213ad22ad673375f671fa6cf43d'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d2fa08588ec368fc240785f55ee3c8fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='0a8ff8467e6a846a790757975c5a4214'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='188e76b4c6ff63066e2abf2056123698'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='46ae600772cece9704d0bc655cbdfc38'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='4252f5da5e1f8d24c3f874056db339fc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='bc13046dfa7b50741783418b17325c12'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='79fd17ad299a37b2824547d69952398a'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='1fa02ac0ac89393eecbd5616ceb36094'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='aa1cb183b1e08d130ecd2bc5a7c1419e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8e65892af1617a41b8b36db23843d0b0'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b6afb27ff030382becd28544eb631e69'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='8fbfdd5f725d856f3cfd32114f123f76'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d91f1ad16fdd5a0776c79e3a25ed6aa6'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='715377e40985eb64ff058a076d67ebbc'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='053d46c28da9afd5c3a8a004e0a3174b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='9f7988f1d25420eee59b448172f7dea3'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='7243fb84a5160733884591662536617c'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='624aa108d181748070150990eed28876'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='72f600f2852031db930fa98ec85207ab'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='167444645ead9e126ec385e98a0f8132'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e1d119a8672e5beb5992a45bfc71a106'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='d16e98e913e5c69fce73ad07c19d436e'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='fb9111c1f22a0a8412d64c93abdf84b8'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='382e411c2fc319a0a438af0f63eff0fa'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='46966a8b4ff52de64d975a800f5bb54b'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='e8ad928bd48988c0956be50aa6997f45'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='c38409b957348f1c61d671d7f7846b65'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='b6f4ceb628aba443aac769989e7dd189'; ");
show_record($conn," SELECT * FROM `golf_fairway_booking` WHERE auth='f43dbd782180a5173be9f9d193fe690e'; ");



}

$conn->close();
$conn_1->close();



die();


date_default_timezone_set('Asia/Hong_Kong');
$date = date_create();

echo date_format($date, 'Y-m-d H');


die();




$id = '722';

$sql = " delete golf_booking_buffer where src='$id'; ";

try {
   if ($conn->query($sql) === TRUE) {
   } else {
   }
} catch (Exception $e) {
}

$sql = "
	update `golf_fairway_booking` 
	set 
		`begin_hour`='16',
		`end_hour`='17' 
	where `id`='$id';
";

try {
   if ($conn->query($sql) === TRUE) {
   } else {
   }
} catch (Exception $e) {
}




die();



















function check_buffer_count($conn,$data)
{

    $id = $data['id'];
    $key1 = $data['booking_date'];
    $begin_hour = (int) $data['begin_hour'];
    $end_hour = (int) $data['end_hour'];
    $p_selections = $data['p_selections'];

    $buffer_count = 0;

    for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
        $hour_int = ((int) $cursor_hour);
        $is_half_hour = $cursor_hour != $hour_int;
        $half_hour_mark = ($is_half_hour ? ':30' : ':00');
        $key2=$hour_int . $half_hour_mark;
        foreach (json_decode($p_selections) as $key => $position) {
        	// echo $position.'<br>';
          $buffer_count += 1;

                 $key4=str_replace("position_", "", $position);
                 $sql_1 = "
                 INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
                 VALUES 
                 ('$key1','$key2','$key4','$id');
                 ";
                   try {
                       // Execute the query
                       if ($conn->query($sql_1) === TRUE) {
                           // echo "Data inserted successfully!";
                       } else {
                       //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
                       // echo "SQL error 222 $sql";
                       }
                   } catch (Exception $e) {
                       // echo $e;
                       // echo "Exception 222 $sql_1";
                   }


                 $sql_1 = "
                 UPDATE `golf_booking_buffer` SET `src`='$id'
                 WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';
                 ";
                   try {
                       // Execute the query
                       if ($conn->query($sql_1) === TRUE) {
                           // echo "Data inserted successfully!";
                       } else {
                       //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
                       // echo "SQL error 222 $sql";
                       }
                   } catch (Exception $e) {
                       // echo $e;
                       // echo "Exception 222 $sql_1";
                   }





        }
    }
    return $buffer_count;

}

function clean_and_check_booking($conn,$id)
{
	
	// $id = '989';

	// $sql = "
	// SELECT * FROM `golf_booking_buffer` WHERE `src`='$id';
	// ";

	// $result = $conn->query($sql);
	// if ($result->num_rows > 0) {
	//   while ($row = $result->fetch_assoc()) {
	//   	var_dump($row);
	//   	echo "<br>";
	//   }
	// }


	// SELECT * FROM `golf_booking_buffer` WHERE `src`='708';
	$sql = "
	SELECT * FROM `golf_fairway_booking` WHERE `id`='$id';
	";

	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	  while ($row = $result->fetch_assoc()) {
	  	$count = check_buffer_count($conn,$row);
	  	// echo $count;
	  	// echo "<br>";
	  	var_dump($row);
	  	echo "<br>";
	  }
	}


	$sql = "SELECT * FROM `golf_booking_buffer` WHERE `src`='$id';";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			var_dump($row);
			echo "<br>";
		}
	}
}


// 1003
// 927

// 1800
// 9 bay
// clean_and_check_booking($conn,'1003');
// clean_and_check_booking($conn,'927');




// clean_and_check_booking($conn,'1046');
// clean_and_check_booking($conn,'1047');













$sql = "
SELECT * FROM `golf_fairway_booking`;
";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
  	$count = check_buffer_count($conn,$row);
  	// echo $count;
  	// echo "<br>";
  	var_dump($row);
  	// echo "<br>";
  }
}











// $sql = "

// SELECT `p_selections` FROM `golf_fairway_booking` WHERE 
//     `booking_date`='$key1'
//     and ( CAST( '$begin_hour' AS UNSIGNED ) between `begin_hour` and `end_hour` )
//     and ( CAST( '$end_hour' AS UNSIGNED ) between `begin_hour` and `end_hour` )
// ;

// ";

// $result = $conn->query($sql);
// if ($result->num_rows > 0) {
//   while ($row = $result->fetch_assoc()) {
// 	$p_selects_arr_1 = json_decode($row['p_selections']);
//   	foreach ($p_selects_arr_1 as $value) {
//   		if (in_array($value, $p_selects_arr)) {
//   			echo "Hit $value";
//   		}
//   	}
//     // var_dump($row);
//   }
// }




die();















































function insert_statement($i,$type)
{
	echo "INSERT INTO `golf-locker-list`(`number`, `type`) VALUES ('$i','$type');";
}
for ($i=157; $i < 164; $i++) { 
	insert_statement($i,'G');
}
for ($i=1001; $i < 1025; $i++) { 
	insert_statement($i,'G');
}

die();


require_once 'account_variable.php';

// echo "3-";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// echo "2-";
// Check connection
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}




$sql = "
	UPDATE `golf_booking_buffer` set
		`golf_booking_buffer`.`src`=(
			SELECT `golf_fairway_booking`.`id` 
			FROM `golf_fairway_booking`
			where `golf_fairway_booking`.`booking_date`=`golf_booking_buffer`.`date`
			and `golf_fairway_booking`.`begin_hour`<=REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '')
			and `golf_fairway_booking`.`end_hour`>=REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '')
			and `golf_fairway_booking`.`p_selections` LIKE concat('%\"',`golf_booking_buffer`.`position`,'\"%')
			limit 1
		)
	where 1
;";
// 	// where `golf_booking_buffer`.`src` is null

try {
	if ($conn->query($sql) === TRUE) {

	}
} catch (Exception $e) {
}

die();
















// $url = 'https://riversidegolf.com.hk/GolfBooking/download_report.php?S=1'; // Replace with your desired URL
// $response = file_get_contents($url);
// echo $response	;


// UPDATE `golf_booking_buffer` SET 
// 	`date`='[value-1]',
// 	`hour`='[value-2]',
// 	`position`='[value-3]',
// 	`src`='[value-4]' 
// WHERE 1


// UPDATE `golf_fairway_booking` SET 
// 	`telephone`='[value-4]',
// 	`octopus_no`='[value-5]',
// 	`booking_date`='[value-7]',
// 	`begin_hour`='[value-8]',
// 	`end_hour`='[value-9]',
// 	`discount`='[value-10]',
// 	`p_selections`='[value-11]'
//  WHERE 1


// UPDATE `T_BOOK` SET 
// 	`tel`='[value-19]',
// 	`card_no`='[value-15]',
// 	`date_begin`='[value-8]',`date_end`='[value-9]',
// 	`order_time`='[value-5]',
// 	`pos_no`='[value-7]'
// WHERE 1

 ?><?php 
// Close the database connection
$conn->close();


 ?>