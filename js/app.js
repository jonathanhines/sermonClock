(function($){
  var currentTime = 10;
  updateDisplay();
  function updateDisplay() {
    $("#mainDisplay").html("This is the current time: " + currentTime);
    currentTime = currentTime - 1;
    setTimeout(updateDisplay, 1000);
  }


})(jQuery);
