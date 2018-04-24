<?php

namespace NikolayS93\Tool;

$args = array(
    array(
        'id'      => 'bestsellers',
        'type'    => 'select',
        'label'   => __('Popular product', DOMAIN),
        'desc'    => '',
        'options' => array(
            ''         => __('Do not use', DOMAIN),
            'personal' => __('Use manually', DOMAIN),
            'views'    => __('Sort by Views', DOMAIN),
            'sales'    => __('Sort by sales', DOMAIN),
        ),
    ),
    array(
        'id'    => 'wholesales',
        'type'  => 'checkbox',
        'label' => __('Wholesales', DOMAIN),
        'desc'  => __('Allow sell out bigger only, than..', DOMAIN),
    ),
    array(
        'id'          => 'product-measure-unit',
        'type'        => 'text',
        'label'       => __('Add measure unit', DOMAIN),
        'desc'        => __('Set default unit', DOMAIN),
        'placeholder' => __('pcs', DOMAIN),
    ),
    array(
        'id'    => 'pack-qty',
        'type'  => 'checkbox',
        'label' => __('Add qty in pack', DOMAIN),
        'desc'  => '',
    ),
    array(
        'id'    => 'plus-minus-buttons',
        'type'  => 'checkbox',
        'label' => __('Add +/-', DOMAIN),
        'desc'  => __('Add +/- buttons for qty increase/decrease', DOMAIN),
    ),
    array(
        'id'    => 'pack-qty-changes',
        'type'  => 'checkbox',
        'label' => __('Increase step size', DOMAIN),
        'desc'  => __('The step change by pressing + / - (Up, Down) will be equal to the specified quantity in the package (Do not oppose entry into the manual)', DOMAIN),
    ),
);

return apply_filters( 'dtools_settings', $args, 'woocommerce' );
