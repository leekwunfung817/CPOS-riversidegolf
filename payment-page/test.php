<?php
require_once '../account_variable.php';
$conn = new mysqli($servername, $username, $password, $dbname);

$sql = "SELECT MAX(CHAR_LENGTH(`T_BOOK`.`qr_code`)) MAX_ , MIN(CHAR_LENGTH(`T_BOOK`.`qr_code`)) MIN_ FROM `T_BOOK`;";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
	var_dump($row);
    $MAX_ = $row['MAX_'];
    $MIN_ = $row['MIN_'];
}





$sql = "SELECT `T_BOOK`.`qr_code` FROM `T_BOOK` WHERE CHAR_LENGTH(`T_BOOK`.`qr_code`)=$MAX_ limit 1;";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
	var_dump($row);
}



$sql = "SELECT `T_BOOK`.`qr_code` FROM `T_BOOK` WHERE CHAR_LENGTH(`T_BOOK`.`qr_code`)=$MIN_ limit 1;";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
	var_dump($row);
}


?>