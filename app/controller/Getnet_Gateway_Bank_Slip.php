<?php 

defined('ABSPATH') || exit;

class Getnet_Gateway_Bank_Slip extends WC_Payment_Gateway
{
   public const ID = 'getnet_bank_slip';
   private const PAYMENT_DATA = '_getnet_transaction_data';
   private const PAYMENT_ID = '_getnet_transaction_id';

   private $_api;

   public function __construct()
   {
      $this->id = self::ID;
      $this->has_fields = true;
      $this->method_title = __('Getnet - Bank Slip', 'getnet');
      $this->method_description = __('Integrates Getnet payment method into Woocommerce checkout', 'getnet');
      $this->supports = ['products'];

      $this->init_form_fields();
      $this->init_settings();
      $this->enqueueHooks();

      $this->title            = $this->get_option('title');
      $this->description      = $this->get_option('description');
      $this->enabled          = $this->get_option('enabled');
      $this->sandbox          = ('yes' === $this->get_option('sandbox'));

      $this->seller_id        = $this->sandbox ? $this->get_option('sandbox_seller_id')       : $this->get_option('seller_id');
      $this->client_id        = $this->sandbox ? $this->get_option('sandbox_client_id')       : $this->get_option('client_id');
      $this->client_secret    = $this->sandbox ? $this->get_option('sandbox_client_secret')   : $this->get_option('client_secret');

      $this->_api             = new VVerner\Getnet\BankSlip( $this->seller_id, $this->client_id, $this->client_secret, $this->isSandbox());

      $this->_api->setInstructions( $this->get_option('instructions') );
      $this->_api->setExpireGap( (int) $this->get_option('expire_gap') );
   }

   public function using_supported_currency()
   {
      return 'BRL' === get_woocommerce_currency();
   }

   public function init_form_fields()
   {
      $this->form_fields = [
         'enabled' => [
            'title'       => __('Enable', 'getnet'),
            'label'       => __('Enable Getnet\'s transparent checkout on WooCommerce', 'getnet'),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no'
         ],
         'title' => [
            'title'       => __('Payment method name', 'getnet'),
            'type'        => 'text',
            'description' => __('How the payment option will be displayed during checkout', 'getnet'),
            'default'     => __('Pay via bank slip', 'getnet'),
            'desc_tip'    => true,
         ],
         'description' => [
            'title'       => __('Description', 'getnet'),
            'type'        => 'textarea',
            'description' => __('Extra information available during checkout', 'getnet'),
            'default'     => __('Secure transaction via Getnet', 'getnet'),
         ],
         'instructions' => [
            'title'       => __('Customer Instructions', 'getnet'),
            'type'        => 'textarea',
            'description' => __('Extra information printed on the bank slip', 'getnet'),
            'default'     => '',
         ],
         'expire_gap' => [
            'title'       => __('Bank Slip expire time', 'getnet'),
            'type'        => 'number',
            'description' => __('In how many days the bank slip will be expired', 'getnet'),
            'default'     => '',
            'custom_attributes'  => [
               'min'    => 1,
               'step'   => 1
            ]
         ],
         'sandbox' => [
            'title'       => __('Sandbox', 'getnet'),
            'label'       => __('Check to use the sandbox version of the integration', 'getnet'),
            'type'        => 'checkbox',
            'default'     => 'no',
            'desc_tip'    => true,
         ],
         'sandbox_client_id' => [
            'title'       => __('SANDBOX: Client ID', 'getnet'),
            'type'        => 'text',
            'class'       => 'sandbox'
         ],
         'sandbox_client_secret' => [
            'title'       => __('SANDBOX: Client Secret', 'getnet'),
            'type'        => 'text',
            'class'       => 'sandbox'
         ],
         'sandbox_seller_id' => [
            'title'       => __('SANDBOX: Seller ID', 'getnet'),
            'type'        => 'text',
            'class'       => 'sandbox'
         ],
         'client_id' => [
            'title'       => __('Client ID', 'getnet'),
            'type'        => 'text',
            'class'       => 'production'
         ],
         'client_secret' => [
            'title'       => __('Client Secret', 'getnet'),
            'type'        => 'text',
            'class'       => 'production'
         ],
         'seller_id' => [
            'title'       => __('Seller ID', 'getnet'),
            'type'        => 'text',
            'class'       => 'production'
         ]
      ];
   }

