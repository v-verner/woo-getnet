"use strict";jQuery(function(t){var e=t("#woocommerce_getnet_sandbox");function n(){e.is(":checked")?(t(".sandbox").parents("tr").show(),t(".production").parents("tr").hide()):(t(".sandbox").parents("tr").hide(),t(".production").parents("tr").show())}e.length&&(e.change(n),n())});