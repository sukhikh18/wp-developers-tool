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
        }
      }).fail(function() {
        console.log('jQuery ajax fail!');
      });
    });
  }
  change_modal_type();
});