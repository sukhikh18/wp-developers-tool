<?php

namespace NikolayS93\Tools;

if ( class_exists( '\WooCommerce' ) ) {
    require_once PLUGIN_DIR . '/include/class-wc-product-settings.php';

    add_filter('Utils_active',  __NAMESPACE__ . '\Utils_woocommerce_active', 5, 1);
}

function Utils_woocommerce_active( $active ) {
    $wc_addons = PLUGIN_DIR . '/include/addons/woocommerce';

    $active = array_merge($active, array(
        'product-measure-unit' => $wc_addons . '/measure-unit.php',
        'wholesales'           => $wc_addons . '/wholesales.php',
        'plus-minus-buttons'   => $wc_addons . '/plus-minus-buttons.php',
        'pack-qty'             => $wc_addons . '/pack-qty.php',
        'pack-qty-changes'     => $wc_addons . '/pack-qty-changes.php',
        'bestsellers'          => $wc_addons . '/bestsellers.php',
        'qty-stock-decimals'   => $wc_addons . '/qty-stock-decimals.php',
    ));

    return $active;
}
