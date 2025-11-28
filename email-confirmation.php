<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');


require_once './logger.php';

t_log('begin[email-confirmation.php]');

session_start();



function stop_by_invalid_booking_2($str)
{

    m_log("page exit stop_by_invalid_booking_2 ! $str");
     ?>
    <script type="text/javascript">
        alert('<?php echo $str; ?>');

        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    die();
}





$allGetParams = array_merge($_GET, $_POST);

// Capture GET parameters (adjust based on your approach)
$data = $allGetParams;


if (isset($allGetParams['email']) && isset($allGetParams['confirmation_code']) && isset($allGetParams['open_datetime']) && (count($allGetParams) === 3)) {

    $open_datetime = $_GET['open_datetime'];
    $email = $_GET['email'];
    $confirmation_code = $_GET['confirmation_code'];
    
    $confirmation_code_raw = md5($email.'_'.$open_datetime);
    function generateNumberFromText(string $text): string {
        $numbers = preg_replace('/[^0-9]/', '', $text); // Extract only numbers
        $number = substr($numbers, 0, 6); // Get first 6 digits
        return str_pad($number, 6, '0', STR_PAD_LEFT); // Pad with zeros to make 6 digits
    }
    $confirmation_code_confirm = generateNumberFromText($confirmation_code_raw);

    m_log("Varify Email Confirm Code $email _ $open_datetime > $confirmation_code > $confirmation_code_confirm");
    
    if (strlen($confirmation_code)>=6) {
        if ($confirmation_code == $confirmation_code_confirm) {
            echo "Y";
            $cookie_name = "email"."_rivergolf";
            setcookie($cookie_name, $allGetParams['email'], time() + (86400 * 90), "/");
        } else {
            echo "N ".$confirmation_code." ".$confirmation_code_confirm;
        }
        die();
    }

    // Set these parameters
    $subject = '白石高爾夫球練習場 - 確認您的電郵地址 | White Head Golf Driving Range - Confirm your email address'; // Subject of the email
    $emailadd = 'support@riversidegolf.com.hk'; // Your email address (where the form information will be sent)
    $url = 'thanks.php'; // Redirect URL after form processing
    $req = '0'; // Set to '1' to make all fields required, '0' to allow empty fields

    // Initialize variables
    $text = "親愛的高爾夫球場客戶，

    感謝您選擇白石高爾夫球練習場！ 

    $confirmation_code_confirm

    請在15分鐘內將6位數字確認碼複製到輸入方塊以驗證您的聯絡資訊。 
    感謝您對我們的高爾夫練習設施的興趣。 為了確保溝通順利，我們懇請您確認您的電子郵件地址。
    如果您有任何其他問題或需要進一步協助，請隨時聯絡我們的客戶服務團隊。
    我們期待很快歡迎您來到我們的高爾夫練習場！

    此致
    白石高爾夫球場 團隊

    ____________________________________________________________________________________________________________________


    Dear golf court customer,

    Thank you for choosing White Head Golf Driving Range! Thank you for your interest in our golf practice facilities. To ensure smooth communication, we kindly ask you to confirm your email address.
    
    $confirmation_code_confirm

    Please copy the 6-digit confirmation code to the input field within 15 minutes to verify your contact information. If you have any further questions or require further assistance, please feel free to contact our customer service team.


    We look forward to welcoming you to our driving range soon!

    Best Regards
    White Head Golf


    ";
    $space = ' ';
    $line = ' ';

    m_log("Send Email Confirm Code $email >>> $confirmation_code_confirm");
    // echo $confirmation_code;

    // email-confirmation
        // Send the email
        mail($email, $subject, $text, 'From: ' . $emailadd);
    echo "Email Sent";


    exit();
} else if (
    isset($allGetParams['email']) && 
    isset($allGetParams['name']) && 
    isset($allGetParams['telephone']) && 
    isset($allGetParams['booking_date']) && 
    isset($allGetParams['begin_hour']) && 
    isset($allGetParams['end_hour'])
) {

    {
        
        $expiry_time = time() + (365 * 24 * 60 * 60); // 1 year in seconds

        $cookie_name = "email";
        setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");

        $cookie_name = "name";
        setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");

        $cookie_name = "telephone";
        setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");

        $cookie_name = "octopus_no";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }
        
        $cookie_name = "octopus_no_q";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }

        $cookie_name = "octopus_no_cf";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }
        
        $cookie_name = "octopus_no_q_cf";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }

        $cookie_name = "school_name";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }
        
        $cookie_name = "disabled_id";
        if (isset($allGetParams[$cookie_name])) {
            setcookie("$cookie_name"."_rivergolf", $allGetParams[$cookie_name], $expiry_time, "/");
        }


        // '/' means the cookie is available in the entire domain
    }
    // echo "Good to go";
    // Good to go
} else {
    
    m_log("page exit 您使用本網站的方式不恰當，請逐步使用 The way you are using this website is inappropriate, please use it step by step");
     ?>
    <meta charset="utf-8">
    <script type="text/javascript">
        alert('您使用本網站的方式不恰當，請逐步使用\nThe way you are using this website is inappropriate, please use it step by step');
        alert('<?php json_encode($allGetParams, true); ?>');
        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    var_dump($allGetParams);
    die();
}

