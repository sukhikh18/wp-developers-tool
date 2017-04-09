<?php

/**
*
*/
class AdminCallBacks // extends DevelopersTools
{
  public $sections = array();
  public $plugin_values;

  function __construct(){
    $this->plugin_values = get_option( DT_PLUGIN_NAME );

    add_action( 'admin_init', array($this, 'register_options') );
    add_action( 'admin_menu', array($this, 'add_pages_in_submenu'), 99);
  }

  /**
   * Добавляет меню (хук в конструкторе)
   */
  public function add_pages_in_submenu(){
    // add_submenu_page('options-general.php', 'Помощь', 'Инструкции', 'manage_options',
    //   $this->plugin_page_name, array($this, 'the_page_template'));
    add_submenu_page('options-general.php', 'Дополнительные настройки', 'Ещё', 'manage_options',
      DT_PLUGIN_PAGENAME, array($this, 'the_page_template'));
  }

  /**
   * Регистрирует секции и ячейки
   * для hidden_area регистрирует так же &_check чекбокс
   */
  function register_section($section, $title, $sub_title, $args){
    $this->sections[$section] = $title;
    add_settings_section( $section, $sub_title, '', $section );
    foreach ($args as $arg) {
      add_settings_field( $arg['id'], $arg['title'],
        array($this, 'settings_templates'), $section, $section, $arg );
    }
  }

