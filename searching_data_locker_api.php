<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');




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

if (
    isset($_POST['update-due-date'])
    &&
    isset($_POST['locker-number'])
) {
    echo "update-due-date ".$_POST['update-due-date']." - ".$_POST['locker-number'];
    $sql = "
    UPDATE `golf-locker-list` 
    SET `due-date`='".$_POST['update-due-date']."' 
    WHERE `number`='".$_POST['locker-number']."'
    ;";
    try {
        if ($conn->query($sql) === TRUE) {
            echo "Update successfully";
        } else {
            echo "Update Failed";
        }
    } catch (Exception $e) {
        echo "Update Error";
        echo $sql;
    }
    die();
}

$sql = "
INSERT INTO `golf-locker-transaction-history`
select * from `golf-locker-transaction`
where `golf-locker-transaction`.`due-date`<CURRENT_DATE;
";

try {
    if ($conn->query($sql) === TRUE) {
        $sql = "
        DELETE FROM `golf-locker-transaction` WHERE `due-date`<CURRENT_DATE;
        ";
        try {
            if ($conn->query($sql) === TRUE) {
            } else {
            }
        } catch (Exception $e) {
        }
    } else {
    }
} catch (Exception $e) {
}

$sql = " 
    SELECT 
        `golf-locker-list`.`number` `number`, 
        `golf-locker-list`.`type`,
        (
            CASE 
                WHEN `a`.`due-date` > CURDATE() 
                THEN concat(`a`.`due-date`)
                WHEN `a`.`due-date` < CURDATE()
                THEN concat(`a`.`due-date`,' ( 逾期 Overdue )')
                ELSE `a`.`due-date`
            END
        ) `due-date`, 

        concat(
            '<form method=\"get\" action=\"./admin-locker.php\">'
            
            ,'<input type=\"hidden\" name=\"name\" value=\"'
            ,`a`.`name`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"telephone\" value=\"'
            ,`a`.`telephone`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"deposit\" value=\"'
            ,`a`.`deposit`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"amount\" value=\"'
            ,`a`.`amount`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"lock-number\" value=\"'
            ,`a`.`lock-number`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"locker-number\" value=\"'
            ,`golf-locker-list`.`number`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"lock-price\" value=\"'
            ,`a`.`lock-price`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"month\" value=\"'
            ,DATE_FORMAT(CURDATE(), '%m')
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"datetime\" value=\"'
            ,DATE_FORMAT(CURRENT_TIMESTAMP(), '%Y-%m-%d %H:%i:%S')
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"auth\" value=\"'
            ,`a`.`auth`
            ,'\">'
            
            ,'<input type=\"hidden\" name=\"due-date\" value=\"'
            ,(
                case MONTH(CURDATE())
                    when 12
                    then DATE_FORMAT(DATE_ADD(CURDATE(), INTERVAL 1 YEAR), '%Y-12-31')
                    else DATE_FORMAT(CURDATE(), '%Y-12-31')
                end
            )
            ,'\">'

            ,'  '

            ,(
                case
                when `a`.`due-date` is null or `a`.`record`='history'
                    then concat('<input type=\"hidden\" name=\"payment_option\" value=\"pay\">')
                    else concat('<input type=\"hidden\" name=\"payment_option\" value=\"not_pay\">')
                end
            )
            
            ,concat('<button>',(
                case
                when `a`.`due-date` is null or `a`.`record`='history'
                    then '再次付款 Pay again'
                    else '僅更新 Update only'
                end
            ),'</button>')

            
            ,'</form>'
        ) `Update Button`,
        
        `a`.`name`, 
        `a`.`telephone`, 
        `a`.`deposit`, 
        `a`.`amount`, 
        `a`.`lock-number`, 
        `a`.`lock-price`, 
        `a`.`month`, 
        `a`.`datetime`, 
        `a`.`remark`, 
        `a`.`auth`,
        (
            case 
            when `a`.`due-date` is null
                then ''
                else concat('<a href=\"?number=',`golf-locker-list`.`number`,'&print\">Print Receipt</a>')
            end
        ) `print`, 
        (
            case
            when `a`.`due-date` is null or `a`.`record`='history'
                then 'Cleared'
                else concat('<a href=\"?number=',`golf-locker-list`.`number`,'&clear\">Clear Locker</a>')
            end
        ) `clear`, 
        `a`.`src`
    FROM `golf-locker-list` 
    left join 
    (
        SELECT *,'normal' `record` FROM `golf-locker-transaction`
        UNION ALL 
        SELECT *,'history' `record` FROM `golf-locker-transaction-history`
    )
    `a` 
        on `a`.`locker-id`=`golf-locker-list`.`number`
        and 
        `a`.`record`=(
            case
            when (
                select count(*)
                from `golf-locker-transaction`
                where `golf-locker-transaction`.`locker-id`=`golf-locker-list`.`number`
            )>0
            then 'normal'
            else 'history'
            end
        )
        and 
        `a`.`datetime`=(
            case
            when (
                select count(*)
                from `golf-locker-transaction`
                where `golf-locker-transaction`.`locker-id`=`golf-locker-list`.`number`
            )>0
            then (
                select max(`golf-locker-transaction`.`datetime`)
                from `golf-locker-transaction`
                where `golf-locker-transaction`.`locker-id`=`a`.`locker-id`
            )
            else (
                select max(`golf-locker-transaction-history`.`datetime`)
                from `golf-locker-transaction-history`
                where `golf-locker-transaction-history`.`locker-id`=`a`.`locker-id`
            )
            end
        )
    group by `golf-locker-list`.`number`
    order by `golf-locker-list`.`number` asc;
";


            // ,
            // (
            //     CASE 
            //         WHEN `a`.`due-date` > CURDATE() 
            //         THEN concat('<a style=\"color: green;\">',`a`.`due-date`,'</a>')
            //         WHEN `a`.`due-date` < CURDATE()
            //         THEN concat('<a style=\"color: red;\">',`a`.`due-date`,'</a>')
            //         ELSE `a`.`due-date`
            //     END
            // )

$arr = array();

$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $arr[] = $row;
    }    
}

echo json_encode($arr,JSON_PRETTY_PRINT);

$conn->close();
 ?>