$data['p_selections'] = str_replace('position_','',json_encode($data['p_selections']));

 ?>
<script type="text/javascript">
    console.log('<?php 

    $hash_1 = md5(json_encode($data));
    $hash_1 = substr($hash_1, 0, 5);
    $auth_1 = 
        $hash_1
        .'_'.$data['booking_date']
        .'_'.$data['begin_hour']
        .'_'.$data['end_hour']
        ;


    $p_selections_arr = json_decode($data['p_selections']);
    $p_selections_count = count($p_selections_arr);

    if ($p_selections_count <= 3) {
        $auth_1 .= '_'.$p_selections_count;
        foreach ($p_selections_arr as $key__ => $value__) {
            // echo "Key Value $key__ $value__";
            $auth_1 .= '_'.$value__;
        }
    } else {
        $auth_1 .= '_'.$p_selections_count;
        $auth_1 .= '_'.substr(md5($data['p_selections']), 0, 5);
    }
    $auth_1 = str_replace("-", '', $auth_1);
    $auth_1 = str_replace("position_", '', $auth_1);
        echo $auth_1; ?>');
</script>
<?php
echo "Auth report: $auth_1  ".$data['p_selections'];


m_log("reach email confirm $auth_1");





$p_selections = $allGetParams['p_selections'];
$email = $allGetParams['email'];
$name = $allGetParams['name'];









 ?>
 <!-- <?php var_dump($allGetParams); ?> -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Email Confirmation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        /** Google webfonts. Recommended to include the .woff version for cross-client compatibility. */
        @media screen {
            @font-face {
                font-family: 'Source Sans Pro';
                font-style: normal;
                font-weight: 400;
                src: local('Source Sans Pro Regular'), local('SourceSansPro-Regular'), url(https://fonts.gstatic.com/s/sourcesanspro/v10/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
            }
        }
        /* Add your custom styles here */
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 3px;
            font-weight: bold;
        }
    </style>
</head>
<body style="display: none;">

<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//analytics.austreme.com/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '215']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>



<!-- 

<?php


// Encode as JSON
$jsonData = json_encode($allGetParams);



// Use the JSON data
echo "All GET parameters in JSON format: $jsonData";

?>




 -->













































<?php
// Assuming you have a database connection established
require_once 'account_variable.php';

// Create a connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Deserialize the JSON data
// $json_data = '{"name":"Lee Kwun Fung","email":"leekwunfung817@gmail.com","telephone":"62193471","octopus_no":"1234567890","booking_date":"2024-04-22","begin_hour":"9","end_hour":"10","p_selections":["position_7","position_62","position_65"]}';
// $data = json_decode($json_data, true);

// $data['discount'] = $data['discount'][0];


var_dump($data);


require_once 'price-calculation.php';

$total_price = price_calculation( array(
    'lan' => 'zn',
    'print' => 'N'
), $data);

if ($total_price == 0) {
    m_log("page exit 您的付款價格顯示您無需付款或預訂 Your payment price shows that you don\'t need to pay or book");
     ?>
    <script type="text/javascript">
        alert('您的付款價格顯示您無需付款或預訂\nYour payment price shows that you don\'t need to pay or book');
        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    die();
}




require_once './checker_duplicate_bay.php';


$_GET['exact_date']=$allGetParams['booking_date'];
$_GET['booking_date']=$allGetParams['booking_date'];
$_GET['begin_hour']=$allGetParams['begin_hour'];
$_GET['end_hour']=$allGetParams['end_hour'];
$_GET['p_selections']=$data['p_selections'];
$_GET['disable_die']=1;

require 'booking-status-json-variable.php';
// var_dump($complexArray);
if ($is_duplicated) {
    m_log("email confirm detect DUPLICATED $duplicate_info $duplicate_info_2");
echo $duplicate_info;
echo "$duplicate_info_2 <br>";
    // code...
}

$allowed_to_book_message = "";
$allowed_to_book_message .= "";


$allowed_to_book = (
!$is_duplicated ?
'Y':'N'
)
;


$staff_name = '';
if (isset($_SESSION['name'])) {
    $staff_name = $_SESSION['name'];
}


echo "Point A";

$key1 = $data['booking_date'];
$begin_hour = (int) $data['begin_hour'];
$end_hour = (int) $data['end_hour'];
$p_selections = $data['p_selections'];






function stop_by_invalid_booking($hins)
{

}


function stop_by_create_error($hins)
{
    m_log("page exit 抱歉，當您和其他人同時提交預訂請求時，您的預訂與其他人發生衝突。");
     ?>
    <script type="text/javascript">
        alert('抱歉，當您和其他人同時提交預訂請求時，您的預訂與其他人發生衝突。 \n Sorry, your booking is conflicted with another person when you and another person submit the booking request at the same time. <?php echo $hins; ?>');

        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    die();
}


if ($allowed_to_book == 'N') {
    stop_by_invalid_booking('Allowed to book = N');
    $allowed_to_book_message .= "stop_by_invalid_booking";

}




$p_selects_arr = json_decode($p_selections);
{


    $sql = "

    SELECT `p_selections` FROM `golf_fairway_booking` WHERE 
        `booking_date`='$key1'
        and ( CAST( '$begin_hour' AS UNSIGNED ) between `begin_hour` and `end_hour` )
        and ( CAST( '$end_hour' AS UNSIGNED ) between `begin_hour` and `end_hour` )
    ;

    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $p_selects_arr_1 = json_decode($row['p_selections']);
        foreach ($p_selects_arr_1 as $value) {
            if (in_array($value, $p_selects_arr)) {
                echo "Hit $value";
                stop_by_invalid_booking('golf_fairway_booking p_selections cohesion');
            }
        }
        // var_dump($row);
      }
    }

}


