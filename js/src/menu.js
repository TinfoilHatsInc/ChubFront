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