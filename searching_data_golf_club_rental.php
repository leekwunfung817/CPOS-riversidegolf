<?php 

    session_start();
if (!isset($_SESSION["management"])) {
     ?>
    <script type="text/javascript">
        alert('請使用您的員工帳號重新登錄\nPlease login with your staff account again');
        window.location.href = "./";
    </script>
    <?php
    die();
}
 ?>
<?php 

require_once 'account_variable.php';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


$sql = "
	SELECT `name`, `deposit`, `rental-fee` FROM `golf-club-price` order by name desc;
";
$prices_list = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
			$prices_list[ $row['name'] ] = $row;
    }
}

 ?>
<?php 




// INSERT INTO `golf-club-rental-record`(`bay`) VALUES ('VIP');

// UPDATE `golf-club-rental-record` 
// 	SET `returned`='1'
// 	WHERE `golf-club-seq`='';

// SELECT `golf-club-seq`, `start-dt`, `bay`, `returned` 
// FROM `golf-club-rental-record` 
// WHERE `returned`='0';

// SELECT `golf-club-seq`, `start-dt`, `bay`, `returned` 
// FROM `golf-club-rental-record` 
// WHERE `returned`='1';









require_once './position_list.php';

 ?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
<style type="text/css">
	td {
		vertical-align: text-top;
		border-style: solid;
		border-width: 2px;
/*		white-space: nowrap;*/
	}
	table {
		
	}
</style>

<table style="height: 10px;width: 100%;">
	<tr>
		<th style="vertical-align: text-top;" colspan="3">
			<h1 style="vertical-align: text-top;"	>高爾夫球桿租賃<br>Glof Club Rental</h1>
		</th>
	</tr>
	<tr>
		<th style="vertical-align: text-top;text-align: left;width: 50%;">
			<h2 style="vertical-align: text-top;">
				高爾夫球桿租賃 - 價格表 
					<br> 
				Glof Club Rental - Price Table
			</h2>
			<?php 
echo "";


echo "<table style=\"width: 500px;vertical-align: text-top;\">";
		echo "<tr>";
			echo "<th>高爾夫球桿 Golf Club Name</th>";
			echo "<th>按金 Deposit</th>";
			echo "<th>租賃費 Rental Fee</tthd>";
		echo "</tr>";
foreach ($prices_list as $name => $row) {
		echo "<tr>";
			echo "<td>";
			echo $row['name'];
			echo "</td>";
			echo "<td>";
			echo $row['deposit'];
			echo "</td>";
			echo "<td>";
			echo $row['rental-fee'];
			echo "</td>";
		echo "</tr>";
}
echo "</table>";
			 ?>










			<form method="get" action="">
				<h2>
					高球桿租賃 提交表格
								<br>
						Golf Club Rental Submit form
				</h2>
			<table>
				<tr>
					<td>
						球道 Bay:<br>
					</td>
					<td>
							<select name="bay">
								<?php 
								foreach ($position_list_ as $key1 => $sublist) {
									foreach ($sublist as $key2 => $value) {
								 ?>
								<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
								<?php 
									}
								}
								 ?>
							</select>
							<br>
					</td>
				</tr>
				<tr>
					<td>
						高爾夫球桿 Golf Club Name:<br>
					</td>
					<td>
							<select name="golf-club-name">
								<?php 
								foreach ($prices_list as $key => $price_set) {
								 ?>
								<option value="<?php echo $price_set['name']; ?>"><?php echo ($price_set['name']=='Wood'?'木桿':'鐵桿').' '.$price_set['name']; ?></option>
								<?php 
								}
								 ?>
							</select>

							<br>
					</td>
				</tr>
				<tr>
					<td>
							數量 Quantity:<br>
					</td>
					<td>
							<input type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" name="quantity" value="1" />
							<br>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" name="">
					</td>
				</tr>
			</table>
							<input type="hidden" name="rent">
			</form>












		</th>
		<th>
			<?php 


			 ?>


<iframe id="receipt_printing_buffer" style="width: 400px;height: 300px;">
</iframe>


			<a onclick="
			    const oIframe = document.getElementById('receipt_printing_buffer');
			    oIframe.contentWindow.print();
			" 
			style="
			    color: blue;
			    background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
			    padding: 30px;
			    width: 350px;
			    border-radius: 30px;
			    display: block;
			    text-align: center;
			    cursor: pointer;
			">
			    列印收據 Print Receipt
			</a>

