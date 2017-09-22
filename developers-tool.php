<?php
/*
Plugin Name: Дополнительные настройки разработчика
Plugin URI: https://github.com/nikolays93/wp-developers-tool
Description: Плагин добавляет дополнительные настройки в WordPress.
Version: 5.2 beta
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
namespace DTools;

/**
  * @todo  revrite enabled_values filter
  */

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if(!defined('DTOOLS_DEBUG'))
  define( 'DTOOLS_DEBUG', true );
// DevelopersTools::write_debug($msg, __FILE__);

// DevelopersTools::$settings;
class DevelopersTools {
  const SETTINGS = 'DTools';
  const PREFIX = 'dt_';

  public static $settings = array();

  /* Singleton Class */
  private function __clone() {}
  private function __wakeup() {}

  private static $instance = null;
  public static function get_instance() {
    if ( ! isset( self::$instance ) )
      self::$instance = new self;

    return self::$instance;
  }

  public static function activate(){
    add_option( self::SETTINGS, array(
      'orign-image-resize'=>'default',
      'remove-emojis'=>'on'
      ) );
  }

  public static function uninstall(){ delete_option(self::SETTINGS); }

  private function __construct() {
    self::define_constants();
    self::include_classes();
    self::$settings = get_option( self::SETTINGS, array() );
    add_filter( self::PREFIX . 'enabled_values', array(__CLASS__, 'active_addons_filter'), 10, 1 );
    self::include_addons();
    self::add_admin_page();
  }

  public static function write_debug($msg, $dir){
    if(!defined('DTOOLS_DEBUG') || !DTOOLS_DEBUG)
      return;

    $dir = str_replace(DT_DIR_PATH, '', $dir);
    $msg = str_replace(DT_DIR_PATH, '', $msg);

    $date = new \DateTime();
    $date_str = $date->format(\DateTime::RSS);

    $handle = fopen(DT_DIR_PATH . "/debug.log", "a+");
    fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
    fclose($handle);
  }

  public static function load_file_if_exists($file_array){
    foreach ( $file_array as $id => $path ) {
      if( class_exists( $id ) ){
        DevelopersTools::write_debug('Класс ' . $path . ' ('.$id.') уже был подключен', __FILE__);
        continue;
      }

      if ( ! is_readable( $path ) ){
        DevelopersTools::write_debug('Файл ' . $path . ' не может быть подключен', __FILE__);
        continue;
      }

      require_once( $path );
    }
  }

  private static function define_constants(){
    define( 'DT_DIR_PATH', rtrim( plugin_dir_path( __FILE__ ), '/') );
    define( 'DT_DIR_CLASSES', DT_DIR_PATH . '/classes' );
    define( 'DT_DIR_INCLUDES', DT_DIR_PATH . '/includes' );

    define( 'DT_BASE_URL', plugins_url( basename(__DIR__) ) );
    define( 'DT_ASSETS_URL', DT_BASE_URL . '/assets' );
  }

  private static function include_classes(){
    $classes = array();

    if( is_admin() ){
      $classes['DTools\WPForm'] = DT_DIR_CLASSES . '/class-wp-form-render.php';
      $classes['DTools\WPAdminPageRender']  = DT_DIR_CLASSES . '/class-wp-admin-page-render.php';
      $classes['DToolsForm'] = DT_DIR_CLASSES . '/dtools-form.php';
    }

    if ( class_exists( 'WooCommerce' ) ) {
      $classes['WCProductSettings'] = DT_DIR_CLASSES . '/class-wc-product-settings.php';
    }

    self::load_file_if_exists( $classes );
  }

  static function active_addons_filter( $non_filtred ){
    $active = array();
    foreach ($non_filtred as $key => $value) {
      if(isset(self::$settings[$key]))
        $active[$key] = $value;
    }

    return $active;
  }

  private static function include_addons(){
    $scripts = DT_DIR_INCLUDES . '/init-scripts.php';
    $woo_inputs = DT_DIR_INCLUDES . '/woo-inputs.php';
    $includes = array(
      'maintenance-mode'   => DT_DIR_INCLUDES . '/maintenance-mode.php',
      'remove-images'      => DT_DIR_INCLUDES . '/admin-remove-images.php',
      'second-title'       => DT_DIR_INCLUDES . '/second-title.php',
      'remove-emojis'      => DT_DIR_INCLUDES . '/remove-emojis.php',
      'orign-image-resize' => DT_DIR_INCLUDES . '/admin-orign-image-resize.php',

      'bestsellers'        => DT_DIR_INCLUDES . '/woocommerce-bestsellers.php',
      'wholesales'         => $woo_inputs,
      'product-val'        => $woo_inputs,

      'smooth_scroll'      => $scripts,
      'sticky'             => $scripts,
      'animate'            => $scripts,
      'font_awesome'       => $scripts,
      'modal_type'         => $scripts,
      'countTo'            => $scripts,
      'back_top'           => $scripts,
      );

    // Подключить только активные
    $includes = apply_filters( self::PREFIX . 'enabled_values', $includes, self::$settings );

    $includes = self::load_file_if_exists( $includes );
  }

