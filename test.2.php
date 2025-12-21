<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


function hash_dict(array $dict, string $algo = 'sha256'): string {
    ksort($dict); // ensure deterministic order
    $json = json_encode($dict, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return hash($algo, $json);
}


require_once './account_variable.php';
require_once './common-function.php';

$conn_download_report = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn_download_report->connect_error) {
    die("Connection failed: " . $conn_download_report->connect_error);
}

function query_hash($sql) {
    global $conn_download_report;
    echo "<hr>";
    echo "=================================";
    echo "<hr>";
    $result = $conn_download_report->query($sql);
    if ($result->num_rows > 0) {
        $arr = array();
        while($row = $result->fetch_assoc()) {
            array_push($arr, $row);
        }
        echo hash_dict($arr)."";
    }
    echo "<hr>";
    echo "=================================";
    echo "<hr>";
    
}


    
require_once 'common-function.php';



$begin = '2025-12-18 15:58:09';
$end = '2025-12-20 16:29:59';

get_staff_cash_received($conn_download_report, $begin, $end);
$username = 'all';
get_staff_cash_received($conn_download_report, $begin, $end, $username, $username);
get_staff_cash_received($conn_download_report, $begin, $end, 'Unknown','Unknown');
$username = 'winnie';
get_staff_cash_received($conn_download_report, $begin, $end, $username, $username);

?>