<script type="text/javascript">


function comfirm_and_print(
	title,
	id,
	bay,
	date,
	deposit,
	fee,
	name,
	returned,
	quantity
) {

  var msg = '';
  var printing = '<h1>白石高球練習場</h1>';
  printing += '<h1>'+title+'</h1>';
  <?php 
      if (isset($_SESSION['name'])&&isset($_SESSION['name2'])) {
   ?>
  printing += '<i style="text-align: left;">On-Duty: <?php echo $_SESSION['name'].' - '.$_SESSION['name2']; ?> </i><br>';
  <?php 
      }
   ?>

  printing += '<div style="text-align: right;">Tel: 27771813</div>';
  printing += '<div style="text-align: right;">RIVERSIDE Whitehead Golf Club</div>';
  printing += '<i style="text-align: center;"><hr></i>';

  sourceTxt = id;
  sourceName = '序列號 Seq.';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = bay;
  sourceName = '球道 Bay';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = date;
  sourceName = '開始時間 Rental time';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = deposit;
  sourceName = '按金 Deposit';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = fee;
  sourceName = '租賃費 Rental Fee';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = name;
  sourceName = '球桿 Rental Content';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = returned;
  sourceName = '歸還狀態 Return State';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }


  sourceTxt = quantity;
  sourceName = '數量 Quantity';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }



  
  // if (confirm(msg)) {
    const oIframe = document.getElementById('receipt_printing_buffer');
    oIframe.contentWindow.document.open();
    oIframe.contentWindow.document.write(printing);
    oIframe.contentWindow.document.close();
    oIframe.contentWindow.print();
  // }
}   

</script>


<?php 


$staff = '';
if (isset($_SESSION['name'])) {
	$staff = $_SESSION['name'];
}


if ( isset($_GET['golf-club-name']) && isset($_GET['bay']) && isset($_GET['rent']) ) {

	$price_set = $prices_list[ $_GET['golf-club-name'] ];
	$quantity = 1;
	if (isset($_GET['quantity'])) {
		$quantity = $_GET['quantity'];
	}
	for ($i=0; $i < $quantity; $i++) {
		$sql = "
		INSERT INTO `golf-club-rental-record`(
			`bay`,
			`name`,
			`deposit`,
			`rental-fee`,
			`src`
		) 
		VALUES (
			'".$_GET['bay']."'
			,'".$price_set['name']."'
			,'".$price_set['deposit']."'
			,'".$price_set['rental-fee']."'
			,'".$staff."'
		);";
		$result = $conn->query($sql);


	}
}

if ( ( ( isset($_GET['golf-club-name']) && isset($_GET['bay']) && isset($_GET['rent']) ) || ( isset($_GET['seq']) && isset($_GET['print_rent']) ) ) ) {
	
	$where_addition = '';

		if (isset($_GET['seq'])) {
			$where_addition = " 
			AND DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')=( 
				SELECT DATE_FORMAT(`b`.`start-dt`, '%Y-%m-%d %H:%i:%s') 
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."' 
			) ";
		}

		$sql = "
			SELECT 
				MAX(`golf-club-seq`) `golf-club-seq`, 
				`start-dt`,
				MAX(`bay`) `bay`, 
				GROUP_CONCAT(`returned` SEPARATOR ', ') `returned`, 
				MAX(`name`) `name`,
				SUM(`deposit`) `deposit`,
				SUM(`rental-fee`) `rental-fee`,
				COUNT(*) `count`
			FROM `golf-club-rental-record` 
			WHERE `returned` IS NULL
			$where_addition
			group by DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
			order by `start-dt` desc
			limit 1;
		";

		// echo $sql;

	// if (isset($_GET['seq'])) {
	// 	$where_addition = " AND `golf-club-seq`='".$_GET['seq']."' ";
	// }

	// 	$sql = "
	// 		SELECT 
	// 			`golf-club-seq`, 
	// 			`start-dt`, 
	// 			`bay`, 
	// 			`returned`,

	// 			`name`,
	// 			`deposit`,
	// 			`rental-fee`
	// 		FROM `golf-club-rental-record` 
	// 		WHERE `returned` IS NULL
	// 		$where_addition
	// 		order by `start-dt` desc
	// 		limit 1;
	// 	";


		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
		    while ($row = $result->fetch_assoc()) {

					// if (!isset($_GET['seq'])) {
					// 	// is after insert ,and is not print only
					// 	$sql = "
					// 	UPDATE `golf-club-rental-record` SET `src`='$staff' WHERE `golf-club-seq`='".$row['golf-club-seq']."';";
					// 	$conn->query($sql);

					// }


				?>
				<script type="text/javascript">
					comfirm_and_print(
						'高爾夫球桿<br>租賃收據<br>(職員)',
						'<?php echo $row['golf-club-seq']; ?>',
						'<?php echo $row['bay']; ?>',
						'<?php echo $row['start-dt']; ?>',

						'HKD $<?php echo $row['deposit']; ?>',
						'HKD $<?php echo $row['rental-fee']; ?>',
						'<?php echo $row['name']; ?>',
						'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>',
						'<?php echo $row['count']; ?>'
					);
					setTimeout(function () {
						comfirm_and_print(
							'高爾夫球桿<br>租賃收據<br>(客戶)',
							'<?php echo $row['golf-club-seq']; ?>',
							'<?php echo $row['bay']; ?>',
							'<?php echo $row['start-dt']; ?>',

							'HKD $<?php echo $row['deposit']; ?>',
							'HKD $<?php echo $row['rental-fee']; ?>',
							'<?php echo $row['name']; ?>',
							'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>',
							'<?php echo $row['count']; ?>'
						);
					},1000);
				</script>
				<?php

		    }
		}
	
}

