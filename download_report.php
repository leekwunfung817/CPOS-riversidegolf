<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

require_once 'logger.php';
t_log('begin[download_report.php]');


if (!isset($_SESSION["management"]) && !isset($_GET['S'])) {
     ?>
  <meta charset="UTF-8">
    <script type="text/javascript">
        alert('您不是管理人員，您無法存取此頁面\nyou are not management, you cannot access this page');
        window.location.href = "./";
    </script>
    <?php
    die();
}


$show_more_days = 3;

if (isset($_GET['more_days'])) {
    $show_more_days = $_GET['more_days'];
}


$part_start = microtime(true);

// Connect to the database
// $servername = "localhost";
// $username = "your_username";
// $password = "your_password";
// $dbname = "your_database";

require_once './account_variable.php';
require_once './common-function.php';

$conn_download_report = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_download_report->connect_error) {
    die("Connection failed: " . $conn_download_report->connect_error);
}

function credit_card_csv($conn_download_report, $filename, $begin, $end, $is_response)
{
    $file_path = './report_file/'.$filename;
    $sql = "
SELECT 
    ifnull(
        (select `golf_fairway_booking`.`id` 
        from `golf_fairway_booking` 
        where `golf_fairway_booking`.`auth`=`req_reference_number` limit 1
        ) ,concat(
            'Overdue - ',ifnull(
                (
                select `golf_fairway_booking_history`.`id` 
                    from `golf_fairway_booking_history` 
                    where `golf_fairway_booking_history`.`auth`=`req_reference_number` limit 1
                )
            ,'Lost data')
        )
    ) reserv_id

    ,(select 
        concat('\'',DATE_FORMAT(`golf_fairway_booking`.`booking_date`, '%Y-%m-%d'),'')
        from `golf_fairway_booking` 
        where `golf_fairway_booking`.`auth`=`req_reference_number` limit 1) booking_date
    

    ,(select `golf_fairway_booking`.`begin_hour` 
        from `golf_fairway_booking` 
        where `golf_fairway_booking`.`auth`=`req_reference_number` limit 1) begin_hour
    
    ,(select `golf_fairway_booking`.`end_hour` 
        from `golf_fairway_booking` 
        where `golf_fairway_booking`.`auth`=`req_reference_number` limit 1) end_hour
    ,card_type_name
    ,auth_amount
    ,concat('\'',DATE_FORMAT( DATE_ADD(signed_date_time, INTERVAL 8 HOUR), '%Y-%m-%d %H:%i:%s'),'') dt2
    ,`req_card_number`
    ,concat('\'',transaction_id,'') transaction_id

    ,(select `golf_fairway_booking`.`p_selections` 
        from `golf_fairway_booking` 
        where `golf_fairway_booking`.`auth`=`req_reference_number` limit 1) p_selections
    ,`req_reference_number`
    ,auth_code

FROM `golf_cybersource_right`
where
    DATE_ADD(signed_date_time, INTERVAL 8 HOUR) between '$begin' and '$end'
    and decision='ACCEPT' and `req_transaction_type`='sale'
order by `signed_date_time` desc;
";
// ,concat('\'',DATE_FORMAT( DATE_ADD(signed_date_time, INTERVAL 8 HOUR), '%Y-%m-%d %H:%i:%s'),'') dt2

    $result = $conn_download_report->query($sql);

    if ($result->num_rows > 0) {
        if ($is_response) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        }
        if ($is_response) {
        $fp = fopen('php://output', 'w');
        }
        $is_create_file = !file_exists($file_path);
        if ($is_create_file) {
            $file = fopen($file_path, 'w');
        }
        

        $columns = array(
            'Reserv. No'
            ,'Reserv. Date'
            ,'Begin Hour'
            ,'End Hour'
            ,'Payment Method'
            ,'Total Amount'
            ,'Payment Time'
            ,'Credit Card Number'
            ,'Transactions ID'
            ,'Bays'
            ,'Reference Number'
            ,'Auth. Code'
        );
            if ($is_response) {
        fputcsv($fp, $columns);
            }
        if ($is_create_file) {
            fputcsv($file, $columns);
        }

        $sum_amount = 0;
        $count = 0;
        // Write the data rows
        $result->data_seek(0);
        $transaction_id_list = array();
        while ($row = $result->fetch_assoc()) {
            if (isset($transaction_id_list[$row['transaction_id']])) {
                continue;
            }
            $transaction_id_list[$row['transaction_id']]=1;

            $row['begin_hour'] = pointToHalfHour($row['begin_hour']);
            $row['end_hour'] = pointToHalfHour($row['end_hour']);
            $auth_amount = (float) $row['auth_amount'];
            if ($auth_amount > 0) {
                if ($is_response) {
                    fputcsv($fp, $row);
                }
                if ($is_create_file) {
                    fputcsv($file, $row);
                }
                $sum_amount += $auth_amount;
                $count += 1;
            }
        }

        $row = null;
        $row = array(
            "",""
        );
            if ($is_response) {
            fputcsv($fp, $row);
            }
        if ($is_create_file) {
            fputcsv($file, $row);
        }

        $row = null;
        $row = array(
            "Total Payment:","HKD $ $sum_amount"
        );
            if ($is_response) {
            fputcsv($fp, $row);
            }
        if ($is_create_file) {
            fputcsv($file, $row);
        }


        $row = null;
        $row = array(
            "Transactions:","$count","record(s)"
        );
            if ($is_response) {
            fputcsv($fp, $row);
            }
        if ($is_create_file) {
            fputcsv($file, $row);
        }
        if ($is_response) {
        fclose($fp);
        }
        if ($is_create_file) {
            fclose($file);
        }

        
    }

    $csvdata = file_get_contents($file_path);
    $encoded = chunk_split(base64_encode($csvdata));
    if (!unlink($file_path)) {
    // echo ("$file_path cannot be deleted due to an error");
    } else {
    // echo ("$file_path has been deleted");
    }
    return $encoded;

}

