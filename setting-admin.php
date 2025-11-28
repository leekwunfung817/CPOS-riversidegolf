<?php 

$half_hour_cluster = false;

$has_octopus_discount = false;

$management = true;

if (isset($_GET['email'])&&isset($_GET['auth'])) {

    if (md5($_GET['email'].'_cpospay')==$_GET['auth']) {
      $management = true;
    }
}


 ?><?php 
$must_half_hour = false;
if (isset($_GET['half_hour'])) {
    $must_half_hour = true;
    // echo "Half Hour Enabled <br>";
}
?>