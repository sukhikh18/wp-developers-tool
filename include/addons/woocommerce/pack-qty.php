<?php

namespace NikolayS93\Tools {
    if( is_admin() && class_exists( __NAMESPACE__ . '\WCProductSettings') ) {
        $wc_fields = new WCProductSettings();

        $wc_fields->add_field( array(
            'id'          => 'pack_qty',
            'type'        => 'number',
            'label'       => __('Pack qty', DOMAIN), // Кол-во в упаковке
            // 'description' => __('На сайте это будет отображаться примерно как "Цена ## руб. за шт."', DOMAIN),
            // 'placeholder' => __('К примеру: "шт."', DOMAIN),
            // 'desc_tip'    => 'true',
            ) );

        $wc_fields->set_fields();
    }
}

namespace {
    if( !function_exists('get_pack_qty') ) {
        function get_pack_qty() {
            global $product;
            if( ! is_a($product, 'WC_Product') ) return false;

            return $product->get_meta('pack_qty');
        }
    }

    if( !function_exists('get_pack_regular_price') ) {
        function get_pack_regular_price() {
            global $product;
            if( ! is_a($product, 'WC_Product') ) return false;

            return get_pack_qty() * $product->get_regular_price();
        }
    }

    if( !function_exists('get_pack_sale_price') ) {
        function get_pack_sale_price() {
            global $product;
            if( ! is_a($product, 'WC_Product') ) return false;

            return get_pack_qty() * $product->get_sale_price();
        }
    }

    if( !function_exists('get_pack_price') ) {
        function get_pack_price() {
            global $product;
            if( ! is_a($product, 'WC_Product') ) return false;

            return get_pack_qty() * $product->get_price();
        }
    }
}
