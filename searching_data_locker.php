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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>


<table>
    <tr>
        <td>
            <h1>置物櫃記錄 搜尋引擎<br>Locker reservation search engine</h1>
            <a href="./admin-locker.php">登記儲物櫃 Register Locker</a>
        </td>
        <td>
            
<iframe id="receipt_printing_buffer" style="width: 300px;height: 400px;">
</iframe>
<script type="text/javascript">


function comfirm_and_print(
  locker_id,
  type,
  due_date,
  name,
  telephone,
  deposit,
  lock_number,
  lock_price,
  amount,
  month,
  lease_date,
  remark,
  auth
) {

    var msg = '';
    var printing = '<h1>白石高球練習場</h1>';
    printing += '<b style="text-align: left;font-size: 1.2em;">置物櫃記錄</b><br>';

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

    sourceTxt = locker_id;
    sourceName = '置物櫃編號 Locker ID';
    if (sourceTxt!=null && sourceTxt.length>0) {
        msg += sourceName+':'+sourceTxt+'\n';
        printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
    }

  sourceTxt = type;
  sourceName = '儲物櫃類型 Locker Type';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = due_date;
  sourceName = '過期日 Due Date';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = name;
  sourceName = '姓名 Name';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = telephone;
  sourceName = '電話 Tel.';
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

  sourceTxt = lock_number;
  sourceName = '鎖號 Lock Num.';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = lock_price;
  sourceName = '鎖價 Lock Price.';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = amount;
  sourceName = '價格 Amount: ';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = month;
  sourceName = '租借月份 Month';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = lease_date;
  sourceName = '租借日期 Lease Date';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = remark;
  sourceName = 'Remark';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = auth;
  sourceName = 'Reference';
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
    console.log('end of page 2');
    <?php 

function hour_num_to_hour_display($hour_num)
{
    $cursor_hour = ((float) $hour_num);
    $hour_int = ((int) $hour_num);
    $is_half_hour = $cursor_hour != $hour_int;
    $half_hour_mark = ($is_half_hour ? ':30' : ':00');
    return str_pad($hour_int, 2, "0", STR_PAD_LEFT) . $half_hour_mark;
}

            // echo '   comfirm_and_print('
            // .'\''.(isset($booking_arr_buf['auth']) ? $booking_arr_buf['auth'] : "").'\''
            // .',\''.(isset($booking_arr_buf['id']) ? $booking_arr_buf['id'] : "").'\''
            // .',\''.(isset($booking_arr_buf['name']) ? $booking_arr_buf['name'] : "").'\''
            // .',\''.(isset($booking_arr_buf['telephone']) ? $booking_arr_buf['telephone'] : "").'\''
            // .',\''.(isset($booking_arr_buf['octopus_no']) ? $booking_arr_buf['octopus_no'] : "").'\''
            // .',\''.(isset($booking_arr_buf['check_digit']) ? $booking_arr_buf['check_digit'] : "").'\''
            // .',\''.(isset($booking_arr_buf['booking_date']) ? $booking_arr_buf['booking_date'] : "").'\''
            // .',\''.(isset($booking_arr_buf['begin_hour']) ? hour_num_to_hour_display($booking_arr_buf['begin_hour']) : "").'\''
            // .',\''.(isset($booking_arr_buf['end_hour']) ? hour_num_to_hour_display($booking_arr_buf['end_hour']) : "").'\''
            // .',\''.(isset($booking_arr_buf['p_selections']) ? 
            //   str_replace('[', '', 
            //   str_replace(']', '', 
            //   str_replace('"', '', 
            //     $booking_arr_buf['p_selections']
            //   )
            //   )
            //   )
            //    : "").'\''
            // .',\''.(isset($booking_arr_buf['discount']) ? $booking_arr_buf['discount'] : "").'\''
            // .',\''.(isset($data['auth_code']) ? $data['auth_code'] : "").'\''
            // .',\''.(isset($data['req_card_number']) ? $data['req_card_number'] : "").'\''
            // .',\''.($credit_card_amount).'\''
            // .',\''.($cash_amount).'\''
            // .'); ';

            if ($is_management) {
                ?>

    const oIframe = document.getElementById('receipt_printing_buffer');
    oIframe.contentWindow.print();

                <?php
            }
     ?>
    console.log('end of page');
</script>

        </td>
    </tr>
</table>


    <style>
table, th, td {
    border: 1px solid black;
}
td {
    white-space:    nowrap  ;
}

.unpaid_button:hover {
    background-color: yellow;
    cursor: pointer;
}

.unpaid_button {
    color: red;
}


    </style>

數據將每 30 秒重新加載一次
Data will be reloaded every 30 seconds

    <table>
        <tr>
            <th colspan="10">
                搜尋欄 Search bar
            </th>
        </tr>
        <tr>
            <th>置物櫃編號 <br>Locker-ID<br><input type="text" id="locker_id_search" placeholder="Locker ID" autocomplete="off"></th>
            <th>置物櫃編號 <br>Locker-ID<br><input type="text" id="locker_type_search" placeholder="Locker Type" autocomplete="off"></th>
            <th>過期日<br>Due date<br><input type="text" id="due_date_search" placeholder="Date&time" autocomplete="off"></th>
            <th>姓名<br>Name<br><input type="text" id="name_search" placeholder="Name" autocomplete="off"></th>
            <th>電話<br>Telephone<br><input type="text" id="telephone_search" placeholder="Telephone" autocomplete="off"></th>
            <th>按金<br>Deposit<br><input type="text" id="deposit_search" placeholder="Deposit Amount" autocomplete="off"></th>
            <th>價格<br>Price<br><input type="text" id="price_search" placeholder="Price" autocomplete="off"></th>
            <th>鎖號<br>Lock Number<br><input type="text" id="lock_no_search" placeholder="Lock No." autocomplete="off"></th>
            <th>鎖價<br>Lock Price<br><input type="text" id="lock_price_search" placeholder="Lock Price" autocomplete="off"></th>
            <th>月份<br>Month<br><input type="text" id="month_search" placeholder="Month" autocomplete="off"></th>
            <th>租借日期<br>Lease Date<br><input type="text" id="lease_date_search" placeholder="Lease Date" autocomplete="off"></th>
            <th>備註 <br>Remark<br><input type="text" id="remark_search" placeholder="Remark" autocomplete="off"></th>

            <th>參考編號<br>Reference Number<br><input type="text" id="auth_search" placeholder="Authorization Code" autocomplete="off"></th>
        </tr>
    </table>

<hr>

    <table id="data-table">
        <thead id="table-head">
        </thead>
        <tbody id="table-body"></tbody>
    </table>

    <!-- Input boxes for searching -->
    
    
    

    <!-- Include your JavaScript code here -->
    <script type="text/javascript">

        column_translation = {
            "number":"置物櫃<br>Locker",
            "id":"預約編號<br>Reservation No.",
            "auth_code":"交易授權<br>編號<br>Authorization Number",
            "auth":"參考編號<br>Reference Number",
            "transaction_id":"銀行交易號碼<br>Transaction ID",
            "name":"姓名<br>Name",
            "email":"電郵<br>Email",
            "datetime":"租借日期<br>Lease Date",
            "locker-id":"置物櫃編號<br>Locker-ID",
            "currency":"貨幣<br>Currency",
            "telephone":"電話<br>Telephone",
            "octopus_no":"八達通號碼<br>Octopus",
            "booking_date":"預訂日期<br>Booking Date",
            "begin_hour":"開始時間<br>小時",
            "end_hour":"結束時間<br>小時",
            "p_selections":"球道<br>Bay(s)",
            "payment_confirmed":"付款已確認",
            "carpark_checked_in":"已入停車場",
            "fairway_checked_in":"已簽入球道",
            "email_confirmation_status":"電子郵件確認狀態",
            "golf_payment_status":"付款狀態",
            "booking_expired":"預訂過期",
            "Link":"QR Code",
            "req_card_number":"信用卡號碼<br>Credit Card Number",
            "amount":"價格<br>Price",
            "cash":"現金<br>交易金額<br>Cash Transaction Amount",
            "cash_timestamp":"現金交易日期<br>Cash Transaction Timestamp",
            "discount":"折扣 <br> Discount",
            "check_digit":"括號內數字<br>Brackets Number",
            "remark":"備註<br>Remark",
            "resend_email":"Resend Email",
            "is_paid":"銀行<br>或<br>支票<br>轉帳",

            "due-date":"過期日<br>Due date",
            "deposit":"按金<br>Deposit",
            "lock-number":"鎖號<br>Lock Number",
            "lock-price":"鎖價<br>Lock Price",

            "type":"儲物櫃類型<br>Locker Type",

            "month":"月份<br>Month",
            "print":"列印收據<br>Print Receipt",
            "clear":"移至歷史<br>Move to History",
            "src":"處理人員<br>Processing Staff"
        }
        column_translation_func = {
            "discount":function (data) {
                if (data=='S') {
                    return 'Student';
                } else if (data=='H') {
                    return '-';
                } else if (data=='D') {
                    return 'Disabled';
                }
            },
            "p_selections":function (data) {
                return data.split(/[\[\]"]/).join('');
            },
            "begin_hour":function (data) {
                var begin_hour_root = Math.floor(data);
                var begin_hour_val = parseFloat(data);
                var is_begin_half = (begin_hour_val > begin_hour_root);
                var begin_hour_time_part = (begin_hour_root+'').padStart(2, '0') + (is_begin_half?":30":":00");
                
                return begin_hour_time_part;
            },
            "end_hour":function (data) {

                var begin_hour_root = Math.floor(data);
                var begin_hour_val = parseFloat(data);
                var is_begin_half = (begin_hour_val > begin_hour_root);
                var begin_hour_time_part = (begin_hour_root+'').padStart(2, '0') + (is_begin_half?":30":":00");
                
                return begin_hour_time_part;
            }
        }

jsonData = null;

// Function to populate the table
function populateTable(filteredData) {




    const tableHead = document.getElementById("table-head");
    const tableBody = document.getElementById("table-body");

    // Clear existing rows
    tableBody.innerHTML = "";

    tableHead.innerHTML = "";
    hasHeader = false;
    const newheader = document.createElement("tr");

    // Loop through the data and create rows
    filteredData.forEach(function (row) {
        const newRow = document.createElement("tr");
        

        for (const key in row) {
            if (row.hasOwnProperty(key)) {
                // console.log(key);
                if (!hasHeader) {


                    newheader.innerHTML = newheader.innerHTML 
                    + '<th>'
                    +(
                        column_translation.hasOwnProperty(key)?
                        column_translation[key]
                        :key
                        )
                    +'</th>';
                }
                newRow.innerHTML = newRow.innerHTML 
                + '<td>'
                +(
                    row[key]==null?
                    '':
                    (
                        column_translation_func.hasOwnProperty(key)?
                        column_translation_func[key](row[key]):
                        row[key]
                    ) 
                )
                +'</td>';
            }
        }
        if (!hasHeader) {
            tableHead.appendChild(newheader);
        }
        tableBody.appendChild(newRow);
        hasHeader = true;

    });
}

async function fetchData(url, callback) {
    try {
        const response = await fetch(url);
        if (response.ok) {
            // console.log(response);
            // const json = await response.json();
            const html = await response.text();
            console.log(html);
            const json = JSON.parse(html);
            // Now you can use the 'json' variable containing your data
            // console.log(json);
            callback(json);
        } else {
            console.error('Error fetching data:', response.status);
        }
    } catch (error) {
        console.error('An error occurred:', error);
    }
}
arr = {};
arr['first_printed_bool'] = false;

printed = false;

// Example usage:
const apiUrl = './searching_data_locker_api.php'; // Replace with your API endpoint
function reflesh() {
    fetchData(apiUrl, function (data) {
        // console.log(data);
        jsonData = data;
        <?php if ( isset($_GET['number']) && isset($_GET['print']) ) { ?>
        if (!printed) {
            printed = true;

            data.forEach(function (row) {
                var com_1 = row['number']+'';
                var com_2 = '<?php echo $_GET['number']; ?>';

                if (com_1 == com_2 && !arr['first_printed_bool']) {
                    arr['first_printed_bool'] = true;
                    console.log(' com '+com_1+' '+com_2);
                    comfirm_and_print(
                      row['number'],
                      row['type'],
                      row['due-date'],
                      row['name'],
                      row['telephone'],
                      'HKD $ '+row['deposit'],
                      row['lock-number'],
                      'HKD $ '+row['lock-price'],
                      'HKD $ '+row['amount'],
                      row['month'],
                      row['datetime'],
                      row['remark'],
                      row['auth']
                    );
                }
            });

        }
        <?php } ?>
        populateTable(jsonData);
        filterTable();
    });
}
setInterval(reflesh, 30*1000); // Fetch data every half minute
reflesh();

<?php if ( isset($_GET['number']) && isset($_GET['clear']) ) {

    require_once 'account_variable.php';

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "
    INSERT INTO `golf-locker-transaction-history`
    select * from `golf-locker-transaction`
    where `golf-locker-transaction`.`locker-id`='".$_GET['number']."';
    ";

    try {
        if ($conn->query($sql) === TRUE) {


            $sql = "
            DELETE FROM `golf-locker-transaction` where `golf-locker-transaction`.`locker-id`='".$_GET['number']."';
            ";
            try {
                if ($conn->query($sql) === TRUE) {
                } else {
                }
            } catch (Exception $e) {
            }



        } else {
        }
    } catch (Exception $e) {
    }

    $conn->close();

} ?>














document.getElementById("locker_id_search").addEventListener("input", filterTable);
document.getElementById("locker_type_search").addEventListener("input", filterTable);

document.getElementById("due_date_search").addEventListener("input", filterTable);
document.getElementById("name_search").addEventListener("input", filterTable);

document.getElementById("telephone_search").addEventListener("input", filterTable);

document.getElementById("deposit_search").addEventListener("input", filterTable);
document.getElementById("price_search").addEventListener("input", filterTable);

document.getElementById("lock_no_search").addEventListener("input", filterTable);
document.getElementById("lock_price_search").addEventListener("input", filterTable);
document.getElementById("month_search").addEventListener("input", filterTable);
document.getElementById("lease_date_search").addEventListener("input", filterTable);
document.getElementById("remark_search").addEventListener("input", filterTable);
document.getElementById("auth_search").addEventListener("input", filterTable);

function filterHelper(row,data_key,html_id) {
    if (row[data_key] == null) {
        return '-'.toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
    }
    return row[data_key].toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
}

function filterTable() {
    const filteredData = jsonData.filter((row) =>
        filterHelper(row,'number','locker_id_search')
        && filterHelper(row,'type','locker_type_search')
        && filterHelper(row,'due-date','due_date_search')
        && filterHelper(row,'name','name_search')
        && filterHelper(row,'telephone','telephone_search')
        && filterHelper(row,'deposit','deposit_search')
        && filterHelper(row,'amount','price_search')
        && filterHelper(row,'lock-number','lock_no_search')
        && filterHelper(row,'lock-price','lock_price_search')
        && filterHelper(row,'month','month_search')
        && filterHelper(row,'datetime','lease_date_search')
        && filterHelper(row,'remark','remark_search')
        && filterHelper(row,'auth','auth_search')
    );

    // Repopulate the table with filtered data
    populateTable(filteredData);
}

    </script>
</body>
</html>
