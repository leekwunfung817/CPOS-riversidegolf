<?php

session_start();

require_once 'logger.php';
t_log('begin[Report_Monthly.php]');



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





ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
</head>
<body>

    <h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
    <h1>
        月度報告
Monthly Report
    </h1>

<?php
// Specify the desired month and year
$year = date('Y'); // Current year
$month = date('m'); // Current month

// First day of the month
$firstDate = new DateTime("$year-$month-01");

// Last day of the month
$lastDate = new DateTime("$year-$month-01");
$lastDate->modify('last day of this month');

?>

<?php
date_default_timezone_set('Asia/Hong_Kong');

// Start from the current month
$start_iternation = new DateTime();
// $end = (new DateTime())->sub(new DateInterval('P1Y')); // 10 years ago
$end_iternation = (new DateTime())->sub(new DateInterval('P1Y')); // 10 years ago

    // echo $start->format('Y-m') . "<br>"; // Output in YYYY-MM format

echo "First month: " . $start_iternation->format('Y-m') . "<br>";
echo "Last month: " . $end_iternation->format('Y-m');



?>



<?php 








    require_once './account_variable.php';
    require_once './common-function.php';

    $conn_download_report = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn_download_report->connect_error) {
        die("Connection failed: " . $conn_download_report->connect_error);
    }

 ?>




<table style="width:80%;">
    <tr>
<?php 
    // Loop backward month by month
    $output_count = 0;
    $column_index = 1;
    while ($start_iternation >= $end_iternation) {
        $start_iternation->sub(new DateInterval('P1M')); // Subtract one month
         ?>
        <td>
        <hr>
        <h1>
        <?php 
        echo $start_iternation->format('Y-m')."<br>"; // Output in YYYY-MM format
        ?>
        </h1>
        <?php

        $year = $start_iternation->format('Y'); // Current year
        $month = $start_iternation->format('m'); // Current month

        // First day of the month
        $firstDate = new DateTime("$year-$month-01");

        // Last day of the month
        $lastDate = new DateTime("$year-$month-01");
        $lastDate->modify('last day of this month');

        echo "First date: " . $firstDate->format('Y-m-d')."<br>";
        echo "Last date: " . $lastDate->format('Y-m-d')."<br>";

        $begin = $firstDate->format('Y-m-d');
        $end = $lastDate->format('Y-m-d');


        ?>
        <br>
        <b>Credit Card Transaction</b>
        <br>
        <?php

        $sql = "
        SELECT count(*) cre_c,  ifnull(sum(auth_amount),0) cre_sum
        FROM `golf_cybersource_right`
        where
            DATE_ADD(signed_date_time, INTERVAL 8 HOUR) between '$begin' and '$end'
        ;
        ";
        
        $cre_sum = 0;
        $cas_sum = 0;

        $result = $conn_download_report->query($sql);
        // echo $column_index;
        while ($row = $result->fetch_assoc()) {
            echo "Credit/Debit Card Revenue: $".((int)$row['cre_sum'])."<br>";
            echo "Transaction Count: ".$row['cre_c']."<br>";
            // var_dump($row);
            $cre_sum = $row['cre_sum'];
        }

        ?>
        <br>
        <b>Cash Transaction</b>
        <br>
        <?php
        $sql = "
        SELECT 
            (cas_c1 + IFNULL(unpaid_count,0) ) cas_c,
            (cas_sum1 + IFNULL(unpaid_sum,0) ) cas_sum
        from (
            SELECT 
                count(*) cas_c1, 
                ifnull(sum(amount),0) cas_sum1 
            FROM `golf-cash`
            left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-cash`.`auth`
            where 1=1
            and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
        ) t1, (
            select 
                count(*) unpaid_count
                ,sum(
                    case 
                        when IFNULL((`golf-unpaid-account`.`is_paid`),-1)<=0
                            then 0
                        when IFNULL((`golf-unpaid-account`.`is_paid`),-1)=1
                            then IFNULL((`golf-unpaid-account`.`amount`),0)
                        else 0
                    end
                ) unpaid_sum 
            from `golf-unpaid-account`
                left join `golf-payment-session` on `golf-payment-session`.`auth`=`golf-unpaid-account`.`auth`
            where 1=1
                and `golf-unpaid-account`.`is_paid` is not null
                and `golf-unpaid-account`.`is_paid`=1
                and `golf-payment-session`.`payment-datetime` between '$begin' and '$end'
        ) t2
        ";

        $result = $conn_download_report->query($sql);
        while ($row = $result->fetch_assoc()) {
            // var_dump($row);
            echo "Cash Revenue: $".$row['cas_sum']."<br>";
            echo "Transaction Count: ".$row['cas_c']."<br>";
            $cas_sum = $row['cas_sum'];
        }
         ?>
        <br>
        <b>Total Revenue: $<?php echo ($cre_sum+$cas_sum); ?></b>
        <hr>
        </td>
        <?php 

        // if ($output_count >= 10) {
        //     break;
        // }
        if ($column_index >= 3) {
            $column_index = 0;
     ?>
    </tr>
    <tr>
    <?php 
        }
        $column_index += 1;
        $output_count += 1;
    }
 ?>
    </tr>
</table>





</body>
</html>
<?php 


t_log('end[Report_Monthly.php]');

 ?>