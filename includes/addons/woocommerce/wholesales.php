<?php

namespace CDevelopers\tool;

function get_product_wholesale_min( $product ) {
    if( ! is_a($product, 'WC_Product') )
        return 0;

    if( method_exists($product, 'get_meta') ){
        $from = $product->get_meta('wholesale_from');
        $default = $product->get_min_purchase_quantity();
    } else {
        $from = get_post_meta( $product->get_id(), 'wholesale_from', true );
        $default = 1;
    }

    if( $from > 1 )
        return $from;

    return $default;
}

add_filter( 'woocommerce_quantity_input_min', __NAMESPACE__ . '\wholesales_min', 50, 2 );
function wholesales_min( $var, $product ){

    return ( $var !== 1 ) ? $var : get_product_wholesale_min( $product );
}

add_filter( 'woocommerce_add_to_cart_validation', __NAMESPACE__ . '\wholesales_field_validation', 10, 2 );
function wholesales_field_validation( $passed_validation, $product_id ) {
    $product = wc_get_product( $product_id );
    $min = get_product_wholesale_min( $product );
    if ( empty( $_REQUEST['quantity'] ) || $_REQUEST['quantity'] < $min ) {
        wc_add_notice( 'Недопустимое значение количества.', 'error' );
        return false;
    }

    return $passed_validation;
}

if( is_admin() && class_exists(__NAMESPACE__ . '\WCProductSettings') ){
    $wc_fields = new WCProductSettings();
    $wc_fields->add_field( array(
        'type'        => 'number',
        'id'          => 'wholesale_from',
        'label'       => 'Опт от:',
        'desc_tip'    => 'true',
        'description' => 'Разрешить продажи от этого количества',
        ) );

    $wc_fields->set_fields();
}
