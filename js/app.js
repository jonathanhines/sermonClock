//(function($,config){
  var currentTime, targetTime, timeoutHandle, isBlank;
  var currentItemTitle = "";
  var targetItemTitle = "";
  var currentServiceNumber = 0;
  function getTimes() {
    $.ajax({
      url: sermonConfig.apiBase + '/times/',
      dataType: 'json',
      success: function(data, status) {
        currentTime = data.current;
        targetTime = data.target;
        isBlank = data.isBlank;
        if(typeof(data.currentItem) !== 'undefined') {
          currentItemTitle = data.currentItem.title;
        } else {
          currentItemTitle = "";
        }
        if(typeof(data.targetItem) !== 'undefined') {
          targetItemTitle = data.targetItem.title;
        } else {
          targetItemTitle = "";
        }
        if(typeof(data.currentServiceNumber) !== 'undefined') {
          currentItemTitle = data.currentServiceNumber + ": " + currentItemTitle;
          targetItemTitle = data.currentServiceNumber + ": " + targetItemTitle;
        }

        // Restart displat update
        clearTimeout(timeoutHandle);
        updateDisplay();
        if(typeof(getTimesCompleteCallback) === "function") {
          getTimesCompleteCallback(data);
        }
      }
    });
    setTimeout(getTimes, sermonConfig.timestep.server * 1000);
  }

  function updateDisplay() {
    var timeMode = 'regular';
    var timeRemaining = targetTime - currentTime;
    if( timeRemaining < 0 ) {
      timeRemaining = timeRemaining * -1;
      timeMode = 'expired';
    } else if( timeRemaining < 5 * 60 ) {
      timeMode = 'endingSoon';
    }

    if(!isBlank) {
      $("#mainDisplay").removeClass("blank");
    } else {
      $("#mainDisplay").addClass("blank");
    }

    $("#mainDisplay .content").html(formatDisplayTime(timeRemaining));
    $("#mainDisplay").attr('data-timemode', timeMode);

    /*$("#mainDisplay").textfill({
      maxFontPixels: -1
    });*/

    $("#currentItemTitle").html(currentItemTitle);
    $("#targetItemTitle").html(targetItemTitle);

    currentTime = currentTime + sermonConfig.timestep.display;
    timeoutHandle = setTimeout(updateDisplay, sermonConfig.timestep.display * 1000);
  }
  getTimes();

//})(jQuery,sermonConfig);
function pad(num) {
    return ("0"+num).slice(-2);
}
function formatDisplayTime(secs) {
  var minutes = Math.floor(secs / 60);
  secs = secs%60;
  var hours = Math.floor(minutes/60)
  minutes = minutes%60;

  if(hours > 0) {
    $("#mainDisplay").addClass('hasHours');
    return pad(hours)+":"+pad(minutes)+":"+pad(secs);
  } else {
    $("#mainDisplay").removeClass('hasHours');
    return minutes+":"+pad(secs);
  }

  //
  //return pad(hours)+":"+pad(minutes);
  //return minutes+":"+pad(secs);
  //return hours+":"+pad(minutes);
}
