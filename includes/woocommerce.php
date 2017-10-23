<?php

namespace CDevelopers\tool;

if ( class_exists( 'WooCommerce' ) ) {
    add_filter('dtools_classes', __NAMESPACE__ . '\dtools_woocommerce_class', 5, 1);
    add_filter('dtools_active',  __NAMESPACE__ . '\dtools_woocommerce_active', 5, 1);
    add_filter('dtools_page_args', __NAMESPACE__ . '\dtools_woocommerce_page_args', 5, 1);
}

function dtools_woocommerce_class( $classes ) {
    $classes[ __NAMESPACE__ . '\WCProductSettings'] = '/class-wc-product-settings.php';

    return $classes;
}

function dtools_woocommerce_active( $active ) {
    $active[ 'product-measure-unit' ] = DIR_INCLUDES . '/addons/woocommerce/measure-unit.php';
    $active[ 'plus-minus-buttons' ]   = DIR_INCLUDES . '/addons/woocommerce/plus-minus-buttons.php';
    $active[ 'pack-qty' ]             = DIR_INCLUDES . '/addons/woocommerce/pack-qty.php';
    $active[ 'pack-qty-changes' ]     = DIR_INCLUDES . '/addons/woocommerce/pack-qty-changes.php';
    $active[ 'wholesales' ]           = DIR_INCLUDES . '/addons/woocommerce/wholesales.php';
    $active[ 'bestsellers' ]          = DIR_INCLUDES . '/addons/woocommerce/bestsellers.php';

    return $active;
}

function dtools_woocommerce_page_args( $args ) {
    $args[ 'tab_sections' ][ DTools::PREFIX . 'woocommerce' ] = __('WooCommerce');
    $args[ 'callback' ][ DTools::PREFIX . 'woocommerce' ] = __NAMESPACE__ . '\woocommerce_settings_tab';

    return $args;
}

function woocommerce_settings_tab(){
    echo (new WP_Admin_Forms( DTools::get_settings('woocommerce'), true ))->render();
}