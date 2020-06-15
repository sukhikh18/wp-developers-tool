<?php

namespace NikolayS93\Tools;

add_filter( 'woocommerce_quantity_input_step', __NAMESPACE__ . '\woocommerce_quantity_input_package_step', 10, 2 );
function woocommerce_quantity_input_package_step( $step, $product ) {
    if( ! is_a($product, 'WC_Product') ) return $step;

    if( Plugin::get_setting( 'pack-qty-cat' ) ) {
        $cats = wc_get_product_cat_ids( $product->get_id() );
        $cat = current( $cats );

        $step = get_cat_frequency( $cat );
    }
    else {
        $step = floatval( $product->get_meta('pack_qty') );
    }

    return $step ? $step : 1;
}

add_filter( 'woocommerce_quantity_input_min', __NAMESPACE__ . '\quantity_input_min', 50, 2 );
function quantity_input_min( $min, $product ) {
	if( ! is_a($product, 'WC_Product') ) return $min;

    if( Plugin::get_setting( 'pack-qty-cat' ) ) {
        $cats = wc_get_product_cat_ids( $product->get_id() );
        $cat = current( $cats );

        $nMin = get_cat_frequency( $cat );
    }
    else {
        $nMin = $product->get_meta('pack_qty');
    }

    if( 1 > floatval($nMin) && $min == 1 ) {
        $min = $nMin;
    }

    return $min;
}

add_filter( 'woocommerce_quantity_input_args', __NAMESPACE__ . '\quantity_input_value', 50, 2 );
function quantity_input_value( $args, $product ) {
    if( ! is_a($product, 'WC_Product') ) return $args;

    $input_value = !empty($args['input_value']) ? floatval($args['input_value']) : 1;
    $step        = !empty($args['step']) ? floatval($args['step']) : 1;
    $min_value   = !empty($args['min_value']) ? floatval($args['min_value']) : 1;

    if( $step && $input_value < $step || (1 == $input_value && $step < 1) ) {
        $input_value = $step;
    }

    if( $step > $min_value ) {
        $min_value = $step;
    }

    /**
     * If value lower then min value
     */
    if( $min_value && $input_value < $min_value ) {
        $input_value = $min_value;
    }

    /**
     * Do not frequency
     */
    if( $step ) {
        $value = $input_value / $step;
        if( intval($value) !== $value ) {
            for ($i = 0; ($value = $i * $step) < $input_value; $i++) {
            }

            $input_value = $value;
        }
    }

    // echo "<pre>";
    // var_dump( compact( array('input_value', 'step', 'min_value') ) );
    // echo "</pre>";

    $args = array_merge( $args, compact( array('input_value', 'step', 'min_value') ) );

    return $args;
}
