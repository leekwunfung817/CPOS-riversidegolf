<?php

require_once 'logger.php';

require_once 'setting-admin.php';

t_log('begin[input-form.php]');

// Function to detect browser
function getBrowser() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    if (strpos($userAgent, 'Chrome') !== false) {
        return 'Chrome';
    } elseif (strpos($userAgent, 'Edge') !== false) {
        return 'Edge';
    } elseif (strpos($userAgent, 'Firefox') !== false) {
        return 'Firefox';
    } elseif (strpos($userAgent, 'Safari') !== false && strpos($userAgent, 'Chrome') === false) {
        return 'Safari'; // Exclude Chrome since it also includes 'Safari'
    } else {
        return 'Other';
    }
}


function is_mobile_browser() {
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    }

    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);

    $keywords = [
        'android', 'iphone', 'ipad', 'ipod',
        'blackberry', 'windows phone', 'opera mini',
        'mobile', 'silk', 'kindle'
    ];

    foreach ($keywords as $k) {
        if (strpos($ua, $k) !== false) {
            return true;
        }
    }

    return false;
}

// Get the browser
$browser = getBrowser();

// Display a message based on the browser
if ($browser === 'Chrome' || $browser === 'Edge' || $browser === 'Firefox' || $browser === 'Safari') {
    // echo "You are using $browser, great choice!";
} else {
    echo "<h1>看來您沒有使用 Chrome、Edge、Firefox 或 Safari。請考慮切換到其中一個瀏覽器以獲得最佳體驗。 <hr> It seems you're not using Chrome, Edge, Firefox, or Safari. Please consider switching to one of these browsers for the best experience.</h1><hr>".$_SERVER['HTTP_USER_AGENT'];
    exit();
}
?>
<?php 

session_start();
$is_management = isset($_SESSION["management"]);

// Prefer explicit GET `type` over session when deciding which reservation UI to show.
// Normalize value to lowercase and trim whitespace.
$reserve_type = '';
if (isset($_GET['type']) && strlen(trim((string)$_GET['type'])) > 0) {
    $reserve_type = strtolower(trim((string)$_GET['type']));
} elseif (isset($_SESSION['type']) && strlen(trim((string)$_SESSION['type'])) > 0) {
    $reserve_type = strtolower(trim((string)$_SESSION['type']));
} else {
    $reserve_type = 'golf';
}

 ?><?php 
require_once 'tesing_stage_verification.php';
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
<title>
    白石高爾夫球練習場 打球位置 預訂表格
    
    White Head Club - Booking Form for Golf Court
</title>

    <style>


    .show_for_desktop {
        display: inline-block;
    }
    .hide_for_desktop {
        display: none;
    }
input {

  box-shadow: inset blue 0px 0px 3px -1px; 
}
    #frame {
        background: rgba(255,255,255
/*            ,0.8*/
        );
        width: 100%;
        height: 100%;
        padding: 30px;
    }




    .position {
        border: 5px solid grey;
        font-size: 2em;
    }

    .span_checkbox {
        font-size: 1.2em;
        height: 3em;
    }

