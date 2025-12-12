<?php
error_reporting(E_ALL);        // Report all errors
ini_set('display_errors', 1);  // Show errors in browser
?><?php 


function credit_card_record_query($conn_download_report, $begin, $end) {
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
    return $result;

}



function cash_report_record_query($conn_download_report, $begin, $end) {
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
    return $result;
}


function credit_card_statistic($conn_download_report, $begin, $end) {
    $arr = array();
    $result = credit_card_record_query($conn_download_report, $begin, $end);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($arr, $row);
        }
    }
    return $arr;
}


function cash_report_statistic($conn_download_report, $begin, $end) {
    $arr = array();
    $result = cash_report_record_query($conn_download_report, $begin, $end);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            array_push($arr, $row);
        }
    }
    return $arr;
}

function financial_report_statistic($conn_download_report, $begin, $end) {
    $total_credit_card_amount = 0;
    $total_credit_card_count = 0;
    $credit_card_arr = credit_card_statistic($conn_download_report, $begin, $end);
    foreach ($credit_card_arr as $key => $value) {
        $total_credit_card_amount += $value['auth_amount'];
        $total_credit_card_count += 1;
    }
    $total_cash_amount = 0;
    $total_cash_count = 0;
    $cash_arr = cash_report_statistic($conn_download_report, $begin, $end);
    foreach ($cash_arr as $key => $value) {
        $total_cash_amount += $value['amount'];
        $total_cash_count += 1;
    }
    $financial_total = $total_credit_card_amount + $total_cash_amount;
    return array(
        'total_credit_card_amount' => $total_credit_card_amount,
        'total_cash_amount' => $total_cash_amount,
        'total_credit_card_count' => $total_credit_card_count,
        'total_cash_count' => $total_cash_count,
        'financial_total' => $financial_total
    );
}

?><?php
function getMonthBoundaries($year, $month) {
    // Ensure inputs are integers
    $year = (int)$year;
    $month = (int)$month;

    // Beginning of the month
    $startOfMonth = new DateTime("$year-$month-01 00:00:00");

    // One second before beginning of the month
    $beforeStart = clone $startOfMonth;
    $beforeStart->modify('-1 second');

    // End of the month (last day at 23:59:59)
    $endOfMonth = clone $startOfMonth;
    $endOfMonth->modify('last day of this month 23:59:59');

    return [
        'before_start' => $beforeStart->format('Y-m-d').' 20:00:00',
        'end_of_month' => $endOfMonth->format('Y-m-d').' 20:00:00'
    ];
}

function statistic_by_range($conn_download_report, $begin, $end) {
    $financial_arr = financial_report_statistic(
        $conn_download_report, 
        $begin, 
        $end
    );
    return array(
        'begin'=> $begin,
        'end'=> $end,
        'cre_c'=> $financial_arr['total_credit_card_count'],
        'cre_sum'=> ((int)$financial_arr['total_credit_card_amount']),
        'cas_c'=> $financial_arr['total_cash_count'],
        'cas_sum'=> ((int)$financial_arr['total_cash_amount'])
    );
}

function statistic_by_month($conn_download_report, $year, $month) {
    $monthBoundaries = getMonthBoundaries($year, $month);
    // var_dump($monthBoundaries);
    return statistic_by_range(
        $conn_download_report, 
        $monthBoundaries['before_start'], 
        $monthBoundaries['end_of_month']
    );
}

if (isset($_GET['specified_report'])) {
    require './account_variable.php';
    $conn_download_report = new mysqli($servername, $username, $password, $dbname);
    if ($conn_download_report->connect_error) {
        die("Connection failed: " . $conn_download_report->connect_error);
    }
    if (isset($_GET['year']) && isset($_GET['month'])) {
        $financial_dict = statistic_by_month($conn_download_report, $_GET['year'], $_GET['month']);
    }
    if (isset($_GET['begin']) && isset($_GET['end'])) {
        $financial_dict = statistic_by_range($conn_download_report, $_GET['begin'], $_GET['end']);
    }
    
}

?>