function credit_card_repoprt($conn_download_report, $type, $begin, $end, $is_response)
{


        $filename = "credit_card_report_$begin.$end.csv";
        if ($is_response) {
            credit_card_csv($conn_download_report, $filename, $begin, $end, $is_response);
        }

        $encoded = null;
        $sql = "SELECT `emaill-address` FROM `golf-report-email`;";
        $result = $conn_download_report->query($sql);

        if ($result->num_rows > 0) {
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                

                $to = $row['emaill-address'];
                $need_send_mail = ($conn_download_report->query("SELECT `emaill-address`, `file-name`, `create-time` FROM `golf-report-record` WHERE `emaill-address`='$to' and `file-name`='$filename';")->num_rows == 0);

                // Send the email
                if ($need_send_mail) {
                    echo "Tried to send email.";

                    $subject = "Riverside Whitehead Golf - Credit Card CSV File Report - $begin ~ $end - Online booking system";
                    $from = "noreply@riversidegolf.com.hk";
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/mixed; boundary=\"----=_NextPart_001\"\r\n";

                    if ($encoded == null) {
                        $encoded = credit_card_csv($conn_download_report, $filename, $begin, $end, $is_response);
                    }

                    $message = "This is a credit card daily report.\r\n";
                    $message .= "------=_NextPart_001\r\n";
                    $message .= "Content-Type: text/plain; charset=\"us-ascii\"\r\n";
                    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $message .= "\r\n\r\nPlease download the attached CSV file.\r\n\r\nRegards,\r\nRiverside Whitehead Golf\r\n";
                    $message .= "------=_NextPart_001\r\n";
                    $message .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n";
                    $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
                    $message .= $encoded;
                    $message .= "\r\n------=_NextPart_001--\r\n";

                    $success = mail($to, $subject, $message, $headers, "-f$from");
                    if ($success) {
                        $conn_download_report->query("INSERT INTO `golf-report-record`
                            (`emaill-address`, `file-name`) 
                            VALUES ('$to','$filename')");
                    }
                }


            }
        }
}