html {
        background-image: url('4c2dd3a4-9b6f-4a23-bb55-f2d0ab71397a.jpg');
        background-color: white;
        padding: 30px;
}

    body {
        background-color: white;
        padding: 30px;
        width: 100%;
        background-size: cover;
        font-family: Arial, sans-serif;
    }

    .input-container {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        
    }

    input {
        border: none;
        padding: 15px 32px;
        text-decoration: none;
        display: inline-block;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        transition-duration: 1s;
    }
    input:hover {
/*        background-color: yellow;*/
background-image: linear-gradient(to right top, #D8E6F0, #D1F1DB);
/*        border: 1px solid #87cefa; */
    }

    input, optgroup , select {
        padding: 10px;
        border: 2px solid #ccc;
        border-radius: 5px;
        background-color: rgba(255, 255, 255
/*            , 0.8*/
        );
        /* Add any other styling you need */
        width:  90%;
        height: 100%;
    }

    .position {
        width: 200px;
    }





    h1 {
        font-size: 2em;
    }
    input, optgroup , select {
        font-size: 2em;
    }
    /* Create a custom checkbox */
    .checkmark {
        height: 1.5em;
        width: 3em;
        font-size: 1.5em;
    }


small {
    font-size: 0.6em;
}















@media only screen and (max-width: 1300px) {
    .show_for_desktop {
        display: none;
    }
    .hide_for_desktop {
        display: inline-block;
    }

    .input-container {
        position: relative;
        width: 100%;
        justify-content: left;
        align-items: left;
    }
    input, optgroup , select {
/*            background-color: rgba(255, 255, 255, 1);*/
        width:  90%;
        height: 100%;
    }
    th, td {
/*        background-color: rgba(255, 255, 255, 0.7);*/
    }

     {
        padding: 10px;
        margin: 10px;
        border: 5px solid white;
    }

    .hide_for_mobile {
        display: none;
    }



    h1 {
        font-size: 4em;
    }
    h3 {
        font-size: 3em;
    }
    h4 {
        font-size: 2em;
    }
    input {
        font-size: 3.5em;
    }
    .position {
        font-size: 2em;
    }

    th {
        font-size: 2.5em;
    }

    /* Create a custom checkbox */
    .checkmark {
        height: 0.6em;
        width: 3em;
    }


    .span_checkbox {
        font-size: 2.5sem;
        height: 4em;
    }
}



















@media only screen and (max-width: 600px) {
    .input-container {
        position: relative;
        width: 100%;
        justify-content: left;
        align-items: left;
    }
    input, optgroup , select {
/*            background-color: rgba(255, 255, 255, 1);*/
        width:  95%;
        height: 100%;
    }
    th, td {
        background-color: rgba(255, 255, 255
/*            , 0.7*/
        );
    }

     {
        padding: 10px;
        margin: 10px;
        border: 5px solid white;
    }

    .hide_for_mobile {
        display: none;
    }

    .show_for_desktop {
        display: none;
    }
    .hide_for_desktop {
        display: inline-block;
    }



    h1 {
        font-size: 3em;
    }
    h3 {
        font-size: 2.5em;
    }
    input, optgroup , select, .position {
        font-size: 2em;
    }

    th {
        font-size: 2em;
    }

}


input, optgroup , select {
    border-radius:  20px;
}
.expend {
    cursor: pointer;
    font-size: 1.3em;
/*    border: 5px solid yellow;*/
transition: opacity 2s;
/*    transition: all 2s;*/
}
.expend:hover {
/*    background-image: linear-gradient(to right top, #E7FDDF, #DFF1FD);*/
/*    border: 5px solid blue;*/
/*transition: all 2s;*/
}

.form-label {
    font-size: 1.3em;
}

    </style>
</head>




































<body>

<?php 
require_once 'booking-status-json-variable.php';
 ?>

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

    <div class="input-container">
<div id="frame" style="padding: 10 px;margin: 10 px;">








<style type="text/css">

    table {
        text-align: left;
        border-collapse: collapse;
        width: 100%;
    }

    th, td {
        text-align: left;
        white-space: nowrap;
    }

    label {
        display: block;
    }

</style>


<form method="get" action="./email-confirmation.php">
    <input type="hidden" name="type" value="<?php echo htmlspecialchars($reserve_type, ENT_QUOTES, 'UTF-8'); ?>">

<!-- 
    <h1 style="color: red;">
        
網站正在建置中，非開發者請勿嘗試透過本網站進行任何操作。  如果您想立即預訂，請自行前往高爾夫球場預訂。
<br>
The website is under construction. Non-developers should not attempt to perform any operations through this website. If you would like to book immediately, please make your own reservation at the golf course.

    </h1> -->
<table style="background-color: white;">
    <tr>
        <td colspan="2">
            <a href="."> < Back</a>
            <br>
            <div style="font-size: 0.9em;">
            <?php 
            if (false) {
                
                echo "<hr>";
                if ($must_half_hour) {
                ?>
                <a href="?whole_hour">切換到<b style="color: red;">一</b>小時制 Switch to one-hour system</a>
                <?php 
                } else {
                ?>
                <a href="?half_hour">切換到<b style="color: red;">半</b>小時制 Switch to half-hour system</a>
                <?php 
                }
            }
            ?>
            </div>
            <h1 style="background-color: white;">
            <hr>
            <?php 
if ($reserve_type == 'pickleball') {
?>
匹克球<br>預訂表格<br> 
Pickleball<br>Reservation Form
<?php 
} else {
?>
白石高爾夫球<br>預訂表格<br> 
White Head Club<br>Reservation Form
<?php 
}
            ?>
            <hr>
            </h1>
        </td>
    </tr>
    <tr>
        <td colspan="2">
<?php 
date_default_timezone_set('Asia/Hong_Kong');

$currentDate = new DateTime();
$current_timestamp = $currentDate->format('Y-m-d').'T'.$currentDate->format('H:i:s');
// echo $current_timestamp;


$futureOneHourDate = new DateTime();
$futureOneHourDate->modify('+1 hour');
$futureOneHour_timestamp = $futureOneHourDate->format('Y-m-d').'T'.$futureOneHourDate->format('H:00:00');
// echo "$futureOneHour_timestamp";



 ?>
(*) 必填項 Mandatory field
<h3 style="background-color: white;">
<hr>
個人資訊 Personal Information 
<hr>
</h3>

        </td>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>
                <span class="form-label">姓名 Full Name</span>
            </small>
            <br>
            *<input 
            class="form-control" 
            type="text" 
            name="name" 
            id="name" 
            placeholder="姓名 Name" 
            required 
            autocomplete="on"
            value="<?php
                $cookie_name = "name"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            ><br></td>
            <script>
                function checkNameEmpty() {
                    const nameValue = document.getElementById("name").value.trim();
                    return (nameValue === "");
                }
            </script>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>
                <span class="form-label">電子郵件地址 Email Address</span>
            </small>
            <br>
            <table style="width: 90%;">
                <tr>
                    <td style="width: 60%;">
                        
            *<input 
            class="form-control" 
            type="text" 
            name="email" 
            id="confirm_email" 
            placeholder="電子郵件地址 Email address" 
            required 
            autocomplete="on"
            value="<?php
                $cookie_name = "email"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            >
                    </td>
                    <td style="width: 40%;">

                        <input 
                            type="text" 
                            class="form-control" 
                            onclick="send_confirm_code(false)" 
                            id="confirmation_button" 
                            placeholder="按此發送6位數字驗證碼 \n Click here to send 6-digit confirmation code" 
                            readonly>

                        <input type="hidden" name="open_datetime" id="open_datetime" value="<?php
                            echo $current_timestamp;
                         ?>" readonly>

                    </td>
                </tr>
            </table>
            







        </td>
    </tr>
    <tr>
                    <td>



                    </td>
    </tr>
    <tr style="color: blue;"><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>


<table style="width: 100%;">
                <tr>
                    <td style="width: 100%;" >
                        <small>
                            <span class="form-label">郵件地址 - 驗證碼 Confirmation Code</span>
                        </small>
                        <br>
                        *<input 
            class="form-control" 
            type="text" 
            name="confirmation_code" 
            id="confirmation_code" 
            style="color: blue;" 
            placeholder="驗證碼 Verification code" 
            onkeydown="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 
            onblur="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 
            onclick="setTimeout(function() {
                checkConfirmCode(false);
            }, 1);" 

            autocomplete="off" 
            required><br>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        
                        <small style="color: red;">
                            我們的電子郵件可能會被網域名稱或郵件信箱過濾。如果您無法收到確認碼，請嘗試其他電子郵件地址。
                            <br>
                            Our emails may be filtered by domain names or mailbox. If you are unable to receive the confirmation code, please try a different email address.
                        </small>
                    </td>
                </tr>
            </table>

    <script type="text/javascript">

        function setCookie(name, value, days) {
            console.log('setCookie:',name, value);
            // Remove the cookie by setting an expired date
            document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";

            // Set the new cookie
            let expires = "";
            if (days) {
                let date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        }




        confirmed = false;
        confirming = false;
        function checkCodeConfirmed(already_checked, bypass_alert) {
            // console.log('Correct', html);


            setCookie("name_rivergolf", document.getElementById('name').value, 90);


            confirmed = true;
            if (already_checked) {
                if (!bypass_alert) {
                    alert('不需要確認碼 該電子郵件地址之前已驗證過 ! Confirmation code is not needed, this email address was validated before!');
                }
                document.getElementById('confirmation_code').value = '不需要 Not needed';
            } else {
                alert('驗證碼正確! Confirmation code correct!');
            }
            
            confirmation_code.style.backgroundColor = '#91FE69';

            document.getElementById('name').readOnly = true;
            document.getElementById('confirm_email').readOnly = true;
            document.getElementById('confirmation_code').readOnly = true;
            document.getElementById('confirmation_button').disabled = true;
        }
        function checkConfirmCode(bypass_alert) {
            if (confirmed || confirming) {
                console.log('Already confirmed or confirming');
                return;
            }
            email = document.getElementById('confirm_email').value;
            confirmation_code = document.getElementById('confirmation_code');
            
            // console.log(confirmation_code.value, confirmation_code.value.length)
            if (confirmation_code.value.length == 6) {
                console.log('Checking confirmation code');
                confirming = true;
                confirmation_code = document.getElementById('confirmation_code');
                
                open_datetime = document.getElementById('open_datetime').value;

                // console.log('confirmation_code',confirmation_code,'.');
                fetchHtml('./email-confirmation.php?confirmation_code='+confirmation_code.value+'&email='+email+'&open_datetime='+open_datetime,function (html) {
                    console.log('Received response for confirmation code check');
                    correct = (html_result=='Y'?true:false);

                    // console.log(confirmation_code.value, code_buffer);
                    if (
                        correct
                        // || true // Test Temporary
                        // confirmation_code.value==code_buffer
                        ) {
                        console.log('Correct');
                        checkCodeConfirmed(false, bypass_alert);
                    } else {
                        console.log('Incorrect', html);
                        confirmation_code.style.backgroundColor = '#FE8569';
                        // confirmation_code.value = confirmation_code.value.slice(0, 5);
                        confirmation_code.focus();
                    }
                    confirming = false;
                });
            }
        }
    </script>








    <style type="text/css">
        #confirmation_code:hover {
            background-color: yellow;
        }
        #confirmation_button {
            padding: 10px;
            margin: 10px;
        }
        #confirmation_button:hover {
            background-color: yellow;
        }
    </style>
        <script type="text/javascript">
            function checkEmailValidity(input) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(input.value);
            }
        </script>
    <script type="text/javascript">
        code_buffer = null;
        html_result = null;
        async function fetchHtml(url, callback) {
            try {
                const response = await fetch(url);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const html = await response.text();
                html_result = html;
                // code_buffer = html;
                console.log(html);
                callback(html)
                return html;
            } catch (error) {
                console.error('Error fetching HTML:', error);
                return null; // Or handle the error differently
            }
        }

        function send_confirm_code(bypass_alert) {
            confirm_email_element = document.getElementById('confirm_email');
            email = confirm_email_element.value;

            // fetchHtml('',function (html) {
                
            // });
            <?php
                $cookie_name = "email"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                     ?>
                    if (email == '<?php echo $_COOKIE[$cookie_name]; ?>') {
                        checkCodeConfirmed(true, bypass_alert);
                        return;
                    }
                    <?php
                } else {
                     ?>
                    if (bypass_alert) {
                        checkCodeConfirmed(true, bypass_alert);
                        return;
                    }
                    <?php
                }
             ?>


            document.getElementById('confirmation_code').value = '';
            if (!checkEmailValidity(confirm_email_element)) {
                alert('請輸入電子郵件地址\n Please enter email address');
                return;
            }
            
            confirmation_code = document.getElementById('confirmation_code').value;
            open_datetime = document.getElementById('open_datetime').value;

            if (confirm('您確定要透過'+email+'接收確認碼嗎? \n Are you sure you want to receive the confirmation code by '+email+'?')) {

                fetchHtml('./email-confirmation.php?confirmation_code='+confirmation_code+'&email='+email+'&open_datetime='+open_datetime,function (html) {});

            }
        }
    </script>





        </td>
    </tr>
    <tr><!-- 
        <th class="hide_for_mobile"><span class="form-label">電話號碼<br>Telephone No.</span></th> -->
        <td>

<script type="text/javascript">
function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}
</script>

            <input 
            class="form-control" 
            type="text" 
            name="telephone" 
            id="telephone" 
            placeholder="電話號碼 Phone number" 
            autocomplete="on"

            value="<?php
                $cookie_name = "telephone"."_rivergolf";
                if(isset($_COOKIE[$cookie_name])) {
                    echo $_COOKIE[$cookie_name];
                }
             ?>" 
            onkeypress="
                return isNumberKey(event);
            " 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('telephone').value, 
                        90
                    );
                },10);
            "
             required
            ><br></td>
    </tr>






<?php 
if ($reserve_type != 'pickleball') {
?>
    <tr>
        <td colspan="2">

<h3 style="background-color: white;">
<hr>
價錢及優惠 Pricing & Discount<br>

<br>
<small>
<a target="_blank" href="./price_display.php">請按此處參考價錢表 Please click here for price table</a>
</small>
<hr>
</h3>




        </td>
    </tr>
<?php 
}
 ?>

    <tr
    
<?php 
if ($reserve_type == 'pickleball') {
?>
hidden
<?php 
}
?>
    >
        <td colspan="2" style="text-align: center;">
            <hr>
            <h4>價錢選項 Pricing option</h4> 
            <hr>
            <br>
            <style type="text/css">
                .discount_radio {

                    color: black;
                    border-style: solid;
                    border-radius:  20px;
                    text-align: center;
                    height: 5em;
                    width: 90%;


                }
            </style>
            <table>
                <tr>
                    
                    <td>
                        
                        <label class="container" style="text-align: center;">
                            <input type="radio" name="discount" id="hourly" value="H">
                            <span id="discount_notice_span_hourly" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                    </td>
                    <td>
                        <label class="container" style="text-align: center;">
                            <input type="radio" class="discount_radio" name="discount" id="student" value="S">
                            <span id="discount_notice_span_student" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                    </td>
