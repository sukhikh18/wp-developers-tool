<?php

namespace NikolayS93\Tools;

if ( is_admin() && class_exists(__NAMESPACE__ . '\WCProductSettings') ) {
    $wc_fields = new WCProductSettings();
    $wc_fields->add_field( array(
        'id'          => 'discount-by-summary',
        'type'        => 'number',
        'label'       => __('Discount from', DOMAIN),
        'description' => __('Set discount price when cart summary price from..', DOMAIN),
        'desc_tip'    => true,
        'custom_attributes' => array(
            'step' => 'any',
            'min'  => '1',
        ),
    ) );

    $wc_fields->set_fields();
}

add_action( 'woocommerce_before_calculate_totals', function ( $cart_object ) {
    $cart_items = $cart_object->get_cart();

    $subtotal = array_reduce( $cart_items, function ( $v, $cart_item ) {
        $cart_item_subtotal = $cart_item['data']->get_price() * $cart_item['quantity'];
        return $v + $cart_item_subtotal;
    }, 0 );

    foreach ( $cart_items as $cart_item ) {
        $product = $cart_item['data'];
        $by_summary_price = floatval( get_post_meta( $product->get_id(), $key = 'discount-by-summary', $single = true ) );
        // Disable discount
        if ( $by_summary_price > $subtotal ) {
            $product->set_price( $product->get_regular_price() );
        }
    }
} );