{
    $sql = "
SELECT `date`, `hour`, `position`, `src`
FROM `golf_booking_buffer`
where '$key1'=`golf_booking_buffer`.`date`
and CAST('$begin_hour' AS UNSIGNED)<=CAST(REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '') AS UNSIGNED)
and CAST('$end_hour' AS UNSIGNED)>=CAST(REPLACE(REPLACE(`golf_booking_buffer`.`hour`,':30', '.5'),':00', '') AS UNSIGNED)
;

    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        if (in_array($row['position'], $p_selects_arr)) {
            echo "Hit $value";
            stop_by_invalid_booking('golf_booking_buffer time_range cohesion');
        }
      }
    }

}




$insert_count = 0;

$key3 = 'booking';
{
    for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
        $hour_int = ((int) $cursor_hour);
        $is_half_hour = $cursor_hour != $hour_int;
        $half_hour_mark = ($is_half_hour ? ':30' : ':00');
        $key2=$hour_int . $half_hour_mark;
        $is_valid_booking = true;
        foreach (json_decode($p_selections) as $key => $position) {
            $key4=str_replace("position_", "", $position);
            if ($complexArray[$key1][$key2][$key3][$key4] != 0) {
                stop_by_invalid_booking('Complex Array cohesion');
            }


            // {
            //     $sql = "
            //         SELECT `date`, `hour`, `position`, `src`
            //         FROM `golf_booking_buffer`
            //         where `golf_booking_buffer`.`date`='$key1'
            //         and `golf_booking_buffer`.`hour`='$key2'
            //         and `position`='$key4'
            //         ;
            //     ";
            //     $result = $conn->query($sql);
            //     if ($result->num_rows > 0) {
            //         stop_by_invalid_booking();
            //     }
            // }


        }

        foreach (json_decode($p_selections) as $key => $position) {
            $key4=str_replace("position_", "", $position);
            if (strlen($key4)==0) {
                continue;
            }











            $sql = "
            INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
            VALUES 
            ('$key1','$key2','$key4','');
            ";
            try {
                    
                // Execute the query
                if ($conn->query($sql) === TRUE) {
                    $insert_count += 1;
                    // echo "Data inserted successfully!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                    echo "SQL error 222 $sql";
                    stop_by_create_error('Insert golf_booking_buffer failed');
                }
            } catch (Exception $e) {
                echo $e;
                echo "Exception 222 $sql";


                $sql = "
                    SELECT count(*) c
                    FROM `golf_booking_buffer`
                    where `date`='$key1' and `hour`='$key2' and `position`='$key4'
                    ;
                ";
                $result = $conn->query($sql);
                if ($result->num_rows <= 0) {
                    while ($row = $result->fetch_assoc()) {
                        stop_by_create_error('Insert golf_booking_buffer Exception '." $key1 $key2 $key4 count: ".$row['c']);
                    }
                }

                
            }



            {
                $sql = "
                    SELECT `date`, `hour`, `position`, `src`
                    FROM `golf_booking_buffer`
                    where `golf_booking_buffer`.`date`='$key1'
                    and `golf_booking_buffer`.`hour`='$key2'
                    and `position`='$key4'
                    ;
                ";
                $result = $conn->query($sql);
                if ($result->num_rows <= 0) {
                    stop_by_invalid_booking('golf_booking_buffer less than 1');
                }
            }



















        }
    }
}

