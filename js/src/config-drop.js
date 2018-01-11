$(function () {
  $("ul.drop-container").sortable({
    connectWith: "ul.drop-container",
    receive: function (event, ui) {
      var moduleId = ui.item[0].dataset['moduleId'];
      var roomId = event.target.dataset['roomId'];
      var type;
      if($(event.target).hasClass('triggers')) {
        type = 'Trigger';
      } else {
        type = 'Observer';
      }
      var existing = $("#config-actions").find('')
    }
  });
  $("li.module").disableSelection();
  $(".rooms-accordion").accordion();

});