<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'logger.php';

t_log('begin[clear_record.php]');

$randomNumber = rand(1, 10);

if (isset($_GET['debug'])) {
    echo "Random Number: $randomNumber";
}
$part_start = microtime(true);


// 1003
// 927

// 1800
// 9 bay

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part A Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);




require_once 'account_variable.php';

$conn_1 = new mysqli($servername, $username, $password, $dbname);
if ($conn_1->connect_error) {
echo "con err";
    die("Connection failed: " . $conn_1->connect_error);
}

$conn = $conn_1;


$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part B Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);
































require_once './cybersource_api/search.php';




$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part A 1 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


// if ($randomNumber == 1) {

$check_quantity = "6";


if (isset($_GET['only_cybersource_api'])) {
    $check_quantity = "3";
}

if (isset($_GET['only_cybersource_api'])) {
    die();
}

echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";
echo "<hr>";



$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part A 2 Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);




$sql = "
SELECT * FROM `golf_fairway_booking_history`
where length(name)>1
order by id desc
limit $check_quantity
;
";

$result = $conn_1->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $booking_id = $row['id'];
    top_up_cybersource_by_id($conn_1,$booking_id);
    // echo $count;
    // echo "<br>";
    // var_dump($row);
    // echo "<br>";
  }
}



























$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part C Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


// if ($randomNumber == 2) {


// }

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part D Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);







$last_asc_id = file_get_contents('last_asc_id.txt');
echo "Start with ID $last_asc_id";

$order = 'asc';
$sql = "SELECT `id`,`auth`,`timestamp` FROM `golf_fairway_booking_history` where (select count(*) from golf_cybersource where `req_reference_number`=`auth`)=0 "
." and `id`>".$last_asc_id
// ." and `id`<".file_get_contents('last_desc_id.txt')
." order by `timestamp` $order;";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $auth=$row['auth'];
        echo "id:".$row['id'].'<br>';
        top_up_cybersource($conn, $auth);
        file_put_contents("last_"."$order"."_id.txt", "".$row['id']);
    }
}

$part_time_elapsed_secs = microtime(true) - $part_start;
if (isset($_GET['debug'])) {
  echo '(Part G Takes): '.$part_time_elapsed_secs.' ';
}
$part_start = microtime(true);


echo "To the end";
t_log('end[clear_record.php]');
die();





 ?>