function cash_report_csv($conn_download_report, $filename, $begin, $end, $is_response)
{

    $file_path = './report_file/'.$filename;

    $sql = "
SELECT 
    `golf_fairway_booking`.`id`
    ,concat('\'',DATE_FORMAT(`golf_fairway_booking`.`booking_date`, '%Y-%m-%d'),'') booking_date_1
    ,`golf_fairway_booking`.`begin_hour`
    ,`golf_fairway_booking`.`end_hour`
    , 'Cash' card_type_name
    ,`golf-cash`.amount
    ,concat('\'',DATE_FORMAT(`golf-payment-session`.`payment-datetime`, '%Y-%m-%d %H:%i:%s'),'') timestamp_
FROM `golf-cash`
left join `golf_fairway_booking` on `golf_fairway_booking`.`auth`=`golf-cash`.`auth`
left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-cash`.`auth`
where 1=1
and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
group by `golf_fairway_booking`.`id`
union all
select 
    `golf_fairway_booking`.`id`
    ,concat('\'',DATE_FORMAT(`golf_fairway_booking`.`booking_date`, '%Y-%m-%d'),'') booking_date_1
    ,`golf_fairway_booking`.`begin_hour`
    ,`golf_fairway_booking`.`end_hour`
    , 'Pay Check' card_type_name
    ,(
        case 
            when IFNULL(max(`golf-unpaid-account`.`is_paid`),-1)<=0
                then 0
            when IFNULL(max(`golf-unpaid-account`.`is_paid`),-1)=1
                then IFNULL(max(`golf-unpaid-account`.`amount`),0)
        end
    ) `amount` 
    ,concat('\'',DATE_FORMAT(`golf-payment-session`.`payment-datetime`, '%Y-%m-%d %H:%i:%s'),'') timestamp_
from `golf-unpaid-account`
    left join `golf_fairway_booking` on `golf_fairway_booking`.`auth`=`golf-unpaid-account`.`auth`
    left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-unpaid-account`.`auth`
where 1=1
    and `golf-unpaid-account`.`is_paid` is not null
    and `golf-unpaid-account`.`is_paid`=1
    and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
group by `golf_fairway_booking`.`id`

";

    $result = $conn_download_report->query($sql);

    if ($result->num_rows > 0) {
        if ($is_response) {
            
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        }
        if ($is_response) {
        $fp = fopen('php://output', 'w');
        }
        $is_create_file = !file_exists($file_path);
        if ($is_create_file) {
            $file = fopen($file_path, 'w');
        }
        

        $columns = array(
            'Reserv. No'
            ,'Reserv. Date'
            ,'Begin Hour'
            ,'End Hour'
            ,'Payment Method'
            ,'Total Amount'
            ,'Payment Time'
            // ,'Payment Time 2'
        );
        if ($is_response) {
        fputcsv($fp, $columns);
        }
        if ($is_create_file) {
            fputcsv($file, $columns);
        }

        $sum_amount = 0;
        $count = 0;
        // Write the data rows
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $row['begin_hour'] = pointToHalfHour($row['begin_hour']);
            $row['end_hour'] = pointToHalfHour($row['end_hour']);
            $amount = (float) $row['amount'];
            if ($amount > 0) {
                if ($is_response) {
                    fputcsv($fp, $row);
                }
                if ($is_create_file) {
                    fputcsv($file, $row);
                }
                $sum_amount += $row['amount'];
                $count += 1;   
            }
        }

        $row = null;
        $row = array(
            "",""
        );
        if ($is_response) {
        fputcsv($fp, $row);
        }
        if ($is_create_file) {
            fputcsv($file, $row);
        }

        $row = null;
        $row = array(
            "Total Payment:","HKD $ $sum_amount"
        );
        if ($is_response) {
        fputcsv($fp, $row);
        }
        if ($is_create_file) {
            fputcsv($file, $row);
        }


        $row = null;
        $row = array(
            "Transactions:","$count","record(s)"
        );
        if ($is_response) {
        fputcsv($fp, $row);
        }
        if ($is_create_file) {
            fputcsv($file, $row);
        }
        if ($is_response) {
        fclose($fp);
        }
        if ($is_create_file) {
            fclose($file);
        }

        
    }

    $csvdata = file_get_contents($file_path);
    $encoded = chunk_split(base64_encode($csvdata));
    if (!unlink($file_path)) {
    // echo ("$file_path cannot be deleted due to an error");
    } else {
    // echo ("$file_path has been deleted");
    }
    return $encoded;

}

