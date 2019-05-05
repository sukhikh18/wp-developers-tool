<?php
/**
 * Подключить нужные скрипты и стили
 */

namespace NikolayS93\Tools;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\Utils_assets' );
function Utils_assets() {
    $suffix = (defined('WP_DEBUG_SCRIPT') && \WP_DEBUG_SCRIPT) ? '' : '.min';
    $assets = Utils::get_plugin_url( 'assets' );

//     if( Utils::get( 'countTo' ) ) {
//         $countto_src = apply_filters('dt_countto_src',
//             $assets . '/countTo/jquery.countTo'.$suffix.'.js', $suffix);

//         wp_enqueue_script('countTo', $countto_src, array( 'jquery' ), false, true);
//     }

//     if( Utils::get( 'appearJs' ) ) {
//         $appear_src = apply_filters('dt_appear_src',
//             $assets . '/jquery.appear.js', $suffix);

//         wp_enqueue_script('appear', $appear_src, array( 'jquery' ), false, true);
//     }

//     if( Utils::get( 'animate' ) ) {
//         $animate_src = apply_filters('dt_animate_src',
//             $assets . '/animate'.$suffix.'.css', $suffix);

//         wp_enqueue_style('animate', $animate_src, false, '3.5.1');
//     }

//     if( Utils::get( 'wow' ) ) {
//         $wow_src = apply_filters('dt_wow_src',
//             $assets . '/WOW/wow'.$suffix.'.js', $suffix);

//         wp_enqueue_script('wow', $wow_src, array( 'jquery' ), '1.3.0', true );
//     }

//     if( Utils::get( 'font_awesome' ) ) {
//         $font_awesome_src = apply_filters('dt_font_awesome_src',
//             "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome$suffix.css");

//         wp_enqueue_style( 'font_awesome', $font_awesome_src, false, '4.7.0');
//     }

    $settings = Utils::get( 'all' );
    $settings['is_mobile'] = wp_is_mobile();
    wp_enqueue_script(  'multitool-public', $assets . '/public'.$suffix.'.js', array( 'jquery' ), '1.1', true);
    wp_localize_script( 'multitool-public', 'Utils', $settings );
}
