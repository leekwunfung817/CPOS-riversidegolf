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
            <h1>零售記錄 搜尋引擎<br>Retail Record Search Engine</h1>
            <a href="./input-retails.php">登記零售 Register Retails</a>
        </td>
        <td>
            
<iframe id="receipt_printing_buffer" style="width: 300px;height: 400px;">
</iframe>
<script type="text/javascript">


function comfirm_and_print(
  transaction_id,
  transaction_number,
  date,
  discount,
  amount,
  // remark,
  update_datetime
) {

  var msg = '';
  var printing = '<h1>白石高球練習場</h1>';
  printing += '<h1>零售記錄</h1>';
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

  sourceTxt = transaction_id;
  sourceName = 'Transaction ID';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = transaction_number;
  sourceName = 'Transaction Number';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;font-size: 1.2em;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = date;
  sourceName = 'Date';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = discount;
  sourceName = 'Discount';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  sourceTxt = amount;
  sourceName = 'Amount';
  if (sourceTxt!=null && sourceTxt.length>0) {
    msg += sourceName+':'+sourceTxt+'\n';
    printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  }

  // sourceTxt = remark;
  // sourceName = 'Remark';
  // if (sourceTxt!=null && sourceTxt.length>0) {
  //   msg += sourceName+':'+sourceTxt+'\n';
  //   printing += '<b style="text-align: left;">'+sourceName+': '+sourceTxt+'</b><br>';
  // }

  sourceTxt = update_datetime;
  sourceName = 'Update Datetime';
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


<?php 


    require 'account_variable.php';
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

if ( isset($_GET['id-number']) && isset($_GET['clear']) ) {


    $sql = "
    DELETE FROM `golf-retails-transaction` where `transaction-number`='".$_GET['id-number']."';
    ";
    try {
        if ($conn->query($sql) === TRUE) {
        } else {
        }
    } catch (Exception $e) {
    }

}

if ( isset($_GET['id-number']) && isset($_GET['print']) ) {
    $sql = " 
        SELECT 
            `transaction-id`, 
            `transaction-number`, 
            `date`, 
            `remark`, 
            `amount`, 
            `discount`, 
            `cancel`, 
            `update-datetime` 
        FROM `golf-retails-transaction` 
        where `transaction-number`='".$_GET['id-number']."'
        order by `update-datetime` desc;
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

         ?>
        comfirm_and_print(
            '<?php echo $row['transaction-id']; ?>',
            '<?php echo $row['transaction-number']; ?>',
            '<?php echo $row['date']; ?>',
            '<?php echo $row['discount']; ?>',
            'HKD $ <?php echo $row['amount']; ?>',
            '<?php echo $row['update-datetime']; ?>'
        );
        <?php 

        }    
    }

}
    $conn->close();
 ?>

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
            <th>序列號 <br>ID<br><input type="text" id="transaction_id_search" placeholder="Transaction ID" autocomplete="off"></th>
            <th>編號 <br>No.<br><input type="text" id="transaction_number_search" placeholder="Transaction Number" autocomplete="off"></th>
            <th>日期<br>date<br><input type="text" id="date_search" placeholder="Date&time" autocomplete="off"></th>
            <th>備註 <br>Remark<br><input type="text" id="remark_search" placeholder="Remark" autocomplete="off"></th>
            <th>金額<br>Amount<br><input type="text" id="amount_search" placeholder="Amount" autocomplete="off"></th>
            <th>員工折扣<br>Staff Discount<br><input type="text" id="staff_discount_search" placeholder="Staff Discount" autocomplete="off"></th>
            <th>取消<br>Cancel<br><input type="text" id="cancel_search" placeholder="Cancel" autocomplete="off"></th>
            <th>更新日期<br>Update Date<br><input type="text" id="update_date_search" placeholder="Update Date" autocomplete="off"></th>

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
            "transaction-id":"序列號 ID",
            "transaction-number":"編號 No.",
            "number":"置物櫃<br>Locker",
            "id":"預約編號<br>Reservation No.",
            "cancel":"取消 Cancel",
            "update-datetime":"更新日期<br> Update Date",
            "auth_code":"交易授權<br>編號<br>Authorization Number",
            "auth":"參考編號<br>Reference Number",
            "transaction_id":"銀行交易號碼<br>Transaction ID",
            "name":"姓名<br>Name",
            "email":"電郵<br>Email",
            "date":"日期 Date",
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
            "amount":"金額 Amount",
            "cash":"現金<br>交易金額<br>Cash Transaction Amount",
            "cash_timestamp":"現金交易日期<br>Cash Transaction Timestamp",
            "discount":"員工折扣 Staff Discount",
            "check_digit":"括號內數字<br>Brackets Number",
            "remark":"備註 Remark",
            "resend_email":"Resend Email",
            "is_paid":"銀行<br>或<br>支票<br>轉帳",

            "due-date":"過期日<br>Due date",
            "deposit":"按金<br>Deposit",
            "lock-number":"鎖號<br>Lock Number",
            "lock-price":"鎖價<br>Lock Price",

            "type":"儲物櫃類型<br>Locker Type",

            "month":"月份<br>Month"
        }
        column_translation_func = {
            // "discount":function (data) {
            //     if (data=='S') {
            //         return 'Student';
            //     } else if (data=='H') {
            //         return '-';
            //     } else if (data=='D') {
            //         return 'Disabled';
            //     }
            // },
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
            const json = await response.json();
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

// Example usage:
const apiUrl = './search-data-input-retails-api.php'; // Replace with your API endpoint
function reflesh() {
    fetchData(apiUrl, function (data) {
        jsonData = data;
        populateTable(jsonData);
        filterTable();
    });
}
setInterval(reflesh, 30*1000); // Fetch data every half minute
reflesh();















document.getElementById("transaction_id_search").addEventListener("input", filterTable);
document.getElementById("transaction_number_search").addEventListener("input", filterTable);

document.getElementById("date_search").addEventListener("input", filterTable);
document.getElementById("remark_search").addEventListener("input", filterTable);

document.getElementById("amount_search").addEventListener("input", filterTable);

document.getElementById("staff_discount_search").addEventListener("input", filterTable);
document.getElementById("cancel_search").addEventListener("input", filterTable);

document.getElementById("update_date_search").addEventListener("input", filterTable);

function filterHelper(row,data_key,html_id) {
    if (row[data_key] == null) {
        console.log(html_id);
        return '-'.toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
    }
    return row[data_key].toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
}

function filterTable() {
    const filteredData = jsonData.filter((row) =>
        filterHelper(row,'transaction-id','transaction_id_search')
        && filterHelper(row,'transaction-number','transaction_number_search')
        && filterHelper(row,'date','date_search')
        && filterHelper(row,'remark','remark_search')
        && filterHelper(row,'amount','amount_search')
        && filterHelper(row,'discount','staff_discount_search')
        && filterHelper(row,'cancel','cancel_search')
        && filterHelper(row,'update-datetime','update_date_search')
    );


    // Repopulate the table with filtered data
    populateTable(filteredData);
}

    </script>
</body>
</html>
