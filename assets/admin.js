jQuery(document).ready(function($) {
  function set_focus_trigger(){
    $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function() {
      var $self = $(this),
          ph = $self.attr('placeholder');

      if('' == $self.val() && ph) {
        ph = ph.replace('e.g. ', '').replace('к пр. ', '');

        $self.val( ph );
        $self.select();
      }
    });
  }

  // event on function wrap for ajax
  set_focus_trigger();
});