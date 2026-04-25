<?php

session_start();

require_once './logger.php';
t_log('begin[booking-records-bydate-2.php]');



if (!isset($_SESSION["management"])) {
     ?>
  <meta charset="UTF-8">
    <script type="text/javascript">
        alert('請使用您的員工帳號重新登錄\nPlease login with your staff account again');
        window.location.href = "./";
    </script>
    <?php
    die();
}






?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>高爾夫網上預訂記錄表</title>
  
  <style type="text/css">

    table {
      text-align: left;
      border-collapse: collapse;
      width: 100%;
    }

    th, td {
      text-align: left;
      vertical-align: text-top;
      border: 1px solid black;
/*      padding: 5px;*/
      white-space: nowrap;
/*      font-size: 0.9em;*/
    }

    th:first-child, td:first-child {
      width: 80px;
    }

    label {
      display: block;
    }

  </style>
</head>









<body>

<link href="bootstrap.css" rel="stylesheet"/>
<script src="jquery.js"></script>
<script src="bootstrap.js"></script>

<script type="text/javascript">
  $('[data-toggle="popover"]').popover({
    placement: "auto",
    trigger: "hover"
  })
</script>

<style>
  .green {
    background-color: green;
  }
  .yellow {
    background-color: yellow;
  }
  .red {
    background-color: red;
  }
  .blue {
    background-color: #5DE2E7;
  }
  .orange {
    background-color: orange;
  }
  .grey {
    background-color: grey;
  }
</style>

<style type="text/css">

span.tooltip {
      position: absolute;
      width: 100px;
      height: 20px;
      line-height: 20px;
      padding: 10px;
      font-size: 14px;
      text-align: center;
      color: rgb(113, 157, 171);
      background: rgb(255, 255, 255);
      border: 4px solid rgb(255, 255, 255);
      border-radius: 5px;
      text-shadow: rgba(0, 0, 0, 0.1) 1px 1px 1px;
      box-shadow: rgba(0, 0, 0, 0.1) 1px 1px 2px 0px;
}

span.tooltip:after {
      content: "";
      position: absolute;
      width: 0;
      height: 0;
      border-width: 10px;
      border-style: solid;
      border-color: #FFFFFF transparent transparent transparent;
      top: 44px;
      left: 50px;
}

b {
  font-size: 18px;
}
td {
  border-style: solid;
  border-width: medium;
  border-width: 1px;
  width: 100px;
  text-align: center;
}






.Selected {
	background-color: purple; /* Customize the selected cell style */
}
</style>
<br>
<a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a>  

<br>
團體預訂或現場預訂 Group booking or walk in booking
<br>

<?php 
if (true) {

  if (isset($_GET['half_hour'])) {
  ?>
  <a href="?whole_hour">切換到<b style="color: red;">一</b>小時制 Switch to one-hour system</a>
  <?php 
  } else {
  ?>
  <a href="?half_hour">切換到<b style="color: red;">半</b>小時制 Switch to half-hour system</a>
  <?php 
  }
  
}
?>

<table style="width: 100%;height: 100%;">
  <tr>
    <th>
    	<br>
		<table>
			<tr>
				<td>
					<label for="dateInput">日曆 - 日期 Calendar Date : </label>
					<input type="date" id="dateInput" name="date" pattern="\d{4}-\d{1,2}-\d{1,2}" onchange="reflesh()">
				</td>
				<td><button onclick="
                window.location.reload();
                reflesh(true);
                ">清除選擇並刷新<br>Clear selection and refresh</button></td>
			</tr>
		</table>


        <table>
            <tr>
                <td>
                    <button onclick="changeDate('year', -1)" style="width: 100%;">- Year</button>
                </td>
                <td id="year_display">
                    
                </td>
                <td>
                    <button onclick="changeDate('year', 1)" style="width: 100%;">+ Year</button>
                </td>

                
                <td>
                    <button onclick="changeDate('month', -1)" style="width: 100%;">- Month</button>
                </td>
                <td id="month_display">
                    
                </td>
                <td>
                    <button onclick="changeDate('month', 1)" style="width: 100%;">+ Month</button>
                </td>


                <td>
                    <button onclick="changeDate('day', -1)" style="width: 100%;">- Day</button>
                </td>
                <td id="day_display">
                    
                </td>
                <td>
                    <button onclick="changeDate('day', 1)" style="width: 100%;">+ Day</button>
                </td>
            </tr>
        </table>

        <input type="text" id="selected_display" style="width: 100%;" name="" value="">
        <br>
        <small style="color: red;">
            請不要選擇超過40個球道，否則將無法預訂
            Please do not select more than 40 fairways or your reservation will not be available
        </small>

    </th>
    	


