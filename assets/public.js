jQuery(document).ready(function($) {
  function scrollTo(elemId, returnTop=40, delay=500){
    var offset = $( elemId ).offset() || $('a[name='+elemId.slice(1)+']').offset();

    if(offset)
      $('html, body').animate({ scrollTop: offset.top - returnTop }, delay);
    else
      console.log('Element not exists.');
  }

  if( DTools.smooth_scroll ){
    $('a[href^=#]').click( function(event){
      var elemId = $(this).attr('href');
      if($(this).attr('rel') != 'noScroll' && elemId.indexOf('#') == 0 && elemId.slice(1).length >= 1){
        event.preventDefault();

        scrollTo( elemId, DTools.smooth_scroll );
      }
    });
  }
  if( DTools.scroll_after_load ){
    var id = window.location.href.split('#');
    if ( id.length > 1 ){
      id = id[id.length-1].match(/\w+/gi);

      setTimeout(function(){
        scrollTo( '#' + id[id.length-1], DTools.scroll_after_load )
      }, 200);
    }
  }

  // sticky
  if( DTools.sticky_selector ){
    if( DTools.is_mobile && DTools.sticky == 'phone_only' || DTools.sticky == 'forever' ){
      var space = ( $('#wpadminbar').length ) ? 32 : 0;

      var $container = $( DTools.sticky_selector );
      $container.sticky({topSpacing:space,zIndex:1100});
      $container.parent('.sticky-wrapper').css('margin-bottom', $container.css('margin-bottom') );
    }
  }

  if( DTools.appearJs ){
    if( DTools.countTo ){
      $( DTools.countTo ).appear();
      $( DTools.countTo ).on("appear", function(event, $all_appeared_elements) {
        if( ! $(this).attr("data-appeared") )
          $(this).countTo();

        $(this).attr("data-appeared", 1);
      });
    }
  }
  if( DTools.countTo && !DTools.appearJs ){
    $( DTools.countTo ).countTo();
  }

  // Back To Top
  if( DTools.back_top ){
    var offset = 200;

    var btnBack = $("<a href='#' id='back-top'></a>" );
    btnBack.on('click', function(event) {
      event.preventDefault();
      $("html, body").animate({scrollTop: 0}, 600);
    });
    btnBack.append(DTools.back_top).appendTo('body');

    $(window).scroll(function() {
      if ($(this).scrollTop() > offset) btnBack.fadeIn(400);
      else btnBack.fadeOut(400);
    });
  }
  if( DTools.modal_selector ){
   if( DTools.modal_type == 'fancybox3' ){
    var fancyModal = $( DTools.modal_selector ).fancybox({
      animationEffect : DTools.fancybox_props.openCloseEffect,
      transitionEffect : DTools.fancybox_props.nextPrevEffect,
    });
   }
   else if(DTools.modal_type) {
    var fancyModal = $( DTools.modal_selector ).fancybox({
      openEffect : DTools.fancybox_props.openEffect,
      closeEffect : DTools.fancybox_props.closeEffect,
      nextEffect : DTools.fancybox_props.nextEffect,
      prevEffect : DTools.fancybox_props.prevEffect,
      helpers: {
        title : { type : 'inside' },
        thumbs : DTools.fancybox_props.thumb ? { width: 120, height: 80 } : false
      }
    });
  }
}
});