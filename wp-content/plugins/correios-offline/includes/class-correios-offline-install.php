<?php
/**
 * Correios integration with the REST API.
 *
 * @package correios_offline/Classes
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Correios_Offline_REST_API class.
 */
class Correios_Offline_Install {

	/**
	 * Get version.
	 *
	 * @return string
	 */
	private static function get_version() {
		return get_option( 'correios_offline_version' );
	}

	/**
	 * Update version.
	 */
	private static function update_version() {
		update_option( 'correios_offline_version', Correios_Offline_VERSION );
	}

	/**
	 * Upgrade to 3.0.0.
	 */
	public static function upgrade_300() {
		global $wpdb;

		$version = self::get_version();

		if ( empty( $version ) ) {
			$wpdb->query( "UPDATE $wpdb->postmeta SET meta_key = '_correios_tracking_code' WHERE meta_key = 'correios_tracking';" ); // WPCS: db call ok, cache ok.
		}
	}

	/**
	 * Upgrade to 3.0.0 while using WooCommerce 2.6.0.
	 */
	public static function upgrade_300_from_wc_260() {
		$old_options = get_option( 'correios_offline_settings' );
		if ( $old_options ) {
			if ( isset( $old_options['tracking_history'] ) ) {
				$integration_options = get_option( 'correios_offline-integration_settings', array(
					'general_options' => '',
					'tracking'        => '',
					'enable_tracking' => 'no',
					'tracking_debug'  => 'no',
				) );

				// Update integration options.
				$integration_options['enable_tracking'] = $old_options['tracking_history'];
				update_option( 'correios_offline-integration_settings', $integration_options );

				// Update the old options.
				unset( $old_options['tracking_history'] );
				update_option( 'correios_offline_settings', $old_options );
			}

			if ( 'no' === $old_options['enabled'] ) {
				delete_option( 'correios_offline_settings' );
			}
		}
	}
}