<?php 


function spotHiddenList($arr)
{
	?>
	<div style="display: none;">
	<?php
    for ($i=0; $i < sizeof($arr); $i++) { 
        $p=$arr[$i];
     ?>
    <input type="checkbox" id="position_<?php echo "$p"; ?>" class="position_checkbox" name="p_selections[]" value="position_<?php echo "$p"; ?>">
    <?php }

	?>
	</div>
	<?php
}

 ?>
<th>
    	<form method="post" action="./email-confirmation.php" target="_blank">

<!-- 
booking_date
begin_hour
end_hour

discount
name
email
octopus_no
octopus_no_q

telephone
p_selections
 -->
    		<input type="hidden" id="booking_date" name="booking_date" readonly>
    		<input type="hidden" id="begin_hour" name="begin_hour" readonly>
    		<input type="hidden" id="end_hour" name="end_hour" readonly>

    		<input type="hidden" name="discount" value="H">
    		<input type="hidden" name="name" value="">
    		<input type="hidden" name="email" value="">
    		<input type="hidden" name="octopus_no" value="">
    		<input type="hidden" name="octopus_no_q" value="">
    		<table>
    			<tr>
    				<td>Telephone</td>
    				<td>Remark</td>
    			</tr>
    			<tr>
    				<td><input type="tel" name="telephone" value=""></td>
    				<td><input type="text" name="remark" value=""></td>
    				<td><button onclick="

setTimeout(function(){
  window.location.reload(1);
}, 1000);


    				">提交選擇<br>Submit selection</button></td>
    			</tr>
    		</table>
    		<style type="text/css">
    			input {
    				height: 50px;
    				width: 300px;
    			}
    			th, td {
/*    				padding: 5px;*/
    			}
    		</style>

    		<?php 

                $position_list = array(
                    //Sand
                    array(
                        1,2
                        // ,3
                    ),
                    // VIP
                    array(
                        "VIP"
                    ),
                    // Iron
                    array(
                        
                        5,6,7,8,9,10,11,12,13,
                        15,16,
                        17,18,19,20,21,22,23,
                        25,26,
                        27,28,29,30,31,32,33,
                        35,
                        36,37,38,39,
                    ),
                    // Wood
                    array(
                        50,51,52,53,

                        55,56,57,
                        59,60,61,62,63,
                        65,66,67,68,69,70,71,72,73,

                        75,76,77,78,79,80,81,82,83,84,85
                    ),
                    // Pickleball
                    array(
                      100,101,102,103
                    ),
                );

                spotHiddenList($position_list[0]);
                spotHiddenList($position_list[1]);
                spotHiddenList($position_list[2]);
                spotHiddenList($position_list[3]);
                spotHiddenList($position_list[4]);


    		 ?>
	    	
	    </form>
    	

<script>
    function changeDate(unit, amount) {
	    const selectedCells = document.querySelectorAll("#fair_way_table .Selected");
	    if (selectedCells.length > 0) {
		alert('請盡快提交或取消選擇所有選定的球道。\n Please submit or deselect all the selected bay as soon as possible.');
			return;
	    }


        const dateInput = document.getElementById("dateInput");
        const currentDate = new Date(dateInput.value);
        let newDate;

        switch (unit) {
            case 'day':
                newDate = new Date(currentDate);
                newDate.setDate(currentDate.getDate() + amount);
                break;
            case 'month':
                newDate = new Date(currentDate);
                newDate.setMonth(currentDate.getMonth() + amount);
                break;
            case 'year':
                newDate = new Date(currentDate);
                newDate.setFullYear(currentDate.getFullYear() + amount);
                break;
        }

        dateInput.valueAsDate = newDate;



        reflesh_display();
        reflesh();
    }
