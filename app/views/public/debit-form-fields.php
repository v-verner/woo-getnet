<?php 

namespace VVerner\Getnet;

defined('ABSPATH') || exit; 

?>

<?php do_action('getnet_debit_before_checkout_fields') ?>

<fieldset id="wc-getnet-debit-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">
   <?php do_action('woocommerce_credit_card_form_start', 'getnet'); ?>
   <div class="form-row form-row-wide">
      <label>
         <?php _e('Name printed on card', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_debit_ccName" value="" name="getnet_debit[name]" type="text" autocomplete="cc-name">
   </div>
   <div class="form-row form-row-wide">
      <label for="getnet_debit_ccNo">
         <?php _e('Card number', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_debit_ccNo" value="" name="getnet_debit[cc]" type="text" inputmode="numeric" autocomplete="cc-number">
   </div>
   <div class="form-row form-row-first">
      <label for="getnet_debit_expdate">
         <?php _e('Expiration date', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_debit_expdate" value="" name="getnet_debit[date]" type="text" placeholder="MM/AA" inputmode="numeric" autocomplete="cc-exp">
   </div>
   <div class="form-row form-row-last">
      <label for="getnet_debit_cvv">
         <?php _e('Security Code (CVC)', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_debit_cvv" name="getnet_debit[cvc]" value="" type="password" placeholder="CVC" inputmode="numeric" maxlength="3" autocomplete="cc-csc">
   </div>
   <div class="clear"></div>
   <?php do_action('woocommerce_credit_card_form_end', 'getnet'); ?>
   <div class="clear"></div>
</fieldset>

<?php do_action('getnet_debit_after_checkout_fields') ?>
