<?php defined( 'ABSPATH' ) || exit; ?>
<fieldset id="wc-getnet-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">
    <?php do_action('woocommerce_credit_card_form_start', 'getnet'); ?>
    <div class="form-row form-row-wide">
        <input type="hidden" id="getnet_ccToken" name="getnet_ccToken">
        <label>
            <?= __('Name printed on card *', 'vverner-getnet')?>
        </label>
        <input id="getnet_ccName" type="text" autocomplete="off">
    </div>
    <div class="form-row form-row-wide">
        <label for="getnet_ccNo">
            <?= __('Card number *', 'vverner-getnet')?>
        </label>
        <input id="getnet_ccNo" type="text" autocomplete="off" inputmode="numeric">
    </div>
    <div class="form-row form-row-first">
        <label for="getnet_expdate">
            <?= __('Expiration date *', 'vverner-getnet')?>
        </label>
        <input id="getnet_expdate" type="text" autocomplete="off" placeholder="MM/AA" inputmode="numeric">
    </div>
    <div class="form-row form-row-last">
        <label for="getnet_cvv">
            <?= __('Security Code (CVC) *', 'vverner-getnet')?>
        </label>
        <input id="getnet_cvv" type="password" autocomplete="off" placeholder="CVC" inputmode="numeric" maxlength="3">
    </div>
    <div class="form-row-wide">
        <label for="getnet_installments">
            <?= __('Installment *', 'vverner-getnet')?>
        </label>
        <select name="getnet_installments" id="getnet_installments">
            <?php foreach($installments as $i) : ?>
                <option value="<?= $i['qty'] ?>">
                    <?= $i['qty'] ?> parcelas de R$ <?= WC_Getnet::FormatNumber($i['price']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="clear"></div>
		<?php do_action('woocommerce_credit_card_form_end', 'getnet'); ?>
    <div class="clear"></div>
</fieldset>