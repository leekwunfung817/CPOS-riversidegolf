<?php 
set_time_limit(5);
require_once './logger.php';
t_log('begin[booking-status-json-variable.php]');

ini_set('memory_limit', '1024M');
$part_start = microtime(true);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once './cybersource_api/search.php';

//       $start = microtime(true);

// require_once './clear_record.php';
//       $time_elapsed_secs = microtime(true) - $start;
//       if (isset($_GET['debug'])) {
//         echo '(Clear record): '.$time_elapsed_secs.' ';
//       }

if (isset($_GET['debug'])) {
}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.1 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


require './position_list.php';

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.2 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


// $position_list = array(
//     //Sand
//         1,2,
//     // VIP
//         "VIP",
//     // Iron
//         // 3,
//         5,6,7,8,9,10,11,12,13,
//         15,16,

//         17,18,19,20,21,22,23,
//         25,26,
//         27,28,29,30,31,32,33,
//         35,
//         36,37,38,39,
//     // Wood
//         50,51,52,53,

//         55,56,57,
//         59,60,61,62,63,
//         65,66,67,68,69,70,71,72,73,

//         75,76,77,78,79,80,81,82,83
//         // ,84
//         ,85
// );



if (!function_exists('check_buffer_count')) {

function check_buffer_count($conn,$data)
{

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
          $buffer_count += 1;

          if (isset($_GET['insert_golf_booking_buffer'])) {

                      $key4=str_replace("position_", "", $position);
                      $sql_1 = "
                      INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
                      VALUES 
                      ('$key1','$key2','$key4','');
                      ";
                      if (isset($_GET['debug'])) {
                        try {
                            // Execute the query
                            if ($conn->query($sql_1) === TRUE) {
                                // echo "Data inserted successfully!";
                            } else {
                                echo "Error: " . $sql_1 . "<br>" . $conn->error;
                            echo "SQL error 222 $sql";
                                $allowed_to_book = 'N';
                            }
                        } catch (Exception $e) {
                            echo $e;
                            echo "Exception 222 $sql_1";
                            $allowed_to_book = 'N';
                        }
                      }

          }


        }
    }
    return $buffer_count;

}

}
  // <!-- 

  // Use php to generate an array, php to create a complex array with multiple layer of array, the first array key of layer is a series of date from today to the following six dates (Total 7 days, show the date with format YYYY-MM-DD in the table cell); the second  array key of layer is a series of hours from 09:00 am to 10:00 pm (Totally 13 hours - one table cell for each hour) with two hours text (the 24-hour formatted time, and the 12-hour formatted time); the third array key of layer are 1 to 60 position numbers

  //  -->

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.3 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);

if (!function_exists('set_date_array')) {

function set_date_array($position_list,$dateArray,$date)
{
  $dateArray[$date] = [];
  for ($j = 8; $j <= 22; $j=$j+0.5) {
    $hour_int = ((int)$j);
    if ($j == $hour_int) {
      $half_hour_mark = ':00';
    } else {
      $half_hour_mark = ':30';
    }
    $dateArray[$date][$hour_int . $half_hour_mark] = [
      date('H:i', strtotime($hour_int . $half_hour_mark)),
      date('h:i A', strtotime($hour_int . $half_hour_mark)),
    ];
    // $position_filler = array();
    foreach ($position_list as $key) {
      $dateArray[$date][$hour_int . $half_hour_mark]['booking'][$key] = 0;

    }
    // $dateArray[$date][$hour_int . $half_hour_mark]['booking'] = array_fill(1, 60, -1);
  }
  return $dateArray;
}

}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.4 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);

if (!function_exists('generateComplexArray')) {

function generateComplexArray($position_list) {

  date_default_timezone_set('Asia/Hong_Kong');

  $dateArray = [];

  if (isset($_GET['exact_date'])) {
    $dateArray = set_date_array($position_list,$dateArray,$_GET['exact_date']);
  } else {
    for ($i = 0; $i < 8; $i++) {
      $date = date('Y-m-d', strtotime("+$i days"));
      $dateArray = set_date_array($position_list,$dateArray,$date);
    }
  }

  return $dateArray;
}

}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.5 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);

$complexArray = generateComplexArray($position_list);


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.6 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);



if (!function_exists('read_json_files')) {

function read_json_files($directory) {
  $data = [];
  if (is_dir($directory)) {
    $scandir = scandir($directory);
    unset($scandir[0], $scandir[1]); // Remove '.' and '..' entries
    foreach ($scandir as $filename) {
      $filepath = $directory . DIRECTORY_SEPARATOR . $filename;
      if (is_file($filepath) && pathinfo($filepath, PATHINFO_EXTENSION) === 'json') {
        $content = file_get_contents($filepath);
        $decoded_data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE) {
          $data[$filename] = $decoded_data;
        } else {
          // Handle JSON decode error (optional)
          echo "Error decoding file: $filepath";
        }
      }
    }
  }
  return $data;
}

}




$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.7 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);




      $start = microtime(true);




// echo "1-";

require 'account_variable.php';

// echo "3-";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

// echo "2-";
// Check connection
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


