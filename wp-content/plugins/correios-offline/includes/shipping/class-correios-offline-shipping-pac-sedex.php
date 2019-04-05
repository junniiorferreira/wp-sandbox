<?php
/**
 * Correios PAC shipping method.
 *
 * @package correios_offline/Classes/Shipping
 * @since   3.0.0
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PAC shipping method class.
 */
class Correios_Offline_Shipping_PAC extends Correios_Offline_Shipping {

	/**
	 * Service code.
	 * 04510 - PAC without contract.
	 *
	 * @var string
	 */
	protected $code = '04510';

	/**
	 * Corporate code.
	 * 04669 - PAC with contract.
	 *
	 * @var string
	 */
	protected $corporate_code = '04669';

	/**
	 * Initialize PAC.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'correios-offline-pac';
		$this->method_title = __( 'PAC Offline', 'correios-offline' );
		$this->more_link    = 'http://www.correios.com.br/para-voce/correios-de-a-a-z/pac-encomenda-economica';

		parent::__construct( $instance_id );
	}

	/**
	 * Get the declared value from the package.
	 *
	 * @param  array $package Cart package.
	 * @return float
	 */
	protected function get_declared_value( $package ) {
		if ( 18 >= $package['contents_cost'] ) {
			return 0;
		}

		return $package['contents_cost'];
	}
}