</script>
        <small style="color: grey;">如要預訂不同時段，需要進行另一次預約。 To book a different time slot, you will need to make another times of reservation.</small>

      <table style="width: 100%;">
        <tr><th> 白色（預訂空缺）<br> White (Booking vacancy) </th>
        <th class="red"> 紅色（待付款）<br> Red (Pendding for payment) </th>
        <th class="yellow"> 黃色（已付款等待到達）<br> Yellow (Paid and wait for arrival) </th>
        <th class="orange"> 橙色（車輛已到達）<br> Orange (Vehicle arrived) </th>
        <th class="blue"> 藍色（已簽到）<br> Blue (Checked-in) </th></tr>
      </table>

    </th>
    <th style="display: none;">
        


    </th>
    <th style="display: none;">
    	Receipt Printing Window:<br>
		<iframe id="receipt_printing_buffer" style="width: 300px;height: 300px;">
		</iframe>
    <br>

<a onclick="

    const oIframe = document.getElementById('receipt_printing_buffer');
    oIframe.contentWindow.print();

" 
style="
  color: blue;
  background-image: linear-gradient(to right top, #5F96F6, #5FE2F6);
  padding: 10px;
  width: 350px;
  border-radius: 30px;
  display: block;
  text-align: center;
  cursor: pointer;
">
  列印收據 Print Receipt
</a>

    </th>
  </tr>
  <tr>
      <td colspan="2">
<div id="record_table_area"  style="overflow-y: scroll;width: 100%;height: 600px;"></div>
          
      </td>
  </tr>
</table>
<?php echo (isset($_GET['half_hour'])?')))half_hour(((':''); ?>
<style type="text/css">
	#record_table_area td:hover {
		border-color: cyan;
	}
	#record_table_area td {
/*		margin: 10px;
		padding: 10px;*/
		border-style: double;
		border-width: 5px;
	}
</style>


<script type="text/javascript">

async function fetchHtml(url, callback) {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const html = await response.text();
        // html_result = html;
        // code_buffer = html;
        // console.log(html);
        callback(html)
        return html;
    } catch (error) {
        console.error('Error fetching HTML:', error);
        return null; // Or handle the error differently
    }
}

var current_date = "<?php 
date_default_timezone_set('Asia/Hong_Kong');

$currentDate = new DateTime();
$current_timestamp = $currentDate->format('Y-m-d');
echo $current_timestamp;
 ?>";

document.getElementById('dateInput').value = current_date;

function formatDate(inputDate) {
    const dateParts = inputDate.split('-');
    if (dateParts.length !== 3) {
        return null; // Invalid input format
    }
    const month = dateParts[1];
    const day = dateParts[2];
    const year = dateParts[0];
    return `${month}/${day}/${year}`;
}

function reflesh_display() {
	setTimeout(function () {
	    const dateInput = document.getElementById("dateInput");
	    const currentDate = new Date(formatDate(dateInput.value));
		// console.log('Date Input : '+currentDate+' '+currentDate.getDate());
	    
	    document.getElementById('year_display').innerHTML = currentDate.getFullYear();
	    document.getElementById('month_display').innerHTML = currentDate.getMonth()+1;
	    document.getElementById('day_display').innerHTML = currentDate.getDate();

		// const formattedDate = currentDate.toISOString().split('T')[0];
    if (document.getElementById('booking_date').value != dateInput.value) {
      // console.log('dateInput.value='+dateInput.value+' > '+formatDate(dateInput.value));
      console.log('booking_date: from '+document.getElementById('booking_date').value+' to '+dateInput.value);
      document.getElementById('booking_date').value = dateInput.value;
    }
		
	},10);
}