<!--                 </tr>
                <tr> -->
                    <td>
                        <label class="container" style="text-align: center;">
                            <input type="radio" name="discount" id="disabled" value="D">
                            <span id="discount_notice_span_disabled" class="checkmark discount_radio span_checkbox discount_checkbox higher" >

                            </span>
                        </label>
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                        <br class="hide_for_desktop">
                    </td>
<!--                 </tr>
                <tr> -->
                </tr>


            </table>
            <table>

                <tr style="white-space: nowrap;">
                    <td>
                        <input type="text" style="
                                background-color: transparent;
                                border-color: transparent;
                                color: transparent;
                                box-shadow: none;
" disabled><br>
                        <div style="color: transparent;">
                            <br>
                            No need to input anything<br>
                        <div style="color: transparent;">_____________________________________</div>
                        </div>
                    </td>
                    <td style="color: red;text-align: center;">
                        <input 
                        type="text" 
                        id="school_name" 
                        name="school_name" 
                        placeholder="學校名稱 School Name"

                        value="<?php
                            $cookie_name = "school_name"."_rivergolf";
                            if(isset($_COOKIE[$cookie_name])) {
                                echo $_COOKIE[$cookie_name];
                            }
                         ?>" 

            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('school_name').value, 
                        90
                    );
                },10);
            "

                        ><br>
                        <small>
                        如果您是學生，請輸入學校名稱。<br>
                        If you are student, please enter school name.<br>
                        </small>
                        <div style="color: transparent;">_____________________________________</div>

                    </td>
                    <td style="color: purple;text-align: center;">
                        <input 
                        type="text" 
                        id="disabled_id" 
                        name="disabled_id" 
                        placeholder="傷健人士號碼 Disabilities Identification"

                        value="<?php
                            $cookie_name = "disabled_id"."_rivergolf";
                            if(isset($_COOKIE[$cookie_name])) {
                                echo $_COOKIE[$cookie_name];
                            }
                         ?>" 


            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('disabled_id').value, 
                        90
                    );
                },10);
            "


                        ><br>
                        <small>
                        如果您是傷健人士，請輸入傷健人士號碼。<br>
                        If you are disabled, please enter your disabilities identification.<br>
                        </small>
                        <div style="color: transparent;">_____________________________________</div>
                    </td>
                </tr>
            </table>

            <script type="text/javascript">
                
                function checked_discount() {

                    var span_id_root = 'discount_notice_span_';
                    var id;

                    id = 'hourly';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選正價<br>Regular Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '正價<br>Regular Price';
                    }

                    id = 'student';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選 學生優惠<br>Student Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '學生優惠<br>Student Price';
                    }


                    id = 'disabled';
                    span_id = span_id_root+id;
                    if (document.getElementById(id).checked) {
                        document.getElementById(span_id).innerHTML 
= '<div style="color: yellow;">已選 傷健人士優惠<br>Disabled Price<br>Selected</div>';
                    } else {
document.getElementById(span_id).innerHTML = '傷健人士優惠<br>Disabled Price';
                    }

                }
                document.getElementById('hourly').checked = true;

            </script>

        </td>
    </tr>
    <tr>
        <td  id="with_vehicle_span_space" style="text-align: center;" colspan="2">
<!--             <hr>
            <h4>其他選項 Other option</h4>
            <hr> -->
            <br>

            <table style="width: 100%;">
                <tr>
                    <td>

<label class="container" style="text-align: center;">
    <input type="checkbox" name="vehicle[]" id="with_vehicle" value="Y">
    <span id="with_vehicle_span" class="span_checkbox checkmark widen_checkbox higher" 
    style="
    color: black;
    border-style: solid;
    border-radius:  20px;
    text-align: center;
    " onchange="checked_with_vehicle()" onclick="checked_with_vehicle()">
    </span>
</label>

    <br class="hide_for_mobile">
    <br class="hide_for_mobile">

    <br class="hide_for_desktop">
    <br class="hide_for_desktop">


                    </td>
                    <td>


<script type="text/javascript">
    
    function check_sand_bay() {
        console.log('check_sand_bay');
        var checkboxes = document.getElementsByClassName("position_checkbox");
        for (var i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false; // Set to true if you want to check them all
        }
    }

</script>
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">


                    </td>
                </tr>
            </table>

<style type="text/css">

#discount_notice_span_hourly, #discount_notice_span_student, #discount_notice_span_disabled {
    height: 4em;
    font-size: 1.5em;
}


#sand_bay_option_span, #with_vehicle_span {
    height: 5em;
    font-size: 1.5em;
}

@media only screen and (max-width: 1300px) {
    #sand_bay_option_span, #with_vehicle_span {
        height: 5em;
        font-size: 2.3em;
        font-style: bold;
    }
    #discount_notice_span_hourly, #discount_notice_span_student, #discount_notice_span_disabled {
        height: 4em;
        font-size: 2.3em;
        font-style: bold;
    }
}
</style>





        </td>
<!--     </tr>
    <tr> -->
        <td style="text-align: center;" colspan="1">
        </td>
        <td></td>
    </tr>
    <tr class="octopus_block">
        <td colspan="2">
            
<h3 style="background-color: white;"><hr> 
    <!-- 進場認證 Entry authentication  -->
    停車場優惠 Parking Offers
    <hr></h3>


        </td>
    </tr>
    <tr class="octopus_block"><!-- 
        <th class="hide_for_mobile">

<script type="text/javascript">
</script>
        </th> -->
<!-- 號碼  number -->
        <td style="vertical-align: text-top;">
            <small>
                <span class="form-label">八達通號碼 Octopus No.</span>
                <?php
                        $cookie_name = "octopus_no"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>
            </small>
            
            <br>
                    *<input type="text" 
                    name="octopus_no" 
                    id="octopus_no" 
                    placeholder="八達通 Octopus"
                    onblur="
                    console.log('confirm_octopus:',confirm_octopus());
                    " 
                    style="width:50%;" 
                    onkeypress="return isNumberKey(event)" 
                    required 
                    autocomplete="on"

                    value="<?php
                        $cookie_name = "octopus_no"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>" 
                    onchange="

                        setTimeout(function () {
                            setCookie(
                                '<?php echo $cookie_name; ?>', 
                                document.getElementById('octopus_no').value, 
                                90
                            );
                        },10);
                            console.log('confirm_octopus:',confirm_octopus());
                    " 
                    >

                    <b class="bracket">(*</b>
                    <input 
                    type="text" 
                    name="octopus_no_q" 
                    id="octopus_no_q"
                    onblur="console.log('confirm_octopus:',confirm_octopus())" 
                    style="width:20%" 
                    placeholder="括號內數字 Bracket number" 
                    onkeypress="return isNumberKey(event)" 
                    required 
                    autocomplete="on"

                    value="<?php
                        $cookie_name = "octopus_no_q"."_rivergolf";
                        if(isset($_COOKIE[$cookie_name])) {
                            echo $_COOKIE[$cookie_name];
                        }
                     ?>" 
                    onchange="

                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_q').value, 
                        90
                    );
                },10);

                    console.log('confirm_octopus:',confirm_octopus());

                    "  

                    ><b class="bracket">)</b>

        </td>
    </tr>
    <tr class="octopus_block" id="octopus_block"><!-- 
        <th class="hide_for_mobile">
        </th> -->
        <td id="octopus_no_cf2">
            <small>
                <span class="form-label">八達通號碼 重複確認 Octopus No. repeat confirmation</span>
            </small>
            <br>
            <!-- 重複確認  number repeat confirmation -->
            <b style="vertical-align: text-top;">
                *<input type="text" 
                name="octopus_no_cf" id="octopus_no_cf" 
                placeholder="八達通 Octopus"
                style="width:50%;" 
                onblur="console.log('confirm_octopus:',confirm_octopus())" 
                onchange="console.log('confirm_octopus:',confirm_octopus())" 
                onkeypress="return isNumberKey(event)" 
                required 
                autocomplete="on"

                value="<?php
                    $cookie_name = "octopus_no_cf"."_rivergolf";
                    if(isset($_COOKIE[$cookie_name])) {
                        echo $_COOKIE[$cookie_name];
                    }
                 ?>" 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_cf').value, 
                        90
                    );
                },10);
            "
                >

                <b class="bracket">(*</b>

                <input type="text" name="octopus_no_q_cf" id="octopus_no_q_cf" 
                onblur="console.log('confirm_octopus:',confirm_octopus())" 
                onchange="console.log('confirm_octopus:',confirm_octopus())" 
                onkeypress="return isNumberKey(event)" 
                style="width:20%" 
                placeholder="括號內數字 Bracket number" 
                required 
                autocomplete="on"

                value="<?php
                    $cookie_name = "octopus_no_q_cf"."_rivergolf";
                    if(isset($_COOKIE[$cookie_name])) {
                        echo $_COOKIE[$cookie_name];
                    }
                 ?>" 
            onchange="
                setTimeout(function () {
                    setCookie(
                        '<?php echo $cookie_name; ?>', 
                        document.getElementById('octopus_no_q_cf').value, 
                        90
                    );
                },10);
            "
                ><b class="bracket">)</b>

            </b>


