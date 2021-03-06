<?php
/**
 * Tracking codes.
 *
 * @author  Claudio_Sanches
 * @package correios_offline/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="correios-offline-tracking__description"><?php echo esc_html( _n( 'Tracking code:', 'Tracking codes:', count( $codes ), 'correios-offline' ) ); ?></p>

<table class="correios-offline-tracking__table woocommerce-table shop_table shop_table_responsive">
	<tbody>
		<?php foreach ( $codes as $code ) : ?>
			<tr>
				<th><?php echo esc_html( $code ); ?></th>
				<td>
					<form method="POST" target="_blank" rel="nofollow noopener noreferrer" action="http://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm" class="correios-offline-tracking__form">
						<input type="hidden" name="Objetos" value="<?php echo esc_attr( $code ); ?>">
						<input class="correios-offline-tracking__button button" type="submit" value="<?php esc_attr_e( 'Query on Correios', 'correios-offline' ); ?>">
					</form>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
