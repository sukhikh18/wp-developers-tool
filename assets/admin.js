jQuery(document).ready(function($) {
  function set_focus_trigger(){
    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
      var ph = $(this).attr('placeholder');
      if($(this).val() == '' && ph){
        $(this).val( ph.replace('e.g. ', '').replace('к пр. ', '') );
        $(this).select();
      }
    });
  }
  set_focus_trigger();
});