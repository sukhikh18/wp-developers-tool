<?php
/*
Plugin Name: Дополнительные настройки разработчика
Plugin URI:
Description: Плагин добавляет новые возможности в WordPress.
Version: 3.3 beta
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
*/
/*  Copyright 2016  NikolayS93  (email: NikolayS93@ya.ru)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
if(!function_exists('is_wp_debug')){
  function is_wp_debug(){
    if( WP_DEBUG ){
      if( defined(WP_DEBUG_DISPLAY) && ! WP_DEBUG_DISPLAY){
        return false;
      }
      return true;
    }
    return false;
  }
}
function is_advanced_type($post_id=false){
  $advanced_post_types = array_keys(WPAdvancedPostType::$post_types);
  $post_types = array('post', 'page');
  $types = array_merge($advanced_post_types, $post_types);

  if($post_id){
    $post_type = get_post_type($post_id);
  }
  else {
    $screen = (function_exists('get_current_screen')) ? get_current_screen() : false;
    if($screen && isset($screen->post_type))
      $post_type = $screen->post_type;
  }

  if($post_type){
    if( in_array($post_type, $types) )
      return true;
  }
  
  return false;
}

register_activation_hook(__FILE__, function(){
  $dt = new DevelopersTools();
  $dt->activation_set_defaults();
});

/**
* 
*/
class DevelopersTools
{
  public $prefix = 'dt_';
  public $plugin_values = array();
  public $version = '3.0';

  protected $errors = array();

  function __construct(){
    $this->define_constants();
    $this->plugin_values = get_option( DT_PLUGIN_NAME );
    $this->include_classes();
    $this->include_addons();

    new AssetsEnqueuer();
  }

  private function define_constants() {
    define( 'DT_PLUGIN_NAME', 'DevelopersTools');
    define( 'DT_PLUGIN_PAGENAME', 'advanced-options');
    
    define( 'DT_BASE_URL',   trailingslashit( plugins_url( basename(__DIR__) ) ) );
    define( 'DT_ASSETS_URL', trailingslashit( DT_BASE_URL . 'assets' )  );

    define( 'DT_DIR_PATH', plugin_dir_path( __FILE__ ) );
    define( 'DT_DIR_CLASSES', trailingslashit( DT_DIR_PATH . 'classes' ) );
    define( 'DT_DIR_INCLUDES', trailingslashit( DT_DIR_PATH . 'includes') );
  }

  private function include_classes(){
    $classes = array(
      'WPAdvancedPostType' => DT_DIR_CLASSES . '/advanced-post-types',
      'scssc'              => DT_DIR_CLASSES . '/scss.inc',
      'AssetsEnqueuer'     => DT_DIR_CLASSES . '/assets_enqueuer'
      );

    if( is_admin() ){
      $classes['DTForm']             = DT_DIR_CLASSES . '/dt-form-render';
      $classes['dt_AdminCallBacks']  = DT_DIR_CLASSES . '/admin-callback-page';
      $classes['dt_CustomMetaBoxes'] = DT_DIR_CLASSES . '/admin-meta-boxes';
      $classes['WCProductSettings']  = DT_DIR_CLASSES . '/admin-wc-product-settings';

      // $classes = array_merge($public, $admin);
    }

    // Подключить вышеуказанные классы
    foreach ( $classes as $id => $path ) {
      $path .= '.php';
      if ( is_readable( $path ) ) {
        if(! class_exists( $id ))
          require_once( $path );
      }
    }
  }

  private function include_addons(){
    $includes = array(
      'maintenance-mode'    => DT_DIR_INCLUDES . '/maintenance-mode',
      'custom-query'        => DT_DIR_INCLUDES . '/custom-query',
      'reviews'             => DT_DIR_INCLUDES . '/reviews',
      'second-title'        => DT_DIR_INCLUDES . '/second-title',
      'sc-code'             => DT_DIR_INCLUDES . '/sc-code',
      'remove_emojis'       => DT_DIR_INCLUDES . '/remove_emojis',
      );
    if(is_admin()){
      $includes['orign-image-resize'] = DT_DIR_INCLUDES . '/admin-orign-image-resize';
      $includes['bestsellers'] = DT_DIR_INCLUDES . '/bestsellers';
    }
    
    // Подключить вышеперечисленные addon'ы которые задействованны в настройках
    $values = apply_filters( $this->prefix . 'enabled_values', $this->plugin_values );
    foreach ( $includes as $id => $path) {
      $path .= '.php';
      if ( is_readable( $path ) ) {
        if(!empty($values[$id]))
          require_once( $path );
      }
    }
  }
  
  private function set_defaults(){
    $defaults = array(
      'orign-image-resize'=>'default',
      'use_scss'=>'on'
      );

    update_option( DT_PLUGIN_NAME, $defaults );
  }

  public function activation_set_defaults(){
  	// Если база пустая
    if($this->plugin_values !== false && sizeof($this->plugin_values) >= 1)
      return false;

    $this->set_defaults();
  }
}
new DevelopersTools();