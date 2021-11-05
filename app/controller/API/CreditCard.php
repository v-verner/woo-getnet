<?php 

namespace VVerner\Getnet;

use \stdClass;
use \WC_Order;

defined('ABSPATH') || exit('No direct script access allowed');

class CreditCard extends API
{
   public function __construct(string $seller_ID = '', string $client_ID = '', string $clientSecret = '', bool $isSandbox = false)
   {
      parent::__construct($seller_ID, $client_ID, $clientSecret, $isSandbox);
   }

   public function processPayment(WC_Order $order, int $installments, array $cc): stdClass
   {
      $isPerson   = (int) $order->get_meta('_billing_persontype') === 1;
      $doc        = $isPerson ? Utils::OnlyDigits($order->get_meta('_billing_cpf')) : Utils::OnlyDigits($order->get_meta('_billing_cnpj'));

      $session_ID = $doc . $order->get_id() . $this->client_ID;

      $data       = [
         'seller_id' => (string) $this->seller_ID,
         'amount'    => (int) ($order->get_total() * 100),
         'currency'  => 'BRL',
         'order'     => $this->getOrderData($order),
         'customer'  => $this->getCustomerData($order),
         'device'    => $this->getDeviceData($session_ID),
         'shippings' => $this->getShippingData($order),
         'credit'    => $this->getPaymentData($installments, $cc)
      ];

      $res = $this->fetch('v1/payments/credit', $data);
      return $this->traitPaymentResult( $res );
   }

   private function getPaymentData(int $installments, array $cc): stdClass
   {
      return (object) [
         'delayed'               => false,
         'save_card_data'        => false,
         'transaction_type'      => ($installments !== 1) ? 'INSTALL_NO_INTEREST' : 'FULL',
         'number_installments'   => (int) $installments,
         'card'                  => (object) [
            'number_token'          => (string) $cc['token'],
            'cardholder_name'       => (string) $cc['name'],
            'security_code'         => (string) $cc['cvc'],
            'expiration_month'      => (string) $cc['expMonth'],
            'expiration_year'       => (string) $cc['expYear'],
         ]
      ];
   }
}
