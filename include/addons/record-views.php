<?php
/**
 * Записать количетсво просмотров в total_views
 */

namespace NikolayS93\Tools;

add_action( 'wp_footer', __NAMESPACE__ . '\add_view_count', 50);
function add_view_count() {
    if( $option = Utils::get( 'record-views' ) ) {
        if( 'all' === $option ) {
            $option = null;
        }

        if( is_singular($option) && ($id = get_the_ID()) ) {
            if( ! $views = get_post_meta( $id, 'total_views', true ) ) {
                $views = 0;
            }

            update_post_meta( $id, 'total_views', $views + 1 );

            if( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) {
                print_r('<pre>(Режим отладки) Популярность товара: '.$views.'</pre>');
            }
        }
    }
}