<style type="text/css">

.bracket {
    font-size: 1.5em;
}

@media only screen and (max-width: 1300px) {
.bracket {
    font-size: 3em;
}
}
</style>

        <script type="text/javascript">
            function confirm_octopus() {

                const checkbox = document.getElementById('octopus_block');
                // console.log('checkbox.checked:',checkbox.style.display,(checkbox.style.display=='none'));
                if ((checkbox.style.display=='none')) {
                    return true;
                }

                // Get the password inputs
                var octopus_no = document.getElementById('octopus_no');
                var octopus_no_cf = document.getElementById('octopus_no_cf');


                var octopus_no_q = document.getElementById('octopus_no_q');
                var octopus_no_cf_q = document.getElementById('octopus_no_q_cf');

                octopus_no_cf.style.backgroundColor = "white";

                if (octopus_no.value.trim() != "" && octopus_no.value === octopus_no_cf.value) {
                    octopus_no_cf.style.backgroundColor = "rgb(200,255,200)";
                } else {
                    octopus_no_cf.style.backgroundColor = "rgb(255,200,200)";
                }

                if (octopus_no_q.value.trim() != "" && octopus_no_q.value === octopus_no_cf_q.value) {
                    octopus_no_cf_q.style.backgroundColor = "rgb(200,255,200)";
                } else {
                    octopus_no_cf_q.style.backgroundColor = "rgb(255,200,200)";
                }

                if (octopus_no.value.trim() === "") {
                    return false;
                }
                if (octopus_no_q.value.trim() === "") {
                    return false;
                }


                if (octopus_no_cf.value.trim() === "") {
                    return false;
                }
                if (octopus_no_cf_q.value.trim() === "") {
                    return false;
                }

                // Check if passwords match
                if (octopus_no.value === octopus_no_cf.value && octopus_no_q.value === octopus_no_cf_q.value) {
                    // console.log('octopus_no match');
                    return true;
                } else {
                    // console.log('octopus_no do not match');
                    return false;
                }
            }











    </script>
    <script type="text/javascript">
                
    function with_vehecle() {
                
        var octopus_no = document.getElementById('octopus_no');
        var octopus_no_cf = document.getElementById('octopus_no_cf');


        var octopus_no_q = document.getElementById('octopus_no_q');
        var octopus_no_cf_q = document.getElementById('octopus_no_q_cf');

        const elementsToHide = document.getElementsByClassName('octopus_block');


        const checkbox = document.getElementById('with_vehicle');
        var has_octopus_discount = checkbox.checked;
        has_octopus_discount = <?php echo (!$has_octopus_discount?'false':'has_octopus_discount'); ?>;
        
        if (has_octopus_discount) {
            console.log('access with car');
            octopus_no.required = true;
            octopus_no_cf.required = true;
            octopus_no_q.required = true;
            octopus_no_cf_q.required = true;
            for (let i = 0; i < elementsToHide.length; i++) {
                elementsToHide[i].style.display = '';
            }
        } else {
            console.log('access without car');
            octopus_no.required = false;
            octopus_no_cf.required = false;
            octopus_no_q.required = false;
            octopus_no_cf_q.required = false;
            for (let i = 0; i < elementsToHide.length; i++) {
                elementsToHide[i].style.display = 'none';
            }
            octopus_no.value = '';
            octopus_no_cf.value = '';
            octopus_no_q.value = '';
            octopus_no_cf_q.value = '';
        }

    }

    function checked_with_vehicle() {
        var has_octopus_discount = document.getElementById('with_vehicle').checked;
        has_octopus_discount = <?php echo (!$has_octopus_discount?'false':'has_octopus_discount'); ?>;
        // todo
        // has_octopus_discount = false;
        if (has_octopus_discount) {
            document.getElementById('with_vehicle_span').innerHTML = '<div style="color: yellow;">已選 停車優惠<br>Parking offer selected</div>';
            document.getElementById('with_vehicle_span').style.backgroundColor = '#2196F3';
        } else {
            <?php if ($has_octopus_discount) { ?>
                document.getElementById('with_vehicle_span').innerHTML = '<div>停車優惠<br>Parking offer</div>';
            <?php } else { ?>
                // document.getElementById('with_vehicle_span').innerHTML = '<div>不提供停車優惠<br>No parking discount</div>';
                document.getElementById('with_vehicle_span').innerHTML = '<div style="color: red;">網站暫不提供泊車優惠，請到接待處換領 <br> Web-order CANNOT provide parking discount. <br> Please redeem at reception.</div>';
                document.getElementById('with_vehicle_span').style.display = 'none';
                document.getElementById('with_vehicle_span_space').style.display = 'none';
            <?php } ?>
                document.getElementById('with_vehicle_span').style.backgroundColor = 'white';
        }
        with_vehecle();
    }
    document.getElementById('with_vehicle').checked = true;
    checked_with_vehicle();

</script>




        </td>
    </tr>
    <tr>
        <td colspan="2">
            

<h3 style="background-color: white;"><hr> 預訂日期和時間 Booking date and time <hr></h3>


        </td>
    </tr>
















<?php

// Function to generate date string for the next 6 days (including today)
function getNextWeekDates() {
  $dates = [];
    // $dates[] = date('Y-m-d', strtotime("-1 days"));
  for ($i = 0; $i < 8; $i++) {
    $cursor = date('Y-m-d', strtotime("+$i days"));
    if ($cursor >= '2024-08-26') {
        $dates[] = $cursor;
    }
    
  }
  return $dates;
}

// Get next week dates
$dates = getNextWeekDates();

?>
    <tr><!-- 
        <th class="hide_for_mobile">
        </th> -->
        <td>
            <small> 預訂日期 Reservation date</small>
            <br>
*
<select name="booking_date" id="booking_date" onchange="setTimeout(function () {
check_datetime();show_and_hide_hours_2();show_and_hide_hours();
},10);" required>
     <optgroup>
    <?php foreach ($dates as $date): ?>
        <option value="<?php echo $date; ?>"><?php 
        $dateString = $date; // Example date string in "YYYY-MM-dd" format
        $dateObject = new DateTime($dateString);
        $dayOfWeekName = $dateObject->format('l'); // Returns the full textual representation of the day (e.g., "Tuesday")
        // echo "Day of the week (name): $dayOfWeekName"; // Output: Tuesday

        echo "$date ($dayOfWeekName)";
         ?></option>
    <?php endforeach; ?>
</optgroup>
</select>

        </td>
    </tr>

<?php 

$iternation = 1;
if ($half_hour_cluster) {
    $iternation = 0.5;
}
    
 ?>
    <tr>
<!--         <th class="hide_for_mobile">
</th> -->
<!-- 
Use javascript to create listener of the select-option "begin_hour" and set the "end_hour" have more than one than "begin_hour" when "begin_hour" have any change 
 -->

        <td>
<small>
開始時間 (小時) Starting time (Hour)    
</small>

<br>
            *
<style type="text/css">
    .half_hour_option {
        text-align: left;
    }
</style>

<?php 
function generate_hour_option($is_begin_hour,$half_hour, $is_monday, $must_half_hour)
{
    $last_hour_option = 21;
    if (!$is_begin_hour) {
        $last_hour_option = 22;
    }

    $prefix = 'b';
    if ($is_begin_hour) {
        $prefix = 'e';
    }
     ?>
    <?php for ($hour = 8; $hour <= $last_hour_option; $hour++): ?>
        <?php 
        $not_monday = false;
        $monday = false;
        if ($hour >= 8 && $hour <= 22) {
            $not_monday = true;
        }
        if ($hour >= 13 && $hour <= 22) {
            $monday = true;
        }
         ?>

        <?php if (
            ($is_begin_hour||(!$is_begin_hour && $hour != 8)) 
            && (($is_monday && $monday)||($not_monday && !$is_monday))
        ) { ?>
            <option 
                class="<?php echo ($not_monday?'not_monday ':''); echo ($monday?'monday ':''); ?>hour_opt <?php echo $prefix; ?>_hour_<?php echo $hour; ?>" 
                value="<?php echo $hour; ?>"
                ><?php echo $hour; ?>:00</option>
        <?php } ?>

        <?php if (
            ($must_half_hour && $hour != 22) || (
                $half_hour 
                && (($is_monday && $monday)||($not_monday && !$is_monday)) 
                && ($is_begin_hour||(!$is_begin_hour && $hour != 22))
            )
        ) { ?>
            <option 
                class="<?php echo ($not_monday?'not_monday ':''); echo ($monday?'monday ':''); ?>hour_opt <?php echo $prefix; ?>_hour_<?php echo $hour; ?> <?php echo $prefix; ?>_half_hour_<?php echo $hour; ?> half_hour_option" 
                value="<?php echo ($hour+0.5) ?>"
            ><?php echo $hour; ?>:30</option>
        <?php } ?>

  <?php endfor; ?>
<?php   
}
 ?>
<?php 

$begin_hour_placeholder = '<option value="" disabled selected>開始時間 Begin time</option>';
$and_hour_placeholder = '<option value="" disabled selected>完結時間 End time</option>';
// 請選擇 Please select a s
// $begin_hour_placeholder = "";
// $and_hour_placeholder = "";


 ?>

