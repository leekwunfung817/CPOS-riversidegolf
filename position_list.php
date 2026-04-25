<?php 

$position_list_group = array(
    "Sand",
    "VIP",
    "Iron",
    "Iron & Short Wood",
    "Wood",
    "Picle Ball"
);


$position_list_ = array(
    //Sand [0]
    array(
        1,2
        // ,3
    ),
    // VIP [1]
    array(
        "VIP"
    ),
    // Iron [2]
    array(
        
        5,6,7,8,9,10,11,12,13,
        15,16,
        17,18,19,20,21,22,23,
        25,26,
        27,28,29,

    ),
    // Iron and Short Wood [3]
    array(

        30,31,32,33,
        35,
        36,37,38,39,

        50,51,52,53,
        55,56,57,
        59,
    ),
    // Wood [4]
    array(


        60,61,62,63,
        65,66,67,68,69,
        
        70,71,72,73,

        75,76,77,78,79,80,81,82,83
        // ,84
        ,85
    ),
    // Picle Ball [5]
    array(
        100,101,102,103
    ),
);

$position_list = array();

foreach ($position_list_ as $key => $sublist) {
	foreach ($sublist as $key => $value) {
		$position_list[] = $value;
	}
}

// var_dump($position_list_);
// var_dump($position_list);

 ?>