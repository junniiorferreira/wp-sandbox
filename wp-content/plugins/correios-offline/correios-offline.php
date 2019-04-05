<?php
/**
 * Plugin Name:          CEP (Correios Exato Pro) para WooCommerce
 * Description:          Aplica métodos de entrega dentro do próprio site.
 * Author:               Carlos Ferreria
 * Author URI:           https://clferreira.com
 * Version:              Beta 0
 * License:              GPLv2 or later
 * Text Domain:          correios-offline
 * Domain Path:          /languages
 * WC requires at least: 3.0.0
 * WC tested up to:      3.5.0
 *
 * Correios Offline is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Correios Offline is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Correios Offline. If not, see
 * <https://www.gnu.org/licenses/gpl-2.0.txt>.
 *
 * @package correios_offline
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'Correios_Offline_VERSION', '3.7.1' );
define( 'Correios_Offline_PLUGIN_FILE', __FILE__ );

if ( ! class_exists( 'Correios_Offline' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-correios-offline.php';

	add_action( 'plugins_loaded', array( 'Correios_Offline', 'init' ) );
}
