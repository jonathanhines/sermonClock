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

getTimesCompleteCallback = function(data){
  $(".itemRow").removeClass("currentItem targetItem");
  $(".itemRow td").removeClass("currentService");
  if(typeof(data.currentItem) !== 'undefined') {
    $("#item_" + data.currentItem.id).addClass('currentItem');
  }

  if(typeof(data.targetItem) !== 'undefined') {
    $("#item_" + data.targetItem.id).addClass('targetItem');
  }

  if(typeof(data.currentServiceNumber) !== 'undefined') {
    if(typeof(data.currentItem) !== 'undefined') {
      $(".service_" + data.currentServiceNumber).addClass('currentService');
    }
    /*if(typeof(data.currentItem) !== 'undefined') {
      $("#item_" + data.currentItem.id + " .service_" + data.currentServiceNumber).addClass('currentItem');
    }
    if(typeof(data.targetItem) !== 'undefined') {
      $("#item_" + data.targetItem.id + " .service_" + data.currentServiceNumber).addClass('targetItem');
    }*/
  }
}
