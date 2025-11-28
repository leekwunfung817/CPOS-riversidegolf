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






// Connect to the database
// $servername = "localhost";
// $username = "your_username";
// $password = "your_password";
// $dbname = "your_database";

require_once './account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to generate the download links
function CashDownloadLinks($conn) {
    $sql = "SELECT 
                DATE_FORMAT(timestamp, '%Y') AS year, 
                DATE_FORMAT(timestamp, '%M') AS month, 
                COUNT(*) AS count 
            FROM `golf-cash`
            GROUP BY year, month
            ORDER BY year DESC, month DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Year</th><th>Month</th></tr>";
        $year_buffer = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            echo "<td>";
            if ($year_buffer != $row["year"]) {
                $year_buffer = $row["year"];
                echo "<a href='download.php?year=" . $row["year"] . "&cash'>" . $row["year"] . "</a>";
            }
            echo "</td>";

            echo "<td><a href='download.php?year=" . $row["year"] . "&month=" . $row["month"] . "&cash'>" 
            . $row["month"]
            . "</a>"
            ."  <small>(".$row["count"]." record(s))</small>"
            ."</td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data available.";
    }
}

// Function to generate the download links
function CybersourceDownloadLinks($conn) {
    $sql = "SELECT DATE_FORMAT(signed_date_time, '%Y') AS year, DATE_FORMAT(signed_date_time, '%M') AS month, COUNT(*) AS count 
            FROM golf_cybersource
            GROUP BY year, month
            ORDER BY year DESC, month DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Year</th><th>Month</th></tr>";
        $year_buffer = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            
            echo "<td>";
            if ($year_buffer != $row["year"]) {
                $year_buffer = $row["year"];
                echo "<a href='download.php?year=" . $row["year"] . "&cybersource'>" . $row["year"] . "</a>";
            }
            echo "</td>";
            echo "<td><a href='download.php?year=" . $row["year"] . "&month=" . $row["month"] . "&cybersource'>" 
            . $row["month"] 
            . "</a>"
            ."  <small>(".$row["count"]." record(s))</small>"
            ."</td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No data available.";
    }
}


date_default_timezone_set('Asia/Hong_Kong');

// Endpoint to download the CSV file
if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['cash'])) {
    // echo "Download Perform";
    $year = $_GET['year'];
    $month = $_GET['month'];

    $sql = "SELECT * FROM `golf-cash` 
            WHERE DATE_FORMAT(timestamp, '%Y') = '$year' 
            AND DATE_FORMAT(timestamp, '%M') = '$month'
            ORDER BY timestamp ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "cash_report_" . $year . "_" . $month . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
} else if (isset($_GET['year']) && isset($_GET['cash'])) {
    // echo "Download Perform";
    $year = $_GET['year'];

    $sql = "SELECT * FROM `golf-cash` 
            WHERE DATE_FORMAT(timestamp, '%Y') = '$year'
            ORDER BY timestamp ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "cash_report_" . $year . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
}



// Endpoint to download the CSV file
if (isset($_GET['year']) && isset($_GET['month']) && isset($_GET['cybersource'])) {
    // echo "Download Perform";
    $year = $_GET['year'];
    $month = $_GET['month'];

    $sql = "SELECT * FROM golf_cybersource 
            WHERE DATE_FORMAT(signed_date_time, '%Y') = '$year' 
            AND DATE_FORMAT(signed_date_time, '%M') = '$month'
            ORDER BY signed_date_time ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "golf_cybersource_report_" . $year . "_" . $month . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
} else if (isset($_GET['year']) && isset($_GET['cybersource'])) {
    // echo "Download Perform";
    $year = $_GET['year'];

    $sql = "SELECT * FROM golf_cybersource 
            WHERE DATE_FORMAT(signed_date_time, '%Y') = '$year'
            ORDER BY signed_date_time ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "golf_cybersource_report_" . $year . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
} else if (isset($_GET['booking_buffer'])) {
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
            `school-name` 
        FROM `golf_fairway_booking` 
        ORDER BY `golf_fairway_booking`.`timestamp` DESC
    ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "full_fairway_booking_" . date('Y-m-d H:i:s') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
} else if (isset($_GET['cybersource'])) {
    $sql = "
        SELECT 
            `transaction_id`, 
            `decision`, 
            `req_access_key`, 
            `req_profile_id`, 
            `req_transaction_uuid`, 
            `req_transaction_type`, 
            `req_reference_number`, 
            `req_amount`, 
            `req_currency`, 
            `req_locale`, 
            `req_payment_method`, 
            `req_override_custom_receipt_page`, 
            `req_bill_to_forename`, 
            `req_bill_to_surname`, 
            `req_card_number`, 
            `req_card_type`, 
            `req_card_type_selection_indicator`, 
            `req_card_expiry_date`, 
            `card_type_name`, 
            `message`, 
            `reason_code`, 
            `auth_avs_code`, 
            `auth_response`, 
            `auth_amount`, 
            `auth_code`, 
            `auth_cavv_result`, 
            `auth_cavv_result_raw`, 
            `auth_cv_result`, 
            `auth_cv_result_raw`, 
            `auth_trans_ref_no`, 
            `auth_time`, 
            `request_token`, 
            `merchant_advice_code`, 
            `auth_reconciliation_reference_number`, 
            `signed_field_names`, 
            `signed_date_time`, 
            `utf8`, 
            `signature`
        FROM `golf_cybersource` 
        ORDER BY `golf_cybersource`.`signed_date_time` DESC
    ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $filename = "full_cybersource_" . date('Y-m-d H:i:s') . ".csv";
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        // Write the header row
        $header = array_keys($result->fetch_assoc());
        fputcsv($fp, $header);

        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            fputcsv($fp, $row);
        }

        exit;
    } else {
        echo "No data available.";
    }
}

?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
<?php

 ?>
<style type="text/css">
    html {
        padding: 30px;
        background: lightskyblue;
    }
    
    body {
        padding: 30px;
        background-color: white;
    }
    body,th,td {
        font-size: 2em;
        text-align: left;
    }
    td {
        width: 50%;
        border-style: double;
    }
    table {
        width: 100%;
    }
</style>






<h2>系統數據下載
<br>
System Data Download</h2> 


<a href="?booking_buffer">下載所有高爾夫球場預訂記錄<br>Download All Golf Court Booking Records</a><br><br>

<a href="?cybersource">下載所有Cybersource交易記錄<br>Download All Cybersource Trading Records</a><br><br>



<table>
    <tr>
        <td>
            Cybersource Data
        </td>
    </tr>
    <tr>
        <td>
<?php 

// Generate the download links
CybersourceDownloadLinks($conn);

 ?>
        </td>
    </tr>
</table>
<hr>







<table>
    <tr>    
        <td>
            Cash Data
        </td>
    </tr>
    <tr>
        <td>
<?php 

// Generate the download links
CashDownloadLinks($conn);

 ?>
        </td>
    </tr>
</table>
<hr>


<table>
    <tr>
        <td>

                <table>
                    <tr>
                        <td>
                            Daily Transaction Report
                        </td>
                    </tr>
                    <tr>
                        <td>
                <?php 


                 ?>
                        </td>
                    </tr>
                </table>
                <hr>
            
        </td>
        <td>
                            
                <table>
                    <tr>
                        <td>
                            Accounting Report
                        </td>
                    </tr>
                    <tr>
                        <td>
                            
                        </td>
                    </tr>
                </table>
                <hr>
            
        </td>
    </tr>
</table>











<?php 
// Close the database connection
$conn->close();


 ?>