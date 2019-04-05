<?php
/**
 * Admin help message.
 *
 * @package correios_offline/Admin/Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( apply_filters( 'correios_offline_help_message', true ) ) : ?>
	<div class="updated woocommerce-message inline">
		<p>
		<?php
			/* translators: %s: plugin name */
			echo esc_html( sprintf( esc_html__( 'Help us keep the %s plugin free making a donation or rate &#9733;&#9733;&#9733;&#9733;&#9733; on WordPress.org. Thank you in advance!', 'correios-offline' ), __( 'Correios Offline', 'correios-offline' ) ) );
		?>
		</p>
		<p><a href="https://claudiosanches.com/doacoes/" target="_blank" rel="nofollow noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Make a donation', 'correios-offline' ); ?></a> <a href="https://wordpress.org/support/plugin/correios-offline/reviews/?filter=5#new-post" target="_blank" class="button button-secondary"><?php esc_html_e( 'Make a review', 'correios-offline' ); ?></a></p>
	</div>
<?php
endif;