// ── Load system variables (configurable expire times) ──────────
$_sysvar_adhoc_buffer = 5;      // default fallback (adhoc-reservation-payment-buffer)
$_sysvar_general_buffer = 60;   // default fallback (general-reservation-payment-buffer)
$sysvar_result = $conn->query("SELECT `variable_name`, `variable_value` FROM `golf-system-variable` WHERE `variable_name` IN ('adhoc-reservation-payment-buffer','general-reservation-payment-buffer')");
if ($sysvar_result && $sysvar_result->num_rows > 0) {
    while ($sv = $sysvar_result->fetch_assoc()) {
        if ($sv['variable_name'] === 'adhoc-reservation-payment-buffer') {
            $_sysvar_adhoc_buffer = (int)$sv['variable_value'];
        } elseif ($sv['variable_name'] === 'general-reservation-payment-buffer') {
            $_sysvar_general_buffer = (int)$sv['variable_value'];
        }
    }
}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.8 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


// Create connection
$conn_1 = new mysqli($servername, $username, $password, $dbname);
$conn_1->set_charset("utf8");
// echo "2-";
// Check connection
if ($conn_1->connect_error) {
echo "con err";
    die("Connection failed: " . $conn_1->connect_error);
}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.9 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


      $time_elapsed_secs = microtime(true) - $start;
      if (isset($_GET['debug'])) {
        echo '(Make Connection): '.$time_elapsed_secs.' ';
      }




















































      $start = microtime(true);



$sql = "SELECT `date`, `hour`, `position` FROM `golf_booking_buffer` WHERE date>CURRENT_TIMESTAMP;";
$key3='booking';
$arr = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $arr[] = $row;
    
    $date = $row['date'];
    $hour = $row['hour'];
    $position = $row['position'];

    $key1 = $date;
    $cursor_hour = ((float) $hour);
    $hour_int = ((int) $hour);
    $is_half_hour = $cursor_hour != $hour_int;
    $half_hour_mark = ($is_half_hour ? ':30' : ':00');
    $key2=$hour_int . $half_hour_mark;

    $key4=str_replace("position_", "", $position);

    $complexArray[$key1][$key2][$key3][$key4] = 0;
  }
}


      $time_elapsed_secs = microtime(true) - $start;
      if (isset($_GET['debug'])) {
        echo '(Process golf_booking_buffer data SQL): '.$time_elapsed_secs.' ';
      }























$booking_table_name = 'golf_fairway_booking';
if (isset($_GET['history_booking'])) {
  $booking_table_name = 'golf_fairway_booking_history';
}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.10 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


// $sql = "
// UPDATE `golf_booking_buffer` set
//   `golf_booking_buffer`.`src`=(
//     SELECT `golf_fairway_booking`.`id` 
//     FROM `golf_fairway_booking`
//     where `golf_fairway_booking`.`booking_date`=`golf_booking_buffer`.`date`
//     and CAST(`golf_fairway_booking`.`begin_hour` AS UNSIGNED)<=CAST(REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '') AS UNSIGNED)
//     and CAST(`golf_fairway_booking`.`end_hour` AS UNSIGNED)>=CAST(REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '') AS UNSIGNED)
//     and `golf_fairway_booking`.`p_selections` LIKE concat('%\"',`golf_booking_buffer`.`position`,'\"%')
//     limit 1
//   )
// where 1
// ;";
// //  // where `golf_booking_buffer`.`src` is null

// $start = microtime(true);
// try {
//   if ($conn->query($sql) === TRUE) {

//   }
// } catch (Exception $e) {
// }
// $time_elapsed_secs = microtime(true) - $start;
// if (isset($_GET['debug'])) {
//   echo '(Update golf_booking_buffer SQL): '.$time_elapsed_secs.' ';
// }


