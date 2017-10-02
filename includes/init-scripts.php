<?php
namespace DTools;

add_action( 'wp_enqueue_scripts', 'DTools\dtools_assets' );
function dtools_assets() {
  $suffix = (defined('WP_DEBUG_SCRIPT') && WP_DEBUG_SCRIPT) ? '' : '.min';
  $settings = DevelopersTools::$settings;
  $url = DT_ASSETS_URL;

  $sticky = isset($settings['sticky']) ? $settings['sticky'] : false;
  if(wp_is_mobile() && $sticky == 'phone_only' || $sticky == 'forever')
    wp_enqueue_script( 'sticky', $url . '/sticky/jquery.sticky'.$suffix.'.js', array( 'jquery' ), '1.0.4', true);

  if( isset($settings['countTo']) )
    wp_enqueue_script('countTo', $url . '/countTo/jquery.countTo'.$suffix.'.js', array( 'jquery' ), false, true);

  if( isset($settings['appearJs']) )
    wp_enqueue_script('appear', $url . '/jquery.appear.js', array( 'jquery' ), false, true);

  if( isset($settings['animate']) )
    wp_enqueue_style( 'animate', $url . '/animate'.$suffix.'.css', false, '3.5.1' );

  if( isset($settings['font_awesome']) )
    wp_enqueue_style( 'font_awesome', $url . '/font-awesome/css/font-awesome'.$suffix.'.css', false, '4.7.0');

  $settings['is_mobile'] = wp_is_mobile();
  wp_enqueue_script(  'dtools-public', $url . '/public.js', array( 'jquery' ), '1.0', true);
  wp_localize_script( 'dtools-public', 'DTools', $settings );
}
