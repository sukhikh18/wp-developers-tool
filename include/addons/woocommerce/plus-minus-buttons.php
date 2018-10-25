<?php

namespace NikolayS93\Tools;

add_action( 'wp_head', __NAMESPACE__ . '\plus_minus_styles' );
function plus_minus_styles() {
    if( apply_filters( 'disable_plus_minus_styles', false ) ) return;
    ?>
    <style type="text/css">
        .plus-minus-qty-wrapper {
            float: left;
            margin-right: .875em;
        }
        div.product .plus-minus-qty-wrapper .minus,
        div.product .plus-minus-qty-wrapper .plus {
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
        div.product .plus-minus-qty-wrapper div.quantity {
            float: none;
            display: inline-block;
            margin: 0;
            vertical-align: middle;
        }
        div.product .plus-minus-qty-wrapper div.quantity .qty::-webkit-inner-spin-button,
        div.product .plus-minus-qty-wrapper div.quantity .qty::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        div.product .plus-minus-qty-wrapper div.quantity .qty {
            -moz-appearance: textfield;
        }
    </style>
    <?php
}

# start qty
add_action('woocommerce_before_add_to_cart_quantity', __NAMESPACE__ . '\plus_minus_buttons_start', 10);
function plus_minus_buttons_start() {
    echo '<div class="plus-minus-qty-wrapper">';
}

# end qty
add_action('woocommerce_after_add_to_cart_quantity', __NAMESPACE__ . '\plus_minus_buttons_end', 10);
function plus_minus_buttons_end() {
    echo '</div><!-- .plus-minus-qty-wrapper -->';
}

add_action('wp_footer', __NAMESPACE__ . '\plus_minus_script', 10);
function plus_minus_script() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            var $wrappers = $('.plus-minus-qty-wrapper');

            $wrappers.each(function(index, el) {
                var $wrap = $(this);
                var $qty = $('[name="quantity"]', $wrap);

                var step = +$qty.attr('step') >= 0.1 ? +$qty.attr('step') : 1;
                var min  = +$qty.attr('min') >= 0.1 ? +$qty.attr('min') : 1;
                var max  = +$qty.attr('max') >= 0.1 ? +$qty.attr('max') : 9999999;

                /**
                 * How much toFixed numbers
                 */
                var fixed = step * 100 % 10 ? 2 : 1;

                var $plus = $('<span class="plus">+</span>');
                $plus.on('click', function(event) {
                    event.preventDefault();

                    // observe restrictions
                    if( +$qty.val() >= max ) return;

                    // increase value
                    $qty.val( (+$qty.val() + step).toFixed(fixed) );

                    // we change it
                    $qty.trigger('change');
                });

                var $minus = $('<span class="minus">-</span>');
                $minus.on('click', function(event) {
                    event.preventDefault();

                    // observe restrictions
                    if( +$qty.val() <= min ) return min;

                    // decrease value
                    $qty.val( ($qty.val() - step).toFixed(fixed) );

                    // we change it
                    $qty.trigger('change');
                });

                $wrap.prepend( $minus );
                $wrap.append( $plus );
            }); // end each
        });
    </script>
    <?php
}
