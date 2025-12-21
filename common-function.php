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

function get_staff_credit_card_only($conn_download_report, $begin, $end, $src='all', $src2='all') {
    $return_result = array();
    $sql = "
    SELECT 
      count(*) cre_c,  
      ifnull(sum(`golf_cybersource_right`.auth_amount),0) cre_sum,
      LOWER('$src') `username`
    FROM `golf_cybersource_right`
        left join `golf_fairway_booking` on `golf_fairway_booking`.`auth`=`golf_cybersource_right`.`req_reference_number` 
    where
        DATE_ADD(`golf_cybersource_right`.signed_date_time, INTERVAL 8 HOUR) between '$begin' and '$end'
        and (
            'all'='$src'
            or
            'all'='$src2'
            or
            (
              (
                SELECT COUNT(*) C 
                FROM `golf-staff` 
                WHERE LOWER(`golf-staff`.`id`) LIKE LOWER('$src') 
                or LOWER(`golf-staff`.`id`) LIKE LOWER('$src2')
              ) and 
              (
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src')
                or 
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src2')
              )
            )
        )
;
    ";
    $result = $conn_download_report->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $return_result['cre_c'] = $row["cre_c"];
            $return_result['cre_sum'] = $row["cre_sum"];
            if ($row["cre_c"]>0) {
              $return_result['username'] = $row["username"];
            }
        }
    }
    if (!isset($return_result['username'])) {
      if (strlen($src)>0) {
        $return_result['username'] = $src;
      }
      if (strlen($src2)>0) {
        $return_result['username'] = $src2;
      }
    }
    return $return_result;
}
function get_staff_cash_n_bank_check_only($conn_download_report, $begin, $end, $src='all', $src2='all') {
    $return_result = array();
    $sql = "
select 
    (cas_c1 + IFNULL(unpaid_count,0) ) cas_n_unpaid_c,
    (cas_sum1 + IFNULL(unpaid_sum,0) ) cas_n_unpaid_sum,
    t1.cas_c1 `cas_c1`,
    t1.cas_sum1 `cas_sum1`,
    IFNULL(t2.unpaid_count,0) `unpaid_count`,
    IFNULL(t2.unpaid_sum,0) `unpaid_sum`,
    LOWER('$src') `username`
from (
    SELECT 
        count(*) cas_c1, 
        ifnull(sum(amount),0) cas_sum1 
    FROM `golf-cash`
    left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-cash`.`auth`
        left join `golf_fairway_booking` on `golf_fairway_booking`.`auth`=`golf-cash`.`auth` 
    where 1=1
    and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
        and (
            'all'='$src'
            or
            'all'='$src2'
            or
            (
              (
                SELECT COUNT(*) C 
                FROM `golf-staff` 
                WHERE LOWER(`golf-staff`.`id`) LIKE LOWER('$src') 
                or LOWER(`golf-staff`.`id`) LIKE LOWER('$src2')
              ) and 
              (
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src')
                or 
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src2')
              )
            )
        )
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
        left join `golf_fairway_booking` on `golf_fairway_booking`.`auth`=`golf-unpaid-account`.`auth` 
    where 1=1
        and `golf-unpaid-account`.`is_paid` is not null
        and `golf-unpaid-account`.`is_paid`=1
        and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
        and (
            'all'='$src'
            or
            'all'='$src2'
            or
            (
              (
                SELECT COUNT(*) C 
                FROM `golf-staff` 
                WHERE LOWER(`golf-staff`.`id`) LIKE LOWER('$src') 
                or LOWER(`golf-staff`.`id`) LIKE LOWER('$src2')
              ) and 
              (
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src')
                or 
                LOWER(`golf_fairway_booking`.`src`) LIKE LOWER('$src2')
              )
            )
        )
) t2
    ";
    $result = $conn_download_report->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $return_result['unpaid_c'] = $row["unpaid_count"];
            $return_result['unpaid_sum'] = $row["unpaid_sum"];
            $return_result['cas_c'] = $row["cas_c1"];
            $return_result['cas_sum'] = $row["cas_sum1"];
            $return_result['cas_n_unpaid_c'] = $row["cas_n_unpaid_c"];
            $return_result['cas_n_unpaid_sum'] = $row["cas_n_unpaid_sum"];
            if ($row["cas_n_unpaid_c"]>0) {
              $return_result['username'] = $row["username"];
            }
        }
    }
    // var_dump($return_result);
    // echo "<br>";
    if (!isset($return_result['username'])) {
      if (strlen($src)>0) {
        $return_result['username'] = $src;
      }
      if (strlen($src2)>0) {
        $return_result['username'] = $src2;
      }
    }
    return $return_result;
}



function get_staff_cash_received($conn_download_report, $begin, $end, $src='all', $src2='all') {
  $result_credit_card_only = get_staff_credit_card_only($conn_download_report, $begin, $end, $src, $src2);
  $result_cash_n_bank_check_only = get_staff_cash_n_bank_check_only($conn_download_report, $begin, $end, $src, $src2);
  $result = array_merge($result_credit_card_only, $result_cash_n_bank_check_only);
  return $result;
}


 ?>