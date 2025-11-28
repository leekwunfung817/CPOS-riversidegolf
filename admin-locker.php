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
<?php 

    require_once './account_variable.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


 ?>
<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
 ?>
<?php

// if (!isset($_GET["auth"])) {
//     die();
// }
?>
<?php


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $locker_id = $_POST['locker-id'];
    $due_date = $_POST['due-date'];
    $name = $_POST['name'];
    $telephone = $_POST['telephone'];
    $deposit = $_POST['deposit'];
    $amount = $_POST['amount'];
    $lock_number = $_POST['lock-number'];
    $lock_price = $_POST['lock-price'];
    $month = $_POST['month'];
    $deposit = $_POST['deposit'];
    // $datetime = $_POST['datetime'];
    $remark = $_POST['remark'];
    $auth = $_POST['auth'];



    $sql = " 
        SELECT `name`
        FROM `golf-locker-transaction`
        where `golf-locker-transaction`.`locker-id`='$locker_id'
        and `golf-locker-transaction`.`due-date`>current_timestamp()
        ;
    ";

// `golf-locker-transaction`.`locker-id`=`golf-locker-list`.`number`

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            if ($row['name']!=$_POST['name']) {
?>
<script type="text/javascript">
    alert('儲物櫃用戶重複或已經發生衝突，請選擇其他期間或其他儲物櫃 \n Locker user duplicated or has conflicted, please choose another period or other locker');
</script>
<?php
?>

<script type="text/javascript">
    window.location.href = "./searching_data_locker.php?locker_id=<?php echo $locker_id; ?>";
</script>
<?php
die();

            }
        }
    }

    $src = 'online';
    if (isset($_SESSION['name'])) {
        $src = $_SESSION['name'];
    }


    $sql = null;
    if (!isset($_POST['payment_option'])) {
        echo "No payment Option";
        die();
    }

    $sql = "
    INSERT INTO `golf-locker-transaction`(`locker-id`, `due-date`, `name`, `telephone`, `deposit`, `amount`, `lock-number`, `lock-price`, `month`, `datetime`, `remark`, `auth`, `src`) 
    select '$locker_id', '$due_date', '$name', '$telephone', '$deposit', '$amount', '$lock_number', '$lock_price', '$month', CURRENT_TIMESTAMP(), '$remark', '$auth', '$src'
    where '$due_date'>current_timestamp()
    ;
    ";
    
    file_put_contents('locker-transaction.sql', $sql);
?>
<?php
$is_not_pay = isset($_POST['payment_option']) && $_POST['payment_option']=='not_pay';
$inserted = false;

try {
    
    if ($conn->query($sql) === TRUE) {
        $inserted = true;
        ?>
        <script type="text/javascript">
            alert(' 儲物櫃記錄已儲存 \n Locker booking recorded ');
            window.location.href = "./searching_data_locker.php?number=<?php echo $locker_id; ?>&print";
        </script>
        <?php
    } else {
        if (!$is_not_pay) {
            echo "<!--  $conn->error   -->";
            ?>
            <script type="text/javascript">
                alert('(1) 儲物櫃用戶重複或已經發生衝突，請選擇其他期間或其他儲物櫃 \n Locker user duplicated or has conflicted, please choose another period or other locker');
            </script>
            <?php
        }
    }
} catch (Exception $e) {
    
    if (!$is_not_pay) {
?>
<script type="text/javascript">
    alert('(2) 儲物櫃用戶重複或已經發生衝突，請選擇其他期間或其他儲物櫃 \n Locker user duplicated or has conflicted, please choose another period or other locker');
</script>
<?php
        echo "<!--  $e  -->";
    }
}



    if (!$inserted && $is_not_pay) {

        $sql = "
        UPDATE `golf-locker-transaction` 
            SET  
                `due-date`='$due_date', 
                `name`='$name', 
                `telephone`='$telephone', 
                `deposit`='$deposit', 
                `amount`='$amount', 
                `lock-number`='$lock_number', 
                `lock-price`='$lock_price', 
                `month`='$month', 
                `datetime`=CURRENT_TIMESTAMP(), 
                `remark`='$remark', 
                `auth`='$auth', 
                `src`='$src'
        where `locker-id`='$locker_id'
        AND `month`='$month'
        ;
        ";

        if ($conn->query($sql) === TRUE) {
        ?>
        <script type="text/javascript">
            alert(' 儲物櫃記錄已更新 \n Locker booking updated ');
            window.location.href = "./searching_data_locker.php?number=<?php echo $locker_id; ?>&print";
        </script>
        <?php
        }
    }












die();
}

?>



