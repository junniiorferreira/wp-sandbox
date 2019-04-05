<?php
/**
 * Tracking history table.
 *
 * @author  Claudio_Sanches
 * @package correios_offline/Templates
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p class="correios-offline-tracking__description"><?php esc_html_e( 'History for the tracking code:', 'correios-offline' ); ?> <strong><?php echo esc_html( $code ); ?></strong></p>

<table class="correios-offline-tracking__table woocommerce-table shop_table shop_table_responsive">
	<thead>
		<tr>
			<th><?php esc_html_e( 'Date', 'correios-offline' ); ?></th>
			<th><?php esc_html_e( 'Location', 'correios-offline' ); ?></th>
			<th><?php esc_html_e( 'Status', 'correios-offline' ); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ( $events as $event ) : ?>
		<tr>
			<td><?php echo esc_html( $event->data . ' ' . $event->hora ); ?></td>
			<td>
				<?php echo esc_html( $event->local . ' - ' . $event->cidade . '/' . $event->uf ); ?>

				<?php if ( isset( $event->destino ) ) : ?>
					<br />
					<?php
						/* translators: %s: address */
						echo esc_html( sprintf( __( 'In transit to %s', 'correios-offline' ), $event->destino->local . ' - ' . $event->destino->cidade . '/' . $event->destino->uf ) );
					?>
				<?php endif; ?>
			</td>
			<td>
				<?php echo esc_html( $event->descricao ); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3">
				<form method="POST" target="_blank" rel="nofollow noopener noreferrer" action="http://www2.correios.com.br/sistemas/rastreamento/resultado_semcontent.cfm" class="correios-offline-tracking__form">
					<input type="hidden" name="Objetos" value="<?php echo esc_attr( $code ); ?>">
					<input class="correios-offline-tracking__button button" type="submit" value="<?php esc_attr_e( 'Query on Correios', 'correios-offline' ); ?>">
				</form>
			</td>
		</tr>
	</tfoot>
</table>
