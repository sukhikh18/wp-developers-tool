<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'type'      => 'select',
        'id'        => 'bestsellers',
        'label'     => __('Популярный товар', DOMAIN),
        'options' => array(
            ''         => 'Не использовать',
            'personal' => 'Использовать вручную',
            'views'    => 'Сортировать по просмотрам',
            'sales'    => 'Сортировать по продажам',
        ),
        'desc'      => __('Установите плагин easy-wp-queries и исользуете <strong> [query type="top-sales"] </strong>', DOMAIN),
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'wholesales',
        'label'     => __('Оптовые продажи', DOMAIN),
        'desc'      => __('Разрешить продажу от.. шт.', DOMAIN),
    ),
    array(
        'type'      => 'text',
        'id'        => 'product-measure-unit',
        'label'     => __('Добавить товару ед. измерения', DOMAIN),
        'desc'      => __('Укажите стандартную величину', DOMAIN),
        'default'   => 'шт.',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'pack-qty',
        'label'     => __('Добавить количество в упаковке', DOMAIN),
        'desc'      => '',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'plus-minus-buttons',
        'label'     => __('Добавить +/-', DOMAIN),
        'desc'      => __('Добавить кнопки + и - для увеличения/уменьшения количество покупки', DOMAIN),
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'pack-qty-changes',
        'label'     => __('Изменять количетсво по упаковкам', DOMAIN),
        'desc'      => __('Шаг изменений при нажатии + / - (Вверх, Вниз) будет равен указанным количеством в упаковке (Не противодействует вводу в ручную)', DOMAIN),
    ),
);

return apply_filters( 'dtools_settings', $args, 'woocommerce' );
