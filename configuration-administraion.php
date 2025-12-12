<?php 

ini_set('session.gc_maxlifetime', 
	// 7*24*3600
	65535
);
ini_set('session.cookie_lifetime', 7*24*3600); // 1 week
session_set_cookie_params(7*24*3600);

// Start the session
session_start();
// echo json_encode($_SESSION,true);

if (isset($_GET['logout'])) {
	unset($_SESSION['management']);
	unset($_SESSION['auth']);
	unset($_SESSION['datetime']);
	unset($_SESSION['email']);
	unset($_SESSION['identity']);
	unset($_SESSION['name']);
	unset($_SESSION['name2']);
}

if (isset($_GET['auth'])&&isset($_GET['datetime'])&&isset($_GET['email'])) {


	$have_update = false;

    $auth = $_GET['auth'];
	$datetime = $_GET['datetime'];
	$email = $_GET['email'];

	if (isset($_GET['name'])) {
		$_SESSION['name'] = $_GET['name'];
	}
	if (isset($_GET['name2'])) {
		$_SESSION['name2'] = $_GET['name2'];
	}



	$md5_str = $datetime.'_'.$email;
	$md5 = md5($md5_str);
	// echo "$md5_str $md5";
	if ($auth==$md5) {
    // Set session variable
    $_SESSION["management"] = $md5;
    $_SESSION["auth"] = $auth;
    $_SESSION["datetime"] = $datetime;
    $_SESSION["email"] = $email;
    $_SESSION["identity"] = 'admin';
		$_SESSION['name'] = 'superuser';
		if (!isset($_SESSION['name2'])) {
			$_SESSION['name2'] = 'Superuser';
		}
    // header('Location: page2.php');
  }

	$md5_str = $datetime.'_full-time_'.$email;
	$md5 = md5($md5_str);
	if ($auth==$md5) {
    $_SESSION["management"] = $md5;
    $_SESSION["auth"] = $auth;
    $_SESSION["datetime"] = $datetime;
    $_SESSION["email"] = $email;
    $_SESSION["identity"] = 'full-time';
  }

	$md5_str = $datetime.'_part-time_'.$email;
	$md5 = md5($md5_str);
	if ($auth==$md5) {
    $_SESSION["management"] = $md5;
    $_SESSION["auth"] = $auth;
    $_SESSION["datetime"] = $datetime;
    $_SESSION["email"] = $email;
    $_SESSION["identity"] = 'part-time';
  }

	$md5_str = $datetime.'_manager_'.$email;
	$md5 = md5($md5_str);
	if ($auth==$md5) {
    $_SESSION["management"] = $md5;
    $_SESSION["auth"] = $auth;
    $_SESSION["datetime"] = $datetime;
    $_SESSION["email"] = $email;
    $_SESSION["identity"] = 'manager';
  }


	if (!isset($_SESSION['name2'])) {
		$_SESSION['name2'] = $_SESSION['name'];
	}


}

// echo json_encode($_SESSION,true);


 ?>


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
</head>

<style type="text/css">
	
	html {
		padding: 30px;
		background: lightskyblue;
	}
	body {
		padding: 30px;
		background-color: white;
	}
</style>
<?php 

$allGetParams = $_GET;

function this_page_link()
{
	$protocol = 
	// isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 
	'https://'
	 // : 'http://'
	 ;
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];
	$fullUrl = $protocol . $host . explode('?', $uri)[0];
	return $fullUrl;
}

require_once 'account_variable.php';
$removed_chars = array("'", '"', "`");

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!$conn->set_charset("utf8")) {
	printf("Error loading character set utf8: %s\n", $conn->error);
} else {
	// printf("Current character set: %s\n", $conn->character_set_name());
}
// mysql_set_charset('utf8');






