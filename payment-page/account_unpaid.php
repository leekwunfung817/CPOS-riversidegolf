<?php 

require_once '../logger.php';

m_log("reach account-unpaid.php ".$_POST);

require_once '../account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (isset($_POST['req_reference_number'])&&isset($_POST['amount'])&&isset($_POST['remark'])) {
    $sql = "INSERT INTO `golf-unpaid-account`(`auth`, `amount`, `multiplied`, `extra`, `remark`) VALUES ('".$_POST['req_reference_number']."', '".$_POST['amount']."', '".$_POST['percentage']."', '".$_POST['addition']."', '".$_POST['remark']."');";
    try {
        
        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // echo "Data inserted successfully!";
?>
    <script type="text/javascript">
        alert('請記得稍後收款並備註付款資訊 \n Please remember to receive payment and remark the payment information later');
        setTimeout(function() {
            window.location.href = "../";
        }, 1000);
        
    </script>
<?php

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

    } catch (Exception $e) {
        echo $sql;
    }
}

// Close the connection
$conn->close();

 ?>