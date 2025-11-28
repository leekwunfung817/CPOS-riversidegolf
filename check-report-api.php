<?php


    session_start();

    
set_time_limit(1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'logger.php';
// echo "3-";



if (
    !(
        isset($_GET['from_date'])
        && isset($_GET['to_date'])
    )
) {
    echo "date not found";
    die();
}

require_once 'account_variable.php';
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// echo "2-";
// Check connection
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_GET['way'])) {
    echo 'Way name not exists.';
	die();
}

if (!isset($_SESSION['name'])) {
    echo 'Session name not exists.';
	die();
}

$src = $_SESSION['name'];
$src2 = $_SESSION['name2'];
if ($_GET['way']=='all') {
	$src='all';
	$src2='all';
}








function this_page_link()
{
	$protocol = 
	// isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 
	'https://'
	 // : 'http://'
	 ;
	$host = $_SERVER['HTTP_HOST'];
	//$uri = $_SERVER['REQUEST_URI'];
	$fullUrl = $protocol . $host
	//  . explode('?', $uri)[0]
	 ;
	return $fullUrl;
}


function this_page_link_()
{
	$protocol = 
	// isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 
	'https://'
	 // : 'http://'
	 ;
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	$fullUrl = $protocol . $host
	 . explode('?', $uri)[0]
	 ;
	return $fullUrl;
}

function addition_element($arr,$key,$num)
{
	if (isset($arr[$key])) {
		$arr[$key] += $num;
	} else {
		$arr[$key] = $num;
	}
	return $arr;
}

function addition_element_2($arr,$key,$key_2,$num)
{
	if (!isset($arr[$key])) {
		$arr[$key] = array();
	}
	$arr[$key] = addition_element($arr[$key],$key_2,$num);
	return $arr;
}



