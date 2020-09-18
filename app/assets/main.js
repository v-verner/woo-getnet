jQuery(function($){
    // MAP
    var getnet_submit = false;
    const $form = $( 'form.woocommerce-checkout' );
    const $inputs = $('#wc-getnet-cc-form input:not([type="hidden"])');

    // LISTENER
	$form.on('checkout_place_order_getnet', processCreditCard );

    // MASKS
    $('#getnet_ccNo').mask('0000.0000.0000.0000', {clearIfNotMatch: true});
    $('#getnet_expdate').mask('00/00', {clearIfNotMatch: true});

    // HELPERS
    function processCreditCard(e) {
        if ( getnet_submit ) {
            getnet_submit = false;
            return true;
        }

        if ( ! $( '#payment_method_getnet' ).is( ':checked' ) ) {
            return true;
        }

        if(!validateForm()) {
            alert('Por favor, confira os dados do seu cartão');
            return false;
        }

        const exp = $('#getnet_expdate').val().split('/');
        const data = {
            action  : 'getnet_processCreditCard',
            card    : $('#getnet_ccNo').val(),
            month   : exp[0],
            year    : exp[1],
            cvv     : $('#getnet_cvv').val(),
            name    : $('#getnet_ccName').val(),
        }

        $.post(getnetParams.url, data, (res) => {
            const data = JSON.parse(res);
            if(data.status === 'success') {
                $form.find('#getnet_ccToken').val(data.message);
                getnet_submit = true;
                $form.submit();
            } else {
                alert(data.message);
                return false;
            }
        })

        return false;
    }

    function isValidDate(check) {
        const day = check.split('/');
        const date = new Date(day[0] + '-01-20' + day[1]);

        if(date == 'Invalid Date') return false;
        const today = new Date();

        if(today.getFullYear() > date.getFullYear() ) return false;
        return true;
    };

    function validateForm() {
        $inputs.each(function(){
            if(!$(this).val()) return false;
        })
        return isValidDate($('#getnet_expdate').val());
    }
});