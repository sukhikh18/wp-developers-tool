<?php

namespace CDevelopers\tool;

$args = array(
    array(
        'id'      => 'sticky',
        'type'    => 'select',
        'label'   => __('Use sticky container', DOMAIN), // Использовать липкий контейнер
        'desc'    => __('When you scroll down, the container sticks to the top of the screen', DOMAIN) . '(<a href="http://stickyjs.com/">Sticky site</a>)', // При прокрутке вниз, контейнер прилипает к верхней части экрана
        'options' => array(
            ''           => __('Do not use', DOMAIN), // 'Не использовать'
            'forever'    => __('Use forever', DOMAIN), // Использовать всегда
            'phone_only' => __('For mobile only', DOMAIN), // Только для телефона
        ),
    ),
    array(
        'id'          => 'sticky_selector',
        'type'        => 'text',
        'label'       => __('Sticky selector', DOMAIN), // Селектор липкого контейнера
        'desc'        => __('Enter Jquery selector (for ex. .ExampleClass or #ExampleID)', DOMAIN), // Введите jQuery сселектор (.ExampleClass или #ExampleID)
        'placeholder' =>  '.navbar-default',
    ),
    array(
        'id'          => 'smooth_scroll',
        'type'        => 'number',
        'label'       => __('Smooth scroll', DOMAIN), // Плаваня прокрутка
        'desc'        => __('Smoothly scrolls over a specified number of pixels before the start of the object if the reference starts with # (Specify the height-to-object distance)[href=#obj]', DOMAIN), // Плавно пролистает за указанное количество пикселей до начала объекта, если ссылка начинается с # (Укажите высоту отступа до объекта)[href=#obj]
        'placeholder' => '40',
    ),
    array(
        'id'          => 'scroll_after_load',
        'type'        => 'number',
        'label'       => __('Scroll after page on load', DOMAIN), // Прокрутка после загрузки страницы
        'desc'        => __('Smoothly scrolls over a specified number of pixels before the start of the object, if the address ends with object ID [http://#obj]', DOMAIN), // Плавно пролистает за указанное количество пикселей до начала объекта, если адрес заканчивается на ID объекта
        'placeholder' => '40',
    ),
    array(
        'id'          => 'back_top',
        'type'        => 'text',
        'label'       => __('"On top" button content', DOMAIN), // Содержимое кнопки "Наверх"
        'desc'        => __('Set style for #back-top', DOMAIN), // 'Задайте кнопке #back-top собственный стиль'
        'placeholder' => '<i class="fa fa-angle-up" aria-hidden="true"></i>',
    ),
    array(
        'id'    => 'font_awesome',
        'type'  => 'checkbox',
        'label' => __('FontAwesome fonts', DOMAIN), // FontAwesome шрифт
        'desc'  => __('Enqueue <a target="_blank" href="http://fontawesome.io/get-started/">FontAwesome</a> fonts', DOMAIN), // Подключить шрифтовые иконки <a target="_blank" href="http://fontawesome.io/get-started/">FontAwesome</a>
    ),
    array(
        'id'    => 'animate',
        'type'  => 'checkbox', // тип
        'label' => __('Enqueue animate.css', DOMAIN), // Подключить анимацию.css
        'desc'  => __('Enqueue popular library <a target="_blank" href="https://daneden.github.io/animate.css/" >animate.css</a>', DOMAIN), // Подключает всем известный файл
    ),
    array(
        'id'    => 'wow',
        'type'  => 'select',
        'label' => __('Animate lib WOW.js', DOMAIN), // Файл анимации WOW.js
        'desc'  => sprintf('%s %s <i>
            data-wow-duration="2s"
            data-wow-delay="5s"
            data-wow-offset="10"
            data-wow-iteration="10"</i>',
                __('When you scroll down, the items change class for animate (for example using animate.css).<br>Objects to specify a class: <a target="_blank" href="http://mynameismatthieu.com/WOW/" >wow</a> together with his. ', DOMAIN), // При прокрутке вниз, элементы изменяют класс для анимации (к примеру с помощью файла animate.css).<br>Обьекту задать класс: <a target="_blank" href="http://mynameismatthieu.com/WOW/">wow</a> совместно со своим.
                __('advanced:', DOMAIN)
            ),
        'options' => array(
            ''          => __('Do not use', DOMAIN), // 'Не подключать'
            'forever'   => __('Use forever', DOMAIN), // Всегда подключать
            'not_phone' => __('Use for desktop only', DOMAIN), // Только на комьютерах
        ),
    ),
    array(
        'id'    => 'countTo',
        'type'  => 'text',
        'label' => __('countTo lib', DOMAIN), // Счетчик countTo
        'desc'  => sprintf('%s <i>data-from="(int)" data-to="(int)"</i> %s data-speed="(int)" data-refresh-interval="(int)"',
            __('Use <a target="_blank" href="https://github.com/mhuggins/jquery-countTo">count</a> attributes:', DOMAIN),
            __('Advanced:', DOMAIN) // Дополнительно:
        ),
        'placeholder' => '.timer',
    ),
    array(
        'id'    => 'appearJs',
        'type'  => 'checkbox',
        'label' => __('Enqueue appear', DOMAIN), // Подключить appear
        'desc'  => __('Enqueue <a target="_blank" href="http://creativelive.github.io/appear/">appear</a> lib'), // Подключить библиотеку <a target="_blank" href="http://creativelive.github.io/appear/">appear</a>
    ),
);

return apply_filters( 'dtools_settings', $args, 'scripts' );