echo "Point B";
$remark = '';

if (isset($allGetParams['remark']) && strlen($allGetParams['remark']) > 0) {
    $remark .= "備註 Remark : \n".$data['remark']."\n\n";
}

if (isset($allGetParams['school_name']) && strlen($allGetParams['school_name']) > 0) {
    $remark .= "學校名稱 School Name : \n".$data['school_name']."\n\n";
}

if (isset($allGetParams['disabled_id']) && strlen($allGetParams['disabled_id']) > 0) {
    $remark .= "傷健人士號碼 Disabilities Identification : \n".$data['disabled_id']."\n\n";
}


 ?>
<script type="text/javascript">
    console.log('Check Auth<?php 
        $auth = $auth_1;
        echo $auth_1.' to '.$auth ?>');
</script>


<?php


echo "Point 1";
if (strlen($remark)>0) {
        
    // Construct the SQL query
    echo "Before SQL \n";
    $sql = "
    INSERT INTO `golf_remark`
    SELECT '$auth' `auth`, '$remark' `remark` 
    where (select count(*) c from `golf_remark` where `auth`='$auth')=0;";
    echo "After SQL \n";

    echo "SQL: $sql";
    try {
            
        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // echo "Data inserted successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } catch (Exception $e) {
        echo $e;
    }

}
echo "Point 2";

$src = 'online';

if (isset($_SESSION['name'])) {
    $src = $_SESSION['name'];
}

$school_name = '';
if (isset($data['school_name'])) {
    $school_name = $data['school_name'];
}





require_once './checker_duplicate_bay.php';

if ($is_duplicated) {
    stop_by_invalid_booking_2("Bay Duplicated ".$auth);
} else {
?>
<script type="text/javascript">
    // alert('Not duplicate');
</script>
<?php
}






// if (isset($data['email']) && strlen($data['email'])>=1 && strlen($data['octopus_no'])<=1) {
//     $sql = "
//     SELECT octopus_no,check_digit 
//     FROM `golf_fairway_booking` 
//     WHERE `email`='{$data['email']}' 
//     order by id desc 
//     limit 1
//     ";
//     $result = $conn->query($sql);
//     if ($result->num_rows > 0) {
//         while ($row__ = $result->fetch_assoc()) {
//             $data['octopus_no'] = $row__['octopus_no'];
//             $data['octopus_no_q'] = $row__['check_digit'];
//             // $count = check_buffer_count_1($conn,$row);
//             // echo $count;
//             // echo "<br>";
//             // var_dump($row);
//             // echo "<br>";
//         }
//     }
// }


// Construct the SQL query
$sql = "INSERT INTO golf_fairway_booking (name, email, telephone, octopus_no, check_digit, booking_date, begin_hour, end_hour, discount, p_selections, auth, src, `school-name`)
        VALUES ('{$data['name']}', '{$data['email']}', '{$data['telephone']}', '{$data['octopus_no']}', '{$data['octopus_no_q']}',
                '{$data['booking_date']}', '{$data['begin_hour']}', '{$data['end_hour']}', '{$data['discount']}', '".$data['p_selections']."', '".$auth."', '".$src."', '$school_name')";

try {
        
    // Execute the query
    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully! Point A";
        m_log("golf_fairway_booking inserted successfully! $sql");
    } else {
        m_log("golf_fairway_booking inserted Error! $sql $conn->error");
        echo "Error: " . $sql . "<br>" . $conn->error;
    echo "SQL error 111";
        $allowed_to_book = 'N';
        echo "";

            $allowed_to_book_message .= " Cannot Insert golf_fairway_booking ";
    }
echo "Check Point 2.0";
} catch (Exception $e) {
    // if ($mysqli->errno == 1062) {

    // }

echo "Check Point 2.1";
    echo $e;
    echo "Exception 111";
    $allowed_to_book = 'N';
    $sql_msg = str_replace("'", "\\'", $sql);
    // $sql_msg = str_replace("", "", $sql_msg);
    $sql_msg = str_replace("\n", "", $sql_msg);
    $allowed_to_book_message .= " Exception Insert golf_fairway_booking $sql_msg ";
}
echo "Check Point 3";
if ($allowed_to_book == 'N') {
    m_log("page exit 抱歉，此預訂無效或與其他預訂重複。");
     ?>
    <script type="text/javascript">
        stop_by_invalid_booking_2('抱歉，此預訂無效或與其他預訂重複。 Sorry, this booking is not valid or duplicate with others.')
        // alert('Sorry, this booking is not allowed')
        console.log('<?php echo $duplicate_info; ?>');
        console.log('<?php echo $duplicate_info_2; ?>');
        console.log('is_duplicated:<?php echo $is_duplicated; ?>');
        console.log('allowed_to_book_message:<?php echo $allowed_to_book_message; ?>');

        setTimeout(function() {
            window.location.href = "./";
        }, 2000);
    </script>
    <?php
    die();
}