if ( isset($_GET['seq']) && isset($_GET['return']) ) {
	$sql = "
	UPDATE `golf-club-rental-record` 
		SET `returned`=CURRENT_TIMESTAMP
		WHERE 1=1
		AND `returned` is null
		AND `bay`=(
			SELECT `bay`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		AND `start-dt`=(
			SELECT `b`.`start-dt`
			FROM `golf-club-rental-record` `b`
			WHERE `golf-club-seq`='".$_GET['seq']."'
		)
		;
	";
	$result = $conn->query($sql);


	// $sql = "
	// 	SELECT 
	// 		`golf-club-seq`, 
	// 		`start-dt`, 
	// 		`bay`, 
	// 		`returned`,

	// 		`name`,
	// 		`deposit`,
	// 		`rental-fee`
	// 	FROM `golf-club-rental-record` 
	// 	WHERE `returned` IS NOT NULL
	// 	order by `returned` desc
	// 	limit 1;
	// ";


		// if (isset($_GET['seq'])) {
		// 	$where_addition = " 
		// 	AND DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')=( 
		// 		SELECT DATE_FORMAT(`b`.`start-dt`, '%Y-%m-%d %H:%i:%s') 
		// 		FROM `golf-club-rental-record` `b`
		// 		WHERE `b`.`golf-club-seq`='".$_GET['seq']."' 
		// 	) ";
		// }

		// $sql = "
		// 	SELECT 
		// 		GROUP_CONCAT(`golf-club-seq` SEPARATOR ', ') `golf-club-seq`, 
		// 		`start-dt`,
		// 		GROUP_CONCAT(`bay` SEPARATOR ', ') `bay`, 
		// 		GROUP_CONCAT(`returned` SEPARATOR ', ') `returned`, 
		// 		GROUP_CONCAT(`name` SEPARATOR ', ') `name`,
		// 		GROUP_CONCAT(`deposit` SEPARATOR ', ') `deposit`,
		// 		GROUP_CONCAT(`rental-fee` SEPARATOR ', ') `rental-fee`
		// 	FROM `golf-club-rental-record` 
		// 	WHERE `returned` IS NULL
		// 	$where_addition
		// 	group by DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
		// 	order by `start-dt` desc;
		// 	limit 1;
		// ";

		if (isset($_GET['seq'])) {
			$where_addition = " 
			AND DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')=( 
				SELECT DATE_FORMAT(`b`.`start-dt`, '%Y-%m-%d %H:%i:%s') 
				FROM `golf-club-rental-record` `b`
				WHERE `b`.`golf-club-seq`='".$_GET['seq']."' 
			) ";
		}

		$sql = "
			SELECT 
				MAX(`golf-club-seq`) `golf-club-seq`, 
				`start-dt`,
				MAX(`bay`) `bay`, 
				MAX(`returned`) `returned`, 
				MAX(`name`) `name`,
				SUM(`deposit`) `deposit`,
				SUM(`rental-fee`) `rental-fee`,
				COUNT(*) `count`
			FROM `golf-club-rental-record` 
			WHERE `returned` IS NULL
			$where_addition
			group by DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
			order by `start-dt` desc
			limit 1;
		";


	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
	    while ($row = $result->fetch_assoc()) {
			?>
			<script type="text/javascript">
				comfirm_and_print(
					'高爾夫球桿<br>歸還收據<br>(職員)',
					'<?php echo $row['golf-club-seq']; ?>',
					'<?php echo $row['bay']; ?>',
					'<?php echo $row['start-dt']; ?>',

					'HKD $<?php echo $row['deposit']; ?>',
					'HKD $<?php echo $row['rental-fee']; ?>',
					'<?php echo $row['name']; ?>',
					'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>'
				);
				setTimeout(function () {
					comfirm_and_print(
						'高爾夫球桿<br>歸還收據<br>(客戶)',
						'<?php echo $row['golf-club-seq']; ?>',
						'<?php echo $row['bay']; ?>',
						'<?php echo $row['start-dt']; ?>',

						'HKD $<?php echo $row['deposit']; ?>',
						'HKD $<?php echo $row['rental-fee']; ?>',
						'<?php echo $row['name']; ?>',
						'<?php echo ($row['returned']==null ? '尚未歸還':'已經歸還'); ?>'
					);
				},1000);


			</script>
			<?php

	    }
	}
}

