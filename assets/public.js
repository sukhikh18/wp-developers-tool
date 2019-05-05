jQuery(document).ready(function($) {
    window.scrollTo = function(selector, returnTop, delay) {
        var returnTop = returnTop || 40;
        var delay = delay || 500;
        var offset = $( selector ).offset() || $('a[name='+selector.slice(1)+']').offset(),
            barheight = $('body').hasClass('admin-bar') && $('#wpadminbar').length ? $('#wpadminbar').height() : 0;

        if( offset ) $('html, body').animate({ scrollTop: offset.top - returnTop - barheight }, delay);
        else console.log('Element not exists.');
    }

    if( Utils.smooth_scroll ) {
        $('a[href*="#"], .scroll').on('click', function(event) {
            try {
                var href = $(this).attr('href') || $(this).find('a').attr('href'),
                    hashPos = href.indexOf('#');

                if( hashPos >= 0 && 1 <= href.slice(hashPos).length && 'noscroll' != $(this).attr('rel') ) {
                    var target = href.split( '#', 2 );

                    if( '/' == target[0] || '' == target[0] || target[0] == window.location.href.split('#', 1)[0] ) {
                        // target element exists
                        if( $( '#' + target[1] ).length ) {
                            event.preventDefault();
                        }

                        scrollTo( '#' + target[1], Utils.smooth_scroll );
                    }
                }
            } catch(e) {
                console.log(e);
            }
        });
    }

    if( Utils.scroll_after_load ) {
        var arrLocHref = window.location.href.split('#', 2);

        if( arrLocHref.length > 1 ) {
            var target = arrLocHref[arrLocHref.length - 1].match(/\w+/gi);

            // for disable twitchings on deprecated computers
            setTimeout( function(){
                scrollTo( '#' + target[target.length - 1], Utils.scroll_after_load )
            }, 200);
        }
    }

    if( Utils.back_top ) {
        var $button = $('<a href="jsvascript:;" id="back-top"></a>'),
            offset = 200;

        $button.on('click', function(event) {
            event.preventDefault();
            scrollTo('body', 0);
        });

        $button.append(Utils.back_top).appendTo('body');

        $(window).scroll(function() {
            if ($(this).scrollTop() > offset) $button.fadeIn();
            else $button.fadeOut();
        });
    }
});