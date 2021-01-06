"use strict";

jQuery(function ($) {
  var $toggleSandbox = $('#woocommerce_getnet_sandbox');
  if (!$toggleSandbox.length) return;
  $toggleSandbox.change(updateNeededKeys);
  updateNeededKeys();

  function updateNeededKeys() {
    var isSandbox = $toggleSandbox.is(':checked');

    if (isSandbox) {
      $('.sandbox').parents('tr').show();
      $('.production').parents('tr').hide();
    } else {
      $('.sandbox').parents('tr').hide();
      $('.production').parents('tr').show();
    }
  }
});