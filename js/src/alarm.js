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