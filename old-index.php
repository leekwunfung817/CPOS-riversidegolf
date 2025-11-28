
<!DOCTYPE html>
<html lang="en">

<meta charset="utf-8">
<style type="text/css">

	table {
		text-align: left;
		border-collapse: collapse;
		width: 100%;
	}

	th, td {
		text-align: left;
		border: 1px solid #ddd;
	}

	label {
		display: block;
	}

</style>


<form method="get" action="./email-confirmation.php">

	<hr>

	<div class="form-group">
		<span class="form-label">姓名</span>
		<input class="form-control" type="text" name="name" placeholder="請輸入姓名" required autocomplete>
	</div>
	<br>

	<div class="form-group">
		<span class="form-label">電子郵件地址</span>
		<input class="form-control" type="text" name="email" placeholder="請輸入電子郵件地址" required autocomplete>
	</div>
	<br>

	<div class="form-group">
		<span class="form-label">電話號碼</span>
		<input class="form-control" type="text" name="telephone" placeholder="請輸入電話號碼" autocomplete>
	</div>
	<br>

	<hr>

















<?php

// Function to generate date string for the next 6 days (including today)
function getNextWeekDates() {
  $dates = [];
  for ($i = 0; $i < 7; $i++) {
    $dates[] = date('Y-m-d', strtotime("+$i days"));
  }
  return $dates;
}

// Get next week dates
$dates = getNextWeekDates();

?>

<select name="booking_date">
	<?php foreach ($dates as $date): ?>
		<option value="<?php echo $date; ?>"><?php echo $date; ?></option>
	<?php endforeach; ?>
</select>

<!-- 
Use javascript to create listener of the select-option "begin_hour" and set the "end_hour" have more than one than "begin_hour" when "begin_hour" have any change 
 -->
<select name="begin_hour" id="begin_hour">
  <?php for ($hour = 9; $hour <= 21; $hour++): ?>
    <option value="<?php echo $hour ?>"><?php echo $hour; ?></option>
  <?php endfor; ?>
</select>

<select name="end_hour" id="end_hour">
  <?php for ($hour = 10; $hour <= 22; $hour++): ?>
    <option value="<?php echo $hour ?>"><?php echo $hour; ?></option>
  <?php endfor; ?>
</select>

<script>
	// javascript get the current hour number
	currentHour = parseInt(new Date().getHours());
	if (currentHour > 21 || currentHour < 9) {
		currentHour = 9;
	} else {
		currentHour = currentHour + 1;
	}
	document.getElementById("begin_hour").value = currentHour;
	document.getElementById("end_hour").value = currentHour + 1;

	document.getElementById("begin_hour").addEventListener("change", function() {
		const selectedBeginHour = parseInt(this.value);
		document.getElementById("end_hour").value = selectedBeginHour + 1;
	});
</script>













<!-- 

Use php to generate html, css to create a complex group of check box in a selection table, the first row shows 1 to 60 position numbers; the second row is a series of date from today to the following six dates (Total 7 days, show the date with format YYYY-MM-DD in the table cell); the third row is a series of hours from 09:00 am to 10:00 pm (Totally 13 hours - one table cell for each hour) with two hours text (the 24-hour formatted time, and the 12-hour formatted time), each hour is assigned an check box for html form.

 -->

	<div class="booking-form">
		<hr>
		請選擇高爾夫打球 預訂位置
		<table>
			<tbody>
				<tr>
					<?php for ($i=1; $i <= 30; $i++) {  ?>
					<th><?php echo $i; ?></th>
					<?php } ?>
				</tr>
				<tr>
					<?php for ($i=1; $i <= 30; $i++) {  ?>
					<td>
					<input type="checkbox" id="position_<?php echo "$i"; ?>" name="p_selections[]" value="position_<?php echo "$i"; ?>">
					</td>
					<?php } ?>
				</tr>


				<tr>
					<?php for ($i=31; $i <= 60; $i++) {  ?>
					<th><?php echo $i; ?></th>
					<?php } ?>
				</tr>
				<tr>
					<?php for ($i=31; $i <= 60; $i++) {  ?>
					<td>
					<input type="checkbox" id="position_<?php echo "$i"; ?>" name="p_selections[]" value="position_<?php echo "$i"; ?>">
					</td>
					<?php } ?>
				</tr>
			</tbody>
		</table>
	</div>

	<input type="submit" name="" value="提交">

</form>