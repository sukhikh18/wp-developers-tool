<?php

namespace NikolayS93\Tools;

use NikolayS93\WPAdminForm\Form as Form;

$data = get_scripts_settings();

$form = new Form( $data, $is_table = true );
$form->display();

submit_button( 'Сохранить', 'primary', 'save_changes' );

function get_scripts_settings() {
    $args = array(
        array(
            'id'          => 'smooth_scroll',
            'type'        => 'number',
            'label'       => __('Smooth scroll', DOMAIN),
            'desc'        => __('Smoothly scrolls over a specified number of pixels before the start of the object if the reference starts with # (Specify the height-to-object distance)[href=#obj]', DOMAIN),
            'placeholder' => '40',
        ),
        array(
            'id'          => 'scroll_after_load',
            'type'        => 'number',
            'label'       => __('Scroll after page on load', DOMAIN),
            'desc'        => __('Smoothly scrolls over a specified number of pixels before the start of the object, if the address ends with object ID [http://#obj]', DOMAIN),
            'placeholder' => '40',
        ),
        array(
            'id'          => 'back_top',
            'type'        => 'text',
            'label'       => __('"On top" button content', DOMAIN),
            'desc'        => __('Set style for #back-top', DOMAIN),
            'placeholder' => '<i class="fa fa-angle-up" aria-hidden="true"></i>',
        ),
    );

    return apply_filters( 'get_scripts_settings', $args );
}

