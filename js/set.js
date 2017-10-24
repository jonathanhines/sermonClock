(function($){
  $('#formModeTabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
  });
})(jQuery);

setActiveItem = function(id, serviceIndex) {
  $.ajax({
    url: sermonConfig.apiBase + '/times',
    dataType: 'json',
    method: "POST",
    data: {
      activateItemID: id,
      serviceIndex: serviceIndex
    },
    success: function(data, status) {
      getTimes();
    }
  });
}
