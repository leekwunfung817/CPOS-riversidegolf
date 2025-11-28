<?php 


error_reporting(E_ALL);
ini_set('display_errors', '1');

function check_duplicate_by_booking($booking_date, $begin_hour, $end_hour, $p_selections)
{

	$_GET['exact_date']=$booking_date;
	$_GET['booking_date']=$booking_date;
    $_GET['begin_hour']=$begin_hour;
    $_GET['end_hour']=$end_hour;
    $_GET['p_selections']=$p_selections;
	$_GET['disable_die']=1;

	require 'booking-status-json-variable.php';
	// var_dump($complexArray);
	// echo $duplicate_info;
	// echo "$duplicate_info_2 <br>";
	return $is_duplicated;
}

function IfSelf() {
    $backtrace = debug_backtrace();
    $isCalledBySameFile = false;

    foreach ($backtrace as $trace) {
        if (isset($trace['file']) && $trace['file'] === __FILE__) {
            $isCalledBySameFile = true;
            break;
        }
    }

    if ($isCalledBySameFile) {
        return true;
    } else {
    	return false;
    }

    // Example call
    // myFunction();
}

function report_duplicate($booking_date, $begin_hour, $end_hour, $p_selections)
{
	if (check_duplicate_by_booking($booking_date, $begin_hour, $end_hour, $p_selections)) {
	 	echo "Is Duplicated <br>";
	} else {
		echo "Is not Duplicated <br>";
	}
}

function is_booking_duplicate_by_auth($booking_date, $auth)
{
	$_GET['exact_date'] = $booking_date;
	require 'booking-status-json-variable.php';
	return in_array($auth,$duplicate_auth_list);
}

// if (IfSelf()) {

	// report_duplicate('2024-10-31',13,14,'[1]');
	// report_duplicate('2024-11-01',15,16,'[1]');
	// report_duplicate('2024-11-01',14,15,'[11,12]');
	// report_duplicate('2024-10-31',13,14,'[5]');


	// report_duplicate('2024-09-30',15,15.5,'[35]');
	// report_duplicate('2024-09-30',15.5,16,'[35]');

	// report_duplicate('2024-09-30',15.5,16.5,'[35]');
	// report_duplicate('2024-09-30',16,16.5,'[35]');
	// report_duplicate('2024-09-30',16.5,17,'[35]');
	// report_duplicate('2024-09-30',15,17,'[35]');

// }

?>
