<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'        => 'sticky',
        'label'     => __('Использовать липкий контейнер', DOMAIN),
        'type'      => 'select',
        'options'      => array(
            ''           => 'Не использовать',
            'forever'    => 'Использовать всегда',
            'phone_only' => 'Только для телефона',
        ),
        'desc'      => __('При прокрутке вниз, контейнер прилипает к верхней части экрана', DOMAIN),
    ),
    array(
        'type'      => 'text',
        'id'        => 'sticky_selector',
        'label'     => __('Селектор липкого контейнера', DOMAIN),
        'placeholder' =>  '.navbar-default',
        'desc'      => __('Введите jQuery сселектор (.ExampleClass или #ExampleID)', DOMAIN),
        'default' =>  '.navbar-static-top',
    ),
    array(
        'type'      => 'number',
        'id'        => 'smooth_scroll',
        'label'     => __('Плаваня прокрутка', DOMAIN),
        'desc'      => __('Плавно пролистает за указанное количество пикселей до начала объекта, если ссылка начинается с # (Укажите высоту отступа до объекта)[href=#obj]', DOMAIN),
        'placeholder'   => '40',
    ),
    array(
        'type'      => 'number',
        'id'        => 'scroll_after_load',
        'label'     => __('Прокрутка после загрузки страницы', DOMAIN),
        'desc'      => __('Плавно пролистает за указанное количество пикселей до начала объекта, если адрес заканчивается на ID объекта [http://#obj]', DOMAIN),
        'placeholder'   => '40',
    ),
    array(
        'type'      => 'text',
        'id'        => 'back_top',
        'label'     => __('Содержимое кнопки "Наверх"', DOMAIN),
        'desc'      => __('Задайте кнопке #back-top собственный стиль', DOMAIN),
        'placeholder'   => '<i class="fa fa-angle-up" aria-hidden="true"></i>',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'font_awesome',
        'label'     => __('FontAwesome шрифт', DOMAIN),
        //'desc'      => __('Подключить шрифтовые иконки <a href="http://fontawesome.io/get-started/">FontAwesome</a>', DOMAIN),
    ),
    array(
        'type'      => 'checkbox', // тип
        'id'        => 'animate',
        'label'     => __('Подключить анимацию.css', DOMAIN),
        'desc'      => __('Подключает всем известный файл animate.css', DOMAIN),
    ),
    array(
        'type'      => 'text',
        'id'        => 'countTo',
        'label'     => __('Счетчик countTo', DOMAIN),
        'desc'      => __('Селектор счетчика, обьекту задать:<i> data-from="(int)" data-to="(int)"</i> дополнительно: data-speed="(int)" data-refresh-interval="(int)"', DOMAIN),
        'placeholder'   => '.timer',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'appearJs',
        'label'     => __('Подключить appear', DOMAIN),
        'desc'      => '',
    ),
);

return apply_filters( 'dtools_settings', $args, 'scripts' );
