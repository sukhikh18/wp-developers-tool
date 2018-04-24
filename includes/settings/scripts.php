<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'      => 'sticky',
        'type'    => 'select',
        'label'   => __('Use sticky container', DOMAIN),
        'desc'    => __('When you scroll down, the container sticks to the top of the screen', DOMAIN) . '(<a href="http://stickyjs.com/">Sticky site</a>)',
        'options' => array(
            ''           => __('Do not use', DOMAIN),
            'forever'    => __('Use forever', DOMAIN),
            'phone_only' => __('For mobile only', DOMAIN),
        ),
    ),
    array(
        'id'          => 'sticky_selector',
        'type'        => 'text',
        'label'       => __('Sticky selector', DOMAIN),
        'desc'        => __('Enter Jquery selector (for ex. .ExampleClass or #ExampleID)', DOMAIN),
        'placeholder' =>  '.navbar-default',
    ),
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
    array(
        'id'    => 'font_awesome',
        'type'  => 'checkbox',
        'label' => __('FontAwesome fonts', DOMAIN),
        'desc'  => __('Enqueue <a target="_blank" href="http://fontawesome.io/get-started/">FontAwesome</a> fonts', DOMAIN),
    ),
    array(
        'id'    => 'animate',
        'type'  => 'checkbox',
        'label' => __('Enqueue animate.css', DOMAIN),
        'desc'  => __('Enqueue popular library <a target="_blank" href="https://daneden.github.io/animate.css/" >animate.css</a>', DOMAIN),
    ),
    array(
        'id'    => 'wow',
        'type'  => 'select',
        'label' => __('Animate lib WOW.js', DOMAIN),
        'desc'  => sprintf('%s %s <i>
            data-wow-duration="2s"
            data-wow-delay="5s"
            data-wow-offset="10"
            data-wow-iteration="10"</i>',
                __('When you scroll down, the items change class for animate (for example using animate.css).<br>Objects to specify a class: <a target="_blank" href="http://mynameismatthieu.com/WOW/" >wow</a> together with his. ', DOMAIN),
                __('advanced:', DOMAIN)
            ),
        'options' => array(
            ''          => __('Do not use', DOMAIN),
            'forever'   => __('Use forever', DOMAIN),
            'not_phone' => __('Use for desktop only', DOMAIN),
        ),
    ),
    array(
        'id'    => 'countTo',
        'type'  => 'text',
        'label' => __('countTo lib', DOMAIN),
        'desc'  => sprintf('%s <i>data-from="(int)" data-to="(int)"</i> %s data-speed="(int)" data-refresh-interval="(int)"',
            __('Use <a target="_blank" href="https://github.com/mhuggins/jquery-countTo">count</a> attributes:', DOMAIN),
            __('Advanced:', DOMAIN)
        ),
        'placeholder' => '.timer',
    ),
    array(
        'id'    => 'appearJs',
        'type'  => 'checkbox',
        'label' => __('Enqueue appear', DOMAIN),
        'desc'  => __('Enqueue <a target="_blank" href="http://creativelive.github.io/appear/">appear</a> lib'),
    ),
);

return apply_filters( 'dtools_settings', $args, 'scripts' );
