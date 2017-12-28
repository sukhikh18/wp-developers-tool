<?php
/**
 * Подключить нужные скрипты и стили
 */

namespace CDevelopers\tool;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\dtools_assets' );
function dtools_assets() {
    $suffix = (defined('WP_DEBUG_SCRIPT') && WP_DEBUG_SCRIPT) ? '' : '.min';

    $assets = DTools::get_plugin_url( 'assets' );
    $sticky = DTools::get( 'sticky' );
    if(wp_is_mobile() && $sticky == 'phone_only' || $sticky == 'forever') {
        wp_enqueue_script( 'sticky', $assets .
            '/sticky/jquery.sticky'.$suffix.'.js', array( 'jquery' ), '1.0.4', true);
    }

    if( DTools::get( 'countTo' ) ) {
        wp_enqueue_script('countTo', $assets .
            '/countTo/jquery.countTo'.$suffix.'.js', array( 'jquery' ), false, true);
    }

    if( DTools::get( 'appearJs' ) ) {
        wp_enqueue_script('appear', $assets .
            '/jquery.appear.js', array( 'jquery' ), false, true);
    }

    if( DTools::get( 'animate' ) ) {
        wp_enqueue_style( 'animate', $assets .
            '/animate'.$suffix.'.css', false, '3.5.1' );
    }

    if( DTools::get( 'wow' ) ) {
        wp_enqueue_script( 'wow', $assets .
            '/WOW/wow'.$suffix.'.js', array( 'jquery' ), '1.3.0', true );
    }

    if( DTools::get( 'font_awesome' ) ) {
        wp_enqueue_style( 'font_awesome', $assets .
            '/font-awesome/css/font-awesome'.$suffix.'.css', false, '4.7.0');
    }

    $settings = DTools::get( 'all' );
    $settings['is_mobile'] = wp_is_mobile();
    wp_enqueue_script(  'dtools-public', $assets . '/public.js', array( 'jquery' ), '1.1', true);
    wp_localize_script( 'dtools-public', 'DTools', $settings );
}
