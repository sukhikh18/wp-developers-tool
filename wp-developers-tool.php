<?php
/*
Plugin Name: WordPress Developer's Tool
Plugin URI: https://github.com/nikolays93/wp-developers-tool
Description: Add more advanced functions for your Wordpress site.
Version: 1.2.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\tool;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

const DOMAIN = 'wp-developers-tool';

class DTools {
    const PREFIX = 'dt_';
    const OPTION = 'DTools';

    private static $initialized;
    private static $settings;

    private function __construct() {}
    private function __clone() {}
    static function uninstall() { delete_option(self::OPTION); }
    static function activate()
    {
        add_option( self::OPTION, array(
            'orign-image-resize' => 'default',
            'remove-emojis' => 'on',
        ) );
    }

    private static function include_required_files()
    {
        $class_dir = self::get_plugin_dir('classes');
        $includes = self::get_plugin_dir('includes');
        $classes = array(
            __NAMESPACE__ . '\WP_Admin_Page'      => $class_dir . '/wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => $class_dir . '/wp-admin-forms.php',
            );

        foreach ($classes as $classname => $path) {
            if( ! class_exists($classname) ) {
                 self::load_file_if_exists( $path );
            }
        }

        // includes
        self::load_file_if_exists( $includes . '/woocommerce.php' );
        self::load_file_if_exists( $includes . '/admin-page.php' );
    }

    /**
     * Подключаем файлы с фичами
     */
    private static function include_addons()
    {
        $addons = self::get_plugin_dir('includes/addons');
        $scripts = $addons . '/init-scripts.php';
        $includes = apply_filters( 'dtools_active', array(
            'maintenance-mode'   => $addons . '/maintenance-mode.php',
            'second-title'       => $addons . '/second-title.php',
            'record-views'       => $addons . '/record-views.php',
            'remove-images'      => $addons . '/admin-remove-images.php',
            'remove-emojis'      => $addons . '/remove-emojis.php',
            'orign-image-resize' => $addons . '/admin-orign-image-resize.php',
            'empty-content'      => $addons . '/empty-content.php',

            'smooth_scroll' => $scripts,
            'sticky'        => $scripts,
            'animate'       => $scripts,
            'font_awesome'  => $scripts,
            'countTo'       => $scripts,
            'back_top'      => $scripts,
        ), self::get('all') );

        self::load_file_if_exists( $includes );
        self::load_file_if_exists( self::get_plugin_dir('includes') . '/placeholders.php' );
    }

    public static function initialize()
    {
        if( self::$initialized ) {
            return false;
        }

        load_plugin_textdomain( DOMAIN, false, DOMAIN . '/languages/' );
        self::include_required_files();
        self::include_addons();

        self::$initialized = true;
    }

    /**
     * Записываем ошибку
     */
    public static function write_debug( $msg, $dir )
    {
        if( ! defined('WP_DEBUG_LOG') || ! WP_DEBUG_LOG )
            return;

        $dir = str_replace(__DIR__, '', $dir);
        $msg = str_replace(__DIR__, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::W3C);

        if( $handle = @fopen(__DIR__ . "/debug.log", "a+") ) {
            fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
            fclose($handle);
        }
        elseif (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            echo "Не удается получить доступ к файлу " . __DIR__ . "/debug.log";
            echo "{$msg} ({$dir})";
        }
    }

    /**
     * Загружаем файл если существует
     */
    public static function load_file_if_exists( $file_array, $args = array() )
    {
        $cant_be_loaded = __('The file %s can not be included', DOMAIN);
        if( is_array( $file_array ) ) {
            $result = array();
            foreach ( $file_array as $id => $path ) {
                if ( ! is_readable( $path ) ) {
                    self::write_debug(sprintf($cant_be_loaded, $path), __FILE__);
                    continue;
                }

                $result[] = include_once( $path );
            }
        }
        else {
            if ( ! is_readable( $file_array ) ) {
                self::write_debug(sprintf($cant_be_loaded, $file_array), __FILE__);
                return false;
            }

            $result = include_once( $file_array );
        }

        return $result;
    }

    public static function get_plugin_dir( $path = false )
    {
        $result = __DIR__;

        switch ( $path ) {
            case 'classes': $result .= '/includes/classes'; break;
            case 'settings': $result .= '/includes/settings'; break;
            default: $result .= '/' . $path;
        }

        return $result;
    }

    public static function get_plugin_url( $path = false )
    {
        $result = plugins_url(basename(__DIR__) );

        switch ( $path ) {
            default: $result .= '/' . $path;
        }

        return $result;
    }

    /**
     * Получает настройку из self::$settings или из кэша или из базы данных
     */
    public static function get( $prop_name, $default = false )
    {
        if( ! self::$settings )
            self::$settings = get_option( self::OPTION, array() );

        if( 'all' === $prop_name ) {
            if( is_array(self::$settings) && count(self::$settings) )
                return self::$settings;

            return $default;
        }

        return isset( self::$settings[ $prop_name ] ) ? self::$settings[ $prop_name ] : $default;
    }

    public static function get_settings( $filename, $args = array() )
    {
        return self::load_file_if_exists( self::get_plugin_dir('includes/settings/') . $filename, $args );
    }
}

/**
 * Подключать только активные
 */
add_filter( 'dtools_active', __NAMESPACE__ . '\active_addons_filter', 10, 1 );
function active_addons_filter( $active )
{
    foreach ($active as $k => $val) {
        if( ! DTools::get( $k ) ) unset( $active[ $k ] );
    }

    return $active;
}

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\DTools', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\DTools', 'uninstall' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\DTools', 'initialize' ), 1200 );