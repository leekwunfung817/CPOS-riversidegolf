<?php 

    session_start();
require_once './logger.php';
t_log('begin[searching_data_future_booking.php]');

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

$randomNumber = rand(1, 10);

if ($randomNumber == 3) {

$url = 'https://riversidegolf.com.hk/GolfBooking/download_report.php?S=1'; // Replace with your desired URL
$response = file_get_contents($url);

// } else {
//     echo "Skip download report";
}

// require_once '../tesing_stage_verification.php';


// Create connection
// $conn = new mysqli($servername, $username, $password, $dbname);

// // echo "2-";
// // Check connection
// if ($conn->connect_error) {
// echo "con err";
//     die("Connection failed: " . $conn->connect_error);
// }

// function check_buffer_count($conn,$data)
// {

//     $id = $data['id'];
//     $key1 = $data['booking_date'];
//     $begin_hour = (int) $data['begin_hour'];
//     $end_hour = (int) $data['end_hour'];
//     $p_selections = $data['p_selections'];

//     $buffer_count = 0;

//     for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
//         $hour_int = ((int) $cursor_hour);
//         $is_half_hour = $cursor_hour != $hour_int;
//         $half_hour_mark = ($is_half_hour ? ':30' : ':00');
//         $key2=$hour_int . $half_hour_mark;
//         foreach (json_decode($p_selections) as $key => $position) {
//             // echo $position.'<br>';
//           $buffer_count += 1;

//                  $key4=str_replace("position_", "", $position);
//                  $sql_1 = "
//                  INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
//                  VALUES 
//                  ('$key1','$key2','$key4','$id');
//                  ";
//                    try {
//                        // Execute the query
//                        if ($conn->query($sql_1) === TRUE) {
//                            // echo "Data inserted successfully!";
//                        } else {
//                        //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
//                        // echo "SQL error 222 $sql";
//                        }
//                    } catch (Exception $e) {
//                        // echo $e;
//                        // echo "Exception 222 $sql_1";
//                    }


//                  $sql_1 = "
//                  UPDATE `golf_booking_buffer` SET `src`='$id'
//                  WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';
//                  ";
//                    try {
//                        // Execute the query
//                        if ($conn->query($sql_1) === TRUE) {
//                            // echo "Data inserted successfully!";
//                        } else {
//                        //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
//                        // echo "SQL error 222 $sql";
//                        }
//                    } catch (Exception $e) {
//                        // echo $e;
//                        // echo "Exception 222 $sql_1";
//                    }





//         }
//     }
//     return $buffer_count;

// }

// $sql = "
// SELECT * FROM `golf_fairway_booking`;
// ";

// $result = $conn->query($sql);
// if ($result->num_rows > 0) {
//   while ($row = $result->fetch_assoc()) {
//     $count = check_buffer_count($conn,$row);
//     // echo $count;
//     // echo "<br>";
//     var_dump($row);
//     // echo "<br>";
//   }
// }

// $conn->close();



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
            <h1>過去3個月和未來預訂記錄的 搜尋引擎<br>Past 3 months and future reservation search engine</h1>
        </td>
    </tr>
    <tr>
        <td id="transfer_n_paycheck">
            <form action="./payment-page/payment-confirm.php" method="post" target="_blank">
                <input type="hidden" name="decision" value="ACCEPT">
                <input type="hidden" name="account_unpaid" value="T">
                <input type="hidden" id="req_reference_number" name="req_reference_number">
                
                <table>
                    <tr>
                        <th colspan="2">銀行或支票轉帳</th>
                    </tr>
                    <tr>
                        <td>
                            預約編號
                        </td>
                        <td>
                            <input type="text" id="id_no" name="id" readonly>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            備註
                        </td>
                        <td>
                            <textarea id="myTextarea" name="remark" rows="5" cols="30"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="submit" value="提交" onclick="document.getElementById('transfer_n_paycheck').style.display = 'none';">
                        </td>
                    </tr>
                </table>
                
                
                <script>
                    function selectTextarea(id, req_reference_number) {

                        document.getElementById('req_reference_number').value = req_reference_number;
                        document.getElementById('id_no').value = id;

                        document.getElementById('transfer_n_paycheck').style.display = '';
                        alert('如需備註交易，請在文字區域輸入資訊後點選提交\nTo remark the transaction, please click submit after your enter information in the textarea ');
                        const textarea = document.getElementById('myTextarea');
                        textarea.focus();
                        textarea.select();

                    }
                    document.getElementById('transfer_n_paycheck').style.display = 'none';
                </script>
            </form>
        </td>
    </tr>
