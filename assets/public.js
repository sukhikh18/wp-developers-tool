jQuery(document).ready(function($) {
  function scrollTo(selector, returnTop = 40, delay = 500) {
    var offset = $( selector ).offset() || $('a[name='+selector.slice(1)+']').offset(),
        barheight = $('body').hasClass('admin-bar') && $('#wpadminbar').length ?
          $('#wpadminbar').height() : 0;

    if( offset ) {
      $('html, body').animate({ scrollTop: offset.top - returnTop - barheight }, delay);
    }
    // not found
    else {
      console.log('Element not exists.');
    }
  }

  if( Utils.smooth_scroll ){
    // replaced to scroll_after_load
    // var arrLocHref = window.location.href.split('#', 2);
    // if( arrLocHref.length >= 2 ) {
    //   scrollTo( '#' + arrLocHref[1], Utils.smooth_scroll, 1 );
    // }
    $('a[href*="#"], .scroll').click( function(event) {
      var $self = $(this),
          href = false,
          pos = false;

      try {
        href = $self.attr('href') || $self.find('a').attr('href');
      } catch(e) {
        console.log(e);
      }

      if( href )
        pos = href.indexOf('#');

      if(pos >= 0 && $(this).attr('rel') != 'noScroll' && linkHref.slice(pos).length >= 1) {
        var target = href.split( '#', 2 );

        if( '/' == target[0] || '' == target[0] || target[0] == window.location.href.split('#', 1)[0] ) {
          // target element exists
          if( $( '#' + target[1] ).length ) {
            event.preventDefault();
          }

          scrollTo( '#' + target[1], Utils.smooth_scroll );
        }
      }
    });
  }

  if( Utils.scroll_after_load ) {
    var arrTarget = window.location.href.split('#');
    if ( arrTarget.length > 1 ){
      target = arrTarget[arrTarget.length-1].match(/\w+/gi);

      // for disable twitchings on deprecated computers
      setTimeout( function(){
        scrollTo( '#' + target[target.length-1], Utils.scroll_after_load )
      }, 200);
    }
  }

  // Back To Top
  if( Utils.back_top ) {
    var $backButton = $("<a href='#' id='back-top'></a>"),
        offset = 200;

    $backButton.on('click', function(event) {
      event.preventDefault();

      $("html, body").animate({scrollTop: 0}, 600);
    });

    $backButton.append(Utils.back_top).appendTo('body');

    $(window).scroll(function() {
      if ($(this).scrollTop() > offset) $backButton.fadeIn();
      else $backButton.fadeOut();
    });
  }
});