function cash_report($conn_download_report, $type, $begin, $end, $is_response)
{

        $filename = "cash_report_$begin.$end.csv";
        if ($is_response) {
            cash_report_csv($conn_download_report, $filename, $begin, $end, $is_response);
        }

        $encoded = null;
        $sql = "SELECT `emaill-address` FROM `golf-report-email`;";
        $result = $conn_download_report->query($sql);

        if ($result->num_rows > 0) {
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()) {
                

                $to = $row['emaill-address'];
                $need_send_mail = ($conn_download_report->query("SELECT `emaill-address`, `file-name`, `create-time` FROM `golf-report-record` WHERE `emaill-address`='$to' and `file-name`='$filename';")->num_rows == 0);

                // Send the email
                if ($need_send_mail) {

                    echo "Tried to send cash email.";




                    if ($encoded == null) {
                        $encoded = cash_report_csv($conn_download_report, $filename, $begin, $end, $is_response);
                    }



                    $subject = "Riverside Whitehead Golf - Cash CSV File Report - $begin ~ $end - Walk-in booking";
                    $from = "noreply@riversidegolf.com.hk";
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/mixed; boundary=\"----=_NextPart_001\"\r\n";

                    $message = "This is a credit card daily report.\r\n";
                    $message .= "------=_NextPart_001\r\n";
                    $message .= "Content-Type: text/plain; charset=\"us-ascii\"\r\n";
                    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $message .= "\r\n\r\nPlease download the attached CSV file.\r\n\r\nRegards,\r\nRiverside Whitehead Golf\r\n";
                    $message .= "------=_NextPart_001\r\n";
                    $message .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";
                    $message .= "Content-Transfer-Encoding: base64\r\n";
                    $message .= "Content-Disposition: attachment; filename=\"$filename\"\r\n\r\n";
                    $message .= $encoded;
                    $message .= "\r\n------=_NextPart_001--\r\n";
























                    $success = mail($to, $subject, $message, $headers, "-f$from");
                    if ($success) {
                        $conn_download_report->query("INSERT INTO `golf-report-record`
                            (`emaill-address`, `file-name`) 
                            VALUES ('$to','$filename')");
                    }
                }


            }
        }

}

function is_over_time($part_time_elapsed_secs)
{
    if ($part_time_elapsed_secs > 3) {
        echo "(Over Time)";
    }
    return $part_time_elapsed_secs;
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part A Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);


if (isset($_GET['type']) && isset($_GET['begin']) && isset($_GET['end'])) {
    $type = $_GET['type'];
    $begin = $_GET['begin'];
    $end = $_GET['end'];

    if ($type == 'credit_card') {
        // echo "Generate Report $type, $begin, $end";
        credit_card_repoprt($conn_download_report, $type, $begin, $end, true);
    }

    if ($type == 'cash_card') {
        cash_report($conn_download_report, $type, $begin, $end, true);
    }
    exit();

}



                if (!isset($_GET['S'])) {
?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
<?php
}

 ?>
<style type="text/css">
    html {
        padding: 30px;
/*        background: lightskyblue;*/
    }
    
    body {
        padding: 30px;
        background-color: white;
    }
    th {
/*        font-size: 0.5em;*/
        border-style: solid;
    }
    body,th,td {
/*        font-size: 2em;*/
        text-align: left;
        vertical-align: text-top;
        white-space: nowrap;
    }
    td {
        width: 50%;
        border-style: double;
    }
    table {
        width: 100%;
    }
</style>





<h2>每日交易報告下載<br>
Daily Transaction Report Download</h2> 




<table>
    <tr>
        <td>
            信用卡 支付當天 交易統算表
        </td>
        <td>
            現金 支付當天 交易統算表
        </td>
    </tr>
    <tr>


        <td>

                <table>
                    <tr>
                        <td>
<form action="" method="get">
    <input type="number" name="more_days" value="<?php echo $show_more_days; ?>">
    <input type="submit" name="" value="顯示更多天數">
</form>
<table>
<tr>
    <th>
        開始和結束日期時間<br>
        Begin-Datetime <br> and End-Datetime
    </th>
    <th>
        信用卡交易數量<br>
        Credit Card<br>
        Transaction Count
    </th>
    <th>
        信用卡交易總額<br>
        Credit Card<br> 
        Total<br>
        Transaction Amount
    </th>
    <th>
        信用卡報告下載按鈕<br>
        Credit Card Report<br>
        Download Button
    </th>

    <th>
        逾期預約交易<br>
        Overdue Booking Transaction<br>
    </th>
</tr>
<?php


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part B Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);


