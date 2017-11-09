<?php

namespace CDevelopers\tool;


add_filter( 'easy_queries_args', __NAMESPACE__ . '\top_sales_query_args', 10, 1 );
function top_sales_query_args( $args ) {
    if( $args['post_type'] == 'top-sales' ){
        $args['post_type'] = 'product';

        switch ( DTools::get( 'bestsellers' ) ) {
            case 'personal':
                $meta = array(
                    'key' => 'top_sale_product',
                    'value' => 'yes',
                    'compare' => '='
                    );
                break;
            case 'views':
                $meta = array(
                    'key' => 'total_views',
                    'value' => 0,
                    'compare' => '>'
                    );
                break;
            case 'sales':
            default:
                $meta = array(
                    'key' => 'total_sales',
                    'value' => 0,
                    'compare' => '>'
                    );
                break;
        }

        $args['meta_key']   = 'total_sales';
        $args['orderby']    = 'meta_value_num';
        $args['meta_query'] = array($meta);
    }

    return $args;
}

if( 'views' === DTools::get( 'bestsellers' )  ) {
    add_action( 'woocommerce_after_single_product', __NAMESPACE__ . '\add_woo_view_count', 50);
    function add_woo_view_count(){
        $id = get_the_ID();

        if( ! $views = get_post_meta( $id, 'total_views', true ) ) {
            $views = 0;
        }

        update_post_meta( $id, 'total_views', $views + 1 );

        if( defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY ) {
            print_r('<pre>(Режим отладки) Популярность товара: '.$views.'</pre>');
        }
    }
}

if( 'personal' === DTools::get( 'bestsellers' ) && is_admin() ) {
    if( ! class_exists(__NAMESPACE__ . '\WCProductSettings') ) {
        DTools::write_debug('Класс WCProductSettings не найден', __FILE__);
        return;
    }
    $wc_fields = new WCProductSettings();
    $wc_fields->add_field( array(
        'type'        => 'checkbox',
        'id'          => 'top_sale_product',
        'label'       => 'Популярный товар',
        'description' => 'Этот товар будет показываться в блоке популярных товаров',
    ) );
    $wc_fields->set_fields();
}
