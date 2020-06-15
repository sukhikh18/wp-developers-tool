<?php

namespace NikolayS93\Tools;

use NikolayS93\WPAdminForm\Form as Form;

$summary = array(
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
);

$frequency = array(
    array(
        'id'    => 'qty-stock-decimals',
        'type'  => 'checkbox',
        'label' => __('Allow decimal quantities', DOMAIN),
        'desc'  => '',
    ),
    array(
        'id'    => 'pack-qty',
        'type'  => 'checkbox',
        'label' => __('Add qty in pack', DOMAIN),
        'desc'  => '',
    ),
    array(
        'id'    => 'pack-qty-cat',
        'type'  => 'checkbox',
        'label' => __('Frequency by category', DOMAIN),
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

$form = new Form( $summary, $is_table = true );
$form->display();

?>
<div class="postbox-container">
    <div>
        <div class="postbox hide-if-js" style="display: block;">
            <h2 class="hndle" style="cursor: pointer;"><span>Кратность</span></h2>
            <div class="inside">
                <?php

                $form = new Form( $frequency, $is_table = true );
                $form->display();

                ?>
            </div>
        </div>
    </div>
</div>
<?php

submit_button( 'Сохранить', 'primary', 'save_changes' );
