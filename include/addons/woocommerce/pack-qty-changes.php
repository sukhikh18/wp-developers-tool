<?php

namespace NikolayS93\Tools;

add_filter( 'woocommerce_quantity_input_step', __NAMESPACE__ . '\woocommerce_quantity_input_package_step', 10, 1 );
function woocommerce_quantity_input_package_step( $step ) {
    global $product;

    if( ! is_a($product, 'WC_Product') )
        return $step;

    return $product->get_meta('pack_qty');
}

add_filter( 'woocommerce_quantity_input_min', __NAMESPACE__ . '\wholesales_min', 50, 2 );
function wholesales_min( $step, $product ) {
	if( ! is_a($product, 'WC_Product') )
        return $step;

    return ( $step !== 1 ) ? $step : $product->get_meta('pack_qty');
}