if (isset($_SESSION["management"])) {


	$have_update = false;

    $auth = $_GET['auth'];
	$datetime = $_GET['datetime'];
	$email = $_GET['email'];

	if (true) {
		if ( isset($_GET['add_staff']) ) {
			$id = $_GET['id'];
			$type = $_GET['type'];
			$name = $_GET['name'];
			$password = $_GET['password'];
			
			$sql = "INSERT INTO `golf-staff`(`id`, `type`, `name`, `password`) VALUES ('$id','$type','$name','$password');";
			try {
				$result = $conn->query($sql);
			} catch (Exception $e) {
				
			}
			if ($result === TRUE) {
			  echo "Add staff successfully";
			} else {
			  echo "Add staff failed: " . $conn->error;
			}

		}
		if (isset($_GET['del_staff'])) {
			$id = $_GET['del_staff'];
			$sql = "DELETE FROM `golf-staff` where `id`='$id'";

			if ($conn->query($sql) === TRUE) {
			  echo "Employee account deleted successfully";
			} else {
			  echo "Error deleting Employee account: " . $conn->error;
			}
		}
		if ( isset($_GET['add_email']) ) {
			$have_update = true;

			$add_email = $_GET['add_email'];
			$add_email = str_replace($removed_chars, "", $add_email);

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-administration-email` (`email-address`) values ('".$add_email."');";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}
		if ( isset($_GET['del_email']) ) {
			$have_update = true;

			$del_email = $_GET['del_email'];
			$del_email = str_replace($removed_chars, "", $del_email);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-administration-email` where `email-address`='".$del_email."'";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}
		if ( isset($_GET['add_boardcast']) ) {
			$have_update = true;

			$add_boardcast = $_GET['add_boardcast'];
			$add_boardcast = str_replace($removed_chars, "", $add_boardcast);
			$add_boardcast = base64_encode($add_boardcast);

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-boardcast` (`boardcast-message`) values ('".$add_boardcast."');";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}
		if ( isset($_GET['del_boardcast']) ) {
			$have_update = true;

			$del_boardcast = $_GET['del_boardcast'];
			$del_boardcast = str_replace($removed_chars, "", $del_boardcast);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-boardcast` where `id`='".$del_boardcast."'";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}	
		if ( isset($_GET['add_black_email']) ) {
			$have_update = true;

			$add_black_email = $_GET['add_black_email'];
			$add_black_email = str_replace($removed_chars, "", $add_black_email);

			// Delete all emails from the table
			$sql = "INSERT INTO black_list_email (`email-address`) values ('".$add_black_email."');";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}
		if ( isset($_GET['del_black_email']) ) {
			$have_update = true;

			$del_black_email = $_GET['del_black_email'];
			$del_black_email = str_replace($removed_chars, "", $del_black_email);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-boardcast` where `id`='".$del_black_email."'";

			if ($conn->query($sql) === TRUE) {
			  echo "Emails deleted successfully";
			} else {
			  echo "Error deleting emails: " . $conn->error;
			}

		}

		if ( isset($_GET['holiday_name']) 
			&& isset($_GET['holiday_type']) 
			&& isset($_GET['date']) 
		) {
			$have_update = true;
			
			// Delete all emails from the table
			$sql = "INSERT INTO `golf-holiday`(`holiday-name`, `holiday_type`, `holiday-date`) VALUES ('".$_GET['holiday_name']."','".$_GET['holiday_type']."','".$_GET['date']."')";

			if ($conn->query($sql) === TRUE) {
			  echo "Holiday adding successfully";
			} else {
			  echo "Error adding Holiday: " . $conn->error;
			}

		}


		if ( isset($_GET['del_holiday_name']) 
			&& isset($_GET['holiday_type']) 
			&& isset($_GET['date']) 
		) {
			$have_update = true;
			
			// Delete all emails from the table
			$sql = "INSERT INTO `golf-holiday`(`holiday-name`, `holiday_type`, `holiday-date`) VALUES ('".$_GET['holiday_name']."','".$_GET['holiday_type']."','".$_GET['date']."')";

			if ($conn->query($sql) === TRUE) {
			  echo "Holiday adding successfully";
			} else {
			  echo "Error adding Holiday: " . $conn->error;
			}

		}

		if ( isset($_GET['reflesh_government_holiday']) ) {
			$have_update = true;


			function cleanEncoding( $text, $type='standard' ){
				// determine the encoding before we touch it
				$encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
				// The characters to output
				if ( $type=='standard' ){
				    $outp_chr = array('...',          "'",            "'",            '"',            '"',            'â¢',            '-',            '-'); // run of the mill standard characters
				} elseif ( $type=='reference' ) {
				    $outp_chr = array('&#8230;',      '&#8216;',      '&#8217;',      '&#8220;',      '&#8221;',      '&#8226;',      '&#8211;',      '&#8212;'); // decimal numerical character references
				}
				// The characters to replace (purposely indented for comparison)
				    $utf8_chr = array("\xe2\x80\xa6", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", '\xe2\x80\xa2', "\xe2\x80\x93", "\xe2\x80\x94"); // UTF-8 hex characters
				    $winc_chr = array(chr(133),       chr(145),       chr(146),       chr(147),       chr(148),       chr(149),       chr(150),       chr(151)); // ASCII characters (found in Windows-1252)
				// First, replace UTF-8 characters.
				$text = str_replace( $utf8_chr, $outp_chr, $text);
				// Next, replace Windows-1252 characters.
				$text = str_replace( $winc_chr, $outp_chr, $text);
				// even if the string seems to be UTF-8, we can't trust it, so convert it to UTF-8 anyway
				$text = mb_convert_encoding($text, 'UTF-8', $encoding);
				return $text;
			}
			$json = file_get_contents('https://www.1823.gov.hk/common/ical/en.json');
			$json = 
					str_replace("\n", '', 
					str_replace("\r", '', 
					str_replace("\t", '', 
					str_replace(": ", ':', 
					str_replace("/", ':', 
						$json
					)))));
			$json = cleanEncoding($json);


			for ($i = 0; $i <= 31; ++$i) {
				$json = str_replace(chr($i), "", $json);
			}
			$json = str_replace(chr(127), "", $json);

			$json = explode('}]}', explode('"vevent":', $json)[1] )[0].'}]';

			// echo ($json);
			$data = json_decode($json, true);


			if (json_last_error() != JSON_ERROR_NONE) {
			    printf("JSON Error: %s", json_last_error_msg());
			}


			// var_dump($data);
			$events = $data;
			// $events = $data['vcalendar'][0]['vevent'];

			$result = array();

			// Delete all emails from the table
			// $sql = "DELETE FROM `applied-solar-holiday` WHERE 1";
			// if ($conn->query($sql) === TRUE) {
			//   echo "Holiday adding successfully";
			// } else {
			//   echo "Error adding Holiday: " . $conn->error;
			// }

			foreach ($events as $event) {
				$holiday_name = $event['summary'];
				$holiday_date = $event['dtstart'][0];
				$holiday_name = str_replace('\'','\\\'', $holiday_name);

			    $result[] = array(
			        'dtstart' => $event['dtstart'][0],
			        'summary' => $event['summary']
			    );

				// Delete all emails from the table
				$sql = "INSERT INTO `applied-solar-holiday`(`holiday-name`, `holiday-date`) VALUES ('".$holiday_name."','".$holiday_date."')";

				if ($conn->query($sql) === TRUE) {
				  echo "Holiday adding successfully";
				} else {
				  echo "Error adding Holiday: " . $conn->error;
				}

			}

			print_r($result);


			// die();
		}



		if ( isset($_GET['config_pay_name']) 
			&& isset($_GET['config_pay_period']) 
			&& isset($_GET['config_pay_value']) 
		) {
			$have_update = true;
			
		}

		if ($have_update) {
			?>
			<script type="text/javascript">
			window.location.href = "<?php echo this_page_link().'?'.'auth='.$auth.'&'.'datetime='.$datetime.'&'.'email='.$email; ?>";
			</script>
			<?php 
		}




function printAllPreviousSubmitParameters()
{
	echo '<input type="hidden" name="'.'auth'.'" value="'.$_GET['auth'].'">';
	echo '<input type="hidden" name="'.'datetime'.'" value="'.$_GET['datetime'].'">';
	echo '<input type="hidden" name="'.'email'.'" value="'.$_GET['email'].'">';
	
}
 ?>


<style type="text/css">
	td {
		width: 50%;
		vertical-align: text-top;
		border-style: outset;
	}
</style>
<br>

<?php 
// $sql = " SELECT `name` FROM `golf-staff` where `id`='".$_SESSION['name']."';";


 ?>
<a href="./" > < Back</a><br><hr>
<a href="?logout" > Logout </a><br>
<h1>白石高爾夫球練習場 -	 行政管理頁面</h1>
<h1>White Head Golf - Administration Management Page</h1>
<br>

<script type="text/javascript">
	setInterval(function() {
    window.location.reload();
	}, 3*60*1000);
	// 600000 milliseconds = 10 minutes
</script>

<h3 style="color: blue;">
Staff ID: <?php echo $_SESSION['name2']; ?><br>
Identity: <?php echo $_SESSION['identity']; ?><br>

</h3>
<table style="width: 100%;vertical-align: text-top;">
	<tr>
		<td style="vertical-align: text-top;">
			<hr>
<h2>預訂操作 <br> Booking operation</h2>
<hr>

<ol>
	<li>
		<hr>
		預訂記錄查詢<br>Booking Record Inquiry
		<hr>
		<ul>
			<li>
				<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, yellow, orange);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
"
				href="./searching_data_future_booking.php">

<span style='font-size: 50px;'>🗓️🗓️🗓️&#128284;</span><br>
				
				過去3個月和未來預訂查詢<br>
				Past 3 months and future reservation Inquiry</a>
				<br>
			</li>

<?php 
// if ($_SESSION['identity']!='part-time') {
 ?>
			<li>
				<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./searching_data_future_booking_history.php">
<span style='font-size: 50px;'>&#128366; &#10060;</span><br>
預訂歷史及支付失敗查詢<br>History Record and Failed Payment Record Inquiry</a>
<br>
			</li>
			<li>
				<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./record_cybersource.php">
<span style='font-size: 50px;'>&#128179; &#128177;</span><br>

Cybersource交易歷史<br>Cybersource Transaction History</a>
<br>
			</li>
<?php 
// }
 ?>
		</ul>
		
	</li>

	<li>
		<hr>
		進行預訂<br>Make a reservation
		<hr>
		<ul>




<?php 
// if ($_SESSION['identity']!='part-time') {
 ?>
			<li><a 
style="
	color: blue;
	background-image: linear-gradient(to right top, yellow, orange);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./booking-records-bydate-2.php">
<span style='font-size: 50px;'>&#128100; &#128101; &#129534;</span><br>

動態 閱覽/預訂 表 (團體預訂或現場預訂 - 小按鈕版本)
				<br>
			Interactive booking form (Group booking or walk in booking - Small button version)</a>
<br></li>
<!-- 
			<li><a 
style="
	color: blue;
	background-image: linear-gradient(to right top, yellow, orange);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./booking-records-bydate.php">
<span style='font-size: 50px;'>&#128100; &#128101; &#129534;</span><br>

動態 閱覽/預訂 表 (團體預訂或現場預訂)
				<br>
			Interactive booking form (Group booking or walk in booking)</a>
<br></li> -->

<?php 
// }
 ?>
<!-- 
			<li><a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./booking-records.php">

<span style='font-size: 50px;'>&#127988;&#127988;&#127988;&#127988;&#127988;&#127988;&#127988;</span><br>

閱覽未來7天的打球道<br>Unbooked Fairway In Future 7 Days</a>
<br></li>
 -->

<?php 
// if ($_SESSION['identity']!='part-time') {
 ?>
			<li>
				<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./input-form.php" target="_blank">
<span style='font-size: 50px;'>&#128221;</span><br>

即場預約 (線上預訂相同的介面)<br>Walkin Booking (Online Booking Interface)</a>
<br>
			</li>
<?php 
// }
 ?>
			<!-- <li>銀行交易或支票 記帳<br>Keep accounts for bank transaction or pay check</li> -->
		</ul>
		
	</li>
	

	<li>
		<hr>
		雜項<br>Miscellaneous
			<hr>
<table>
	<tr>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./admin-locker.php">置物櫃 登記<br> Locker registration  </a>
		</td>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./searching_data_locker.php">  置物櫃搜尋引擎 <br> Locker Search Engine </a>
		</td>
	</tr>
	<tr>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./input-retails.php">零售登記 <br> Retails Registration</a>
		</td>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./search-data-input-retails.php">零售 搜尋引擎 <br> Retails Search Engine</a>
		</td>
	</tr>
	<tr>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./searching_data_golf_club_rental.php">高爾夫球桿租賃 登記 <br> Golf Club Rental Registration</a>
		</td>
		<td>
			<a 
				style="
					color: blue;
					background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
					padding: 30px;
					width: 200px;
					border-radius: 30px;
					display: block;
					text-align: center;
				 	cursor: pointer;
				" href="./searching_data_golf_club_rental.php">高爾夫球桿租賃 搜尋引擎 <br> Golf Club Rental Search Engine</a>
		</td>
	</tr>
</table>
	</li>
	

<?php 
if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
 ?>
	<li>
			報告<br>Report
			<hr>
			<ul>

		<?php 
		if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
		 ?>
				<li>
					<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, orange, yellow);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./Report_Monthly.php">月度報告<br>Monthly Report</a>
<br></li>

		<?php 
		}
		 ?>
				<li>
					<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./IncomeBalanceSheetOnTheDay.php">預約日期當天 收入統算表<br>Income balance sheet on the day</a>
<br></li>

				<li>
					<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./download_report.php">每日報告下載<br>Daily Report Download</a>
<br></li>
				</li>
				<li>
					
			<a 
style="
	color: blue;
	background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	padding: 30px;
	width: 350px;
	border-radius: 30px;
	display: block;
	text-align: center;
 	cursor: pointer;
" href="./download.php">系統數據下載<br>System Data Download</a>
			<hr>
				</li>
<?php 
}
 ?>

			</ul>
	</li>
				<li>
					埋數 Clock-out
					<ul>
						<li>
								<hr>
								<a 
					style="
						color: blue;
						background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
						padding: 30px;
						width: 350px;
						border-radius: 30px;
						display: block;
						text-align: center;
					 	cursor: pointer;
					" href="./check-report.php?way=individual">埋數和報告下載 - 個別職員<br>Clock-out And Download Report  - Individual Staff</a>
								<hr>
						</li>

		<?php 
		if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
		 ?>
						<li>
								<hr>
								<a 
					style="
						color: blue;
						background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
						padding: 30px;
						width: 350px;
						border-radius: 30px;
						display: block;
						text-align: center;
					 	cursor: pointer;
					" href="./check-report.php?way=all">埋數和報告下載 - 全體員工<br>Clock-out And Download Report - All Staff</a>
								<hr>
						</li>

		<?php 
		}
		 ?>
					</ul>
				</li>
</ol>


<hr>

		</td>



<?php 
if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
 ?>
		<td>
			

			<table>
				<tr>
					<td>
								<hr>
								<h2>新增或刪除 員工帳戶 <br> Add or delete employee account</h2>
								<hr>

								<form action="<?php echo this_page_link(); ?>">
								<?php printAllPreviousSubmitParameters(); ?>
								Add employee account:<br>
								<input type="hidden" name="add_staff"> 
								<input type="text" name="name" placeholder="Enter the employee name" autocomplete="off"><br>
								<select name="type">
									<option value="full-time">Full-time</option>
									<option value="part-time">Part-time</option>
									<option value="manager">Manager</option>
								</select><br>
								<input type="text" name="id" placeholder="Enter the Username" autocomplete="off"><br>
								<input type="text" name="password" placeholder="Enter the password" autocomplete="off"><br>
								<input type="submit" name="Submit">
								<hr>
								</form>

								<form action="<?php echo this_page_link(); ?>">
								<?php printAllPreviousSubmitParameters(); ?>
								Delete employee account: 
								<input type="text" name="del_staff" placeholder="Enter the username">
								<input type="submit" name="Submit">
								<hr>
								</form>

								<hr>

								<table>
									<tr>
										<th>Name</th>
										<th>Employee Position</th>
										<th style="width: 5%;">Login username</th>
										<th>Password</th>
										<th>Create-Date</th>
									</tr>
								<?php 

								$sql = "SELECT `id`, `type`, `name`, `create-datetime`, `password` FROM `golf-staff` order by `type`;";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
								  // Output data of each row
								  while($row = $result->fetch_assoc()) {
								    echo "<tr><td>" . $row["name"] . "</td><td>" . $row["type"] . "</td><td style=\"width: 5%;\">" . $row["id"] . "</td><td>" . $row["password"] . "</td><td style=\"white-space: nowrap;\">" . $row["create-datetime"] . "</td></tr>";
								  }
								} else {
								  echo "<tr><td>No employee account</td></tr>";
								}

								 ?>
								</table>

								<hr>
					</td>
				</tr>







				<tr>
					<td>
						
								<h2>新增或刪除廣播通知訊息 <br> Add or delete broadcast notification message</h2>
								<hr>

								<form action="<?php echo this_page_link(); ?>">
								<?php printAllPreviousSubmitParameters(); ?>
								Add boardcast notification message:
								<textarea name="add_boardcast"></textarea> 
								<input type="submit" name="Submit">
								<hr>
								</form>

								<form action="<?php echo this_page_link(); ?>">
								<?php printAllPreviousSubmitParameters(); ?>
								Delete boardcast notification message: 
								<input type="number" name="del_boardcast" placeholder="Enter the (ID) number">
								<input type="submit" name="Submit">
								<hr>
								</form>

								<hr>

								<table>
									<tr>
										<th style="width: 5%;">ID</th>
										<th>Boardcast Message</th>
									</tr>
								<?php 

								$sql = "SELECT `id`, `boardcast-message` FROM `golf-boardcast`;";
								$result = $conn->query($sql);

								if ($result->num_rows > 0) {
								  // Output data of each row
								  while($row = $result->fetch_assoc()) {
								    echo "<tr><td style=\"width: 5%;\">" . $row["id"] . "</td><td>" . base64_decode(	$row["boardcast-message"] ) . "</td></tr>";
								  }
								} else {
								  echo "<tr><td>No boardcast</td></tr>";
								}

								 ?>
								</table>

								<hr>
					</td>
				</tr>

<?php 
if ($_SESSION['identity']=='admin') {
 ?>
				<tr>
					<td>
															
									<h2>新增或刪除管理員電子郵件 <br> Add or delete the administrator email</h2>
									<hr>

									<form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Add administration email address: 
									<input type="email" name="add_email">
									<input type="submit" name="Submit">
									</form>
									<hr>

									<form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Delete administration email address: 
									<input type="email" name="del_email">
									<input type="submit" name="Submit">
									</form>

									<hr>
									<table>
									<?php 

									$sql = "SELECT `email-address` FROM `golf-administration-email`;";
									$result = $conn->query($sql);

									if ($result->num_rows > 0) {
									  // Output data of each row
									  while($row = $result->fetch_assoc()) {
									    echo "<tr><td>" . $row["email-address"] . "</td></tr>";
									  }
									} else {
									  echo "<tr><td>No emails found</td></tr>";
									}

									 ?>
									</table>

					</td>
				</tr>
<?php } ?>

				<tr>
					<td>
															
									<h2>新增或刪除黑名單電子郵件地址 <br> Add or delete blacklist email address</h2>
									<hr>


									<form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Add blacklist email address: 
									<input type="email" name="add_black_email">
									<input type="submit" name="Submit">
									<hr>
									</form>

									<form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Delete blacklist email address: 
									<input type="email" name="del_black_email">
									<input type="submit" name="Submit">
									<hr>
									</form>

									<hr>


									<!-- <form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Add holiday: <br>
									  <label for="holiday_name">Holiday Name:</label>
									  <input type="text" id="holiday_name" name="holiday_name" required><br>

									  <label for="holiday_type">Holiday Type:</label>
									  <select id="holiday_type" name="holiday_type">
									    <option value="">Select</option>
									    <option value="Lunar">Lunar</option>
									    <option value="Solar">Solar</option>
									  </select><br>

									  <label for="date">Date:</label>
									  <input type="date" id="date" name="date" required><br>

									  <input type="submit" value="Submit">
									</form> -->

									<!-- <form action="<?php echo this_page_link(); ?>">
									<?php printAllPreviousSubmitParameters(); ?>
									Delete holiday: <br>
									  <label for="del_holiday_name">Holiday Name:</label>
									  <input type="text" id="del_holiday_name" name="del_holiday_name" required><br>
									  <input type="submit" value="Submit">
									</form> -->

					</td>
				</tr>
			</table>
			<hr>
			
			<iframe src="./prompt-interact.php" style="width: 100%;height: 1000px;" ></iframe>
		</td>

<?php } ?>
	</tr>
	<tr>
		<td>

			
		</td>

<?php 
if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
 ?>
		<td>

		</td>
<?php } ?>
	</tr>
</table>


<!-- 

							<table>
								<tr>
									<th></th>
									<th></th>
									<th></th>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</table>
 -->











<hr>




<?php 
if ($_SESSION['identity']=='admin' || $_SESSION['identity']=='manager') {
 ?>
<hr>
<h2>透過政府 API 更新假期 <br> Renew holiday from government API</h2>
<hr>


<form action="<?php echo this_page_link(); ?>">
<?php printAllPreviousSubmitParameters(); ?>
  <input type="hidden" name="reflesh_government_holiday"><br>
  <input type="submit" value="Renew holiday from government API">
</form>




<hr>
<hr>

<table style="width: 100%;">
	<tr>
		<th>Holiday Name</th>
		<!-- <th>Holiday Type</th> -->
		<th>Holiday Date</th>
<!-- 		<th>Converted Type</th>
		<th>Converted Date</th> -->
	</tr>
<?php 

$sql = "SELECT `holiday-name`, `holiday-date` FROM `applied-solar-holiday` order by `holiday-date` desc, `holiday-name` asc limit 50;";
$result = $conn->query($sql);

require_once 'DateConvert.php';

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row["holiday-name"] . "</td>";
  	echo "<td>" . $row["holiday-date"] . "</td>";
  	// echo "<td>" . $row["holiday_type"] . "</td>";

    // echo "<td>" . date("Y").'-'.$row["holiday-date"] . "</td>";
    // if ($row["holiday_type"]=='Solar') {
    // 	echo "<td>" . 'Lunar' . "</td>";
    // 	echo "<td>" . convertSolarToLunar(date("Y").'-'.$row["holiday-date"]) . "</td>";
    // } else if ($row["holiday_type"]=='Lunar') {
    // 	echo "<td>" . 'Solar' . "</td>";
    // 	echo "<td>" . convertLunarToSolar(date("Y").'-'.$row["holiday-date"]) . "</td>";
    // }


    echo "</tr>";
  }
} else {
  echo "<tr><td>No holiday</td></tr>";
}

 ?>
</table>

<?php } ?>


<?php 	
 ?>

<?php
		die();
	} else {
    	?>
<script type="text/javascript">
	alert('Incorrect token.');
</script>
<?php

	}
}

?>

<!-- 
<style type="text/css">
	html, body {
		text-align: center;
		vertical-align: middle;

		width: 100%;
		height: 100%;

	}
	input {
		border-radius: 10px;
		width: 50%;
		font-size: 30px;
	}
	div	{
		box-shadow: 3px 3px 5px rgba(0.3, 0.3, 0.3, 0.3);
		width: 80%;
		height: 80%;
		margin: 100px;
	}
</style>
<div>
	<h1>Please enter an email address for system administration and then check the mailbox</h1>
	<form action="./configuration-administraion.php">
		<input type="email" name="email">
		<br>
		<input type="submit" name="Confirm">
	</form>
</div>
 -->
<!DOCTYPE html>
<html>
<head>
<title>Email Address Entry</title>
</head>
<body>



<table style="width: 	100%;">	
		<tr>
			<th style="width: 50%;">
				職員<br>
				Staff
			</th>
			<th style="width: 50%;">
				管理頁面<br>
				Administration
			</th>
		</tr>
		<tr>	
				<td style="border-style: double;">	
					<?php 

if (isset($_GET['action']) && $_GET['action']=='staff_page') {
	$username = mb_ereg_replace("'","", $_GET['id']);
	$password = mb_ereg_replace("'","", $_GET['password']);
	$sql = "SELECT `name`,`type` from `golf-staff` where `id`='$username' and `password`='$password';";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	  while($row = $result->fetch_assoc()) {
	  	$name = $row['name'];
	  	$type = $row['type'];
	  	// echo "Welcome $name";
	  	if ($type == 'full-time') {
				$md5_str=$currentDateTime.'_full-time_'.$email;
				$auth=md5($md5_str);
				$fullUrl = this_page_link();
				$url_ = $fullUrl.'?auth='.$auth.'&datetime='.$currentDateTime.'&email='.$email.'&name='.$username.'&name2='.$name;
				header("Location: $url_");
	  	}
	  	if ($type == 'part-time') {
				$md5_str=$currentDateTime.'_part-time_'.$email;
				$auth=md5($md5_str);
				$fullUrl = this_page_link();
				$url_ = $fullUrl.'?auth='.$auth.'&datetime='.$currentDateTime.'&email='.$email.'&name='.$username.'&name2='.$name;
				header("Location: $url_");
	  	}
	  	if ($type == 'manager') {
				$md5_str=$currentDateTime.'_manager_'.$email;
				$auth=md5($md5_str);
				$fullUrl = this_page_link();
				$url_ = $fullUrl.'?auth='.$auth.'&datetime='.$currentDateTime.'&email='.$email.'&name='.$username.'&name2='.$name;
				header("Location: $url_");
	  	}
			die();
	  }
	} else {
		echo "Login failed";
	}
}

					 ?>
					<form action="./configuration-administraion.php">
						<input type="hidden" name="action" value="staff_page">
						<table>	
								<tr>
									<td>
										使用者名稱<br>
										Username
									</td>
									<td>
										<input type="text" name="id" placeholder="Username">
									</td>
								</tr>
								<tr>
									<td>
										密碼<br>
										Password
									</td>
									<td>
										<input type="text" name="password" placeholder="Password">
									</td>
								</tr>
								<tr>
									<td>
										
									</td>
									<td>
										<input type="submit" value="登入 Login">
									</td>
								</tr>
							</table>
						</form>
				</td>
				<td style="border-style: double;">	

<br>
		請輸入電子郵件地址，然後我們將發送包含管理頁面網址的電子郵件<br>
		Please enter the email address and then we will send an email with administration page URL

<br>
<br>
<form action="./configuration-administraion.php">
  <label for="email">電子郵件地址 Email:</label><br>
  <input type="hidden" name="action" value="admin_page">
  <input type="email" id="email" name="email" placeholder="電子郵件地址 Superuser Email Address" required><br>
<br>
  <input type="submit" value="登入 Login">
<br>
<br>
</form>

				</td>
		</tr>
</table>
<p></p>

</body>
</html>



<?php 
if (!isset($_GET['email'])) {
    die();
}
$email = $_GET['email'];

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
SELECT * FROM `golf-administration-email` where `email-address`='$email';
";
$result = $conn->query($sql);





if ($result->num_rows > 0) {
    while ($booking_arr = $result->fetch_assoc()) {
    	?>
<script type="text/javascript">
	alert('The administration interface sent');
</script>
<?php

// Set the desired timezone (e.g., 'America/New_York')
date_default_timezone_set('America/New_York');

// Get the current date and time
$currentDateTime = date('YmdHis');

// echo "Current date and time: $currentDateTime";








// Set these parameters
$subject = '白石高爾夫球練習場 - 行政管理頁面 | White Head Golf - Administration Management Page'; // Subject of the email
$emailadd = 'support@cpospay.com'; // Your email address (where the form information will be sent)
$url = 'thanks.php'; // Redirect URL after form processing
$req = '0'; // Set to '1' to make all fields required, '0' to allow empty fields

$md5_str=$currentDateTime.'_'.$email;
$auth=md5($md5_str);
$fullUrl = this_page_link();
$url_ = $fullUrl.'?auth='.$auth.'&datetime='.$currentDateTime.'&email='.$email.'&name=superuser';
$text = "
____________________________________________________________________________________________________________________

Administration Management Page:
$url_

____________________________________________________________________________________________________________________


";
$space = ' ';
$line = ' ';

// $md5_str
// $auth
// email-confirmation

// Send the email
mail($email, $subject, $text, 'From: ' . $emailadd);

















    }
} else {

    	?>
<script type="text/javascript">
	alert('This email address is not valid, <?php echo $email; ?>');
</script>
    	<?php
}























$conn->close();



 ?>