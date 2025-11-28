<?php 
if (!isset($_GET['auth'])) {
    die();
}
$auth = $_GET['auth'];
$allGetParams = $_GET;

?>

<?php 

require_once './account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}




$sql = "
SELECT 
	`name`, 
	`octopus_no`, 
	`booking_date`, 
	`begin_hour`, 
	`end_hour`, 
	`p_selections`
	,(
		SELECT (case when COUNT(*)>0 then 'CONFIRMED' else 'UNCONFIRMED' end) 
		FROM `golf_confirmed_booking` 
		where `golf_confirmed_booking`.`auth`=`golf_fairway_booking`.`auth`
	) `email-confirmation-status`
	,(
		SELECT (case when COUNT(*)>0 then 'PAID' else 'UNPAID' end) 
		FROM `golf-payment-session` 
		where `golf-payment-session`.`auth`=`golf_fairway_booking`.`auth`
		and `payment-datetime` is not null
	) `golf-payment-status`
FROM `golf_fairway_booking` 
WHERE `auth`='$auth';
";
$result = $conn->query($sql);





if ($result->num_rows > 0) {
    while ($booking_arr = $result->fetch_assoc()) {
    	$booking_arr['p_selections']=implode(",", json_decode($booking_arr['p_selections']));

		if (strpos($booking_arr['begin_hour'], '.5') !== false) {
		    $booking_arr['begin_hour_24'] = str_replace('.5', ':30', $booking_arr['begin_hour']);
		} else {
		    $booking_arr['begin_hour_24'] = $booking_arr['begin_hour'].':00';
		}

		if (strpos($booking_arr['end_hour'], '.5') !== false) {
		    $booking_arr['end_hour_24'] = str_replace('.5', ':30', $booking_arr['end_hour']);
		} else {
		    $booking_arr['end_hour_24'] = $booking_arr['end_hour'].':00';
		}

    	echo json_encode($booking_arr, JSON_PRETTY_PRINT);
    }
}


 ?>