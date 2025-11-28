<?php 


error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once 'logger.php';

t_log('begin[clear_record_api.php]');

require 'account_variable.php';

$conn_th = new mysqli($servername, $username, $password, $dbname);
if ($conn_th->connect_error) {
    echo "con err";
    die("Connection failed: " . $conn_th->connect_error);
}




function check_buffer_count_1($conn,$data)
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
                 if (strlen($key4)==0) {
                     continue;
                 }
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
    
    $sql = "
    SELECT * FROM `golf_fairway_booking` WHERE `id`='$id';
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $count = check_buffer_count_1($conn,$row);
      }
    }

}


require_once './cybersource_api/search.php';

$check_api_index = (int) $_GET['check_api_index'];
$offset = $check_api_index - 1;


$sql = "
SELECT * 
FROM `golf_fairway_booking`
where length(name)>1 
and (
    select max(`payment-datetime`) 
    from `golf-payment-session`
    where `golf-payment-session`.auth=`golf_fairway_booking`.`auth`
) is null
and (
    select max(`golf_cybersource`.auth_code)
    from `golf_cybersource`
    where `golf_cybersource`.`req_reference_number`=`golf_fairway_booking`.`auth` 
    and `decision`='ACCEPT' 
    and `req_transaction_type`='sale'
    and LENGTH(transaction_id)>0
    and LENGTH(auth_code)>0
    and `golf_cybersource`.auth_amount > 0
) is null
order by id desc
LIMIT 1 OFFSET $offset
;
";

$result = $conn_th->query($sql);
$pipe = array();
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    
    $booking_id = $row['id'];
    $count = check_buffer_count_1($conn_th,$row);
    clean_and_check_booking($conn_th,$booking_id);
    top_up_cybersource_by_id($conn_th,$booking_id);

    // var_dump($row);
    // echo "\n\n\n";
  }

}


$conn_th->close();
t_log('end[clear_record_api.php]');

 ?>