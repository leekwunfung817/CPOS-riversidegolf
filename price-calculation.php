<?php 

require_once 'common-function.php';

function price_calculation($price_config, $booking_arr)
{
    require 'setting-admin.php';
    require 'account_variable.php';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $period = 'workday';

    $sql = "SELECT count(*) c FROM `applied-solar-holiday` WHERE `holiday-date`='".$booking_arr['booking_date']."';";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ( ( (double) $row['c'] ) > 0 ) {
                $period = 'holiday';
            }
        }
    }


    $date = $booking_arr['booking_date'];
    $dayOfWeek = date('w', strtotime($date));
    if ($dayOfWeek == 0 || $dayOfWeek == 6) {
        $period = 'holiday';
    }


    $identity = 'hourly';
    if ($booking_arr['discount'] == 'S') {
        $identity = 'student';
    } else if ($booking_arr['discount'] == 'D') {
        $identity = 'disabled';
    } else if ($booking_arr['discount'] == 'H') {
        $identity = 'hourly';
    }




    $begin_hour = (double) $booking_arr['begin_hour'];
    $end_hour = (double) $booking_arr['end_hour'];

    if ($price_config['print']=='Y') {
        ?>
<button onclick="show_price_calculation_steps('<?php echo $price_config['lan']; ?>')"><?php 
    echo ($price_config['lan']=='en' ? 
    "Price calculation (Hide/Show)" : 
        (
            $price_config['lan']=='zn' ? 
            "價格計算 (隱藏/顯示)" : 
            "價格計算 (隱藏/顯示)<br>Price calculation (Hide/Show) "
        ) 
    );
 ?></button>

<div style="display: none;" id="price_calculation_steps_<?php echo $price_config['lan']; ?>">

    <b><?php echo (
        $price_config['lan']=='en' ? "Price calculation" : (
            $price_config['lan']=='zn' ? "價格計算" : "價格計算 Price calculation"
            ) )." - ($period:$identity)"; ?></b>



    <ol><?php
    }

    // echo " $begin_hour to $end_hour ";
    
    $iternation = 1;
    $half_hour_cluster = (((int)$begin_hour)!=$begin_hour) || (((int)$end_hour)!=$end_hour);
    if ($half_hour_cluster) {
        $iternation = 0.5;
        // echo "half hour system<br>";
    }
    // echo $iternation;

    $p_selections = json_decode($booking_arr['p_selections']);

    $is_pickleball = false;

    $total_price = 0;
    for ($cursor_hour=$begin_hour; $cursor_hour < $end_hour; $cursor_hour=$cursor_hour+$iternation) {

        foreach ($p_selections as $key => $p_selection) {
            if ($p_selection == '1' || $p_selection == '2') {
                $iternation = 0.5;
            }
        }


        if ($price_config['print']=='Y') {
             ?><li><?php echo pointToHalfHour($cursor_hour); ?>-<?php echo pointToHalfHour($cursor_hour+$iternation); 
        }

        foreach ($p_selections as $key => $p_selection) {
            if (((int)$p_selection)>100 && ((int)$p_selection)<199) {
                $is_pickleball = true;
            }
            
            if ($price_config['print']=='Y') {
                echo ', <br>['.(
                    $price_config['lan']=='en' ?
                    "Position" : (
                        $price_config['lan']=='zn' ?
                        "打球位置" : "打球位置 Position"
                    ) 
                ).' '.$p_selection.': ';
            }









            $this_period = $period;
            $price = 0;
            $effective_date = null;


            $sql = "
SELECT 
    max(`effective-date`) `effective-date-max`
FROM (
    select * from golf_price
    union 
    select * from golf_price_2
) AS combined_tables
where `price-name`='".$p_selection."' 
and `identity`='".$identity."' 
and `period` like '$this_period' 
and `effective-date`<='$date'
order by `effective-date` desc
limit 1
; ";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $effective_date = $row['effective-date-max'];
                    break;
                }
            }


            if ($cursor_hour >= 19 && $period == 'holiday') {
                $this_period = 'holiday_19To22';


                // Special Period (Especially for VIP rest time)
                $sql = "
SELECT 
    max(period) period,
    max(price) price,
    max(`effective-date`) `effective-date-max`,
    min(`effective-date`) `effective-date-min`
FROM (
    select * from golf_price
    union 
    select * from golf_price_2
) AS combined_tables
where `price-name`='".$p_selection."' 
and `identity`='".$identity."' 
and `period` like '$this_period' 
and `effective-date`='$effective_date'
order by `effective-date` desc
limit 1
; ";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $price = (double) $row['price'];
                        $effective_date_max = $row['effective-date-max'];
                        $effective_date_min = $row['effective-date-min'];
                        break;
                    }
                }
            } else {
                $sql = "
SELECT 
    max(price) price ,
    max(`effective-date`) `effective-date-max`,
    min(`effective-date`) `effective-date-min`
FROM (
    select * from golf_price
    union 
    select * from golf_price_2
) AS combined_tables
where `price-name`='".$p_selection."' 
and `identity`='".$identity."' 
and `period`='$this_period'
and `effective-date`='$effective_date'
order by `effective-date` desc
limit 1
; ";            
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $price = (double) $row['price'];
                        $effective_date_max = $row['effective-date-max'];
                        $effective_date_min = $row['effective-date-min'];

                        break;
                    }
                }
            }
            if ($price_config['print']=='Y') {
                
                // if (true) {
                //     echo " ($effective_date_max to $effective_date_min) ";
                // }
                if ($this_period == 'holiday_19To22') {
                    echo ' (週末或假日晚上 Weekend or holiday evenings)';
                } else if ($this_period == 'holiday') {
                    echo ' (週末或假日 Weekends or holidays)';
                } else if ($this_period == 'workday') {
                    echo ' (平日 Workdays)';
                } else {
                    echo ' (特殊時段 Special period)';
                }
            }

            $accumulated_price = ($price * $iternation);
            $total_price = $total_price + $accumulated_price;
            if ($price_config['print']=='Y') {
                echo ' +$'.$accumulated_price.' ] ';
            }




















        }
        if ($price_config['print']=='Y') {
            // echo " = $$total_price";
             ?></li><?php
        }
    }




    if ($price_config['print']=='Y') {

    }

    if ($price_config['print']=='Y') {

         ?></ol>
        <small><a target="_blank" href="../price_display.php<?php echo $is_pickleball ? '?type=pickleball' : '?type=golf'; ?>"><?php 

        echo 
        
        ($price_config['lan']=='en' ? 
            "For details, please refer to price table" : (
                $price_config['lan']=='zn' ? 
                "詳細請參考 價格表" : "詳細請參考 價格表 For details, please refer to price table"
            )
        )
        ;

         ?> </a></small>

</div>
<hr>

<?php 

        $booking_arr_buf = $booking_arr;

        $credit_card_amount = $booking_arr_buf['credit_card_amount'];

        $is_credit_card = $credit_card_amount!=null;

        $unpaid_is_paid = $booking_arr_buf['unpaid_is_paid']=='1';
        if ($unpaid_is_paid) {
            $is_credit_card = false;
        }
        $cash_amount = $booking_arr_buf['cash_amount'];
        $unpaid_amount = $booking_arr_buf['unpaid_amount'];

        $is_cash = $cash_amount!=null;
        if ($unpaid_is_paid) {
            $is_cash = false;
        }


 ?>
<b><?php 
    if ($is_cash) {
        echo 
        ($price_config['lan']=='en' ? 
            "Total before change" : (
                $price_config['lan']=='zn' ? 
                "變更前總額" : "變更前總額 Total before change"
            )
        );
    } else {

        echo 
        ($price_config['lan']=='en' ? 
            "Total" : (
                $price_config['lan']=='zn' ? 
                "總額" : "總額 Total"
            )
        );
    }
    

    echo ": $ $total_price";
 ?>
<br>
<?php 

        $addition = floatval($booking_arr_buf['extra']);
        $percentage = floatval($booking_arr_buf['multiplied']);

        if (($is_cash || $unpaid_is_paid) && ($addition != 0 || $percentage != 100)) {


            echo '$'.$total_price." (";
            if ($price_config['lan']=='en') {
                if ($unpaid_is_paid) {
                    echo "Bank transfer or pay check";
                } else if ($is_cash) {
                    echo 'Cash';
                } else if ($is_credit_card) {
                    echo 'Credit Card';
                }
            } else if ($price_config['lan']=='zn') {
                if ($unpaid_is_paid) {
                    echo "銀行或支票轉帳";
                } else if ($is_cash) {
                    echo '現金';
                } else if ($is_credit_card) {
                    echo '信用卡';
                }
            }
            echo ") ";


            $amount = $total_price;

            if ($is_credit_card) {
                echo $booking_arr_buf['req_currency'].' $'.$credit_card_amount;
            } else if (($is_cash || $unpaid_is_paid)) {
                $cash_currency = $booking_arr_buf['cash_currency'];

                $auth_amount = ( $amount * ($percentage/100) ) + $addition;

                if ($percentage != 100) {
                    echo 'x'.$booking_arr_buf["multiplied"].'%';
                }
                
                if ($addition != 0) {
                    echo ' + $'.$booking_arr_buf['extra'];
                }
                
                echo ' = '
                    .$cash_currency.' $'.$auth_amount.'<br>';

                if (($is_cash && $auth_amount != $cash_amount) || ($unpaid_is_paid && $auth_amount != $unpaid_amount)) {
                    if ($price_config['lan']=='en') {
                        echo "If the price changed before this calculation, please request the management if confused.";
                    } else if ($price_config['lan']=='zn') {
                        echo "如在此計算之前價格已發生變化，有疑問請向管理人員詢問。";
                    }
                    // echo '<br><b style="color: red;">='.$cash_currency.' $'.$cash_amount;
                }
            }

        }

 ?>

</b>
<script type="text/javascript">
function show_price_calculation_steps(lan) {
  var x = document.getElementById("price_calculation_steps_"+lan);
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>
         <?php 
    }

    $conn->close();
    return $total_price;
}


 ?>