<?php

namespace NikolayS93\Tool;

if ( class_exists( '\WooCommerce' ) ) {
    add_filter('dtools_classes', __NAMESPACE__ . '\dtools_woocommerce_class', 5, 1);
    add_filter('dtools_active',  __NAMESPACE__ . '\dtools_woocommerce_active', 5, 1);
    add_filter('dtools_page_args', __NAMESPACE__ . '\dtools_woocommerce_page_args', 5, 1);
}

function dtools_woocommerce_class( $classes ) {
    $classes[ __NAMESPACE__ . '\WCProductSettings'] =
        Dtools::get_plugin_dir('classes') . '/class-wc-product-settings.php';

    return $classes;
}

function dtools_woocommerce_active( $active ) {
    $includes = Dtools::get_plugin_dir('includes');
    $active[ 'product-measure-unit' ] = $includes . '/addons/woocommerce/measure-unit.php';
    $active[ 'plus-minus-buttons' ]   = $includes . '/addons/woocommerce/plus-minus-buttons.php';
    $active[ 'pack-qty' ]             = $includes . '/addons/woocommerce/pack-qty.php';
    $active[ 'pack-qty-changes' ]     = $includes . '/addons/woocommerce/pack-qty-changes.php';
    $active[ 'wholesales' ]           = $includes . '/addons/woocommerce/wholesales.php';
    $active[ 'bestsellers' ]          = $includes . '/addons/woocommerce/bestsellers.php';

    return $active;
}

function dtools_woocommerce_page_args( $args ) {
    $args[ 'tab_sections' ][ DTools::PREFIX . 'woocommerce' ] = __('WooCommerce');
    $args[ 'callback' ][ DTools::PREFIX . 'woocommerce' ] = __NAMESPACE__ . '\woocommerce_settings_tab';

    return $args;
}

function woocommerce_settings_tab(){
    echo (new WP_Admin_Forms( DTools::get_settings('woocommerce.php'), true ))->render();
}