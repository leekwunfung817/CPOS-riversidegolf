


SELECT `id`

    ,`golf_cybersource`.auth_code auth_code
    ,`golf_cybersource`.req_card_number req_card_number

  ,`name`, `email`, `telephone`,  octopus_no, `booking_date`, `begin_hour`, `end_hour`, `p_selections`  ,concat(`golf_cybersource`.req_currency,' $',`golf_cybersource`.req_amount) amount  ,concat('HKD $',`golf-cash`.`amount`) cash   , concat('<a href=\"./payment-page/payment-confirm.php?auth=',golf_fairway_booking.`auth`,'&decision=ACCEPT&download=true\">Receipt</a>') `Link`  

    ,(case when (select count(*) > 0 from golf_confirmed_booking where golf_confirmed_booking.auth=golf_fairway_booking.auth) then 'T' else 'F' end) payment_confirmed
    ,(case when (select count(*) > 0 from `golf-carpark-check-in` where `golf-carpark-check-in`.auth=golf_fairway_booking.auth) then 'T' else 'F' end) carpark_checked_in
    ,(case when (select count(*) > 0 from `golf-fairway-check-in` where `golf-fairway-check-in`.auth=golf_fairway_booking.auth) then 'T' else 'F' end) fairway_checked_in
    ,(
      SELECT (case when COUNT(*)>0 then 'T' else 'F' end) 
      FROM `golf_confirmed_booking` 
      where `golf_confirmed_booking`.`auth`=`golf_fairway_booking`.`auth`
    ) email_confirmation_status
    ,(
      SELECT (case when COUNT(*)>0 then 'T' else 'F' end) 
      FROM `golf-payment-session` 
      where `golf-payment-session`.`auth`=`golf_fairway_booking`.`auth`
      and `payment-datetime` is not null
    ) golf_payment_status

    ,(case when 
      
      (select `payment-datetime` from `golf-payment-session` where `golf-payment-session`.auth=`golf_fairway_booking`.auth) is null
      and golf_fairway_booking.`timestamp`<DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 15 MINUTE)

      then 'Y'
      else 'N'

    end) booking_expired 
FROM golf_fairway_booking 
  left join `golf_cybersource` on `golf_cybersource`.`req_reference_number`=`golf_fairway_booking`.`auth`
   left join `golf-cash` on `golf-cash`.`auth`=`golf_fairway_booking`.`auth`   where `booking_date` >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%Y-%m-%d')


order by golf_fairway_booking.`id` desc