function initFairWayGroupToggle() {
  var table = document.getElementById('fair_way_table');
  console.log('[group-toggle-main-2] init start, table=', table);
  if (!table) {
    console.log('[group-toggle-main-2] no table found, abort');
    return;
  }

  var groupRows = table.querySelectorAll('.group_row');
  console.log('[group-toggle-main-2] group rows found=', groupRows.length, groupRows);

  function hasClass(element, className) {
    return (' ' + element.className + ' ').indexOf(' ' + className + ' ') > -1;
  }

  function addClass(element, className) {
    if (!hasClass(element, className)) {
      element.className = element.className ? element.className + ' ' + className : className;
    }
  }

  function removeClass(element, className) {
    var classString = ' ' + element.className + ' ';
    while (classString.indexOf(' ' + className + ' ') > -1) {
      classString = classString.replace(' ' + className + ' ', ' ');
    }
    element.className = classString.replace(/^\s+|\s+$/g, '');
  }

  function toggleGroup(groupRow) {
    var groupId = groupRow.getAttribute('data-group');
    var headerCell = groupRow.getElementsByTagName('th')[0];
    var icon = groupRow.querySelector('.group_toggle_icon');
    var memberRows = table.querySelectorAll('.time_row[data-group="' + groupId + '"]');
    var isExpanded = headerCell && headerCell.getAttribute('aria-expanded') !== 'false';
    var index;

    console.log('[group-toggle-main-2] click groupId=', groupId, 'memberRows=', memberRows.length, memberRows, 'expanded=', isExpanded);

    for (index = 0; index < memberRows.length; index++) {
      if (isExpanded) {
        addClass(memberRows[index], 'is-hidden');
      } else {
        removeClass(memberRows[index], 'is-hidden');
      }
      console.log('[group-toggle-main-2] row updated index=', index, 'className=', memberRows[index].className);
    }

    if (headerCell) {
      headerCell.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
      console.log('[group-toggle-main-2] new aria-expanded=', headerCell.getAttribute('aria-expanded'));
    }

    if (icon) {
      icon.innerHTML = isExpanded ? '&#9654;' : '&#9660;';
      console.log('[group-toggle-main-2] icon updated=', icon.innerHTML);
    }
  }

  window.toggleFairWayGroupRow = function (groupRow) {
    console.log('[group-toggle-main-2] window.toggleFairWayGroupRow called with', groupRow);
    if (!groupRow) {
      console.log('[group-toggle-main-2] window.toggleFairWayGroupRow missing groupRow');
      return;
    }
    toggleGroup(groupRow);
  };

  for (var rowIndex = 0; rowIndex < groupRows.length; rowIndex++) {
    console.log('[group-toggle-main-2] binding rowIndex=', rowIndex, 'group=', groupRows[rowIndex].getAttribute('data-group'));
    groupRows[rowIndex].onclick = function () {
      console.log('[group-toggle-main-2] click event fired for', this);
      toggleGroup(this);
    };
  }

  console.log('[group-toggle-main-2] init complete');
}

function submit_selection() {
	
}
const array = ['A', 'B', 'C'];
const search_term = 'B';
for (let i = array.length - 1; i >= 0; i--) {
    if (array[i] === search_term) {
        array.splice(i, 1); // Remove the matching term
        // Uncomment the 'break' statement if you want to remove only the first occurrence
    }
}

function deselect_cell(cell_element) {
  cell = cell_element;
  classList = cell.classList.value.split(' ');
  stringToRemove = "Selected";
  for (let i = classList.length - 1; i >= 0; i--) {
      if (classList[i] === stringToRemove) {
          classList.splice(i, 1);
      }
  }
  cell.classList = classList.join(" ");
}

function select_cell(cell_element) {
	cell = cell_element;
	classList = cell.classList.value.split(' ');
	stringToRemove = "Selected";
	const indexToRemove = classList.indexOf(stringToRemove);
	if (indexToRemove > -1) {
	    classList.splice(indexToRemove, 1); // Remove one element at the specified index
	}
	classList.push(stringToRemove);
	cell.classList = classList.join(" ");
}

function reflesh() {
	reflesh(false);
}

function reflesh(skip_selection) {
    // window.location.reload();
	if (!skip_selection) {
	    const selectedCells = document.querySelectorAll("#fair_way_table .Selected");
	    if (selectedCells.length > 0) {
			// alert('請盡快提交或取消選擇所有選定的球道。\n Please submit or deselect all the selected bay as soon as possible.');
			return;
	    }
	}

	console.log('Reflesh');
	var select_date = document.getElementById('dateInput').value;
	console.log('Date Input : '+select_date);

	// document.getElementById('record_table_area').innerHTML = '<h1> Loading ...... </h1>';
	fetchHtml('./booking-records-bydate-api.php?<?php echo (isset($_GET['half_hour'])?'half_hour':''); ?>&exact_date='+select_date, function(html) {
		document.getElementById('record_table_area').innerHTML = html;
    initFairWayGroupToggle();
		reflesh_display();


		const cells = document.querySelectorAll("#fair_way_table td");
		console.log('cells:'+cells);
		cells.forEach(cell => {
			if (cell.classList.contains('booked')) {

			} else {
				cell.addEventListener("click", () => {
					select_cell(cell);
					console.log(cell.classList);
					showSelectedCells();
				});
			}
			
		});


	});
}

setInterval(function(){
	reflesh();
}, 60*1000);

reflesh();


