<?php
/**
 * Handles checking required plugins statuses
 *
 * @package Tutormate
 */

namespace TUTORMATE;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin Check class
 */
class PluginCheck {

	/**
	 * Static var active plugins
	 *
	 * @var $active_plugins
	 */
	private static $active_plugins;

	/**
	 * Static var installed plugins
	 *
	 * @var $installed_plugins
	 */
    private static $installed_plugins;
    
	/**
	 * Initialize
	 */
	public static function init() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		self::$installed_plugins = ( array ) get_plugins();

		self::$active_plugins = ( array ) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
	}

	/**
	 * Check plugin status
	 *
	 * @param string $plugin_base_name is plugin folder/filename.php.
	 */
	public static function check_status( $plugin_base_name ) {
		if ( ! self::$active_plugins || ! self::$installed_plugins ) {
			self::init();
		}
		if ( in_array( $plugin_base_name, self::$active_plugins, true ) || array_key_exists( $plugin_base_name, self::$active_plugins ) ) {
			return 'active';
		} elseif ( in_array( $plugin_base_name, self::$installed_plugins, true ) || array_key_exists( $plugin_base_name, self::$installed_plugins ) ) {
			return 'installed';
		} else {
			return 'not installed';
		}
	}
}