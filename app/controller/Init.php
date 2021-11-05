<?php 

namespace VVerner\Getnet;

defined('ABSPATH') || exit('No direct script access allowed');

class Init
{
   public function init()
   {
      add_action('init', [$this, 'loadTranslations']);
      require_once WC_GETNET_APP . '/controller/Utils.php';

      if (!class_exists('WC_Payment_Gateway')):
         add_action('admin_notices', [$this, 'enqueueWooCommerceMissingNotice']);

      elseif(!class_exists('Extra_Checkout_Fields_For_Brazil')):
         add_action('admin_notices', [$this, 'enqueueEcfbMissingNotice']);

      else:
         $this->requires();

         add_filter('woocommerce_payment_gateways', [$this, 'enqueueGateway']);
         add_filter('plugin_action_links_' . plugin_basename(WC_GETNET_FILE), [$this, 'enqueueActionsLinks']);

         if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueuePrivateAssets']);
         }
      endif;
   }

   public function loadTranslations()
   {
      $path = dirname( plugin_basename( WC_GETNET_FILE ) ) . '/app/languages';
      load_plugin_textdomain('getnet', false, $path);
   }

   public static function enqueueGateway($methods)
   {
      $methods[] = 'Getnet_Gateway';
      $methods[] = 'Getnet_Gateway_Bank_Slip';
      return $methods;
   }

   public static function enqueuePrivateAssets()
   {
      $screen = get_current_screen();
      if ($screen->base !== 'woocommerce_page_wc-settings') return;
      $min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

      wp_enqueue_script(
         'getnet-admin',
         Utils::getAssetsUrl() . '/private/admin' . $min . '.js',
         ['jquery'], 
         WC_GETNET_VERSION, 
         true
      );
   }

   public static function enqueueActionsLinks(array $links): array
   {
      $getnetLinks   = [];
      $getnetLinks[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=getnet')) . '">' . __('Settings', 'getnet') . '</a>';
      $getnetLinks[] = '<a href="' . esc_url(admin_url('https://developers.getnet.com.br/api')) . '" target="_blank" rel="noopener noreferrer">' . __('API Docs', 'getnet') . '</a>';
      return array_merge($links, $getnetLinks);
   }

   private function requires()
   {
      require_once WC_GETNET_APP . '/controller/API/Getnet.php';
      require_once WC_GETNET_APP . '/controller/API/CreditCard.php';
      require_once WC_GETNET_APP . '/controller/API/BankSlip.php';

      require_once WC_GETNET_APP . '/controller/Getnet_Gateway.php';
      require_once WC_GETNET_APP . '/controller/Getnet_Gateway_Bank_Slip.php';
   }

   public function enqueueEcfbMissingNotice()
   {
      Utils::loadPrivateView('notice-ecfb-missing');
   }

   public function enqueueWooCommerceMissingNotice()
   {
      Utils::loadPrivateView('notice-woocommerce-missing');
   }
}

$handler = new Init;
add_action('init', [$handler, 'init'], 9);