if (!function_exists('where_clauster')) {

function where_clauster($booking_table_name,$__GET)
{
  $where_str = " where 1=1 ";
  if ( isset($__GET['src']) ) {
    $where_str .= " and `$booking_table_name`.`src` = '".$__GET['src']."' ";
  }

  if ( isset($__GET['exact_date']) ) {
    $where_str .= " and `booking_date`='".$__GET['exact_date']."' ";
  } else if ( isset($__GET['from_date']) && isset($__GET['to_date']) ) {
    $where_str .= " and `$booking_table_name`.`timestamp` BETWEEN '".$__GET['from_date']."' and '".$__GET['to_date']."' ";
  } else if ( isset($__GET['history_booking']) ) {
    $where_str .= '';
  } else if ( isset($__GET['future_booking']) && !isset($__GET['page']) ) {
    // $where_str .= "";

    $where_str .= " and (
      `booking_date` >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') 
      or
      DATE_FORMAT(`$booking_table_name`.`timestamp`, '%Y-%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') 
    )
    ";
  } else if ( isset($__GET['future_booking']) && isset($__GET['page']) ) {
    $where_str .= " and (
    `booking_date` >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%Y-%m-%d')
    or
      DATE_FORMAT(`$booking_table_name`.`timestamp`, '%Y-%m-%d') >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') 
    )";
  } else {
    $where_str .= " and `booking_date` >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') ";
  }

  if ( isset($__GET['only_show']) ) {
    $where_str .= ' and ( 1<>1 ';
    if ( isset($__GET['only_show'.'_credit_card']) ) {
      $where_str .= " or `golf_cybersource`.auth_amount IS NOT NULL ";
    }
    if ( isset($__GET['only_show'.'_cash']) ) {
      $where_str .= " or `golf-cash`.`amount` IS NOT NULL ";
    }
    if ( isset($__GET['only_show'.'_unpaid']) ) {
      $where_str .= " or ( `golf-unpaid-account`.`is_paid` is not null and IFNULL(`golf-unpaid-account`.`is_paid`,-1)<=0 ) ";
    }
    if ( isset($__GET['only_show'.'_paid']) ) {
      $where_str .= " or ( `golf-unpaid-account`.`is_paid` is not null and IFNULL(`golf-unpaid-account`.`is_paid`,-1)=1 ) ";
    }
    $where_str .= ' ) ';
  }

  $column_name = 'id';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }

  $column_name = 'auth';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  $column_name = 'name';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  $column_name = 'email';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  $column_name = 'telephone';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  $column_name = 'octopus_no';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  $column_name = 'check_digit';
  if (isset($__GET['search_'.$column_name])) {
    $data = $__GET['search_'.$column_name];
    if (strlen($data)>0) {
    $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
    }
  }
  // $column_name = 'booking_date';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'begin_hour';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'end_hour';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'discount';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'p_selections';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'src';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'school-name';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `$booking_table_name`.`$column_name` like '%$data%' ";
  // }
  // $column_name = 'req_card_number';
  // if (isset($__GET['search_'.$column_name])) {
  //   $data = $__GET['search_'.$column_name];
  //   $where_str .= " and `golf_cybersource`.`$column_name` like '%$data%' ";
  // }
  
  return $where_str;
}

}


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z.1 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


if (!function_exists('limit_clauster')) {

function limit_clauster($__GET)
{
    $page_size = 50;

    if ( isset($__GET['show_paid_expire']) ) {
      $page_size = 5000;
    }

    if ( isset($__GET['page']) ) {
        $page = ( (int) $__GET['page'] );
        if ($page >= 1) {
          $page = $page - 1;
        }
        return ' limit '.($page*$page_size).', '.($page_size);
    }
    
    // $page_size = 1000;

    // if ( isset($__GET['future_booking']) ) {
    // // if (isset($__GET['debug'])) {
    // //   echo "Detected limit 100!!!!!!!!!!!!";
    // // }
    //     return " limit 500 ";
    // } else {
        // return " limit $page_size ";
    // }
    // Do not change!!! or the dynamic booking cannot see any 
    return '';
}

}
// CONVERT(`name` USING utf8) `name`, 
// `octopus_no`, 




$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part Z Takes): '.$part_time_elapsed_secs.' ';
}

$part_start = microtime(true);


$sql = "


