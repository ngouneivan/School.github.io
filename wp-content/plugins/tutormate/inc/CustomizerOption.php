<?php
/**
 * A class that extends WP_Customize_Setting so we can access
 * the protected updated method when importing options.
 *
 * Used in the Customizer importer.
 *
 * @since 1.0.0
 * @package Tutormate
 */

namespace TUTORMATE;

defined( 'ABSPATH' ) || exit;

/**
 * Customizer Option class
 */
final class CustomizerOption extends \WP_Customize_Setting {

	/**
	 * Import an option value for this setting.
	 *
	 * @since 1.0.0
	 * @param mixed $value The option value.
	 * @return void
	 */
	public function import( $value ) {
		$this->update( $value );
	}
}