  /********************************* ADMIN SETTINGS PAGE ********************************/
  static private function add_admin_page(){
    if( ! is_admin() )
      return;
    // for side metaboxes
    // add_filter( self::SETTINGS . '_columns', function(){return 2;} );
    $sections = array(
      self::PREFIX . 'general'      => __('Главная'),
      self::PREFIX . 'scripts'      => __('Скрипты'),
      self::PREFIX . 'modal'        => __('Модальное окно'),
      );
    $callbacks = array(
      self::PREFIX . 'general'      => array(__CLASS__, 'admin_settings_page_tab1'),
      self::PREFIX . 'scripts'      => array(__CLASS__, 'admin_settings_page_tab2'),
      self::PREFIX . 'modal'        => array(__CLASS__, 'admin_settings_page_tab4'),
      );
    if( class_exists('woocommerce') ){
      $sections[self::PREFIX . 'woo-settings'] = __('WooCommerce');
      $callbacks[self::PREFIX . 'woo-settings'] = array(__CLASS__, 'admin_settings_page_tab3');
    }

    $page = new WPAdminPageRender(
      self::SETTINGS,
      array(
        'parent' => 'options-general.php',
        'title' => __('Дополнительные настройки'),
        'menu' => __('Ещё'),
        'tab_sections' => $sections,
        ),
      $callbacks,
      self::SETTINGS,
      array(__CLASS__, 'validate_options')
      );

    add_action($page->page . '_after_form_inputs', 'submit_button' );
    add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_assets'));
    add_action('wp_ajax_change_modal_type', array(__CLASS__, 'admin_settings_page_tab4') );
    // $page->add_metabox( 'metabox1', 'first metabox', array($this, 'metabox_cb'), $position = 'side');
    // $page->add_metabox( 'metabox2', 'second metabox', array($this, 'metabox_cb'), $position = 'side');
    // $page->set_metaboxes();
  }

  static function admin_enqueue_assets(){
    $screen = get_current_screen();
    if( !isset($screen->id) || $screen->id !== 'settings_page_DTools' )
      return;

    wp_enqueue_script(self::PREFIX . 'admin_js', DT_ASSETS_URL . '/admin.js', array('jquery'), false, true);
    wp_localize_script( self::PREFIX . 'admin_js', self::PREFIX . 'admin_js', array('nonce' => wp_create_nonce('modal') ) );
  }

  static function admin_settings_page_tab1(){
    $form = get_dtools_form('dp-general');

    $active = WPForm::active(self::SETTINGS, false, true);
    WPForm::render( $form, $active, true, array('admin_page' => self::SETTINGS) );
  }

  static function admin_settings_page_tab2(){
    $form = get_dtools_form('dt-scripts');

    $active = WPForm::active(self::SETTINGS, false, true);
    WPForm::render( $form, $active, true, array('admin_page' => self::SETTINGS) );
  }

  static function admin_settings_page_tab3(){
    $form = get_dtools_form('dt-woo-settings');

    $active = WPForm::active(self::SETTINGS, false, true);
    WPForm::render( $form, $active, true, array('admin_page' => self::SETTINGS) );
  }

  static function admin_settings_page_tab4(){
    if( defined('DOING_AJAX') && DOING_AJAX ){
      if( ! wp_verify_nonce( $_POST['nonce'], 'modal' ) )
        wp_die('Ошибка! нарушены правила безопасности');

      $modal_type = (isset($_POST['modal_type']) && $_POST['modal_type']) ? $_POST['modal_type']: 'dismodal';
    }
    else {
      $modal_type = (isset(self::$settings['modal_type']) && self::$settings['modal_type']) ?
        self::$settings['modal_type'] : 'dismodal';
    }

    $form = get_dtools_form($modal_type);

    $active = WPForm::active(self::SETTINGS, false, true);
    WPForm::render( $form, $active, true, array('admin_page' => self::SETTINGS) );
    if( defined('DOING_AJAX') && DOING_AJAX )
      wp_die();
  }

  // function metabox_cb(){
  //   echo "METABOX";
  // }

  static function validate_options( $inputs ){
    // $inputs = array_map_recursive( 'sanitize_text_field', $inputs );
    $inputs = array_filter_recursive($inputs);

    return $inputs;
  }
}

add_action( 'plugins_loaded', array('DTools\DevelopersTools', 'get_instance') );
register_activation_hook( __FILE__, array( 'DTools\DevelopersTools', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'DevelopersTools', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'DTools\DevelopersTools', 'uninstall' ) );