  // Все параметры
  function register_options(){
    register_setting( DT_PLUGIN_NAME, DT_PLUGIN_NAME, array($this, 'validate_settings') );

    $args = array(
      array(
        'id'        => 'orign-image-resize',
        'title'     => 'Сжимать оригиналы изображений',
        'type'      => 'select',
        'vals'      => array(
          ''           => 'Не сжимать',
          'default'    => 'Сжимать стандартно (1600х1024)',
          'large' => 'Сжимать до "Крупного" размера',
          ),
        ),
      array(
        'type'      => 'checkbox',
        'id'        => 'sc-code',
        'title'     => 'Включить возможность использовать [CODE]',
        'desc'      => 'Добавит метабокс с возможностью разрешить [CODE] для записи',
        ),
    	array(
    		'type'      => 'checkbox',
    		'id'        => 'second-title',
    		'title'     => 'Включить дополнительные заголовки',
        'desc'      => 'Используйте дополнительные заголовки в своей теме при помощи: <br><strong> get_second_title($id, $before, $after) </strong> или <strong> the_second_title($id, $before, $after) </strong>',
    		),
    	array(
    		'type'      => 'checkbox',
    		'id'        => 'reviews',
    		'title'     => 'Включить отзывы',
    		'desc'      => 'Читайте информацию на странице отзывов в разделе помощь (В вехнем правом углу)',
    		),
      // array(
      //   'type'      => 'checkbox',
      //   'id'        => 'dp_post_types',
      //   'title'     => 'Дополнительные типы записей',
      //   'desc'      => 'В своей теме, для добавления типов, я обычно использую <strong>include/functions-custom.php</strong><br>
      //   К примеру: <pre>
      //   if(class_exists("WPAdvancedPostType")){
      //     $types = new WPAdvancedPostType();
      //     $types -> add_type( "enty", "Entity", "Entities", array("public"=>false) );
      //     $types -> add_type( "news", "News");
      //     $types -> reg_types();
      //   }
      //   $args не обязателен, но могут быть такие же как у registr_post_type: array(
      //     "supports" => array("title","editor","thumbnail","custom-fields")
      //   );
      //   </pre>'
      //   ),
      array(
        'type'      => 'checkbox',
        'id'        => 'custom-query',
        'title'     => 'Использовать [query] запросы',
        'desc'      => 'Стандарты: [query max="4" type="post" status="publish" order="DESC" container="container-fluid" columns="4" template="#post_type"]<br>
          max=int, - количество постов <br>
          type=post_type, - post, page, product... <br>
          cat=int, - ID категоирии (для записей) <br>
          slug=string, - slug категории (для записей) <br>
          parent=1,2,3 - записи родителей 1,2,3 (для иерархий), <br>
          status= "publish" | "future" | "alltime" ("Опубликованные", "Запланированные", "Оба варианта"), <br>
          order="DESC", | "ASC", <br>
          container=string | false - подложка, <br>
          [Шаблонные]<br>
          columns="4", | 1 | 2 | 3 | 4 | 10 | 12 <br>
          template=string определенный шаблон ищется так (если шаблон не найден, будет использоваться последующий):<br><pre>
          #theme/template-parts/content-#template-query.php
          #theme/template-parts/content-#template.php
          #theme/template-parts/content-query.php
          #theme/template-parts/content.php</pre>',
        ),
      array(
        'type'      => 'textarea',
        'id'        => 'maintenance-mode',
        'title'     => 'Включить техническое обслуживание',
        'placeholder'   => "Сайт находится на техническом обслуживании.\nПожалуйста, зайдите позже.",
        'desc'      => 'Техническое обслуживание времено закроет доступ к сайту с указанным сообщением',
        ),
      array(
        'type'      => 'checkbox',
        'id'        => 'remove-emojis',
        'title'     => 'Отключить wp_emoji',
        'desc'      => '',
        ),
    	);
    $this->register_section('dp-general', 'Главная', '', $args);

    $args = array(
      array(
        'id'        => 'use-scss',
        'title'     => 'Использовать SCSS',
        'type'      => 'checkbox',
        'desc'      => 'При включении, отключите подключение get_stylesheet_uri() в файле functions.php',
        // 'require' => 'sticky'
        ),
      array(
        'id'        => 'sticky',
        'title'     => 'Использовать липкий контейнер',
        'type'      => 'select',
        'vals'      => array(
          ''=>'Не использовать',
          'forever'=>'Использовать всегда',
          'phone_only'=>'Только для телефона',
          ),
        // 'desc'      => 'При прокрутке вниз, контейнер прилипает к верхней части экрана',
        // 'require' => 'sticky'
        ),
      array(
        'type'      => 'text',
        'id'        => 'sticky_selector',
        'title'     => 'Селектор липкого контейнера',
        'placeholder' =>  '.navbar-default',
        //'desc'      => 'Введите jQuery сселектор (.ExampleClass или #ExampleID)',
        'default' =>  '.navbar-static-top',
        //'require' => 'sticky'
        ),
      // Прокрутка после загрузки страницы по параметру scroll
      // К пр.: http://mydomain.ru/?scroll=primary
      // Пролистает за $smooth_scroll пикселя до начала объекта #primary
      // Внимание! параметр scroll указывается без "#" и прокручивает только до объекта с ID.
    	array(
    		'type'      => 'number',
    		'id'        => 'smooth_scroll',
    		'title'     => 'Плаваня прокрутка',
    		'desc'      => '<br>Плавная прокрутка до якоря, если ссылка начинается с #. (Укажите высоту отступа до объекта) <br> Для отключения удалите значение.',
        'placeholder'   => '40',
    		),
      array(
        'type'      => 'checkbox',
        'id'        => 'back_top',
        'title'     => 'Кнопка "Наверх"',
        'desc'      => 'Задайте кнопке #back-top собственный стиль',
        ),
    	array(
    		'type'      => 'checkbox', 
    		'id'        => 'font_awesome',
    		'title'     => 'FontAwesome шрифт',
    		//'desc'      => 'Подключить шрифтовые иконки <a href="http://fontawesome.io/get-started/">FontAwesome</a>',
    		),
    	array(
		    'type'      => 'checkbox', // тип
		    'id'        => 'animate',
		    'title'     => 'Подключить анимацию.css',
		    'desc'      => 'Подключает всем известный файл animate.css',
		    ),
      array(
        'type'      => 'text',
        'id'        => 'fancybox',
        'title'     => 'Необычная коробка fancybox',
        'desc'      => '<br>Модальное окно (Галерея, всплывающее окно)',
        'placeholder'   => '.fancybox, .zoom',
        ),
      array(
        'type'      => 'checkbox',
        'id'        => 'fancybox_thumb',
        'title'     => 'Показывать превью',
        'desc'      => 'Показывать превью, если определена галерея атрибутом rel',
        ),
      array(
        'type'      => 'checkbox',
        'id'        => 'fancybox_mousewheel',
        'title'     => 'Прокрутка мышью',
        'desc'      => 'Прокручивать изображения в fancybox окне колесом мыши',
        ),
      array(
        'type'      => 'text',
        'id'        => 'countTo',
        'title'     => 'Счетчик countTo',
        'desc'      => '<br>Селектор счетчика, обьекту задать:<i> data-from="(int)" data-to="(int)"</i>',
        'placeholder'   => '.timer',
        ),
    	);
    $this->register_section('scripts', 'Скрипты', '', $args);

    if ( class_exists( 'WooCommerce' ) ) {
      $args = array(
        array(
          'type'      => 'select',
          'id'        => 'bestsellers',
          'title'     => 'Популярный товар',
          'vals'      => array(
            ''=>'Не использовать',
            'personal'=>'Использовать вручную',
            'views'=>'Сортировать по просмотрам',
            'sales'=>'Сортировать по продажам'
            ),
          'desc'      => '<br> 1. Убедитесь что [query] запросы включены. <br> 2. Используйте <strong> [query type="top-sales"] </strong>',
        )
      );
      $this->register_section('dt-woo-settings', 'WooCommerce', '', $args);
    }

    if(is_wp_debug()){
      $args = array(
      array(
        'type'      => 'checkbox', // тип
        'id'        => 'bs_shortcodes',
        'title'     => 'Использовать bootstrap [shortcode]\'ы',
        //'desc'      => '',
        ),
      );
      //if ( class_exists( 'WooCommerce' ) ) {
      $args[] = array(
        'type'      => 'checkbox', // тип
        'id'        => 'woo_add_value',
        'title'     => 'Добавить товару ед. измерения',
        );
      //}
      $this->register_section('develop', 'В разработке', '', $args);
    }
  }