date_default_timezone_set('Asia/Hong_Kong');
// Create a date object
$date = date_create();
$hour_int = (int) date_format($date, 'H');
if ($hour_int >= 20) {
    
} else {
    date_sub($date, date_interval_create_from_date_string('1 days'));
}

$report_check_count_credit_card = 0;
$report_check_count_cash = 0;

for ($i=0; $i < $show_more_days; $i++) {
?>
<tr>
<?php
    // echo '<td>'.($i+1).'</td>';
    $end = date_format($date, 'Y-m-d').' 20:00:00';
    date_sub($date, date_interval_create_from_date_string('1 days'));
    $begin = date_format($date, 'Y-m-d').' 20:00:00';
    echo '<td>From '.$begin.'<br>To '.$end.'</td>';

    $sql = "
    SELECT count(*) cre_c,  ifnull(sum(auth_amount),0) cre_sum
    FROM `golf_cybersource_right`
    where
        DATE_ADD(signed_date_time, INTERVAL 8 HOUR) between '$begin' and '$end'
;
    ";
    // and decision='ACCEPT'and `req_transaction_type`='sale'
     // -- `signed_date_time`<>'0000-00-00 00:00:00'
     //    -- and `decision`='ACCEPT'
     //    -- 
     //    -- and 

$part_start_6 = microtime(true);

    $result = $conn_download_report->query($sql);

$part_time_elapsed_secs = microtime(true) - $part_start_6;
if (isset($_GET['debug'])) {
  echo '<br>(Part Cre Query Takes): '.is_over_time($part_time_elapsed_secs).' ';
}

    if (is_bool($result)) {
    } else if ($result->num_rows > 0) {

$part_start_1 = microtime(true);


        while ($row = $result->fetch_assoc()) {
            if ($row["cre_c"] > 0 || $row["cre_sum"] > 0) {
                echo '<td>'.$row["cre_c"].' record(s)</td>';
                echo '<td>HKD $'.$row["cre_sum"].'</td>';
    ?>
<td>
<form action="./download_report.php" method="get" style="height: 0px;" target="_blank">
    <input type="hidden" name="type" value="credit_card">
    <input type="hidden" name="begin" value="<?php echo $begin; ?>">
    <input type="hidden" name="end" value="<?php echo $end; ?>">
    <input type="submit" value="Download">    
</form>
<?php 
                if (isset($_GET['S']) && $report_check_count_credit_card <1) {
                    $report_check_count_credit_card += 1;
                    $type='credit_card';
                    credit_card_repoprt($conn_download_report, $type, $begin, $end, false);
                }

 ?>
</td>
    <?php

                $sql = "
    SELECT 
        `golf_cybersource_right`.transaction_id,
        COUNT(*) c,
        max(`golf_fairway_booking_history`.`id`) id,
        GROUP_CONCAT(`golf_fairway_booking_history`.`auth`) auth,
        max(`golf_cybersource_right`.`auth_amount`) auth_amount,
        max(`golf_cybersource_right`.`auth_code`) auth_code,
        max(`golf_cybersource_right`.`req_transaction_type`) req_transaction_type,
        max(DATE_ADD(signed_date_time, INTERVAL 8 HOUR)) `payment-datetime`
    FROM (`golf_fairway_booking_history` join `golf_cybersource_right` join `golf-payment-session`) 
    WHERE 1=1
        AND `golf_fairway_booking_history`.`auth` = `golf_cybersource_right`.`req_reference_number` 
        AND `golf_cybersource_right`.`decision` = 'ACCEPT' 
        AND `golf_cybersource_right`.`transaction_id` is not null 
        AND `golf_cybersource_right`.`transaction_id` <> '' 
        and reason_code=100
        and auth_amount>0
        and `golf-payment-session`.auth=`golf_fairway_booking_history`.`auth`
        and DATE_ADD(signed_date_time, INTERVAL 8 HOUR) between '$begin' and '$end'
        and (
            select count(*) from golf_fairway_booking where golf_fairway_booking.auth=`golf_fairway_booking_history`.`auth`
            )=0
    group by `golf_cybersource_right`.transaction_id
    ORDER BY `golf_fairway_booking_history`.`timestamp` DESC 
    ;
";


                $ids = "";
                $total_amount = 0;
                $count = 0;


$part_start_3 = microtime(true);


                $result = $conn_download_report->query($sql);

$part_time_elapsed_secs = microtime(true) - $part_start_3;
if (isset($_GET['debug'])) {
  echo '<br>(Part Cybersource Query Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start_2 = microtime(true);

                if (is_bool($result)) {
                } else if ($result->num_rows > 0) {
                    $arr = array();
                    while ($row = $result->fetch_assoc()) {
                        if (in_array($row['transaction_id'], $arr)) {
                            continue;
                        }
                        $arr[]=$row['transaction_id'];

                        $ids .= 
                        "<small> ["
                        ."Booking ID:".$row['id']."<br>"
                        ."Count:".$row['c']."<br>"
                        ."Reference:".$row['auth']."<br>"
                        ."Amount:".$row['auth_amount']."<br>"
                        ."Transaction ID: ".$row['transaction_id']."<br>"
                        ."Payment Datetime: ".$row['payment-datetime']."<br>"
                        ."Transactionn Type: ".$row['req_transaction_type']

                        ."]</small>".
                        "<br><br>";
                        $total_amount += $row['auth_amount'];
                        $count += 1;
                    }
                }

$part_time_elapsed_secs = microtime(true) - $part_start_2;
if (isset($_GET['debug'])) {
  echo '<br>(Part Cybersource Loop Takes): '.is_over_time($part_time_elapsed_secs).' ';
}

                echo "<td>".$count.' <br> '.$ids."</td>";
                echo "<td>".$total_amount."</td>";


            } else {
                echo "<td> - </td>";
                echo "<td> - </td>";
                echo "<td> - </td>";
            }
        }

$part_time_elapsed_secs = microtime(true) - $part_start_1;
if (isset($_GET['debug'])) {
  echo '(Part Credit Card Loop Takes): '.is_over_time($part_time_elapsed_secs).' ';
}

    }
?>
</tr>
<?php
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part C Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);


?>
</table>

                        </td>
                    </tr>
                </table>
                <hr>
            
        </td>
        <td>
                            
                <table>
                    <tr>
                        <th>
                            開始和結束日期時間<br>
                            Begin-Datetime <br> and End-Datetime
                        </th>
                        <th>
                            現金交易數量<br>
                            Cash<br>
                            Transaction Count
                        </th>
                        <th>
                            現金交易總額<br>
                            Cash<br> 
                            Total Transaction<br>Amount
                        </th>
                        <th>
                            現金報告下載按鈕<br>
                            Cash Report<br>
                            Download Button
                        </th>
                    </tr>
                    <tr>
<?php 

$date = date_create();
$hour_int = (int) date_format($date, 'H');
if ($hour_int >= 22) {
    
} else {
    date_sub($date, date_interval_create_from_date_string('1 days'));
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part D Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);



for ($i=0; $i < $show_more_days; $i++) {
?>
<tr>
<?php
    // echo '<td>'.($i+1).'</td>';
    $end = date_format($date, 'Y-m-d').' 22:00:00';
    date_sub($date, date_interval_create_from_date_string('1 days'));
    $begin = date_format($date, 'Y-m-d').' 22:00:00';
    echo '<td>From '.$begin.'<br>To '.$end.'</td>';



    // echo '<td>'.$sql.'</td>';
    $sql = "
select 
    (cas_c1 + IFNULL(unpaid_count,0) ) cas_c,
    (cas_sum1 + IFNULL(unpaid_sum,0) ) cas_sum
from (
    SELECT 
        count(*) cas_c1, 
        ifnull(sum(amount),0) cas_sum1 
    FROM `golf-cash`
    left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-cash`.`auth`
    where 1=1
    and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
) t1, (
    select 
        count(*) unpaid_count
        ,sum(
            case 
                when IFNULL((`golf-unpaid-account`.`is_paid`),-1)<=0
                    then 0
                when IFNULL((`golf-unpaid-account`.`is_paid`),-1)=1
                    then IFNULL((`golf-unpaid-account`.`amount`),0)
                else 0
            end
        ) unpaid_sum 
    from `golf-unpaid-account`
        left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-unpaid-account`.`auth`
    where 1=1
        and `golf-unpaid-account`.`is_paid` is not null
        and `golf-unpaid-account`.`is_paid`=1
        and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
) t2
    ";
    // where `timestamp` between '$begin' and '$end';
    
    // echo '<td>'.$sql.'</td>';
    $result = $conn_download_report->query($sql);
    // echo '<td>';
    // var_dump($result);
    // echo '</td>';

    if ($result->num_rows > 0) {

$part_start_1 = microtime(true);

        while ($row = $result->fetch_assoc()) {
            if ($row["cas_c"] > 0 || $row["cas_sum"] > 0) {
                echo '<td>'.$row["cas_c"].' record(s)</td>';
                echo '<td>HKD $'.$row["cas_sum"].'</td>';
    ?>
<td>
<form action="./download_report.php" method="get" style="height: 0px;" target="_blank">
    <input type="hidden" name="type" value="cash_card">
    <input type="hidden" name="begin" value="<?php echo $begin; ?>">
    <input type="hidden" name="end" value="<?php echo $end; ?>">
    <input type="submit" value="Download">    
</form>
<?php 
                if (isset($_GET['S']) && $report_check_count_cash < 1) {
                    $report_check_count_cash += 1;
                    $type = 'cash_card';
                    cash_report($conn_download_report, $type, $begin, $end, false);
                }
 ?>
</td>
    <?php

            } else {
                echo "<td> - </td>";
                echo "<td> - </td>";
                echo "<td> - </td>";
            }
        }

$part_time_elapsed_secs = microtime(true) - $part_start_1;
if (isset($_GET['debug'])) {
  echo '(Part Cash loop Takes): '.is_over_time($part_time_elapsed_secs).' ';
}


    }
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part E Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);

 ?>
                    </tr>
                </table>
                <hr>
            
        </td>


                    <td>
                        <h1>
                            逾期預約交易 - 退款清單 <br> 
                            Refund List - Overdue Booking Transaction
                        </h1>
<?php 

$sql = "
    SELECT 
        `golf_cybersource_right`.transaction_id,
        COUNT(*) c,
        max(`golf_fairway_booking_history`.`id`) id,
        GROUP_CONCAT(`golf_fairway_booking_history`.`auth`) auth,
        max(`golf_cybersource_right`.`auth_amount`) auth_amount,
        max(`golf_cybersource_right`.`auth_code`) auth_code,
        max(`golf_cybersource_right`.`req_transaction_type`) req_transaction_type,
        max(DATE_ADD(signed_date_time, INTERVAL 8 HOUR)) `payment-datetime`
    FROM (`golf_fairway_booking_history` join `golf_cybersource_right` join `golf-payment-session`) 
    WHERE `golf_fairway_booking_history`.`auth` = `golf_cybersource_right`.`req_reference_number` 
    AND `golf_cybersource_right`.`decision` = 'ACCEPT' 
    AND `golf_cybersource_right`.`transaction_id` is not null 
    AND `golf_cybersource_right`.`transaction_id` <> '' 
    and reason_code=100
    and auth_amount>0
    and `golf-payment-session`.auth=`golf_fairway_booking_history`.`auth`
    and (
        select count(*) from golf_fairway_booking where golf_fairway_booking.auth=`golf_fairway_booking_history`.`auth`
        )=0
    group by `golf_cybersource_right`.transaction_id
    ORDER BY `golf_fairway_booking_history`.`timestamp` DESC ;
";


$result = $conn_download_report->query($sql);
if (is_bool($result)) {
} else if ($result->num_rows > 0) {
    $arr = array();
    while ($row = $result->fetch_assoc()) {
        echo "<small> ["
        ."Booking ID:".$row['id']."<br>"
        ."Count:".$row['c']."<br>"
        ."Reference:".$row['auth']."<br>"
        ."Amount:".$row['auth_amount']."<br>"
        ."Transaction ID: ".$row['transaction_id']."<br>"
        ."Payment Datetime: ".$row['payment-datetime']."<br>"
        ."Transactionn Type: ".$row['req_transaction_type']

        ."]</small>"
        ."<br><br>";
    }
}

 ?>
                    </td>


    </tr>
</table>


<?php 
// var_dump($arr);
  // echo json_encode($arr,JSON_PRETTY_PRINT);
  // echo json_encode($complexArray);
 ?>







<?php 
// Close the database connection
$conn_download_report->close();

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '<br>(Part F Takes): '.is_over_time($part_time_elapsed_secs).' ';
}
$part_start = microtime(true);


t_log('end[download_report.php]');
 ?>