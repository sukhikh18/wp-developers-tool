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
          ''        => 'Не сжимать',
          'default' => 'Сжимать стандартно (1600х1024)',
          'large'   => 'Сжимать до "Крупного" размера',
          ),
        ),
      array(
        'id'        => 'remove-images',
        'title'     => 'Удалять прикрепленные изображения в след за записью',
        'type'      => 'select',
        'vals'      => array(
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
    		'title'     => 'Включить дополнительные заголовки',
        'desc'      => 'Изменяет заголовок на детальной странице записи, если указан второй заголовок. Укажите "false" если совсем не хотите выводить заголовок детальной страницы.',
    		),
      array(
        'type'      => 'textarea',
        'id'        => 'maintenance-mode',
        'title'     => 'Включить техническое обслуживание',
        'placeholder'   => "Сайт находится на техническом обслуживании.\nПожалуйста, зайдите позже.",
        'desc'      => 'Техническое обслуживание времено закроет доступ к сайту (кроме пользователей с привелегией "edit_themes") с указанным сообщением.',
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
        'type'      => 'checkbox',
        'id'        => 'custom-query',
        'title'     => 'Использовать [query] запросы',
        'desc'      => '
        <input title="Количество записей (max)[4]" type="number" placeholder="4" style="width: 50px">
        <select title="Тип записей (type)[post]" id="post_type"><option value="post">post</option><option value="post">page</option></select>
        <input title="ID групп записи (cat)" type="text" placeholder="e.g. 4,8,12" style="width: 80px;">
        <input title="Slug группы записей (slug)" type="text" placeholder="e.g. best_articles">
        <input title="Страницы родителей (parent)" type="text" placeholder="e.g. 1,2,5" style="width: 80px;">
        <select id="post_status" title="Статус записи (status)[publish]">
          <option value="publish">publish</option>
          <option value="pending">pending</option>
          <option value="draft">draft</option>
          <option value="auto-draft">auto-draft</option>
          <option value="future">future</option>
          <option value="private">private</option>
          <option value="inherit">inherit</option>
          <option value="trash">trash</option>
          <option value="any">any</option>
        </select>
        <select title="Сортировка (order)[DESC]" name="" id=""><option value="DESC">DESC</option><option value="ASC">ASC</option></select>
        <input title="Класс контейнера (container)[container-fluid]" type="text" placeholder="row"><br>
        <input type="text" value="[query max=\'4\' type=\'post\' status=\'publish\' order=\'DESC\' container=\'container-fluid\' columns=\'4\' template=\'#post_type\']" class="widefat readonly" readonly="true">

        <!-- Стандарты: [query max="4" type="post" status="publish" order="DESC" container="container-fluid" columns="4" template="#post_type"]<br> -->

          columns="4", | 1 | 2 | 3 | 4 | 10 | 12 <br>
          template=string определенный шаблон ищется так (если шаблон не найден, будет использоваться последующий):<br><pre>
          #theme/template-parts/content-#template-query.php
          #theme/template-parts/content-#template.php
          #theme/template-parts/content-query.php
          #theme/template-parts/content.php</pre>',
        ),
      );

    $this->register_section('dp-queries', 'Запросы', '', $args);

    $args = array(
      array(
        'id'        => 'sticky',
        'title'     => 'Использовать липкий контейнер',
        'type'      => 'select',
        'vals'      => array(
          ''=>'Не использовать',
          'forever'=>'Использовать всегда',
          'phone_only'=>'Только для телефона',
          ),
        'desc'      => '<br> При прокрутке вниз, контейнер прилипает к верхней части экрана',
        ),
      array(
        'type'      => 'text',
        'id'        => 'sticky_selector',
        'title'     => 'Селектор липкого контейнера',
        'placeholder' =>  '.navbar-default',
        'desc'      => '<br> Введите jQuery сселектор (.ExampleClass или #ExampleID)',
        'default' =>  '.navbar-static-top',
        ),
    	array(
    		'type'      => 'number',
    		'id'        => 'smooth_scroll',
    		'title'     => 'Плаваня прокрутка',
    		'desc'      => '<br> Плавно пролистает за указанное количество пикселей до начала объекта, если ссылка начинается с # (Укажите высоту отступа до объекта)[href=#obj]',
        'placeholder'   => '40',
    		),
      array(
        'type'      => 'number',
        'id'        => 'scroll_after_load',
        'title'     => 'Прокрутка после загрузки страницы',
        'desc'      => '<br> Плавно пролистает за указанное количество пикселей до начала объекта, если адрес заканчивается на ID объекта [http://#obj]',
          'placeholder'   => '40',
          ),
      array(
        'type'      => 'text',
        'id'        => 'back_top',
        'title'     => 'Содержимое кнопки "Наверх"',
        'desc'      => '<br>Задайте кнопке #back-top собственный стиль',
        'placeholder'   => '<i class=\'fa fa-angle-up\' aria-hidden=\'true\'></i>',
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
        'desc'      => '<br>Селектор счетчика, обьекту задать:<i> data-from="(int)" data-to="(int)"</i> дополнительно: data-speed="(int)" data-refresh-interval="(int)"',
        'placeholder'   => '.timer',
        ),
      array(
        'type'      => 'checkbox',
        'id'        => 'appearJs',
        'title'     => 'Подключить appear',
        'desc'      => '',
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
        ),
        array(
          'type'      => 'checkbox',
          'id'        => 'wholesales',
          'title'     => 'Оптовые продажи',
          'desc'      => 'Разрешить продажу от.. шт.',
        ),
        array(
          'type'      => 'checkbox',
          'id'        => 'product-val',
          'title'     => 'Добавить товару ед. измерения',
        ),        
      );
      $this->register_section('dt-woo-settings', 'WooCommerce', '', $args);
    }

    // if(is_wp_debug()){
    //   $args = array(
    //   array(
    //     'type'      => 'checkbox', // тип
    //     'id'        => 'bs_shortcodes',
    //     'title'     => 'Использовать bootstrap [shortcode]\'ы',
    //     //'desc'      => '',
    //     ),
    //   );
    //   $this->register_section('develop', 'В разработке', '', $args);
    // }
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


