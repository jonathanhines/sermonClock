<?php
  date_default_timezone_set ("America/Toronto");
  $timeFilepath = '../data/time.txt';
  $timeTarget = false;
  $successMessage = false;
  if(isset($_POST['targetTime'])) {
    $timeTarget = $_POST['targetTime'];
    file_put_contents($timeFilepath, $timeTarget);
    $successMessage = "Timer was updated, now set to $timeTarget";
  } elseif(isset($_POST['timeOffset'])) {
    $timeTarget = date('g:i:sa', time() + intval($_POST['timeOffset']) * 60);
    file_put_contents($timeFilepath, $timeTarget);
    $successMessage = "Timer was updated by adding " . intval($_POST['timeOffset']) . " minutes to the current time. Target time set to $timeTarget";
  } elseif(file_exists($timeFilepath)) {
    $timeTarget = file_get_contents($timeFilepath);
  }

  $blankFilepath = '../data/blank.txt';
  if(isset($_POST['isBlank'])) {
    $isBlank = $_POST['isBlank'];
    file_put_contents($blankFilepath, $isBlank);
    if($isBlank === "true") {
      $successMessage = "Timer was instructed to <strong>Go Dark</strong>";
    } else {
      $successMessage = "Timer was instructed to <strong>Go Live</strong>";
    }
  } elseif(file_exists($blankFilepath)) {
    $isBlank = file_get_contents($blankFilepath);
  }
 ?><!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title>Control the Timer</title>
  <meta name="description" content="Sermon Clock">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/css/style.css">
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
  <div class='container'>
    <h1>Sermon Timer</h1><?php
      if( $successMessage ) {
        ?><div class="alert alert-success alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Success!</strong> <?php echo $successMessage; ?>
</div><?php
      }
    ?>


    <div style="max-width:300px;">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs" id="formModeTabs" role="tablist">
        <li role="presentation" class="active"><a href="#offset" aria-controls="offset" role="tab" data-toggle="tab">Offset</a></li>
        <li role="presentation"><a href="#time" aria-controls="time" role="tab" data-toggle="tab">Set</a></li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="offset">
          <form method="post" target="">
            <div class="form-group">
              <label for="timeAddition">Time addition</label>
              <div class="input-group">
                <input type="number" class="form-control" id="timeAddition" name="timeOffset" placeholder="Email" value="35">
                <span class="input-group-addon">minutes</span>
              </div>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
          </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="time">
          <form method="post" target="">
            <div class="form-group">
              <label for="targetTime">Set Time</label>
              <input type="text" class="form-control" id="targetTime" placeholder="11:55am" name="targetTime"<?php
                if($timeTarget) {
                  echo " value='$timeTarget'";
                }?>>
            </div>
            <button type="submit" class="btn btn-success">Save</button>
          </form>
        </div>
      </div>
    </div>
    <hr>
    <form method="post" target="">
      <input type="hidden" name="isBlank" value="<?php echo $isBlank === "true" ? "false" : "true"; ?>">
      <button type="submit" class="btn btn-<?php echo $isBlank === "true" ? "info" : "danger";?>"><?php echo $isBlank === "true" ? "Go Live" : "Go Dark"; ?></button>
      <a href='/' class="btn btn-primary return-button">Back to timer</a>
    </form>

    <div id="mainDisplay" class="admin-preview"><span class="content"></span></div>

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
