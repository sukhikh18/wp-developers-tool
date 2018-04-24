<?php

namespace NikolayS93\Tool;

add_action( 'wp_head', __NAMESPACE__ . '\plus_minus_styles' );
function plus_minus_styles() { ?>
    <style type="text/css">
        div.product #plus-minus-qty-wrapper .minus,
        div.product #plus-minus-qty-wrapper .plus {
            display: inline-block;
            width: 25px;
            font-weight: 700;
            font-size: 15px;
            text-align: center;
            background: #ededed;
            padding: 2px 0;
            border-radius: 2px;
            box-shadow: 0 2px 0 #dcdcdc;
            cursor: pointer;
            position: relative;
            vertical-align: middle;
            top: -2px;
        }
        div.product #plus-minus-qty-wrapper div.quantity {
            float: none;
            display: inline-block;
            margin: 0;
            vertical-align: middle;
        }
        div.product #plus-minus-qty-wrapper div.quantity .qty::-webkit-inner-spin-button,
        div.product #plus-minus-qty-wrapper div.quantity .qty::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        div.product #plus-minus-qty-wrapper div.quantity .qty {
            -moz-appearance: textfield;
        }
    </style>
    <?php
}

# start qty
add_action('woocommerce_before_add_to_cart_quantity', __NAMESPACE__ . '\plus_minus_buttons_start', 10);
function plus_minus_buttons_start() { ?>
    <div id="plus-minus-qty-wrapper">
    <?php
}

# end qty
add_action('woocommerce_after_add_to_cart_quantity', __NAMESPACE__ . '\plus_minus_buttons_end', 10);
function plus_minus_buttons_end() { ?>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $qty = $('[name="quantity"]');
            var qty_step = $qty.attr('step') > 1 ? $qty.attr('step') : 1;

            $('#plus-minus-qty-wrapper').prepend('<span class="minus">-</span>');
            $('#plus-minus-qty-wrapper').append( '<span class="plus">+</span>');

            $('#plus-minus-qty-wrapper .minus').on('click', function(event) {
                event.preventDefault();
                $qty.val( function( val ){
                    var min = $qty.attr('min') || 0;
                    return ( +$qty.val() <= min ) ? min : +$qty.val() - +qty_step;
                } );
                $qty.trigger('change');
            });

            $('#plus-minus-qty-wrapper .plus').on('click', function(event) {
                event.preventDefault();
                $qty.val( function( val ){
                    var max = +$qty.attr('max');
                    return ( max && +$qty.val() > max ) ? max : +$qty.val() + +qty_step;
                } );
                $qty.trigger('change');
            });

            // $('.plus').on('click', function(event) {
            //   console.log(qty_val());
            //   if( qty_val() - 1 > $qty.attr('max') ) $qty.val( --qty_val );
            // });
        });
    </script>
    <?php
}
