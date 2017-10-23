<?php

$args = array(
    array(
        'id'        => 'orign-image-resize',
        'label'     => 'Сжимать оригиналы изображений',
        'type'      => 'select',
        'options'      => array(
            ''        => 'Не сжимать',
            'default' => 'Сжимать стандартно (1600х1024)',
            'large'   => 'Сжимать до "Крупного" размера',
        ),
    ),
    array(
        'id'        => 'remove-images',
        'label'     => 'Удалять прикрепленные изображения в след за записью',
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
        'type'      => 'select',
        'options'   => array(
            ''       => 'Не использовать',
            'loop'   => 'Для The loop',
            'detail' => 'На детальной странице',
        ),
        'id'        => 'second-title',
        'label'     => 'Включить дополнительные заголовки',
        'desc'      => 'Изменить заголовок ..',
    ),
    array(
        'type'      => 'textarea',
        'id'        => 'maintenance-mode',
        'label'     => 'Включить техническое обслуживание',
        'placeholder'   => "Сайт находится на техническом обслуживании.\nПожалуйста, зайдите позже.",
        'desc'      => 'Техническое обслуживание времено закроет доступ к сайту (кроме пользователей с привелегией "edit_themes") с указанным сообщением.',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'remove-emojis',
        'label'     => 'Отключить wp_emoji',
        'desc'      => '',
    ),
);

return apply_filters( 'dtools_settings', $args, 'general' );
