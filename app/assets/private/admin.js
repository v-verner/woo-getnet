jQuery(function($){
   const $toggleSandbox = $('#woocommerce_getnet_sandbox, #woocommerce_getnet_debit_sandbox,  #woocommerce_getnet_bank_slip_sandbox');

   if(!$toggleSandbox.length) return;

   $toggleSandbox.change(updateNeededKeys);
   updateNeededKeys();

   function updateNeededKeys() {
       const isSandbox = $toggleSandbox.is(':checked');
       if(isSandbox) {
           $('.sandbox').parents('tr').show();
           $('.production').parents('tr').hide();
       } else {
           $('.sandbox').parents('tr').hide();
           $('.production').parents('tr').show();
       }
   }
});