SELECT 
  `$booking_table_name`.`id`

  ,`golf_cybersource`.auth_code auth_code
  "
  // ."
  // ,count(`golf_cybersource`.auth_code) acc
  // "
  ."
  ,`$booking_table_name`.discount

  ,`$booking_table_name`.`name`
  ,`$booking_table_name`.`email`
  ,`$booking_table_name`.`telephone`
  ,`$booking_table_name`.`octopus_no`

  ,`$booking_table_name`.`check_digit`
  ,`$booking_table_name`.`begin_hour`
  ,`$booking_table_name`.`end_hour`
  ,`$booking_table_name`.`p_selections` "

  ." ,(
      case 
      when `golf_cybersource`.auth_amount > 0
      then concat(`golf_cybersource`.req_currency,' $',`golf_cybersource`.auth_amount)
      else ' '
      end
    ) amount 

    ,`golf_cybersource`.req_card_number req_card_number

  "
    .(
        // isset($_GET['future_booking'])?
      " ,concat('HKD $',`golf-cash`.`amount`) cash "
      // :''
    // .",`golf-cash`.`timestamp` cash_timestamp "
    )


    .(isset($_GET['future_booking'])?" 
    ,(
      case 
        when IFNULL(`golf-unpaid-account`.`is_paid`,-1)=-1
          then ''
        when IFNULL(`golf-unpaid-account`.`is_paid`,-1)=0
          then 
            concat('<a class=\"unpaid_button\" onclick=\"selectTextarea(`'
                ,`$booking_table_name`.`id`
                ,'`,`'
                ,`$booking_table_name`.`auth`
            ,'`)\">'
            ,concat(`golf-unpaid-account`.`currency`
                ,' $',`golf-unpaid-account`.`amount`)
                ,' - 未付款<br>點擊此處付款</a><br><a href=\"./delete_booking.php?auth='
                ,`$booking_table_name`.`auth`
                ,'\" target=\"_blank\">刪除預訂</a>')
        when IFNULL(`golf-unpaid-account`.`is_paid`,-1)=1
          then concat(`golf-unpaid-account`.`currency`,' $',`golf-unpaid-account`.`amount`)
      end
    ) is_paid 
":'')
    ." ,`booking_date` "

    ."
    ,(
      case 
      when 
      (
        `golf-payment-session`.`payment-datetime` is not null
        or `golf_cybersource`.auth_code is not null
        OR `golf-cash`.`amount` is not null
        OR `golf_cybersource`.auth_amount is not null
        OR ( `golf-unpaid-account`.amount is not null and IFNULL(`golf-unpaid-account`.`is_paid`,-1)=1 )
      )
      then 'T' 
      else 'F' 
    end
    ) golf_payment_status
    "

    .(isset($_GET['future_booking'])?" 

,(
  `golf-payment-session`.`payment-datetime` 
) golf_payment_datetime

, concat(`golf_remark`.`remark` ,IFNULL(`golf-unpaid-account`.`remark`,\"\") ) remark

 ":'')

    .(isset($_GET['future_booking'])?" , concat('<a href=\"./admin-locker.php?','auth=',`$booking_table_name`.`auth`,'&name=',`$booking_table_name`.`name`,'&telephone=',`$booking_table_name`.`telephone`,'\" target=\"_blank\">預訂置物櫃</a>') locker_link ":'')
    
    .(
    isset($_GET['future_booking'])?
      '  '."
        ,(
          (
            case when 
              (
                `golf_cybersource`.auth_amount is not null
                or `golf-cash`.`amount` is not null
                or ( `golf-unpaid-account`.amount is not null AND `golf-unpaid-account`.`is_paid`=1 )
                or `golf-payment-session`.`payment-datetime` is not null
              )
             then "
            .'concat(\'<a href=\"./payment-page/payment-confirm.php?auth=\', `'.$booking_table_name.'`.`auth`,\'&decision=ACCEPT&download=true\" target=\"_blank\">Receipt</a>\')'
            ." else '' end
          )
        )
      ".'
       `Link`  '
      :''
    )

    .(
    isset($_GET['future_booking'])?
      ",(
          (
            case when
              (
                `golf_cybersource`.auth_amount is not null
                or `golf-cash`.`amount` is not null
                or ( `golf-unpaid-account`.amount is not null AND `golf-unpaid-account`.`is_paid`=1 )
                or `golf-payment-session`.`payment-datetime` is not null
              )
             then "
            .'concat(\'<a href=\"./payment-page/payment-confirm.php?auth=\', `'.$booking_table_name.'`.`auth`,\'&decision=ACCEPT&download=true&resend=1\" target=\"_blank\">Resend email</a>\') '
            ." else '' end
          )

        ) `resend_email`
      ":''
    )







    ."
    ,(case 
      when 
        `golf_cybersource`.auth_code is not null
        OR `golf-cash`.`amount` is not null
        OR `golf_cybersource`.auth_amount is not null
        OR ( `golf-unpaid-account`.amount is not null AND `golf-unpaid-account`.`is_paid`=1 )
      then 'T' 
      else 'F' 
    end) payment_confirmed
    "

    // ."
    // ,`golf-cash`.`amount` `golf-cash-amount`
    // ,`golf_cybersource`.auth_amount `golf_cybersource_auth_amount`
    // ,`golf-unpaid-account`.amount `golf-unpaid-account-amount`
    // ,`golf-unpaid-account`.`is_paid` `golf-unpaid-account_is_paid`
    // "

    ."
    ,(
      case 
        when 
          (
            `golf-carpark-check-in`.auth is not null
          ) or (
            `T_BOOK`.`time_in` is not null and `T_BOOK`.`time_in`<>'0000-00-00 00:00:00'
          )
        then 'T' 
        else 'F' 
      end
    ) carpark_checked_in
    ,(
      case 
      when 
        (
          `golf-fairway-check-in`.auth is not null
        ) or (
            `T_BOOK`.`print_time` is not null and `T_BOOK`.`print_time`<>'0000-00-00 00:00:00'
        ) 
        then 'T' 
        else 'F' 
      end) fairway_checked_in
    ,(
      case 
        when ( `golf_confirmed_booking`.`auth` is not null )
        then 'T' 
        else 'F' 
      end
    ) email_confirmation_status

    ,(case when
        `T_BOOK`.`qr_code` is not null
      then 'T' else 'F'
      end
      ) is_synchronized







    ,(case when 
      `$booking_table_name`.`timestamp`<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL (
        case when (`$booking_table_name`.`name`='' and `$booking_table_name`.`email`='')
          then $_sysvar_adhoc_buffer
          else $_sysvar_general_buffer
        end
        ) MINUTE)
      and (
        `golf_cybersource`.auth_amount is null
        and `golf-cash`.`amount` is null
        and `golf-unpaid-account`.`auth` is null
        and `golf-payment-session`.`payment-datetime` is null
      )
      then 'Y'
      else 'N'
    end) booking_expired 


    ,`golf_cybersource`.`card_type_name`
    , `$booking_table_name`.`auth`
"
    ."  ,`T_BOOK`.`pay_amount`  "
    .(!isset($_GET['history_booking'])?" , `$booking_table_name`.`src`":'')

    // .
    // (isset($_GET['golf_booking_buffer'])?
    //   " , (select count(*) from `golf_booking_buffer` where `golf_booking_buffer`.src=`$booking_table_name`.`id` ) `real_buffer_count` "
    //   :"")

."
  ,T_BOOK.invalid
  ,`$booking_table_name`.`timestamp`
  ,`golf_cybersource`.signed_date_time

  "

    
    .(
    (
      isset($_GET['future_booking'])
      && false
    )?
      '  '."
        ,(
          (
            case when 
              (
                `golf_cybersource`.auth_amount is not null
                or `golf-cash`.`amount` is not null
                or ( `golf-unpaid-account`.amount is not null AND `golf-unpaid-account`.`is_paid`=1 )
                or `golf-payment-session`.`payment-datetime` is not null
              ) or (
                `golf-unpaid-account`.`is_paid` is not null
              )
             then '' else "
            .'concat(\'<a href=\"./process_expire.php?id=\', `'.$booking_table_name.'`.`id`,\'&auth=\', `'.$booking_table_name.'`.`auth`,\'\" target=\"_blank\">Cancel Booking (Please Careful !!!!)</a>\')'
            ." end
          )
        )
      ".'
       `Cancel_Link`  '
      :''
    )


  ."
FROM `$booking_table_name` 
  left join `golf_cybersource` on 
        `golf_cybersource`.`req_reference_number`=`$booking_table_name`.`auth` 
        and `decision`='ACCEPT' 
        and `req_transaction_type`='sale'
        and LENGTH(transaction_id)>0
        and LENGTH(auth_code)>0
        and `golf_cybersource`.auth_amount > 0
        "
  ."
        and (
          '$booking_table_name'='golf_fairway_booking'
          or
            (
              '$booking_table_name'='golf_fairway_booking_history'
              and (
                select count(*) c 
                from golf_fairway_booking golf_fairway_booking_2
                where golf_fairway_booking_2.`auth` = `golf_cybersource`.`req_reference_number`
                )=0
            )
          )
  "
  ." left join `golf-cash` on `golf-cash`.`auth`=`$booking_table_name`.`auth` "
  ." left join `golf_remark` on `golf_remark`.`auth`=`$booking_table_name`.`auth` "
  ." left join `golf-unpaid-account` on `golf-unpaid-account`.`auth`=`$booking_table_name`.`auth` "
  ." left join `T_BOOK` on `T_BOOK`.`qr_code`=`$booking_table_name`.`auth` "
  ." left join `golf-payment-session` on `golf-payment-session`.auth=`$booking_table_name`.`auth` "
  ." left join `golf-carpark-check-in` on `golf-carpark-check-in`.auth=`$booking_table_name`.auth "
  ." left join `golf-fairway-check-in` on `golf-fairway-check-in`.auth=`$booking_table_name`.auth "
  ." left join `golf_confirmed_booking` on `golf_confirmed_booking`.`auth`=`$booking_table_name`.`auth` "

  ." "
.where_clauster($booking_table_name,$_GET)
."

group by `$booking_table_name`.`id`



"
.( 
  isset($_GET['history_booking'])?
  " order by "
  ." `$booking_table_name`.`id` desc  "
  :" order by `$booking_table_name`.`id` desc " )
.limit_clauster($_GET)
;

if (isset($_GET['debug'])) {
  var_dump($_GET);
  echo "Debug Enabled $sql<br>";
}


// file_put_contents('booking_json.sql', $sql);


      $start = microtime(true);
$result = $conn->query($sql);
      $time_elapsed_secs = microtime(true) - $start;
      if (isset($_GET['debug'])) {
        echo '(Select whole data SQL): '.$time_elapsed_secs.' ';
      }
$arr = array();


$key3='booking';

require_once './price-calculation.php';


if (!function_exists('notNullnEmpty')) {

function notNullnEmpty($data)
{
  return $data!=null && strlen($data) > 1;
}

}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part X Takes): '.$part_time_elapsed_secs.' ';
}


