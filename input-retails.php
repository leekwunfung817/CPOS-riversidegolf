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

require 'account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch retail items from the database
$sql = "SELECT `retail-id`, `name`, `discount-price`, `regular-price` FROM `golf-retails-item` where `enable`=1;";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retail Items</title>
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
    </style>
</head>
<body>


    

    <form action="input-retails-api.php" method="post">
        <table>
            <tr>
                <td>
                    <h3><a href="./configuration-administraion.php?auth=<?php echo $_SESSION["auth"]; ?>&datetime=<?php echo $_SESSION["datetime"]; ?>&email=<?php echo $_SESSION["email"]; ?>"> < Admin Page</a></h3>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h1>
                    Retail Form
                    </h1>
                </td>
            </tr>
            <tr>
                <th>Item</th>
                <th>Discount Price</th>
                <th>Regular Price</th>
                <th>Quantity</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["name"] . "</td>";
                    echo "<td>" . $row["discount-price"] . "</td>";
                    echo "<td>" . $row["regular-price"] . "</td>";
                    echo "<td><input type='number' name='quantity[" . $row["retail-id"] . "]' min='0' value='0'></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No items found</td></tr>";
            }
            ?>
        </table>
        <hr>
        Staff Discount: <input type="checkbox" name="discount">
        <hr>
        <br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>

<?php
$conn->close();
?>
