<?php
class WPAdvancedPostType
{
  public static $post_types = array();

  private function get_defaults($params){
    $defaults = array(
      'public' => true,               // Публичный или используется только технически
      'publicly_queryable' => true,   // Показывать во front'е
      'show_ui' => true,              // Показывать управление типом записи
      'show_in_menu' => true,         // Показывать ли в админ-меню
      'query_var' => true,            // $post_type в query_var
      'rewrite' => true,              // ЧПУ
      'capability_type' => 'post',    // Права такие же как
      'has_archive' => true,          // Поддержка архивной страницы
      'hierarchical' => true,         // Родители / тексономии
      'menu_position' => null,
      'menu_icon'   => 'dashicons-admin-post',
      'supports' => array('title','editor','thumbnail','excerpt', 'custom-fields', 'page-attributes') //  add_post_type_support()
      );

    return array_merge($defaults, $params);
  }

  function deny_access_private_type(){
    if(is_admin())
      return;

    foreach (self::$post_types as $type => $args) {
      if(isset($args['public']) && $args['public'] == false){
        if( is_singular($type) || is_post_type_archive($type) ){
          global $wp_query;
          $wp_query->set_404();
          status_header(404);
          // u can hide this pages:
          // wp_die('Это техническая страница - к сожалению она не доступна');
        }
      }
    }
  }

  function registr_post_types(){
    foreach (self::$post_types as $type => $args) {
      register_post_type($type, $args);
    }
  }
  /**
   * Добавляет тип записи
   *
   * @param $type_slug  string type ID
   * @param $single     string
   * @param $multiple   string 
   * @param $params     array() register_post_type args split w/ defaults
   */
  public function add_type($type_slug=false, $single=false, $multiple=false, $params=array()){
    $type_slug = esc_attr($type_slug);

    if( empty($type_slug) || empty($single))
      return false;

    if(empty($multiple))
      $multiple = $single;

    $args = $this->get_defaults($params);
    $args['labels'] = array(
      'name' => __($multiple),
      'singular_name' => __($single),
      'add_new' => 'Добавить '.__($single),
      'add_new_item' => 'Добавить '.__($single),
      'edit_item' => 'Изменить '.__($single),
      'new_item' => 'Новая запись',
      'view_item' => 'Показать '.__($single),
      'search_items' => 'Найти '.__($single),
      'not_found' =>  'Записей не найдено',
      'not_found_in_trash' => 'В корзине нет записей',
      'parent_item_colon' => '',
      'menu_name' => __($multiple)
      );
    self::$post_types = array_merge(self::$post_types, array($type_slug => $args));
  }
  /**
   * Регистрирует добавленные типы
   */
  public function reg_types(){
    add_action('init', array($this, 'registr_post_types'));
    add_action( 'wp', array($this, 'deny_access_private_type'), 1 );
  }

  // ** example how to use:
  // $types = new WPAdvancedPostType();
  // $types -> add_type( 'enty', 'Entity', 'Entities', array('public'=>false) );
  // $types -> add_type( 'news', 'News');
  // $types -> reg_types();
}