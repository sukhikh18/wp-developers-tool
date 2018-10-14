<?php

namespace NikolayS93\Tools;

if ( class_exists( '\WooCommerce' ) ) {
    require_once PLUGIN_DIR . '/include/class-wc-product-settings.php';

    add_filter('Utils_active',  __NAMESPACE__ . '\Utils_woocommerce_active', 5, 1);
}

function Utils_woocommerce_active( $active ) {
    $wc_addons = PLUGIN_DIR . '/include/addons/woocommerce';

    $active[ 'product-measure-unit' ] = $wc_addons . '/measure-unit.php';
    $active[ 'plus-minus-buttons' ]   = $wc_addons . '/plus-minus-buttons.php';
    $active[ 'pack-qty' ]             = $wc_addons . '/pack-qty.php';
    $active[ 'pack-qty-changes' ]     = $wc_addons . '/pack-qty-changes.php';
    $active[ 'wholesales' ]           = $wc_addons . '/wholesales.php';
    $active[ 'bestsellers' ]          = $wc_addons . '/bestsellers.php';

    return $active;
}
