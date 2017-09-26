<?php

function get_pack_qty() {
    global $product;

    if( ! is_a($product, 'WC_Product') ) {
        return false;
    }

    return $product->get_meta('pack_qty');
}

function get_pack_regular_price() {
    global $product;

    if( ! is_a($product, 'WC_Product') ) {
        return false;
    }

    return get_pack_qty() * $product->get_regular_price();
}

function get_pack_sale_price() {
    global $product;

    if( ! is_a($product, 'WC_Product') ) {
        return false;
    }

    return get_pack_qty() * $product->get_sale_price();
}

function get_pack_price() {
    global $product;

    if( ! is_a($product, 'WC_Product') ) {
        return false;
    }

    return get_pack_qty() * $product->get_price();
}

if( is_admin() && class_exists( 'DTools\WCProductSettings') ) {
    $wc_fields = new DTools\WCProductSettings();
    $wc_fields->add_field(array(
        'type'        => 'number',
        'id'          => 'pack_qty',
        'label'       => 'Кол-во в упаковке',
        // 'desc_tip'    => 'true',
        // 'placeholder' => 'К примеру: "шт."',
        // 'description' => 'На сайте это будет отображаться примерно как "Цена ## руб. за шт."',
        ) );
    $wc_fields->set_fields();
}