echo "Check Point 4";
if (is_booking_duplicate_by_auth($data['booking_date'], $auth)) {
    stop_by_invalid_booking_2("Bay Duplicated ".$auth);
}



    // stop_by_invalid_booking_2("Bay Duplicated");


// $_GET['exact_date']=$data['booking_date'];

// require_once 'booking-status-json-variable.php';

// if (isset($duplicate_array[$auth])) {
//     stop_by_invalid_booking("$auth > ".$duplicate_array[$auth]);
// }

// foreach ($duplicate_array as $key => $value) {
//     if ($key == $auth || $value == $auth) {
//         stop_by_invalid_booking("$auth > $key > $value");
//     }
// }




?>









































<?php

$skip_email_confirmation = true;


// Set these parameters
$subject = '白石高爾夫球練習場 - 確認您的電郵地址 | White Head Golf Driving Range - Confirm your email address'; // Subject of the email
$emailadd = 'support@riversidegolf.com.hk'; // Your email address (where the form information will be sent)
$url = 'thanks.php'; // Redirect URL after form processing
$req = '0'; // Set to '1' to make all fields required, '0' to allow empty fields

$confirm_url = "./email-confirmation-return.php?name=".urlencode($name)
."&auth=".urlencode($auth)
// ."&interface=".urlencode($json_filename)
."";
// Initialize variables
$text = "親愛的".$name."，

感謝您選擇白石高爾夫球練習場！ 感謝您對我們的高爾夫練習設施的興趣。 為了確保溝通順利，我們懇請您確認您的電子郵件地址。

請按一下以下按鈕以驗證您的聯絡資訊。 如果您有任何其他問題或需要進一步協助，請隨時聯絡我們的客戶服務團隊。

$confirm_url

我們期待很快歡迎您來到我們的高爾夫練習場！

此致
白石高爾夫球場 團隊

____________________________________________________________________________________________________________________


Dear ".$name.",

Thank you for choosing White Head Golf Driving Range! Thank you for your interest in our golf practice facilities. To ensure smooth communication, we kindly ask you to confirm your email address.

Please click the button below to verify your contact information. If you have any further questions or require further assistance, please feel free to contact our customer service team.

$confirm_url

We look forward to welcoming you to our driving range soon!

Best Regards
White Head Golf


";
$space = ' ';
$line = ' ';

// email-confirmation
if ($skip_email_confirmation) {
    echo "Relocate 1";
    echo $confirm_url;
    m_log("page exit forward to next payment page $confirm_url");
    echo '<script type="text/javascript"> window.location.href = "'.$confirm_url.'"; </script>';
} else {
    echo "Send mail";
    // Send the email
    mail($email, $subject, $text, 'From: ' . $emailadd);
}



?>







<?php

// $filename = './email-confirmation/'.$auth;
// $content = $email;

// // Open the file for writing (mode "w")
// $file = fopen($filename, "w") or die("Unable to open file!");

// // Write the content to the file
// fwrite($file, $content);

// // Close the file
// fclose($file);

// // echo "File created successfully!";

// clean_and_check_booking_auth($conn,$auth);






































