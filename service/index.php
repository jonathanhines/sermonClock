<?php
/*
 * Includes
 */
require_once("../config.php");
require_once("../includes/serviceStorage.inc.php");
require_once("../includes/getServiceFromAPI.inc.php");
require_once("../includes/printTable.inc.php");
?><!doctype html>
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
 <link href="/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet">
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
 /*
  * Setup
  */
$oldServiceData = getStoredServiceData();

$doSave = false;

$move = 0;
if(isset($_GET['move'])) {
	$move = intval($_GET['move']);
}

// An active item will be counted down to.  An array of item id's are returned from pco
$activeItems = [];
if(isset($_POST['activeItems']) && is_array($_POST['activeItems'])) {
  foreach($_POST['activeItems'] as $item_id) {
    if(is_string($item_id) && preg_match('/^[a-z0-9]+$/i', $item_id)) {
      $activeItems[] = $item_id;
    }
  }
  $doSave = true;
} else {
  if(isset($oldServiceData['active_items'])) {
    $activeItems = $oldServiceData['active_items'];
  }
}

// Get the requested plan from the PCO api.
$serviceData = getServiceFromAPI($move);

// Append the local active items.
$serviceData['active_items'] = $activeItems;

// If the form was submitted, we can save the api results to a file.
$successMessage = false;
if( $doSave ) {
  if(putStoredServiceData($serviceData)) {
    $successMessage = "Successfully stored service plan";
  } else {
    $errorMessage = "Error saving service plan.";
  }
}

/*
 * Render the content.
 */
echo "<h1>" . $serviceData['series_title'] . "</h1>";
echo "<h2>" . $serviceData['plan_title'] . "</h2>";
echo "<h3>" . date("F j, Y",$serviceData['service_start_times'][0]) . "</h3>";
echo "<div class='previousNextControls'>";
	echo "<a href='?move=" . ( $move - 1 ) . "'><i class='fa fa-chevron-circle-left' aria-hidden='true'></i> Previous</a>";
	echo "<a href='?move=" . ( $move + 1 ) . "'>Next <i class='fa fa-chevron-circle-right' aria-hidden='true'></i></a>";
echo "</div>\n";

if($successMessage) {
  ?><div class="alert alert-success alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Success!</strong> <?php echo $successMessage; ?>
    </div><?php
}
if($errorMessage) {
  ?><div class="alert alert-danger alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <strong>Error: </strong> <?php echo $errorMessage; ?>
    </div><?php
}

printTable($serviceData, 'setup');
?></div>
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
  function toggleSelectAll() {
    var allSelected = $("#selectAllItems").prop('checked');
    $('.itemCheckbox').each(function() {
      $( this ).prop('checked', allSelected);
    });
  }
</script>
</body></html>