$duplicate_list = array();
$duplicate_auth_list = array();


$duplicate_array = array();
$duplicate_auth = array();


$part_start = microtime(true);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {


      $is_cybersource_top_up = 0;
      // if (
      //   $row['golf_payment_status']=='F' 
      //   && strlen($row['name'])>3 
      //   && isset($_GET['future_booking'])
      // ) {
      //   try {
          
      //   $is_cybersource_top_up = top_up_cybersource_by_id($conn,$row['id']);
      //   } catch (Exception $e) {
          
      //   }
      // }


      $loop_start = microtime(true);

      if (isset($_GET['history_booking'])) {
        
        $total_price = price_calculation( array(
            'lan' => 'zn',
            'print' => 'N'
        ), $row);

        $row['amount'] = 'HKD $ '.$total_price;

      }
      // echo "ID: " . $row["id"] . " - Name: " . $row["name"] . " - Email: " . $row["email"] . "<br>";

      $id = $row['id'];
      $auth = $row['auth'];
      $booking_expired = $row['booking_expired'];

      // if (isset($_GET['debug'])) {
      //   echo "$id $booking_expired <br>";
      // }


        $card_type_name = 'Unknown';

        $pay_time = '';
        if (isset($row['golf_payment_datetime'])) {
          $pay_time = (
            $row['golf_payment_datetime']
          );
        }

        $pay_type = null;
        $payment_amount = null;
        if (isset($row['amount']) && strlen($row['amount'].'') > 0) {
          $card_type_name = $row['card_type_name'];
          $pay_type = 'credit-card';
          $source_str = $row['amount'];
          $payment_amount = (int) substr($source_str, strpos($source_str, '$') + 1);
          // $payment_amount = (int) substr($source_str, strpos($source_str, '$'), strlen($source_str) - strpos($source_str, '$'));
        }
        if (isset($row['cash']) && strlen($row['cash'].'') > 0) {
          $card_type_name = 'Cash';
          $pay_type = 'cash';
          $source_str = $row['cash'];
          $payment_amount = (int) substr($source_str, strpos($source_str, '$') + 1);
        } else if (isset($row['is_paid']) && strlen($row['is_paid'].'') > 0) {
          $card_type_name = 'Bank or paycheck';
          $pay_type = 'bank-or-paycheck';
          $source_str = $row['is_paid'];
          $payment_amount = (int) substr($source_str, strpos($source_str, '$') + 1);
        }

        $row['pay_type'] = $pay_type;





      $successfully_moved_to_history = false;

      // m_log(
      //   "Check to history: $booking_expired : ".
      //   json_encode($row)
      // );

      if (
        $booking_expired=='Y' 
        && !isset($_GET['history_booking']) 
        && (is_null($payment_amount) || ((int) $payment_amount) <= 1)
        && ( $is_cybersource_top_up == 0 ) 
        && ( $row['auth_code']==null || strlen($row['auth_code']) <= 1 )
        // && false
      ) {

        m_log(
          "Move to history: ".
          json_encode($row)
        );

        if (isset($_GET['debug'])) {
        echo "Delete $id <br>";
        }

        $sql = "
        INSERT INTO `golf_fairway_booking_history` 
        (
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
        )
        select 
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
         from `golf_fairway_booking`
        where `id`='$id' and `auth`='$auth';
        ";
        try {
            if ($conn->query($sql) === TRUE) {
              if (isset($_GET['debug'])) {
                echo "$id $booking_expired get executed<br>";
              }
                $sql = "
                DELETE FROM `golf_fairway_booking` 
                WHERE `golf_fairway_booking`.`id`=(
                  select `golf_fairway_booking_history`.`id` from `golf_fairway_booking_history`
                  where `golf_fairway_booking_history`.`id`='$id' and `golf_fairway_booking_history`.`auth`='$auth'
                ) 
                and (
                  select count(*) from `golf_fairway_booking_history`
                  where `golf_fairway_booking_history`.`id`='$id' and `golf_fairway_booking_history`.`auth`='$auth'
                )>0
                and `golf_fairway_booking`.`id`='$id' and `golf_fairway_booking`.`auth`='$auth'
                ;
                ";
                      if (isset($_GET['debug'])) {
                      echo "$sql <br>";
                      }
                try {
                    if ($conn->query($sql) === TRUE) {
                      $successfully_moved_to_history = true;
                      if (isset($_GET['debug'])) {
                      echo "Delete $id succeed<br>";
                      }
                    } else {
                      $successfully_moved_to_history = false;
                    }
                } catch (Exception $e) {
                      $successfully_moved_to_history = false;
                }


            } else {
                      $successfully_moved_to_history = false;
            }
        } catch (Exception $e) {
              $successfully_moved_to_history = false;
        }
      }



        $key1 = $row['booking_date'];
        $begin_hour = (float) $row['begin_hour'];
        $end_hour = (float) $row['end_hour'];

        $row_debug = ( $row['id']=='91499' );


        // Assign occupied hours into complex array
        for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
          $hour_int = ((int) $cursor_hour);
          $is_half_hour = $cursor_hour != $hour_int;
          $half_hour_mark = ($is_half_hour ? ':30' : ':00');
          $key2=$hour_int . $half_hour_mark;

          foreach (json_decode($row['p_selections']) as $key => $position) {
            $key4=str_replace("position_", "", $position);
            if ($row_debug) {
              // echo "key1: ".$key1."<br>";
              // echo "key2: ".$key2."<br>";
              // echo "key3: ".$key3."<br>";
              // echo "key4: ".$key4."<br>";
              // echo "cursor_hour: ".$cursor_hour."<br>";
            }

            if ($successfully_moved_to_history) {
              $sql = "DELETE FROM `golf_booking_buffer` WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';";
              try {
                  if ($conn->query($sql) === TRUE) {
                  } else {
                  }
              } catch (Exception $e) {
              }
            }

            // if ($row['carpark_checked_in'] == 'F') {
            //   $complexArray[$key1][$key2][$key3][$key4] = 2;
            // }
            // if ($row['fairway_checked_in'] == 'F') {
            //   $complexArray[$key1][$key2][$key3][$key4] = 3;
            // }



            $complexArray[$key1][$key2][$key3][$key4] = 1;
            if ($row['payment_confirmed'] == 'T') {
              $complexArray[$key1][$key2][$key3][$key4] = 2;

              if ($row['carpark_checked_in'] == 'T') {
                $complexArray[$key1][$key2][$key3][$key4] = 3;
              }

              if ($row['fairway_checked_in'] == 'T') {
                $complexArray[$key1][$key2][$key3][$key4] = 4;
              }

            }

            // $complexArray[$key1][$key2][$key3]['fn_'.$key4] = $filename;
            if (!isset($_GET['api_1'])) {

              if (isset($_SESSION["management"])) {
                if (isset($complexArray[$key1][$key2][$key3]['data_'.$key4])) {
                  
                  $duplicate_auth[
                    $complexArray[$key1][$key2][$key3]['data_'.$key4]['auth']
                  ] = $row['auth'];

                  $duplicate_array[
                    $complexArray[$key1][$key2][$key3]['data_'.$key4]['id']
                  ] = $row['id'];






                  if (!in_array($row['auth'],$duplicate_auth_list)) {
                    $duplicate_auth_list[] = $row['auth'];
                  }

                  if (!in_array(
                      $complexArray[$key1][$key2][$key3]['data_'.$key4]['auth']
                    ,$duplicate_auth_list)) {
                    $duplicate_auth_list[] = $complexArray[$key1][$key2][$key3]['data_'.$key4]['auth'];
                  }


                  if (!in_array($row['id'],$duplicate_list)) {
                    $duplicate_list[] = $row['id'];
                  }

                  if (!in_array(
                      $complexArray[$key1][$key2][$key3]['data_'.$key4]['id']
                    ,$duplicate_list)) {
                    $duplicate_list[] = $complexArray[$key1][$key2][$key3]['data_'.$key4]['id'];
                  }

                }
                
                $complexArray[$key1][$key2][$key3]['data_'.$key4] = $row;
              }


            }
            
          }

        }

