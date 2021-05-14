jQuery(function ($) {
   const $form = $("form.woocommerce-checkout");

   $form.on("checkout_place_order_getnet", function(){
      if (!$("#payment_method_getnet").is(":checked")) {
         return true;
      }

      const $inputs = $('#wc-getnet-cc-form input:not([type="hidden"])');
      let isFilled = true;

      $inputs.each(function () {
         if (!$(this).val()) isFilled = false;
      });

      if(!isFilled) {
         alert("Por favor, preencha todos os dados do seu cartão");
         return false;
      }

      if(!isValidDate( $('#getnet_expdate').val() )) {
         alert("Por favor, confirma a data de vencimento do seu cartão");
         return false;
      }

      return true;
   });

   $(document).change(function() {
      $("#getnet_ccNo").mask("0000 0000 0000 0000", {
         clearIfNotMatch: true
      });
      $("#getnet_expdate").mask("00/00", {
         clearIfNotMatch: true
      });
   })

   function isValidDate(check) {
      const day = check.split("/");
      const date = new Date(day[0] + "-01-20" + day[1]);

      if (date == "Invalid Date") return false;
      const today = new Date();

      return date > today;
   }
});