$complexArray = array();

$sql = "
	SELECT 

		`golf-club-seq`, 
		`start-dt`, 
		`bay`, 
		`returned`,

		`name`,
		`deposit`,
		`rental-fee`

	FROM `golf-club-rental-record` 
	WHERE `returned` IS NULL;
";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    	if (!isset($complexArray[$row['bay']])) {
    		$complexArray[$row['bay']] = array();
    	}
    	$complexArray[$row['bay']][] = $row;
    }
}



 ?>
		</th>
		<th style="width: 30%;">
			
		</th>
	</tr>
	<tr>
		<th style="width: 30%;" colspan="3">
			<h2>
				未還 Not yet returned
			</h2>
			<?php 

$sql = "
	SELECT 
		DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s') `start-dt`, 
		MAX(`golf-club-seq`) `golf-club-seq-max`, 
		GROUP_CONCAT(`golf-club-seq` SEPARATOR ', ') `golf-club-seq`, 
		count(*) `quantity`,
		`bay`, 
		GROUP_CONCAT(`returned` SEPARATOR ', ') `returned`, 
		MAX(`name`) `name`,
		SUM(`deposit`) `deposit`,
		SUM(`rental-fee`) `rental-fee`
	FROM `golf-club-rental-record` 
	WHERE `returned` IS NULL
	group by `bay`,DATE_FORMAT(`start-dt`, '%Y-%m-%d %H:%i:%s')
	order by `bay` asc,`start-dt` desc;
";
$result = $conn->query($sql);

