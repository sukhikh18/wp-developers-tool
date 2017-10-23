<?php

$args = array(
    array(
        'type'      => 'select',
        'id'        => 'bestsellers',
        'label'     => 'Популярный товар',
        'options' => array(
            ''         => 'Не использовать',
            'personal' => 'Использовать вручную',
            'views'    => 'Сортировать по просмотрам',
            'sales'    => 'Сортировать по продажам',
        ),
        'desc'      => '
            1. Убедитесь что [query] запросы включены. <br>
            2. Используйте <strong> [query type="top-sales"] </strong>',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'wholesales',
        'label'     => 'Оптовые продажи',
        'desc'      => 'Разрешить продажу от.. шт.',
    ),
    array(
        'type'      => 'text',
        'id'        => 'product-measure-unit',
        'label'     => 'Добавить товару ед. измерения',
        'desc'      => 'Укажите стандартную величину',
        'default'   => 'шт.',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'pack-qty',
        'label'     => 'Добавить количество в упаковке',
        'desc'      => '',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'plus-minus-buttons',
        'label'     => 'Добавить +/-',
        'desc'      => 'Добавить кнопки + и - для увеличения/уменьшения количество покупки',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'pack-qty-changes',
        'label'     => 'Изменять количетсво по упаковкам',
        'desc'      => 'Шаг изменений при нажатии + / - (Вверх, Вниз) будет равен указанным количеством в упаковке (Не противодействует вводу в ручную)',
    ),
);

return apply_filters( 'dtools_settings', $args, 'woocommerce' );
