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
$end_iternation = (new DateTime())->sub(new DateInterval(
    'P6M'
    // 'P1Y'
));

    // echo $start->format('Y-m') . "<br>"; // Output in YYYY-MM format

echo "First month: " . $start_iternation->format('Y-m') . "<br>";
echo "Last month: " . $end_iternation->format('Y-m');



?>


<h2 style="color: red;">
    如果網頁伺服器繁忙，請勿下載每月報告，否則整個系統將因您的匯出操作而暫停，並在匯出完成後繼續運作。
    <br>
    If the web server is busy, do not download the monthly report; otherwise, the entire system will be paused due to your export operation and will resume operation after the export is completed.
</h2>
<?php 








    require_once './account_variable.php';
    require_once './common-function.php';

    $conn_download_report = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn_download_report->connect_error) {
        die("Connection failed: " . $conn_download_report->connect_error);
    }

    require_once 'lib-statistic-core.php';

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
        $financial_dict = statistic_by_month($conn_download_report, $year, $month);

        echo "First date: " . $financial_dict['begin']."<br>";
        echo "Last date: " . $financial_dict['end']."<br>";
        ?>
        <br>
        <b>Credit Card Transaction</b>
        <br>
        <?php
        
        $cre_sum = 0;
        $cas_sum = 0;

        echo "Credit/Debit Card Revenue: $".$financial_dict['cre_sum']."<br>";
        echo "Transaction Count: ".$financial_dict['cre_c']."<br>";
        $cre_sum = $financial_dict['cre_sum'];

        ?>
        <form action="./download_report.php" method="get" style="height: 0px;" target="_blank">
            <input type="hidden" name="type" value="credit_card">
            <input type="hidden" name="begin" value="<?php echo $financial_dict['begin']; ?>">
            <input type="hidden" name="end" value="<?php echo $financial_dict['end']; ?>">
            <input type="submit" value="Download">
        </form>
        <br>
        <br>
        <br>
        <b>Cash Transaction</b>
        <br>
        <?php

        echo "Cash Revenue: $".$financial_dict['cas_sum']."<br>";
        echo "Transaction Count: ".$financial_dict['cas_c']."<br>";
        $cas_sum = $financial_dict['cas_sum'];

         ?>
        <form action="./download_report.php" method="get" style="height: 0px;" target="_blank">
            <input type="hidden" name="type" value="cash_card">
            <input type="hidden" name="begin" value="<?php echo $financial_dict['begin']; ?>">
            <input type="hidden" name="end" value="<?php echo $financial_dict['end']; ?>">
            <input type="submit" value="Download">    
        </form>
        <br>
        <br>
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