<div style="display: none;" id="BeginWholeHourMon">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, false , true, $must_half_hour); ?>
</div>

<div style="display: none;" id="BeginHalfHourMon">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, true, true, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndWholeHourMon">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, false, true, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndHalfHourMon">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, true, true, $must_half_hour); ?>
</div>


<div style="display: none;" id="BeginWholeHour">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, false, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="BeginHalfHour">
    <?php echo $begin_hour_placeholder; ?>
    <?php generate_hour_option(true, true, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndWholeHour">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, false, false, $must_half_hour); ?>
</div>

<div style="display: none;" id="EndHalfHour">
    <?php echo $and_hour_placeholder; ?>
    <?php generate_hour_option(false, true, false, $must_half_hour); ?>
</div>





<select name="begin_hour" id="begin_hour" onchange="check_datetime()" required> 
<optgroup id="begin_hour_group">
    <?php // generate_hour_option(true, false); ?>
</optgroup>
</select>


        </td>
    </tr>




    <tr><!-- 
        <th class="hide_for_mobile"></th> -->
        <td>
            <small>結束時間 (小時) End time (Hours)</small>

<br>
            *
<select name="end_hour" id="end_hour" onchange="check_datetime()" required> <optgroup id="end_hour_group">
    <?php // generate_hour_option(false, false); ?>
    </optgroup>
</select>


<script type="text/javascript">

function show_class(class_name) {
    // console.log('show_class: '+class_name);
    const elements = document.getElementsByClassName(class_name);
    for (const element of elements) {
        element.style.display = "";
    }
}
function hide_class(class_name) {
    // console.log('hide_class: '+class_name);
    const elements = document.getElementsByClassName(class_name);
    for (const element of elements) {
        element.style.display = "none";
    }
}

// hide_class('selection_area');
function hidePastHalfHourBlocksForEach(dateStr, elements, hourStr, minuteStr, now) {
    elements.forEach(el => {
        if (!dateStr) return;

        const elementTime = new Date(`${dateStr}T${hourStr}:${minuteStr}:00`);

        if (elementTime < now) {
            el.style.display = "none";
            el.disabled = true;
            el.hidden = true;
            console.log('Hiding past time block: ', el);
        }
    });
}
// hide_class('selection_area');
function showHourBlocksForEach(elements) {
    elements.forEach(el => {
        el.style.display = "";
        el.disabled = false;
        el.hidden = false;
    });
}

function hidePastHalfHourBlocks(dateStr) {
    console.log('BEGIN hidePastHalfHourBlocks: ',dateStr);
    var now = new Date("<?= date('Y-m-d H:i:s') ?>");

    console.log('NOW: ', now);

    // Loop 48 half-hour slots
    for (let i = 0; i < 48; i++) {
        const h = Math.floor(i / 2);
        const m = (i % 2 === 0) ? "00" : "30";

        const hourStr = String(h).padStart(2, "0");
        const minuteStr = m;

        // Map to your existing class names
        const className = (minuteStr === "00")
            ? `e_hour_${hourStr}`
            : `e_half_hour_${hourStr}`;
        // console.log('Checking class: ', className);
        const elements = document.querySelectorAll(`.${className}`);
        showHourBlocksForEach(elements);
        hidePastHalfHourBlocksForEach(dateStr, elements, hourStr, minuteStr, now);
    }
    console.log('END hidePastHalfHourBlocks: ',dateStr);
}


function show_and_hide_hours_2() {
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');
    // console.log('Show: begin_hour ',begin_hour.value);
    // console.log('Show: end_hour ',end_hour.value);

    const dateString = booking_date.value; // Example date string in "YYYY-MM-dd" format
    const dateParts = dateString.split("-"); // Split the string into year, month, and day parts
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]) - 1; // Months are zero-based (0 = January, 1 = February, etc.)
    const day = parseInt(dateParts[2]);

    const dateObject = new Date(year, month, day);
    const formattedDate = dateObject.toISOString().split('T')[0];
    // console.log('formattedDate:',formattedDate);


<?php 


require_once './account_variable.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "
    SELECT
        `holiday-date`
    FROM
        `applied-solar-holiday`
    WHERE 
    `holiday-date` 
        BETWEEN 
            DATE_ADD(NOW(), INTERVAL -100 DAY) 
        AND 
            DATE_ADD(NOW(), INTERVAL 100 DAY)
    GROUP BY `holiday-date`;
";

$recent_holiday_list = array();
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        array_push($recent_holiday_list, $row['holiday-date']);
    }
}
$conn->close();
 ?>
    const dayOfWeek = dateObject.getDay(); // Returns a number (0 for Sunday, 1 for Monday, etc.)
    const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var weekdayName = weekdays[dayOfWeek];


    var recent_holiday_list = <?php 
        echo json_encode($recent_holiday_list);
     ?>;
    console.log('booking_date.value:',booking_date.value);
    hidePastHalfHourBlocks(booking_date.value);
    if (recent_holiday_list.indexOf(booking_date.value) !== -1) {
        console.log('You\'re choosing holiday');
        weekdayName = 'Holiday';
    }
    var is_monday = (weekdayName=='Monday');
    // console.log('is_monday:',is_monday,'sand_bay_option_checked:',sand_bay_option_checked);


    begin_hour_group = document.getElementById('begin_hour_group');
    end_hour_group = document.getElementById('end_hour_group');

    console.log('sand_bay_option_checked',sand_bay_option_checked);
    // console.log('is_monday',is_monday);

    var begin_hour_name = 'Begin'
        +(sand_bay_option_checked?'Half':'Whole')
        +'Hour'
        +(is_monday?'Mon':'');
    var end_hour_name = 'End'
        +(sand_bay_option_checked?'Half':'Whole')
        +'Hour'
        +(is_monday?'Mon':'');
    // console.log('Show: Begin hour ',begin_hour_name);
    // console.log('Show: End hour ',end_hour_name);

    begin_hour_group.innerHTML =
    document.getElementById(begin_hour_name).innerHTML;
    begin_hour.value = '';

    end_hour_group.innerHTML =
    document.getElementById(end_hour_name).innerHTML;
    end_hour.value = '';

}


function setDisplayById(ele_id ,display) {
    var element = document.getElementById(ele_id);
    if (!element) {
        return;
    }
    if (display) {
        element.style.display = '';
    } else {
        element.style.display = 'none';
    }
}


function show_and_hide_hours() {
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;

    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');

    const dateString = booking_date.value; // Example date string in "YYYY-MM-dd" format
    const dateParts = dateString.split("-"); // Split the string into year, month, and day parts
    const year = parseInt(dateParts[0]);
    const month = parseInt(dateParts[1]) - 1; // Months are zero-based (0 = January, 1 = February, etc.)
    const day = parseInt(dateParts[2]);

    const dateObject = new Date(year, month, day);
    const dayOfWeek = dateObject.getDay(); // Returns a number (0 for Sunday, 1 for Monday, etc.)
    const weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    var weekdayName = weekdays[dayOfWeek];




    const formattedDate = dateObject.toISOString().split('T')[0];
    // console.log('formattedDate:',formattedDate);
    // console.log('booking_date.value:',booking_date.value);
    

    var recent_holiday_list = <?php 
        echo json_encode($recent_holiday_list);
     ?>;
    if (recent_holiday_list.indexOf(booking_date.value) !== -1) {
        console.log('You\'re choosing holiday');
        weekdayName = 'Holiday';
    }

    var is_monday = (weekdayName=='Monday');
    // console.log('is_monday:',is_monday,'sand_bay_option_checked:',sand_bay_option_checked);
    var is_half_hour = ( sand_bay_option_checked || <?php echo ($must_half_hour?"true":"false"); ?> );

    if (is_monday) {
        if (is_half_hour) {
            show_class('half_hour_option');
        }
        hide_class('not_monday');
        show_class('monday');
        // console.log('Show Monday');
        if (!is_half_hour) {
            hide_class('half_hour_option');
        }
    } else {
        if (is_half_hour) {
            show_class('half_hour_option');
        }
        hide_class('monday');
        show_class('not_monday');
        // console.log('Show other than monday');
        if (!is_half_hour) {
            hide_class('half_hour_option');
        }
    }









    if (sand_bay_option_checked) {
        document.getElementById('sand_bay_option_span').innerHTML = '<div style="color: yellow;">已選 沙地球道 <br> 半小時預訂<br> Sand Bay <br> Half Hour Booking Selected</div>';
        document.getElementById('sand_bay_option_span').style.backgroundColor = '#2196F3';


        setDisplayById('selection_VIP', false);
        setDisplayById('selection_sand', true);
        setDisplayById('selection_iron', false);

        setDisplayById('selection_short_wood', false);
        setDisplayById('selection_wood', false);
        
    } else {
        document.getElementById('sand_bay_option_span').innerHTML = '<div>沙地球道 <br> 半小時預訂<br> Sand Bay (Half Hour)'
        +'<br>'
        // +'<small style="color: red;">請雙擊 Please double click</small>'
        +'</div>';
        document.getElementById('sand_bay_option_span').style.backgroundColor = 'white';

        setDisplayById('selection_VIP', true);
        setDisplayById('selection_sand', false);
        setDisplayById('selection_iron', true);
        setDisplayById('selection_short_wood', true);
        setDisplayById('selection_wood', true);
    }

    selection_area();

}

