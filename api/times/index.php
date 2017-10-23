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

switch($_SERVER['REQUEST_METHOD']) {
  case "GET":

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
    break;

  case "POST":
    if(isset($_POST['activateItemID'])) {
      $service = getStoredServiceData();
      $activatedItemID = $_POST['activateItemID'];
      if(!preg_match('/^[a-z0-9]+$/i', $activatedItemID)) {
        header("500 Internal Server Error", true, 500);
        die( "Invalid item id" );
      }
      foreach($service['items'] as $item) {
        if($item['id']===$activatedItemID) {
          $state['mode'] = "service";
          $state['data'] = [
            'offset' => $item['startTimes'][0] - time()
          ];
          if( !putStoredStateData($state) ) {
            header("500 Internal Server Error", true, 500);
            die( "Could not store state" );
          } else {
            echo json_encode(['offset' => $state['data']['offset']]);
          }
        }
      }
    }
    break;

  default:
    header("501 Not Implemented", true, 501);
    die();
}
