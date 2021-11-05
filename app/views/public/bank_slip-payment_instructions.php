<?php 
if (isset($bankSlip)) : ?>
   <h3>
      <?php _e('Use the information below to pay your bank slip.', 'getnet') ?>
   </h3>
   <p>
      <?php _e('The bank slip expiration date is: ', 'getnet') ?> <strong><?php echo $bankSlip->expiration_date ?></strong>
   </p>

   <p>
      <input type="text" id="_getnet_bank_slip_code" value="<?= $bankSlip->typeful_line ?>" readonly>
   </p>

   <p>
      <a id="getnet_bank_slip_button" href="<?= $bankSlip->download_url ?>" target="_blank" rel="noopener noreferrer">
         <?php _e('View Bank Slip', 'getnet') ?>
      </a>
   </p>

   <script>
      const $input = document.getElementById('_getnet_bank_slip_code');
      $input.addEventListener('click', () => {
         $input.select();
         $input.setSelectionRange(0, 99999)
         navigator.clipboard.writeText($input.value);

         alert('<?php _e('Typeful_line copied!', 'getnet')?>')
      });
   </script>
<?php endif; ?>