  // Шаблоны вывода параметров
  function settings_templates($args){
    extract( $args );

    
    $option_name = DT_PLUGIN_NAME;
    $opt = $this->plugin_values;

    // Значение опции
    $value = (!empty($opt[$id])) ? esc_attr( stripslashes($opt[$id]) ) : '';
    $placeholder = $ph = (!empty($placeholder)) ? ' placeholder="'.$placeholder.'"' : false;

    $checked = false;
    if(!empty($value) || !empty($default)){
        $checked = ' checked';
    }
    
    echo "<label for='$id'>";
    switch ($type) {
      case 'checkbox':

        echo "<input value='on' type='{$type}' id='{$id}' name='{$option_name}[{$id}]'{$checked} />";  
        break;

      case 'text':
      case 'number':
        echo "<input type='{$type}'{$ph} id='{$id}' name='{$option_name}[{$id}]' value='{$value}'>";
        break;

      case 'textarea':
        echo "<textarea cols='60'{$ph} id='{$id}' name='{$option_name}[{$id}]' title='Для отмены оставьте область пустой'>{$value}</textarea><br>";
        break;

      case 'select':
        echo "<select type='{$type}' id='{$id}' name='{$option_name}[{$id}]'>";
        foreach($vals as $val=>$text){
          $selected = (!empty($opt[$id]) && $opt[$id] == $val) ? "selected=' selected'" : ' ';  
          echo "<option value='{$val}'{$selected}>{$text}</option>";
        }
        echo "</select>";
        break;
    }
    if(!empty($desc))
      echo "<span class='description'>$desc</span>";
    echo "</label>";
  }

