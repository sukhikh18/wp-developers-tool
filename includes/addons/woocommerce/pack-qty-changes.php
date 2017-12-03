<?php

add_filter( 'woocommerce_quantity_input_step', 'woocommerce_quantity_input_package_step', 10, 1 );
function woocommerce_quantity_input_package_step( $step ) {
    global $product;
    if( ! is_a($product, 'WC_Product') ) return $step;

    return $product->get_meta('pack_qty');
}