function check_buffer_count_1($conn,$data)
{

    $id = $data['id'];
    $key1 = $data['booking_date'];
    $begin_hour = (int) $data['begin_hour'];
    $end_hour = (int) $data['end_hour'];
    $p_selections = $data['p_selections'];

    $buffer_count = 0;

    for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+0.5) {
        $hour_int = ((int) $cursor_hour);
        $is_half_hour = $cursor_hour != $hour_int;
        $half_hour_mark = ($is_half_hour ? ':30' : ':00');
        $key2=$hour_int . $half_hour_mark;
        foreach (json_decode($p_selections) as $key => $position) {
            // echo $position.'<br>';
          $buffer_count += 1;

                 $key4=str_replace("position_", "", $position);
                 if (strlen($key4)==0) {
                     continue;
                 }
                 $sql_1 = "
                 INSERT INTO `golf_booking_buffer`(`date`, `hour`, `position`, `src`) 
                 VALUES 
                 ('$key1','$key2','$key4','$id');
                 ";
                   try {
                       // Execute the query
                       if ($conn->query($sql_1) === TRUE) {
                           // echo "Data inserted successfully!";
                       } else {
                       //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
                       // echo "SQL error 222 $sql";
                       }
                   } catch (Exception $e) {
                       // echo $e;
                       // echo "Exception 222 $sql_1";
                   }


                 $sql_1 = "
                 UPDATE `golf_booking_buffer` SET `src`='$id'
                 WHERE `date`='$key1' and `hour`='$key2' and `position`='$key4';
                 ";
                   try {
                       // Execute the query
                       if ($conn->query($sql_1) === TRUE) {
                           // echo "Data inserted successfully!";
                       } else {
                       //     echo "Error: " . $sql_1 . "<br>" . $conn->error;
                       // echo "SQL error 222 $sql";
                       }
                   } catch (Exception $e) {
                       // echo $e;
                       // echo "Exception 222 $sql_1";
                   }





        }
    }
    return $buffer_count;

}

function clean_and_check_booking($conn,$id)
{
    
    // $id = '989';

    // $sql = "
    // SELECT * FROM `golf_booking_buffer` WHERE `src`='$id';
    // ";

    // $result = $conn->query($sql);
    // if ($result->num_rows > 0) {
    //   while ($row = $result->fetch_assoc()) {
    //      var_dump($row);
    //      echo "<br>";
    //   }
    // }


    // SELECT * FROM `golf_booking_buffer` WHERE `src`='708';
    $sql = "
    SELECT * FROM `golf_fairway_booking` WHERE `id`='$id';
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $count = check_buffer_count_1($conn,$row);
        // echo $count;
        // echo "<br>";
        // var_dump($row);
        // echo "<br>";
      }
    }


    // $sql = "SELECT * FROM `golf_booking_buffer` WHERE `src`='$id';";
    // $result = $conn->query($sql);
    // if ($result->num_rows > 0) {
    //  while ($row = $result->fetch_assoc()) {
    //      // var_dump($row);
    //      echo "<br>";
    //  }
    // }
}

function clean_and_check_booking_auth($conn,$auth)
{
    $sql = "
    SELECT * FROM `golf_fairway_booking` WHERE `auth`='$auth';
    ";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $count = check_buffer_count_1($conn,$row);
        clean_and_check_booking($conn,$row['id']);
        // echo $count;
        // echo "<br>";
        // var_dump($row);
        // echo "<br>";
      }
    }

}

// 1003
// 927

// 1800
// 9 bay
// clean_and_check_booking($conn,'1003');
// clean_and_check_booking($conn,'927');






$sql = "
SELECT * FROM `golf_fairway_booking`
order by id desc
limit 10
;
";

$result = $conn->query($sql);
if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $count = check_buffer_count_1($conn,$row);
    clean_and_check_booking($conn,$row['id']);
    // echo $count;
    // echo "<br>";
    // var_dump($row);
    // echo "<br>";
  }
}

$sql = "DELETE FROM `golf_booking_buffer` WHERE LENGTH(`src`)=0 or LENGTH(`position`)=0;";
$result = $conn->query($sql);



// $sql = "SELECT * FROM `golf_booking_buffer` WHERE `src`='';";
// $result = $conn->query($sql);
// if ($result->num_rows > 0) {
//  while ($row = $result->fetch_assoc()) {
//      // var_dump($row);
//      // echo "<br>";
//  }
// }





















// Close the connection
$conn->close();

t_log('end[email-confirmation.php]');


?>


</body>
</html>
