(function($,config){
  var currentTime, targetTime, timeoutHandle;
  function getTimes() {
    $.ajax({
      url: config.apiBase + '/times',
      dataType: 'json',
      success: function(data, status) {
        currentTime = data.current;
        targetTime = data.target;

        // Restart displat update
        clearTimeout(timeoutHandle);
        updateDisplay();
      }
    });
    setTimeout(getTimes, config.timestep.server * 1000);
  }

  function updateDisplay() {
    var timeRemaining = targetTime - currentTime;
    if( timeRemaining < 0 ) {
      timeRemaining = timeRemaining * -1;
      $("#mainDisplay").addClass('timeExpired');
    } else {
      $("#mainDisplay").removeClass('timeExpired');
    }
    $("#mainDisplay .content").html(formatDisplayTime(timeRemaining));
    /*$("#mainDisplay").textfill({
      maxFontPixels: -1
    });*/
    currentTime = currentTime + config.timestep.display;
    timeoutHandle = setTimeout(updateDisplay, config.timestep.display * 1000);
  }
  getTimes();

})(jQuery,sermonConfig);
function pad(num) {
    return ("0"+num).slice(-2);
}
function formatDisplayTime(secs) {
  var minutes = Math.floor(secs / 60);
  secs = secs%60;
  var hours = Math.floor(minutes/60)
  minutes = minutes%60;
  //return pad(hours)+":"+pad(minutes)+":"+pad(secs);
  //return pad(hours)+":"+pad(minutes);
  //return minutes+":"+pad(secs);
  return hours+":"+pad(minutes);
}
