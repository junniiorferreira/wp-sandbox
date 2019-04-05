<?php
/**
 * Correios
 *
 * @package correios_offline/Classes
 * @since   3.6.0
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Correios Offline main class.
 */
class Correios_Offline {

	/**
	 * Initialize the plugin public actions.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ), -1 );

		// Checks with WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {
			self::includes();

			if ( is_admin() ) {
				self::admin_includes();
			}

			add_filter( 'woocommerce_integrations', array( __CLASS__, 'include_integrations' ) );
			add_filter( 'woocommerce_shipping_methods', array( __CLASS__, 'include_methods' ) );
			add_filter( 'woocommerce_email_classes', array( __CLASS__, 'include_emails' ) );
		} else {
			add_action( 'admin_notices', array( __CLASS__, 'woocommerce_missing_notice' ) );
		}
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'correios-offline', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Includes.
	 */
	private static function includes() {
		include_once dirname( __FILE__ ) . '/correios-offline-functions.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-install.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-package.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-webservice.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-webservice-international.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-autofill-addresses.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-tracking-history.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-rest-api.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-orders.php';
		include_once dirname( __FILE__ ) . '/class-correios-offline-cart.php';

		// Integration.
		include_once dirname( __FILE__ ) . '/integrations/class-correios-offline-integration.php';

		// Shipping methods.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
			include_once dirname( __FILE__ ) . '/abstracts/class-correios-offline-shipping.php';
			include_once dirname( __FILE__ ) . '/abstracts/class-correios-offline-shipping-carta.php';
			include_once dirname( __FILE__ ) . '/abstracts/class-correios-offline-shipping-impresso.php';
			include_once dirname( __FILE__ ) . '/abstracts/class-correios-offline-shipping-international.php';
			foreach ( glob( plugin_dir_path( __FILE__ ) . '/shipping/*.php' ) as $filename ) {
				include_once $filename;
			}

			// Update settings to 3.0.0 when using WooCommerce 2.6.0.
			Correios_Offline_Install::upgrade_300_from_wc_260();
		} else {
			include_once dirname( __FILE__ ) . '/shipping/class-correios-offline-shipping-legacy.php';
		}

		// Update to 3.0.0.
		Correios_Offline_Install::upgrade_300();
	}

	/**
	 * Admin includes.
	 */
	private static function admin_includes() {
		include_once dirname( __FILE__ ) . '/admin/class-correios-offline-admin-orders.php';
	}

	/**
	 * Include Correios integration to WooCommerce.
	 *
	 * @param  array $integrations Default integrations.
	 *
	 * @return array
	 */
	public static function include_integrations( $integrations ) {
		$integrations[] = 'Correios_Offline_Integration';

		return $integrations;
	}

	/**
	 * Include Correios shipping methods to WooCommerce.
	 *
	 * @param  array $methods Default shipping methods.
	 *
	 * @return array
	 */
	public static function include_methods( $methods ) {
		// Legacy method.
		$methods['correios-legacy'] = 'Correios_Offline_Shipping_Legacy';

		// New methods.
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.6.0', '>=' ) ) {
			// $methods['correios-offline-pac-sedex']    = 'Correios_Offline_Shipping_PAC_SEDEX';
			$methods['correios-offline-sedex']    = 'Correios_Offline_Shipping_SEDEX';
			$methods['correios-offline-pac']    = 'Correios_Offline_Shipping_PAC';
			$methods['correios-offline-carta-registrada']     = 'Correios_Offline_Shipping_Carta_Registrada';
			$methods['correios-offline-impresso-normal']      = 'Correios_Offline_Shipping_Impresso_Normal';
			$methods['correios-offline-impresso-urgente']     = 'Correios_Offline_Shipping_Impresso_Urgente';

			$old_options = get_option( 'correios_offline_settings' );
			if ( empty( $old_options ) ) {
				unset( $methods['correios-legacy'] );
			}
		}

		return $methods;
	}

	/**
	 * Include emails.
	 *
	 * @param  array $emails Default emails.
	 *
	 * @return array
	 */
	public static function include_emails( $emails ) {
		if ( ! isset( $emails['Correios_Offline_Tracking_Email'] ) ) {
			$emails['Correios_Offline_Tracking_Email'] = include dirname( __FILE__ ) . '/emails/class-correios-offline-tracking-email.php';
		}

		return $emails;
	}

	/**
	 * WooCommerce fallback notice.
	 */
	public static function woocommerce_missing_notice() {
		include_once dirname( __FILE__ ) . '/admin/views/html-admin-missing-dependencies.php';
	}

	/**
	 * Get main file.
	 *
	 * @return string
	 */
	public static function get_main_file() {
		return Correios_Offline_PLUGIN_FILE;
	}

	/**
	 * Get plugin path.
	 *
	 * @return string
	 */
	public static function get_plugin_path() {
		return plugin_dir_path( Correios_Offline_PLUGIN_FILE );
	}

	/**
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path() {
		return self::get_plugin_path() . 'templates/';
	}
}
