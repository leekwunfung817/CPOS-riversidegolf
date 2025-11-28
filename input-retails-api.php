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
            height: 100vh;
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
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>





<?php
// Database connection

require 'account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



if ( $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['method']) && $_POST['method']=='submit' ) {
    
    // Construct the SQL query
    $sql = "
INSERT INTO `golf-retails-transaction`(
    `transaction-number`, 
    `remark`, 
    `amount`, 
    `discount`
) VALUES (
    '".$_POST['id-number']."',
    '".$_POST['remark']."',
    '".$_POST['amount']."',
    '".$_POST['discount']."'
);";
    try {
        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // echo "Data inserted successfully!";
             ?>
            <script type="text/javascript">
                alert('零售已記錄, Retail Recorded');
            </script>
            <?php
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
             ?>
            <script type="text/javascript">
                alert('零售失敗 Retail Failed');
            </script>
            <?php
        }
    } catch (Exception $e) {
        echo "Error: " . $sql . "<br>" . $e;
         ?>
        <script type="text/javascript">
            alert('零售失敗 Retail Failed');
        </script>
        <?php
    }


    $sql = "UPDATE `golf-retails-transaction` SET `src`='".$_SESSION['name']."' WHERE `transaction-number`='".$_POST['id-number']."';";
    try {
        // Execute the query
        if ($conn->query($sql) === TRUE) {
        }
    } catch (Exception $e) {
    }

     ?>
    <script type="text/javascript">
        window.location.href = "./search-data-input-retails.php?id-number=<?php echo $_POST['id-number']; ?>&print";
    </script>
    <?php

} else if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
    ?>
    <form action="" method="post">

        

        <table>
            <tr>
                <td>
                    <h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
                </td>
            </tr>
            <tr>
                <td>
                </td>
            </tr>
            <tr>
                <td>
                    <?php 

    $quantities = $_POST['quantity'];
    
    $total_price = 0;

    $text_remark = '';

    foreach ($quantities as $retail_id => $quantity) {
        if ($quantity > 0) {
            $sql = "SELECT `discount-price`, `name` FROM `golf-retails-item` WHERE `retail-id` = '$retail_id'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $name = $row["name"];
                $discount_price = $row["discount-price"];
                $iternated_price = $discount_price * $quantity;
                $total_price += $iternated_price;
                $text_remark .= "$name ($retail_id) -> $ $discount_price x $quantity = $ $iternated_price - ";
                $text_remark .= "Accumulated ($ $total_price) \n";
            }
        }
    }





    // Get the current date and time in the format YYYYMMDDHHIISS
    $currentDateTime = date('YmdHis');

    // Concatenate "C" with the formatted date and time
    $formattedString = 'C' . $currentDateTime;

    // echo $formattedString."<br>";

    $discount = $_POST['discount'];

    // echo "Discount:".$discount;



                     ?>
                    <?php 
                        $text_remark .= "Total Price: $" . $total_price."\n";
                     ?>
                     <input type="hidden" name="method" value="submit">
                </td>
            </tr>
            <tr>
                <td>
                    號碼 ID Number
                </td>
                <td>
                    <input type="text" name="id-number" value="<?php echo $formattedString; ?>" readonly><br>
                </td>
            </tr>
            <tr>
                <td>
                    金額 Amount - $
                </td>
                <td>
                    <input type="text" name="amount" value="<?php echo $total_price; ?>" readonly>
                </td>
            </tr>
            <tr>
                <td>
                    員工折扣 Staff Discount:
                </td>
                <td>
                    <input type="text" name="discount" value="<?php 

if ($discount=='on') {
    echo "Y";
} else {
    echo "N";
}

 ?>" readonly>
                </td>
            </tr>
            <tr>
                <td>
                    備註 Remark:
                </td>
                <td>
                    <textarea name="remark" style="width: 500px;height: 200px;"><?php echo $text_remark; ?></textarea>
                </td>
            </tr>
        </table>
        <input type="submit" name="" value="Submit">
    </form>

    <?php 


}


$conn->close();

 ?>