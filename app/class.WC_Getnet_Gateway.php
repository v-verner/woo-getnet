<?php defined('ABSPATH') || exit;

class WC_Getnet_Gateway extends WC_Payment_Gateway
{
   public function __construct()
   {
      $this->id = 'getnet';
      $this->has_fields = true;
      $this->method_title = __('Getnet', 'vverner-getnet');
      $this->method_description = __('Integrates Getnet payment method into Woocommerce checkout', 'vverner-getnet');
      $this->supports = ['products', 'refunds'];

      $this->init_form_fields();
      $this->init_settings();

      $this->title           = $this->get_option('title');
      $this->description        = $this->get_option('description');
      $this->enabled            = $this->get_option('enabled');
      $this->sandbox            = ('yes' === $this->get_option('sandbox'));
      $this->seller_id        = $this->sandbox ? $this->get_option('sandbox_seller_id')       : $this->get_option('seller_id');
      $this->client_id        = $this->sandbox ? $this->get_option('sandbox_client_id')       : $this->get_option('client_id');
      $this->client_secret    = $this->sandbox ? $this->get_option('sandbox_client_secret')    : $this->get_option('client_secret');
      $this->installments     = $this->get_option('installments') + 1;
      $this->min_installment  = $this->get_option('min_installment');
      $this->api             = new WC_Getnet_Api($this);

      add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
      add_action('wp_enqueue_scripts', [$this, 'payment_scripts']);
   }

