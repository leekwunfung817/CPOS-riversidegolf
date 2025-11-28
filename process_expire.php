<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

  if ( !isset($_GET['id']) || !isset($_GET['auth']) ) {
    echo "Invalid Request";
    die();
  }

  $id = $_GET['id'];
  $auth = $_GET['auth'];







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









$booking_table_name = 'golf_fairway_booking';
  $sql = "
  SELECT
    `$booking_table_name`.*,
      (case when 
        `$booking_table_name`.`timestamp`<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 20 MINUTE)
        and (
          `golf_cybersource`.auth_amount is null
          and `golf-cash`.`amount` is null
          and `golf-unpaid-account`.`auth` is null
          and `golf-payment-session`.`payment-datetime` is null
        )
      then 'Y'
      else 'N'
    end) booking_expired 
    ,(case 
      when 
        `golf_cybersource`.auth_code is not null
        OR `golf-cash`.`amount` is not null
        OR `golf_cybersource`.auth_amount is not null
        OR `golf-unpaid-account`.amount is not null
      then 'T' 
      else 'F' 
    end) payment_confirmed

  "
."
  FROM `$booking_table_name` 
    left join `golf_cybersource` on 
      `golf_cybersource`.`req_reference_number`=`$booking_table_name`.`auth` 
      and `decision`='ACCEPT' 
      and `req_transaction_type`='sale'
  "
  ." left join `golf-cash` on `golf-cash`.`auth`=`$booking_table_name`.`auth` "
  ." left join `golf-unpaid-account` on `golf-unpaid-account`.`auth`=`$booking_table_name`.`auth` "
  ." left join `golf-payment-session` on `golf-payment-session`.auth=`$booking_table_name`.`auth` "
  ."
    WHERE `$booking_table_name`.`id`='$id'
    and `$booking_table_name`.`auth`='$auth'
  ;
  ";

$booking_expired = null;
$payment_confirmed = null;
$result = $conn->query($sql);
$row_buffer = null;
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $booking_expired = $row['booking_expired'];
    $payment_confirmed = $row['payment_confirmed'];
    $row_buffer = $row;

  }
}

if (isset($_GET['force'])) {
  
} else {
  if ( $booking_expired == null || $payment_confirmed == null ) {
    echo "Unknown expired state";
    die();
  } else if ( $payment_confirmed == 'Y' || $payment_confirmed == 'T' ) {
    echo "Cannot cancel paid booking";
    exit();
  } else if ( $booking_expired == 'Y' || $booking_expired == 'T' ) {
    echo "Expired booking is not allowed to be cancelled";
    exit();
  } else if ( $booking_expired == 'N' ) {
    echo "Not expired yet";
  }

}



echo "<hr>";


require_once './logger.php';


  m_log(
    "Move to history (process_expire.php): ".
    json_encode($row_buffer)
  );







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
      echo "$id $auth Inserted";
    } else {
      echo "Failed "." to move to booking history";
      die();
    }
  } catch (Exception $e) {
    echo "Exception when "." move to booking history";
    echo "$e";
    die();
  }




echo "<hr>";








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
        echo "Delete succeed";
  try {
      if ($conn->query($sql) === TRUE) {
        if (isset($_GET['debug'])) {
          echo "Delete $id booking succeed<br>";
        }
      } else {
        echo "Failed "." to delete to booking record";
        die();
      }
  } catch (Exception $e) {
    echo "Exception when "." delete to booking record";
    echo "$e";
    die();
  }

echo "<hr>";



  $sql = "DELETE FROM `golf_booking_buffer` WHERE `src`='$id' ;";
  try {
      if ($conn->query($sql) === TRUE) {
          echo "Delete $id buffer succeed";
      } else {
        echo "Failed "." to delete to booking buffer";
        die();
      }
  } catch (Exception $e) {
    echo "Exception when "." delete to booking buffer";
    echo "$e";
    die();
  }


echo "<hr>";

$conn->close();
 ?>
<script type="text/javascript">
  alert('預訂成功取消 Reservation canceled successfully');
  window.close();
</script>