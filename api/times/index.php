<?php
date_default_timezone_set ("America/Toronto");
$timeFilepath = '../../data/time.txt';
if(file_exists($timeFilepath)) {
  $timeTarget = file_get_contents($timeFilepath);
} else {
  $timeTarget = date('g:ia');
  file_put_contents($timeFilepath, $timeTarget);
}
// File should be a single line with a time of the format: '10:15am'
$target = strtotime('today ' . strtolower($timeTarget));

$times = array(
  'current' => time(),
  'target' => $target
);

echo json_encode($times);
