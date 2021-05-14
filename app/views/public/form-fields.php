<?php 

namespace VVerner\Getnet;

defined('ABSPATH') || exit; 

?>

<?php do_action('getnet_before_checkout_fields') ?>

<fieldset id="wc-getnet-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">
   <?php do_action('woocommerce_credit_card_form_start', 'getnet'); ?>
   <div class="form-row form-row-wide">
      <label>
         <?php _e('Name printed on card', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_ccName" value="" name="getnet[name]" type="text" autocomplete="cc-name">
   </div>
   <div class="form-row form-row-wide">
      <label for="getnet_ccNo">
         <?php _e('Card number', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_ccNo" value="" name="getnet[cc]" type="text" inputmode="numeric" autocomplete="cc-number">
   </div>
   <div class="form-row form-row-first">
      <label for="getnet_expdate">
         <?php _e('Expiration date', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_expdate" value="" name="getnet[date]" type="text" placeholder="MM/AA" inputmode="numeric" autocomplete="cc-exp">
   </div>
   <div class="form-row form-row-last">
      <label for="getnet_cvv">
         <?php _e('Security Code (CVC)', 'getnet') ?>
      </label>
      <input class="input-text" id="getnet_cvv" name="getnet[cvc]" value="" type="password" placeholder="CVC" inputmode="numeric" maxlength="3" autocomplete="cc-csc">
   </div>
   <div class="form-row form-row-wide">
      <label for="getnet_installments">
         <?php _e('Installment', 'getnet') ?>
      </label>
      <select class="input-text" name="getnet[installments]" id="getnet_installments">
         <?php foreach ($installments as $i) : ?>
            <option value="<?= $i['qty'] ?>">
            <?= 
               sprintf(
                  '%d parcelas(s) de %s',
                  $i['qty'],
                  wc_price($i['price'])
               );
            ?>
            </option>
         <?php endforeach; ?>
      </select>
   </div>
   <div class="clear"></div>
   <?php do_action('woocommerce_credit_card_form_end', 'getnet'); ?>
   <div class="clear"></div>
</fieldset>

<?php do_action('getnet_after_checkout_fields') ?>
