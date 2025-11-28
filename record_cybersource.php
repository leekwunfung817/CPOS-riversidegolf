<?php 

    session_start();

require_once './logger.php';
t_log('begin[record_cybersource.php]');

if (!isset($_SESSION["management"])) {
     ?>
    <script type="text/javascript">
        alert('請使用您的員工帳號重新登錄\nPlease login with your staff account again');
        window.location.href = "./";
    </script>
    <?php
    die();
}
 ?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
<?php


require_once './account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




// Execute the SQL query
$sql = "SELECT `transaction_id`, `decision`, `req_access_key`, `req_profile_id`, `req_transaction_uuid`, `req_transaction_type`, `req_reference_number`, `req_amount`, `req_currency`, `req_locale`, `req_payment_method`, "
// ."`req_override_custom_receipt_page`,"
." `req_bill_to_forename`, `req_bill_to_surname`, `req_card_number`, `req_card_type`, `req_card_type_selection_indicator`, `card_type_name`, `message`, `reason_code`, `auth_avs_code`, `auth_response`, `auth_amount`, `auth_code`, `auth_cavv_result`, `auth_cavv_result_raw`, `auth_cv_result`, `auth_cv_result_raw`, `auth_trans_ref_no`, `request_token`, `merchant_advice_code`, `auth_reconciliation_reference_number`, "
// ."`signed_field_names`,"
." 
    `signed_date_time`, `signature` 
    FROM `golf_cybersource` 
    WHERE 1
    group by  `transaction_id`
    order by signed_date_time desc 
    limit 1000";
$result = $conn->query($sql);

// Check if the query was successful
if ($result->num_rows > 0) {
    // Start the HTML table
    echo "<table>";

    // Print the table header
    echo "<tr>
            <th>Transaction ID</th>
            <th>Decision</th>
            <th>Access Key</th>
            <th>Profile ID</th>
            <th>Transaction UUID</th>
            <th>Transaction Type</th>
            <th>Reference Number</th>
            <th>Amount</th>
            <th>Currency</th>
            <th>Locale</th>
            <th>Payment Method</th>
            "
            // ."<th>Override Custom Receipt Page</th>"
            ."
            <th>Billing First Name</th>
            <th>Billing Last Name</th>
            <th>Card Number</th>
            <th>Card Type</th>
            <th>Card Type Selection Indicator</th>
            <th>Card Type Name</th>
            <th>Message</th>
            <th>Reason Code</th>
            <th>AVS Code</th>
            <th>Auth Response</th>
            <th>Auth Amount</th>
            <th>Auth Code</th>
            <th>CAVV Result</th>
            <th>CAVV Result Raw</th>
            <th>CV Result</th>
            <th>CV Result Raw</th>
            <th>Transaction Reference Number</th>
            <th>Request Token</th>
            <th>Merchant Advice Code</th>
            <th>Reconciliation Reference Number</th>
            "
            // ."<th>Signed Field Names</th>"
            ."
            <th>Signed Date Time</th>
            <th>Signature</th>
        </tr>";

    // Print the data rows
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["transaction_id"]. "</td>
                <td>" . $row["decision"]. "</td>
                <td>" . $row["req_access_key"]. "</td>
                <td>" . $row["req_profile_id"]. "</td>
                <td>" . $row["req_transaction_uuid"]. "</td>
                <td>" . $row["req_transaction_type"]. "</td>
                <td>" . $row["req_reference_number"]. "</td>
                <td>" . $row["req_amount"]. "</td>
                <td>" . $row["req_currency"]. "</td>
                <td>" . $row["req_locale"]. "</td>
                <td>" . $row["req_payment_method"]. "</td>
                "
                // ."<td>" . $row["req_override_custom_receipt_page"]. "</td>"
                ."
                <td>" . $row["req_bill_to_forename"]. "</td>
                <td>" . $row["req_bill_to_surname"]. "</td>
                <td>" . $row["req_card_number"]. "</td>
                <td>" . $row["req_card_type"]. "</td>
                <td>" . $row["req_card_type_selection_indicator"]. "</td>
                <td>" . $row["card_type_name"]. "</td>
                <td>" . $row["message"]. "</td>
                <td>" . $row["reason_code"]. "</td>
                <td>" . $row["auth_avs_code"]. "</td>
                <td>" . $row["auth_response"]. "</td>
                <td>" . $row["auth_amount"]. "</td>
                <td>" . $row["auth_code"]. "</td>
                <td>" . $row["auth_cavv_result"]. "</td>
                <td>" . $row["auth_cavv_result_raw"]. "</td>
                <td>" . $row["auth_cv_result"]. "</td>
                <td>" . $row["auth_cv_result_raw"]. "</td>
                <td>" . $row["auth_trans_ref_no"]. "</td>
                <td>" . $row["request_token"]. "</td>
                <td>" . $row["merchant_advice_code"]. "</td>
                <td>" . $row["auth_reconciliation_reference_number"]. "</td>
                "
                // ."<td>" . $row["signed_field_names"]. "</td>"
                ."
                <td>" . $row["signed_date_time"]. "</td>
                <td>" . $row["signature"]. "</td>
            </tr>";
    }

    // Close the HTML table
    echo "</table>";
} else {
    echo "No results found.";
}

// Close the database connection
$conn->close();
t_log('end[record_cybersource.php]');
?>