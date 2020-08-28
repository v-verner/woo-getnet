<?php defined( 'ABSPATH' ) || exit;

$is_installed = false;

if ( function_exists( 'get_plugins' ) ) {
	$all_plugins  = get_plugins();
	$is_installed = ! empty( $all_plugins['woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php'] );
}

?>

<div class="error">
	<p><strong><?= __('VVerner - Getnet para WooCommerce', 'vverner-getnet') ?></strong> <?= __('precisa do plugin Brazilian Market on WooCommerce para funcionar!', 'vverner-getnet') ?></p>

	<?php if ( $is_installed && current_user_can( 'install_plugins' ) ) : ?>
		<p>
			<a href="<?= esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php&plugin_status=active' ), 'activate-plugin_woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php' ) ); ?>" class="button button-primary">
				<?= __('Ativar plugin', 'vverner-getnet') ?>
			</a>
		</p>
	<?php else :
		if ( current_user_can( 'install_plugins' ) ) {
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce-extra-checkout-fields-for-brazil' ), 'install-plugin_woocommerce-extra-checkout-fields-for-brazil' );
		} else {
			$url = 'http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/';
		}
	?>
		<p>
			<a href="<?= esc_url( $url ); ?>" class="button button-primary">
				<?= __('Instalar plugin', 'vverner-getnet') ?>
			</a>
		</p>
	<?php endif; ?>
</div>