////////////////////////////////////////////////////////////////////////////////////////
        // prepare variable for T_BOOK synchronize

        $octopus_no = $row['octopus_no'];
        $check_digit = $row['check_digit'];

        $email = $row['email'];
        $telephone = $row['telephone'];
        // $remark = $row['remark'];
        
////////////////////////////////////////////////////////////////////////////////////////

      if ($row['is_synchronized']=='F' && $row['golf_payment_status']=='T') {

        $sql = "
        INSERT INTO `T_BOOK`(
          `car_park_id`,
          `member_id`,
          `reference`,
          `order_time`,
          `order_source`,
          `pos_no`,
          `date_begin`,
          `date_end`,
          `pay_type`,
          `pay_time`,
          `pay_amount`,
          `qr_code`,
          `card_no`,
          `email`,
          `invalid`,
          `tel`
        )
        SELECT 
          '$car_park_id'
          ,`golf_fairway_booking`.`name`
          ,`golf_fairway_booking`.`id`
          ,`golf_fairway_booking`.`timestamp`
          ,'web'
          ,REPLACE(REPLACE(REPLACE(`golf_fairway_booking`.`p_selections`,'\"',''),'[',''),']','')
          ,concat(`golf_fairway_booking`.`booking_date`,' ',
            
            REPLACE(`golf_fairway_booking`.`begin_hour`, '.5', ':30')
            
          )
          ,concat(`golf_fairway_booking`.`booking_date`,' ',
            
            REPLACE(`golf_fairway_booking`.`end_hour`, '.5', ':30')
            
          )
          ,'$card_type_name'
          ,'$pay_time'
          ,$payment_amount
          ,'$auth'
          ,'$octopus_no'
          ,'$email'
          ,'0'
          ,'$telephone'
        FROM `golf_fairway_booking`
        where `golf_fairway_booking`.`auth`='$auth'
        ;";
        try {
        $conn->query($sql);
        } catch (Exception $e) {
          // echo $e;
        }

      }
      if ($row['pay_amount']."" != "$payment_amount") {
          
        $sql = "UPDATE `T_BOOK` set `pay_amount`='$payment_amount' where `pay_amount`<>'$payment_amount' and `qr_code`='$auth';";
          try {
          $conn->query($sql);
          } catch (Exception $e) {
            // echo $e;
          }
      }

      // if (isset($_GET['golf_booking_buffer'])) {
      //   $row['check_buffer_count'] = check_buffer_count($conn_1,$row);
        
      //   if ($row['check_buffer_count'] < $row['real_buffer_count']) {
      //     $row['lost_buffer_count'] = 'extra: '.(((int) $row['check_buffer_count']) - ((int) $row['real_buffer_count']));
      //   }
      //   if ($row['check_buffer_count'] > $row['real_buffer_count']) {
      //     $row['lost_buffer_count'] = 'lost: '.(((int) $row['real_buffer_count'])-((int) $row['check_buffer_count']));
      //   }
      // }

      $payment_confirmed = $row['payment_confirmed'];
      if ($payment_confirmed == 'T' && !isset($row['golf_payment_datetime'])) {
                
        // Construct the SQL query
        $sql = "INSERT INTO `golf-payment-session`(`auth`, `price`, `payment-datetime`) 
          VALUES ('$auth','$payment_amount',CURRENT_TIMESTAMP);";
        try {
            if ($conn->query($sql) === TRUE) {
            } else {
            }
        } catch (Exception $e) {
        }

        // Construct the SQL query
        $sql = "UPDATE `golf-payment-session` SET `payment-datetime`=CURRENT_TIMESTAMP WHERE `payment-datetime` is null and `auth`='$auth';";
        try {
            if ($conn->query($sql) === TRUE) {
            } else {
            }
        } catch (Exception $e) {
            
        }

      }
      // if (isset($_GET['future_booking'])) {
      //   unset($row['auth']);
      // }