setTimeout(show_and_hide_hours,100);

function getSelectedDateTime_begin() {
    var booking_date = document.getElementById('booking_date');
    var begin_hour = document.getElementById('begin_hour');

    if (begin_hour.options[begin_hour.selectedIndex] == undefined) {
        return undefined;
    }

    var begin_hour_root = Math.floor(begin_hour.value);
    var begin_hour_val = parseFloat(begin_hour.value);
    var is_begin_half = (begin_hour_val > begin_hour_root);
    var begin_hour_time_part = (begin_hour_root+'').padStart(2, '0') + (is_begin_half?":30":":00");
    var beginTime_str = booking_date.value+'T'+begin_hour_time_part;
    const beginTime = new Date(beginTime_str);

    if (begin_hour.value == '' || isNaN(beginTime)) {
        // console.log('beginTime_str',beginTime_str);
        return undefined;
    }

    return beginTime;
}

function getSelectedDateTime_end() {
    var booking_date = document.getElementById('booking_date');
    var end_hour = document.getElementById('end_hour');

    if (end_hour.options[begin_hour.selectedIndex] == undefined) {
        return undefined;
    }

    var end_hour_root = Math.floor(end_hour.value);
    var end_hour_val = parseFloat(end_hour.value);
    var is_end_half = (end_hour_val > end_hour_root);
    var end_hour_time_part = (end_hour_root+'').padStart(2, '0') + (is_end_half?":30":":00");
    var endTime_str = booking_date.value+'T'+end_hour_time_part;
    const endTime = new Date(endTime_str);

    if (end_hour.value == '' || isNaN(endTime)) {
        // console.log('endTime_str',endTime_str);
        return undefined;
    }

    return endTime;
}

function clearHours() {
    var begin_hour = document.getElementById('begin_hour');
    var end_hour = document.getElementById('end_hour');
    begin_hour.value = '';
    end_hour.value = '';
}

hide_class('selection_area');
function checkFutureDateTime() {
    checkFutureDateTime(false);

}

function isLessThanOneHourApart(dateA, dateB) {
  const diffMs = Math.abs(dateA - dateB); // Difference in milliseconds
  const oneHourMs = 60 * 60 * 1000;       // One hour in milliseconds
  return diffMs < oneHourMs;
}

function getHKDatetime() {
    const hongKongTime = new Intl.DateTimeFormat('en-GB', {
    timeZone: 'Asia/Hong_Kong',
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
    }).format(new Date());
    console.log('hongKongTime:',hongKongTime); // e.g., "28/08/2025, 17:11:00"
    // Split date and time
    const [datePart, timePart] = hongKongTime.split(', ');
    const [day, month, year] = datePart.split('/');
    const [hour, minute, second] = timePart.split(':');
    // Create Date object in Hong Kong time (UTC+8)
    const hongKongDate = new Date(Date.UTC(year, month - 1, day, hour - 8, minute, second));
    console.log(hongKongDate); // Native Date object in your local time zone
    return hongKongDate;
}
</script>

<script>
// Example usage:
const now = new Date();
const fortyFiveMinutesLater = new Date(now.getTime() + 45 * 60 * 1000);

console.log(isLessThanOneHourApart(now, fortyFiveMinutesLater)); // true

function checkFutureDateTime(justCheck) {
    var is_management = <?php echo ($is_management?'true':'false'); ?>;
    
    var current_timestamp = '<?php echo $current_timestamp; ?>';
    var futureOneHour_timestamp = '<?php echo $futureOneHour_timestamp; ?>';
    // console.log('current_timestamp:', current_timestamp);
    // console.log('futureOneHour_timestamp:', futureOneHour_timestamp);
    var currentDate = new Date(current_timestamp);
    var futureOneHourDate = new Date(futureOneHour_timestamp);

    var now;
    if (is_management || <?php echo ($must_half_hour?'true':'false')  ?> ) {
        // console.log('currentDate:', currentDate);
        now = currentDate;
    } else {
        // console.log('futureOneHourDate:', futureOneHourDate);
        now = futureOneHourDate;
    }

    const beginTime = getSelectedDateTime_begin();
    const endTime = getSelectedDateTime_end();


    if ( beginTime==undefined || endTime==undefined ) {
        if (justCheck) {
            return false;
        }
        return;
    }
    
    var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
    isLessThanOneHour = isLessThanOneHourApart(beginTime, endTime);
    if ( 
        beginTime >= now && endTime > now <?php 
        if ($must_half_hour) {
            // half hour system cannot play less than an hour 
            echo "&& (!isLessThanOneHour || sand_bay_option_checked)"; 
        }
         ?>
    ) {
        if (justCheck) {
            return true;
        }
    } else {
        if (justCheck) {
            return false;
        } else {
            if (isLessThanOneHour) {
                console.log('is less than an hour:');
                console.log('beginTime:'+beginTime);
                console.log('endTime:'+endTime);
                alert('請選擇一小時或更長。\n Please select one hour or more.');
            } else {
                alert('您選擇了不當的時間\nYou selected an inappropriate time.');
            }
            console.log('( = '+beginTime+' - '+endTime+' ) ');
            console.log('now:',now);
            console.log('beginTime:',beginTime);
            console.log('endTime:',endTime);
            clearHours();
        }
    }

}

function isValidDateTime() {
    return checkFutureDateTime(true);
}

function getTimeInTimezone() {
    const now = new Date();
    // now.setHours(now.getHours() + 1);
    return now;
}

function selection_area() {

    setTimeout(function () {
        var begin_hour = document.getElementById('begin_hour');
        var end_hour = document.getElementById('end_hour');
        // console.log('begin_hour.value: '+begin_hour.value);
        // console.log('end_hour.value: '+end_hour.value);
        if ( (begin_hour.value).length>0 && (end_hour.value).length>0 ) {
            show_class('selection_area');
            checkBookingRecord();
        } else {
            hide_class('selection_area');
        }
    }, 100);
    
}
selection_area();
function check_datetime() {
    selection_area();
    // show_and_hide_hours();
    checkFutureDateTime();
}

clearHours();





















            function updateInputState() {
                // console.log('updateInputState ');
                // show_and_hide_hours();

                const emailInput = document.getElementById('confirm_email'); // Replace with your input ID
                const confirmInput = document.getElementById('confirmation_code'); // Replace with your input ID
                if (checkEmailValidity(emailInput)) {
                    // confirmInput.readOnly = false;
                    confirmInput.disabled = false;
                    // confirmInput.style.backgroundColor = 'white';
                } else {
                    // confirmInput.readOnly = true;   
                    confirmInput.disabled = true;
                    confirmInput.style.backgroundColor = 'orange';
                }

                // check_sand_bay();
            }
            const checkInterval = setInterval(() => updateInputState(), 300);




</script>
    


        </td>
    </tr>













<tr
<?php 
if ($reserve_type == 'pickleball') {
?>
hidden
<?php 
}
 ?>
>
    <td>

<label class="container" style="text-align: center;">
    <input type="checkbox" name="sand_bay_option" id="sand_bay_option" onclick="
    check_sand_bay();
    show_and_hide_hours_2();
    show_and_hide_hours();
">
    <span id="sand_bay_option_span" class="span_checkbox checkmark widen_checkbox higher" 
    style="
        color: black;
        border-style: solid;
        border-radius:  20px;
        text-align: center;
    " onchange="setTimeout(function () {
        check_sand_bay();show_and_hide_hours();show_and_hide_hours_2();
    },10);">
    </span>

    
</label>

                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">
                        <br class="hide_for_mobile">

<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">
<br class="hide_for_desktop">

    </td>
</tr>
<script>
show_and_hide_hours_2();
</script>






















    <tr class="selection_area">
        <td colspan="2">


<h3 style="background-color: white;"><hr>

<?php 
if ($reserve_type == 'pickleball') {
?>
請選擇匹克球 預訂位置
<br>
Please select the courts you would like to reserve<hr>
<?php 
} else {
    
?>
請選擇高爾夫打球 預訂位置
<br>
Please select the courts you would like to reserve<hr>
<?php 
}
 ?>
</h3>

        </td>
    </tr>
    <tr>
        <td colspan="2">
            


























<table style="width: 100px;" class="selection_area">
    <tr><td colspan="2">
        

請先選擇您的<?php echo ($reserve_type == 'pickleball'?"球場":"球道"); ?>，然後按「提交」按鈕。<br>
Please select your court(s) and then click the Submit button. <br>
未選擇<?php echo ($reserve_type == 'pickleball'?"球場":"球道"); ?>時，提交按鈕將無法使用。<br>
When no court is selected, the submit button will not be visible.<br>
<hr>
    </td></tr>
    <tr><td style="width: 50px;"> 綠色（可預約）<br> Green (Available for reservation) </td><td style="background-color: #91FE69;width: 100px;"></td></tr>
    <tr><td style="width: 50px;"> 紅色（已預訂）<br> Red (Reserved) </td><td style="background-color: #FE8569;width: 100px;"></td></tr>