function generate_report($conn,$complexArray, $src, $src2, $from, $to, $is_preview)
{
	
    	$html = '';
    	$html_1 = '';
    	$html_2 = '';
    	$html_3 = '';

    	$html .= 'Staff: "'.$src.'"';
    	$html .= "<hr>";
    	$html .= "$from ~ $to<br>";
    	$html .= "<hr>";
    	$html .= "<br>";

		?>
<!-- 
Report Input Critria:::::
From
<?php var_dump($from); ?>
To
<?php var_dump($to); ?>
-->
		<?php

		$arr = array();
		$total = 0;
		$record_count = 0;
		foreach ($complexArray as $value) {
			// var_dump($value);
			if ($value['golf_payment_datetime'] > $from && $value['golf_payment_datetime'] < $to) {
				// echo "$src == ".$value['src']."<br>";
				if ($src!='all') {
					if ($src!=$value['src'] && $src2!=$value['src']) {
						// echo "\n<!-- Skipped $from < $value['golf_payment_datetime'] < $to : $src/$value['src'] pay_type: $value['pay_type'] pay_amount : $value['pay_amount'] -->\n";
						continue;
					}
				}
				// echo "\n<!-- $from < $value['golf_payment_datetime'] < $to : $src/$value['src'] pay_type: $value['pay_type'] pay_amount : $value['pay_amount'] -->\n";
				$arr = addition_element_2($arr, $value['src'], $value['pay_type'], $value['pay_amount']);
				$total += $value['pay_amount'];
				$record_count += 1;
			}
		}

		?>
<!-- 
Report Debuug Identify:::::
<?php var_dump($arr); ?>
-->
		<?php


		{
	    	// $from_date = DateTime::createFromFormat('Y-m-d H:i:s', $from)->format('Y-m-d');
	    	// $to_date = DateTime::createFromFormat('Y-m-d H:i:s', $to)->format('Y-m-d');
	    	// $html_1 .= "<h1>置物櫃租賃 Locker Rental</h1>";
	    	$html_1 .= "<h1>置物櫃租賃 Locker Rental</h1>";
	    	// $html_1 .= "From $from to $to";

			$html_1 .= "<table style=\"width: 100%\">";

			$html_1 .= "<tr>";
			
			$html_1 .= "<td>";
			$html_1 .= "Staff";
			$html_1 .= "</td>";

			$html_1 .= "<td>";
			$html_1 .= "Deposit";
			$html_1 .= "</td>";
			
			$html_1 .= "<td>";
			$html_1 .= "Amt";
			$html_1 .= "</td>";
			
			$html_1 .= "</tr>";

	    	$locker_arr = array();
	    	$sql = "
	    		select `src`,`deposit`,`amount`,`datetime` from `golf-locker-transaction`
	    		WHERE `datetime` between '$from' and '$to'
	    		union all
	    		select `src`,`deposit`,`amount`,`datetime` from `golf-locker-transaction-history`
	    		WHERE `datetime` between '$from' and '$to'
	    	";
	    	// echo $sql;
			$result = $conn->query($sql);
			// $html_1 .= "<br> Locker Transaction:  $result->num_rows <br>";
			if ($result->num_rows > 0) {
	    		while ($row = $result->fetch_assoc()) {

					if ($src!='all') {
						if ($src!=$row['src'] && $src2!=$row['src']) {
							continue;
						}
					}
	    			if (!isset($locker_arr[$row['src']])) {
	    			 	$locker_arr[$row['src']]['total_deposit'] = 0;
	    			 	$locker_arr[$row['src']]['total_amount'] = 0;
	    			}
	    			$locker_arr[$row['src']]['total_deposit'] += $row['deposit'];
	    			$locker_arr[$row['src']]['total_amount'] += $row['amount'];

					// $html_1 .= "<br>";
					// $html_1 .= json_encode($row);
					// $html_1 .= "<br>";
					
	    		}
			}
			// $html_1 .= json_encode($locker_arr);
			foreach ($locker_arr as $src_ => $ele) {

				if ($src!='all') {
					if ($src!=$src_&&$src2!=$src_) {
						continue;
					}
				}

				$html_1 .= "<tr>";
				
				$html_1 .= "<td>";
				$html_1 .= (strlen($src_)==0?'Unknown':$src_);
				$html_1 .= "</td>";

				$html_1 .= "<td>";
				$html_1 .= $ele['total_deposit'];
				$html_1 .= "</td>";
				
				$html_1 .= "<td>";
				$html_1 .= $ele['total_amount'];
				$total += $ele['total_amount'];
				$html_1 .= "</td>";
				
				$html_1 .= "</tr>";

			}
	    			
	    	$html_1 .= "</table>";

		}

		{
	    	$html_2 .= "<h1>零售 Retails</h1>";

			$html_2 .= "<table style=\"width: 100%\">";

			$html_2 .= "<tr>";
			
			$html_2 .= "<td>";
			$html_2 .= "Staff";
			$html_2 .= "</td>";

			$html_2 .= "<td>";
			$html_2 .= "Amt";
			$html_2 .= "</td>";
			
			$html_2 .= "</tr>";

	    	$locker_arr = array();
	    	$sql = "
	    	SELECT `src`, sum(`amount`) `sum_amount` FROM `golf-retails-transaction`
	    		WHERE `update-datetime` between '$from' and '$to'
	    		group by `src` asc
	    	";
	    	// echo $sql;
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
	    		while ($row = $result->fetch_assoc()) {

					if ($src!='all') {
						if ($src!=$row['src']&&$src2!=$row['src']) {
							continue;
						}
					}
					
					$html_2 .= "<tr>";
					
					$html_2 .= "<td>";
					$html_2 .= (strlen($row['src'])==0?'Unknown':$row['src']);
					$html_2 .= "</td>";
					
					$html_2 .= "<td>";
					$html_2 .= $row['sum_amount'];
					$total += $row['sum_amount'];
					$html_2 .= "</td>";
					
					$html_2 .= "</tr>";
	    		}
			}
	    			
	    	$html_2 .= "</table>";

		}

		{
	    	$html_3 .= "<h1>球桿租賃 Golf Club Rental</h1>";

			$html_3 .= "<table style=\"width: 100%\">";

			$html_3 .= "<tr>";
			
			$html_3 .= "<td>";
			$html_3 .= "Staff";
			$html_3 .= "</td>";

			$html_3 .= "<td>";
			$html_3 .= "Deposit";
			$html_3 .= "</td>";

			$html_3 .= "<td>";
			$html_3 .= "Amt";
			$html_3 .= "</td>";
			
			$html_3 .= "</tr>";

	    	$locker_arr = array();
	    	$sql = "
	    	SELECT `src`, sum(`deposit`) `total_deposit`, sum(`rental-fee`) `sum_amount` FROM `golf-club-rental-record`
	    		WHERE `start-dt` between '$from' and '$to'
	    		group by `src` asc
	    	";
	    	// echo $sql;
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
	    		while ($row = $result->fetch_assoc()) {

					if ($src!='all') {
						if ($src!=$row['src'] && $src2!=$row['src']) {
							continue;
						}
					}
					
					$html_3 .= "<tr>";
					
					$html_3 .= "<td>";
					$html_3 .= (strlen($row['src'])==0?'Unknown':$row['src']);
					$html_3 .= "</td>";
					
					$html_3 .= "<td>";
					$html_3 .= $row['total_deposit'];
					$html_3 .= "</td>";

					$html_3 .= "<td>";
					$html_3 .= $row['sum_amount'];
					$total += $row['sum_amount'];
					$html_3 .= "</td>";
					
					$html_3 .= "</tr>";
	    		}
			}
	    			
	    	$html_3 .= "</table>";
	    	// $html_3 .= "$sql";
	    	// $html_3 .= "
			// <script>
			// 	console.log(`$sql`);
			// </script>
			// ";

		}
    	$html .= "<table style=\"width: 100%\">";

    	$html .= "<tr>";
    	
    	$html .= "<td>";
    	$html .= "<h1>All total Amt.</h1>";
    	$html .= "</td>";

    	$html .= "<td>";
    	$html .= "<h1>$total</h1>";
    	$html .= "</td>";
    	
    	$html .= "</tr>";

    	$html .= "<tr>";
    	
    	$html .= "<td>";
    	$html .= "Finish Float for Rent";
    	$html .= "</td>";

    	$html .= "<td>";
    	$html .= "<h1></h1>";
    	$html .= "</td>";
    	
    	$html .= "</tr>";

    	$html .= "<tr>";
    	
    	$html .= "<td>";
    	$html .= "<h1>No of count</h1>";
    	$html .= "</td>";

    	$html .= "<td>";
    	$html .= "<h1>$record_count</h1>";
    	$html .= "</td>";
    	
    	$html .= "</tr>";

    	$html .= "<tr>";
    	
    	$html .= "<td>";
    	$html .= "<h1>Rent Amt.</h1>";
    	$html .= "</td>";

    	$html .= "<td>";
    	$html .= "<h1>0</h1>";
    	$html .= "</td>";
    	
    	$html .= "</tr>";

    	$html .= "<tr>";
    	
    	$html .= "<td span=\"2\">";


    		$html .= "<table style=\"width: 100%\">";

	    	$html .= "<tr>";
	    	
	    	$html .= "<td>";
	    	$html .= "Staff";
	    	$html .= "</td>";

	    	$html .= "<td>";
	    	$html .= "P.Method";
	    	$html .= "</td>";

	    	$html .= "<td>";
	    	$html .= "Tot Amt.";
	    	$html .= "</td>";

	    	$html .= "</tr>";



	    	foreach ($arr as $username => $ele_1) {
		    	foreach ($ele_1 as $method => $amount) {
		    		if ($src!='all' && $username!=$src && $username!=$src2 ) {
		    			continue;
		    		}
		    		if ($username == '') {
		    			$username = 'Unknown';
		    		}
			    	$html .= "<tr>";
			    	
			    	$html .= "<td>";
			    	$html .= "$username";
			    	$html .= "</td>";

			    	$html .= "<td>";
			    	$html .= "$method";
			    	$html .= "</td>";

			    	$html .= "<td>";
			    	$html .= "$amount";
			    	$html .= "</td>";

			    	$html .= "</tr>";

		    	}
	    	}

    		$html .= "</table>";


    	$html .= "</td>";


    	$html .= "</tr>";




    	$html .= "</table>";
    	$html .= $html_1;
    	$html .= $html_2;
    	$html .= $html_3;


		return $html;
    	// var_dump($arr);
}
 

function get_booking_record($__GET)
{
	$_GET = $__GET;
	$_GET['future_booking']=1;
	$_GET['skip_printout']=1;
	// $_GET['src']=1;
	// echo $_GET['from_date'];
	// echo "->";
	// echo $_GET['to_date'];
	// echo "->";
	// echo $_GET['src'];
	// var_dump($__GET);
	
	require 'booking-status-json-variable.php';
	return $arr;
}

		?>
<!-- 
Report Input Critria:::::
_GET
<?php var_dump($_GET); ?>
-->
		<?php

$complexArray = get_booking_record($_GET);

?>
<!-- 
<?php
	var_dump($complexArray);
?> 
-->
<?php


$from = $_GET['from_date'];
$to = $_GET['to_date'];
$html_preview_report = generate_report($conn,$complexArray, $src , $src2, $from, $to, true);

echo "$html_preview_report";



?>