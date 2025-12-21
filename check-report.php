<?php 

set_time_limit(1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'logger.php';
t_log('begin[check-report.php]');


    session_start();
if (!isset($_SESSION["management"])) {
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當\nThe way you are using this website is inappropriate');
        window.location.href = "./";
    </script>
    <?php
    die();
}
 ?>
<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>

<?php 

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


if (!isset($_GET['way'])) {
	die();
}

if (!isset($_SESSION['name'])) {
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
if (isset($_GET['debug'])) {
	?> 
	<?php
		var_dump($complexArray);
	?> 
	<?php 
	die();
}


if (isset($_GET['check'])) {

	// date_default_timezone_set('Asia/Hong_Kong');
	// $clock_out_datetime = date('Y-m-d h:i:s');
	// ,`report-time`
	// , '$clock_out_datetime'
	$sql = "
	INSERT INTO `golf-check-report`(`src`) VALUES ('$src');
	";
	echo "$sql";
	try {
	    if ($conn->query($sql) === TRUE) {

	?>
<script type="text/javascript">
	alert('打卡報告已生成，請從歷史報告下載 \n Clock-out report already  generated, please download from history report\n\n <?php //	echo $clock_out_datetime; ?> \n\n INSERT by <?php echo $src; ?>');
	setTimeout(function () {
		// window.close();
		window.location.href = "<?php echo this_page_link_().'?way='.$_GET['way']; ?>";
	}, 1);
</script>
	<?php
	    } else {
	    	echo "failed";
	    }
	} catch (Exception $e) {
		echo $e;
	}
	die();
}
echo "Select by $src<br>";

$last_report_count = 15;
$sql = " 
SELECT 
`src`, `report-time`
,`report-time` `to`
,IFNULL(
	(
		select `t2`.`report-time` 
		from `golf-check-report` `t2`
		where `t2`.`report-time`<`t1`.`report-time`
		and `t2`.`src`=`t1`.`src`
		order by `t2`.`report-time` desc
		limit 1
	),(
		'0000-00-00 00:00:00'
	)
) `from`
FROM `golf-check-report` `t1`
WHERE `src`='$src' or `src`='$src2' 
order by `report-time` desc 
limit $last_report_count;
";
// echo "$sql";
// $arr = array();


$result = $conn->query($sql);

date_default_timezone_set('Asia/Hong_Kong');

function build_the_key($from, $to)
{
	return "$to<br>~<br>$from";
}

$from = '0000-00-00 00:00:00';
$to = date('Y-m-d h:i:s');
$last_report_time = null;

$htmlArray = array();
$last_report_from = null;
$last_report_to = null;
if ($result->num_rows > 0) {
	echo "$result->num_rows records";
    while ($row = $result->fetch_assoc()) {
        $from = $row['from'];
        $to = $row['to'];
		if ($last_report_from == null && $last_report_to == null) {
			$last_report_from = $from;
			$last_report_to = $to;
		}
        $htmlArray[build_the_key($from,$to)] = 1;
		if ($last_report_time == null) {
			$last_report_time = $to;
		}
    }
}
if ($last_report_time == null) {
	$from = '0000-00-00 00:00:00';
} else {
	$from = $last_report_time;
}
$to = date('Y-m-d h:i:s');
// $html_preview_report = generate_report($conn,$complexArray, $src , $src2, $from, $to, true);

 ?>
<table style="width: 100%;">
	<tr>
		<td>
			
<?php 	
if (!isset($_GET['download'])) {
 ?>
				<hr>
				<h1>員工打卡下班按鈕 <br>Staff clock-out button</h1>
<a 
	href="?way=<?php echo $_GET['way']; ?>&check=1"
	onclick="window.location.reload();"

	style="
	    color: blue;
	    background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
	    padding: 30px;
	    width: 80%;
	    border-radius: 30px;
	    display: block;
	    text-align: center;
	    cursor: pointer;
	"
	>
	點這裡 埋數 <br>Click here to clock-out
</a>
				<hr>
<?php 	
}
 ?>
			</td>
			<td>
				<h3>打卡前預覽 Clock-out preview</h3>

<iframe
	id="clockoutpreview"
	src="./check-report-api.php?from_date=<?php 
		echo str_replace(' ','%20',$from);
	 ?>&to_date=<?php 
		echo date('Y-m-d').'%20'.date('h:i:s');
	 ?>&src=<?php 
		echo $src;
	 ?>&way=<?php echo $_GET['way'] ?>"
	style="width: 400px;height: 400px;border-style: double; margin: 3px;"
>
				</iframe>
				<script type="text/javascript">
					// setTimeout(() => {
					// 	const oIframe = document.getElementById('clockoutpreview');
					// 	oIframe.contentWindow.document.open();
					// 	oIframe.contentWindow.document.write('<?php // echo "$html_preview_report"; ?>');
					// 	oIframe.contentWindow.document.close();
					// }, 500);
				</script>
			</td>
			<td>
				<hr>
				<h3>上次最後報告 Last report</h3>

<iframe
	id="receipt_printing_buffer"
	src="./check-report-api.php?from_date=<?php 
		echo $last_report_from;
	 ?>&to_date=<?php 
		echo $last_report_to;
	 ?>&src=<?php 
		echo $src;
	 ?>&way=<?php echo $_GET['way'] ?>"
	style="width: 400px;height: 400px;border-style: double; margin: 3px;"
>
				</iframe>
				<hr>

		</td>
	</tr>
	<tr>
		<td colspan="3">
			
				<hr>

<h1>歷史報告 History Report</h1>
<h3>
	(
		最後<?php echo $last_report_count; ?>個報告
		Last <?php echo $last_report_count; ?> reports
	)
</h3>

<table style="width: 100%;">
	<tr>
		<td>埋數時間 Clock-out Time</td>
		<td>下載按鈕 Download Button</td>
	</tr>
<?php
foreach ($htmlArray as $key => $value) {
	// echo "$key <br>";
	$parts = explode("<br>~<br>", $key);
	$from_date = $parts[1];
	$to_date = $parts[0];
 ?>
	<tr>
	 	<td><?php echo "$key"; ?></td>

		


	 	<td><a href="./check-report-api.php?from_date=<?php 
		echo str_replace(' ','%20',$from_date);
	 ?>&to_date=<?php 
		echo str_replace(' ','%20',$to_date);
	 ?>&src=<?php 
		echo $src;
	 ?>&way=<?php 
	 	echo $_GET['way'];
		if ($src !== 'all') {
			echo "&src=$src";
		}
	 ?>&download=true" target="_blank">下載報告<br>Download Report</a></td>

	</tr>
<?php
}
 ?>
</table>		
				<hr>
		</td>
	</tr>
</table>

<style type="text/css">
	
	html {
		padding: 30px;
		background: lightpink;
	}
	body {
		padding: 30px;
		background-color: white;
	}
</style>
<style type="text/css">
	td {
		border-style: double;
		padding: 20px;
		text-align: center;
/*		vertical-align: text-top;*/
	}
</style>
 <?php
// echo json_encode($arr,JSON_PRETTY_PRINT);

t_log('end[check-report.php]');

 ?>