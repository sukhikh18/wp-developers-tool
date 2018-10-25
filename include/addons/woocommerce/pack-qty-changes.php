<?php

namespace NikolayS93\Tools;

add_filter( 'woocommerce_quantity_input_step', __NAMESPACE__ . '\woocommerce_quantity_input_package_step', 10, 1 );
function woocommerce_quantity_input_package_step( $step ) {
    global $product;

    if( ! is_a($product, 'WC_Product') )
        return $step;

    return floatval( $product->get_meta('pack_qty') );
}

add_filter( 'woocommerce_quantity_input_min', __NAMESPACE__ . '\quantity_input_min', 50, 2 );
function quantity_input_min( $min, $product ) {
	if( ! is_a($product, 'WC_Product') ) return $min;

    $nMin = $product->get_meta('pack_qty');
    if( 1 > floatval($nMin) && $min == 1 ) {
        $min = $nMin;
    }

    return $min;
}

add_filter( 'woocommerce_quantity_input_args', __NAMESPACE__ . '\quantity_input_value', 50, 2 );
function quantity_input_value( $args, $product ) {
    if( ! is_a($product, 'WC_Product') )
        return $args;

    if( $args['input_value'] < $args['step'] ) {
        $args['input_value'] = $args['step'];
    }

    if( $args['input_value'] > $args['min_value'] ) {
        $args['input_value'] = $args['min_value'];
    }

    return $args;
}
