<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'      => 'bestsellers',
        'type'    => 'select',
        'label'   => __('Popular product', DOMAIN), // 'Популярный товар'
        'desc'    => '', // __('Установите плагин easy-wp-queries и исользуете <strong> [query type="top-sales"] </strong>', DOMAIN),
        'options' => array(
            ''         => __('Do not use', DOMAIN), // __('Не использовать'),
            'personal' => __('Use manually', DOMAIN), // __('Использовать вручную'),
            'views'    => __('Sort by Views', DOMAIN), // __('Сортировать по просмотрам'),
            'sales'    => __('Sort by sales', DOMAIN), // __('Сортировать по продажам'),
        ),
    ),
    array(
        'id'    => 'wholesales',
        'type'  => 'checkbox',
        'label' => __('Wholesales', DOMAIN), // Оптовые продажи
        'desc'  => __('Allow sell out bigger only, than..', DOMAIN), // Разрешить продажу от.. шт.
    ),
    array(
        'id'          => 'product-measure-unit',
        'type'        => 'text',
        'label'       => __('Add measure unit', DOMAIN), // Добавить товару ед. измерения
        'desc'        => __('Set default unit', DOMAIN), // Укажите стандартную величину
        'placeholder' => __('pcs', DOMAIN), // шт.
    ),
    array(
        'id'    => 'pack-qty',
        'type'  => 'checkbox',
        'label' => __('Add qty in pack', DOMAIN), // Добавить количество в упаковке
        'desc'  => '',
    ),
    array(
        'id'    => 'plus-minus-buttons',
        'type'  => 'checkbox',
        'label' => __('Add +/-', DOMAIN), // Добавить +/-
        'desc'  => __('Add +/- buttons for qty increase/decrease', DOMAIN), // Добавить кнопки + и - для увеличения/уменьшения количество покупки
    ),
    array(
        'id'    => 'pack-qty-changes',
        'type'  => 'checkbox',
        'label' => __('Increase step size', DOMAIN), // Изменять количетсво по упаковкам
        'desc'  => __('The step change by pressing + / - (Up, Down) will be equal to the specified quantity in the package (Do not oppose entry into the manual)', DOMAIN), // Шаг изменений при нажатии + / - (Вверх, Вниз) будет равен указанным количеством в упаковке (Не противодействует вводу в ручную)
    ),
);

return apply_filters( 'dtools_settings', $args, 'woocommerce' );
