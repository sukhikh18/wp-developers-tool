<?php
/*
Plugin Name: Дополнительные настройки разработчика
Plugin URI: https://github.com/nikolays93/wp-developers-tool
Description: Плагин добавляет дополнительные настройки в WordPress.
Version: 4.1.0 alpha
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if( defined('DT_PLUGIN_NAME') )
  return false;

if(!function_exists('is_wp_debug')){
  function is_wp_debug(){
    if( WP_DEBUG ){
      if( defined('WP_DEBUG_DISPLAY') && false === WP_DEBUG_DISPLAY ){
        return false;
      }
      return true;
    }
    return false;
  }
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

  function __construct(){
    $this->define_constants();
    $this->plugin_values = get_option( DT_PLUGIN_NAME );
    $this->include_classes();
    $this->include_addons();
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
      'AssetsEnqueuer'     => DT_DIR_CLASSES . '/assets_enqueuer'
      );

    if ( class_exists( 'WooCommerce' ) ) {
      $classes['WCProductSettings']  = DT_DIR_CLASSES . '/admin-wc-product-settings';
    }
    
    if( is_admin() ){
      $classes['DTForm']             = DT_DIR_CLASSES . '/dt-form-render';
      $classes['dt_AdminCallBacks']  = DT_DIR_CLASSES . '/admin-callback-page';
      $classes['dt_CustomMetaBoxes'] = DT_DIR_CLASSES . '/admin-meta-boxes';
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
    $scripts = DT_DIR_INCLUDES . 'init-scripts';
    $woo_inputs = DT_DIR_INCLUDES . 'woo-inputs';
    $includes = array(
      'maintenance-mode'    => DT_DIR_INCLUDES . 'maintenance-mode',
      'remove-images'       => DT_DIR_INCLUDES . 'admin-remove-images',
      'custom-query'        => DT_DIR_INCLUDES . 'custom-query',
      'second-title'        => DT_DIR_INCLUDES . 'second-title',
      'sc-code'             => DT_DIR_INCLUDES . 'sc-code',
      'remove-emojis'       => DT_DIR_INCLUDES . 'remove-emojis',

      'wholesales'          => $woo_inputs,
      'product-val'         => $woo_inputs,

      'smooth_scroll' => $scripts,
      'sticky'        => $scripts,
      'animate'       => $scripts,
      'font_awesome'  => $scripts,
      'fancybox'      => $scripts,
      'countTo'       => $scripts,
      'back_top'      => $scripts,
      );
    
    if(is_admin()){
      $includes['orign-image-resize'] = DT_DIR_INCLUDES . 'admin-orign-image-resize';
      $includes['bestsellers']        = $woo_inputs;
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
      'remove-emojis'=>'on'
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