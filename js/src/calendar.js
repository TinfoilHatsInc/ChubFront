$(function () {

  var currMonth = new Date();
  currMonth.setDate(1);
  var nextMonth = new Date();
  nextMonth.setDate(1);
  nextMonth.setMonth(nextMonth.getMonth() + 1);

  $('#calendar').fullCalendar({
    eventClick: function(event, jsEvent) {
      $('.video-popup .video-container video').attr('src', event.video);
      $('.video-popup').addClass('opened');
    },
    eventRender: function(event, element) {
      if(event.video != null) {
        $(element).attr('data-video-src', event.video);
      }
    },
    validRange: {
      start: currMonth.toISOString().slice(0,10),
      end: nextMonth.toISOString().slice(0,10)
    },
    header : {
      left : 'title',
      center: '',
      right : ''
    },
    timeFormat: 'H(:mm)',
    events: [
      {
        title  : 'event1',
        start  : '2018-01-01T14:30:00',
        className: 'critical',
        allDay : false,
        video: '/files/videos/sample-video.m4v'
      },
      {
        title  : 'event2',
        start  : '2018-01-05T13:30:00',
        allDay : false,
        video: '/files/videos/sample-video.m4v'
      },
      {
        title  : 'event3',
        start  : '2018-01-09T12:30:00',
        className: 'critical',
        allDay : false,
        video: '/files/videos/sample-video.m4v'
      }
    ]
  });

  $('.video-popup .close').click(function () {
    $(this).parent().removeClass('opened');
  })

});