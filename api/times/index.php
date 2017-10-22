<?php
date_default_timezone_set ("America/Toronto");
require_once("../../includes/stateStorage.inc.php");
require_once("../../includes/serviceStorage.inc.php");

$state = getStoredStateData();
if(!$state) {
  $state = [
    'blank' => false,
    'mode' => 'target',
    'data' => [
      'targetTime' => date('g:ia')
    ]
  ];
  putStoredStateData($state);
}

switch($state['mode']) {
  case 'service':
    // Get current position in the service (time + offset)
    $currentPosition = time() + $state['data']['offset'];

    // Get the current service data
    $service = getStoredServiceData();
    $serviceCount = count($service['service_start_times']);

    $targetTime = 0;
    $nextTime = 0;

    // Walk through the service items to find the upcoming and next targets
    for($serviceIndex = 0; $serviceIndex < $serviceCount; ++$serviceIndex) {
      foreach($service['items'] as $item) {
        if($item['startTimes'][$serviceIndex] > $currentPosition && in_array($item['id'], $service['active_items'])) {
          // We found a future active item
          if( $targetTime === 0 ) {
            $targetTime = $item['startTimes'][$serviceIndex];
            continue;
          }

          if( $nextTime === 0 ) {
            $nextTime = $item['startTimes'][$serviceIndex];
            break;
          }
        }
      }
      if($nextTime !== 0) {
        // Both the target and next times have been found, no need to continue;
        break;
      }
    }

    $result = array(
      'current' => $currentPosition,
      'target' => $targetTime,
      'next' => $nextTime,
      'isBlank' => $state['isBlank']
    );
    break;

  default:
    // File should be a single line with a time of the format: '10:15am'
    $targetTime = strtotime('today ' . strtolower($state['data']['targetTime']));
    $result = array(
      'current' => time(),
      'target' => $targetTime,
      'isBlank' => $state['isBlank']
    );
    break;
}

echo json_encode($result);
