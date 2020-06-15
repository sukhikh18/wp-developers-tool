<?php
/**
 * Register plugin actions
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\Tools;

use NikolayS93\WPAdminPage\Page;
use NikolayS93\WPAdminPage\Section;
use NikolayS93\WPAdminPage\Metabox;

/**
 * Class Register
 */
class Register {

	/**
	 * Call this method before activate plugin
	 */
	public static function activate() {
		add_option( Plugin::get_option_name(), array(
            'orign-image-resize' => 'default',
        ) );
	}

	/**
	 * Call this method before disable plugin
	 */
	public static function deactivate() {
	}

	/**
	 * Call this method before delete plugin
	 */
	public static function uninstall() {
		delete_option( Plugin::get_option_name() );
	}

	/**
	 * Register new admin menu item
	 *
	 * @return null|Page $Page
	 */
	public static function register_plugin_page() {
		if ( ! class_exists( 'NikolayS93\WPAdminPage\Page' ) ) {
			return null;
		}

		$page = new Page(
			Plugin::get_option_name(),
			__( 'Advanced settings', DOMAIN ),
			array(
				'parent'      => 'options-general.php',
				'menu'        => __( 'Advance', DOMAIN ),
				'permissions' => Plugin::get_once_permission(),
				'columns'     => 1,
			)
		);

		$page->set_content(
			function () {
				if ( $template = Plugin::get_template( 'admin/template/menu-page' ) ) {
					require $template;
				}
			}
		);

		$page->add_section(
			new Section(
				'general',
				__( 'General', DOMAIN ),
				Plugin::get_template( 'admin/template/general' )
			)
		);

		$page->add_section(
			new Section(
				'scripts',
				__( 'Scripts', DOMAIN ),
				Plugin::get_template( 'admin/template/scripts' )
			)
		);

		if ( class_exists( '\WooCommerce' ) ) {
			$page->add_section(
				new Section(
					'woocommerce',
					__( 'Woocommerce', DOMAIN ),
					Plugin::get_template( 'admin/template/woocommerce' )
				)
			);
		}

		return $page;
	}

	public static function register_placeholders() {
		require_once PLUGIN_DIR . 'include/placeholders.php';
	}

	public function require_map($path) {
	}

	public function require( $addons ) {
		$enabled = Plugin::get_settings();

		foreach ($enabled as $addon_name => $value) {
			$filename = !empty($addons[$addon_name]) ? $addons[$addon_name] : false;

			if($filename && file_exists($filename)) {
				require_once $filename;
			} else {
				error_log( "$addon_name not exists ($filename)" );
			}
		}
	}
}
