<?php 

session_start();
$_SESSION['type'] = $_GET['type'];
session_write_close();





require_once 'setting-admin.php';

?>
<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>條款及細則</title>
<style>
  body { font-family: Arial, sans-serif; line-height: 1.6; }
  .container { width: 80%; margin: auto; overflow: hidden; }
  header { background: #50b3a2; color: white; padding: 20px; text-align: center; }
  section { margin: 20px 0; }
  h2 { color: #333; }
</style>
</head>
<body>
<header>
  <h1>條款及細則</h1>
</header>

<hr>
<div class="container">

<?php if ($_SESSION['type'] == 'pickleball') { ?>

<table>
  <tr>
    <td>
      <section>
        <h2>免責聲明</h2>
        <p>

<h2>預約</h2>
1. 預約球場必須至少提前一小時透過線上系統辦理。閣下可預約未來7天內、1小時後的時段。<br>
2. 預約時需繳付全額費用。我們接受Visa或Master Card付款，所有交易以港元（HK$）結算。<br>
3. 預約一經確認及扣款，恕不接受取消、更改或退款。<br>
4. 如遇惡劣天氣（例如：黃色、紅色或黑色暴雨警告，三號或以上熱帶氣旋警告信號），閣下可於球場服務處辦理取消預約，每次取消將收取每片場地港幣30元的行政手續費。<br>
<br>

<h2>泊車優惠</h2>
所有顧客於預約時段內可享免費泊車優惠，詳情如下：預約一小時場地可享兩小時免費泊車；預約兩小時場地則享三小時免費泊車。<br>
<br>

<h2>場地使用守則</h2>
任何人士進入球場必須遵守以下規則：<br>
1. 必須遵從球場工作人員的指示。<br>
2. 球場範圍內禁止吸煙、賭博、大聲喧嘩或作出任何滋擾行為。<br>
3. 非打球人士請勿進入或逗留於比賽場地範圍內。<br>
4. 請僅使用您所預約的指定場地及時段，不得佔用其他場地或時段。預約時段結束後，不論是否有下一時段使用者等候，均須立即收拾個人物品並離開場地。<br>
5. 12歲以下兒童必須由成年會員陪同方可進入及使用球場。<br>
6. 未經球場管理方書面許可，不得在場內進行任何形式的收費匹克球教學活動。<br>
7. 球場管理方保留權利，可因任何理由取消任何人士的使用資格。<br>
8. 所有使用者必須穿著合適的運動服裝及運動鞋，並遵守與匹克球運動相關的所有安全規則及使用條款。<br>
9. 比賽場地範圍內不得攜帶任何寵物。<br>
10. 球場管理方有權隨時更新本使用守則，恕不另行通知。<br>

<br>

    </p>
  </section>

<a href="./input-form.php">
<header style="
cursor: pointer;
">
  <h1>同意並繼續</h1>
</header>
</a>


    </td>
  </tr>
  <tr>
    <td>

<section>
 <h2>Disclaimer</h2>
 <p>

<h2>Reservation</h2>
1. Reservations for pickleball courts must be made online at least one hour in advance; you can book a court from 1 hour up to 7 days ahead. <br>
2. Full payment is required at the time of reservation. We accept Visa or MasterCard, and all transactions are settled in Hong Kong dollars (HK$). <br>
3. Once a reservation is confirmed and payment is processed, it cannot be canceled, changed, or refunded. <br>
4. In the event of severe weather (yellow rain, red rain, black rain, typhoon signal No. 3, etc.), you may cancel your reservation at the court's payment office. An administrative fee of HK$30 per court will be charged for each cancellation. <br>
<br>

<h2>Parking Discount</h2>
All customers are eligible for free parking during their booking period. For example, if you reserve a one-hour court session, you receive two hours of free parking; if you book a two-hour session, you get three hours of free parking. <br>
<br>

<h2>Pickleball Court Terms of Use</h2>
Anyone entering the pickleball courts must comply with all relevant rules:<br>
1. Agree to use the venue in accordance with the instructions of the court staff. <br>
2. Smoking, gambling, excessive noise, and any harassing behavior are strictly prohibited. <br>
3. Non-players are not allowed to remain inside the court area. <br>
4. Only reserved courts and times may be used. After your reservation ends, you must pack up your equipment and leave immediately, regardless of whether another player is waiting. <br>
5. Children under 12 years old must be accompanied by an adult member before they can enter and use the courts. <br>
6. Without prior permission from the court management, paid pickleball instruction is not allowed. <br>
7. The court reserves the right to cancel the membership of any person for any reason. <br>
8. All users must wear appropriate sports clothing and non-marking court shoes, and comply with all safety rules and conditions of use. <br>
9. No pets are allowed on the courts. <br>
10. The court reserves the right to update the venue usage rules from time to time. <br>
<br>

 </p>
 </section>


    </td>
  </tr>
</table>

<?php } else { ?>

<table>
  <tr>
    
    <td>
      
  <section>
    <h2>免責條款</h2>
    <p>

<h2>預約</h2>
1.  預約練習場必須最少提前一小時於網上辦理；閣下可以預約1小時後至未來7天內的球道練習。<br>
2.  閣下於預約時須繳付全部費用。我們接受以Visa或Master Card付款。所有交易均以港元(HK$)結算。<br>
3.  預約系統設有學生及傷健人士優惠。<br>
4.  預約一但確認成功並扣款，一律不可取消，更改，或退款。<br>
5.  如遇上惡劣天氣(黃雨，紅雨，黑雨及三號風球等)，閣下可於練習場繳費處辦理取消預約，每次取消收取每條球道港幣30元之行政費用。<br>
<br>

<h2>泊車優惠</h2>
<?php if ($has_octopus_discount) { ?>
1.  練習場停車場提供泊車優惠($10/小時, 最多優惠3小時)，閣下需於網上預約時輸入八達通卡登記，並使用登記的八達通卡進入及離開停車場。<br>
2.  請確保輸入之八達通卡號碼無誤，輸入錯卡號會導致收取正價停車場，並不會獲得退還。<br>
<?php } else { ?>
所有客户於預約時段內均可優待免費泊車優惠，例如：預約一小時球道可享有兩小時免費泊車，預約兩小時球道則有三小時免費泊車。 <br>
<?php } ?>


<br>

<h2>球道使用守則</h2>

任何人士進入練習場必須遵守所有相關守則:<br>
1. 同意依照練習場職員指示使用場地。<br>
2. 練習場範圍內不准吸煙、賭博、喧嘩，以及作出其他任何騷擾行為。<br>
3. 非打球者不得逗留球道範圍。<br>
4. 除預約指定球道及時間外，不可佔用其他球道及時間。預約時間完結後，不論是否有下一節使用者等候，閣下都須即時收拾球具並離開球道。<br>
5. 12歲以下之兒童必須有成人會員陪同方可進入及使用打球道。<br>
6. 未得練習場准許，不得在場內進行收取學費之高爾夫球教學。<br>
7. 練習場不論任何理由，有權取消任何人士會員身份。<br>
8. 所有使用者均須穿着合適的服裝和運動鞋，以及遵守有關運動／活動的所有安全規則和使用條件。<br>
9. 球道範圍不得携帶任何寵物。<br>
10. 練習場有權不定時更新場地使用守則。<br>

<br>

    </p>
  </section>

<a href="./input-form.php">
<header style="
cursor: pointer;
">
  <h1>同意並繼續</h1>
</header>
</a>


    </td>
  </tr>
  <tr>
    <td>
      

    </td>
  </tr>
  <tr>
    <td>

<section>
 <h2>Disclaimer</h2>
 <p>

<h2>Reservation</h2>
1. Reservation of the driving range must be made online at least one hour in advance; you can make an appointment for fairway practice from 1 hour to the next 7 days. <br>
2. You must pay the full fee when making the reservation. We accept payment by Visa or Master Card. All transactions are settled in Hong Kong dollars (HK$). <br>
3. The reservation system provides discounts for students and people with disabilities. <br>
4. Once the reservation is confirmed and the payment is deducted, it cannot be canceled, changed, or refunded. <br>
5. In the event of severe weather (yellow rain, red rain, black rain, typhoon signal No. 3, etc.), you can cancel your reservation at the driving range payment office. An administrative fee of HK$30 per fairway will be charged for each cancellation. <br>
<br>

<h2>Parking discount</h2>
<?php if ($has_octopus_discount) { ?>
1. The driving range parking lot provides parking discount ($10/hour, maximum discount offer for 3 hours). You need to enter your Octopus card to register when making an online reservation, and use the registered Octopus card to enter and leave the parking lot. <br>
2. Please ensure that the Octopus card number entered is correct. Entering an incorrect card number will result in full price parking charges and will not be refunded. <br>
<?php } else { ?>
All customers are eligible for free parking during their booking period. For instance, if you reserve a one-hour bay, you receive two hours of free parking, and if you book a two-hour bay, you get three hours of free parking. <br>
<?php } ?>

<br>

<h2>Driving Range Terms of Use</h2>Anyone entering the driving range must comply with all relevant rules:<br>
1. Agree to use the venue in accordance with the instructions of the driving range staff. <br>
2. Smoking, gambling, making noise, and any other harassing behavior are not allowed within the driving range. <br>
3. Non-players are not allowed to stay within the fairway. <br>
4. Except for the reserved fairways and times, other fairways and times are not allowed. After the reservation time is over, you must pack up your golf equipment and leave the fairway immediately, regardless of whether there are other users waiting for the next session. <br>
5. Children under 12 years old must be accompanied by an adult member before they can enter and use the golf course. <br>
6. Without permission from the driving range, golf instruction for which tuition is charged is not allowed. <br>
7. The driving range reserves the right to cancel the membership of any person for any reason. <br>
8. All users are required to wear appropriate clothing and sports shoes, and comply with all safety rules and conditions of use related to the sport/activity. <br>
9. No pets are allowed on the fairways. <br>
10. The driving range reserves the right to update the venue usage rules from time to time. <br>
<br>

 </p>
 </section>


    </td>
  </tr>
</table>

<br>
<a href="./input-form.php">
<header style="
cursor: pointer;
">
  <h1>Agree and continue</h1>
</header>
</a>

<?php } ?>

<br>
<br>
<br>
<br>
<br>
<hr>

<footer>
  <p>版權所有 © 2024 白石高球練習場有限公司</p>
</footer>
</body>
</html>
