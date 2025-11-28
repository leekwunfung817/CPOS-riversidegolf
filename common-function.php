<?php 


function pointToHalfHour($pointHour)
{
  $hour_int = ((int)$pointHour);
  if ($pointHour == $hour_int) {
    $half_hour_mark = ':00';
  } else {
    $half_hour_mark = ':30';
  }
  return $hour_int . $half_hour_mark;
}


 ?>