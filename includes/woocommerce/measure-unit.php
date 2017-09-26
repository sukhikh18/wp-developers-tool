<?php

namespace DTools;

if( ! class_exists( __NAMESPACE__ . '\WCProductSettings') ) {
	return;
}

add_action( 'woocommerce_after_add_to_cart_quantity',
	__NAMESPACE__ . '\add_unit_after_add_to_cart_quantity', 30 );
function add_unit_after_add_to_cart_quantity() {
	global $product;

	if( ! is_a($product, 'WC_Product') ) {
		return false;
	}

	$default_unit = DevelopersTools::$settings['product-measure-unit'];
	$unit = $product->get_meta('unit') ? $product->get_meta('unit') : $default_unit;

	echo sprintf("<span class='qty-unit'>%s</span>", esc_html( $unit ) );
}

if( is_admin() ) {
	$wc_fields = new WCProductSettings();
	$wc_fields->add_field(array(
		'type'        => 'text',
		'id'          => 'unit',
		'label'       => 'Ед. измерения',
		'desc_tip'    => 'true',
		'placeholder' => 'К примеру: "шт."',
		// 'description' => 'На сайте это будет отображаться примерно как "Цена ## руб. за шт."',
		) );
	$wc_fields->set_fields();
}