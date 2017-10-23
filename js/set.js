(function($){
  $('#formModeTabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
})(jQuery);

setActiveItem = function(id) {
  $.ajax({
    url: sermonConfig.apiBase + '/times',
    dataType: 'json',
    method: "POST",
    data: {
      activateItemID: id
    },
    success: function(data, status) {
      // TODO: trigger a timer update if possible
    }
  });
}