function showSelectedCells() {
  const selectedCells = document.querySelectorAll("#fair_way_table .Selected");
  console.log('selectedCells:');
  console.log(selectedCells);
  console.log(`You selected ${selectedCells.length} cells.`);

  var min_hour = 22;
  var max_hour = 8;
  var position = [];
  var position_key = {};
  for (let i = 0; i < selectedCells.length; i++) {
    var class_arr = selectedCells[i].classList.value.split(' ');

    <?php if (isset($_GET['half_hour'])) {  ?>
    var cur_hour = parseFloat(class_arr[0]);
    <?php } else { ?>
    var cur_hour = parseInt(class_arr[0]);
    <?php } ?>
    
    var cur_position = class_arr[1];
    if (min_hour > cur_hour) {
      min_hour = cur_hour;
    }
    if (max_hour < cur_hour) {
      max_hour = cur_hour;
    }
    position.push(cur_position);
    position_key[cur_position] = 1;
    console.log('cur_position:',cur_position);
  }
  console.log(`Position `+JSON.stringify(position_key)+` from `+min_hour+` to `+max_hour);
  document.getElementById('selected_display').value = `From `+min_hour+`:00 To `+max_hour+`:00 - `;


	for (var i = position.length - 1; i >= 0; i--) {
		for (var ii = min_hour; ii <= max_hour;
      <?php if (isset($_GET['half_hour'])) {  ?>
        ii+=0.5
      <?php } else { ?>
        ii++
      <?php } ?>
    ) {
			classes_buffer = position[i]+" "+ii;
			const cells = document.getElementsByClassName(ii+" "+position[i]);
			// console.log('classes_buffer:',classes_buffer,cells.length);
			
			for (iii = 0; iii < cells.length; iii++) {
				classList = cells[iii].classList.value.split(' ');
				var first_class = classList[0];
				var second_class = classList[1];
				if (first_class==ii && second_class==position[i]) {
					// console.log(
					// 	'first_class:',first_class,ii
					// 	,'second_class:',second_class,position[i]
					// 	,'class_list:',classList
					// );
					// console.log('Hit');
					select_cell(cells[iii]);
				}
			}
		}		
	}


	document.getElementById('begin_hour').value = min_hour;
      <?php if (isset($_GET['half_hour'])) {  ?>
	document.getElementById('end_hour').value = max_hour + 0.5;
      <?php } else { ?>
	document.getElementById('end_hour').value = max_hour + 1;
      <?php } ?>
	const inputElements = document.getElementsByClassName('position_checkbox');
	for (let key in position_key) {
		var is_checked = false;
		for (let i = 0; i < inputElements.length; ++i) {
			if (inputElements[i].value == 'position_'+key) {
        document.getElementById('selected_display').value += ` (Bay `+key+`) `;
				console.log('Check ',key);
				inputElements[i].checked = true;
				is_checked = true;
			}
		}
		if (!is_checked) {
			console.log('Not checked ',key);
		}
	}

  const cells = document.getElementsByClassName("booked Selected");
  if (cells.length > 0) {
    alert('Schedult Conflict.');
    reflesh(true);
  }

}

function discount_digit_convert(data) {
  if (data=='S') {
      return 'Student';
  } else if (data=='H') {
      return 'No disount';
  } else if (data=='D') {
      return 'Disabled';
  }
}



function allBetween100and199(csv) {
    // clean spaces
    const arr = csv.split(',').map(v => v.trim());

    // convert to integers and validate
    return arr.every(v => {
        if (!/^\d+$/.test(v)) return false;   // must be integer
        const n = Number(v);
        return n >= 100 && n <= 199;
    });
}

// Example:
// console.log(allBetween100and199("101,150,199")); // true
// console.log(allBetween100and199("101, 200, 150")); // false

function transformCsvIfInPickleballRange(csv) {
	const normalizedCsv = String(csv).replace(/\s+/g, "");
	const parts = normalizedCsv.split(",");

  // Convert to numbers and ensure all are valid integers
  const nums = parts.map(v => Number(v));
  if (nums.some(n => !Number.isInteger(n))) {
    return null; // invalid input
  }

  // Check all are between 100 and 199 (inclusive)
  const allInRange = nums.every(n => n >= 100 && n <= 199);
  if (!allInRange) {
    return null; // condition not met
  }

  // Subtract 99 and return as new CSV
  return nums.map(n => n - 99).join(",");
}

