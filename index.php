<?php
/**
 * Plugin Name: WordPress Developer's multitool
 * Plugin URI: https://github.com/nikolays93
 * Description: Add more advanced functions for your Wordpress site.
 * Version: 2.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * Author EMAIL: NikolayS93@ya.ru
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: Tools
 * Domain Path: /languages/
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\Tools;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You shall not pass' );
}

if ( ! defined( __NAMESPACE__ . '\PLUGIN_DIR' ) ) {
	define( __NAMESPACE__ . '\PLUGIN_DIR', dirname( __FILE__ ) . DIRECTORY_SEPARATOR );
}

if ( is_readable( PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once 'vendor/autoload.php';
}

require_once 'include/class/Plugin.php';
require_once 'include/class/Register.php';
require_once 'include/class-wc-product-settings.php';

/**
 * Initialize this plugin once all other plugins have finished loading.
 */
add_action(
	'plugins_loaded',
	function () {
		/**
		 * @var Plugin
		 */
		$plugin = Plugin::get_instance( __FILE__ );

		// load plugin languages.
		load_plugin_textdomain( DOMAIN, false, basename( Plugin::get_dir() ) . '/languages/' );

		$addons = array(
			'maintenance-mode'   => ADDONS_DIR . 'maintenance-mode.php',
			'second-title'       => ADDONS_DIR . 'second-title.php',
			'record-views'       => ADDONS_DIR . 'record-views.php',
			'remove-images'      => ADDONS_DIR . 'admin-remove-images.php',
			'remove-emojis'      => ADDONS_DIR . '/remove-emojis.php',
			'orign-image-resize' => ADDONS_DIR . 'admin-orign-image-resize.php',
			'empty-content'      => ADDONS_DIR . 'empty-content.php',

			'smooth_scroll'      => ADDONS_DIR . 'init-scripts.php',
			'back_top'           => ADDONS_DIR . 'init-scripts.php',
		);

		if ( class_exists( '\WooCommerce' ) ) {
			$addons = array_merge($addons, array(
				'product-measure-unit' => WOO_ADDONS_DIR . 'measure-unit.php',
				'wholesales'           => WOO_ADDONS_DIR . 'wholesales.php',
				'plus-minus-buttons'   => WOO_ADDONS_DIR . 'plus-minus-buttons.php',
				'pack-qty'             => WOO_ADDONS_DIR . 'pack-qty.php',
				'pack-qty-cat'         => WOO_ADDONS_DIR . 'pack-qty-cat.php',
				'pack-qty-changes'     => WOO_ADDONS_DIR . 'pack-qty-changes.php',
				'bestsellers'          => WOO_ADDONS_DIR . 'bestsellers.php',
				'qty-stock-decimals'   => WOO_ADDONS_DIR . 'qty-stock-decimals.php',
			));
		}

		$register = new Register();
		$register->register_plugin_page();
		$register->require( $addons );
	},
	20
);

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'activate' ) );
register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Register', 'uninstall' ) );