//////////////////////////////////////////////////////////////////////////////
      $should_append_record = false;
//////////////////////////////////////////////////////////////////////////////
      if (
        isset($_GET['show_paid_expire'])
        // || isset($_GET['history_booking'])
      ) {
        if ( 
          $row['auth_code']!=null 
          // && 
          // $row['booking_expired']=='N' 
        ) {
          $should_append_record = true;
        }
      } else {
        $should_append_record = true;
      }
//////////////////////////////////////////////////////////////////////////////
      // if (isset($_GET['paid_only'])) {
      //   if (!isset($row['auth_code']) || $row['auth_code']==null) {
      //     $should_append_record = false;
      //   }
      // }
//////////////////////////////////////////////////////////////////////////////
      if ($should_append_record) {
        $arr[] = $row;
      }
//////////////////////////////////////////////////////////////////////////////
      // if (count($arr)>150) {
      //   continue;
      // }


      $loop_time_elapsed_secs = microtime(true) - $loop_start;
      if (isset($_GET['debug'])) {
        echo '(Loop takes time): '.$loop_time_elapsed_secs.' ';
      }

    }
} else {
    // echo "0 results";
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part A Takes): '.$part_time_elapsed_secs.' ';
}

$part_start = microtime(true);

$sql = "
  UPDATE `T_BOOK`,`golf_fairway_booking_history` 
  SET  `T_BOOK`.`invalid`=1 
  WHERE `T_BOOK`.`qr_code`=`golf_fairway_booking_history`.`auth`
