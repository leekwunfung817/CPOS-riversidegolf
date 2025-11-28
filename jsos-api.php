<?php 

require_once './account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql_query_part = "SELECT `name`, `email`, `telephone`, `octopus_no`, `booking_date`, `begin_hour`, `end_hour`, `p_selections`, `auth`
		,(case when (select count(*) > 0 from golf_confirmed_booking where golf_confirmed_booking.auth=golf_fairway_booking.auth) then 'T' else 'F' end) payment_confirmed
		,(case when (select count(*) > 0 from `golf-carpark-check-in` where `golf-carpark-check-in`.auth=golf_fairway_booking.auth) then 'T' else 'F' end) carpark_checked_in
		,(case when (select count(*) > 0 from `golf-fairway-check-in` where `golf-fairway-check-in`.auth=golf_fairway_booking.auth) then 'T' else 'F' end) fairway_checked_in
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
    ";

function query_to_arr($row)
{
	$result_ = [
	    'name' => $row['name'],
	    'email' => $row['email'],
	    'telephone' => $row['telephone'],
	    'octopus_no' => $row['octopus_no'],
	    'booking_date' => $row['booking_date'],
	    'begin_hour' => $row['begin_hour'],
	    'end_hour' => $row['end_hour'],
	    'p_selections' => $row['p_selections'],
	    'auth' => $row['auth'],

	    'email-confirmation-status' => $row['email-confirmation-status'],
	    'golf-payment-status' => $row['golf-payment-status'],

	    'payment_confirmed' => $row['payment_confirmed'],
	    'carpark_checked_in' => $row['carpark_checked_in'],
	    'fairway_checked_in' => $row['fairway_checked_in']
	];
	return $result_;
}







$param_name = 'octopus_no';

if ( (count($_GET) === 1 && isset($_GET[$param_name])) || (count($_POST) === 1 && isset($_POST[$param_name])) ) {

	// Assuming you've received the octopus_no from the GET request
	$octopus_no = null;

	if (isset($_GET[$param_name])) {
		$octopus_no = $_GET[$param_name];
	}
	
	if (isset($_POST[$param_name])) {
		$octopus_no = $_POST[$param_name];
	}

	// Prepare the query
	$query = "
	$sql_query_part 
    WHERE `octopus_no`=?
    order by `booking_date` 
    desc limit 1";

	// Create a prepared statement
	$stmt = $conn->prepare($query);

	// Bind the parameter
	$stmt->bind_param("s", $octopus_no);

	// Execute the query
	$stmt->execute();

	// Fetch the result
	$result = $stmt->get_result();
	if (!$result) {
		throw new Exception("Database Error [{$this->database->errno}] {$this->database->error}");
	}
	while ($row = $result->fetch_assoc()) {
		$sql = "INSERT INTO `golf-carpark-check-in` (`auth`) VALUES ('".$row['auth']."')";
		if ($conn->query($sql) === TRUE) {
		    // echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$result_ = query_to_arr($row);

		// Convert to JSON
		$jsonResult = json_encode($result_, JSON_PRETTY_PRINT);

		// Print the JSON
		echo $jsonResult;
	}
	// Close the statement and connection
	$stmt->close();
}






$param_name = 'auth';

if ( (count($_GET) === 1 && isset($_GET[$param_name])) || (count($_POST) === 1 && isset($_POST[$param_name])) ) {

	// Assuming you've received the octopus_no from the GET request
	$auth = null;

	if (isset($_GET[$param_name])) {
		$auth = $_GET[$param_name];
	}
	
	if (isset($_POST[$param_name])) {
		$auth = $_POST[$param_name];
	}

	$sql = "
	$sql_query_part 
	WHERE `auth`='$auth'
    order by `booking_date` 
    desc limit 1";
	$result = $conn->query($sql);

    while ($row = $result->fetch_assoc()) {
		$sql = "INSERT INTO `golf-fairway-check-in` (`auth`) VALUES ('".$row['auth']."')";
		if ($conn->query($sql) === TRUE) {
		    // echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$result_ = query_to_arr($row);

		// Convert to JSON
		$jsonResult = json_encode($result_, JSON_PRETTY_PRINT);

		// Print the JSON
		echo $jsonResult;
    }
}

$param_name = 'p_position';

if ( (count($_GET) === 1 && isset($_GET[$param_name])) || (count($_POST) === 1 && isset($_POST[$param_name])) ) {

	// Assuming you've received the octopus_no from the GET request
	$p_position = null;

	if (isset($_GET[$param_name])) {
		$p_position = $_GET[$param_name];
	}
	
	if (isset($_POST[$param_name])) {
		$p_position = $_POST[$param_name];
	}

	// Prepare the query
	$query = "
	$sql_query_part
	where `p_selections` like '%\"".$p_position."\"%'
	and `booking_date` > DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d')
	ORDER BY `golf_fairway_booking`.`booking_date` DESC
	limit 1;";

	// Create a prepared statement
	$stmt = $conn->prepare($query);

	// Execute the query
	$stmt->execute();

	// Fetch the result
	$result = $stmt->get_result();
	if (!$result) {
		throw new Exception("Database Error [{$this->database->errno}] {$this->database->error}");
	}
	while ($row = $result->fetch_assoc()) {
		$sql = "INSERT INTO `golf-fairway-check-in` (`auth`) VALUES ('".$row['auth']."')";
		if ($conn->query($sql) === TRUE) {
		    // echo "New record created successfully";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}
		$result_ = query_to_arr($row);

		// Convert to JSON
		$jsonResult = json_encode($result_, JSON_PRETTY_PRINT);

		// Print the JSON
		echo $jsonResult;
	}
	// Close the statement and connection
	$stmt->close();
}





if (count($_GET) === 0 && count($_POST) === 0 ) {
	// Prepare the query
	$query = "
	$sql_query_part
	where `booking_date` >= DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d')
	ORDER BY `golf_fairway_booking`.`booking_date` DESC;";

	// Create a prepared statement
	$stmt = $conn->prepare($query);

	// Execute the query
	$stmt->execute();

	// Fetch the result
	$result = $stmt->get_result();
	if (!$result) {
		throw new Exception("Database Error [{$this->database->errno}] {$this->database->error}");
	}
	$arr = array();
	while ($row = $result->fetch_assoc()) {
		$result_ = query_to_arr($row);
		$arr[] = $result_;
	}

	// Convert to JSON
	$jsonResult = json_encode($arr, JSON_PRETTY_PRINT);

	// Print the JSON
	echo $jsonResult;

	// Close the statement and connection
	$stmt->close();
}


















$conn->close();

 ?>