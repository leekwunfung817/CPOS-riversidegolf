<?php 

    session_start();
if (!isset($_SESSION["management"])) {
     ?>
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當\nThe way you are using this website is inappropriate');
        window.location.href = "./";
    </script>
    <?php
    die();
}
 ?>
<?php 

require_once 'account_variable.php';

// echo "3-";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// echo "2-";
// Check connection
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


$sql = " 
    SELECT 
        `transaction-id`, 
        `transaction-number`, 
        `date`, 
        `remark`, 
        `amount`, 
        `discount`, 
        `cancel`, 
        `update-datetime`,
        (
            case 
            when `transaction-number` is null
                then ''
                else concat('<a href=\"?id-number=',`transaction-number`,'&print\">Print Receipt</a>')
            end
        ) `print`, 
        (
            case
            when `transaction-number` is null
                then ''
                else concat('<a href=\"?id-number=',`transaction-number`,'&clear\">Cancel Retails</a>')
            end
        ) `clear`
    FROM `golf-retails-transaction` 
    order by `update-datetime` desc;
";
$arr = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $arr[] = $row;
    }    
}

echo json_encode($arr,JSON_PRETTY_PRINT);
 ?>