   public function init_form_fields()
   {
      $this->form_fields = array(
         'enabled' => array(
            'title'       => __('Enable', 'vverner-getnet'),
            'label'       => __('Enable Getnet\'s transparent checkout on WooCommerce', 'vverner-getnet'),
            'type'        => 'checkbox',
            'description' => '',
            'default'     => 'no'
         ),
         'title' => array(
            'title'       => __('Payment method name', 'vverner-getnet'),
            'type'        => 'text',
            'description' => __('How the payment option will be displayed during checkout', 'vverner-getnet'),
            'default'     => __('Pay via credit card', 'vverner-getnet'),
            'desc_tip'    => true,
         ),
         'description' => array(
            'title'       => __('Description', 'vverner-getnet'),
            'type'        => 'textarea',
            'description' => __('Extra information available during checkout', 'vverner-getnet'),
            'default'     => __('Secure transaction via Getnet', 'vverner-getnet'),
         ),
         'min_installment' => [
            'title'         => __('Minimum installment value', 'vverner-getnet'),
            'type'         => 'number',
            'default'       => 10,
         ],
         'installments' => [
            'title' => __('Maximum installment quantity', 'vverner-getnet'),
            'type'  => 'select',
            'default' => 12,
            'options' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
         ],
         'sandbox' => array(
            'title'       => __('Sandbox', 'vverner-getnet'),
            'label'       => __('Check to use the sandbox version of the integration', 'vverner-getnet'),
            'type'        => 'checkbox',
            'default'     => 'no',
            'desc_tip'    => true,
         ),
         'sandbox_client_id' => array(
            'title'       => __('SANDBOX: Client ID', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'sandbox'
         ),
         'sandbox_client_secret' => array(
            'title'       => __('SANDBOX: Client Secret', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'sandbox'
         ),
         'sandbox_seller_id' => array(
            'title'       => __('SANDBOX: Seller ID', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'sandbox'
         ),
         'client_id' => array(
            'title'       => __('Client ID', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'production'
         ),
         'client_secret' => array(
            'title'       => __('Client Secret', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'production'
         ),
         'seller_id' => array(
            'title'       => __('Seller ID', 'vverner-getnet'),
            'type'        => 'text',
            'class'         => 'production'
         )
      );
   }

   public function payment_fields()
   {
      if (!empty($this->description)) {
         echo wpautop(trim(sanitize_text_field($this->description)));
      }
      if ($this->sandbox) {
         echo wpautop(__('Sandbox mode. You can use the credit cards provided <a href="https://developers.getnet.com.br/checkout#section/Cartoes-para-Teste" target="_blank" rel="noopener noreferrer">in the documentation</a>', 'vverner-getnet'));
      }

      wc_get_template(
         'form-fields.php',
         [
            'installments' => $this->getInstallments()
         ],
         'woocommerce/getnet/',
         WC_Getnet::get_templates_path()
      );
   }

   public function payment_scripts()
   {
      if ('no' === $this->enabled) return;
      if (empty($this->seller_id) || empty($this->client_id) || empty($this->client_secret)) return;
      if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) return;

      $min = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.prod';
      $min = ''; // error on prod version

      wp_enqueue_script('jquery-mask', plugins_url('assets/jquery.mask.min.js', __FILE__), ['jquery'], WC_GETNET_VERSION, true);
      wp_enqueue_script('gn-main', plugins_url('assets/main' . $min . '.js', __FILE__), ['jquery', 'jquery-mask'], WC_GETNET_VERSION, true);
      wp_localize_script('gn-main', 'getnetParams', [
         'url'    => admin_url('admin-ajax.php'),
      ]);
   }

   public function process_payment($order_id)
   {
      global $woocommerce;
      $order = wc_get_order($order_id);
      if (!isset($_POST['getnet_ccToken']) || empty($_POST['getnet_ccToken'])) return;

      $sec = [
         'token'    => sanitize_text_field($_POST['getnet_ccToken']),
         'expMonth'    => sanitize_text_field($_POST['getnet_ccMonth']),
         'expYear'     => sanitize_text_field($_POST['getnet_ccYear']),
         'name'     => sanitize_text_field($_POST['getnet_ccName']),
      ];
      $installments = (int) sanitize_text_field($_POST['getnet_installments']);
      $paymentId = $this->api->processPayment($order, $installments, $sec);

      if ($paymentId) :
         $order->payment_complete();
         wc_reduce_stock_levels($order);
         $order->add_order_note(__('Payment received', 'vverner-getnet'), false);
         $order->add_order_note(__('Payment ID: ' . $paymentId, 'vverner-getnet'), false);
         $order->add_meta_data('getnet_data', serialize([
            'payment' => $paymentId,
            'date'      => date('U')
         ]), true);
         $woocommerce->cart->empty_cart();

         $status = [
            'result'   => 'success',
            'redirect' => $this->get_return_url($order)
         ];
      else :
         $status = [
            'result'   => 'fail',
            'redirect' => ''
         ];
      endif;

      return $status;
   }

   public function process_refund($order_id, $amount = NULL, $reason = '')
   {
      global $woocommerce;
      $order = wc_get_order($order_id);

      $meta = $order->get_meta('getnet_data');
      $meta = ($meta) ? unserialize($meta) : null;
      $refund = false;

      if (!$meta) :
         $order->add_order_note(__('The payment could not be refunded due to lack of information', 'vverner-getnet'), false);

      elseif (intval($meta['date']) !== intval(date('U'))) :
         $order->add_order_note(__('The payment could not be refunded because it is out of date for a refund.', 'vverner-getnet'), false);

      else :
         $refund = $this->api->refundPayment($meta['payment']);

      endif;

      if ($refund) :
         $order->add_order_note(__('Payment refunded', 'vverner-getnet'), false);

      else :
         $order->add_order_note(__('Payment not refunded', 'vverner-getnet'), false);

      endif;

      return $refund;
   }

   public function getInstallments(): array
   {
      global $woocommerce;
      $orderAmount = $woocommerce->cart->total;
      $res = [['qty' => 1, 'price' => $orderAmount]];
      for ($i = 2; $i <= $this->installments; $i++) :
         $iAmount = $orderAmount / $i;
         if ($iAmount < $this->min_installment) break;

         $res[] = [
            'qty' => $i,
            'price' => $iAmount
         ];
      endfor;

      return $res;
   }

   public function isSandbox(): bool
   {
      return $this->sandbox;
   }
}
