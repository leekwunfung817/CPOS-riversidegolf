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

<h1>歷史記錄和支付失敗記錄<br>History record and failed payment record</h1>

    <style>
table, th, td {
    border: 1px solid black;
}
td {
    white-space:    nowrap  ;
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
            <th>預約號碼 <br>Reservation No.<br><input type="text" id="id_s" placeholder="ID" onkeyup="reflesh()" autocomplete="off"></th>
            <th>交易授權編號<br>Authorization Number<br><input type="text" id="auth_code" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            <!-- <th>銀行交易號碼<br> Transaction ID<br><input type="text" id="transaction_id" placeholder="Transaction ID" onkeyup="reflesh()" autocomplete="off"></th> -->

            <th>信用卡號碼<br>Credit Card Number<br><input type="text" id="credit_card" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            
            <th>姓名<br> Name<br><input type="text" id="name" placeholder="Name" onkeyup="reflesh()" autocomplete="off"></th>
            <th>電郵<br> Email<br><input type="text" id="email" placeholder="Email" onkeyup="reflesh()" autocomplete="off"></th>
            <th>電話<br> Telephone<br><input type="text" id="telephone" placeholder="Telephone" onkeyup="reflesh()" autocomplete="off"></th>
            <th>八達通號碼 <br>Octopus No.<br><input type="text" id="octopus_no" placeholder="OctopusNo." onkeyup="reflesh()" autocomplete="off"></th>

            <th>預訂日期<br> Booking Date<br><input type="text" id="booking_date" placeholder="Booking Date" onkeyup="reflesh()" autocomplete="off"></th>

            <th>信用卡交易金額<br>Credit Card Transaction Amount<br><input type="text" id="transaction_amount" placeholder="Authorization Code" onkeyup="reflesh()" autocomplete="off"></th>
            <th>現金交易金額<br>Cash Transaction Amount<br><input type="text" id="cash" placeholder="Cash" onkeyup="reflesh()" autocomplete="off"></th>

            <th>參考編號 <br>Reference Number<br><input type="text" id="auth" placeholder="Auth" onkeyup="reflesh()" autocomplete="off"></th>



                        
        </tr>
    </table>

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
            "id":"預約編號<br>Reservation No.",
            "auth_code":"交易授權<br>編號<br>Authorization Number",
            "auth":"參考編號<br>Reference Number",
            "transaction_id":"銀行交易號碼<br>Transaction ID",
            "name":"姓名<br>Name",
            "email":"電郵<br>Email",
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
            "amount":"失敗交易金額<br>Failed Transaction Amount",
            "cash":"-",
            "cash_timestamp":"-",
            "discount":"折扣 <br> Discount",
            "check_digit":"括號內數字<br>Brackets Number",
            "remark":"備註<br>Remark",
            "resend_email":"Resend Email",
            "is_paid":"-",
            "locker_link":" 預訂置物櫃聯結<br>Locker reserved link",
            "golf_payment_datetime":"-"
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
// Example JSON data (replace with your actual data)
// const jsonData = [
//     { "Column1": "Value1", "Column2": "ValueA", "Column3": "DataX" },
//     { "Column1": "Value2", "Column2": "ValueB", "Column3": "DataY" },
//     // ... more data rows ...
// ];
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

// Call the function to populate the table
// populateTable(jsonData);


// html_result = null;
// async function fetchHtml(url, callback) {
//     try {
//         const response = await fetch(url);
//         if (!response.ok) {
//             throw new Error(`HTTP error! status: ${response.status}`);
//         }
//         const html = await response.text();
//         html_result = html;
//         // code_buffer = html;
//         console.log(html);
//         callback(html)
//         return html;
//     } catch (error) {
//         console.error('Error fetching HTML:', error);
//         return null; // Or handle the error differently
//     }
// }

async function fetchData(url, callback) {
    try {
        const response = await fetch(url);
        if (response.ok) {
            // console.log(response);
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

// Example usage:
const apiUrl = './booking-status-json-variable.php?future_booking&history_booking&paid_only'; // Replace with your API endpoint
function reflesh() {
    const page_number = document.getElementById('page_number');
    var apiUrl_buf = apiUrl
        +'&page='+page_number.value
    ;

    var col_name;

    {
        var ele_ = document.getElementById('id_s');
        apiUrl_buf += '&search_'+'id'+'='+ele_.value;
    }

    {
        var ele_ = document.getElementById('credit_card');
        apiUrl_buf += '&search_'+'req_card_number'+'='+ele_.value;
    }



    col_name = 'auth';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }
    
    col_name = 'auth_code';
    {
        var ele_ = document.getElementById(col_name);
        apiUrl_buf += '&search_'+col_name+'='+ele_.value;
    }

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
        jsonData = data;
        populateTable(jsonData);
        filterTable();
    });
}
setInterval(reflesh, 30*1000); // Fetch data every half minute
reflesh();















document.getElementById("id_s").addEventListener("input", filterTable);
// document.getElementById("auth").addEventListener("input", filterTable);
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
        // row[data_key] = '-';
        return '-'.toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
    //     // if (document.getElementById(html_id)==null) {
    //     //     return false;
    //     // }
    //     // if (document.getElementById(html_id).value==null) {
    //     //     return false;
    //     // } else {
    //     //     return true;
    //     // }
    //     return true;
        
    }
    // if (row[data_key] == '') {
    //     return false;
    // } else {
    return row[data_key].toLowerCase().includes(document.getElementById(html_id).value.toLowerCase());
    // }
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

         // && row.auth.toLowerCase().includes(document.getElementById("auth").value.toLowerCase());
         // && (row.req_card_number==null?false:row.req_card_number)
         // .toLowerCase().includes(document.getElementById("credit_card").value.toLowerCase());
         // && (row.amount==null?false:row.amount).toLowerCase().includes(document.getElementById("transaction_amount").value.toLowerCase());
         // && row.cash.toLowerCase().includes(document.getElementById("cash").value.toLowerCase());
         // && row.auth_code.toLowerCase().includes(document.getElementById("auth_code").value.toLowerCase());
         // && row.name.toLowerCase().includes(document.getElementById("name").value.toLowerCase());
         // && row.email.toLowerCase().includes(document.getElementById("email").value.toLowerCase());
         // && row.telephone.toLowerCase().includes(document.getElementById("telephone").value.toLowerCase());
         // && row.octopus_no.toLowerCase().includes(document.getElementById("octopus_no").value.toLowerCase());
         // && row.booking_date.toLowerCase().includes(document.getElementById("booking_date").value.toLowerCase());

        // return is_match;
    );

    // Repopulate the table with filtered data
    populateTable(filteredData);
}

    </script>
</body>
</html>