function comfirm_and_print(
  auth,
  id,
  name,
  telephone,
  octopus_no,
  check_digit,
  booking_date,
  begin_hour,
  end_hour,
  p_selections,
  discount,
  auth_code,
  req_card_number,
  amount,
  cash
) {

  var isPickleball = allBetween100and199(p_selections);
  var msg = '';
  var printing = '<h1>'+(isPickleball ? '白石匹克球練習場 <br> Whitehead Pickleball Club' : '白石高球練習場 <br> Whitehead Golf Club')+'</h1>';
  printing += '<div style="text-align: right;">Tel: 27771813</div>';
  // printing += '<div style="text-align: right;">RIVERSIDE Whitehead Golf Club</div>';
  printing += '<i style="text-align: center;"><hr></i>';

  sourceTxt = p_selections.replace(/,/g, ", ");
  if (sourceTxt.length>1) {

    var pickelball_csv = transformCsvIfInPickleballRange(sourceTxt);
    if (pickelball_csv) {
      msg += 'Booking:'+pickelball_csv+'\n';
      printing += '<b style="text-align: left;font-size: 1.5em;">Court: '+pickelball_csv+'</b><br>';
    } else {
      msg += 'Booking:'+sourceTxt+'\n';
      printing += '<b style="text-align: left;font-size: 1.5em;">Bay: '+sourceTxt+'</b><br>';
    }


  }
  
  sourceTxt = booking_date;
  if (sourceTxt.length>1) {
    msg += 'Date:'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.8em;">Date: '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = begin_hour;
  sourceTxt2 = end_hour;
  if ( sourceTxt.length>0 && sourceTxt2.length>0 ) {
    msg += 'Time:'+sourceTxt+' to '+sourceTxt2+'\n';
    printing += '<b style="text-align: left;font-size: 1.8em;">Time: '+sourceTxt+'-'+sourceTxt2+'</b><br>';
  }
  
  sourceTxt = name;
  if (sourceTxt.length>1) {
    msg += 'Name:'+sourceTxt+'\n';
    printing += '<i style="text-align: right;">Name: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = telephone;
  if (sourceTxt.length>1) {
    msg += 'Tel:'+sourceTxt+'\n';
    printing += '<i style="text-align: right;">Tel: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = auth;
  if (sourceTxt.length>1) {
    msg += 'Auth:'+sourceTxt+'\n';
  }
  
  sourceTxt = id;
  if (sourceTxt.length>1) {
    msg += 'ID:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">ID: '+sourceTxt+'</i><br>';
  }
  
  sourceTxt = octopus_no;
  sourceTxt2 = check_digit;
  if ( sourceTxt.length>1 && sourceTxt2.length>1 ) {
    msg += 'Octopus: '+sourceTxt+' ('+sourceTxt2+')'+'\n';
  }
  
  sourceTxt = auth_code;
  if (sourceTxt.length>1) {
    msg += 'Auth Code: '+sourceTxt+'\n';
  }
  
  sourceTxt = req_card_number;
  if (sourceTxt.length>1) {
    msg += 'Card Number: '+sourceTxt+'\n';
  }
  
  sourceTxt = discount_digit_convert(discount);
  if (sourceTxt.length>0) {
    msg += 'Discount:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Discount: '+sourceTxt+'</i><br>';
  }

  var hasPayment = false;

  sourceTxt = amount;
  if (sourceTxt.length>1) {
    hasPayment = true;
    msg += 'Credit Card Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Credit Card</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;">Amount: '+sourceTxt+'</b><br>';
  }
  
  sourceTxt = cash;
  if (sourceTxt.length>1) {
    hasPayment = true;
    msg += 'Cash Payment:'+sourceTxt+'\n';
    printing += '<i style="text-align: left;">Paid by: Cash</i><br>';
    printing += '<b style="text-align: left;font-size: 1.8em;">Amount: '+sourceTxt+'</b><br>';
  }


  if (confirm(msg)) {
    if (hasPayment) {
      window.open('./payment-page/payment-confirm.php?auth='+auth+'&decision=ACCEPT&download=true');
      // window.open('./payment-page/payment-confirm.php?auth='+auth_code+'&decision=ACCEPT&download=true');
    } else {
      const oIframe = document.getElementById('receipt_printing_buffer');
      oIframe.contentWindow.document.open();
      oIframe.contentWindow.document.write(printing);
      oIframe.contentWindow.document.close();
      oIframe.contentWindow.print();
    }
  }
}
</script>

<?php   

t_log('end[booking-records-bydate-2.php]');
die();

 ?>