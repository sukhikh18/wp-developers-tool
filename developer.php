<?php
/*
Plugin Name: Дополнительные настройки разработчика
Plugin URI:
Description: Плагин добавляет новые возможности в WordPress.
Version: 3.2 beta
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
    $this->add_requires();
    $this->add_assets();
  }

  function show_admin_notice(){
    if(sizeof($this->errors) == 0)
      return;

    foreach ($this->errors as $error) {
      $type = (isset($error['type'])) ? $error['type'] . ' ' : ' ';
      $msg = (isset($error['msg'])) ? apply_filters('the_content', $error['msg']) : false;
      if($msg)
        echo '
        <div id="message" class="'.$type.'notice is-dismissible">
          '.$msg.'
        </div>';
      else
        echo '
        <div id="message" class="'.$type.'notice is-dismissible">
          <p>Обнаружена неизвестная ошибка!</p>
        </div>';
    }
  }
  protected function set_notice($msg=false, $type='error'){
    $this->errors[] = array('type' => $type, 'msg' => $msg);

    add_action( 'admin_notices', array($this, 'show_admin_notice') );
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

  private function classes(){
    $public = $admin = array();

    if(is_admin()){
      $admin = array(
        'dt_AdminCallBacks'  => DT_DIR_CLASSES . '/admin-callback-page',
        'dt_CustomMetaBoxes' => DT_DIR_CLASSES . '/admin-meta-boxes',
        'WCProductSettings'  => DT_DIR_CLASSES . '/admin-wc-product-settings'
        );
    }
    $public = array(
      'WPAdvancedPostType' => DT_DIR_CLASSES . '/advanced-post-types'
      );
    $classes = array_merge($public, $admin);
    return $classes;
  }
  private function includes(){
    $public = $admin = array();
    if(is_admin()){
      $admin = array(
        'orign-image-resize'  => DT_DIR_INCLUDES . '/admin-orign-image-resize',
        'bestsellers' => DT_DIR_INCLUDES . '/bestsellers'
        );
    }
    $public = array(
      'maintenance-mode'    => DT_DIR_INCLUDES . '/maintenance-mode',
      'custom-query'        => DT_DIR_INCLUDES . '/custom-query',
      'reviews'             => DT_DIR_INCLUDES . '/reviews',
      'second-title'        => DT_DIR_INCLUDES . '/second-title',
      'sc-code'             => DT_DIR_INCLUDES . '/sc-code',
      );

    $includes = array_merge($public, $admin);
    return $includes;
  }
  private function add_requires(){
    foreach ( $this->classes() as $id => $path ) {
      $path .= '.php';
      if ( is_readable( $path ) ) {
        if(! class_exists( $id ))
          require_once( $path );
      }
      else {
          $this->set_notice('Обнаружен поврежденный класс - <strong>'.$id.'</strong>', 'error');
      }
    }
    $values = apply_filters( $this->prefix . 'enabled_values', $this->plugin_values );
    foreach ($this->includes() as $id => $path) {
      $path .= '.php';
      if ( is_readable( $path ) ) {
        if(!empty($values[$id]))
          require_once( $path );
      }
      else {
          $id = basename($path);
          $this->set_notice('Обнаружен поврежденный файл - <strong>'.$id.'</strong>', 'error');
      }
    }
  }
  
  function assets(){
    $suffix = !is_wp_debug() ? '.min' : '';
    extract($this->plugin_values);

    if(isset($smooth_scroll))
      add_action('wp_footer', array($this, 'init_scroll'), 99 );

    // sticky
    if(isset($sticky)){
      wp_enqueue_script('sticky', DT_ASSETS_URL . 'jquery.sticky'.$suffix.'.js', array('jquery'), '1.0.4', true);
      add_action('wp_footer', array($this, 'init_sticky'), 99 );
    }

    // animate
    if(isset($animate))
      wp_enqueue_style('animate', DT_ASSETS_URL . 'animate'.$suffix.'.css', array(), '3.5.1');
    
    // font-awesome
    if(isset($font_awesome))
      wp_enqueue_style('font_awesome', DT_ASSETS_URL . 'font-awesome/css/font-awesome'.$suffix.'.css', array(), '4.7.0');

    // fancybox
    if(isset($fancybox)){
      wp_deregister_style('gllr_fancybox_stylesheet');
      foreach (array('gllr_fancybox_js', 'fancybox-script', 'fancybox', 'jquery.fancybox', 'jquery_fancybox', 'jquery-fancybox') as $value) {
        wp_deregister_script($value);
      }

      wp_enqueue_style('fancybox', DT_ASSETS_URL . 'fancybox/jquery.fancybox'.$suffix.'.css');
      wp_enqueue_script('fancybox', DT_ASSETS_URL . 'fancybox/jquery.fancybox'.$suffix.'.js', array('jquery'), '1.6', true);

      if(isset($fancybox_thumb)){
        wp_enqueue_style( 'fancybox-thumb',
          DT_ASSETS_URL . 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.css', array(), '1.0.7' );
        wp_enqueue_script('fancybox-thumb',
          DT_ASSETS_URL . 'fancybox/helpers/jquery.fancybox-thumbs'.$suffix.'.js', array('jquery'), '1.0.7', true);
      }

      if(isset($fancybox_mousewheel))
        wp_enqueue_script('mousewheel', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js', array('jquery'), '3.1.13', true);

      add_action('wp_footer', array($this, 'init_fancybox'), 99 );
    }
  }
  private function add_assets(){

    add_action('wp_enqueue_scripts', array($this, 'assets') ); 
  }
  function init_sticky(){ //has html
    if(!isset($this->plugin_values['sticky_selector']))
      return;

    $value = $this->plugin_values['sticky'];
    $selector = $this->plugin_values['sticky_selector'];

    if ( (wp_is_mobile() && $value == 'phone_only' ) || $value == 'forever' ):
      if( function_exists('is_admin_bar_showing') && is_admin_bar_showing() )
          echo "<style>.admin-bar .is-sticky {$selector} { top: 32px !important; }</style>";
      ?>
      <script type="text/javascript">
        jQuery(document).ready(function($) {
          var $container = $("<?=$selector;?>");
          $container.sticky({topSpacing:0,zIndex:666});
          $container.parent(".sticky-wrapper").css("margin-bottom", $container.css("margin-bottom") );
        });
      </script>
    <?php endif;
  }
  function init_fancybox(){ // has html
    $selector = $this->plugin_values['fancybox'];
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        $('<?=$selector;?>').fancybox({
          nextEffect : "none",
          prevEffect : "none",
          helpers:  {
            title : {
              type : "inside"
            },
            thumbs : {
              width: 120,
              height: 80
            }
          }
        });
      });
    </script>
   
    <?php
  }
  function init_scroll(){
    $top = $this->plugin_values['smooth_scroll'];
    // Прокрутка после загрузки страницы по параметру scroll
    // К пр.: http://mydomain.ru/?scroll=primary
    // Пролистает за $top пикселя до начала объекта #primary
    // Внимание! параметр scroll указывается без "#" и прокручивает только до объекта с ID.
    $scroll_el = !empty($_GET['scroll']) ? esc_attr($_GET['scroll']) : false;
    ?>
    <script type="text/javascript">
      jQuery(document).ready(function($) {
        function scrollTo($elem, returnTop=<?=$top;?>, delay=500){
          $("html, body").animate({ scrollTop: $elem.offset().top - returnTop }, delay);
        }
        $("a[href^=\'#\']").click( function(){
          if( $(this).attr("rel") != "noScroll" ){
            var scrollEl = $(this).attr("href");
            if (scrollEl.length > 1) {
              scrollTo($(scrollEl));
              return false;
            }
          }
        });
        <?php
          if($scroll_el) // scroll from timeOut after load';
            echo 'setTimeout(function(){ scrollTo($("#'.$scroll_el.'")) }, 200);';
        ?>
      });
    </script>
    <?php
  }

  function set_defaults(){
    $defaults = array(
      'orign-image-resize'=>'default',
      );

    update_option( DT_PLUGIN_NAME, $defaults );
  }
  function activation_set_defaults(){
    if($this->plugin_values !== false && sizeof($this->plugin_values) >= 1)
      return false;

    $this->set_defaults();

  }
}
new DevelopersTools();