jQuery(document).ready(function($) {
  // @localize dt_admin_js.nonce
  function change_modal_type(){
    $('#modal_type').on('change', function(event) {
      var select = $(this);
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          'modal_type' : $( "option:selected", select ).val(),
          'nonce' : dt_admin_js.nonce,
          'action' : 'change_modal_type'
        },
        success: function(data){
          select.closest('#dt_modal').html(data);
          change_modal_type();
          set_focus_trigger();
        }
      }).fail(function() {
        console.log('jQuery AJAX FAIL!');
      });
    });
  }
  change_modal_type();

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