echo "<table>";
    	echo "<tr>";
    	
    	echo "<td style=\"width: 200px;\">";
    	echo '序列號<br>Sequence Number';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '租賃 開始日期時間<br>Rental Timestamp';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "球道<br>Bay";
    	echo "</td>";

    	// echo "<td style=\"width: 200px;\">";
    	// echo "歸還日期時間<br>Return Timestamp";
    	// echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '高爾夫球桿<br>Golf Club Rental';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '按金<br>Deposit';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "租賃費<br>Rental Fee";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "數量<br>Quantity";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "列印<br>Print";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "歸還<br>Return";
    	echo "</td>";

    	echo "</tr>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    	echo "<tr>";

    	echo "<td>";
    	echo $row['golf-club-seq-max'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['start-dt'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['bay'];
    	echo "</td>";

    	// echo "<td>";
    	// echo $row['returned'];
    	// echo "</td>";

    	echo "<td>";
    	echo $row['name'];
    	echo "</td>";

    	echo "<td>$";
    	echo $row['deposit'];
    	echo "</td>";

    	echo "<td>$";
    	echo $row['rental-fee'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['quantity'];
    	echo "</td>";

    	echo "<td>";
						echo "<a href=\"?seq=".$row['golf-club-seq-max']."&print_rent\">列印 Print</a>";
    	echo "</td>";

    	echo "<td>";
			echo "<a href=\"?seq=".$row['golf-club-seq-max']."&return\">歸還 Return</a>";
    	echo "</td>";



    	echo "</tr>";
    }
}
echo "</table>";

			 ?>

		</th>
	</tr>
	<tr>
		<th>
		</th>
		<th>
		</th>
	</tr>
</table>
<?php


echo "<hr>";




// echo "<h1>租賃記錄 Current Rental</h1>";



// echo "<table style=\"width: 100%;\">";
// 	echo "<tr>";

// 		echo "<th style=\"width: 20%;\">Sand Bay</th>";
// 		echo "<th style=\"width: 20%;\">VIP</th>";
// 		echo "<th style=\"width: 20%;\">Iron</th>";
// 		echo "<th style=\"width: 20%;\">Iron and Short Wood</th>";
// 		echo "<th style=\"width: 20%;\">Wood</th>";
		
// 	echo "</tr>";
// 	echo "<tr>";
// foreach ($position_list_ as $key1 => $sublist) {
// 		echo "<td>";

// 			echo "<table style=\"width: 100%;\">";
// 				// echo "<tr>";
// 				// echo "<td>";
// 				// echo $key1;
// 				// echo "</td>";
// 				// echo "</tr>";
// 	foreach ($sublist as $key2 => $value) {
				
// 				echo "<tr>";

// 				echo "<td style=\"width: 20%;\">";
// 				echo "$value";
// 				echo "<hr>";
// 				foreach ($prices_list as $key => $price_set) {
// 					echo "<a href=\"?bay=$value&golf-club-name=".$price_set['name']."&rent\">點擊租用(".($price_set['name']=='Wood'?'木桿':'鐵桿').") <br> Rent (".$price_set[ 'name' ].")</a> <br>";
// 				}
				
// 				echo "<hr>";
// 				// echo "</td>";

// 				// echo "<td>";

// 				{

// 					echo "<table>";
					

// 					foreach ($complexArray[$value] as $key3 => $row) {
						
// 						echo "<tr>";
						
// 						echo "<td>";
// 						echo $row['golf-club-seq'];
// 						echo "</td>";
						
// 						echo "<td>";
// 						echo 'Golf Club:'.$row['name']."<br>";
// 						echo 'Deposit: HKD $'.$row['deposit']."<br>";
// 						echo 'Rental-Fee: HKD $'.$row['rental-fee']."<br>";
// 						echo 'Start From:'.$row['start-dt']."<br>";
// 						echo "<hr>";
// 						echo "<a href=\"?seq=".$row['golf-club-seq']."&print_rent\">Print</a>";
// 						echo "<hr>";
// 						echo "<a href=\"?seq=".$row['golf-club-seq']."&return\">Return</a>";
// 						echo "<hr>";
// 						echo "</td>";

// 						echo "</tr>";
// 					}

					

// 					echo "</table>";

// 				}

// 				echo "</td>";

// 				echo "</tr>";

// 	}
// 			echo "</table>";

// 		echo "</td>";
// }
// 	echo "</tr>";
// echo "</table>";

 ?>

<h1>歸還歷史 Returned History</h1>
<?php








$sql = "
	SELECT 
		`golf-club-seq`, 
		`start-dt`, 
		`bay`, 
		`returned`,

		`name`,
		`deposit`,
		`rental-fee`

	FROM `golf-club-rental-record` 
	WHERE `returned` IS NOT NULL
	ORDER BY `start-dt` DESC;
";
$result = $conn->query($sql);

echo "<table>";
    	echo "<tr>";
    	
    	echo "<td style=\"width: 200px;\">";
    	echo '序列號<br>Sequence Number';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '租賃 開始日期時間<br>Rental Timestamp';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "球道<br>Bay";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "歸還日期時間<br>Return Timestamp";
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '高爾夫球桿<br>Golf Club Rental';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo '按金<br>Deposit';
    	echo "</td>";

    	echo "<td style=\"width: 200px;\">";
    	echo "租賃費<br>Rental Fee";
    	echo "</td>";

    	echo "</tr>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    	echo "<tr>";

    	echo "<td>";
    	echo $row['golf-club-seq'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['start-dt'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['bay'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['returned'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['name'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['deposit'];
    	echo "</td>";

    	echo "<td>";
    	echo $row['rental-fee'];
    	echo "</td>";

    	echo "</tr>";
    }
}
echo "</table>";


$conn->close();


 ?>

