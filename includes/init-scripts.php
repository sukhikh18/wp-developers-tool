<?php
JQScript::$assets_dir = DT_ASSETS_URL;

extract( get_option( DT_PLUGIN_NAME ) );
$suffix = '.min';
if( is_wp_debug() !== false )
  $suffix = '';

ob_start();
?>
  <?php if( !empty($smooth_scroll) || !empty($scroll_after_load) ): ?>
    function scrollTo(elemId, returnTop=40, delay=500){
      var offset = $( elemId ).offset();
      if( !offset )
        offset = $('a[name='+elemId.slice(1)+']').offset();

      if(offset)
        $('html, body').animate({ scrollTop: offset.top - returnTop }, delay);
      else
        console.log('Element not exists.');
    }
  <?php endif; ?>
  <?php if( !empty($smooth_scroll) ): ?>
    $('a[href^=#]').click( function(event){
      event.preventDefault();

      if( $(this).attr('rel') != 'noScroll' )
        scrollTo( $(this).attr('href'), <?php echo $smooth_scroll; ?> );
    });
  <?php endif; ?>

  <?php if( !empty($scroll_after_load) ): ?>
    var id = split('#', 'window.location.href');

    if ( id.length >= 1 )
      setTimeout(function(){ scrollTo( '#' + id[id.length-1], <?php echo $scroll_after_load; ?> ) }, 200);
  <?php endif; ?>

<?php
$scrollCode = ob_get_clean();
if($scrollCode)
  JQScript::custom($scrollCode);

// sticky
if( isset( $sticky ) ){
  JQScript::enqueue( 'sticky', 'sticky/jquery.sticky'.$suffix.'.js', '1.0.4', true );

  if ( !empty($sticky_selector) && ( (wp_is_mobile() && $sticky == 'phone_only' ) || $sticky == 'forever' ) ){
    add_action( 'admin_bar_menu', function(){
      if( function_exists('is_admin_bar_showing') && is_admin_bar_showing() )
        echo "<style>.admin-bar .is-sticky > div, .admin-bar .is-sticky > ul, .admin-bar .is-sticky > nav { top: 32px !important; }</style>";
    });
    
    $sticky_script = "
      var \$container = \$('{$sticky_selector}');
      \$container.sticky({topSpacing:0,zIndex:666});
      \$container.parent('.sticky-wrapper').css('margin-bottom', \$container.css('margin-bottom') );\n";

    JQScript::custom( $sticky_script );
  }   
}

// animate
if( isset( $animate ) )
  JQScript::style('animate', 'animate'.$suffix.'.css', '3.5.1');

// font-awesome
if( isset( $font_awesome ) )
  JQScript::style('font_awesome', 'font-awesome/css/font-awesome'.$suffix.'.css', '4.7.0');

// fancybox
if( isset( $fancybox ) ){
  add_action( 'wp_enqueue_scripts', function(){
    wp_deregister_style('gllr_fancybox_stylesheet');
    foreach (array('gllr_fancybox_js', 'fancybox-script', 'fancybox', 'jquery.fancybox', 'jquery_fancybox', 'jquery-fancybox') as $value) { wp_deregister_script($value); }
  }, 660 );

  JQScript::style( 'fancybox', 'fancybox/jquery.fancybox'.$suffix.'.css' );
  JQScript::enqueue('fancybox', 'fancybox/jquery.fancybox'.$suffix.'.js', '1.6');

  if(isset($fancybox_thumb)){
    JQScript::style( 'fancybox-thumb', 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.css', '1.0.7');
    JQScript::enqueue('fancybox-thumb', 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.js', '1.0.7');
  }

  if(isset($fancybox_mousewheel))
    JQScript::enqueue('mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js', '3.1.13');

  $fancy_opts = array(
    'nextEffect' => 'none',
    'prevEffect' => 'none',
    'helpers' => array(
      'title' => array('type' => 'inside'),
      'thumbs' => array('width' => 120, 'height' => 80)
      )
    );
  JQScript::common($fancybox, 'fancybox', $fancy_opts);
}

if( isset($countTo) ){
  JQScript::enqueue('countTo', 'countTo/jquery.countTo'.$suffix.'.js');
  if( isset($appearJs) ){
    JQScript::enqueue('appear', 'jquery.appear.js');
    $appear = '
    $("'.$countTo.'").appear();
    $("'.$countTo.'").on("appear", function(event, $all_appeared_elements) {
      console.log($(this).attr("data-appeared"));
      if( !$(this).attr("data-appeared") )
        $(this).countTo();

      $(this).attr("data-appeared", 1);
    });
    ';

    JQScript::custom( $appear );
  } else {
    JQScript::common( $countTo, 'countTo' );
  }
  
}


if( isset($back_top) ){
  $back_top_script = '
  var offset = 200;
  var selector = "#back-top";

  $(window).scroll(function() {
    if ($(this).scrollTop() > offset) {
      $(selector).fadeIn(400);
    } else {
      $(selector).fadeOut(400);
    }
  });
  $(selector).click(function(event) {
    event.preventDefault();
    $("html, body").animate({scrollTop: 0}, 600);
    return false;
  });' . "\n";
  JQScript::custom( $back_top_script );
  add_action( 'wp_footer', function(){
    $option = get_option( DT_PLUGIN_NAME );

    echo "<a href='#' id='back-top'>{$option['back_top']}</a>";
  });
}