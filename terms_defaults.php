<?php
// Default terms dictionary. This file is intended to be included by
// `terms_booking.php` and may rely on `$has_octopus_discount` being defined
// by the including script (from setting-admin.php).

$terms_defaults = [];

$terms_defaults['pickleball'] = [
  'zh' => [
    [
      'title' => '預約',
      'items' => [
        '預約球場必須至少提前一小時透過線上系統辦理。閣下可預約未來7天內、1小時後的時段。',
        '預約時需繳付全額費用。我們接受Visa或Master Card付款，所有交易以港元（HK$）結算。',
      ],
    ],
    [
      'title' => '泊車優惠',
      'paragraphs' => [
        '所有顧客於預約時段內可享免費泊車優惠，詳情如下：預約一小時場地可享兩小時免費泊車；預約兩小時場地則享三小時免費泊車。',
      ],
    ],
    [
      'title' => '場地使用守則',
      'intro' => '任何人士進入球場必須遵守以下規則：',
      'items' => [
        '必須遵從球場工作人員的指示。',
        '球場範圍內禁止吸煙、賭博、大聲喧嘩或作出任何滋擾行為。',
        '非打球人士請勿進入或逗留於比賽場地範圍內。',
        '請僅使用您所預約的指定場地及時段，不得佔用其他場地或時段。預約時段結束後，不論是否有下一時段使用者等候，均須立即收拾個人物品並離開場地。',
        '12歲以下兒童必須由成年會員陪同方可進入及使用球場。',
        '未經球場管理方書面許可，不得在場內進行任何形式的收費匹克球教學活動。',
        '球場管理方保留權利，可因任何理由取消任何人士的使用資格。',
        '所有使用者必須穿著合適的運動服裝及運動鞋，並遵守與匹克球運動相關的所有安全規則及使用條款。',
        '比賽場地範圍內不得攜帶任何寵物。',
        '球場管理方有權隨時更新本使用守則，恕不另行通知。',
      ],
    ],
    [
      'title' => '免責聲明',
      'paragraphs' => [
        '使用本球場期間，如發生任何財物損失、人身傷害或意外，球場管理方概不負責。',
      ],
    ],
  ],
  'en' => [
    [
      'title' => 'Reservation',
      'items' => [
        'Reservations for pickleball courts must be made online at least one hour in advance; you can book a court from 1 hour up to 7 days ahead.',
        'Full payment is required at the time of reservation. We accept Visa or MasterCard, and all transactions are settled in Hong Kong dollars (HK$).',
      ],
    ],
    [
      'title' => 'Parking Discount',
      'paragraphs' => [
        'All customers are eligible for free parking during their booking period. For example, if you reserve a one-hour court session, you receive two hours of free parking; if you book a two-hour session, you get three hours of free parking.',
      ],
    ],
    [
      'title' => 'Pickleball Court Terms of Use',
      'intro' => 'Anyone entering the pickleball courts must comply with all relevant rules:',
      'items' => [
        'Agree to use the venue in accordance with the instructions of the court staff.',
        'Smoking, gambling, excessive noise, and any harassing behavior are strictly prohibited.',
        'Non-players are not allowed to remain inside the court area.',
        'Only reserved courts and times may be used. After your reservation ends, you must pack up your equipment and leave immediately, regardless of whether another player is waiting.',
        'Children under 12 years old must be accompanied by an adult member before they can enter and use the courts.',
        'Without prior permission from the court management, paid pickleball instruction is not allowed.',
        'The court reserves the right to cancel the membership of any person for any reason.',
        'All users must wear appropriate sports clothing and non-marking court shoes, and comply with all safety rules and conditions of use.',
        'No pets are allowed on the courts.',
        'The court reserves the right to update the venue usage rules from time to time.',
      ],
    ],
    [
      'title' => 'Disclaimer of Liability',
      'paragraphs' => [
        'The management of this sports ground shall not be held liable for any loss of property, personal injury, or accidents occurring during the use of the premises. All users acknowledge and accept that entry and participation are undertaken at their own risk.',
      ],
    ],
  ],
];

