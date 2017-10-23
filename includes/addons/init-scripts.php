<?php
namespace CDevelopers\tool;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\dtools_assets' );
function dtools_assets() {
    $suffix = (defined('WP_DEBUG_SCRIPT') && WP_DEBUG_SCRIPT) ? '' : '.min';
    $DTools = DTools::get_instance();

    $sticky = $DTools->get( 'sticky' );
    if(wp_is_mobile() && $sticky == 'phone_only' || $sticky == 'forever') {
        wp_enqueue_script( 'sticky', URL_ASSETS .
            '/sticky/jquery.sticky'.$suffix.'.js', array( 'jquery' ), '1.0.4', true);
    }

    if( $DTools->get( 'countTo' ) ) {
        wp_enqueue_script('countTo', URL_ASSETS .
            '/countTo/jquery.countTo'.$suffix.'.js', array( 'jquery' ), false, true);
    }

    if( $DTools->get( 'appearJs' ) ) {
        wp_enqueue_script('appear', URL_ASSETS .
            '/jquery.appear.js', array( 'jquery' ), false, true);
    }

    if( $DTools->get( 'animate' ) ) {
        wp_enqueue_style( 'animate', URL_ASSETS .
            '/animate'.$suffix.'.css', false, '3.5.1' );
    }

    if( $DTools->get( 'font_awesome' ) ) {
        wp_enqueue_style( 'font_awesome', URL_ASSETS .
            '/font-awesome/css/font-awesome'.$suffix.'.css', false, '4.7.0');
    }

    $settings = $DTools->get( 'all' );
    $settings['is_mobile'] = wp_is_mobile();
    wp_enqueue_script(  'dtools-public', URL_ASSETS . '/public.js', array( 'jquery' ), '1.0', true);
    wp_localize_script( 'dtools-public', 'DTools', $settings );
}
