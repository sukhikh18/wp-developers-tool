<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'        => 'orign-image-resize',
        'label'     => __('Сжимать оригиналы изображений', DOMAIN),
        'type'      => 'select',
        'options'      => array(
            ''        => 'Не сжимать',
            'default' => 'Сжимать стандартно (1600х1024)',
            'large'   => 'Сжимать до "Крупного" размера',
        ),
    ),
    array(
        'type'      => 'select',
        'options'   => array(
            ''       => 'Не использовать',
            'loop'   => 'Для The loop',
            'detail' => 'На детальной странице',
        ),
        'id'        => 'second-title',
        'label'     => __('Включить дополнительные заголовки', DOMAIN),
        'desc'      => 'Изменить заголовок ..',
        'custom_attributes' => array(
            'cols' => 80,
            'rows' => 3,
            )
    ),
    array(
        'id'        => 'record-views',
        'label'     => __('Записывать количество просмотров', DOMAIN),
        'desc'      => __('Записывать данные о количестве просмотров в доп. поле total_views'),
        'type'      => 'select',
        'options'      => array(
            ''        => 'Не использовать',
            'all'     => 'Записывать для всех',
            'post'    => 'Только для записей',
            'page'    => 'Только для страниц',
            'product' => 'Только для товаров',
        ),
    ),
    array(
        'id'        => 'remove-images',
        'label'     => __('Удалять прикрепленные изображения в след за записью', DOMAIN),
        'type'      => 'select',
        'options'      => array(
            ''        => 'Не удалять',
            'all'     => 'Удалять все',
            'post'    => 'Только для записей',
            'page'    => 'Только для страниц',
            'product' => 'Только для товаров',
        ),
    ),
    array(
        'type'    => 'text',
        'id'      => 'empty-content',
        'label'   => __('Сообщение пустой страницы', DOMAIN),
        'desc'    => __('Если страница не заполнена, показывать сл. сообщение', DOMAIN),
        'default' => '<h3>Страница находится в стадии разработки</h3>',
        'input_class' => 'widefat',
    ),
    array(
        'type'      => 'textarea',
        'id'        => 'maintenance-mode',
        'label'     => __('Включить техническое обслуживание', DOMAIN),
        'placeholder'   => "Сайт находится на техническом обслуживании.\nПожалуйста, зайдите позже.",
        'desc'      => 'Техническое обслуживание времено закроет доступ к сайту (кроме пользователей с привелегией "edit_themes") с указанным сообщением.',
        'input_class' => 'widefat',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'remove-emojis',
        'label'     => __('Отключить wp_emoji', DOMAIN),
        'desc'      => '',
    ),
);

return apply_filters( 'dtools_settings', $args, 'general' );
