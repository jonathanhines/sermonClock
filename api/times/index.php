<?php
date_default_timezone_set ("America/Toronto");
//$target = strtotime('today 7:30pm');
$target = time() + 5;

$times = array(
  'current' => time(),
  'target' => $target
);

echo json_encode($times);
