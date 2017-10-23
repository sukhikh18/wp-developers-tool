<?php

$args = array(
    array(
        'id'        => 'sticky',
        'label'     => 'Использовать липкий контейнер',
        'type'      => 'select',
        'options'      => array(
            ''           => 'Не использовать',
            'forever'    => 'Использовать всегда',
            'phone_only' => 'Только для телефона',
        ),
        'desc'      => 'При прокрутке вниз, контейнер прилипает к верхней части экрана',
    ),
    array(
        'type'      => 'text',
        'id'        => 'sticky_selector',
        'label'     => 'Селектор липкого контейнера',
        'placeholder' =>  '.navbar-default',
        'desc'      => 'Введите jQuery сселектор (.ExampleClass или #ExampleID)',
        'default' =>  '.navbar-static-top',
    ),
    array(
        'type'      => 'number',
        'id'        => 'smooth_scroll',
        'label'     => 'Плаваня прокрутка',
        'desc'      => 'Плавно пролистает за указанное количество пикселей до начала объекта, если ссылка начинается с # (Укажите высоту отступа до объекта)[href=#obj]',
        'placeholder'   => '40',
    ),
    array(
        'type'      => 'number',
        'id'        => 'scroll_after_load',
        'label'     => 'Прокрутка после загрузки страницы',
        'desc'      => 'Плавно пролистает за указанное количество пикселей до начала объекта, если адрес заканчивается на ID объекта [http://#obj]',
        'placeholder'   => '40',
    ),
    array(
        'type'      => 'text',
        'id'        => 'back_top',
        'label'     => 'Содержимое кнопки "Наверх"',
        'desc'      => 'Задайте кнопке #back-top собственный стиль',
        'placeholder'   => '<i class="fa fa-angle-up" aria-hidden="true"></i>',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'font_awesome',
        'label'     => 'FontAwesome шрифт',
        //'desc'      => 'Подключить шрифтовые иконки <a href="http://fontawesome.io/get-started/">FontAwesome</a>',
    ),
    array(
        'type'      => 'checkbox', // тип
        'id'        => 'animate',
        'label'     => 'Подключить анимацию.css',
        'desc'      => 'Подключает всем известный файл animate.css',
    ),
    array(
        'type'      => 'text',
        'id'        => 'countTo',
        'label'     => 'Счетчик countTo',
        'desc'      => 'Селектор счетчика, обьекту задать:<i> data-from="(int)" data-to="(int)"</i> дополнительно: data-speed="(int)" data-refresh-interval="(int)"',
        'placeholder'   => '.timer',
    ),
    array(
        'type'      => 'checkbox',
        'id'        => 'appearJs',
        'label'     => 'Подключить appear',
        'desc'      => '',
    ),
);

return apply_filters( 'dtools_settings', $args, 'scripts' );
