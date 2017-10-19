<?php
require_once("../tools/curl.php");
function getServiceFromAPI($move) {
	$request = new sermonCurl('');
	$request->setName(PCO_APPLICATION_ID);
	$request->setPass(PCO_APPLICATION_SECRET);
	$request->useAuth(true);

	$target_plan_url = 'https://api.planningcenteronline.com/services/v2/service_types/' . SERVICE_TYPE_ID . '/plans?per_page=1';
	if($move >= 0) {
		$target_plan_url .= "&filter=future";
	} else {
		$target_plan_url .= "&filter=past&order=-sort_date";
	}

	if($move != 0) {
		$target_plan_url .= "&offset=" . abs($move);
	}

	$result_plans = json_decode($request->execute($target_plan_url));
	$this_plan = $result_plans->data[0];
	$plan_id = $this_plan->id;

	$result_plan = json_decode($request->execute('https://api.planningcenteronline.com/services/v2/service_types/' . SERVICE_TYPE_ID . '/plans/' . $plan_id));
	$result_items = json_decode($request->execute('https://api.planningcenteronline.com/services/v2/service_types/' . SERVICE_TYPE_ID . '/plans/' . $plan_id . '/items'));
	$result_times = json_decode($request->execute('https://api.planningcenteronline.com/services/v2/service_types/' . SERVICE_TYPE_ID . '/plans/' . $plan_id . '/plan_times'));

	$plan = $result_plan->data->attributes;
	$items = $result_items->data;
	$times = $result_times->data;

	// We've got our api responses, now to process them so that we have some usable data
	$running_times = array();

	// This is a list of when each service starts
	$serviceStartTimes = [];
	foreach($times as $time) {
		if($time->attributes->time_type == "service") {
			$running_times[] = strtotime($time->attributes->starts_at);
	    $serviceStartTimes[] = strtotime($time->attributes->starts_at);
		}
	}

	// If an item happens before the service starts move the start time back.
	foreach($items as $item) {
		if($item->attributes->service_position == "pre") {
			foreach($running_times as $i => $running_time) {
				$running_times[$i] -= intval($item->attributes->length);
			}
		}
	}

	// An item is one component of a service like a song or sermon.
	$serviceItems = [];
	foreach($items as $item) {
		if($item->attributes->service_position !== "post") {
	    $startTimes = [];
			foreach($running_times as $i => $running_time) {
	      $startTimes[$i] = $running_time;
				$running_times[$i] += intval($item->attributes->length);
			}
	    $serviceItems[] = [
	      "id" => $item->id,
	      "title" => $item->attributes->title,
	      "type" => $item->attributes->item_type,
	      "length" => intval($item->attributes->length),
	      "startTimes" => $startTimes
	    ];
		}
	}

	// This is the main data object that we will display and use.
	return [
	  "plan_id" => $plan_id,
	  "series_title" => $plan->series_title,
	  "plan_title" => $plan->title,
	  "service_start_times" => $serviceStartTimes,
	  "items" => $serviceItems
	];
}


?>
