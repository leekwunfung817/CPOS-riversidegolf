<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once './account_variable.php';


$sql_condition_general_bay = " `price-name` BETWEEN 5 and 85 ";
$sql_condition_sand_bay = " `price-name` BETWEEN 1 and 2 ";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getSinglePrice($conn, $sql_query)
{
	$result = $conn->query($sql_query);
	while($row = $result->fetch_assoc()) {
		echo $row['price'];
	}

	if ($result->num_rows != 1) {
		echo "QUERY ".$result->num_rows." rows from single price query";
	}
}





function price_table_display($conn,$price_table_name)
{
	global $sql_condition_general_bay;
	global $sql_condition_sand_bay;

?>
<style type="text/css">
	html {
		padding: 30px;
		background-color: CornflowerBlue;
	}
	body {
		background-color: white;
		border-style: outset;
	}
	table {
		font-size: 2em;
		width: 100%;
	}
	th, td {
		border-style: inset;
		border-style: outset;
		text-align: center;
	}
</style>

<br>
<hr>

<h2>
&darr; 生效日期 Effective Date: <?php 

$result = $conn->query("
SELECT `effective-date` 
FROM `$price_table_name`
group by `effective-date`
;
");
while($row = $result->fetch_assoc()) {
	echo " [ ".$row['effective-date']." ] ";
}
 ?>
&darr;
</h2>


<table>
	<tr>
		<td colspan="4"><h1>白石高球練習場 - 價格表 
			<!-- <hr> WhiteHead Golf Club - Price table -->
		</h1></td>
	</tr>
	<tr>
		<td></td>
		<th>正價
			<!-- <br>Regular Price -->
		</th>
		<th>學生優惠
			<!-- <br>Student Price -->
		</th>
		<th>傷健人士優惠
			<!-- <br>Disabled Price -->
		</th>
	</tr>
	<tr>
		<th>星期一 
			<!-- Monday -->
			<br>(13:00-22:00)<hr>星期二至星期五
			<!-- <br>Tuesday to Friday -->
			<br>(08:00-22:00)</th>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='workday' 
and $sql_condition_general_bay
and `identity`='hourly'
group by `price`
;
");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='workday' 
and $sql_condition_general_bay
and `identity`='student'
group by `price`
;
");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='workday' 
and $sql_condition_general_bay
and `identity`='disabled'
group by `price`
;
");

 ?>元/小時
		</td>
	</tr>

	<tr>
		<th>星期六日及公眾假期
			<!-- <br>Saturday, Sunday, and public holiday  -->
			<br>(08:00-18:59)</th>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday' 
and $sql_condition_general_bay
and `identity`='hourly'
group by `price`
;");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday' 
and $sql_condition_general_bay
and `identity`='student'
group by `price`
;");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday' 
and $sql_condition_general_bay
and `identity`='disabled'
group by `price`
;");

 ?>元/小時
		</td>
	</tr>

	<tr>
		<th>星期六日及公眾假期
			<!-- <br>Saturday, Sunday, and public holiday  -->

			<br>(19:00-22:00)</th>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday_19To22' 
and $sql_condition_general_bay
and `identity`='hourly'
group by `price`
;");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday_19To22' 
and $sql_condition_general_bay
and `identity`='student'
group by `price`
;");

 ?>元/小時
		</td>
		<td>
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='holiday_19To22' 
and $sql_condition_general_bay
and `identity`='disabled'
group by `price`
;");

 ?>元/小時
		</td>
	</tr>

	<tr>
		<th>沙地球道<br>星期一至星期五
			<!-- <br>Sand Bay<br>Monday to Friday -->
		</th>
		<td colspan="3">
$<?php 	
getSinglePrice($conn, "
SELECT ( `price` / 2 ) `price`
FROM `$price_table_name` 
WHERE `period`='workday'
and $sql_condition_sand_bay
group by `price`
;
");

 ?>元/半小時
		</td>
	</tr>


	<tr>
		<th>沙地球道<br>星期六日及公眾假期
			<!-- <br>Sand Bay<br>Saturday, Sunday, and public holiday -->
		</th>
		<td colspan="3">
$<?php 	
getSinglePrice($conn, "
SELECT ( `price` / 2 ) `price`
FROM `$price_table_name` 
WHERE ( `period`='holiday' or `period`='holiday_19To22' )
and $sql_condition_sand_bay
group by `price`
;
");

 ?>元/半小時
		</td>
	</tr>


	<tr>
		<th>VIP房<br>星期一至星期五
			<!-- <br>VIP room<br>Monday to Friday -->
		</th>
		<td colspan="3">
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE `period`='workday'
and `price-name`='VIP'
group by `price`
;
");

 ?>元/小時
		</td>
	</tr>


	<tr>
		<th>VIP房<br>星期六日及公眾假期<br>(08:00-19:00)
			<!-- <br>VIP room<br>Saturday, Sunday, and public holiday -->
		</th>
		<td colspan="3">
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE ( `period`='holiday')
and `price-name`='VIP'
group by `price`
;
");

 ?>元/小時
		</td>
	</tr>


	<tr>
		<th>VIP房<br>星期六日及公眾假期<br>(19:00-22:00)
			<!-- <br>VIP room<br>Saturday, Sunday, and public holiday -->
		</th>
		<td colspan="3">
$<?php 	
getSinglePrice($conn, "
SELECT `price` 
FROM `$price_table_name` 
WHERE (`period`='holiday_19To22' )
and `price-name`='VIP'
group by `price`
;
");

 ?>元/小時
		</td>
	</tr>
</table>



<?php
}


price_table_display($conn,'golf_price_2');
price_table_display($conn,'golf_price');


function change_price($conn,$price_table_name,$price_content) {
	global $sql_condition_general_bay;
	global $sql_condition_sand_bay;
	////////////////////////////////////////////////////////////////////////////////////
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['workday-hourly']."
			WHERE `period`='workday' 
			and $sql_condition_general_bay
			and `identity`='hourly'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['workday-student']."
			WHERE `period`='workday' 
			and $sql_condition_general_bay
			and `identity`='student'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['workday-disabled']."
			WHERE `period`='workday' 
			and $sql_condition_general_bay
			and `identity`='disabled'
		;
	");
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday-hourly']."
			WHERE `period`='holiday' 
			and $sql_condition_general_bay
			and `identity`='hourly'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday-student']."
			WHERE `period`='holiday' 
			and $sql_condition_general_bay
			and `identity`='student'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday-disabled']."
			WHERE `period`='holiday' 
			and $sql_condition_general_bay
			and `identity`='disabled'
		;
	");
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday_19To22-hourly']."
			WHERE `period`='holiday_19To22' 
			and $sql_condition_general_bay
			and `identity`='hourly'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday_19To22-student']."
			WHERE `period`='holiday_19To22' 
			and $sql_condition_general_bay
			and `identity`='student'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday_19To22-disabled']."
			WHERE `period`='holiday_19To22' 
			and $sql_condition_general_bay
			and `identity`='disabled'
		;
	");
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['workday-sandbay']."
			WHERE `period`='workday'
			and $sql_condition_sand_bay
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday-holiday_19To22-sandbay']."
			WHERE ( `period`='holiday' or `period`='holiday_19To22' )
			and $sql_condition_sand_bay
		;
	");
	////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['workday-VIP']."
			WHERE `period`='workday'
			and `price-name`='VIP'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday-VIP']."
			WHERE ( `period`='holiday')
			and `price-name`='VIP'
		;
	");
	$conn->query("
		UPDATE `$price_table_name` SET `price`=".$price_content['holiday_19To22-VIP']."
			WHERE (`period`='holiday_19To22' )
			and `price-name`='VIP'
		;
	");
	////////////////////////////////////////////////////////////////////////////////////
}

// change_price($conn,'golf_price',array(
// 	'workday-hourly' => 80,
// 	'workday-student' => 40,
// 	'workday-disabled' => 60,

// 	'holiday-hourly' => 140,
// 	'holiday-student' => 140,
// 	'holiday-disabled' => 140,

// 	'holiday_19To22-hourly' => 80,
// 	'holiday_19To22-student' => 80,
// 	'holiday_19To22-disabled' => 80,

// 	'workday-sandbay' => 50*2,
// 	'holiday-holiday_19To22-sandbay' => 80*2,

// 	'workday-VIP' => 250,
// 	'holiday-VIP' => 360,
// 	'holiday_19To22-VIP' => 250
	
// ));

$conn->close();

 ?>