$terms_defaults['default'] = [
  'zh' => [
    [
      'title' => '預約',
      'items' => [
        '預約練習場必須最少提前一小時於網上辦理；閣下可以預約1小時後至未來7天內的球道練習。',
        '閣下於預約時須繳付全部費用。我們接受以Visa或Master Card付款。所有交易均以港元(HK$)結算。',
        '預約系統設有學生及傷健人士優惠。',
        '預約一但確認成功並扣款，一律不可取消，更改，或退款。',
        '如遇上惡劣天氣(黃雨，紅雨，黑雨及三號風球等)，閣下可於練習場繳費處辦理取消預約，每次取消收取每條球道港幣30元之行政費用。',
      ],
    ],
    [
      'title' => '泊車優惠',
      'items' => $has_octopus_discount
        ? [
          '練習場停車場提供泊車優惠($10/小時, 最多優惠3小時)，閣下需於網上預約時輸入八達通卡登記，並使用登記的八達通卡進入及離開停車場。',
          '請確保輸入之八達通卡號碼無誤，輸入錯卡號會導致收取正價停車場，並不會獲得退還。',
        ]
        : [
          '所有客户於預約時段內均可優待免費泊車優惠，例如：預約一小時球道可享有兩小時免費泊車，預約兩小時球道則有三小時免費泊車。',
        ],
    ],
    [
      'title' => '球道使用守則',
      'intro' => '任何人士進入練習場必須遵守所有相關守則：',
      'items' => [
        '同意依照練習場職員指示使用場地。',
        '練習場範圍內不准吸煙、賭博、喧嘩，以及作出其他任何騷擾行為。',
        '非打球者不得逗留球道範圍。',
        '除預約指定球道及時間外，不可佔用其他球道及時間。預約時間完結後，不論是否有下一節使用者等候，閣下都須即時收拾球具並離開球道。',
        '12歲以下之兒童必須有成人會員陪同方可進入及使用打球道。',
        '未得練習場准許，不得在場內進行收取學費之高爾夫球教學。',
        '練習場不論任何理由，有權取消任何人士會員身份。',
        '所有使用者均須穿着合適的服裝和運動鞋，以及遵守有關運動／活動的所有安全規則和使用條件。',
        '球道範圍不得携帶任何寵物。',
        '練習場有權不定時更新場地使用守則。',
      ],
    ],
    [
      'title' => '免責條款',
      'paragraphs' => [
        '使用本場地期間，如發生任何財物損失、人身傷害或意外，管理方概不負責。',
      ],
    ],
  ],
  'en' => [
    [
      'title' => 'Reservation',
      'items' => [
        'Reservation of the driving range must be made online at least one hour in advance; you can make an appointment for fairway practice from 1 hour to the next 7 days.',
        'You must pay the full fee when making the reservation. We accept payment by Visa or Master Card. All transactions are settled in Hong Kong dollars (HK$).',
        'The reservation system provides discounts for students and people with disabilities.',
        'Once the reservation is confirmed and the payment is deducted, it cannot be canceled, changed, or refunded.',
        'In the event of severe weather (yellow rain, red rain, black rain, typhoon signal No. 3, etc.), you can cancel your reservation at the driving range payment office. An administrative fee of HK$30 per fairway will be charged for each cancellation.',
      ],
    ],
    [
      'title' => 'Parking Discount',
      'items' => $has_octopus_discount
        ? [
          'The driving range parking lot provides parking discount ($10/hour, maximum discount offer for 3 hours). You need to enter your Octopus card to register when making an online reservation, and use the registered Octopus card to enter and leave the parking lot.',
          'Please ensure that the Octopus card number entered is correct. Entering an incorrect card number will result in full price parking charges and will not be refunded.',
        ]
        : [
          'All customers are eligible for free parking during their booking period. For instance, if you reserve a one-hour bay, you receive two hours of free parking, and if you book a two-hour bay, you get three hours of free parking.',
        ],
    ],
    [
      'title' => 'Driving Range Terms of Use',
      'intro' => 'Anyone entering the driving range must comply with all relevant rules:',
      'items' => [
        'Agree to use the venue in accordance with the instructions of the driving range staff.',
        'Smoking, gambling, making noise, and any other harassing behavior are not allowed within the driving range.',
        'Non-players are not allowed to stay within the fairway.',
        'Except for the reserved fairways and times, other fairways and times are not allowed. After the reservation time is over, you must pack up your golf equipment and leave the fairway immediately, regardless of whether there are other users waiting for the next session.',
        'Children under 12 years old must be accompanied by an adult member before they can enter and use the golf course.',
        'Without permission from the driving range, golf instruction for which tuition is charged is not allowed.',
        'The driving range reserves the right to cancel the membership of any person for any reason.',
        'All users are required to wear appropriate clothing and sports shoes, and comply with all safety rules and conditions of use related to the sport/activity.',
        'No pets are allowed on the fairways.',
        'The driving range reserves the right to update the venue usage rules from time to time.',
      ],
    ],
    [
      'title' => 'Disclaimer',
      'paragraphs' => [
        'The management of this sports ground shall not be held liable for any loss of property, personal injury, or accidents occurring during the use of the premises. All users acknowledge and accept that entry and participation are undertaken at their own risk.',
      ],
    ],
  ],
];

return;

?>
