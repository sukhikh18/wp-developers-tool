<?php

namespace NikolayS93\Tools;

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

    if( $from > 0.1 )
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
    if ( empty( $_REQUEST['quantity'] ) || intval($_REQUEST['quantity']) < $min ) {
        wc_add_notice( __('Invalid quantity value.', DOMAIN), 'error' );
        return false;
    }

    return $passed_validation;
}

if( is_admin() && class_exists(__NAMESPACE__ . '\WCProductSettings') ){
    $wc_fields = new WCProductSettings();
    $wc_fields->add_field( array(
        'id'          => 'wholesale_from',
        'type'        => 'number',
        'label'       => __('Sale from', DOMAIN),
        'description' => __('Allow sell out bigger only, than..', DOMAIN),
        'desc_tip'    => true,
        'custom_attributes' => array(
            'step' => 'any',
            'min'  => '0.1',
        ),
    ) );

    $wc_fields->set_fields();
}
