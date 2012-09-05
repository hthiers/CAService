$(document).ready(function() {
  $('.links li code').hide();  
  $('.links li p').click(function() {
    $(this).next().slideToggle('fast');
  });
  
  $("#navmenu-h li,#navmenu-v li").hover(
    function() { $(this).addClass("iehover"); },
    function() { $(this).removeClass("iehover"); }
  );
});