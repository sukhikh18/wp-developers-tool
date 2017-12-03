<?php

namespace CDevelopers\tool;

add_action( 'wp_footer', __NAMESPACE__ . '\add_view_count', 50);
function add_view_count() {
    $option = DTools::get( 'record-views' );
    if( 'all' === $option ) {
        $option = nul;
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