</table>

    <style>
table, th, td {
    border: 1px solid black;
/*    padding: 3px;*/
    text-align: center;
}
td {
    white-space:    nowrap  ;
    font-size: 0.9em;
}
th {
    white-space: nowrap;
    font-size: 0.7em;
}
.unpaid_button:hover {
    background-color: yellow;
    cursor: pointer;
}

.unpaid_button {
    color: red;
}


    </style>
    <p id="message_bar"></p>

數據將每 30 秒重新加載一次
Data will be reloaded every 30 seconds




    <form id="only_show_form">
        <table>
            <tr>
                <td>
                    <label>
                        <input type="checkbox" name="only_show_credit_card" onclick="SubmitOnlyShow()"> 僅顯示信用卡交易 <br> Show Credit Card Transaction only
                    </label>
                </td>
                <td>
                    <label>
                        <input type="checkbox" name="only_show_cash" onclick="SubmitOnlyShow()"> 僅顯示現金交易  <br> Show Cash Transaction only
                    </label>
                </td>
                <td>
                    <label>
                        <input type="checkbox" name="only_show_unpaid" onclick="SubmitOnlyShow()"> 僅顯示未付款記帳 <br> Show Accounted Unpaid only
                    </label>
                </td>
                <td>
                    <label>
                        <input type="checkbox" name="only_show_paid" onclick="SubmitOnlyShow()"> 僅顯示已付費記帳 <br> Show Accounted Paid only
                    </label>
                </td>
            </tr>
        </table>
    </form>

    <script type="text/javascript">
        function GetOnlyShowParams() {
            const form = document.getElementById('only_show_form');
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (const [name, value] of formData.entries()) {
                params.append(name, value);
            }

            var only_show_params = (params.toString().length>0 ? '&':'')+params.toString()+(params.toString().length>0 ? '&only_show=on':'');

            // console.log(only_show_params.length,only_show_params);
            return only_show_params;
        }
        function SubmitOnlyShow() {
            var only_show_params = GetOnlyShowParams();
            console.log(only_show_params.length,only_show_params);
            reflesh();
            // return only_show_params;
        }
    </script>










    <table>
        <tr>
            <th colspan="10">
                搜尋欄 Search bar
            </th>
        </tr>
        <tr>
            <th>預約號碼 <br>Reservation No.<br><input type="text" id="id_s" placeholder="ID" onkeyup="reflesh()" autocomplete="off"></th>
            <th>交易授權編號<br>Authorization Number<br><input type="text" id="auth_code" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            <th>信用卡號碼<br>Credit Card Number<br><input type="text" id="credit_card" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            <th>姓名<br> Name<br><input type="text" id="name" placeholder="Name" onkeyup="reflesh()" autocomplete="off"></th>
            <th>電郵<br> Email<br><input type="text" id="email" placeholder="Email" onkeyup="reflesh()" autocomplete="off"></th>
            <th>電話<br> Telephone<br><input type="text" id="telephone" placeholder="Telephone" onkeyup="reflesh()" autocomplete="off"></th>
            <th>八達通號碼 <br>Octopus No.<br><input type="text" id="octopus_no" placeholder="OctopusNo." onkeyup="reflesh()" autocomplete="off"></th>
            <th>預訂日期<br> Booking Date<br><input type="text" id="booking_date" placeholder="Booking Date" onkeyup="reflesh()" autocomplete="off"></th>
            <th>信用卡交易金額<br>Credit Card Transaction Amount<br><input type="text" id="transaction_amount" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            <th>現金交易金額<br>Cash Transaction Amount<br><input type="text" id="cash" placeholder="Cash" onkeyup="reflesh()" autocomplete="off"></th>


                        
        </tr>
    </table>

