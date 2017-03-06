<?php
  date_default_timezone_set ("America/Toronto");
  $timeFilepath = '../data/time.txt';
  $timeTarget = false;
  if(isset($_POST['targetTime'])) {
    $timeTarget = $_POST['targetTime'];
    file_put_contents($timeFilepath, $timeTarget);
  } elseif(file_exists($timeFilepath)) {
    $timeTarget = file_get_contents($timeFilepath);
  }
 ?><!doctype html>
<html class="no-js" lang="">

<head>
  <meta charset="utf-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title></title>
  <meta name="description" content="Sermon Clock">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/style.css">
  <link href="https://fonts.googleapis.com/css?family=Cabin|Oswald" rel="stylesheet">
</head>

<body class="admin">
  <!--[if lt IE 8]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
  <![endif]-->
  <div class='container'>
    <h1>Sermon Timer</h1>
    <form method="post" target="">
      <div class="form-group">
        <label for="targetTime">TargetTime</label>
        <input type="text" class="form-control" id="targetTime" placeholder="11:55am" name="targetTime"<?php
          if($timeTarget) {
            echo " value='$timeTarget'";
          }?>>
      </div>
      <button type="submit" class="btn btn-success">Submit</button>
    </form>
  </div>
  <script src="/bower_components/jquery/dist/jquery.min.js"></script>
  <script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
  <script src="/bower_components/jquery-textfill/source/jquery.textfill.min.js"></script>
  <script>
    __reloadServerUrl="ws://localhost:6001";
  </script>
  <script type="text/javascript" src="//localhost:6001/__reload/client.js"></script>
  <script type="text/javascript" src="/js/set.js"></script>
</body>

</html>
