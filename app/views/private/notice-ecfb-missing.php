<?php defined('ABSPATH') || exit('No direct script access allowed');

$is_installed = false;
if ( function_exists( 'get_plugins' ) ):
	$all_plugins  = get_plugins();
	$is_installed = ! empty( $all_plugins['woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php'] );
endif;
?>

<div class="error">
	<p>
      <strong>
         <?php _e('VVerner - Getnet for WooCommerce', 'getnet') ?>
      </strong> 
      <?php _e('needs the Brazilian Market on WooCommerce plugin to work!', 'getnet') ?>
   </p>

	<?php if ( $is_installed && current_user_can( 'install_plugins' ) ) : ?>
		<p>
			<a href="<?= esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php&plugin_status=active' ), 'activate-plugin_woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php' ) ); ?>" class="button button-primary">
				<?php _e('Enable plugin', 'getnet') ?>
			</a>
		</p>
	<?php else :
		if ( current_user_can( 'install_plugins' ) ):
			$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=woocommerce-extra-checkout-fields-for-brazil' ), 'install-plugin_woocommerce-extra-checkout-fields-for-brazil' );
      else:
			$url = 'http://wordpress.org/plugins/woocommerce-extra-checkout-fields-for-brazil/';
		endif;
	?>
		<p>
			<a href="<?= esc_url( $url ); ?>" class="button button-primary">
				<?php _e('Install plugin', 'getnet') ?>
			</a>
		</p>
	<?php endif; ?>
</div>
