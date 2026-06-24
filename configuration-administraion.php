<?php

ini_set(
	'session.gc_maxlifetime',
	// 7*24*3600
	65535
);
ini_set('session.cookie_lifetime', 7 * 24 * 3600); // 1 week
session_set_cookie_params(7 * 24 * 3600);

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

if (isset($_GET['auth']) && isset($_GET['datetime']) && isset($_GET['email'])) {


	$have_update = false;

	$auth = $_GET['auth'];
	$datetime = $_GET['datetime'];
	// $datetime is in the format of "YmdHis", e.g. "20240630153045".
	// check if $datetime is within 30 minutes from current time
	$current_time = time();
	$datetime_time = strtotime($datetime);
	if (abs($current_time - $datetime_time) > 3 * 24 * 3600 && false) {
		// If the time difference is greater than 3 days, do not authenticate
		echo "Authentication failed: Time difference is greater than 3 days.";
		echo "Provided Datetime: " . date("Y-m-d H:i:s", $datetime_time) . " Current Datetime: " . date("Y-m-d H:i:s", $current_time);
		exit();
	}
	$email = $_GET['email'];

	if (isset($_GET['name'])) {
		$_SESSION['name'] = $_GET['name'];
	}
	if (isset($_GET['name2'])) {
		$_SESSION['name2'] = $_GET['name2'];
	}



	$md5_str = $datetime . '_' . $email;
	$md5 = md5($md5_str);
	// echo "$md5_str $md5";
	if ($auth == $md5) {
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

	$md5_str = $datetime . '_full-time_' . $email;
	$md5 = md5($md5_str);
	if ($auth == $md5) {
		$_SESSION["management"] = $md5;
		$_SESSION["auth"] = $auth;
		$_SESSION["datetime"] = $datetime;
		$_SESSION["email"] = $email;
		$_SESSION["identity"] = 'full-time';
	}

	$md5_str = $datetime . '_part-time_' . $email;
	$md5 = md5($md5_str);
	if ($auth == $md5) {
		$_SESSION["management"] = $md5;
		$_SESSION["auth"] = $auth;
		$_SESSION["datetime"] = $datetime;
		$_SESSION["email"] = $email;
		$_SESSION["identity"] = 'part-time';
	}

	$md5_str = $datetime . '_manager_' . $email;
	$md5 = md5($md5_str);
	if ($auth == $md5) {
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
		if (isset($_GET['add_staff'])) {
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
		if (isset($_GET['add_email'])) {
			$have_update = true;

			$add_email = $_GET['add_email'];
			$add_email = str_replace($removed_chars, "", $add_email);

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-administration-email` (`email-address`) values ('" . $add_email . "');";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}
		if (isset($_GET['del_email'])) {
			$have_update = true;

			$del_email = $_GET['del_email'];
			$del_email = str_replace($removed_chars, "", $del_email);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-administration-email` where `email-address`='" . $del_email . "'";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}
		if (isset($_GET['add_boardcast'])) {
			$have_update = true;

			$add_boardcast = $_GET['add_boardcast'];
			$add_boardcast = str_replace($removed_chars, "", $add_boardcast);
			$add_boardcast = base64_encode($add_boardcast);

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-boardcast` (`boardcast-message`) values ('" . $add_boardcast . "');";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}
		if (isset($_GET['del_boardcast'])) {
			$have_update = true;

			$del_boardcast = $_GET['del_boardcast'];
			$del_boardcast = str_replace($removed_chars, "", $del_boardcast);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-boardcast` where `id`='" . $del_boardcast . "'";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}
		if (isset($_GET['add_black_email'])) {
			$have_update = true;

			$add_black_email = $_GET['add_black_email'];
			$add_black_email = str_replace($removed_chars, "", $add_black_email);

			// Delete all emails from the table
			$sql = "INSERT INTO black_list_email (`email-address`) values ('" . $add_black_email . "');";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}
		if (isset($_GET['del_black_email'])) {
			$have_update = true;

			$del_black_email = $_GET['del_black_email'];
			$del_black_email = str_replace($removed_chars, "", $del_black_email);

			// Delete all emails from the table
			$sql = "DELETE FROM `golf-boardcast` where `id`='" . $del_black_email . "'";

			if ($conn->query($sql) === TRUE) {
				echo "Emails deleted successfully";
			} else {
				echo "Error deleting emails: " . $conn->error;
			}
		}

		if (
			isset($_GET['holiday_name'])
			&& isset($_GET['holiday_type'])
			&& isset($_GET['date'])
		) {
			$have_update = true;

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-holiday`(`holiday-name`, `holiday_type`, `holiday-date`) VALUES ('" . $_GET['holiday_name'] . "','" . $_GET['holiday_type'] . "','" . $_GET['date'] . "')";

			if ($conn->query($sql) === TRUE) {
				echo "Holiday adding successfully";
			} else {
				echo "Error adding Holiday: " . $conn->error;
			}
		}


		if (
			isset($_GET['del_holiday_name'])
			&& isset($_GET['holiday_type'])
			&& isset($_GET['date'])
		) {
			$have_update = true;

			// Delete all emails from the table
			$sql = "INSERT INTO `golf-holiday`(`holiday-name`, `holiday_type`, `holiday-date`) VALUES ('" . $_GET['holiday_name'] . "','" . $_GET['holiday_type'] . "','" . $_GET['date'] . "')";

			if ($conn->query($sql) === TRUE) {
				echo "Holiday adding successfully";
			} else {
				echo "Error adding Holiday: " . $conn->error;
			}
		}

		if (isset($_GET['reflesh_government_holiday'])) {
			$is_ajax = isset($_GET['ajax']) && $_GET['ajax'] === '1';
			if (!$is_ajax) {
				$have_update = true;
			}

			function cleanEncoding($text, $type = 'standard')
			{
				// determine the encoding before we touch it
				$encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
				// The characters to output
				if ($type == 'standard') {
					$outp_chr = array('...',          "'",            "'",            '"',            '"',            'â¢',            '-',            '-'); // run of the mill standard characters
				} elseif ($type == 'reference') {
					$outp_chr = array('&#8230;',      '&#8216;',      '&#8217;',      '&#8220;',      '&#8221;',      '&#8226;',      '&#8211;',      '&#8212;'); // decimal numerical character references
				}
				// The characters to replace (purposely indented for comparison)
				$utf8_chr = array("\xe2\x80\xa6", "\xe2\x80\x98", "\xe2\x80\x99", "\xe2\x80\x9c", "\xe2\x80\x9d", '\xe2\x80\xa2', "\xe2\x80\x93", "\xe2\x80\x94"); // UTF-8 hex characters
				$winc_chr = array(chr(133),       chr(145),       chr(146),       chr(147),       chr(148),       chr(149),       chr(150),       chr(151)); // ASCII characters (found in Windows-1252)
				// First, replace UTF-8 characters.
				$text = str_replace($utf8_chr, $outp_chr, $text);
				// Next, replace Windows-1252 characters.
				$text = str_replace($winc_chr, $outp_chr, $text);
				// even if the string seems to be UTF-8, we can't trust it, so convert it to UTF-8 anyway
				$text = mb_convert_encoding($text, 'UTF-8', $encoding);
				return $text;
			}
			$json = file_get_contents('https://www.1823.gov.hk/common/ical/en.json');
			$json =
				str_replace(
					"\n",
					'',
					str_replace(
						"\r",
						'',
						str_replace(
							"\t",
							'',
							str_replace(
								": ",
								':',
								str_replace(
									"/",
									':',
									$json
								)
							)
						)
					)
				);
			$json = cleanEncoding($json);


			for ($i = 0; $i <= 31; ++$i) {
				$json = str_replace(chr($i), "", $json);
			}
			$json = str_replace(chr(127), "", $json);

			$json = explode('}]}', explode('"vevent":', $json)[1])[0] . '}]';

			// echo ($json);
			$data = json_decode($json, true);


			if (json_last_error() != JSON_ERROR_NONE) {
				if ($is_ajax) {
					echo json_encode(['success' => false, 'error' => 'JSON parse error: ' . json_last_error_msg()]);
					die();
				} else {
					printf("JSON Error: %s", json_last_error_msg());
				}
			}


			// var_dump($data);
			$events = $data;
			// $events = $data['vcalendar'][0]['vevent'];

			$result = array();
			$insert_count = 0;
			$error_count = 0;

			// Delete all existing holiday records first, then insert fresh
			$sql_del = "DELETE FROM `applied-solar-holiday` WHERE 1";
			$conn->query($sql_del);

			foreach ($events as $event) {
				$holiday_name = $event['summary'];
				$holiday_date = $event['dtstart'][0];
				$holiday_name = str_replace('\'', '\\\'', $holiday_name);

				$result[] = array(
					'dtstart' => $event['dtstart'][0],
					'summary' => $event['summary']
				);

				// Insert each holiday
				$sql = "INSERT INTO `applied-solar-holiday`(`holiday-name`, `holiday-date`) VALUES ('" . $holiday_name . "','" . $holiday_date . "')";

				if ($conn->query($sql) === TRUE) {
					$insert_count++;
				} else {
					$error_count++;
				}
			}

			if ($is_ajax) {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode([
					'success' => true,
					'total' => count($result),
					'inserted' => $insert_count,
					'errors' => $error_count,
					'holidays' => $result
				], JSON_UNESCAPED_UNICODE);
				die();
			} else {
				echo "Holidays refreshed: {$insert_count} inserted, {$error_count} errors.";
			}
		}



		if (
			isset($_GET['config_pay_name'])
			&& isset($_GET['config_pay_period'])
			&& isset($_GET['config_pay_value'])
		) {
			$have_update = true;
		}

		if ($have_update) {
?>
			<script type="text/javascript">
				window.location.href = "<?php echo this_page_link() . '?' . 'auth=' . $auth . '&' . 'datetime=' . $datetime . '&' . 'email=' . $email; ?>";
			</script>
		<?php
		}




		function printAllPreviousSubmitParameters()
		{
			echo '<input type="hidden" name="' . 'auth' . '" value="' . $_GET['auth'] . '">';
			echo '<input type="hidden" name="' . 'datetime' . '" value="' . $_GET['datetime'] . '">';
			echo '<input type="hidden" name="' . 'email' . '" value="' . $_GET['email'] . '">';
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
		<a href="./">
			< Back</a><br>
				<hr>
				<a href="?logout"> Logout </a><br>
				<h1>白石高爾夫球練習場 - 行政管理頁面</h1>
				<h1>White Head Golf - Administration Management Page</h1>
				<br>

				<script type="text/javascript">
					setInterval(function() {
						window.location.reload();
					}, 3 * 60 * 1000);
					// 600000 milliseconds = 10 minutes
				</script>

				<h3 style="color: blue;">
					Staff ID: <?php echo $_SESSION['name2']; ?><br>
					Identity: <?php echo $_SESSION['identity']; ?><br>

				</h3>

				<style>
					.config-layout {
						display: flex;
						width: 100%;
						min-height: 600px;
						border: 1px solid #bfdbfe;
						border-radius: 10px;
						overflow: hidden;
						box-shadow: 0 1px 3px rgba(59, 130, 246, 0.1);
					}

					.config-sidebar {
						width: 220px;
						min-width: 220px;
						background: #fff;
						color: #1e40af;
						padding: 0;
						flex-shrink: 0;
						border-right: 1px solid #bfdbfe;
					}

					.config-sidebar .sidebar-title {
						display: none;
					}

					.tab-btn {
						display: block;
						width: 100%;
						padding: 12px 18px;
						border: none;
						background: none;
						color: #475569;
						text-align: left;
						font-size: 14px;
						cursor: pointer;
						border-left: 3px solid transparent;
						transition: all 0.2s;
						line-height: 1.4;
					}

					.tab-btn small {
						display: block;
						font-size: 11px;
						color: #94a3b8;
						font-weight: 400;
						margin-top: 1px;
					}

					.tab-btn.active small {
						color: #60a5fa;
					}

					.tab-btn:hover {
						background: #eff6ff;
						color: #1e40af;
					}

					.tab-btn.active {
						background: #dbeafe;
						color: #1d4ed8;
						border-left-color: #2563eb;
						font-weight: 600;
					}

					.config-content {
						flex: 1;
						padding: 20px;
						background: #fff;
						overflow-y: auto;
					}

					.tab-pane {
						display: none;
					}

					.tab-pane.active {
						display: block;
					}

				/* ── Booking Operations card layout ──────────── */
				.booking-section { margin-bottom: 32px; }
				.booking-section-title {
					font-size: 16px; font-weight: 700; color: #1e40af;
					border-left: 4px solid #3b82f6; padding-left: 12px;
					margin-bottom: 16px; line-height: 1.5;
				}
				.booking-section-title small { font-size: 12px; color: #6b7280; font-weight: 400; display: block; }
				.booking-cards { display: flex; flex-wrap: wrap; gap: 16px; }
				.booking-card {
					display: block; text-decoration: none; color: #1e40af;
					background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
					padding: 26px 20px 22px; width: 215px; min-height: 160px;
					text-align: center; transition: all 0.25s ease;
					box-shadow: 0 1px 3px rgba(0,0,0,0.05);
					position: relative; overflow: hidden;
				}
				.booking-card::before {
					content: ''; position: absolute; top: 0; left: 0; right: 0;
					height: 4px; background: #93c5fd; border-radius: 14px 14px 0 0;
					transition: height 0.25s ease;
				}
				.booking-card:hover::before { height: 5px; }
				.booking-card:hover {
					border-color: #93c5fd;
					box-shadow: 0 10px 28px rgba(59,130,246,0.13);
					transform: translateY(-4px); color: #1d4ed8;
				}
				.booking-card:active { transform: translateY(-1px); }
				.booking-card .card-icon {
					font-size: 44px; display: block; margin-bottom: 12px;
					line-height: 1; transition: transform 0.25s ease;
				}
				.booking-card:hover .card-icon { transform: scale(1.08); }
				.booking-card .card-label { font-size: 14px; font-weight: 600; line-height: 1.4; }
				.booking-card .card-sublabel { font-size: 11px; color: #6b7280; font-weight: 400; display: block; margin-top: 3px; }
				/* ── Section-specific top-bar colors ── */
				.booking-card.card-inquiry::before { background: #3b82f6; }
				.booking-card.card-reserve::before { background: #f59e0b; }
				.booking-card.card-misc::before   { background: #8b5cf6; }
				.booking-card.card-report::before  { background: #f97316; }
				.booking-card.card-clock::before   { background: #10b981; }
				/* Hover glow tint per section */
				.booking-card.card-inquiry:hover { box-shadow: 0 10px 28px rgba(59,130,246,0.13); }
				.booking-card.card-reserve:hover { box-shadow: 0 10px 28px rgba(245,158,11,0.13); }
				.booking-card.card-misc:hover   { box-shadow: 0 10px 28px rgba(139,92,246,0.13); }
				.booking-card.card-report:hover  { box-shadow: 0 10px 28px rgba(249,115,22,0.13); }
				.booking-card.card-clock:hover   { box-shadow: 0 10px 28px rgba(16,185,129,0.13); }
			</style>

			<script>
					document.addEventListener('DOMContentLoaded', function() {
						var btns = document.querySelectorAll('.tab-btn');
						var panes = document.querySelectorAll('.tab-pane');
						btns.forEach(function(btn) {
							btn.addEventListener('click', function() {
								var tabId = this.getAttribute('data-tab');
								btns.forEach(function(b) {
									b.classList.remove('active');
								});
								panes.forEach(function(p) {
									p.classList.remove('active');
								});
								this.classList.add('active');
								var pane = document.getElementById(tabId);
								if (pane) pane.classList.add('active');
							});
						});
					});
				</script>

				<div class="config-layout">
					<nav class="config-sidebar">
						<div class="sidebar-title">&#9776; 菜單 / Menu</div>
						<button class="tab-btn active" data-tab="tab-bookings">&#128203; 預訂操作<br><small>Booking Operations</small></button>
						<?php if ($_SESSION['identity'] == 'admin' || $_SESSION['identity'] == 'manager') { ?>
							<button class="tab-btn" data-tab="tab-staff">&#128100; 員工帳戶<br><small>Employee Accounts</small></button>
							<button class="tab-btn" data-tab="tab-boardcast">&#128226; 廣播通知<br><small>Broadcast</small></button>
							<?php if ($_SESSION['identity'] == 'admin') { ?>
								<button class="tab-btn" data-tab="tab-email">&#128231; 管理員電郵<br><small>Admin Emails</small></button>
							<?php } ?>
							<button class="tab-btn" data-tab="tab-blacklist">&#128683; 黑名單<br><small>Blacklist</small></button>
							<button class="tab-btn" data-tab="tab-holiday">&#128197; 透過政府 API 更新假期<br><small>Govt Holiday Renewal</small></button>
							<button class="tab-btn" data-tab="tab-prompt">&#129302; 通知提示配置<br><small>Notice Prompt Configuration</small></button>
							<button class="tab-btn" data-tab="tab-sysvar">&#9881; 系統變數<br><small>System Variables</small></button>
						<?php } ?>
					</nav>
					<div class="config-content">

						<div class="tab-pane active" id="tab-bookings">

						<h2 style="color:#1e40af;margin-bottom:6px;">預訂操作 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Booking Operations</span></h2>
						<hr>

						<!-- 預訂記錄查詢 -->
						<div class="booking-section">
							<div class="booking-section-title">&#128269; 預訂記錄查詢 <small>Booking Record Inquiry</small></div>
							<div class="booking-cards">
								<a class="booking-card" href="./searching_data_future_booking.php">
									<span class="card-icon">🗓️</span>
									<span class="card-label">過去3個月和未來預訂查詢</span>
									<span class="card-sublabel">Past 3 months and future reservation inquiry</span>
								</a>
								<a class="booking-card" href="./searching_data_future_booking_history.php">
									<span class="card-icon">&#128366;</span>
									<span class="card-label">預訂歷史及支付失敗查詢</span>
									<span class="card-sublabel">History Record and Failed Payment Record Inquiry</span>
								</a>
								<a class="booking-card" href="./record_cybersource.php">
									<span class="card-icon">&#128179;</span>
									<span class="card-label">Cybersource 交易歷史</span>
									<span class="card-sublabel">Cybersource Transaction History</span>
								</a>
							</div>
						</div>

						<!-- 進行預訂 -->
						<div class="booking-section">
							<div class="booking-section-title">&#128221; 進行預訂 <small>Make a Reservation</small></div>
							<div class="booking-cards">
								<a class="booking-card card-accent" href="./booking-records-bydate-2.php">
									<span class="card-icon">&#128100;</span>
									<span class="card-label">動態閱覽/預訂表</span>
									<span class="card-sublabel">Interactive booking form (Group / Walk-in)</span>
								</a>
								<a class="booking-card" href="./input-form.php" target="_blank">
									<span class="card-icon">&#128221;</span>
									<span class="card-label">即場預約</span>
									<span class="card-sublabel">Walk-in Booking (Online Booking Interface)</span>
								</a>
							</div>
						</div>

						<!-- 雜項 -->
						<div class="booking-section">
							<div class="booking-section-title">&#128230; 雜項 <small>Miscellaneous</small></div>
							<div class="booking-cards">
								<a class="booking-card" href="./admin-locker.php">
									<span class="card-icon">&#128274;</span>
									<span class="card-label">置物櫃登記</span>
									<span class="card-sublabel">Locker Registration</span>
								</a>
								<a class="booking-card" href="./searching_data_locker.php">
									<span class="card-icon">&#128270;</span>
									<span class="card-label">置物櫃搜尋引擎</span>
									<span class="card-sublabel">Locker Search Engine</span>
								</a>
								<a class="booking-card" href="./input-retails.php">
									<span class="card-icon">&#128722;</span>
									<span class="card-label">零售登記</span>
									<span class="card-sublabel">Retails Registration</span>
								</a>
								<a class="booking-card" href="./search-data-input-retails.php">
									<span class="card-icon">&#128270;</span>
									<span class="card-label">零售搜尋引擎</span>
									<span class="card-sublabel">Retails Search Engine</span>
								</a>
								<a class="booking-card" href="./searching_data_golf_club_rental.php">
									<span class="card-icon">&#127948;</span>
									<span class="card-label">高爾夫球桿租賃登記</span>
									<span class="card-sublabel">Golf Club Rental Registration</span>
								</a>
								<a class="booking-card" href="./searching_data_golf_club_rental.php">
									<span class="card-icon">&#128270;</span>
									<span class="card-label">高爾夫球桿租賃搜尋引擎</span>
									<span class="card-sublabel">Golf Club Rental Search Engine</span>
								</a>
							</div>
						</div>

						<?php if ($_SESSION['identity'] == 'admin' || $_SESSION['identity'] == 'manager') { ?>
						<!-- 報告 -->
						<div class="booking-section">
							<div class="booking-section-title">&#128202; 報告 <small>Reports</small></div>
							<div class="booking-cards">
								<?php if ($_SESSION['identity'] == 'admin' || $_SESSION['identity'] == 'manager') { ?>
								<a class="booking-card card-report" href="./Report_Monthly.php">
									<span class="card-icon">&#128200;</span>
									<span class="card-label">月度報告</span>
									<span class="card-sublabel">Monthly Report</span>
								</a>
								<?php } ?>
								<a class="booking-card" href="./IncomeBalanceSheetOnTheDay.php">
									<span class="card-icon">&#128176;</span>
									<span class="card-label">預約日期當天收入統算表</span>
									<span class="card-sublabel">Income Balance Sheet on the Day</span>
								</a>
								<a class="booking-card" href="./download_report.php">
									<span class="card-icon">&#128229;</span>
									<span class="card-label">每日報告下載</span>
									<span class="card-sublabel">Daily Report Download</span>
								</a>
								<a class="booking-card" href="./download.php">
									<span class="card-icon">&#128190;</span>
									<span class="card-label">系統數據下載</span>
									<span class="card-sublabel">System Data Download</span>
								</a>
							</div>
						</div>
						<?php } ?>

						<!-- 埋數 -->
						<div class="booking-section">
							<div class="booking-section-title">&#9200; 埋數 <small>Clock-out</small></div>
							<div class="booking-cards">
								<a class="booking-card card-clock" href="./check-report.php?way=individual">
									<span class="card-icon">&#128100;</span>
									<span class="card-label">埋數和報告下載 — 個別職員</span>
									<span class="card-sublabel">Clock-out &amp; Download Report — Individual Staff</span>
								</a>
								<?php if ($_SESSION['identity'] == 'admin' || $_SESSION['identity'] == 'manager') { ?>
								<a class="booking-card card-clock" href="./check-report.php?way=all">
									<span class="card-icon">&#128101;</span>
									<span class="card-label">埋數和報告下載 — 全體員工</span>
									<span class="card-sublabel">Clock-out &amp; Download Report — All Staff</span>
								</a>
								<?php } ?>
							</div>
						</div>
						</div><!-- /tab-bookings -->


						<?php
						if ($_SESSION['identity'] == 'admin' || $_SESSION['identity'] == 'manager') {
						?>
							<div class="tab-pane" id="tab-staff">

							<h2 style="color:#1e40af;margin-bottom:6px;">&#128100; 員工帳戶 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Employee Accounts</span></h2>
							<hr>

							<!-- Two-column card layout for Add / Delete -->
							<div style="display:flex;gap:24px;flex-wrap:wrap;margin-bottom:28px;">

								<!-- ── Add Employee Card ── -->
								<div style="flex:1;min-width:300px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#1e40af;">&#10133; 新增員工 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Add Employee</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">填寫以下資料以建立新的員工帳戶 / Fill in details to create a new employee account</p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<input type="hidden" name="add_staff">
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128100; 員工姓名 <span style="font-weight:400;color:#9ca3af;">/ Employee Name</span></label>
											<input type="text" name="name" placeholder="e.g. 張三 / John Chan" autocomplete="off" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
										</div>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128188; 職位 <span style="font-weight:400;color:#9ca3af;">/ Position</span></label>
											<select name="type" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;background:#fff;cursor:pointer;box-sizing:border-box;">
												<option value="full-time">&#128338; 全職 / Full-time</option>
												<option value="part-time">&#128339; 兼職 / Part-time</option>
												<option value="manager">&#128081; 經理 / Manager</option>
											</select>
										</div>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128272; 登入名稱 <span style="font-weight:400;color:#9ca3af;">/ Username</span></label>
											<input type="text" name="id" placeholder="e.g. johnchan" autocomplete="off" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
										</div>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128273; 密碼 <span style="font-weight:400;color:#9ca3af;">/ Password</span></label>
											<input type="text" name="password" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" autocomplete="off" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
										</div>
										<button type="submit" style="margin-top:4px;padding:12px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10133; 新增員工 / Add Employee</button>
									</form>
								</div>

								<!-- ── Delete Employee Card ── -->
								<div style="flex:1;min-width:280px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);align-self:flex-start;">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#b91c1c;">&#10060; 刪除員工 <span style="font-size:12px;color:#9ca3af;font-weight:400;">/ Delete Employee</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入使用者名稱以刪除帳戶（無法復原）<br><small style="color:#f87171;">Enter username to delete the account (irreversible)</small></p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#991b1b;margin-bottom:4px;">&#128272; 使用者名稱 <span style="font-weight:400;color:#9ca3af;">/ Username</span></label>
											<input type="text" name="del_staff" placeholder="輸入使用者名稱 / Enter username" style="width:100%;padding:10px 14px;border:2px solid #fca5a5;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#fca5a5'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10060; 刪除 / Delete</button>
									</form>
								</div>

							</div>

							<!-- ── Staff List Table ── -->
							<h3 style="color:#1e40af;font-size:16px;margin:0 0 12px 0;">&#128203; 員工列表 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Staff List</span></h3>
							<div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
								<table style="width:100%;border-collapse:collapse;">
									<thead>
										<tr style="background:#f9fafb;">
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">姓名<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Name</small></th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">職位<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Position</small></th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">登入名稱<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Username</small></th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">密碼<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Password</small></th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">建立日期<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Created</small></th>
										</tr>
									</thead>
									<tbody>
									<?php
									$sql = "SELECT `id`, `type`, `name`, `create-datetime`, `password` FROM `golf-staff` order by `type`, `name`;";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$rowNum = 0;
										while ($row = $result->fetch_assoc()) {
											$bg = ($rowNum % 2 === 0) ? '#fff' : '#f9fafb';
											$typeLabel = '';
											switch ($row["type"]) {
												case 'full-time': $typeLabel = '&#128338; 全職 / Full-time'; break;
												case 'part-time': $typeLabel = '&#128339; 兼職 / Part-time'; break;
												case 'manager':   $typeLabel = '&#128081; 經理 / Manager';   break;
												default: $typeLabel = htmlspecialchars($row["type"]);
											}
											echo "<tr style=\"background:{$bg};\">";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-weight:600;color:#1e40af;\">" . htmlspecialchars($row["name"]) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;color:#374151;\">{$typeLabel}</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-family:Consolas,monospace;color:#6b7280;\">" . htmlspecialchars($row["id"]) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-family:Consolas,monospace;color:#6b7280;\">" . htmlspecialchars($row["password"]) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;white-space:nowrap;color:#6b7280;\">" . htmlspecialchars($row["create-datetime"]) . "</td>";
											echo "</tr>";
											$rowNum++;
										}
									} else {
										echo "<tr><td colspan=\"5\" style=\"padding:40px 16px;text-align:center;color:#9ca3af;font-size:14px;\">&#128100; 暫無員工帳戶 / No employee accounts found</td></tr>";
									}
									?>
									</tbody>
								</table>
							</div>

							</div><!-- /tab-staff -->
							<div class="tab-pane" id="tab-boardcast">

							<h2 style="color:#1e40af;margin-bottom:6px;">&#128226; 廣播通知 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Broadcast Notifications</span></h2>
							<hr>

							<!-- Two-column card layout for Add / Delete -->
							<div style="display:flex;gap:24px;flex-wrap:wrap;margin-bottom:28px;">

								<!-- ── Add Broadcast Card ── -->
								<div style="flex:1;min-width:320px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#1e40af;">&#10133; 新增廣播 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Add Broadcast</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入廣播通知訊息內容 / Enter the broadcast notification message</p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128172; 訊息內容 <span style="font-weight:400;color:#9ca3af;">/ Message</span></label>
											<textarea name="add_boardcast" rows="5" placeholder="請輸入廣播訊息 / Enter broadcast message here..." style="width:100%;padding:12px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;font-family:inherit;resize:vertical;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'"></textarea>
										</div>
										<button type="submit" style="padding:12px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10133; 發送廣播 / Add Broadcast</button>
									</form>
								</div>

								<!-- ── Delete Broadcast Card ── -->
								<div style="flex:1;min-width:280px;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);align-self:flex-start;">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#92400e;">&#10060; 刪除廣播 <span style="font-size:12px;color:#9ca3af;font-weight:400;">/ Delete Broadcast</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入廣播 ID 以刪除特定訊息（無法復原）<br><small style="color:#f59e0b;">Enter the broadcast ID to delete (irreversible)</small></p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#92400e;margin-bottom:4px;">&#128220; 廣播 ID <span style="font-weight:400;color:#9ca3af;">/ Broadcast ID</span></label>
											<input type="number" name="del_boardcast" placeholder="e.g. 1" min="1" style="width:100%;padding:10px 14px;border:2px solid #fcd34d;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#fcd34d'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#d97706;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#b45309'" onmouseout="this.style.background='#d97706'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10060; 刪除 / Delete</button>
									</form>
								</div>

							</div>

							<!-- ── Broadcast List Table ── -->
							<h3 style="color:#1e40af;font-size:16px;margin:0 0 12px 0;">&#128203; 廣播列表 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Broadcast List</span></h3>
							<div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
								<table style="width:100%;border-collapse:collapse;">
									<thead>
										<tr style="background:#f9fafb;">
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;width:80px;">ID</th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">廣播訊息<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Broadcast Message</small></th>
										</tr>
									</thead>
									<tbody>
									<?php
									$sql = "SELECT `id`, `boardcast-message` FROM `golf-boardcast`;";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$rowNum = 0;
										while ($row = $result->fetch_assoc()) {
											$bg = ($rowNum % 2 === 0) ? '#fff' : '#f9fafb';
											echo "<tr style=\"background:{$bg};\">";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-weight:700;color:#1e40af;font-size:15px;\">#" . htmlspecialchars($row["id"]) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;color:#374151;line-height:1.5;\">" . nl2br(htmlspecialchars(base64_decode($row["boardcast-message"]))) . "</td>";
											echo "</tr>";
											$rowNum++;
										}
									} else {
										echo "<tr><td colspan=\"2\" style=\"padding:40px 16px;text-align:center;color:#9ca3af;font-size:14px;\">&#128226; 暫無廣播通知 / No broadcast messages found</td></tr>";
									}
									?>
									</tbody>
								</table>
							</div>

							</div><!-- /tab-boardcast -->
							<?php
							if ($_SESSION['identity'] == 'admin') {
							?>
								<div class="tab-pane" id="tab-email">

							<h2 style="color:#1e40af;margin-bottom:6px;">&#128231; 管理員電郵 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Admin Emails</span></h2>
							<hr>

							<!-- Two-column card layout for Add / Delete -->
							<div style="display:flex;gap:24px;flex-wrap:wrap;margin-bottom:28px;">

								<!-- ── Add Email Card ── -->
								<div style="flex:1;min-width:300px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#1e40af;">&#10133; 新增電郵 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Add Email</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入管理員電子郵件地址 / Enter the administrator email address</p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128231; 電子郵件 <span style="font-weight:400;color:#9ca3af;">/ Email Address</span></label>
											<input type="email" name="add_email" placeholder="admin@example.com" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10133; 新增電郵 / Add Email</button>
									</form>
								</div>

								<!-- ── Delete Email Card ── -->
								<div style="flex:1;min-width:280px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);align-self:flex-start;">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#b91c1c;">&#10060; 刪除電郵 <span style="font-size:12px;color:#9ca3af;font-weight:400;">/ Delete Email</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入要刪除的電子郵件地址（無法復原）<br><small style="color:#f87171;">Enter the email address to delete (irreversible)</small></p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#991b1b;margin-bottom:4px;">&#128231; 電子郵件 <span style="font-weight:400;color:#9ca3af;">/ Email Address</span></label>
											<input type="email" name="del_email" placeholder="admin@example.com" style="width:100%;padding:10px 14px;border:2px solid #fca5a5;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#fca5a5'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10060; 刪除 / Delete</button>
									</form>
								</div>

							</div>

							<!-- ── Emails List Table ── -->
							<h3 style="color:#1e40af;font-size:16px;margin:0 0 12px 0;">&#128203; 電郵列表 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Email List</span></h3>
							<div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
								<table style="width:100%;border-collapse:collapse;">
									<thead>
										<tr style="background:#f9fafb;">
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">#</th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">電子郵件地址<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Email Address</small></th>
										</tr>
									</thead>
									<tbody>
									<?php
									$sql = "SELECT `email-address` FROM `golf-administration-email`;";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$rowNum = 0;
										while ($row = $result->fetch_assoc()) {
											$bg = ($rowNum % 2 === 0) ? '#fff' : '#f9fafb';
											echo "<tr style=\"background:{$bg};\">";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-weight:600;color:#9ca3af;width:60px;\">" . ($rowNum + 1) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;color:#374151;font-family:Consolas,monospace;\">" . htmlspecialchars($row["email-address"]) . "</td>";
											echo "</tr>";
											$rowNum++;
										}
									} else {
										echo "<tr><td colspan=\"2\" style=\"padding:40px 16px;text-align:center;color:#9ca3af;font-size:14px;\">&#128231; 暫無管理員電郵 / No admin emails found</td></tr>";
									}
									?>
									</tbody>
								</table>
							</div>

								</div><!-- /tab-email -->
							<?php } ?>
							<div class="tab-pane" id="tab-blacklist">

							<h2 style="color:#1e40af;margin-bottom:6px;">&#128683; 黑名單 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Blacklist</span></h2>
							<hr>

							<!-- Two-column card layout for Add / Delete -->
							<div style="display:flex;gap:24px;flex-wrap:wrap;margin-bottom:28px;">

								<!-- ── Add Blacklist Email Card ── -->
								<div style="flex:1;min-width:300px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#1e40af;">&#10133; 新增黑名單 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Add to Blacklist</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">將電子郵件地址加入黑名單以禁止預約 / Block an email address from making reservations</p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:4px;">&#128683; 電子郵件 <span style="font-weight:400;color:#9ca3af;">/ Email Address</span></label>
											<input type="email" name="add_black_email" placeholder="spam@example.com" style="width:100%;padding:10px 14px;border:2px solid #d1d5db;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10133; 加入黑名單 / Add to Blacklist</button>
									</form>
								</div>

								<!-- ── Delete Blacklist Email Card ── -->
								<div style="flex:1;min-width:280px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;padding:24px;box-shadow:0 1px 2px rgba(0,0,0,0.04);align-self:flex-start;">
									<h3 style="margin:0 0 4px 0;font-size:16px;color:#b91c1c;">&#10060; 移出黑名單 <span style="font-size:12px;color:#9ca3af;font-weight:400;">/ Remove from Blacklist</span></h3>
									<p style="margin:0 0 18px 0;font-size:12px;color:#9ca3af;">輸入電子郵件地址以從黑名單中移除 / Enter the email address to remove from blacklist</p>
									<form action="<?php echo this_page_link(); ?>" style="display:flex;flex-direction:column;gap:12px;">
										<?php printAllPreviousSubmitParameters(); ?>
										<div>
											<label style="display:block;font-size:13px;font-weight:600;color:#991b1b;margin-bottom:4px;">&#128231; 電子郵件 <span style="font-weight:400;color:#9ca3af;">/ Email Address</span></label>
											<input type="email" name="del_black_email" placeholder="spam@example.com" style="width:100%;padding:10px 14px;border:2px solid #fca5a5;border-radius:8px;font-size:14px;transition:border-color 0.2s;box-sizing:border-box;" onfocus="this.style.borderColor='#ef4444'" onblur="this.style.borderColor='#fca5a5'">
										</div>
										<button type="submit" style="padding:12px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;transition:background 0.2s,transform 0.1s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">&#10060; 移出黑名單 / Remove from Blacklist</button>
									</form>
								</div>

							</div>

							</div><!-- /tab-blacklist -->
							<div class="tab-pane" id="tab-holiday">

							<h2 style="color:#1e40af;margin-bottom:6px;">&#128197; 透過政府 API 更新假期 <span style="font-size:15px;color:#6b7280;font-weight:400;">/ Govt Holiday Renewal</span></h2>
							<hr>

							<!-- ── API Renewal Card ── -->
							<div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border:1px solid #bfdbfe;border-radius:12px;padding:28px;margin-bottom:28px;box-shadow:0 1px 3px rgba(59,130,246,0.08);">
								<div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;">
									<div style="flex:1;min-width:200px;">
										<h3 style="margin:0 0 6px 0;font-size:17px;color:#1e40af;">&#128259; 從香港政府 API 更新假期資料 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Renew from Govt API</span></h3>
										<p style="margin:0;font-size:13px;color:#6b7280;line-height:1.6;">
											&#8505; 此操作會從香港政府 1823 API 擷取最新公眾假期資料並更新資料庫。<br>
											<small>This will fetch the latest public holiday data from the Hong Kong Government 1823 API and update the database.</small>
										</p>
									</div>
									<div style="flex-shrink:0;">
										<button type="button" id="btnRefreshHoliday" style="padding:14px 28px;background:#2563eb;color:#fff;border:none;border-radius:10px;font-size:16px;font-weight:600;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 12px rgba(37,99,235,0.25);display:flex;align-items:center;gap:8px;white-space:nowrap;" onmouseover="this.style.background='#1d4ed8';this.style.boxShadow='0 6px 20px rgba(37,99,235,0.35)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='#2563eb';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.25)';this.style.transform='translateY(0)'" onmousedown="this.style.transform='scale(0.97)'" onmouseup="this.style.transform='scale(1)'">
											<span style="font-size:20px;">&#128259;</span>
											<span style="text-align:left;">
												<strong>更新假期資料</strong><br>
												<small style="font-size:11px;opacity:0.85;font-weight:400;">Fetch &amp; renew holiday data</small>
											</span>
										</button>
									</div>
								</div>
								<!-- ── AJAX result message ── -->
								<div id="holidayResult" style="margin-top:12px;font-size:14px;"></div>
									<script>
									document.getElementById('btnRefreshHoliday').addEventListener('click', function() {
										var btn = this;
										var resultDiv = document.getElementById('holidayResult');
										btn.disabled = true;
										btn.style.opacity = '0.6';
										resultDiv.innerHTML = '&#8987; 正在更新假期資料 / Fetching holiday data...';
										resultDiv.style.color = '#6b7280';
										var params = new URLSearchParams(window.location.search);
										params.set('reflesh_government_holiday', '1');
										params.set('ajax', '1');
										fetch(window.location.pathname + '?' + params.toString())
											.then(function(r) { return r.json(); })
											.then(function(data) {
												if (data.success) {
													resultDiv.innerHTML = '&#9989; ' + data.inserted + ' 筆假期已更新 / ' + data.inserted + ' holidays inserted.' + (data.errors > 0 ? ' (' + data.errors + ' errors)' : '');
													resultDiv.style.color = '#059669';
													// Reload the page after 1.5s to refresh the table
													setTimeout(function() { location.reload(); }, 1500);
												} else {
													resultDiv.innerHTML = '&#10060; ' + (data.error || 'Unknown error');
													resultDiv.style.color = '#dc2626';
												}
											})
											.catch(function(err) {
												resultDiv.innerHTML = '&#10060; Network error: ' + err.message;
												resultDiv.style.color = '#dc2626';
											})
											.finally(function() {
												btn.disabled = false;
												btn.style.opacity = '1';
											});
									});
									</script>
							</div>

							<!-- ── Holiday Records Table ── -->
							<h3 style="color:#1e40af;font-size:16px;margin:0 0 12px 0;">&#128197; 假期記錄 <span style="font-size:12px;color:#6b7280;font-weight:400;">/ Holiday Records</span></h3>
							<div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,0.04);">
								<table style="width:100%;border-collapse:collapse;">
									<thead>
										<tr style="background:#f9fafb;">
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">假期名稱<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Holiday Name</small></th>
											<th style="padding:14px 16px;text-align:left;font-size:13px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #e5e7eb;">日期<br><small style="font-weight:400;text-transform:none;letter-spacing:0;color:#9ca3af;">Date</small></th>
										</tr>
									</thead>
									<tbody>
									<?php
									$sql = "SELECT `holiday-name`, `holiday-date` FROM `applied-solar-holiday` order by `holiday-date` desc, `holiday-name` asc limit 50;";
									$result = $conn->query($sql);
									require_once 'DateConvert.php';
									if ($result->num_rows > 0) {
										$rowNum = 0;
										while ($row = $result->fetch_assoc()) {
											$bg = ($rowNum % 2 === 0) ? '#fff' : '#f9fafb';
											echo "<tr style=\"background:{$bg};\">";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;font-weight:600;color:#1e40af;\">" . htmlspecialchars($row["holiday-name"]) . "</td>";
											echo "<td style=\"padding:12px 16px;border-bottom:1px solid #f3f4f6;color:#6b7280;font-family:Consolas,monospace;\">" . htmlspecialchars($row["holiday-date"]) . "</td>";
											echo "</tr>";
											$rowNum++;
										}
									} else {
										echo "<tr><td colspan=\"2\" style=\"padding:40px 16px;text-align:center;color:#9ca3af;font-size:14px;\">&#128197; 暫無假期記錄 / No holiday records found</td></tr>";
									}
									?>
									</tbody>
								</table>
							</div>

							</div><!-- /tab-holiday -->
							<div class="tab-pane" id="tab-prompt">
								<iframe src="./prompt-interact.php" style="width: 100%;height: 1000px;"></iframe>
							</div><!-- /tab-prompt -->
							<div class="tab-pane" id="tab-sysvar">
								<h2>&#9881; System Variables (Booking Expire Time Config)</h2>
								<hr>
								<iframe src="./system-variable.php" style="width: 100%;height: 800px;border: 1px solid #e5e7eb;border-radius: 8px;"></iframe>
							</div><!-- /tab-sysvar -->

						<?php } ?>
					</div><!-- /config-content -->
				</div><!-- /config-layout -->


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

						if (isset($_GET['action']) && $_GET['action'] == 'staff_page') {
							$username = mb_ereg_replace("'", "", $_GET['id']);
							$password = mb_ereg_replace("'", "", $_GET['password']);
							$sql = "SELECT `name`,`type` from `golf-staff` where `id`='$username' and `password`='$password';";
							$result = $conn->query($sql);

							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									$name = $row['name'];
									$type = $row['type'];
									// echo "Welcome $name";
									if ($type == 'full-time') {
										$md5_str = $currentDateTime . '_full-time_' . $email;
										$auth = md5($md5_str);
										$fullUrl = this_page_link();
										$url_ = $fullUrl . '?auth=' . $auth . '&datetime=' . $currentDateTime . '&email=' . $email . '&name=' . $username . '&name2=' . $name;
										header("Location: $url_");
									}
									if ($type == 'part-time') {
										$md5_str = $currentDateTime . '_part-time_' . $email;
										$auth = md5($md5_str);
										$fullUrl = this_page_link();
										$url_ = $fullUrl . '?auth=' . $auth . '&datetime=' . $currentDateTime . '&email=' . $email . '&name=' . $username . '&name2=' . $name;
										header("Location: $url_");
									}
									if ($type == 'manager') {
										$md5_str = $currentDateTime . '_manager_' . $email;
										$auth = md5($md5_str);
										$fullUrl = this_page_link();
										$url_ = $fullUrl . '?auth=' . $auth . '&datetime=' . $currentDateTime . '&email=' . $email . '&name=' . $username . '&name2=' . $name;
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
				$emailadd = 'support@riversidegolf.com.hk'; // Your email address (where the form information will be sent)
				$url = 'thanks.php'; // Redirect URL after form processing
				$req = '0'; // Set to '1' to make all fields required, '0' to allow empty fields

				$md5_str = $currentDateTime . '_' . $email;
				$auth = md5($md5_str);
				$fullUrl = this_page_link();
				$url_ = $fullUrl . '?auth=' . $auth . '&datetime=' . $currentDateTime . '&email=' . $email . '&name=superuser';
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