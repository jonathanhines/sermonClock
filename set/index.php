<?php
  date_default_timezone_set ("America/Toronto");
  require_once("../includes/serviceStorage.inc.php");
  require_once("../includes/stateStorage.inc.php");
  require_once("../includes/printTable.inc.php");

  $state = getStoredStateData();

  $targetTime = false;
  $successMessages = [];
  $errorMessages = [];
  if(isset($_POST['targetTime'])) {
    $targetTime = $_POST['targetTime'];
    $state['mode'] = 'target';
    $state['data'] = [
      'targetTime' => $targetTime
    ];
    if( !putStoredStateData($state) ) {
      $errorMessages[] = "Unable to save time mode.";
    } else {
      $successMessages[] = "Timer was updated, now set to $targetTime";
    }
  } elseif(isset($_POST['timeOffset'])) {
    $targetTime = date('g:i:sa', time() + intval($_POST['timeOffset']) * 60);
    $state['mode'] = 'offset';
    $state['data'] = [
      'targetTime' => $targetTime
    ];
    if( !putStoredStateData($state) ) {
      $errorMessages[] = "Unable to save time mode.";
    } else {
      $successMessages[] = "Timer was updated by adding " . intval($_POST['timeOffset']) . " minutes to the current time. Target time set to $targetTime";
    }
  } elseif(isset($_POST['activateServiceMode'])) {
    $state['mode'] = 'service';
    // Start out assuming everything is on schedule and the server wallclock time is correct.
    $state['data'] = [
      'offset' => 0
    ];
    if( !putStoredStateData($state) ) {
      $errorMessages[] = "Unable to go into autopilot mode.";
    } else {
      $successMessages[] = "Autopilot engaged!";
    }
  } elseif($state['mode'] === 'service' ) {
    $targetTime = date('g:ia');
  } else {
    $targetTime = $state['data']['targetTime'];
  }

  if(isset($_POST['isBlank'])) {
    $isBlank = $_POST['isBlank'];
    if($isBlank === "true") {
      $state['isBlank'] = true;
    } else {
      $state['isBlank'] = false;
    }
    if( putStoredStateData($state) ) {
      if($state['isBlank']) {
        $successMessages[] = "Timer was instructed to <strong>Go Dark</strong>";
      } else {
        $successMessages[] = "Timer was instructed to <strong>Go Live</strong>";
      }
    } else {
      $errorMessages = "Unable to update blank state.";
    }
  }
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
  <div class='container'>
    <h1>Sermon Timer</h1><?php
      foreach($successMessages as $message) {
        ?><div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Success!</strong> <?php echo $message; ?>
</div><?php
      }
      foreach($errorMessages as $message) {
        ?><div class="alert alert-error alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Error:</strong> <?php echo $message; ?>
</div><?php
      }
    ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="formModeTabs" role="tablist">
      <li role="presentation" <?php if($state['mode'] === 'offset') { echo ' class="active"';} ?>><a href="#offset" aria-controls="offset" role="tab" data-toggle="tab"><i class="fa fa-circle<?php if($state['mode'] === 'offset') { echo ' active';} ?>" aria-hidden="true"></i> Offset</a></li>
      <li role="presentation" <?php if($state['mode'] === 'target') { echo ' class="active"';} ?>><a href="#time" aria-controls="time" role="tab" data-toggle="tab"><i class="fa fa-circle<?php if($state['mode'] === 'target') { echo ' active';} ?>" aria-hidden="true"></i> Set</a></li>
      <li role="presentation" <?php if($state['mode'] === 'service') { echo ' class="active"';} ?>><a href="#service" aria-controls="service" role="tab" data-toggle="tab"><i class="fa fa-circle<?php if($state['mode'] === 'service') { echo ' active';} ?>" aria-hidden="true"></i> Automatic</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane<?php if($state['mode'] === 'offset') { echo ' active';} ?>" id="offset">
        <div style="max-width:300px;">
          <form method="post" target="">
            <div class="form-group">
              <label for="timeAddition">Time addition</label>
              <div class="input-group">
                <input type="number" class="form-control" id="timeAddition" name="timeOffset" placeholder="Email" value="35">
                <span class="input-group-addon">minutes</span>
              </div>
            </div>
            <button type="submit" class="btn btn-success"><i class='fa fa-floppy-o' aria-hidden='true'></i> Save</button>
          </form>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane<?php if($state['mode'] === 'target') { echo ' active';} ?>" id="time">
        <div style="max-width:300px;">
          <form method="post" target="">
            <div class="form-group">
              <label for="targetTime">Set Time</label>
              <input type="text" class="form-control" id="targetTime" placeholder="11:55am" name="targetTime"<?php
                if($targetTime) {
                  echo " value='$targetTime'";
                }?>>
            </div>
            <button type="submit" class="btn btn-success"><i class='fa fa-floppy-o' aria-hidden='true'></i> Save</button>
          </form>
        </div>
      </div>
      <div role="tabpanel" class="tab-pane<?php if($state['mode'] === 'service') { echo ' active';} ?>" id="service">
        <div class="autopilotControls">
          <form method="post" target="">
            <?php if( $state['mode'] !== 'service') { ?>
              <button class="btn-cleared launch-action" type='submit' name="activateServiceMode"><i class="fa fa-rocket fa-2x" aria-hidden="true"></i></button>
            <?php } ?>
            <a class="settings-action" href="/service"><i class="fa fa-cog fa-2x" aria-hidden="true"></i></a>
          </form>
        </div>
        <?php
          $serviceData = getStoredServiceData();
          if($serviceData) {
            printTable($serviceData, "live");
          }
        ?>
      </div>
    </div>

    <hr>
    <form method="post" target="">
      <input type="hidden" name="isBlank" value="<?php echo $state['isBlank'] ? "false" : "true"; ?>">
      <button type="submit" class="btn btn-<?php echo $state['isBlank'] ? "info" : "danger";?>"><?php echo $state['isBlank'] ? "<i class='fa fa-sun-o' aria-hidden='true'></i>
 Go Live" : "<i class='fa fa-moon-o' aria-hidden='true'></i> Go Dark"; ?></button>
    </form>

    <div id="mainDisplay" class="admin-preview"><span class="content"></span><span id="currentItemTitle"></span><span id="targetItemTitle"></div>

  </div>
  <script src="/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <!--script src="/bower_components/jquery-textfill/source/jquery.textfill.min.js"></script>
  <script>
    __reloadServerUrl="ws://localhost:6001";
  </script>
  <script type="text/javascript" src="//localhost:6001/__reload/client.js"></script-->
  <script type="text/javascript" src="/js/set.js"></script>
  <script type="text/javascript" src="/js/app.js"></script>
</body>

</html>