  // Функция обработки вводимых полей
  function validate_settings($input) {
    //$valid_input= (!empty(parent::$ao_value)) ? parent::$ao_value : array();

    foreach($input as $key => $val) {
      //if(!empty( $val ) && $val != 'off'){
      if($val != '')
        $valid_input[$key] = $val;
      //}
      //elseif($val == 'off' || $val == ''){
      //unset($valid_input[$key]);
      //}
    }
    if(is_wp_debug())
      file_put_contents(__DIR__.'/debug.log', print_r($input,true));

    return $valid_input;
  }

  /**
   * Выводит табы (если $echo_tabs == true) и возвращает активную страницу
   */
  public function get_current_section( $echo_tabs = true ) {
  	if (!empty($_GET['tab'])){
  		$current = $_GET['tab'];
  	}
  	else {
  		$sections_id = array_keys($this->sections);
      reset($sections_id);
  		$current = current($sections_id);
  	}

  	if($echo_tabs){
  		echo '<h2 class="nav-tab-wrapper">';
  		foreach( $this->sections as $tab => $name ){
  			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
  			echo "<a class='nav-tab{$class}' href='?page=".DT_PLUGIN_PAGENAME."&tab={$tab}' data-tab='{$tab}'>$name</a>";
  		}
  		echo '</h2>';
  	}

  	return $current;
  }

  /**
   * Вывод страницы
   */
  function the_page_template(){
    echo '
    <style type="text/css">
      td>input[type="number"] { width: 64px; }
    </style>
    ';
  	echo '<h1>'.get_admin_page_title().'</h1>';
    $current = $this->get_current_section();
    echo '
    <div class="wrap">
    	<form method="post" enctype="multipart/form-data" action="options.php">';
        // do_action('before_dp_plugin_settings');
        echo "<div id='tab-content'>";
        foreach ($this->sections as $section => $label) {
          $class = ($section == $current) ? '' : ' class="hidden"';
          echo "<div id='{$section}'{$class}>";
          do_settings_sections($section);
          echo "</div>";
        }
        echo '</div>';
        // do_action('after_dp_plugin_settings');
        
        settings_fields( DT_PLUGIN_NAME );
        submit_button( 'Сохранить настройки', 'primary');//, 'update' );
    
    echo '
    	</form>
    </div>';
    ?>
    <script type="text/javascript">
      dpjq = jQuery.noConflict();
      dpjq(function( $ ) {
        // on.load
        $(function(){
          $('input[type=\'text\'], input[type=\'number\'], textarea').on('focus', function(){
            if($(this).val() == ''){
              $(this).val($(this).attr('placeholder'));
              $(this).select();
            }
          });
          // $('.wrap > form').on('submit', function(){
          //   $('input[type=\"checkbox\"]', this).each(function(i){
          //     if( !$(this).is(':checked') ){
          //       var input = $('<input>')
          //       .attr('type', 'hidden')
          //       .attr('name', $(this).attr('name')).val('off');

          //       $(this).closest('form').append($(input));
          //     }
          //   });
          // });
          $('a.nav-tab').on('click', function(e){
            e.preventDefault();
            if($(this).hasClass('nav-tab-active'))
              return false;

            var loc = window.location.href.split('&tab')[0] + '&tab=' + $(this).attr('data-tab');
            history.replaceState(null, null, loc);
            $('input[name="_wp_http_referer"]').val(loc + '&settings-updated=true');
            
            $(window).bind('hashchange', function() {
              alert(window.location.href);
           });

            $('#tab-content #' + $('.nav-tab-active').attr('data-tab')).addClass('hidden');
            $('.nav-tab-active').removeClass('nav-tab-active');

            $('#tab-content #' + $(this).attr('data-tab') ).removeClass('hidden');
            $(this).addClass('nav-tab-active');
          });
        });
      });
    </script>
    <?php
  }
}

new AdminCallBacks();