</table>

<hr>
<!-- 
<p style="color: red;">
            請不要選擇超過40<?php echo ($reserve_type == 'pickleball'?"個球場":"條球道"); ?>，否則將無法預訂<br>
            Please do not select more than 40 fairways or your reservation will not be available
</p> -->

<script>



complexArray = null;
function update_booking_record() {
                // console.log('update_booking_record ');
    fetch('./booking-status-json-variable.php?api&api_1') // Replace with your API endpoint
    .then(response => response.json()) // Parse the data as JSON
    .then(data => {
        if (complexArray == null) {
           // console.log(data); 
        }
        complexArray = data;
        // console.log(data); // Log the data
        // 
    })
    .catch(error => {
        console.error('Error:', error); // Log any errors
    });

}
update_booking_record();
const intervalId1 = setInterval(update_booking_record, 5*60*1000);













    document.getElementById("begin_hour").addEventListener("change", function() {
        const selectedBeginHour = parseFloat(this.value);

        var sand_bay_option_checked = document.getElementById('sand_bay_option').checked;
        document.getElementById("end_hour").value = selectedBeginHour + (sand_bay_option_checked?0.5:1);
    });












</script>













<!-- 

Use php to generate html, css to create a complex group of check box in a selection table, the first row shows 1 to 60 position numbers; the second row is a series of date from today to the following six dates (Total 7 days, show the date with format YYYY-MM-DD in the table cell); the third row is a series of hours from 09:00 am to 10:00 pm (Totally 13 hours - one table cell for each hour) with two hours text (the 24-hour formatted time, and the 12-hour formatted time), each hour is assigned an check box for html form.

 -->
    <style type="text/css">
        .c {
            text-align: center;
            border: 1px solid #ddd;
        }
    </style>
    <div class="booking-form">


<style type="text/css">
.navbar {
  background-color: #333; /* Black background color */
  position: fixed; /* Make it stick/fixed */
  top: 0; /* Stay on top */
  width: 100%; /* Full width */
  transition-duration: 1s;
}

/* Style the navbar links */
.navbar a {
  float: left;
  display: block;
  color: white;
  text-align: center;
  padding: 15px;
  text-decoration: none;
}

.navbar a:hover {
  background-color: #ddd;
  color: black;
}
</style>

<style type="text/css">
input[type=checkbox]
{
  margin:  30 px;
  
  padding: 20px;
  width: 100%;
}
td, th {
    vertical-align: top;
}
</style>
<?php 

require_once './position_list.php';

// $position_list_ = array(
//     //Sand
//     array(
//         1,2
//         // ,3
//     ),
//     // VIP
//     array(
//         "VIP"
//     ),
//     // Iron
//     array(
        
//         5,6,7,8,9,10,11,12,13,
//         15,16,
//         17,18,19,20,21,22,23,
//         25,26,
//         27,28,29,30,31,32,33,
//         35,
//         36,37,38,39,
//     ),
//     // Wood
//     array(
//         50,51,52,53,

//         55,56,57,
//         59,60,61,62,63,
//         65,66,67,68,69,70,71,72,73,

//         75,76,77,78,79,80,81,82,83
//         // ,84
//         ,85
//     ),
// );

?>



<style>
/* Customize the label (the container) */
.container {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    width: 90%;
}

/* Hide the browser's default checkbox */
.container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

/* Create a custom checkbox */
.checkmark {
    position: absolute;
    top: 0;
    left: 0;
    background-color: #eee;
/*    color: white;*/
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
    background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
    background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
    display: block;
}

.container .checkmark2:after {
    vertical-align: text-top;
    font-size: 30px;
}

.container .checkmark2:after {
    content: "已選取 Checked";
}
/* Style the checkmark/indicator */
.container .checkmark:after {
    vertical-align: text-top;
    font-size: 30px;
/*    left: 9px;
    top: 5px;
    width: 5px;
    border: solid white;
    border-width: 0 3px 3px 0;*/
/*    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);*/
}
</style>



<style type="text/css">
    .widen_checkbox {
        width: 100%;
    }
</style>
<?php

$spotTableVertical = function ($arr,$title="") use ($reserve_type)
{

    $ele_id = substr(md5($title), 0, 4);
?>




<script>
function toggleDiv<?php echo $ele_id; ?>() {
    var x = document.getElementById("myDiv<?php echo $ele_id; ?>");
    console.log(x.style.display);
    if (x.style.display === "none") {
        var elements = document.getElementsByClassName("expend_area");
        for(var i = 0; i < elements.length; i++){
            elements[i].style.display = "none";
        }
        x.style.display = "";
        x.focus();
    } else {
        var elements = document.getElementsByClassName("expend_area");
        for(var i = 0; i < elements.length; i++){
            elements[i].style.display = "none";
        }
        x.style.display = "none";
    }
}
</script>









<div onclick="toggleDiv<?php echo $ele_id; ?>()" class="expend" style="background-color: white;">
<hr>
    <h3><b><?php 
        echo $title; 
    
    ?></b></h3><br>
    <small>展開/隱藏 Expand/Hide</small><br>
<hr>
</div>

<table style="width: 100%;display: none;"  class="expend_area" id="myDiv<?php echo $ele_id; ?>">
    <tbody>
        <?php 
        // $min = 50;
        // $max = 69;
        // $max = 59;
        for ($i=0; $i < sizeof($arr); $i++) { 
            $p=$arr[$i];
            if (filter_var($p, FILTER_VALIDATE_INT) !== false) {
                $integer = (int) $p;
                // Check if the integer is within the specified range
                // if ($integer >= $min && $integer <= $max) {
                //     continue;
                // }
            }
         ?>
        <tr class="position position_<?php echo "$p"; ?>">
            <td class="c higher position position_<?php echo "$p"; ?>" ><?php 
            // echo $p;
    if ($reserve_type == 'pickleball') {
        if (filter_var($p, FILTER_VALIDATE_INT) !== false) {
            $num = (int)$p;
            $result = $num - 100 + 1;
            ?>
            <small>
                球場編號 Court No. <?php echo $result; ?>
            </small>
            <?php
        }
    } else {
        echo $p; 
    }
            ?></td>
            <th>



<label class="container">
    <input type="checkbox" class="position_checkbox position_<?php echo "$p"; ?>" id="position_<?php echo "$p"; ?>" name="p_selections[]" value="position_<?php echo "$p"; ?>" onclick="checkBookingRecord()">
    <span class="checkmark checkmark2 widen_checkbox higher"></span>
</label>






</th>
        </tr>
        <?php } ?>
    </tbody>
</table>
<?php
};


function spotTableHorizon($arr)
{
 ?>
<table>
    <tbody>
        <tr>
            <?php 
            for ($i=0; $i < sizeof($arr); $i++) { 
                $p=$arr[$i];
             ?>
            <th class="c"><?php echo $p; ?></th>
            <?php } ?>
        </tr>
        <tr>
            <?php 
            for ($i=0; $i < sizeof($arr); $i++) { 
                $p=$arr[$i];
             ?>
            <td class="c">
            <input type="checkbox" id="position_<?php echo "$p"; ?>" class="position" name="p_selections[]" value="position_<?php echo "$p"; ?>">
            </td>
            <?php } ?>
        </tr>
    </tbody>
</table>
<?php
}

 ?>

<table class="selection_area">
    <tbody>
        <?php
$bays_tr = function($id, $position_submenu,  $title) use ($reserve_type, $spotTableVertical) {
    ?>
        <tr id="<?php echo $id; ?>">
            <td>
<?php 
$spotTableVertical($position_submenu, title: $title);
 ?>
            </td>
        </tr>
    <?php
};
        ?>
<?php 
if (!$position_list_) {
    die();
}
$golf_bays_trs = function() use ($bays_tr, $position_list_) {
$bays_tr("selection_VIP", $position_list_[1], '貴賓室球道 VIP Room Bays');
$bays_tr("selection_sand", $position_list_[0], '沙地 球道 Sand court');
$bays_tr("selection_iron", $position_list_[2], '鐵桿球道 <br> Irons Only Bays');
$bays_tr("selection_short_wood", $position_list_[3], '鐵桿及球道木桿球道 <br> Irons to Fairway Woods Bays');
$bays_tr("selection_wood", $position_list_[4], '所有球桿球道 <br> All Clubs Bays');
};
?>












<?php 
if ($reserve_type == 'pickleball') {
    $bays_tr("selection_pickle_ball", $position_list_[5], '匹克球 <br> Pickleball');
} else {
    $golf_bays_trs();
}
 ?>

<script>
const expanded = document.querySelectorAll('.expend');
if (expanded.length === 1) {
    expanded[0].click();
}
</script>














    </tbody>
</table>








<hr>
<script type="text/javascript">
    
show_and_hide_hours();
</script>
</div>





























        </td>
    </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <b style="font-size: 1.5em;">備註 Remark</b>
                        <br>

