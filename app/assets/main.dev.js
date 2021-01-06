"use strict";

jQuery(function ($) {
  // MAP
  var getnet_submit = false;
  var $form = $("form.woocommerce-checkout");
  var $inputs = $('#wc-getnet-cc-form input:not([type="hidden"])'); // LISTENER

  $form.on("checkout_place_order_getnet", processCreditCard); // MASKS

  $(document).change(function () {
    $("#getnet_ccNo").mask("0000.0000.0000.0000", {
      clearIfNotMatch: true
    });
    $("#getnet_expdate").mask("00/00", {
      clearIfNotMatch: true
    });
  }); // HELPERS

  function processCreditCard(e) {
    if (getnet_submit) {
      getnet_submit = false;
      return true;
    }

    if (!$("#payment_method_getnet").is(":checked")) {
      return true;
    }

    var placeOrderText = $form.find("#place_order").text();
    $form.find("#place_order").attr("disabled", true).text("Validando Cartão...");

    if (!validateForm()) {
      alert("Por favor, confira os dados do seu cartão");
      return false;
    }

    var exp = $("#getnet_expdate").val().split("/");
    var data = {
      action: "getnet_processCreditCard",
      card: $("#getnet_ccNo").val(),
      month: exp[0],
      year: exp[1],
      cvv: $("#getnet_cvv").val(),
      name: $("#getnet_ccName").val()
    };
    $.post(getnetParams.url, data, function (res) {
      var result = res.data.message;

      if (res.success) {
        $form.find('input[name="getnet_ccToken"]').val(result.token);
        $form.find('input[name="getnet_ccMonth"]').val(result.expMonth);
        $form.find('input[name="getnet_ccYear"]').val(result.expYear);
        getnet_submit = true;
        $form.submit();
      } else {
        alert(result);
        $form.find("#place_order").attr("disabled", false).text(placeOrderText);
        return false;
      }
    });
    return false;
  }

  function isValidDate(check) {
    var day = check.split("/");
    var date = new Date(day[0] + "-01-20" + day[1]);
    if (date == "Invalid Date") return false;
    var today = new Date();
    if (today.getFullYear() > date.getFullYear()) return false;
    return true;
  }

  function validateForm() {
    $inputs.each(function () {
      if (!$(this).val()) return false;
    });
    return isValidDate($("#getnet_expdate").val());
  }
});