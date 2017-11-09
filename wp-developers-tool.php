<?php
/*
Plugin Name: Дополнительные настройки разработчика
Plugin URI: https://github.com/nikolays93/wp-developers-tool
Description: Плагин добавляет дополнительные настройки в WordPress.
Version: 5.3.1 beta
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\tool;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if( ! defined('DTOOLS_DEBUG') ) {
    define( 'DTOOLS_DEBUG', apply_filters( 'dtools_debug', true ) );
}

define('LANG', basename(__FILE__, '.php') );

define('DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('DIR_INCLUDES', DIR . '/includes' );
define('URL', rtrim(plugins_url(basename(__DIR__)), '/') );
define('URL_ASSETS', URL . '/assets' );

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\DTools', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\DTools', 'uninstall' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\DTools', 'get_instance' ), 1200 );
class DTools {
    const PREFIX = 'dt_';
    const SETTINGS = 'DTools';

    private $settings = array();

    private static $_instance = null;
    private function __construct() {}
    private function __clone() {}
    static function uninstall() { delete_option(self::SETTINGS); }
    static function activate()
    {
        add_option( self::SETTINGS, array(
            'orign-image-resize' => 'default',
            'remove-emojis' => 'on',
        ) );
    }

    public static function get_instance()
    {
        if( ! self::$_instance ) {
            self::$_instance = new self();
            self::$_instance->initialize();
        }

        return self::$_instance;
    }

    /**
     * Запуск плагина (определяется сразу после создания класса)
     */
    private function initialize()
    {
        // $locale = is_admin() ? get_user_locale() : get_locale();
        // $path = DIR . '/languages/' . LANG . '-' . $locale . '.mo';
        load_plugin_textdomain( LANG, false, LANG . '/languages' );
        $this->settings = get_option( self::SETTINGS, array() );
        self::include_required_files();
        self::include_addons();
    }

    /**
     * Подклчаем классы и управляющие ими файлы
     */
    private static function include_required_files()
    {
        $classes = apply_filters( 'dtools_classes', array(
            __NAMESPACE__ . '\WP_Admin_Page'      => 'wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => 'wp-admin-forms.php',
            ) );

        foreach ($classes as $classname => $file) {
            if( ! class_exists($classname) ) {
                self::load_file_if_exists( DIR_INCLUDES . '/classes/' . $file );
            }
        }

        // includes
        self::load_file_if_exists( DIR_INCLUDES . '/woocommerce.php' );
        self::load_file_if_exists( DIR_INCLUDES . '/admin-page.php' );
    }

    /**
     * Подключаем файлы с фичами
     */
    private static function include_addons()
    {
        $scripts = DIR_INCLUDES . '/addons/init-scripts.php';
        $includes = apply_filters( 'dtools_active', array(
            'maintenance-mode'   => DIR_INCLUDES . '/addons/maintenance-mode.php',
            'remove-images'      => DIR_INCLUDES . '/addons/admin-remove-images.php',
            'second-title'       => DIR_INCLUDES . '/addons/second-title.php',
            'remove-emojis'      => DIR_INCLUDES . '/addons/remove-emojis.php',
            'orign-image-resize' => DIR_INCLUDES . '/addons/admin-orign-image-resize.php',

            'smooth_scroll' => $scripts,
            'sticky'        => $scripts,
            'animate'       => $scripts,
            'font_awesome'  => $scripts,
            'countTo'       => $scripts,
            'back_top'      => $scripts,
        ), self::$_instance->get('all') );

        self::load_file_if_exists( $includes );
        self::load_file_if_exists( DIR_INCLUDES . '/placeholders.php' );
    }

    /**
     * Записываем ошибку
     */
    public static function write_debug( $msg, $dir )
    {
        if( ! defined('DTOOLS_DEBUG') || ! DTOOLS_DEBUG )
            return;

        $dir = str_replace(DIR, '', $dir);
        $msg = str_replace(DIR, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::RSS);

        $handle = fopen(DIR . "/debug.log", "a+");
        fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
        fclose($handle);
    }

    /**
     * Загружаем файл если существует
     */
    public static function load_file_if_exists( $file_array )
    {
        $cant_be_loaded = __('The file %s can not be included', LANG);
        if( is_array( $file_array ) ) {
            foreach ( $file_array as $id => $path ) {
                if ( ! is_readable( $path ) ) {
                    self::write_debug(sprintf($cant_be_loaded, $path), __FILE__);
                    continue;
                }

                require_once( $path );
            }
        }
        else {
            if ( ! is_readable( $file_array ) ) {
                self::write_debug(sprintf($cant_be_loaded, $file_array), __FILE__);
                return false;
            }

            require_once( $file_array );
        }
    }

    /**
     * Загружает формы из файла
     */
    public static function get_settings( $filename )
    {

        return include DIR_INCLUDES . '/settings/' . $filename . '.php';
    }

    /**
     * Получает настройку из $this->settings
     */
    public function get( $prop_name )
    {
        if( $prop_name === 'all' ) {
            if( $this->settings )
                return $this->settings;

            return false;
        }

        return isset( $this->settings[ $prop_name ] ) ? $this->settings[ $prop_name ] : false;
    }
}

/**
 * Подключать только активные
 */
add_filter( 'dtools_active', __NAMESPACE__ . '\active_addons_filter', 10, 1 );
function active_addons_filter( $active )
{
    $DTools = DTools::get_instance();
    foreach ($active as $k => $val) {
        if( ! $DTools->get( $k ) ) unset( $active[ $k ] );
    }

    return $active;
}
