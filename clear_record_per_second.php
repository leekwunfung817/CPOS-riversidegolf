<?php 


require_once 'logger.php';

t_log('begin[clear_record_per_second.php]');

require_once 'account_variable.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
echo "con err";
    die("Connection failed: " . $conn->connect_error);
}


{

$sql = "
UPDATE `T_BOOK` SET `pay_time`=(
    select ADDTIME(max(`golf_cybersource`.`signed_date_time`),'08:00:00.000000')
    from `golf_cybersource`
    where `golf_cybersource`.`req_reference_number`=`T_BOOK`.`qr_code`
)
WHERE `pay_time`='0000-00-00 00:00:00'
and (
    select max(`payment-datetime`) `payment-datetime`
    from `golf-payment-session`
    where `golf-payment-session`.`auth`=`T_BOOK`.`qr_code`
) is not null
and (
    select max(`golf_cybersource`.`signed_date_time`)
    from `golf_cybersource`
    where `golf_cybersource`.`req_reference_number`=`T_BOOK`.`qr_code`
) is not null;
";
$conn->query($sql);

}

{


$sql = "
UPDATE `T_BOOK` SET `pay_time`=(
    SELECT max(`timestamp`) 
    FROM `golf-cash` 
    WHERE `golf-cash`.`auth`=`T_BOOK`.`qr_code`
)
WHERE `pay_time`='0000-00-00 00:00:00'
and (
    select max(`payment-datetime`) `payment-datetime`
    from `golf-payment-session`
    where `golf-payment-session`.`auth`=`T_BOOK`.`qr_code`
) is not null
and (
    SELECT max(`timestamp`) 
    FROM `golf-cash` 
    WHERE `golf-cash`.`auth`=`T_BOOK`.`qr_code`
) is not null;
";
$conn->query($sql);


}

{


$sql = "DELETE FROM `golf_booking_buffer` WHERE `src`='' or `position`='' or `src` is null or `position` is null;";
$result = $conn->query($sql);

	
}


{

require_once './cybersource_api/search.php';

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


$conn->close();

}
t_log('end[clear_record_per_second.php]');
 ?>