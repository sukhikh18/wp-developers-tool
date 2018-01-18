<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'      => 'orign-image-resize',
        'type'    => 'select',
        'label'   => __('Compress original images', DOMAIN), // 'Сжимать оригиналы изображений'
        'desc'    => __('If the size of the uploaded image exceeds this, the original will be deleted, and instead the image with the specified size will be renamed to the original.', DOMAIN), // Если размер загруженного изображения превышает указанный, оригинал будет удален, а вместо него изображение с указанным размером переименуется в оригинал.
        'options' => array(
            ''        => __('Do not compress', DOMAIN), // Не сжимать
            'default' => __('Default compress', DOMAIN), // Сжимать стандартно (1600х1024)
            // 'large'   => __('Сжимать до "Крупного" размера', DOMAIN),
            ),
        ),
    array(
        'id'      => 'second-title',
        'type'    => 'select',
        'label'   => __('Enable second titles', DOMAIN), // Включить дополнительные заголовки
        'desc'    => __('Change title ..', DOMAIN), // Изменить заголовок
        'options' => array(
            ''       => __('Do not use', DOMAIN), // Не использовать
            'loop'   => __('For "The loop"', DOMAIN), // Для "The loop"
            'detail' => __('For detail only', DOMAIN), // На детальной странице
            ),
        'custom_attributes' => array(
            'cols' => 80,
            'rows' => 3,
            )
        ),
    array(
        'id'      => 'record-views',
        'type'    => 'select',
        'label'   => __('Record views', DOMAIN), // Записывать количество просмотров
        'desc'    => __('Record views data to meta field "total_views"', DOMAIN), // Записывать данные о количестве просмотров в дополнительное поле total_views
        'options' => array(
            ''        => __('Do not use', DOMAIN), // Не использовать
            'all'     => __('For all post types', DOMAIN), // Записывать для всех
            'post'    => __('For "post" only', DOMAIN), // Только для записей
            'page'    => __('For "page" only', DOMAIN), // Только для страниц
            'product' => __('For "product" only', DOMAIN), // Только для товаров
            ),
        ),
    array(
        'id'      => 'remove-images',
        'type'    => 'select',
        'label'   => __('Remove attached images', DOMAIN), // 'Удалять прикрепленные изображения'
        'desc'    => __('Remove attached images, when post will be deleted', DOMAIN), // Если изображение привязано к записи, оно будет удалено, когда удалят запись
        'options' => array(
            ''        => __( 'Do not remove', DOMAIN ), // 'Не удалять'
            'all'     => __( 'Remove all', DOMAIN ), // 'Удалять все'
            'post'    => __( 'For post only', DOMAIN ), // Только для записей
            'page'    => __( 'For page only', DOMAIN ), // Только для страниц
            'product' => __( 'For products only', DOMAIN ), // Только для товаров
            ),
        ),
    array(
        'id'          => 'empty-content',
        'type'        => 'text',
        'label'       => __('Empty page message', DOMAIN), // Сообщение пустой страницы
        'desc'        => __('Set message when page is empty', DOMAIN), // Если страница не заполнена, показывать сл. сообщение
        'placeholder' => __('<h3>Page be under development</h3>', DOMAIN), // Страница находится в стадии разработки
        'input_class' => 'widefat',
        ),
    array(
        'id'          => 'maintenance-mode',
        'type'        => 'textarea',
        'label'       => __('Enable maintenance', DOMAIN), // Включить техническое обслуживание
        'desc'        => __('Maintenance for temporary close your site', DOMAIN), // Техническое обслуживание времено закроет доступ к сайту (кроме пользователей с привелегией "edit_themes") с указанным сообщением.
        'placeholder' => __("The website is in maintenance.\nPlease check back later.", DOMAIN), // Сайт находится на техническом обслуживании.\nПожалуйста, зайдите позже.
        'input_class' => 'widefat',
        ),
    array(
        'id'    => 'remove-emojis',
        'type'  => 'checkbox',
        'label' => __('Disable wp_emoji', DOMAIN), // Отключить wp_emoji
        'desc'  => __('Disable default Wordpress emoji smiles', DOMAIN), // Отключить стандартную функционал Wordpress поддерживающий Emoji смайлы
        ),
    );

return apply_filters( 'dtools_settings', $args, 'general' );