<!DOCTYPE html>
<html>
<head>
    <title>Golf Locker Form</title>
    <style>
        html {
            background-color: grey;
        }
        body {
            font-family: Arial, sans-serif;
/*            background-color: #f4f4f4;*/
            background-color: #D4F9FA;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200vh;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
        }
        td {
            padding: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 4px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <form id="locker-submit-form" method="post" action="">
        <table>
            <tr>
                <td>

<h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>


                    <h1>
                        置物櫃登記表<br>Locker registration form
                    </h1>
                </td>
            </tr>
            <tr>
                <td><label for="locker-id">Locker ID:<br>置物櫃編號：</label></td>
                <td>
                    <select name="locker-id" id="locker-id" required>
<?php 

$sql = "
    SELECT 
        `number`, 
        `type` 
    FROM `golf-locker-list`
    order by `number` asc
    ;
";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
     ?><option value="<?php echo $row["number"]; ?>"><?php echo $row["number"].' ('.$row["type"].')'; ?></option><?php 
  }
}

 ?>
                    </select>
                    <script type="text/javascript">
                        document.getElementById("locker-id").value = "<?php 
                        if (isset($_GET['locker-number'])) {
                            echo $_GET['locker-number']; 
                        }
                         ?>";
                    </script>
                </td>
            </tr>
            <tr>
                <td><label for="name">Name:<br>姓名：</label></td>
                <td><input type="text" id="name" name="name" 
                    value="<?php if (isset($_GET['name'])) { echo $_GET['name']; } ?>"></td>
            </tr>
            <tr>
                <td><label for="telephone">Telephone:<br>電話：</label></td>
                <td><input type="text" id="telephone" name="telephone" 
                    value="<?php if (isset($_GET['telephone'])) { echo $_GET['telephone']; } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="deposit">Deposit:<br>按金：</label></td>
                <td><input type="number" step="0.01" id="deposit" name="deposit" 
                    value="<?php if (isset($_GET['deposit'])) { echo $_GET['deposit']; } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="amount">Amount:<br>價格：</label></td>
                <td><input type="number" step="0.01" id="amount" name="amount" 
                    value="<?php if (isset($_GET['amount'])) { echo $_GET['amount']; } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="lock-number">Lock Number:<br> 鎖號：</label></td>
                <td><input type="text" id="lock-number" name="lock-number" 
                    value="<?php if (isset($_GET['lock-number'])) { echo $_GET['lock-number']; } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="lock-price">Lock Price:<br>鎖價：</label></td>
                <td><input type="number" step="0.01" id="lock-price" name="lock-price" 
                    value="<?php if (isset($_GET['lock-price'])) { echo $_GET['lock-price']; } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="month">Start Month:<br>月份：</label></td>
                <td><input type="number" min="1" min="12" id="month" name="month" 
                    value="<?php 
                    if (isset($_GET['month'])) { 
                        echo $_GET['month']; 
                    } ?>"
                ></td>
            </tr>
            <tr>
                <td><label for="datetime">Lease date:<br>租賃日期：</label></td>
                <td>
                    根據提交時間決定 <br>
                    Depends on submission time
                    <!-- <input type="datetime-local" id="datetime" name="datetime"
                    value="<?php 
                    if (isset($_GET['datetime'])) { 
                        echo $_GET['datetime'];
                    } ?>"
                > -->
<script type="text/javascript">
function toLocalISOString(date) {
    const localDate = new Date(date - date.getTimezoneOffset() * 60000); //offset in milliseconds. Credit https://stackoverflow.com/questions/10830357/javascript-toisostring-ignores-timezone-offset

    // Optionally remove second/millisecond if needed
    localDate.setSeconds(null);
    localDate.setMilliseconds(null);
    return localDate.toISOString().slice(0, -1);
}

window.addEventListener("load", () => {
    if (document.getElementById("datetime").value=='') {
        document.getElementById("datetime").value = toLocalISOString(new Date());
    } else {
        console.log('Origin datetime',document.getElementById("datetime").value );
    }
});

</script>
                </td>
            </tr>
            <tr>
                <td><label for="due-date">Due date:<br>過期日：</label></td>
                <td><input type="date" id="due-date" name="due-date" 
                    value="<?php 
                    if (isset($_GET['due-date'])) { 
                        echo $_GET['due-date'];
                    } ?>"
                    required></td>
            </tr>
            <tr>
                <td><label for="remark">Remark:<br>備註：</label></td>
                <td><textarea id="remark" name="remark" style="width: 30em;height: 20em;"></textarea></td>
            </tr>
            <?php 
            if (
                (
                    isset($_GET['payment_option']) 
                    && $_GET['payment_option']=='pay'
                ) 
                || !isset($_GET['payment_option'])
            ) {
             ?>
            <tr>
                <td> <b> With Payment <br> 付款 </b> </td>
                <td><input type="radio" name="payment_option" value="pay" 
                    <?php 
                    if (
                        (
                            isset($_GET['payment_option']) 
                            && $_GET['payment_option']=='pay'
                        ) || !isset($_GET['payment_option']) ) {
                        echo "checked";
                    }
                     ?>
                    ></td>
            </tr>
            <?php 
            } else if (
                (
                    isset($_GET['payment_option']) 
                    && $_GET['payment_option']=='not_pay'
                ) || !isset($_GET['payment_option']) ) {
             ?>
            <tr>
                <td> <p> Only update <br> 僅更新記錄 </p> </td>
                <td><input type="radio" name="payment_option" value="not_pay"
                    <?php 
                    if ((isset($_GET['payment_option']) && $_GET['payment_option']=='not_pay')) {
                        echo "checked";
                    }
                     ?>
                    ></td>
            </tr>
            <?php 
            }
             ?>
            <tr>
                <td></td>
                <td colspan="2">
                    <button style="width: 100%;" >
                        Submit
                    </button>
                </td>
            </tr>
            <tr>
                <td><label for="auth">Auth:<br>認證號碼:</label></td>
                <td><input type="text" id="auth" name="auth" value="<?php if (isset($_GET['auth'])) {
                    echo $_GET['auth'];
                } ?>" readonly></td>
            </tr>
        </table>
    </form>
</body>
</html>






<?php 

    $conn->close();
    die();

 ?>