<br>

<script type="text/javascript">
    // function ( ) {
        
    // }
</script>


<table>
    <tr>
        <td>
            
<button style="width: 100px;height: 50px;" onclick="
    const input = document.getElementById('page_number');
    if ( parseInt(input.value) > 1 ) {
        input.value = parseInt(input.value) - 1;
    }
    reflesh();
">上一頁 <br> Previous Page</button>

        </td>
        <td>
<input style="width: 100px;height: 50px;" type="number" name="page_number" id="page_number" value="1">
        </td>
        <td>
<button style="width: 100px;height: 50px;" onclick="
    const input = document.getElementById('page_number');
    input.value = parseInt(input.value) + 1;
    reflesh();
">下一頁 <br> Next Page</button>
        </td>
    </tr>
</table>

<br>
<br>

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
            "id":"預約編號<br>Reservation<br>No.",
            "auth_code":"交易授權編號<br>Authoriz.<br>Number",
            "auth":"參考編號<br>Reference Number",
            "transaction_id":"銀行交易號碼<br>Transaction ID",
            "name":"姓名<br>Name",
            "email":"電郵<br>Email",
            "telephone":"電話<br>Telephone",
            "octopus_no":"八達通號碼<br>Octopus",
            "booking_date":"預訂日期<br>Booking Date",
            "begin_hour":"開始時間<br>Begin Hour", //  - 小時
            "end_hour":"結束時間<br>End Hour",
            "p_selections":"球道<br>Bay(s)",
            "payment_confirmed":"付款已確認",
            "carpark_checked_in":"已入停車場",
            "fairway_checked_in":"已簽入球道",
            "email_confirmation_status":"電子郵件確認狀態",
            "golf_payment_status":"付款狀態<br>Payment<br>Status",
            "booking_expired":"預訂過期",
            "Link":"二維碼單據 <br>QR Code payment receipt",
            "req_card_number":"信用卡號碼<br>Credit Card Number",
            "amount":"<b style=\"color: purple;\">信用卡<br>交易金額<br>Credit Card<br>Trans. Amount</b>",
            "cash":"<b style=\"color: purple;\">現金 交易金額<br>Cash Trans.<br>Amount</b>",
            "cash_timestamp":"現金交易日期<br>Cash Transaction Timestamp",
            "discount":"折扣 <br> Discount",
            "check_digit":"括號數<br>Bra.<br>No.",
            "remark":"備註<br>Remark",
            "resend_email":"重發電子郵件 <br>Resend Email",
            "is_paid":"<b style=\"color: purple;\">銀行或支票<br>轉帳<br>Bank<br>or<br>paycheck<br>transfer</b>",
            "locker_link":" 預訂置物櫃聯結<br>Locker reserv.<br>link",
            "golf_payment_datetime":"付款時間<br>Pay time",
            'is_synchronized':"同步狀態<br>syn. status",
            'card_type_name':'信用卡類型<br>credit card type',
            "pay_amount":'同步金額'
        }
        column_translation_func = {
            
            "req_card_number":function (data) {
                return '<small>'+data+'</small>';
            },
            "telephone":function (data) {
                return '<small>'+data+'</small>';
            },
            "email":function (data) {
                return '<small>'+data+'</small>';
            },
            "pay_amount":function (data) {
                return '<small>HKD $'+data+'</small>';
            },
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
            },
            "remark":function (data) {
                return '<pre>'+data+'</pre>'
            },
            "golf_payment_status":function (data) {
                var text = '';
                if (data=='T') {
                    text += '<b style="color: green;">';
                    text += "PAID";
                    text += '</b>';
                } else {
                    text += '<b style="color: red;">';
                    text += "CREATE";
                    text += '</b>';
                }
                return text;
            },
            "golf_payment_datetime":function (data) {
                var text = '';
                    text += '<b>';
                    text += '<u>';
                    text += data;
                    text += '</u>';
                    text += '</b>';
                return text;
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

async function fetchDataPure(url, callback) {
    try {
        const response = await fetch(url);
        if (response.ok) {
            callback();
        } else {
            console.error('Error fetching data:', response.status);
        }
    } catch (error) {
        console.error('An error occurred:', error);
    }
}

async function fetchData(url, callback) {
    try {
        const response = await fetch(url);
        if (response.ok) {
            // console.log(response);
            
            const response_body = await response.text();
            try {
                const json = JSON.parse(response_body);
                // Now you can use the 'json' variable containing your data
                // console.log(json);
                callback(json);
            } catch (error) {
                callback(response_body);
            }
        } else {
            console.error('Error fetching data:', response.status);
        }
    } catch (error) {
        console.error('An error occurred:', error);
    }
}

// Example usage:
const apiUrl = './booking-status-json-variable.php?future_booking'
+'&golf_booking_buffer'
; // Replace with your API endpoint
function reflesh() {

    const page_number = document.getElementById('page_number');
    var apiUrl_buf = apiUrl
        +'&page='+page_number.value
        +GetOnlyShowParams()
    ;

    var col_name;

    {
        var ele_ = document.getElementById('id_s');
        apiUrl_buf += '&search_'+'id'+'='+ele_.value;
    }

    // {
    //     var ele_ = document.getElementById('credit_card');
    //     apiUrl_buf += '&search_'+'req_card_number'+'='+ele_.value;
    // }



    // col_name = 'auth_code';
    // {
    //     var ele_ = document.getElementById(col_name);
    //     apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    // }

    col_name = 'name';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }

    col_name = 'email';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }

    col_name = 'telephone';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }

    col_name = 'octopus_no';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }

    col_name = 'booking_date';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }
    


    fetchData(apiUrl_buf, function (data) {
        console.log('Received Data');

        const tableBody = document.getElementById("message_bar");
        tableBody.innerHTML = "Data Received, just rendering data on this page.";


        
        jsonData = data;
        populateTable(jsonData);
        filterTable();

        tableBody.innerHTML = "";

    });


    fetchData('./clear_record.php?only_cybersource_api', function (data) {
        // console.log('clear_record',data);
    });
}
setInterval(reflesh, 30*1000); // Fetch data every half minute
reflesh();















