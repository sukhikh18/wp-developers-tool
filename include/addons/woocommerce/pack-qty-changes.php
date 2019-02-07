<?php

namespace NikolayS93\Tools;

add_filter( 'woocommerce_quantity_input_step', __NAMESPACE__ . '\woocommerce_quantity_input_package_step', 10, 2 );
function woocommerce_quantity_input_package_step( $step, $product ) {
    if( ! is_a($product, 'WC_Product') ) return $step;

    $step = floatval( $product->get_meta('pack_qty') );
    return $step ? $step : 1;
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
    if( ! is_a($product, 'WC_Product') ) return $args;

    if( $args['step'] && $args['input_value'] < $args['step'] || (1 == $args['input_value'] && $args['step'] < 1) ) {
        $args['input_value'] = floatval($args['step']);
    }

    /**
     * If value lower then min value
     */
    if( $args['min_value'] && $args['input_value'] < $args['min_value'] ) {
        $args['input_value'] = floatval($args['min_value']);
    }

    /**
     * Do not frequency
     */
    if( $args['step'] ) {
        $value = $args['input_value'] / $args['step'];
        if( intval($value) !== $value ) {
            for ($i = 0; ($value = $i * $args['step']) < $args['input_value']; $i++) {
            }

            $args['input_value'] = $value;
        }
    }

    return $args;
}
