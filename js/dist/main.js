$(function () {
  $(document).on('click', 'form#alarm-control .switch input:not(.delayed)', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('form#alarm-control').submit();
  });
  var $remaining = $('.remaining-time');
  var remainingInterval;
  if($remaining.length > 0) {
    remainingInterval = setInterval(remainingTime, 1000);
  }
  function remainingTime() {
    var $remaining = $('.remaining-time');
    var timeLeft = parseInt($remaining.html());
    if(timeLeft > 1) {
      $remaining.html(timeLeft - 1);
    } else {
      clearInterval(remainingInterval);
      $('.remaining-time').parent().parent().remove();
      $('.switch input').removeClass('delayed').prop('checked', false);
      $('.alarm-status').text('ON');
      $('form#alarm-control').attr('action', '/alarm/disable');
      $('.recomm-turn-on').remove();
    }
  }
});
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
$(function () {
  $('.hamburger').click(function () {
    $(this).toggleClass('is-active');
    if($(this).hasClass('is-active')) {
      $('.menu .menu-content').show('blind', 200);
    } else {
      $('.menu .menu-content').hide('blind', 200);
    }
  });
});
function ohSnap(n,t){var o={color:null,icon:null,duration:"5000","container-id":"ohsnap","fade-duration":"fast"};t="object"==typeof t?$.extend(o,t):o;var a=$("#"+t["container-id"]),e="",i="",h="";t.icon&&(e="<span class='"+t.icon+"'></span> "),t.color&&(i="alert-"+t.color),h=$('<div class="alert '+i+'">'+e+n+"</div>").fadeIn(t["fade-duration"]),a.append(h),h.on("click",function(){ohSnapX($(this))}),setTimeout(function(){ohSnapX(h)},t.duration)}function ohSnapX(n,t){defaultOptions={duration:"fast"},t="object"==typeof t?$.extend(defaultOptions,t):defaultOptions,"undefined"!=typeof n?n.fadeOut(t.duration,function(){$(this).remove()}):$(".alert").fadeOut(t.duration,function(){$(this).remove()})}