document.getElementById("id_s").addEventListener("input", filterTable);
document.getElementById("credit_card").addEventListener("input", filterTable);

document.getElementById("transaction_amount").addEventListener("input", filterTable);
document.getElementById("cash").addEventListener("input", filterTable);

document.getElementById("auth_code").addEventListener("input", filterTable);

document.getElementById("name").addEventListener("input", filterTable);
document.getElementById("email").addEventListener("input", filterTable);
document.getElementById("telephone").addEventListener("input", filterTable);
document.getElementById("octopus_no").addEventListener("input", filterTable);
document.getElementById("booking_date").addEventListener("input", filterTable);

function filterHelper(row,data_key,html_id) {
    
    if (row[data_key] == null) {
        return '-'.toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
    }
    return row[data_key].toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
}

function filterTable() {
    const filteredData = jsonData.filter((row) =>
        filterHelper(row,'id','id_s')
        && filterHelper(row,'auth_code','auth_code')
        && filterHelper(row,'req_card_number','credit_card')
        && filterHelper(row,'amount','transaction_amount')
        && filterHelper(row,'cash','cash')
        && filterHelper(row,'auth_code','auth_code')
        && filterHelper(row,'name','name')
        && filterHelper(row,'email','email')
        && filterHelper(row,'telephone','telephone')
        && filterHelper(row,'octopus_no','octopus_no')
        && filterHelper(row,'booking_date','booking_date')
    );

    // Repopulate the table with filtered data
    populateTable(filteredData);
}

    </script>
</body>
</html>
<?php 

t_log('end[searching_data_future_booking.php]');
?>