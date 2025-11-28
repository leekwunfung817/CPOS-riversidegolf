<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

	<title>Booking Form HTML Template</title>

	<!-- Google font -->
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet">

	<!-- Bootstrap -->
	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />

	<!-- Custom stlylesheet -->
	<link type="text/css" rel="stylesheet" href="css/style.css" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>

<!-- 

Use php to generate html, css to create a complex group of check box in a selection table, the first row shows 1 to 60 position numbers; the second row is a series of date from today to the following six dates (Total 7 days, show the date with format YYYY-MM-DD in the table cell); the third row is a series of hours from 09:00 am to 10:00 pm (Totally 13 hours - one table cell for each hour) with two hours text (the 24-hour formatted time, and the 12-hour formatted time), each hour is assigned an check box for html form.

 -->


	<div id="booking" class="section">
		<div class="section-center">
			<div class="container">
				<div class="row">




					<div class="col-md-7 col-md-push-5">
						<div class="booking-cta">
							<br><br><br>
							<br><br><br>
							<h1>白石高爾夫球練習場</h1>

							<p>白石高爾夫球練習場 是各種技能水平的高爾夫球手的熱門目的地，並提供各種服務和設施。

練習場全年開放，提供室內和室外練習場。 室內海灣設有氣候控制，室外海灣可欣賞周圍山脈的景色。 練習場配備了各種目標果嶺，還設有切桿和推桿區。

白石高爾夫球練習場還提供各種課程和診所，並設有專業商店。 練習場靠近其他幾個高爾夫球場，該地區還有許多餐廳和酒店。
							</p>
						</div>
					</div>










					<div class="col-md-4 col-md-pull-7">
						<div class="booking-form">
							<form action="../payment-page/">
								<div class="form-group">
									<span class="form-label">姓名</span>
									<input class="form-control" type="text" name="name" placeholder="請輸入與護照相同的全名" autocomplete>
								</div>
								<div class="row">
									<div class="col-sm-6">
										<div class="form-group">
											<span class="form-label">進入時間</span>
											<input class="form-control" name="start_date" type="datetime-local" value="<?php echo date('Y-m-d\TH:i'); ?>" autocomplete required>
										</div>
									</div>
									<div class="col-sm-6">
										<div class="form-group">
											<span class="form-label">離開時間</span>
											<input class="form-control" name="end_date" type="datetime-local" value="<?php echo date('Y-m-d\TH:i'); ?>" autocomplete required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">成人人數</span>
											<select class="form-control" name="num_adults" autocomplete>
												<option>1</option>
												<option>2</option>
												<option>3</option>
											</select>
											<span class="select-arrow"></span>
										</div>
									</div>
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">兒童人數</span>
											<select class="form-control" name="num_children" autocomplete>
												<option>0</option>
												<option>1</option>
												<option>2</option>
											</select>
											<span class="select-arrow"></span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-4">
										<div class="form-group">
											<span class="form-label">預訂企位數量</span>
											<select class="form-control" name="num_spot" autocomplete>
												<option>1</option>
												<option>2</option>
												<option>3</option>
											</select>
											<span class="select-arrow"></span>
										</div>
									</div>
								</div>















								<div class="form-btn">
									<button class="submit-btn">Check availability</button>
								</div>
<!-- 

Use javascript to create an associative array, the keys of the first level array are 1 to 60 position numbers; the keys of the second level array are 1 to 7 with the value of date from today to the following six dates (Total 7 days) and a lower level of array (The third level); the keys of the third level array are the hours from 09:00 am to 10:00 pm (Totally 13 keys - one key for each hour) with three values (A number - (-1), the 24-hour formatted time, and the 12-hour formatted time).

 -->
<script type="text/javascript">

function createSchedule() {
  const today = new Date();
  const schedule = {};

  // Loop through positions (1 to 60)
  for (let position = 1; position <= 60; position++) {
    schedule[position] = {};

    // Loop through days (7 days)
    for (let day = 1; day <= 7; day++) {
      const date = new Date(today.getTime() + (day - 1) * 24 * 60 * 60 * 1000); // Add days to today's date

		const month = date.getUTCMonth() + 1; // Months are indexed from 0 to 11
		const day1 = date.getUTCDate();
		const year = date.getUTCFullYear();

      schedule[position][day] = {};

      // Loop through hours (09:00 - 22:00)
      for (let hour = 9; hour <= 22; hour++) {
        const formattedTime = `${hour.toString().padStart(2, '0')}:00`; // Pad hour with leading zero
        var formattedTime12;
        if (hour>12) {
        	formattedTime12 = `${(hour-12).toString().padStart(2, '0')}:00 pm`; // Pad hour with leading zero
        } else {
        	formattedTime12 = `${hour.toString().padStart(2, '0')}:00 am`; // Pad hour with leading zero
        }
        schedule[position][day][formattedTime] = [false, formattedTime,formattedTime12,date,year,month,day1]; // Boolean and time
      }
    }
  }

  return schedule;
}

const mySchedule = createSchedule();

// Example usage:
console.log(mySchedule);
console.log(mySchedule[10][2]["11:00"][0]); // Check if slot is available (position 10, day 2, 11:00) - initially false

const formattedJSON = JSON.stringify(mySchedule, null, 2); // The third argument sets the indentation level (2 spaces in this case)
console.log(formattedJSON);
</script>

							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body><!-- This templates was made by Colorlib (https://colorlib.com) -->

</html>