jQuery(document).ready(function($) {
  function scrollTo(elemId, returnTop=40, delay=500){
    var offset = $( elemId ).offset() || $('a[name='+elemId.slice(1)+']').offset();

    barheight = $('body').hasClass('admin-bar') ? 32 : 0;
    if( offset ) {
      $('html, body').animate({ scrollTop: offset.top - returnTop - barheight }, delay);
    }
    else {
      console.log('Element not exists.');
    }
  }

  if( DTools.smooth_scroll ){
    // replaced to scroll_after_load
    // var arrLocHref = window.location.href.split('#', 2);
    // if( arrLocHref.length >= 2 ) {
    //   scrollTo( '#' + arrLocHref[1], DTools.smooth_scroll, 1 );
    // }
    $('a[href^=#], .scroll').click( function(event){
      var linkHref = $(this).attr('href');
      if( ! linkHref ) {
        linkHref = $(this).find('a').attr('href');
      }

      var finded = linkHref.indexOf('#');
      if( finded >= 0 ) {
        if( $(this).attr('rel') != 'noScroll' && linkHref.slice(finded).length >= 1 ) {
          var arrLinkHref = linkHref.split( '#', 2 );

          if( arrLinkHref[0] == window.location.href.split('#', 1)[0] ) {
            event.preventDefault();

            scrollTo( '#' + arrLinkHref[1], DTools.smooth_scroll );
          }
        }
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
});