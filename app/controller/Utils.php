<?php 

namespace VVerner\Getnet;

defined('ABSPATH') || exit('No direct script access allowed');

class Utils
{
   public static function getTemplatesPath(): string
   {
      return WC_GETNET_APP . '/views/';
   }

   public static function loadPrivateView(string $view): void
   {
      $file = self::getTemplatesPath() . 'private/' . $view . '.php';
      if(file_exists($file)):
         require $file;
      else:
         echo sprintf(__( 'Private view file not found for %s', 'getnet'), 
            $view
         );
      endif;
   }

   public static function loadPublicView(string $view): void
   {
      $file = self::getTemplatesPath() . 'public/' . $view . '.php';
      if(file_exists($file)):
         require $file;
      else:
         echo sprintf(__( 'Public view file not found for %s', 'getnet'), 
            $view
         );
      endif;
   }

   public static function log($thing): void
   {
      $message = (is_array($thing) || is_object($thing)) ? print_r($thing, true) : $thing;
      if (function_exists('wc_get_logger')) :
         $logger  = wc_get_logger();
         $logger->error($message, ['source' => 'getnet']);
      endif;
      error_log($message);
   }

   public static function onlyDigits(string $str)
   {
      return preg_replace('/\D/', '', $str);
   }

   public static function formatNumber($number): string
   {
      return number_format($number, 2, ',', '.');
   }

   public static function getAssetsUrl(): string
   {
      return plugins_url('app/assets', WC_GETNET_FILE);
   }

   public static function getInstallments(float $orderAmount, int $minInstallmentAmount, int $maxInstallmentQuantity): array
   {
      $res = [[
         'qty'    => 1, 
         'price'  => $orderAmount
      ]];

      for ($i = 2; $i <= $maxInstallmentQuantity; $i++):
         $iAmount = $orderAmount / $i;
         if ($iAmount < $minInstallmentAmount) break;

         $res[] = [
            'qty'    => $i,
            'price'  => $iAmount
         ];
      endfor;

      return $res;
   }
}