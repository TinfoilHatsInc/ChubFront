$(function () {

  var currMonth = new Date();
  currMonth.setDate(1);
  var nextMonth = new Date();
  nextMonth.setDate(1);
  nextMonth.setMonth(nextMonth.getMonth() + 1);
  
  $.ajax({
    url: '/ajax/events/new',
    dataType: 'json',
    success: function (events) {
      $('#calendar').fullCalendar({
        eventClick: function(event, jsEvent) {
          $('.video-popup .flag-btn')
            .attr('data-event-id', event.eventId)
            .attr('data-room-id', event.roomId);
          if(event.isCritical) {
            $('.video-popup .flag-btn').addClass('flagged').find('span.text').text('Flagged as critical');
          } else {
            $('.video-popup .flag-btn').removeClass('flagged').find('span.text').text('Flag as critical');
          }
          $('.video-popup .video-elem-container').css('max-height', $(window).height() - 160).empty();
          createVideo(event.videos[0]);
          initVideoElems(event.videos);
        },
        eventRender: function(event, element) {
          if(event.isCritical) {
            $(element).addClass('critical').find('.fc-title').html('<i>warning</i>');
          }
          if(event.eventId != null) {
            $(element).attr('data-event-id', event.eventId);
          }
          if(event.room != null) {
            var $title = $(element).find('.fc-title');
            $title.html($title.html() + event.room);
          }
          if(event.roomId != null) {
            $(element).attr('data-room-id', event.roomId);
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
        events: events
      });
    }
  });

  $('.video-popup .video-container').on('click', function (e) {
    if($(e.target).hasClass('video-container')) {
      $('.video-popup .video-container .video-wrapper .video video').remove();
      $('.video-popup').removeClass('opened');
    }
  });
  $('.video-popup .close-btn').on('click', function () {
    $('.video-popup .video-container .video-wrapper .video video').remove();
    $('.video-popup').removeClass('opened');
  });

  $(document).on('click', '.video-popup .flag-btn:not(.flagged)', function () {
    var dataset = $(this).get(0).dataset;
    $.ajax({
      url: '/event/flag',
      method: 'post',
      data: dataset,
      success: function (data) {
        if(data['error'] != null) {
          ohSnap(data['message'], {'color': 'red'});
        } else {
          var event = $('#calendar').fullCalendar('clientEvents', dataset['eventId'])[0];
          event.isCritical = true;
          $('#calendar').fullCalendar('updateEvent', event);
          $('#calendar').fullCalendar('refetchEvents');
        }
      }
    })
  });

  $(document).on('click', '.video-popup .video-elem-container .video-elem', function () {
    createVideo($(this).find('video').attr('src'));
    $(this).addClass('active');
  });

  $(document).on('click', '.old-event', function () {
    var $videos = $(this).find('.video-src');
    var videoUrls = [];
    for(var i = 0; i < $videos.length; i++) {
      videoUrls[i] = $($videos[i]).attr('data-video-src');
    }
    createVideo(videoUrls[0]);
    $('.video-popup .flag-btn').attr('data-event-id', $(this).attr('data-event-id')).addClass('flagged').find('span.text').text('Flagged as critical');
    initVideoElems(videoUrls);
  });

  function initVideoElems(videoUrls) {
    $('.video-popup .video-elem-container').empty();
    for(var i = 0; i < videoUrls.length; i++) {
      var $video = $('<video src="' + videoUrls[i] + '" preload=metadata></video>');
      var $wrapper = $('<div class="video-elem"></div>');
      if(i == 0) {
        $wrapper.addClass('active');
      }
      $wrapper.append($video);
      $('.video-popup .video-elem-container').append($wrapper);
    }
  }


  function createVideo(videoSrc) {
    var $video = $('<video preload="auto" controls autoplay loop></video>');
    $video.append('<source src="' + videoSrc + '" type="video/mp4">');
    $video.css('max-height', $(window).height() - 160);
    $('.video-popup .video-container .video-wrapper .video').empty().append($video);
    $('.video-popup .video-elem-container .video-elem').removeClass('active');
    $('.video-popup').addClass('opened');
  }

});