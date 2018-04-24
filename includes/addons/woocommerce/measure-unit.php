<?php

namespace NikolayS93\Tool;

add_action( 'woocommerce_after_add_to_cart_quantity',
	__NAMESPACE__ . '\add_unit_after_add_to_cart_quantity', 30 );
function add_unit_after_add_to_cart_quantity() {
	global $product;

	if( ! is_a($product, 'WC_Product') )
		return false;

	$unit = $product->get_meta('_unit') ?
		$product->get_meta('_unit') : DTools::get( 'product-measure-unit' );

	echo sprintf("<span class='qty-unit'>%s</span>", apply_filters( 'dt_measure-unit', $unit ) );
}

add_filter( 'dt_measure-unit', 'esc_html', 10, 1 );

if( is_admin() && class_exists( __NAMESPACE__ . '\WCProductSettings') ) {
	$wc_fields = new WCProductSettings();
	$wc_fields->add_field( array(
		'id'          => '_unit',
		'type'        => 'text',
		'label'       => __('Measure unit', DOMAIN), // ед. измерения
		'placeholder' => __('For ex. "pcs"', DOMAIN), // К примеру: "шт."
		'desc_tip'    => true,
		) );
	$wc_fields->set_fields();
}
