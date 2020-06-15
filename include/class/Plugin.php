<?php
/**
 * Main singleton plugin class
 *
 * @package Newproject.WordPress.plugin
 */

namespace NikolayS93\Tools;

use ReflectionClass;
use ReflectionException;
use RuntimeException;

/**
 * Class Plugin
 */
class Plugin {
	/**
	 * The stored singleton instance
	 *
	 * @var static Plugin
	 */
	protected static $instance;

	/**
	 * Plugin's data by main file.
	 *
	 * @var array
	 */
	public $data;

	/**
	 * Creates the original or retrieves the stored singleton instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			try {
				static::$instance = ( new ReflectionClass( get_called_class() ) )
					->newInstanceWithoutConstructor();
				call_user_func_array( array( static::$instance, 'init' ), func_get_args() );
			} catch ( ReflectionException $e ) {
				wp_die( esc_html( $e->getMessage() ) );
			}
		}

		return static::$instance;
	}

	/**
	 * The constructor is disabled
	 *
	 * @throws RuntimeException If called.
	 */
	public function __construct() {
		throw new RuntimeException( 'You may not explicitly instantiate this object, because it is a singleton.' );
	}

	/**
	 * Method on initialize (instead __construct)
	 *
	 * @param $file string path to main file
	 */
	public function init( $file ) {
		require_once ABSPATH . "wp-admin/includes/plugin.php";

		$this->data = get_plugin_data( $file );

		if ( ! defined( __NAMESPACE__ . '\DOMAIN' ) ) {
			define( __NAMESPACE__ . '\DOMAIN', $this->data['TextDomain'] );
		}

		if ( ! defined( __NAMESPACE__ . '\ADDONS_DIR' ) ) {
			define( __NAMESPACE__ . '\ADDONS_DIR', PLUGIN_DIR . 'include/addons/' );
		}

		if ( ! defined( __NAMESPACE__ . '\WOO_ADDONS_DIR' ) ) {
			define( __NAMESPACE__ . '\WOO_ADDONS_DIR', ADDONS_DIR . 'woocommerce/' );
		}
	}

	/**
	 * Get plugin prefix
	 *
	 * @return string
	 */
	public static function prefix() {
		return DOMAIN . '_';
	}

	/**
	 * Get option name for a options in the WordPress database
	 *
	 * @param string $suffix option name suffix "plugin_$suffix".
	 *
	 * @return string
	 */
	public static function get_option_name( $suffix = '' ) {
		$option_name = $suffix ? Plugin::prefix() . $suffix : DOMAIN;

		return apply_filters( Plugin::prefix() . 'get_option_name', $option_name, $suffix );
	}

	/**
	 * Get capability required to use this plugin.
	 *
	 * @return array
	 */
	public static function get_permissions() {
		return apply_filters( Plugin::prefix() . 'get_permissions', array( 'manage_options' ) );
	}

	/**
	 * @return string
	 */
	public static function get_once_permission() {
		return current( static::get_permissions() );
	}

	/**
	 * Get plugin dir (without slash end)
	 *
	 * @param string $path Path to something relative.
	 *
	 * @return string
	 */
	public static function get_dir( $path = '' ) {
		return PLUGIN_DIR . ltrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get file by plugin dir path
	 *
	 * @param string $dir_path [description].
	 * @param string $filename [description].
	 *
	 * @return string
	 */
	public static function get_file( $dir_path, $filename ) {
		return self::get_dir( $dir_path ) . trim( $filename, DIRECTORY_SEPARATOR );
	}

	/**
	 * Get plugin url
	 *
	 * @param string $path Path to something relative.
	 *
	 * @return string
	 */
	public static function get_url( $path = '' ) {
		$basename = basename( self::get_dir() );
		$url      = plugins_url( $basename ) . '/' . ltrim( $path, '/' );

		return apply_filters( Plugin::prefix() . 'get_url', $url, $path, $basename );
	}


	/**
	 * Get plugin template path
	 *
	 * @param  [type] $template [description].
	 *
	 * @return string|false
	 */
	public static function get_template( $template ) {
		if ( ! pathinfo( $template, PATHINFO_EXTENSION ) ) {
			$template .= '.php';
		}

		$path = self::get_dir() . ltrim( $template, '/' );
		if ( ! file_exists( $path ) || ! is_readable( $path ) ) {
			$path = false;
		}

		return apply_filters( Plugin::prefix() . 'get_template', $path, $template );
	}

	/**
	 * @param  mixed $default What's return if field value not defined.
	 * @param  string $context suffix option name. @see get_option_name().
	 *
	 * @return mixed
	 */
	public static function get_settings( $default = false, $context = '' ) {
		/** @var string */
		$option_name = self::get_option_name( $context );

		/**
		 * Get field value from wp_options
		 *
		 * @link https://developer.wordpress.org/reference/functions/get_option/
		 * @var mixed
		 */
		return apply_filters( Plugin::prefix() . 'get_option', get_option( $option_name, $default ) );
	}

	/**
	 * Get plugin setting from cache or database
	 *
	 * @param string $prop_name Option key.
	 * @param mixed $default What's return if field value not defined.
	 * @param string $context suffix option name. @see get_option_name().
	 *
	 * @return mixed
	 */
	public static function get_setting( $prop_name, $default = false, $context = '' ) {
		/** @var string @todo add exception */
		$prop_name = (string) $prop_name;
		/** @var array */
		$option = static::get_settings( $default, $context );

		return isset( $option[ $prop_name ] ) ? $option[ $prop_name ] : $default;
	}

	/**
	 * Set new plugin setting
	 *
	 * @param string|array $prop_name Option key || array.
	 * @param string $value prop_name key => value.
	 * @param string $context suffix option name. @see get_option_name().
	 *
	 * @return bool                   Is updated @see update_option()
	 */
	public static function set_setting( $prop_name, $value = '', $context = '', $autoload = 'no' ) {
		if ( ! $prop_name || ( $value && ! (string) $prop_name ) ) {
			// @todo add exception
			return false;
		}

		// Get all defined settings by context.
		$option = self::get_setting( null, false, $context );

		if ( is_array( $prop_name ) ) {
			$option = array_merge( $option, $prop_name );
		} else {
			$option[ $prop_name ] = $value;
		}

		// Do not auto load for plugin settings.
		$autoload = $autoload === 'no' ? 'no' : 'yes';

		return update_option(
			self::get_option_name( $context ),
			$option,
			apply_filters( Plugin::prefix() . 'autoload', $autoload, $option, $context )
		);
	}
}
