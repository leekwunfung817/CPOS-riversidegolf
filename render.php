<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




require_once './account_variable.php';
require_once './common-function.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("delete from golf_booking_buffer where src='manual';");













$startDate = '2024-08-19';
$endDate = '2024-08-25';

$currentDate = strtotime($startDate);
$formattedDates = [];

while ($currentDate <= strtotime($endDate)) {
	$dateStr = date('Y-m-d', $currentDate);
    $formattedDates[] = $dateStr;
    $currentDate = strtotime('+1 day', $currentDate);



	$startHour = '08:00';
	$endHour = '22:30';

	$currentHour = strtotime($startHour);
	$formattedHours = [];

	while ($currentHour <= strtotime($endHour)) {
		$hourStr = str_pad(date('H:i', $currentHour),5,"0");
	    $formattedHours[] = $hourStr;
	    $currentHour = strtotime('+30 minutes', $currentHour);


		$positionString = '1,2,VIP,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,25,26,27,28,29,30,31,32,33,35,36,37,38,39,50,51,52,53,55,56,57,59,60,61,62,63,65,66,67,68,69,70,71,72,73,75,76,77,78,79,80,81,82,83,84,85';

		$positions = explode(',', $positionString);

		foreach ($positions as $key => $value) {
			$sql = " insert into golf_booking_buffer (date,hour,position,src) values ('$dateStr', '$hourStr', '$value', 'manual');";
			echo $sql.'<br>';
			try {
				$result = $conn->query($sql);
				echo $result;
		        // if ($result->num_rows > 0) {
			    //     if ($result->num_rows > 0) {
			    //         $result->data_seek(0);
			    //         while ($row = $result->fetch_assoc()) {

			    //         }
			    //     } else {
			    //     	echo "error";
			    //     }
		        // }
			} catch (Exception $e) {
				echo $e;
			}
		}
    	
	}



}

$conn->close();
die();



// Now $formattedDates contains the dates from August 19th to August 25th
// You can use this array to render the dates in your HTML table.
?>
