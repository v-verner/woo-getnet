<?php 

namespace VVerner\Getnet;

use DateTime;
use \stdClass;
use \WC_Order;

defined('ABSPATH') || exit('No direct script access allowed');

class BankSlip extends API
{
   protected $instructions;
   protected $expireGap;

   public function __construct(string $seller_ID = '', string $client_ID = '', string $clientSecret = '', bool $isSandbox = false)
   {
      parent::__construct($seller_ID, $client_ID, $clientSecret, $isSandbox);
   }
  
   public function setInstructions(string $value): void
   {
      $this->instructions = $value;
   }

   public function setExpireGap(int $value): void
   {
      $this->expireGap = $value;
   }

   public function processPayment(WC_Order $order): stdClass
   {
      $data       = [
         'seller_id' => (string) $this->seller_ID,
         'amount'    => (int) ($order->get_total() * 100),
         'currency'  => 'BRL',
         'order'     => $this->getOrderData($order),
         'customer'  => $this->getCustomerData($order),
         'boleto'    => $this->getPaymentData($order)
      ];

      $res = $this->fetch('v1/payments/boleto', $data);
      return $this->traitPaymentResult( $res );
   }

   protected function traitPaymentResult(array $data): stdClass
   {
      Utils::log($data);

      $res = (object) [
         'success' => false,
         'message' => '',
         'ID'      => 0,
         'date'    => date('U'),
         'typeful_line'    => '',
         'expiration_date' => '',
         'download_url'    => ''
      ];

      if(isset($data['payment_id'])):
         $res->success           = true;
         $res->ID                = $data['payment_id'];
         $res->typeful_line      = $data['boleto']['typeful_line'];
         $res->expiration_date   = $data['boleto']['expiration_date'];
         $res->download_url      = $this->getUrl() . $data['boleto']['_links'][1]['href'];

      elseif(isset($data['message'])):
         $res->message  = $data['message'];
         $res->message .= ' ' . $data['details'][0]['description'] ?? '';

      endif;

      return $res;
   }

   private function getPaymentData(WC_Order $order): stdClass
   {
      return (object) [
         'document_number' => (string) $order->get_id(),
         'provider'        => 'santander',
         'expiration_date' => $this->getExpirationDate(),
         'instructions'    => $this->instructions
      ];
   }

   private function getExpirationDate(): string
   {
      $now = new DateTime(current_time('Y-m-d 00:00:00'));
      $now->modify('+ '. $this->expireGap .' days');

      return $now->format('d/m/Y');
   }
}
