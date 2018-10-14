<?php

namespace NikolayS93\Tools;

add_filter( 'easy_queries_args', __NAMESPACE__ . '\top_sales_query_args', 10, 1 );
function top_sales_query_args( $args ) {
    if( $args['post_type'] == 'top-sales' ) {
        $args['post_type'] = 'product';

        switch ( Utils::get( 'bestsellers' ) ) {
            case 'personal':
                $args['meta_key'] = 'top_sale_product';
                $meta = array(
                    'key' => 'top_sale_product',
                    'value' => 'yes',
                    'compare' => '='
                    );
                break;
            case 'views':
                $args['meta_key'] = 'total_views';
                $meta = array(
                    'key' => 'total_views',
                    'value' => 0,
                    'compare' => '>'
                    );
                break;
            case 'sales':
            default:
                $args['meta_key'] = 'total_sales';
                $meta = array(
                    'key' => 'total_sales',
                    'value' => 0,
                    'compare' => '>'
                    );
                break;
        }

        $args['orderby']    = 'meta_value_num';
        $args['meta_query'] = array($meta);
    }

    return $args;
}

if( 'personal' === Utils::get( 'bestsellers' ) && is_admin() ) {
    if( ! class_exists(__NAMESPACE__ . '\WCProductSettings') ) {
        Utils::write_debug('Class WCProductSettings not found', __FILE__);
        return;
    }

    $wc_fields = new WCProductSettings();

    $wc_fields->add_field( array(
        'type'        => 'checkbox',
        'id'          => 'top_sale_product',
        'label'       => __( 'Popularity product', DOMAIN ), // 'Популярный товар'
        'description' => __( 'Show it on popular block', DOMAIN ), // 'Этот товар будет показываться в блоке популярных товаров'
    ) );

    $wc_fields->set_fields();
}
