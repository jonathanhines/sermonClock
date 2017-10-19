<!doctype html>
<html class="no-js" lang="">

<head>
 <meta charset="utf-8">
 <meta http-equiv="x-ua-compatible" content="ie=edge">
 <title>Control the Timer</title>
 <meta name="description" content="Sermon Clock">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="/css/style.css?v=2">
 <link href="https://fonts.googleapis.com/css?family=Cabin|Oswald" rel="stylesheet">
 <link href="/bower_components/droid-sans-mono/droidSansMono.css" rel="stylesheet">
 <script>
	 var sermonConfig={
		 apiBase: "/api",
		 timestep: {
			 display: 1,
			 server: 10
		 }
	 };
 </script>
</head>

<body class="admin">
 <!--[if lt IE 8]>
		 <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
 <![endif]-->
 <div class='container'><?php
//header('Content-type: application/json');
include("../config.php");
include("../tools/curl.php");

$serviceFilepath = '../data/service.json';
if( file_exists( $serviceFilepath )) {
  $oldServiceData = json_decode(file_get_contents($serviceFilepath),true);
} else {
  $oldServiceData = [];
}

$doSave = false;

$move = 0;
if(isset($_GET['move'])) {
	$move = intval($_GET['move']);
}

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

$display_times = array();

if(isset($_POST['activeItems'])) {
  $activeItems = $_POST['activeItems'];
  $doSave = true;
} else {
  $activeItems = [];
  if(isset($oldServiceData['items'])) {
    foreach($oldServiceData['items'] as $old_item) {
      $activeItems[] = $old_item['id'];
    }
  }
}

$storageServiceTimes = [];
foreach($times as $time) {
	if($time->attributes->time_type == "service") {
		$display_times[] = [
			"start_time_formatted" => date("g:i a",strtotime($time->attributes->starts_at)),
			"running_timestamp" => strtotime($time->attributes->starts_at)
		];
		$day_formated_string = date("F j, Y",strtotime($time->attributes->starts_at));
    $storageServiceTimes[] = strtotime($time->attributes->starts_at);
	}
}

echo "<h1>" . $plan->series_title . "</h1>";
echo "<h2>" . $plan->title . "</h2>";
echo "<h3>" . $day_formated_string . "</h3>\n";
echo "<p>";
	echo "<a href='?move=" . ( $move - 1 ) . "'>&lt; Previous</a>";
	echo " <a href='?move=" . ( $move + 1 ) . "'>Next &gt;</a>";
echo "</p>\n";
echo "<p>" . date("r", time()) . "</p>";
echo "<form method='post' action=''>";
echo "<table class='table'><tr>";
	echo "<th>Name</th>";
	echo "<th>Type</th>";
	echo "<th>Length</th>";
foreach($display_times as $display_time){
	echo "<th>" . $display_time['start_time_formatted'] . "</th>";
}
echo "<th>" . "<input onChange='toggleSelectAll()' id='selectAllItems' type='checkbox'>";
echo "</tr>";

foreach($items as $item) {
	if($item->attributes->service_position == "pre") {
		foreach($display_times as $i => $display_time) {
			$display_times[$i]['running_timestamp'] -= intval($item->attributes->length);
		}
	}
}

$storageItems = [];
foreach($items as $item) {
	if($item->attributes->service_position !== "post") {
		echo "<tr>";
		echo "<td>" . $item->attributes->title . "</td>";
		echo "<td>" . $item->attributes->item_type . "</td>";
		echo "<td>" . gmdate("i:s", intval($item->attributes->length) ) . "</td>";
    $startTimes = [];
		foreach($display_times as $i => $display_time) {
			echo "<td>" . date("g:i:s a",$display_time['running_timestamp']) . "</td>";
      $startTimes[$i] = $display_time['running_timestamp'];
			$display_times[$i]['running_timestamp'] += intval($item->attributes->length);
		}
    echo "<td>" . "<input class='itemCheckbox'" . (in_array($item->id, $activeItems) ? "checked='checked'" : "") . " type='checkbox' value='". $item->id . "' name='activeItems[]'>" . "</td>";
		echo "</tr>";
    if(in_array($item->id, $activeItems)) {
      $storageItems[] = [
        "id" => $item->id,
        "title" => $item->attributes->title,
        "type" => $item->attributes->item_type,
        "length" => intval($item->attributes->length),
        "startTimes" => $startTimes
      ];
    }
	}
}
echo "</table>";
echo "<input class='btn btn-success' type='submit' value='Save'>";
echo "</form>";

$storageItem = [
  "plan_id" => $plan_id,
  "series_title" => $plan->series_title,
  "plan_title" => $plan->title,
  "service_start_times" => $storageServiceTimes,
  "items" => $storageItems
];
if( $doSave ) {
  file_put_contents($serviceFilepath, json_encode($storageItem));
}

/*echo "_POST:<pre>" . print_r($_POST,true) . "</pre>";
echo "Storage:<pre>" . print_r($storageItem,true) . "</pre>";
echo "Storage:<pre>" . print_r($plan,true) . "</pre>";

//echo "Plans:<pre>" . print_r($result_plans,true) . "</pre>";
echo "Times:<pre>" . print_r($result_times,true) . "</pre>";
echo "Plan:<pre>" . print_r($result_plan,true) . "</pre>";
echo "Items:<pre>" . print_r($result_items,true) . "</pre>";*/

?></div>
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script>
  function toggleSelectAll() {
    var allSelected = $("#selectAllItems").prop('checked');
    $('.itemCheckbox').each(function() {
      $( this ).prop('checked', allSelected);
    });
  }
</script>
</body></html>
