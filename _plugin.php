<?php

/*
 * Plugin Name: WordPress Developer's multitool
 * Plugin URI: https://github.com/nikolays93/wp-developers-tool
 * Description: Add more advanced functions for your Wordpress site.
 * Version: 2.0
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: Tools
 * Domain Path: /languages/
 */

/**
 * Фильтры плагина:
 * "get_{Text Domain}_option_name" - имя опции плагина
 * "get_{Text Domain}_option" - значение опции плагина
 * "get_{Text Domain}_plugin_url" - УРЛ плагина
 */

namespace NikolayS93\Tools;

__("WordPress Developer\'s multitool", DOMAIN);
__("Add more advanced functions for your Wordpress site.", DOMAIN);

use NikolayS93\WPAdminPage as Admin;

if ( ! defined( 'ABSPATH' ) ) exit('You shall not pass'); // disable direct access

require_once ABSPATH . "wp-admin/includes/plugin.php";

if (version_compare(PHP_VERSION, '5.3') < 0) {
    throw new \Exception('Plugin requires PHP 5.3 or above');
}

class Plugin
{
    protected static $data;
    protected static $options;

    private function __construct() {}

    static function activate() {
        add_option( self::get_option_name(), array(
            'orign-image-resize' => 'default',
            'remove-emojis' => 'on',
        ) );
    }

    static function uninstall() { delete_option( self::get_option_name() ); }

    /**
     * Получает название опции плагина
     *     Чаще всего это название плагина
     *     Чаще всего оно используется как название страницы настроек
     * @return string
     */
    public static function get_option_name()
    {
        return apply_filters("get_{DOMAIN}_option_name", DOMAIN);
    }

    public static function _admin_assets()
    {
        // array(
            // 'tab_sections' => array(
            //     DTools::PREFIX . 'general' => __('Main', DOMAIN),
            //     DTools::PREFIX . 'scripts' => __('Scripts', DOMAIN),
            // ),
            // 'callback' => array(
            //     DTools::PREFIX . 'general' => array(__CLASS__, 'general_settings_tab'),
            //     DTools::PREFIX . 'scripts' => array(__CLASS__, 'scripts_settings_tab'),
            // ),
        // );

        wp_enqueue_script(
            'NikolayS93\Tools\Plugin\_admin_assets',
            Utils::get_plugin_url('assets/admin.js'),
            array('jquery'),
            false,
            true
        );
    }

    public static function admin_menu_page()
    {
        $page = new Admin\Page(
            Utils::get_option_name(),
            __('Advanced settings', DOMAIN),
            array(
                'parent'      => 'options-general.php',
                'menu'        => __('Advance', DOMAIN),
                'validate'    => array(__CLASS__, 'validate_options'),
                'permissions' => 'manage_options',
                'columns'     => 1,
            )
        );

        $page->set_assets( array(__CLASS__, '_admin_assets') );

        $page->set_content( function() {
            Utils::get_admin_template('menu-page.php', false, $inc = true);
        } );

        $page->add_section( new Admin\Section(
            'Section',
            __('Section'),
            function() {
                Utils::get_admin_template('section.php', false, $inc = true);
            }
        ) );

        // $metabox1 = new Admin\Metabox(
        //     'metabox1',
        //     __('metabox1', DOMAIN),
        //     function() {
        //         Utils::get_admin_template('metabox1.php', false, $inc = true);
        //     },
        //     $position = 'side',
        //     $priority = 'high'
        // );

        // $page->add_metabox( $metabox1 );

        // $metabox2 = new Admin\Metabox(
        //     'metabox2',
        //     __('metabox2', DOMAIN),
        //     function() {
        //         Utils::get_admin_template('metabox2.php', false, $inc = true);
        //     },
        //     $position = 'side',
        //     $priority = 'high'
        // );

        // $page->add_metabox( $metabox2 );

        add_action($page->page . '_after_form_inputs', 'submit_button' );
    }

    public static function define()
    {
        self::$data = get_plugin_data(__FILE__);

        if( !defined(__NAMESPACE__ . '\DOMAIN') )
            define(__NAMESPACE__ . '\DOMAIN', self::$data['TextDomain']);

        if( !defined(__NAMESPACE__ . '\PLUGIN_DIR') )
            define(__NAMESPACE__ . '\PLUGIN_DIR', __DIR__);
    }

    public static function initialize()
    {
        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );

        require PLUGIN_DIR . '/include/utils.php';
        // require PLUGIN_DIR . '/vendor/NikolayS93/wp-admin-page/src/Page.php';

        $autoload = PLUGIN_DIR . '/vendor/autoload.php';
        if( file_exists($autoload) ) include $autoload;

        $includes = apply_filters( 'dtools_active', array(
            'maintenance-mode'   => PLUGIN_DIR . '/include/maintenance-mode.php',
            'second-title'       => PLUGIN_DIR . '/include/second-title.php',
            'record-views'       => PLUGIN_DIR . '/include/record-views.php',
            'remove-images'      => PLUGIN_DIR . '/include/admin-remove-images.php',
            'remove-emojis'      => PLUGIN_DIR . '/include/remove-emojis.php',
            'orign-image-resize' => PLUGIN_DIR . '/include/admin-orign-image-resize.php',
            'empty-content'      => PLUGIN_DIR . '/include/empty-content.php',

            'smooth_scroll' => PLUGIN_DIR . '/include/init-scripts.php',
            'back_top'      => PLUGIN_DIR . '/include/init-scripts.php',
        ), self::get('all') );

        // foreach ($includes as $include) {
        //     if(is_file($include)) include $include;
        // }

        // include PLUGIN_DIR . '/include/placeholders.php';

        self::admin_menu_page();
    }

    public static function validate( $inputs )
    {
        $inputs = \NikolayS93\WPAdminPage\Util::array_filter_recursive($inputs);

        return $inputs;
    }
}

Plugin::define();

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'initialize' ), 10 );


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