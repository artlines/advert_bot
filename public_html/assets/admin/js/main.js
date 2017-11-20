$(function() {
  var window_width = $(window).width();
  adaptiveMenu(window_width);

  $(window).resize(function(){
    window_width = $(window).width();
    adaptiveMenu(window_width);
  })

  $('#menu_button').click(function(){
    $('#main_nav').slideToggle();
  });

});

function adaptiveMenu(window_width){
  if (window_width < 1000) {
    $('#main_nav').addClass('flex-column').hide();
  }else{
    $('#main_nav').removeClass('flex-column').show();
  }
}