;";
try {
  $conn->query($sql);
} catch (Exception $e) {
  // echo $e;
}


$sql = "
  UPDATE `T_BOOK`,`golf_fairway_booking` 
  SET  `T_BOOK`.`invalid`=0
  WHERE `T_BOOK`.`qr_code`=`golf_fairway_booking`.`auth`
;";
try {
  $conn->query($sql);
} catch (Exception $e) {
  // echo $e;
}


// INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`) 
// VALUES 
// ('[value-1]','[value-2]','[value-3]'),
// ('[value-1]','[value-2]','[value-3]')
// ;

// remove unpaid booking after 15 minutes







// $sql = "
// DELETE FROM `golf_fairway_booking`
//   WHERE `timestamp`<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE)
//   and (select `payment-datetime` from `golf-payment-session` where `golf-payment-session`.auth=`golf_fairway_booking`.auth) is null;
// ";
// try {
//     // Execute the query
//     if ($conn->query($sql) === TRUE) {
//         // echo "Data inserted successfully!";
//     } else {
//     }
// } catch (Exception $e) {
// }













$is_duplicated = false;
$duplicate_info = 'Didn\'t called';
$duplicate_info_2 = '';
$check_count = 0;


if ( isset($_GET['booking_date']) && isset($_GET['begin_hour']) && isset($_GET['end_hour']) && isset($_GET['p_selections']) ) {
  $duplicate_info = 'Begin to check';
    $key1 = $_GET['booking_date'];
    $begin_hour = (float) $_GET['begin_hour'];
    $end_hour = (float) $_GET['end_hour'];
    for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
      $hour_int = ((int) $cursor_hour);
      $is_half_hour = $cursor_hour != $hour_int;
      $half_hour_mark = ($is_half_hour ? ':30' : ':00');
      $key2=$hour_int . $half_hour_mark;

      $duplicate_info .= " [  $key2 ( ".json_encode($complexArray[$key1][$key2])." ) ] ";
      foreach (json_decode($_GET['p_selections']) as $key => $position) {
        $key4=str_replace("position_", "", $position);

          $duplicate_info_2 .= " [$is_duplicated] checking for $key1 - $key2 - $key3 - $key4 -----";

        if ($complexArray[$key1][$key2][$key3][$key4] != 0) {
          $duplicate_info .= " { $key1 - $key2 - $key3 - $key4 - ".$complexArray[$key1][$key2][$key3][$key4]." } ";
          $duplicate_info_2 .= " { [$is_duplicated]  Duplicated $key1 - $key2 - $key3 - $key4 - ".$complexArray[$key1][$key2][$key3][$key4]." } ";
          
          $is_duplicated = true;
          if (!isset($_GET['disable_die'])) {
            echo "N";
            die();
          }
        } else {
          $duplicate_info_2 .= "  { [$is_duplicated] Not : $key1 - $key2 - $key3 - $key4 - ".$complexArray[$key1][$key2][$key3][$key4]." } ";
        }
      }
    }
  if (!$is_duplicated) {
    $duplicate_info .= ' - Not Duplicate - ';
    if (!isset($_GET['disable_die'])) {
      echo "Y";
      die();
    }
  }
}







if (!isset($_GET['skip_printout'])) {


  if (isset($_GET['future_booking'])) {
    echo json_encode($arr,JSON_PRETTY_PRINT);
  } else if (basename($_SERVER['PHP_SELF']) == 'booking-status-json-variable.php') {
    // echo basename($_SERVER['PHP_SELF']);

    echo json_encode($complexArray, JSON_PRETTY_PRINT);
  // } else {
  //   echo "Booking variable";
  } else if (isset($_GET['api'])) {
    echo json_encode($complexArray);

  } else if (basename($_SERVER['PHP_SELF']) == 'payment-confirm.php') {
  }

}


// $conn_1->close();
// $conn->close();

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part B Takes): '.$part_time_elapsed_secs.' ';
}

t_log('end[booking-status-json-variable.php]');
 ?>