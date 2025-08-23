<?php

define('PAYPALME_LINK', 'https://www.paypal.com/paypalme/BristolRHG');

add_shortcode('brhg-donate-button', 'brhg2025_donate_button');

function brhg2025_donate_button($atts) {
  extract(shortcode_atts(array(
    'amounts'         => '5,10,20',
    'button_text'     => 'Donate',
    'pre_text'        => 'Donate:',
    'custom_default'  => '5.00'
  ), $atts, 'brhg-donate-button'));

  $custom_default = number_format((float) $custom_default, 2);

  // Split the preset amounts into an array
  $amounts_array = array_map('trim', explode(',', $amounts));

  // Make a list of the preset amounts
  $amounts_list = '';

  foreach ($amounts_array as $dosh) {
    $link = esc_url(PAYPALME_LINK) . "/{$dosh}gbp";

    $amounts_list .= "<li class='brhg-donate-button-amount'>\n
    <a href='{$link}' class='brhg-donate-button-link' target='_blank'>£{$dosh}</a>\n
    </li>";
  }

  ob_start();

?>

  <div class='brhg-donate-button' aria-label="PayPal donate">
    <p><?php echo $pre_text; ?></p>
    <ul class='brhg-donate-button-list'><?php echo $amounts_list; ?></ul>

    <form onsubmit="handlePayPalRedirect(event)" class="brhg-donate-custom-amount">
      <label for="amountInput" class="brhg-donate-input-label">Or, choose amount:</label>
      <div class="brhg-donate-input-wrap">£
        <input
          name="brhgDonateAmount"
          type="text"
          class="amountInput brhg-donate-input"
          value=<?php echo $custom_default; ?>
          inputmode="decimal"
          pattern="\d+(\.\d{0,2})?"
          title="Enter a number with up to 2 decimal places" />
        <button type="submit" class="btn btn-primary brhgCustomDonateButton brhg-custom-donate-button"><?php echo $button_text; ?></button>
      </div>
    </form>
    <script>
      const inputs = document.querySelectorAll('.amountInput');
      const validPattern = /^(\d+|\d*\.\d{0,2}|\.|)$/;

      inputs.forEach(input => {
        input.addEventListener('input', () => {
          let val = input.value;

          // Allow partial decimal typing
          const filtered = val.replace(/[^\d.]/g, '');

          // Enforce single "."
          const dotParts = filtered.split('.');
          if (dotParts.length > 2) {
            val = dotParts[0] + '.' + dotParts.slice(1).join('');
          } else {
            val = filtered;
          }

          // Validate and preserve
          if (validPattern.test(val)) {
            input.value = val;
            input.setAttribute('data-last-valid', val);
          } else {
            input.value = input.getAttribute('data-last-valid') || '';
          }
        });

        ['blur', 'change'].forEach(evt => {
          input.addEventListener(evt, () => {
            const num = parseFloat(input.value);
            input.value = isNaN(num) ? '' : num.toFixed(2);
          });
        });
      });

      // PayPal link handler
      function handlePayPalRedirect(event) {
        event.preventDefault();

        // Collect input values
        const formData = new FormData(event.target);
        const values = Object.fromEntries(formData.entries());

        const amount = values.brhgDonateAmount || '';

        if (!/^\d+(\.\d{1,2})?$/.test(amount)) {
          alert("Please enter a valid amount (e.g. 10 or 12.50)");
          return;
        }

        const url = "<?php echo PAYPALME_LINK; ?>/" + encodeURIComponent(amount + 'gbp');
        window.open(url, '_blank');
      }
    </script>
  </div>

<?php
  return ob_get_clean();
}
