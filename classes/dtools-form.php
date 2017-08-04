<?php

function get_dtools_form($section){
  $args = array();

  $args['dp-general'] = array(
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
      'type'      => 'checkbox',
      'id'        => 'second-title',
      'label'     => 'Включить дополнительные заголовки',
      'desc'      => 'Изменяет заголовок на детальной странице записи, если указан второй заголовок. Укажите "false" если совсем не хотите выводить заголовок детальной страницы.',
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
  $args['dt-scripts'] = array(
    array(
      'id'        => 'sticky',
      'label'     => 'Использовать липкий контейнер',
      'type'      => 'select',
      'options'      => array(
        ''=>'Не использовать',
        'forever'=>'Использовать всегда',
        'phone_only'=>'Только для телефона',
        ),
      'desc'      => '<br> При прокрутке вниз, контейнер прилипает к верхней части экрана',
      ),
    array(
      'type'      => 'text',
      'id'        => 'sticky_selector',
      'label'     => 'Селектор липкого контейнера',
      'placeholder' =>  '.navbar-default',
      'desc'      => '<br> Введите jQuery сселектор (.ExampleClass или #ExampleID)',
      'default' =>  '.navbar-static-top',
      ),
    array(
      'type'      => 'number',
      'id'        => 'smooth_scroll',
      'label'     => 'Плаваня прокрутка',
      'desc'      => '<br> Плавно пролистает за указанное количество пикселей до начала объекта, если ссылка начинается с # (Укажите высоту отступа до объекта)[href=#obj]',
      'placeholder'   => '40',
      ),
    array(
      'type'      => 'number',
      'id'        => 'scroll_after_load',
      'label'     => 'Прокрутка после загрузки страницы',
      'desc'      => '<br> Плавно пролистает за указанное количество пикселей до начала объекта, если адрес заканчивается на ID объекта [http://#obj]',
      'placeholder'   => '40',
      ),
    array(
      'type'      => 'text',
      'id'        => 'back_top',
      'label'     => 'Содержимое кнопки "Наверх"',
      'desc'      => '<br>Задайте кнопке #back-top собственный стиль',
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
      'desc'      => '<br>Селектор счетчика, обьекту задать:<i> data-from="(int)" data-to="(int)"</i> дополнительно: data-speed="(int)" data-refresh-interval="(int)"',
      'placeholder'   => '.timer',
      ),
    array(
      'type'      => 'checkbox',
      'id'        => 'appearJs',
      'label'     => 'Подключить appear',
      'desc'      => '',
      ),
    );
  $args['dt-modal'] = array(array(
    'type'      => 'text',
    'id'        => 'fancybox',
    'label'     => 'Необычная коробка fancybox',
    'desc'      => '<br>Модальное окно (Галерея, всплывающее окно)',
    'placeholder'   => '.fancybox, .zoom',
    ),
  array(
    'type'      => 'checkbox',
    'id'        => 'fancybox_thumb',
    'label'     => 'Показывать превью',
    'desc'      => 'Показывать превью, если определена галерея атрибутом rel',
    ),
  array(
    'type'      => 'checkbox',
    'id'        => 'fancybox_mousewheel',
    'label'     => 'Прокрутка мышью',
    'desc'      => 'Прокручивать изображения в fancybox окне колесом мыши',
    ),
  );
  $args['dt-woo-settings'] = array(
    array(
      'type'      => 'select',
      'id'        => 'bestsellers',
      'label'     => 'Популярный товар',
      'options'      => array(
        ''=>'Не использовать',
        'personal'=>'Использовать вручную',
        'views'=>'Сортировать по просмотрам',
        'sales'=>'Сортировать по продажам'
        ),
      'desc'      => '<br> 1. Убедитесь что [query] запросы включены. <br> 2. Используйте <strong> [query type="top-sales"] </strong>',
      ),
    array(
      'type'      => 'checkbox',
      'id'        => 'wholesales',
      'label'     => 'Оптовые продажи',
      'desc'      => 'Разрешить продажу от.. шт.',
      ),
    array(
      'type'      => 'checkbox',
      'id'        => 'product-val',
      'label'     => 'Добавить товару ед. измерения',
      ),
    );

  return isset($args[$section]) ? $args[$section] : $args;
}