   public function process_admin_options()
   {
      parent::process_admin_options();
      $settings = new WC_Admin_Settings();
      $settings->add_message(
         __('For now, bank slip payments do not receive confirmation of payment by the customer. You must manually approve payments', 'getnet'),
      );
   }

   public function payment_fields()
   {
      if (!empty($this->description)):
         echo wpautop(trim(sanitize_text_field($this->description)));
      endif;

      if ($this->isSandbox()):
         echo sprintf(__( 'Sandbox mode. You can use the debit cards provided <a href="%s" target="_blank" rel="noopener noreferrer">in the documentation</a>', 'getnet'), 'https://developers.getnet.com.br/api#section/Cartoes-para-Teste');
      endif;
   }

   public function is_available()
   {
      return $this->_api->isAvailable();
   }

   public function process_payment($order_ID)
   {
      global $woocommerce;
      $order = wc_get_order($order_ID);
      if(!isset($_REQUEST['payment_method']) || $_REQUEST['payment_method'] !== 'getnet_bank_slip') return;

      $payment = $this->_api->processPayment($order);
      if ($payment->success) :
         $order->payment_complete();

         $order->add_order_note(sprintf(__( 'Payment received, transaction ID: %s', 'getnet'), $payment->ID ), false);
         $order->add_meta_data(self::PAYMENT_DATA,  $payment, true);
         $order->add_meta_data(self::PAYMENT_ID,  $payment->ID, true);
         $order->save();

         $woocommerce->cart->empty_cart();
         $status = [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order)
         ];
      else :
         wc_add_notice( 'Erro ao processar o pagamento: ' . $payment->message , 'error' );
         $status = [
            'result'   => 'fail',
            'redirect' => ''
         ];
      endif;

      return $status;
   }

   public function hideWhenIsOutsideBrazil(array $gateways): array
   {
      if (isset($_REQUEST['country']) && 'BR' !== $_REQUEST['country']) :
         unset($gateways['getnet']);
      endif;

      return $gateways;
   }

   public function updateCheckoutFields(array $fields): array
   {
      if (isset($fields['billing_neighborhood'])) :
         $fields['billing_neighborhood']['required'] = true;
      endif;

      if (isset($fields['billing_number'])) :
         $fields['billing_number']['required'] = true;
      endif;

      return $fields;
   }

   public function enqueueCheckoutAssets()
   {
      if ('no' === $this->enabled) return;
      if (!$this->_api->isAvailable()) return;
      if (!is_cart() && !is_checkout() && !isset($_REQUEST['pay_for_order'])) return;

      $min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
      $url = VVerner\Getnet\Utils::getAssetsUrl();

      wp_enqueue_script(
         'jquery-mask', 
         $url . '/public/jquery.mask.min.js', 
         ['jquery'], 
         WC_GETNET_VERSION, 
         true
      );

      wp_enqueue_script(
         'getnet-checkout', 
         $url . '/public/checkout' . $min . '.js', 
         ['jquery', 'jquery-mask'], 
         WC_GETNET_VERSION, 
         true
      );
   }

   public function isSandbox(): bool
   {
      return $this->sandbox;
   }

   public function showPaymentInstructions(int $order_ID): void
   {
      $order    = wc_get_order($order_ID);
      $bankSlip = $order->get_meta(self::PAYMENT_DATA, true);

      wc_get_template(
         'public/bank_slip-payment_instructions.php',
         [
            'order' => $order,
            'bankSlip' => $bankSlip,
         ],
         'woocommerce/getnet/',
         VVerner\Getnet\Utils::getTemplatesPath()
      );
   }

   private function enqueueHooks(): void
   {
      add_filter('woocommerce_available_payment_gateways', [$this, 'hideWhenIsOutsideBrazil']);
      add_filter('woocommerce_billing_fields', [$this, 'updateCheckoutFields'], 9999);

      add_action('woocommerce_update_options_payment_gateways_' . self::ID, [$this, 'process_admin_options']);
      add_action('wp_enqueue_scripts', [$this, 'enqueueCheckoutAssets']);

      add_action('woocommerce_thankyou_' . self::ID, [$this, 'showPaymentInstructions']);
   }
}
