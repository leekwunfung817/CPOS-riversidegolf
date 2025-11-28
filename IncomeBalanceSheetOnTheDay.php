<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();

if (!isset($_SESSION["management"]) && !isset($_GET['S'])) {
     ?>
  <meta charset="UTF-8">
    <script type="text/javascript">
        alert('您不是管理人員，您無法存取此頁面\nyou are not management, you cannot access this page');
        window.location.href = "./";
    </script>
    <?php
    die();
}





// Connect to the database
// $servername = "localhost";
// $username = "your_username";
// $password = "your_password";
// $dbname = "your_database";

require_once './account_variable.php';
require_once './common-function.php';

$conn_download_report = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_download_report->connect_error) {
    die("Connection failed: " . $conn_download_report->connect_error);
}



 ?>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>

<style type="text/css">
    html {
        padding: 30px;
/*        background: lightskyblue;*/
    }
    
    body {
        padding: 30px;
        background-color: white;
    }
    th {
/*        font-size: 0.5em;*/
        border-style: solid;
    }
    body,th,td {
/*        font-size: 2em;*/
        text-align: left;
        vertical-align: text-top;
        white-space: nowrap;
    }
    td {
        width: 50%;
        border-style: double;
    }
    table {
        width: 100%;
    }
</style>

<h1>

	預約日期當天 收入統算表
	<br>
	Income balance sheet on the day
</h1>

<table>
<tr>
    <th>
        日期<br>Date
    </th>
    <th>
        預訂數<br>
        Booking Count
    </th>
    <th>
        信用卡總收入
        <br>
        Total Credit Card Income
    </th>
    <th>
        現金收入總額
        <br>
        Total Cash Income
    </th>
    <th>
        未付總額（銀行交易/支付支票）<br>
        Total Unpaid (Bank Transaction/Paycheck)
    </th>
    <th>
        支付總額（銀行交易/支付支票）
        <br>
        Total Paid (Bank Transaction/Paycheck)
    </th>
    <th>
        同步金額<br>
        Synchronized Amount
    </th>
    <th>
        收入總額<br>
        Total Revenue
    </th>
</tr>

<?php 

// require_once './booking-status-json-variable.php';

    $sql = "

SELECT 
        `booking_date`
        ,count(*) booking_count
        ,sum(IFNULL(credit_card_amount,0)) `total_credit_card`
        ,sum(IFNULL(pay_amount,0)) total_pay_amount_syn
        ,sum(
            case 
                when IFNULL(`is_paid`,-1)<=0
                  then IFNULL(`unpaid_amount`,0)
                when IFNULL(`is_paid`,-1)=1
                  then 0
            end
        ) `total_unpaid_unpaid` 

        ,sum(
            case 
                when IFNULL(`is_paid`,-1)<=0
                  then 0
                when IFNULL(`is_paid`,-1)=1
                  then IFNULL(`unpaid_amount`,0)
            end
        ) `total_paid_unpaid` 
        ,sum(IFNULL(cash_amount,0)) `total_cash`
        ,sum(IFNULL(pay_amount,0)) `total_sum`
    FROM `golf_fairway_booking`
    left join (
        select 
        (IFNULL(max(`golf_cybersource_right`.auth_amount),0)) `credit_card_amount`
        ,`golf_cybersource_right`.`req_reference_number`
        from `golf_cybersource_right` 
        where 1=1
        and `decision`='ACCEPT' 
        and `req_transaction_type`='sale'
        group by `golf_cybersource_right`.`req_reference_number`
    ) `golf_cybersource_` on `golf_cybersource_`.`req_reference_number`=`golf_fairway_booking`.`auth`
    left join (
        select 
            IFNULL(max(`T_BOOK`.`pay_amount`),0) pay_amount
            ,`T_BOOK`.`qr_code`
        from `T_BOOK` 
        group by `T_BOOK`.`qr_code`
    ) `T_BOOK_` on `T_BOOK_`.`qr_code`=`golf_fairway_booking`.`auth`
    left join (
        select 
            max(`golf-unpaid-account`.`is_paid`) `is_paid`
            ,max(`golf-unpaid-account`.`amount`) `unpaid_amount`
            ,`golf-unpaid-account`.`auth`
        from `golf-unpaid-account`
        where `golf-unpaid-account`.`is_paid` is not null
        and `golf-unpaid-account`.`is_paid`=1
        group by `golf-unpaid-account`.`auth`
    ) `golf-unpaid-account_` on `golf-unpaid-account_`.`auth`=`golf_fairway_booking`.`auth`
    left join (
        select
            IFNULL(max(`golf-cash`.`amount`),0) `cash_amount`
            ,`golf-cash`.`auth`
        from `golf-cash`
        group by `golf-cash`.`auth`
    ) `golf-cash_` on `golf-cash_`.`auth`=`golf_fairway_booking`.`auth`
    GROUP BY `booking_date`
        ORDER BY `booking_date` DESC;
    ;
    ";

    $result = $conn_download_report->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
?>
<tr>
    <td><?php echo $row['booking_date']; ?></td>
    <td><?php echo $row['booking_count']; ?></td>

    <td style="background-color: lightblue;">$<?php echo $row['total_credit_card']; ?></td>
    <td style="background-color: yellow;">$<?php echo $row['total_cash']; ?></td>
    <td style="background-color: pink;color: red;">$<?php echo $row['total_unpaid_unpaid']; ?></td>
    <td style="background-color: lightgreen;color: purple;">$<?php echo $row['total_paid_unpaid']; ?></td>
    <td>$<?php echo $row['total_pay_amount_syn']; ?></td>
    <td>$<?php echo ($row['total_credit_card']+$row['total_cash']
    // +$row['total_unpaid_unpaid']
    +$row['total_paid_unpaid']);
    if ($row['total_pay_amount_syn']!=($row['total_credit_card']+$row['total_cash']+$row['total_paid_unpaid'])) {
        echo "(".$row['total_pay_amount_syn']-($row['total_credit_card']+$row['total_cash']+$row['total_paid_unpaid']).")";
    }
     ?></td>
</tr>
<?php
        }
    }
 ?>

</table>
<?php 



$conn_download_report->close();


 ?>