<!-- 
                        如果您選擇正價，不需要備註。<br>
                        If you choose regular price, remark may not needed.<br>
                        <br>

                        <div style="color: red;">
                            如果您是學生，請輸入學校名稱。<br>
                            If you are student, please enter school name.<br>
                        <br>
                        </div>

                        <div style="color: purple;">
                        如果您是傷健人士，請輸入殘疾人士號碼。<br>
                        If you are disabled, please enter your disabilities identification.<br>
                        </div>
                        <br>

                        如果您無法提供任何相關信息，工作人員可能會在您抵達時詢問您。<br>
                        If you cannot provide any related information, the staff may ask you while you arrived.<br>
                        <br>
                        <br>
 -->
                        <br>
                        <textarea name="remark" style="
/*                        width: 400px;*/
                        width: 100%;
                        height: 300px;" placeholder="
備註 Remark


"></textarea>
                        <br>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td>
                        

<div
    onmouseover="notice_submitbutton()" 
    onclick="notice_submitbutton()" 
>
    
<input 
    type="submit" 
    style="width: 100%;" 
    class="submit-button" 
    value="提交 Submit" 
    onmousedown="update_booking_record();checkBookingRecord();"
    disabled
    >

</div>
<script type="text/javascript">
    function notice_submitbutton() {
        setTimeout(function() {
            send_confirm_code(true);
            setTimeout(function() {
                confirmationCodeValid = validateConfirmationCode();
                checkboxesValids = validateCheckboxes();
                dateTimeIsValid = isValidDateTime();
                octopusConfirm = confirm_octopus();
                
                if (checkNameEmpty()) {
                    alert("請輸入您的姓名。 \n Please enter your name.");
                } else if (!checkboxesValids) {
                    alert('請在提交前選擇球道 \n Please select a fairway before submitting ');
                } else if (!dateTimeIsValid) {
                    alert('請輸入目前時間之前的有效日期 \n Please enter a valid date before the current time ');
                } else if (!octopusConfirm) {
                    alert('請在輸入欄和確認輸入欄輸入有效的八達通卡號碼 \n Please enter a valid Octopus card number in the input field and confirmation input field ');
                } else if (!confirmationCodeValid) {
                    alert('請輸入正確的電子郵件確認碼 \n Please enter correct email confirmation code ');
                }
                console.log('checkboxesValids,dateTimeIsValid,octopusConfirm: ',checkboxesValids,dateTimeIsValid,octopusConfirm);
            }, 1);
        }, 1);
    }
</script>
<br>

<i><small style="color: grey;font-size: 0.6em;">提交前，請確保您的（1）電子郵件已確認並輸入正確的確認碼，（2）如果您想駕車進入高爾夫球場，請輸入八達通號碼並重複確認，以及（3）選擇球道。<br>
Before submission, please make sure your (1) email confirmed with correct confirmation code, (2) enter octupus number with repeat confirmation if you want to access the golf court with car, and (3) select the fairway.</small></i>

<hr>

</form>






</div>
    </div>

<script type="text/javascript">


    function validateConfirmationCode() {
        return confirmed;
        // const confirmation_code = document.getElementById('confirmation_code'); // Replace with your input ID
        // const confirmation_code_match = (confirmation_code.value.length == 6 && confirmed);
        // // console.log('confirmation_code && confirmation_code_match',confirmation_code, confirmation_code_match)


        // return confirmation_code_match;

    }
    function validateCheckboxes() {

        checked_discount();
        // checked_with_vehicle();

        const checkboxes = document.getElementsByClassName('position_checkbox');
        const atLeastOneChecked = Array.from(checkboxes).some((checkbox) => checkbox.checked);

        if (!atLeastOneChecked) {
            // Handle the case where no checkbox is checked (e.g., show an error message)
            // console.log("Please select at least one checkbox.");
        }






        return atLeastOneChecked;
    }

    function check_red(booking_date, hour_number, position_name) {
        if (complexArray == null) {
            // console.log('booking data not received yet');
            return;
        }
        // console.log('hour_number: ',hour_number);
        // console.log('position_name: ',position_name);
        i = hour_number;
        ii = position_name;
        const element_id = "position_"+ii;
        if (!document.getElementById(element_id)) {
            return;
        }
        // var is_problematic_position = (element_id == 'position_1' || element_id == 'position_2');

        var state_number = complexArray[booking_date][i+':00']['booking'][ii];
        // if (is_problematic_position) {
        //     console.log('Checking '+element_id+' '+state_number);
        // }

        if (state_number > 0) {
            // console.log('Set red to ',i,ii);
            document.getElementById(element_id).disabled = true;
            // document.getElementById(element_id).display = 'none';

            collection = document.getElementsByClassName(element_id);
            for (let i = 0; i < collection.length; i++) {
                collection[i].style.backgroundColor = "rgb(255,200,200)";
                collection[i].checked = false;
            }

            // console.log("disabled - "+element_id+' '+booking_date+' '+i+':00'+' '+'booking'+' '+ii);
        } else {

        }
    }

    function filterNonNumericCharactersById(id) {
        var ele = document.getElementById(id);
        var str = ele.value;
        ele.value = str.replace(/\D/g, '');
    }

    function checkBookingRecord() {
                // console.log('update_booking_record ');
        setTimeout(function() {
            checkConfirmCode(false);
        }, 1);


        // Get the password inputs
        filterNonNumericCharactersById('octopus_no');
        filterNonNumericCharactersById('octopus_no_cf');

        filterNonNumericCharactersById('octopus_no_q');
        filterNonNumericCharactersById('octopus_no_q_cf');
        filterNonNumericCharactersById('octopus_no_q_cf');
        filterNonNumericCharactersById('telephone');
        
        const booking_date = document.getElementById("booking_date").value;
        const begin_hour = parseInt(document.getElementById("begin_hour").value);
        const end_hour = parseInt(document.getElementById("end_hour").value);
        // console.log("check for - "+booking_date+' '+begin_hour+' '+end_hour);
        max_num = 200
        for (var ii = 1; ii <= max_num; ii++) {
            const element_id = "position_"+ii;
            if (!document.getElementById(element_id)) {
                continue;
            }
            document.getElementById(element_id).disabled = false;
            document.getElementById(element_id).display = 'block';
        }

        positionClass = document.getElementsByClassName('position');
        for (let i = 0; i < positionClass.length; i++) {
            positionClass[i].style.backgroundColor = "#A4FD51";
        }

                    // collection = document.getElementsByClassName(element_id);
                    // for (let i = 0; i < collection.length; i++) {
                    //     collection[i].style.backgroundColor = "white";
                    // }
        for (var i = begin_hour; i < end_hour; i++) {
            // console.log('level 1 '+i);
            for (var ii = 1; ii <= max_num; ii++) {
                check_red(booking_date, i, ii);
            }
            check_red(booking_date, i, 'VIP');
        }



        begin_hour_ = parseFloat(document.getElementById('begin_hour').value);
        end_hour_ = parseFloat(document.getElementById('end_hour').value);
        if (end_hour_ < begin_hour_) {
            alert('您不能選擇早於開始時間的結束時間。 \n You cannot choose the ending hour earlier than beginning hour.');
            document.getElementById('begin_hour').value = '';
            document.getElementById('end_hour').value = '';
        }

        checkFutureDateTime();
        confirmationCodeValid = validateConfirmationCode();
        checkboxesValids = validateCheckboxes();
        dateTimeIsValid = isValidDateTime();
        octopusConfirm = confirm_octopus();
        nameValid = !checkNameEmpty();

        // console.log('checkboxesValids,dateTimeIsValid,octopusConfirm: ',checkboxesValids,dateTimeIsValid,octopusConfirm);

        collection = document.getElementsByClassName('submit-button');
        for (let i = 0; i < collection.length; i++) {
            if (checkboxesValids&&dateTimeIsValid&&octopusConfirm&&confirmationCodeValid&&nameValid) {
                collection[i].style.color = 'black';
                collection[i].disabled = false;
            } else {
                collection[i].style.color = 'grey';
                collection[i].disabled = true;
            }
        }











    }

    // Create an interval that calls sayHello() every 2 seconds (2000 milliseconds)
    const intervalId = setInterval(checkBookingRecord, 1000);












// Cleaning previous page data
// Select all input elements and checkboxes
var inputs = document.querySelectorAll('input');

// Loop through the selected elements
for (var i = 0; i < inputs.length; i++) {
    // If it's a text input, clear the value
    // if (inputs[i].type == 'text') {
    //     inputs[i].value = '';
    // }

    // If it's a checkbox, uncheck it
    if (inputs[i].type == 'checkbox') {
        inputs[i].checked = false;
    }
}
document.getElementById('confirmation_code').value = '';

document.getElementById('confirmation_button').value = ' 確認電郵 Verify Email ';






</script>


<script type="text/javascript">
    

// document.write("Window width : " + window.innerWidth);
// document.write("Window height : " + window.innerHeight);


setTimeout(function () {
    alert('頁面逾時 \n Page timeout ');
    window.location.href = "./";
}, 15*60*1000);



</script>



                    </td>
                </tr>
            </table>

</body>
</html>
<?php
t_log('end[input-form.php]');
?>