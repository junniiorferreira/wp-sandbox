<?php
/**
 * Correios functions.
 *
 * @package correios_offline/Functions
 * @since   3.0.0
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Safe load XML.
 *
 * @throws Exception Show detected errors while parsing the data.
 *
 * @param  string $source Source XML.
 * @param  int    $options Reading options.
 *
 * @return SimpleXMLElement|bool
 */
function Correios_Offline_safe_load_xml( $source, $options = 0 ) {
	$old = null;

	if ( function_exists( 'libxml_disable_entity_loader' ) ) {
		$old = libxml_disable_entity_loader( true );
	}

	$dom    = new DOMDocument();
	$return = $dom->loadXML( trim( $source ), $options );

	if ( ! is_null( $old ) ) {
		libxml_disable_entity_loader( $old );
	}

	if ( ! $return ) {
		return false;
	}

	if ( isset( $dom->doctype ) ) {
		throw new Exception( 'Unsafe DOCTYPE Detected while XML parsing' );
	}

	return simplexml_import_dom( $dom );
}

/**
 * Sanitize postcode.
 *
 * @param  string $postcode Postcode.
 *
 * @return string
 */
function Correios_Offline_sanitize_postcode( $postcode ) {
	return preg_replace( '([^0-9])', '', sanitize_text_field( $postcode ) );
}

/**
 * Get estimating delivery description.
 *
 * @param string $name            Shipping name.
 * @param string $days            Estimated days to accomplish delivery.
 * @param int    $additional_days Additional days.
 *
 * @return string
 */
function Correios_Offline_get_estimating_delivery( $name, $days, $additional_days = 0 ) {
	$total = intval( $days ) + intval( $additional_days );

	if ( $total > 0 ) {
		/* translators: %d: days to delivery */
		$name .= ' (' . sprintf( _n( 'Delivery within %d working day', 'Delivery within %d working days', $total, 'correios-offline' ), $total ) . ')';
	}

	return apply_filters( 'correios_offline_get_estimating_delivery', $name, $days, $additional_days );
}

/**
 * Fix Correios prices.
 *
 * @param  string $value Value to fix.
 *
 * @return string
 */
function Correios_Offline_normalize_price( $value ) {
	$value = str_replace( '.', '', $value );
	$value = str_replace( ',', '.', $value );

	return $value;
}

/**
 * Get error messages.
 *
 * @param  string $code Error code.
 *
 * @return string
 */
function Correios_Offline_get_error_message( $code ) {
	$code = (string) $code;

	$messages = apply_filters( 'correios_offline_available_error_messages', array(
		'-33' => __( 'System temporarily down. Please try again later.', 'correios-offline' ),
		'-3'  => __( 'Invalid zip code.', 'correios-offline' ),
		'010' => __( 'Area with delivery temporarily subjected to different periods.', 'correios-offline' ),
		'011' => __( 'The destination CEP is subject to special delivery conditions by ECT and will be carried out with the addition of up to 7 (seven) business days to the regular term.', 'correios-offline' ),
	) );

	return isset( $messages[ $code ] ) ? $messages[ $code ] : '';
}

/**
 * Trigger tracking code email notification.
 *
 * @param WC_Order $order         Order data.
 * @param string   $tracking_code The Correios tracking code.
 */
function Correios_Offline_trigger_tracking_code_email( $order, $tracking_code ) {
	$mailer       = WC()->mailer();
	$notification = $mailer->emails['Correios_Offline_Tracking_Email'];

	if ( 'yes' === $notification->enabled ) {
		if ( method_exists( $order, 'get_id' ) ) {
			$notification->trigger( $order->get_id(), $order, $tracking_code );
		} else {
			$notification->trigger( $order->id, $order, $tracking_code );
		}
	}
}

/**
 * Get tracking codes.
 *
 * @param  WC_Order|int $order Order ID or order data.
 *
 * @return array
 */
function Correios_Offline_get_tracking_codes( $order ) {
	if ( is_numeric( $order ) ) {
		$order = wc_get_order( $order );
	}

	if ( method_exists( $order, 'get_meta' ) ) {
		$codes = $order->get_meta( '_correios_tracking_code' );
	} else {
		$codes = $order->correios_tracking_code;
	}

	return array_filter( explode( ',', $codes ) );
}

/**
 * Update tracking code.
 *
 * @param  WC_Order|int $order         Order ID or order data.
 * @param  string       $tracking_code Tracking code.
 * @param  bool         $remove        If should remove the tracking code.
 *
 * @return bool
 */
function Correios_Offline_update_tracking_code( $order, $tracking_code, $remove = false ) {
	$tracking_code = sanitize_text_field( $tracking_code );

	// Get order instance.
	if ( is_numeric( $order ) ) {
		$order = wc_get_order( $order );
	}

	if ( method_exists( $order, 'get_meta' ) ) {
		$tracking_codes = $order->get_meta( '_correios_tracking_code' );
	} else {
		$tracking_codes = $order->correios_tracking_code;
	}

	$tracking_codes = array_filter( explode( ',', $tracking_codes ) );

	if ( '' === $tracking_code ) {
		if ( method_exists( $order, 'delete_meta_data' ) ) {
			$order->delete_meta_data( '_correios_tracking_code' );
			$order->save();
		} else {
			delete_post_meta( $order->id, '_correios_tracking_code' );
		}

		return true;
	} elseif ( ! $remove && ! in_array( $tracking_code, $tracking_codes, true ) ) {
		$tracking_codes[] = $tracking_code;

		if ( method_exists( $order, 'update_meta_data' ) ) {
			$order->update_meta_data( '_correios_tracking_code', implode( ',', $tracking_codes ) );
			$order->save();
		} else {
			update_post_meta( $order->id, '_correios_tracking_code', implode( ',', $tracking_codes ) );
		}

		// Add order note.
		/* translators: %s: tracking code */
		$order->add_order_note( sprintf( __( 'Added a Correios tracking code: %s', 'correios-offline' ), $tracking_code ) );

		// Send email notification.
		Correios_Offline_trigger_tracking_code_email( $order, $tracking_code );

		return true;
	} elseif ( $remove && in_array( $tracking_code, $tracking_codes, true ) ) {
		$key = array_search( $tracking_code, $tracking_codes, true );

		if ( false !== $key ) {
			unset( $tracking_codes[ $key ] );
		}

		if ( method_exists( $order, 'update_meta_data' ) ) {
			$order->update_meta_data( '_correios_tracking_code', implode( ',', $tracking_codes ) );
			$order->save();
		} else {
			update_post_meta( $order->id, '_correios_tracking_code', implode( ',', $tracking_codes ) );
		}

		// Add order note.
		/* translators: %s: tracking code */
		$order->add_order_note( sprintf( __( 'Removed a Correios tracking code: %s', 'correios-offline' ), $tracking_code ) );

		return true;
	}

	return false;
}

/**
 * Get address by postcode.
 *
 * @param string $postcode Postcode.
 *
 * @return stdClass
 */
function Correios_Offline_get_address_by_postcode( $postcode ) {
	return Correios_Offline_Autofill_Addresses::get_address( $postcode );
}
